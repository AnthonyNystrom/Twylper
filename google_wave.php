<?php 

include 'includes/config.php';
include 'includes/functions_wave.php';
$page_title = 'TwitReply - Tweet. Discuss. Simple.';
include 'includes/header_wave.php';

if (LOGGED_IN == false)
	echo '<img src="' . SITE_IMG_PATH . '/promo_wave.png"><p>';

?>

<div id="post_title"><h3>Create a New Discussion Topic</h3></div><div id="characters">116 characters remaining</div><br clear=both><br>

<?php

echo '
<div id="responsemsg"></div>

<form id="post_topic" name="post_topic">
<input type="hidden" name="author" value="' . $user_data['twitter_id'] . '">
<input type="hidden" name="reply" value="false">
';

if (LOGGED_IN == true) {
	echo '
	<input type="hidden" name="user" value="' . $user_data['user'] . '">
	<input type="hidden" name="pass" value="' . $user_data['pass'] . '">
	';
}

	echo '<div id="post_top">&nbsp;</div>';
	echo '<div id="post_body"><div id="comment_padding">';
		echo '<textarea name="tweet" rows="3" cols="60" id="tweet" style="font-size: 18pt" class="expanding"></textarea>';
	echo '</div></div>';
	echo '<div id="post_bottom">&nbsp;</div>';
		if (LOGGED_IN == true) {
		
			// Avatar
			if ($user_data['local_avatar'] != '') {
				$avatar = $user_data['local_avatar'];
			} elseif ($user_data['twitter_avatar'] != '') {
				$avatar = $user_data['twitter_avatar'];
			} else {
				$avatar = AVATAR_DEFAULT;
			}
		
			echo '<div id="comment_poster_arrow"><img src="/images/comment_arrow.png"></div><div id="comment_poster_img"><img src="' . $avatar . '"></div><div id="comment_poster_user_data"><a href="/profile/' . $user_data['user'] . '">' . $user_data['user'] . '</a></div><div id="comment_post_btn"><input type="image" src="' . SITE_IMG_PATH . '/btn_post.png" align="absmiddle" id="topic_submit"></div><br clear=both><br><br>';
		} else {
			echo '<div id="comment_poster_arrow"><img src="/images/comment_arrow.png"></div><div id="comment_twitter_login"><img src="' . SITE_IMG_PATH . '/twitter_login.png" align="absmiddle">&nbsp;<input type="text" id="login_box" name="user" value="twitter username">&nbsp;<input type="password" id="login_box" name="pass" value="password">&nbsp;<input type="image" src="' . SITE_IMG_PATH . '/btn_post.png" align="absmiddle" id="topic_submit"></div><br clear=both><br><br>';
		}
echo '</form>';

// Show recent discussion topics
if (LOGGED_IN == true) {
	echo '<h4>Recent Discussion Topics</h4>';

	// Show most recent theads
	$query = "SELECT * FROM topics WHERE user_id = '" . $user_data['id'] . "' ORDER BY date DESC LIMIT " . NUM_THREADS;
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