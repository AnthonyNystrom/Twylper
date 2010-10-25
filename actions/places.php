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
//Search for place based on IP or place_id
if (!isset($_GET['place_id'])) {
	$ip = $_SERVER['REMOTE_ADDR'];
	$places = $connection->post('geo/search', array('ip' => $ip));
	$place_id = $places->result->places[0]->id;
} else {
	$place_id = $_GET['place_id'];
}

$place = $connection->get('geo/id/' . $place_id);

$shape = array();
$i = 0;
foreach ($place->geometry->coordinates as $c => $polygon) {
	$j = 0;
	foreach ($polygon as $coords) {
		if (is_array($coords[0])) {
			foreach ($coords as $key => $co) {
				$shape['places'][$c][$key]['lng'] = $co[0];
				$shape['places'][$c][$key]['lat'] = $co[1];
			}
		} else {
			$shape['places'][0][$j]['lng'] = $coords[0];
			$shape['places'][0][$j]['lat'] = $coords[1];
		}
		$j++;
	}
	$i++;
}

//find center 
$lat = 0;
$lng = 0;
foreach ($place->bounding_box->coordinates[0] as $coord) {
	$lng += $coord[0];
	$lat += $coord[1];
}

$lat = round($lat / 4, 6);
$lng = round($lng / 4, 6);

$shape['center']['lat'] = $lat;
$shape['center']['lng'] = $lng;
$shape['location'] = $place->full_name;

//echo "<pre>";
//print_r($place);
echo json_encode($shape);
?>