<?php

include '../includes/config.php';
include '../includes/functions_wave.php';

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0

// Reply?
if ($_POST['user'] == 'undefined' || $_POST['pass'] == 'undefined') {
	$info = explode(':', base64_decode($_COOKIE[COOKIE_NAME]));
	$user = $info[0];
	$pass = $info[1];
} else {
	$user = $_POST['user'];
	$pass = $_POST['pass'];
}

if ($_POST['reply'] == true) {
	$response = twitter_auth_update($_POST['tweet'], $user, $pass, $post, true, $_POST['string'], $_POST['author']);
// New topic
} else {
	$response = twitter_auth_update($_POST['tweet'], $user, $pass, $post);
}

//echo 'error||' . $response;

echo $response;

?>