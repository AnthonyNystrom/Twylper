<?php
/**
 * @file
 * User has successfully authenticated with Twitter. Access tokens saved to session and DB.
 */
echo "<pre>";
/* Load required lib files. */
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('includes/config.php');

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

// Show top users according to total comments
$query = "SELECT topics.id, INET_NTOA(discussions.ipaddr) FROM topics, discussions WHERE topics.user_id = discussions.user_id GROUP BY topics.id";
$result = mysql_query($query) OR error($query);
			
if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$status = $connection->post('geo/search', array('ip' => $row[1]));
		//geo info
		$lat = 0;
		$long = 0;
		foreach ($status->result->places[0]->bounding_box->coordinates[0] as $coord) {
			$long += $coord[0];
			$lat += $coord[1];
		}
		$lat = round($lat / 4, 6);
		$long = round($long / 4, 6);
		$place_id = $status->result->places[0]->id;
		
		$query = "UPDATE topics SET place_id = '" . $place_id . "', latitude = '" . $lat . "', longitude = '" . $long . "' WHERE id = '" . $row[0] . "'";
		$res = mysql_query($query) OR error($query);
		echo $query;
		echo "<br>";
	}
}	

	
	/* If method is set change API call made. Test is called by default. */
//$content = $connection->get('account/verify_credentials');
?>