<?php

// Site settings
define('SITE_NAME', 'TwitRep.ly');
define('SITE_SHORT_URL', 'localhost');
define('BASE_URL', '/Twitreply');
define('RENDER_CLASS', 'xml.php');
define('STRING_LENGTH', 5);

define('RESULTS_PER_PAGE', 10);
define('NUM_TOP_USERS', 15);
define('NUM_THREADS', 10);


// Twitter API
define('TWITTER_VERIFY_URL', 'http://twitter.com/account/verify_credentials.xml');
define('TWITTER_UPDATE_URL', 'http://twitter.com/statuses/update.xml');


function run_twitter_request($curl_url, $login, $pass, $post = '') {
	/*---------------------------------------------------------------------------*/
	
	//Create the connection handle
	$curl_conn 	= curl_init();
	
	//Set cURL options
	
	curl_setopt($curl_conn, CURLOPT_URL, $curl_url); //URL to connect to
	
	if ($post != '') {
		curl_setopt($curl_conn, CURLOPT_POST,1);
		curl_setopt($curl_conn, CURLOPT_POSTFIELDS, $post);
	} else {
		curl_setopt($curl_conn, CURLOPT_GET, 1); //Use GET method
	}
	
	curl_setopt($curl_conn, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($curl_conn, CURLOPT_LOW_SPEED_LIMIT, 5);
	curl_setopt($curl_conn, CURLOPT_LOW_SPEED_TIME, 10);
	curl_setopt($curl_conn, CURLOPT_TIMEOUT, 5);
	curl_setopt($curl_conn, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); //Use basic authentication
	curl_setopt($curl_conn, CURLOPT_USERPWD, $login . ':' . $pass); //Set u/p
	curl_setopt($curl_conn, CURLOPT_SSL_VERIFYPEER, false); //Do not check SSL certificate (but use SSL of course), live dangerously!
	curl_setopt($curl_conn, CURLOPT_RETURNTRANSFER, true); //Return the result as string
	
	// Result from querying URL. Will parse as xml
	$output 	= @curl_exec($curl_conn);
	
	// close cURL resource. It's like shutting down the water when you're brushing your teeth.
	curl_close($curl_conn);
	
	if ($output === FALSE) {
		return '<error>Sorry, Twitter is currently unavailable. Please try again later.</error>';
	} else {
		return $output;
	}
}


function xml_parser($data) {
	/*---------------------------------------------------------------------------*/
	$xml_parser  =  xml_parser_create();
	xml_parse_into_struct($xml_parser, $data, $values); 
	xml_parser_free($xml_parser); 
	return $values;
}


function xml_return_value($data, $tag) {
	/*---------------------------------------------------------------------------*/
	$count = count($data);
	for ($i = 0; $i <= $count-1; $i++) {
		if (strtolower($data[$i]['tag']) == $tag) {
			$value= $data[$i]['value'];
			return $value;
		}
	}
}


function makeSafe($input) {
	//---------------------------------------------//
	return mysql_real_escape_string($input);
}

function generate_string() {
	//---------------------------------------------//
	// Random string
	$chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
	$string = '';
	for ($i = 1; $i <= STRING_LENGTH; $i++) {
		// Randomize the array
		$rand = array_rand($chars);
		$string .= $chars[$rand];
	}
	
	// Verify it is unique
	$query = "SELECT * FROM topics WHERE string = '" . $string . "'";
	$result = mysql_query($query) OR error($query);
	if (mysql_num_rows($result) == 0) {
		return $string;
	} else {
		generate_string();
	}
}


function error($query) {
    echo "Query: $query<br>MySQL Error: " . mysql_error();
    die();
}


function output($status, $message) {
    if ($status == 'error')
	$status = '<div id="error_outer"><div id="error_inner">';
    else
    	$status = '<div id="success_outer"><div id="success_inner">';
    echo $status . $message . '</div></div><br><br>';
}


function getUserId($userName, $pass) {
    $query = "SELECT * FROM users WHERE user = '" . makeSafe($userName) . "' AND pass = '" . makeSafe($pass) . "'";
    $user_check = mysql_query($query) OR error($query);
    if (mysql_num_rows($user_check) > 0) {
	$user_data = mysql_fetch_array($user_check);
	return $user_data[0];
    } else {
	return -1;
    }
}
	
/**
* Generate the HTTP response data.
*/
function generateResponseData($data, $colName, $className) {
	require_once(RENDER_CLASS);
	$renderer = new TwilperRenderer();
	$renderer->render($data,$colName, $className);
}

?>