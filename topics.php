<?php

include 'includes/config.php';
include 'includes/functions.php';

// Default CSS
$recent_css = 'bubble_nav';
$popular_css = 'bubble_nav';

// User-defined sorting
$_GET = array_map('addslashes', $_GET);

// If profile page, change link
if (isset($_GET['user'])) {
	$link = '/profile';
} else {
	$link = '/topics';
}

if (isset($_GET['sort'])) {
	if ($_GET['sort'] == 'recent' || $_GET['sort'] == '') {
		$sort	= 'topics.date';
		$sort2 = 'DESC';
		$sort_title = 'Recent Topics';
		$recent_css = 'bubble_nav2';
		$url = $link . '/recent';
	} elseif ($_GET['sort'] == 'popular') {
		$sort	= 'topics.comments';
		$sort2 = 'DESC';
		$sort_title = 'Popular Topics';
		$popular_css = 'bubble_nav2';
		$url = $link . '/popular';
	} else {
			$sort = 'topics.date';
			$sort_title = '';
			$sort2 = 'DESC';
			$url = $link;
	}
} else {
	$sort = 'topics.date';
	$sort_title = '';
	$sort2 = 'DESC';
	$url = $link;
}
// User specific page
if (isset($_GET['user'])) {
	if (isset($_GET['sort'])) {
	$sort_title = ucfirst($_GET['sort']) . ' Topics by ' . $_GET['user'];
	}
	$sql_query = ' WHERE topics.user = "' . $_GET['user'] . '" ';
	
	if (isset($_GET['sort'])) {
		$url2 = '/' . $_GET['sort'];
	} else {
		$url2 = '/';
	}
	
	$url = $link . '/' . $_GET['user'] . $url2;
	$link = $link . '/' . $_GET['user'];
	
} else {
	$sql_query = '';
}

$page_title = $sort_title;
include 'includes/header.php';

echo '
<div id="topic_title"><h3>' . $sort_title . '</h3></div>
<div id="topic_nav">
<div id="' . $recent_css . '" onClick="window.location=\'' . $link . '/recent\'"><a href="' . $link . '/recent">Recent</a></div>
<div id="' . $popular_css . '" onClick="window.location=\'' . $link . '/popular\'"><a href="' . $link . '/popular">Popular</a></div>
</div><p><br>';

// Get all topics
$query = "SELECT SQL_CALC_FOUND_ROWS *, DATE_FORMAT(topics.date, '%b %e, %Y at %l:%i %p') as fdate, topics.comments FROM topics LEFT JOIN users ON users.id = topics.user_id " . $sql_query . " ORDER BY " . $sort . " " . $sort2 . " LIMIT " . $start . ', ' . RESULTS_PER_PAGE;
$result = mysql_query($query) OR error($query);
$result2 = mysql_query("SELECT FOUND_ROWS()");
list($total_num) = mysql_fetch_row($result2);
if ($total_num > 0) {
	echo '<div id="topic_top">&nbsp;</div>';
		echo '<div id="topic_body"><div id="comment_padding">';
		// Fetch the topic and user data
		while($topic = mysql_fetch_array($result)) {

			// Avatar
			if ($topic['profile_image_url'] != '') {
				$avatar = $topic['profile_image_url'];
			} else {
				$avatar = AVATAR_DEFAULT;
			}
		
			echo '<div id="topic_row">
				<div id="topic_img"><img src="' . $avatar . '"></div>
				<div id="topic_data">
					<a href="/' . $topic['string'] . '" title="' . $topic['title'] . ' Replies">' . ucfirst($topic['title']) . '</a>
					<div id="topic_meta_data"><a href="/profile/' . $topic['screen_name'] . '" title="Twitter Comments by ' . $topic['screen_name'] . '"><b>' . $topic['screen_name'] . '</b></a> on ' . $topic['fdate'] . ' with <a href="/' . $topic['string'] . '" title="' . $topic['title'] . ' Replies">' . $topic['comments'] . ' comments</a></div>
				</div><br clear=both>
			</div>'; 
		}
		echo '</div><br></div>';
	echo '<div id="topic_bottom">&nbsp;</div><br>';
	pagenum($page, $total_num, $url);
} else {
	output('error', 'We couldn\'t find any discussions. Try again later.');
}

include 'includes/footer.php';

?>