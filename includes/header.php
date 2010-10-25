<html>
<head>
	<title><?php if ($page_title == '') { echo SITE_META_TITLE; } else { echo $page_title; } ?></title>
	<meta name="keywords" content="twitreply,twitter replies,twitter comments,twitter commenting,twitter conversations,twitter topics, twitter application">
	<meta name="description" content="Add Commenting Ability To Your Twitter Tweets">
	<meta name="google-site-verification" content="y0lOzemt8X49WWLhX5_ac4YfJVP2AxT9xhZ5g1VyJNo" />
	<script src="/includes/js/jquery-1.3.2.min.js" type="text/javascript" language="javascript"></script>
	<script src="/includes/js/jquery.autogrow.js" type="text/javascript" language="javascript"></script>
	<script src="/includes/js/jquery.simplemodal.js" type="text/javascript" language="javascript"></script>
<?php if (isset($map)) { ?>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script src="/includes/js/map.js" type="text/javascript" language="javascript"></script>
<?php 	
} ?>
	<script src="/includes/js/main.js" type="text/javascript" language="javascript"></script>
	<link rel="stylesheet" href="/css/main.css" type="text/css">
</head>

<body>
<div id="container">
	<div id="header">
		<div id="logo"><a href="/index" title="Twitter Comment System"><img src="<?php echo SITE_IMG_PATH; ?>/logo.png"></a></div>
		<div id="navigation">
			<div id="login_bar">
				<?php if (LOGGED_IN) { 
					echo '<span class="greeting">';
					echo $_SESSION['screen_name'] . " | <a href=\"/logout.php\">Logout</a></span> <img style=\"padding: 0px 0px 2px 5px;\" src=\"" . $_SESSION['profile_image_url'] . "\" align=\"absmiddle\"/>"; 
					} else { 
					?>
					<span class="greeting">Hello, guest! </span><a href="/login.php"><img src="/images/darker.png" alt="Sign in with Twitter" align="absmiddle"/></a>
					<?php 
					} ?>
			
			</div>
			
			<div id="navigation_items">
				<div id="navigation_item"><a href="/index" title="Post <?php echo SITE_NAME; ?> Conversations"><img src="<?php echo SITE_IMG_PATH; ?>/nav_home<?php if ($_SERVER['PHP_SELF'] == '/index.php') { echo '_active'; } ?>.gif"></a></div>
				<div id="navigation_item"><a href="/map" title="<?php echo SITE_NAME; ?> Map"><img src="<?php echo SITE_IMG_PATH; ?>/nav_map<?php if ($_SERVER['PHP_SELF'] == '/map.php') { echo '_active'; } ?>.gif"></a></div>
				<div id="navigation_item"><a href="/topics" title="Post <?php echo SITE_NAME; ?> Topic Conversations"><img src="<?php echo SITE_IMG_PATH; ?>/nav_topics<?php if ($_SERVER['PHP_SELF'] == '/topics.php') { echo '_active'; } ?>.gif"></a></div>
				<div id="navigation_item"><a href="/replies" title="Recent <?php echo SITE_NAME; ?> Replies"><img src="<?php echo SITE_IMG_PATH; ?>/nav_replies<?php if ($_SERVER['PHP_SELF'] == '/replies.php') { echo '_active'; } ?>.gif"></a></div>
			</div>
		</div>
	</div>
	
	<div style="float: left; width: 600px">