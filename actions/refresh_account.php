<?php
/* Start session and load lib */
session_start();
require_once('../twitteroauth/twitteroauth.php');
require_once('../includes/config.php');

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
$content = $connection->get('account/verify_credentials');
//save user info and token into db
$query = "UPDATE users SET oauth_token = '" . $_SESSION['oauth_token'] . "', oauth_token_secret = '" . $_SESSION['oauth_token_secret'] . "', name = '" . $content->name . "', screen_name = '" . $content->screen_name . "', description = '" . $content->description . "', profile_image_url = '" . $content->profile_image_url . "', url = '" . $content->url . "', location = '" . $content->location . "', geo_enabled = '" . $content->geo_enabled . "' WHERE id = " . $content->id . ";";
$result = mysql_query($query);		 
if (mysql_affected_rows()==0) {
	$query = "INSERT INTO users (id, oauth_token, oauth_token_secret, name, date, screen_name, description, profile_image_url, url, location, geo_enabled) VALUES ('" . $content->id . "', '" . $_SESSION['oauth_token'] . "', '" . $_SESSION['oauth_token_secret'] . "', '" . $content->name . "', NOW(), '" . $content->screen_name . "', '" . $content->description . "', '" . $content->profile_image_url . "', '" . $content->url . "', '" . $content->location . "', '" . $content->geo_enabled . "');";
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

//redirect back to the page that called the refresh
header('Location: ' . $_SERVER['HTTP_REFERER']);