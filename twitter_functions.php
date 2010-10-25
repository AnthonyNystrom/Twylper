<?php

/*---------------------------------------------------------------------------*/
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

/*---------------------------------------------------------------------------*/
function xml_parser($data) {
/*---------------------------------------------------------------------------*/
	$xml_parser  =  xml_parser_create();
	xml_parse_into_struct($xml_parser, $data, $values); 
	xml_parser_free($xml_parser); 
	return $values;
}

/*---------------------------------------------------------------------------*/
function xml_return_value($data, $tag) {
/*---------------------------------------------------------------------------*/
	$count		= count($data);
	for ($i = 0; $i <= $count-1; $i++) {
		if (strtolower($data[$i]['tag']) == $tag) {
			$value= $data[$i]['value'];
			return $value;
		}
	}
}


?>