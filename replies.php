<?php

include 'includes/config.php';
include 'includes/functions.php';

// User-defined sorting
$_GET = array_map('addslashes', $_GET);

if (isset($_GET['sort'])) {
if ($_GET['sort'] == 'recent' || $_GET['sort'] == '') {
	$sort	= 'topics.date';
	$sort_title = 'Recent Replies';
	$recent_css = 'bubble_nav2';
	$popular_css = 'bubble_nav';
	$url = '/recent';
} elseif ($_GET['sort'] == 'popular') {
	$sort	= 'topics.comments';
	$sort_title = 'Popular Replies';
	$recent_css = 'bubble_nav';
	$popular_css = 'bubble_nav2';
	$url = '/popular';
} elseif ($_GET['sort'] == 'username') {
	$sort 	= 'users.screen_name';
	$url = '/username';
	$sort_title = '';
}
} else {
	$url = '';
	$sort_title = '';	
}

$page_title = $sort_title;
include 'includes/header.php';

echo '<div id="topic_title"><h3>' . $sort_title . '</h3></div><p><br>';

// Get all topics
$query = "SELECT SQL_CALC_FOUND_ROWS *, DATE_FORMAT(discussions.date, '%b %e, %Y at %l:%i %p') as fdate FROM discussions LEFT JOIN topics ON topics.string = discussions.string LEFT JOIN users ON users.id = discussions.user_id ORDER BY discussions.date DESC LIMIT " . $start . ', ' . RESULTS_PER_PAGE;
$result = mysql_query($query) OR error($query);
$result2 = mysql_query("SELECT FOUND_ROWS()");
list($total_num) = mysql_fetch_row($result2);
if ($total_num > 0) {
	echo '<div id="topic_top">&nbsp;</div>';
		echo '<div id="topic_body"><div id="comment_padding">';
		// Fetch the topic and user data
		while($topic = mysql_fetch_array($result, MYSQL_BOTH)) {
		
			// Avatar
			if ($topic['profile_image_url'] != '') {
				$avatar = $topic['profile_image_url'];
			} else {
				$avatar = AVATAR_DEFAULT;
			}
			
			echo '<div id="topic_row">
				<div id="topic_img"><img src="' . $avatar . '"></div>
				<div id="topic_data">
					<a href="/' . $topic['string'] . '#comment-' . $topic[0] . '" title="' . $topic['title'] . ' Replies">' . ucfirst($topic['title']) . '</a>
					<div id="topic_meta_data"><a href="/profile/' . $topic['screen_name'] . '" title="Comments by '  . $topic['screen_name'] . '"><b>' . $topic['screen_name'] . '</b></a> said "' . $topic['comment'] . '" on ' . $topic['fdate'] . ' ... <a href="/' . $topic['string'] . '#comment-' . $topic[0] . '" title="' . $topic['title'] . ' Replies">read more</a>.</div>
				</div><br clear=both>
			</div>'; 
		}
		echo '</div><br></div>';
	echo '<div id="topic_bottom">&nbsp;</div><br>';
	pagenum($page, $total_num, '/replies' . $url);
} else {
	output('error', 'We couldn\'t find any replies. Try again later.');
}

include 'includes/footer.php';

?>