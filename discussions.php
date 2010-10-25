<?php

include 'includes/config.php';
include 'includes/functions.php';

// Discussion ID
if ($_GET['id'] != '') {
	$query = "SELECT * FROM topics LEFT JOIN users ON users.id = topics.user_id WHERE string = '" . makeSafe($_GET['id']) . "'";
	$result = mysql_query($query) OR error($query);
	if (mysql_num_rows($result) > 0) {
		// Fetch the topic and user data
		$topic = mysql_fetch_array($result);
		
		// Update that author read the thread
		if (isset($_SESSION['id'])){
		if ($_SESSION['id'] == $topic['user_id']) {
			$query = "UPDATE topics SET comments_pending = '0', last_read = NOW() WHERE string = '" . makeSafe($_GET['id']) . "'";
			$result = mysql_query($query) OR error($query);
		}
		}
	
		$page_title = $topic['title'];
		include 'includes/header.php';
		
		echo '<h3>' . $topic['title'] . '</h3><p>';

		echo '
		<div id="responsemsg"></div><br>
		
		<form id="post_comment" name="post_comment">
		<input type="hidden" name="string" id="string" value="' . $topic['string'] . '">
		<input type="hidden" name="tweet_id" id="tweet_id" value="' . $topic['tweet_id'] . '">
		<input type="hidden" name="screen_name" id="screen_name" value="' . $topic['user'] . '">
		<input type="hidden" name="place" id="place">
		<input type="hidden" name="reply" id="reply" value="true">
		';
				
		echo '		
		<div id="comment_top">&nbsp;</div>
			<div id="comment_body"><div id="comment_padding">
		';
				echo '<textarea name="tweet" rows="3" cols="60" id="comment_reply_text" class="expanding"></textarea>';
				echo '</div></div>';
				echo '<div id="comment_bottom">&nbsp;</div>';
				if (LOGGED_IN == true) {
				
					echo '<div class="comment_poster"><img class="comment_poster_img" src="' . $_SESSION['profile_image_url'] . '"><a class="comment_poster_username" href="/profile/' . $_SESSION['screen_name'] . '">' . $_SESSION['screen_name'] . '</a>' . add_location($_SESSION['geo_enabled']) . '</div><div id="comment_post_btn"><a id="comment_submit"><img src="' . SITE_IMG_PATH . '/btn_post.png" valign="top"/></a></div><div id="comment_poster_arrow"><img src="/images/comment_arrow.png"></div><br clear=both><br><br>';
				} else {
					echo '<div id="comment_twitter_login"><a href="/login.php"><img src="/images/darker.png" alt="Sign in with Twitter" align="absmiddle"/></a></div><div id="comment_poster_arrow"><img src="/images/comment_arrow.png"></div><br clear=both><br><br>';
				}
				echo '</form>';

		// Grab all of the comments
		echo '<div id="new_comment"></div>';
		echo '<div id="show_comments">';
		$query = "SELECT *, DATE_FORMAT(discussions.date, '%b %e, %Y at %l:%i %p') as fdate FROM discussions LEFT JOIN users ON users.id = discussions.user_id WHERE string = '" . makeSafe($_GET['id']) . "' ORDER BY discussions.date DESC";
		$result = mysql_query($query) OR error($query);
		if (mysql_num_rows($result) > 0) {
			while($comment = mysql_fetch_array($result)) {
				echo '<div id="comment_top">&nbsp;</div>';
				echo '<div id="comment_body"><div id="comment_padding">';
					echo '<a name="comment-' . $comment[0] . '"></a>' . $comment['comment'];
				echo '</div></div>';
				echo '<div id="comment_bottom">&nbsp;</div>';
				
				echo '<div class="comment_poster"><img class="comment_poster_img" src="' . $comment['profile_image_url'] . '"><a class="comment_poster_username" href="/profile/' . $comment['screen_name'] . '">' . $comment['screen_name'] . '</a> <div class="geo_status">' . $comment['fdate'] . '</div></div><div id="comment_post_btn"></div><div id="comment_poster_arrow" style="left: -285px;"><img src="/images/comment_arrow.png"></div><br clear=both><br><br>';
			}
		}
		echo '</div>';
	} else {
		include 'includes/header.php';
		output('error', 'We couldn\'t find the discussion.');
	}
} else {
	include 'includes/header.php';
	output('error', 'We couldn\'t find the discussion.');
}

include 'includes/footer.php';

?>