<?php

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0

include '../includes/config.php';
include '../includes/functions.php';

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
$response = '';

// Reply?
if ($_POST['reply'] == 'true') {
	$string = $_POST['string'];
	//Update twitter status
//	$tweet =  strip_tags($_POST['tweet']) . ' http://' . SITE_SHORT_URL . '/' . $string;
	$place_id = $_POST['place_id'];
	$tweet_id = $_POST['tweet_id'];
	$tweet =  '@' . $_POST['screen_name'] . ' ' . strip_tags($_POST['tweet']);
	$status = $connection->post('statuses/update', array('status' => $tweet, 'in_reply_to_status_id' => $tweet_id, 'place_id' => $place_id));
	
	// Update comment count
	$query = "UPDATE topics SET comments = comments + 1, comments_pending = comments_pending + 1 WHERE string = '" . $string . "'";
	$result = mysql_query($query) OR error($query);

	$query = "UPDATE users SET comments = comments + 1 WHERE id = '" . $_SESSION['id'] . "'";
	$result = mysql_query($query) OR error($query);
	
	//geo info
	$lat = 0;
	$long = 0;
	foreach ($status->place->bounding_box->coordinates[0] as $coord) {
		$long += $coord[0];
		$lat += $coord[1];
	}
	
	$lat = round($lat / 4, 6);
	$long = round($long / 4, 6);

	// Add the new comment
	$query = "INSERT INTO discussions (string, user_id, comment, date, ipaddr, place_id, latitude, longitude) VALUES ('" . $string . "', '" . $_SESSION['id'] . "', '" . strip_tags(addslashes($_POST['tweet'])) . "', NOW(), INET_ATON('" . $_SERVER['REMOTE_ADDR'] . "'), '" . $place_id . "', '" . $lat . "', '" . $long . "')";
	$result = mysql_query($query) OR error($query);
	$comment_id = mysql_insert_id();
	if ($result == true)
		$response .= 'success||Your reply has been posted.<br><br>||' . $comment_id;
	else
		$response .= 'error||There was a problem entering in the Tweet! Please try again later.';	


// New topic
} else {
	// Generate a unique string
	$string = generate_string();
	$place_id = $_POST['place_id'];
	
	//Update twitter status
	$tweet =  strip_tags($_POST['tweet']) . ' http://' . SITE_SHORT_URL . '/' . $string;
	$status = $connection->post('statuses/update', array('status' => $tweet, 'place_id' => $place_id));
	
	//geo info
	$lat = 0;
	$long = 0;
	foreach ($status->place->bounding_box->coordinates[0] as $coord) {
		$long += $coord[0];
		$lat += $coord[1];
	}
	
	$lat = round($lat / 4, 6);
	$long = round($long / 4, 6);

	// Increase their topic count
	$query = "UPDATE users SET num_topics = num_topics + 1 WHERE id = '" . $_SESSION['id'] . "'";
	$result = mysql_query($query) OR error($query);

	// Insert the new discussion topic
	$query = "INSERT INTO topics (user_id, date, tweet_id, string, title, user, place_id, latitude, longitude) VALUES ('" . $_SESSION['id'] . "', NOW(), '" . $status->id . "', '" . $string . "', '" . strip_tags(addslashes($_POST['tweet'])) . "', '" . $_SESSION['screen_name'] . "', '" . $place_id . "', '" . $lat . "', '" . $long . "')";
	$result = mysql_query($query) OR error($query);
	$comment_id = mysql_insert_id();
	if ($result == true)
		$response .= 'success||Your Tweet has been posted and users can now <b><a href="/' . $string . '">discuss</a></b> this Tweet!';
	else
		$response .= 'error||There was a problem entering in the Tweet! Please try again later.';
}
echo $response;

?>