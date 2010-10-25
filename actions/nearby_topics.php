<?php

include '../includes/config.php';
include '../includes/functions.php';

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
header('Content-type: application/json');

$shape = array();

//get bounds to search within

$lat_min = $_GET['lat_min'];
$lat_max = $_GET['lat_max'];
$lng_min = $_GET['lng_min'];
$lng_max = $_GET['lng_max'];

$query = "SELECT string, topics.comments, title, latitude, longitude, screen_name, profile_image_url FROM topics LEFT JOIN users ON users.id = topics.user_id WHERE latitude BETWEEN " . $lat_min . " and " . $lat_max . " AND longitude BETWEEN " . $lng_min . " and " . $lng_max . " ORDER BY topics.comments DESC LIMIT 0,15";
$result = mysql_query($query) OR error($query);

if (mysql_num_rows($result) > 0) {
	$i = 0;
	while($topic = mysql_fetch_assoc($result)) {
		$shape['topics'][$i] = $topic;
		$i++;
	}
}	

//echo "<pre>";
//print_r($place);
echo json_encode($shape);
?>