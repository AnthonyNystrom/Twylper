<html>
<head>
	<title><?php if ($page_title == '') { echo SITE_META_TITLE; } else { echo $page_title; } ?></title>
	<meta name="keywords" content="twitreply,twitter replies,twitter comments,twitter commenting,twitter conversations,twitter topics, twitter application">
	<meta name="description" content="Add Commenting Ability To Your Twitter Tweets">
	<script src="/includes/js/jquery-1.3.2.min.js" type="text/javascript" language="javascript"></script>
	<script src="/includes/js/jquery.autogrow.js" type="text/javascript" language="javascript"></script>
	<script src="/includes/js/jquery.simplemodal.js" type="text/javascript" language="javascript"></script>
	<script src="/includes/js/main_wave.js" type="text/javascript" language="javascript"></script>
	<link rel="stylesheet" href="/css/main.css" type="text/css">
</head>

<body>
<div id="container">
	<div id="header">
		<div id="logo"><a href="/index" title="Twitter Comment System"><img src="/images/twitreply_logo.png"></a></div>
		<div id="navigation">
			<div id="<?php if ($PHP_SELF == '/index.php') { echo 'navigation_tab_active'; } else { echo 'navigation_tab'; } ?>"><a href="/index" title="Post <?php echo SITE_NAME; ?> Conversations">Home</a></div>
			<div id="<?php if ($PHP_SELF == '/topics.php') { echo 'navigation_tab_active'; } else { echo 'navigation_tab'; } ?>"><a href="/topics" title="<?php echo SITE_NAME; ?> Topic Conversations">Topics</a></div>
			<div id="<?php if ($PHP_SELF == '/replies.php') { echo 'navigation_tab_active'; } else { echo 'navigation_tab'; } ?>"><a href="/replies" title="Recent <?php echo SITE_NAME; ?> Replies">Replies</a></div>
			<div id="<?php if ($PHP_SELF == '/login.php') { echo 'navigation_tab_active'; } else { echo 'navigation_tab'; } ?>"><?php if (LOGGED_IN == true) echo '<a href="/logout" title="Log Out">Logout</a>'; else echo '<a href="/login" title="Log In">Login</a>'; ?></div>
		</div>
	</div>
	
	<div style="float: left; width: 600px">