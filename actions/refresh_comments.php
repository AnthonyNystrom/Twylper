<?php

include '../includes/config.php';
include '../includes/functions.php';

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0

// Refresh the newly posted comment
$query = "SELECT *, DATE_FORMAT(discussions.date, '%b %e, %Y at %l:%i %p') as fdate FROM discussions LEFT JOIN users ON users.id = discussions.user_id WHERE discussions.id = '" . makeSafe($_GET['id']) . "'";
$result = mysql_query($query) OR error($query);
if (mysql_num_rows($result) > 0) {
	$comment = mysql_fetch_array($result);
	echo '<div id="comment_top">&nbsp;</div>';
	echo '<div id="comment_body"><div id="comment_padding">';
		echo $comment['comment'];
	echo '</div></div>';
	echo '<div id="comment_bottom">&nbsp;</div>';
	
	// Avatar
	if ($comment['profile_image_url'] != '') {
		$avatar = $comment['profile_image_url'];
	} else {
		$avatar = AVATAR_DEFAULT;
	}

	echo '<div class="comment_poster"><img class="comment_poster_img" src="' . $comment['profile_image_url'] . '"><a class="comment_poster_username" href="/profile/' . $comment['screen_name'] . '">' . $comment['screen_name'] . '</a> <div class="geo_status">' . $comment['fdate'] . '</div></div><div id="comment_post_btn"></div><div id="comment_poster_arrow" style="left: -285px;"><img src="/images/comment_arrow.png"></div><br clear=both><br><br>';
}

?>