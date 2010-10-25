<?php

include '../includes/config.php';
include '../includes/functions.php';

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0

// Save the e-mail
if ($_POST['email'] != '' && LOGGED_IN == true) {

	// Verify e-mail
	if (check_email($_POST['email']) == false) {
		echo 'error||You did not enter in a valid e-mail.';
		return false;
	}
		
	$query = "UPDATE users SET email = '" . makeSafe($_POST['email']) . "' WHERE twitter_username = '" . makeSafe($user_data['twitter_username']) . "' LIMIT 1";
	$result = mysql_query($query) OR error($query);
	
	if ($result == true) {
		echo 'success||Your e-mail has been updated.';
		return true;
	} else {
		echo 'error||Your e-mail could not be updated. Please try again later.';
		return false;
	}
} else {
	echo 'error||Not logged in or e-mail is blank.';
	return false;
}

?>