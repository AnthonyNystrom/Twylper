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
		
$userId =getUserId( $_POST['username'], $_POST['password']);
if ($userId == -1)
{
    unauthorized();
    return;
}	
			
$date = date('Y-m-d H:i:s');
		
$tweet_id = xml_return_value(xml_parser($data), 'id');
$twitter_id = xml_return_value(xml_parser($data), 'id');
$twitter_name = xml_return_value(xml_parser($data), 'name');
		
	
// Update comment count
$query = "UPDATE topics SET comments = comments + 1, comments_pending = comments_pending + 1 WHERE string = '" . $_POST['string'] . "'";
$result = mysql_query($query) OR error($query);
		
$query = "UPDATE users SET comments = comments + 1 WHERE id = '" . $userId . "'";
$result = mysql_query($query) OR error($query);
		
// Add the new comment
$query = "INSERT INTO discussions (string, user_id, twitter_id, comment, date, ipaddr, geoA, geoB) VALUES ('" . $_POST['string'] . "', '" . $userId . "', '" . $twitter_id . "', '" . strip_tags(addslashes($_POST['post'])) . "', '" . date('Y-m-d H:i:s') . "', '2222', '" . $_POST['geoA'] . "', '" . $_POST['geoB'] . "')";
$result = mysql_query($query) OR error($query);
$comment_id = mysql_insert_id();
if ($result == true)
    $response .= 'success||Your reply has been posted.<br><br>';
else
    $response .= 'error||There was a problem entering in the Tweet! Please try again later.';	
		
		
		
if ($reply == true && $author != '') {
	$query = "SELECT twitter_username FROM users WHERE twitter_id = '" . $twitter_id . "'";
	$result = mysql_query($query) OR error($query);
	$row = mysql_fetch_array($result);
	$author = $row['twitter_username'];
    }
		
    // Calculate total size of Tweet including @author and URL
$tweet =  '@' . $author . ' ' . urlencode(stripslashes(trim($_POST['post'])) . ' http://' . SITE_SHORT_URL . '/r/' . $comment_id);
$num_chars = strlen($tweet);
		
if ($num_chars > 140) {
    $strip_amount = $num_chars - 140;
    $tweet = '@' . $author . ' ' . urlencode(substr(stripslashes(trim($_POST['post'])), 0, -$strip_amount) . ' http://' . SITE_SHORT_URL . '/r/' . $comment_id);		
}
		
// Update Twitter status if they want this done
//		if ($twitter_reply_post == '1')
$data = run_twitter_request(TWITTER_UPDATE_URL, $_POST['username'], $_POST['password'], "status=" . $tweet);
		
    //	$response = twitter_auth_update('', $this->urlParts[0], $this->urlParts[1], $this->urlParts[3], true, $this->urlParts[4], $this->urlParts[5], false);
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