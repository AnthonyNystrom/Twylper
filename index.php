<?php 

$index_page = 1;
include 'includes/config.php';
include 'includes/functions.php';
$page_title = SITE_NAME . ' - ' . 'Tweet. Discuss. Simple.';
include 'includes/header.php';

if (LOGGED_IN == false)
	echo '<center><img src="' . SITE_IMG_PATH . '/front_promo.gif"></center><p>';

?>

<div id="post_title"><h3>Create a New Discussion Topic</h3></div><div id="characters">116 characters remaining</div><br clear=both><br>

<?php

echo '
<div id="responsemsg"></div>

<form id="post_topic" name="post_topic">
<input type="hidden" name="reply" id="reply" value="false">
<input type="hidden" name="place" id="place">
';

	echo '<div id="post_top">&nbsp;</div>';
	echo '<div id="post_body"><div id="comment_padding">';
		echo '<textarea name="tweet" rows="3" cols="60" id="tweet" style="font-size: 18pt" class="expanding"></textarea>';
	echo '</div></div>';
	echo '<div id="post_bottom">&nbsp;</div>';
		if (LOGGED_IN == true) {
		
			echo '<div class="comment_poster"><img class="comment_poster_img" src="' . $_SESSION['profile_image_url'] . '"><a class="comment_poster_username" href="/profile/' . $_SESSION['screen_name'] . '">' . $_SESSION['screen_name'] . '</a>' . add_location($_SESSION['geo_enabled']) . '</div><div id="comment_post_btn"><a id="topic_submit"><img src="' . SITE_IMG_PATH . '/btn_post.png" valign="top"/></a></div><div id="comment_poster_arrow"><img src="/images/comment_arrow.png"></div><br clear=both><br><br>';
		} else {
			echo '<div id="comment_twitter_login"><a href="/login.php"><img src="/images/darker.png" alt="Sign in with Twitter" align="absmiddle"/></a></div><div id="comment_poster_arrow"><img src="/images/comment_arrow.png"></div><br clear=both><br><br>';
		}
echo '</form>';

// Show recent discussion topics
if (LOGGED_IN == true) {
	echo '<h4>Recent Discussion Topics</h4>';

	// Show most recent theads
	$query = "SELECT * FROM topics WHERE user_id = '" . $_SESSION['id'] . "' ORDER BY date DESC LIMIT " . NUM_THREADS;
	$result = mysql_query($query) OR error($query);
	$num = 1;

	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_array($result)) {
			if (strlen($row['title']) > 70) {
				$row['title'] = substr($row['title'], 0, 50) . '...';
			}
			
			if ($row['comments_pending'] == 1)
				$s = '';
			else
				$s = 's';
		
			echo '
			<div id="recent_topics_row"><div id="recent_topics_topic"><a href="/' . $row['string'] . '">' . ucfirst($row['title']) . '</a></div><div id="recent_topics_posts">' . $row['comments_pending'] . ' comment' . $s . ' unread</div></div>
			';
			$num++;
		}
	} else {
		output('error', 'You don\'t have any discussion topics! Create one above.');
	}
	
	echo '<br clear=both>';
}

include 'includes/footer.php';

?>