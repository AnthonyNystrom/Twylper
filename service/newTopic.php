<?php

include '../includes/config.php';
require_once('functions.php');
require_once('httpResponses.php');

$data = run_twitter_request(TWITTER_VERIFY_URL, $_POST['username'], $_POST['password']);
		
if (xml_return_value(xml_parser($data), 'error') == true) {
    echo('error||' . xml_return_value(xml_parser($data), 'error'));
    unauthorized();
    return;
} 
		
$userId = getUserId( $_POST['username'], $_POST['password']);
if ($userId == -1)
{
    unauthorized();
    return;
}	
		
$date = date('Y-m-d H:i:s');
	
$tweet_id = xml_return_value(xml_parser($data), 'id');
$twitter_id = xml_return_value(xml_parser($data), 'id');
$twitter_name = xml_return_value(xml_parser($data), 'name');

$string = generate_string();

// Increase their topic count
$query = "UPDATE users SET num_topics = num_topics + 1 WHERE id = '" . $userId . "'";
$result = mysql_query($query) OR error($query);
		
// Insert the new discussion topic
$query = "INSERT INTO topics (user_id, date, twitter_id, tweet_id, string, title, user, geoA, geoB) VALUES ('" . $userId . "', '" . $date . "', '" . $twitter_id . "', '" . $tweet_id . "', '" . $string . "', '" . strip_tags(addslashes($_POST['post'])) . "', '" . $_POST['username'] . "', '" . $_POST['geoA'] . "', '" . $_POST['geoB'] . "')";
$result = mysql_query($query) OR error($query);
$comment_id = mysql_insert_id();
if ($result == true)
    $response .= 'success||Your Tweet has been posted and users can now <b><a href="/' . $string . '">discuss</a></b> this Tweet!';
else
    $response .= 'error||There was a problem entering in the Tweet! Please try again later.';
		
$url = ' http://' . SITE_SHORT_URL . '/' . $string;
		
$tweet = urlencode(stripslashes(trim($_POST['post'])) . $url);
		
$num_chars = strlen($tweet);
		
if ($num_chars > 140)
{
    $strip_amount = $num_chars - 140;
    $tweet = urlencode(substr(stripslashes(trim($_POST['post'])), 0, -$strip_amount) . $url);
}

$data = run_twitter_request(TWITTER_UPDATE_URL, $_POST['username'], $_POST['password'], "status=" . $tweet);
		
if (xml_return_value(xml_parser($data), 'error') == true) {
    echo('error||' . xml_return_value(xml_parser($data), 'error'));
    notFound();
    return;
} 
else
{
    ok();
}
?>