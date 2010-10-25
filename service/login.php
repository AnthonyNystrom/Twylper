<?php

include '../includes/config.php';
require_once('functions.php');
require_once('httpResponses.php');

$data = run_twitter_request(TWITTER_VERIFY_URL, $_POST['username'], $_POST['password']);
if (xml_return_value(xml_parser($data), 'error') == true) {
	notFound();		
} else {
	noContent();
}

?>