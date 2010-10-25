<?php
/**
 * @file
 * Take the user when they return from Twitter. Get access tokens.
 * Verify credentials and redirect to based on response from Twitter.
 */

/* Start session and load lib */
session_start();
require_once('twitteroauth.php');
require_once('../includes/config.php');

/* If the oauth_token is old redirect to the connect page. */
if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
  header('Location: ../clearsessions.php');
}

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

/* Save the access tokens into session */
$_SESSION['oauth_token'] = $access_token['oauth_token'];
$_SESSION['oauth_token_secret'] =$access_token['oauth_token_secret'];

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 == $connection->http_code) {
	/* The user has been verified and the access tokens can be saved for future use */
	$_SESSION['status'] = 'verified';

	/* Create a TwitterOauth object with consumer/user tokens. */
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
	$content = $connection->get('account/verify_credentials');
	//save user info and token into db
	$query = "UPDATE users SET oauth_token = '" . $access_token['oauth_token'] . "', oauth_token_secret = '" . $access_token['oauth_token_secret'] . "', name = '" . $content->name . "', screen_name = '" . $content->screen_name . "', description = '" . $content->description . "', profile_image_url = '" . $content->profile_image_url . "', url = '" . $content->url . "', location = '" . $content->location . "', geo_enabled = '" . $content->geo_enabled . "' WHERE id = " . $content->id . ";";
	$result = mysql_query($query);		 
	if (mysql_affected_rows()==0) {
		$query = "INSERT INTO users (id, oauth_token, oauth_token_secret, name, date, screen_name, description, profile_image_url, url, location, geo_enabled) VALUES ('" . $content->id . "', '" . $access_token['oauth_token'] . "', '" . $access_token['oauth_token_secret'] . "', '" . $content->name . "', NOW(), '" . $content->screen_name . "', '" . $content->description . "', '" . $content->profile_image_url . "', '" . $content->url . "', '" . $content->location . "', '" . $content->geo_enabled . "');";
		$result = mysql_query($query);
	}

	
	$_SESSION['id'] = $content->id;
	$_SESSION['name'] = $content->name;
	$_SESSION['screen_name'] = $content->screen_name;
	$_SESSION['description'] = $content->description;
	$_SESSION['profile_image_url'] = $content->profile_image_url;
	$_SESSION['url'] = $content->url;
	$_SESSION['location'] = $content->location;
	$_SESSION['geo_enabled'] = $content->geo_enabled;
	
	$cookie = base64_encode($access_token['oauth_token'] . ':' . $access_token['oauth_token_secret']);
	setcookie(COOKIE_NAME, $cookie, time()+60*60*24*7*3, '/', COOKIE_DOMAIN);

	header('Location: ' . $_SESSION['redirect']);
} else {
  /* Save HTTP status for error dialog on connnect page.*/
  header('Location: ../clearsessions.php');
}
