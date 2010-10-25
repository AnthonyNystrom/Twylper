<?php

include 'includes/config.php';
include 'includes/functions.php';
$page_title = 'Your Account';
include 'includes/header.php';

if (LOGGED_IN == false) {
	header('/login');
	exit();
}

?>

<div id="post_title"><h3>Your Account</h3></div><br clear=both><br>

<a href="/account/settings">Settings</a><br><br>

<?php

function settings($user_data) {
	echo '<h4>Settings</h4>';
	
	// If posting changes
	if ($_POST['submit'] == true) {
		if ($_POST['tweet_replies'] != '') {
			// Update their preference
			$update = "UPDATE users SET twitter_reply_post = '" . addslashes($_POST['tweet_replies']) . "' WHERE user = '" . addslashes($user_data['user']) . "' LIMIT 1";
			$result = mysql_query($update) OR die(mysql_error());
			
			if ($result == true)
				output('success', 'Your preferences have been updated.');
			else
				output('error', 'We could not update your preferences. Please try again later.');
				
			$hide = true;
		}
	}
	
	echo '<div id="responsemsg"></div>';
	
	// If no e-mail is on file, they must enter one before we can show notifications
	if ($user_data['email'] == '') {
		$hidden = 'style="display: none;"';
		echo '<div id="notify_text">Please provide your e-mail below so we can send you notifications. We promise, attest, and pinky-swear not to reveal it to anyone.<br><br><form id="save_email" name="save_email">E-mail: <input type="text" name="email" value="you@user.com" onclick="this.value=\'\'">&nbsp;&nbsp;<input type="submit" id="save_email" name="submit" value="Save"></form></div>';
	} else {
		$hidden = '';
	}

	// If they have a default preference
	if ($user_data['twitter_reply_post'] == '1')
		$tweet_reply_post1 = 'selected';
	else
		$tweet_reply_post0 = 'selected';
	
	if ($hide == false) {
		echo '
		<div id="notify_options" ' . $hidden . '>
		<form id="save_notify" name="save_notify" method="post" action="/account/settings">
		Tweet Replies:&nbsp;<select name="tweet_replies"><option value="">Please Select</option><option value="1" ' . $tweet_reply_post1 . '>Yes</option><option value="0" ' . $tweet_reply_post0 . '>No</option></select><br><br>
		<input type="submit" id="save_notify" name="submit" value="Save"></form>
		</div>';
	}
}

switch($_GET['action']) {

	case 'settings':
		settings($user_data);
		break;

}

?>

<?php include 'includes/footer.php'; ?>