<?php

include '../includes/config.php';
include '../includes/functions.php';

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
header('Content-type: application/json');


if (isset($_SESSION['oauth_token'])) {
	//logged in, use users oauth token
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
} else {
	//not logged in, make request without authentication
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
}
// OLD - Search twitter places
//$query = $_GET['query'];
//$places = $connection->get('geo/search', array('query' => $query, 'max_results' => '15', 'granularity' => 'neighborhood'));

$query = $_GET['query'];
$request = "http://maps.google.com/maps/api/geocode/json?address=" . urlencode($query) . "&sensor=false";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $request);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);

$geo = json_decode($output);

$lat = $geo->results[0]->geometry->location->lat;
$lng = $geo->results[0]->geometry->location->lng;

$places = $connection->get('geo/search', array('lat' => $lat, 'long' => $lng, 'max_results' => '15', 'granularity' => 'neighborhood'));


$response = array();
if (isset($places->result->places[0])) {
	foreach ($places->result->places as $i => $place) {
		$response['places'][$i]['full_name'] = $place->full_name;
		$response['places'][$i]['id'] = $place->id;	
	}
} else {
	$response['places'] = 'none';
}

//echo "<pre>";
//print_r($geo);
echo json_encode($response);
?>