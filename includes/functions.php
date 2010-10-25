<?php

// Pagination
if (isset($_GET['page'])) {
	$start = ($_GET['page'] - 1) * RESULTS_PER_PAGE;
	$page = $_GET['page'];
} else {
	$page = 1;
	$start = 0;
}

session_start();
require_once('/home/twylper/public_html/twitteroauth/twitteroauth.php');
require_once('config.php');

/* If access tokens are not available redirect to connect page. */
if (!empty($_SESSION['oauth_token']) || !empty($_SESSION['oauth_token_secret'])) {
	define('LOGGED_IN', true);
} else {
	//---------------------------------------------//
	if (isset($_COOKIE[COOKIE_NAME])) {
	//---------------------------------------------//
		// Validate their information
		$info = explode(':', base64_decode($_COOKIE[COOKIE_NAME]));
		$query = "SELECT * FROM users WHERE oauth_token = '" . makeSafe($info[0]) . "' AND oauth_token_secret = '" . makeSafe($info[1]) . "'";
		$user_check = mysql_query($query) OR error($query);
		if (mysql_num_rows($user_check) > 0) {
			define('LOGGED_IN', true);
			$_SESSION = mysql_fetch_array($user_check, MYSQL_ASSOC);
		} else {
			define('LOGGED_IN', false);
		}
	} else {
		define('LOGGED_IN', false);
	}
}

//---------------------------------------------//
function add_location($geo_enabled) {
//---------------------------------------------//
	if ($geo_enabled == 1) {
		$output = '<div id="position_container" class="geo_status"></div>';
	} else {
		$output = '<div class="geo_status"> Add a location to your tweets. <a href="#" id="show_geo_dialog">Turn it on</a></div>
				   <div id="location_setting">
					<iframe width="100%" scrolling="no" height="365" frameborder="0" src="http://twitter.com/account/settings/geo">
				  		<p>Your browser does not support iframes. Please visit http://twitter.com/account/settings/geo</p>
					</iframe>
					<div id="close_settings"><a href="/actions/refresh_account.php">Close</a></div>
				   </div>';
	}
	
	return $output;
}

//---------------------------------------------//
function pagenum($current, $totalnum, $currenturl, $shownum_override = '') {
//---------------------------------------------//
	if ($shownum_override == '') 
		$shownum	= RESULTS_PER_PAGE;
	else
		$shownum 	= $shownum_override;

		$totalnum = ceil($totalnum/$shownum);

	if ($totalnum > 1) {
		$previous	= $current - 1;
		$next		= $current + 1;

		echo "<table border=\"0\" cellpadding=\"6\" cellspacing=\"3\" id=\"pagination\"><tr>";

		if ($previous >= 1) {
			echo "<td align=\"center\" class=\"pagenum\"><a href=\"$currenturl/$previous\">Previous</a></td>";
		} else {
			#echo "<td align=\"center\" class=\"pagenum\">Previous</td>";
		}

		$start	= $current - 2;
		
		/* If this is page 1, do end + 4 */
		if ($current == 1)
			$end	= $current+4;
		elseif ($current == 2)
			$end	= $current+3;
		elseif ($current > 2) {
			$end	= $current+2;
		}
		
		for ($i = $start; $i <= $end; $i++) { 
			if ($i > 0 && $i <= $totalnum) {
				if ($i == $current) {
					/* New pagination */
					echo "<td align=\"center\" id=\"pagenumcurrent\">$i</td>";
				} else {
					/* New pagination */
					echo "<td align=\"center\" class=\"pagenum\"><a href=\"$currenturl/$i\">$i</a></td>";
				}
			}
		}
		
		if ($next >= 1 && $next <= $totalnum) {
			echo "<td align=\"center\" id=\"pagenum\"><a href=\"$currenturl/$next\">Next</a></td>";
		} else {
			#echo "<td align=\"center\" id=\"pagenum\">Next</td>";
		}

		echo "</tr></table><br>";
	}
}

/*---------------------------------------------------------------------------*/
function check_email($email) {
/*---------------------------------------------------------------------------*/
   $isValid = true;
   $atIndex = strrpos($email, "@");
   
   if (is_bool($atIndex) && !$atIndex) {
	  $isValid = false;
   } else {
	  $domain = substr($email, $atIndex+1);
	  $local = substr($email, 0, $atIndex);
	  $localLen = strlen($local);
	  $domainLen = strlen($domain);
	  if ($localLen < 1 || $localLen > 64)
	  {
		 // local part length exceeded
		 $isValid = false;
	  }
	  else if ($domainLen < 1 || $domainLen > 255)
	  {
		 // domain part length exceeded
		 $isValid = false;
	  }
	  else if ($local[0] == '.' || $local[$localLen-1] == '.')
	  {
		 // local part starts or ends with '.'
		 $isValid = false;
	  }
	  else if (preg_match('/\\.\\./', $local))
	  {
		 // local part has two consecutive dots
		 $isValid = false;
	  }
	  else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
	  {
		 // character not valid in domain part
		 $isValid = false;
	  }
	  else if (preg_match('/\\.\\./', $domain))
	  {
		 // domain part has two consecutive dots
		 $isValid = false;
	  }
	  else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
				 str_replace("\\\\","",$local)))
	  {
		 // character not valid in local part unless 
		 // local part is quoted
		 if (!preg_match('/^"(\\\\"|[^"])+"$/',
			 str_replace("\\\\","",$local)))
		 {
			$isValid = false;
		 }
	  }
	  if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
	  {
		 // domain not found in DNS
		 $isValid = false;
	  }
   }
   
   return $isValid;
}

//---------------------------------------------//
function show_topics() {
//---------------------------------------------//

}

//---------------------------------------------//
function show_comments() {
//---------------------------------------------//

}

//---------------------------------------------//
function makeSafe($input) {
//---------------------------------------------//
	return mysql_real_escape_string($input);
}

//---------------------------------------------//
function error($query) {
//---------------------------------------------//
	echo "Query: $query<br>MySQL Error: " . mysql_error();
	die();
}

//---------------------------------------------//
function output($status, $message) {
//---------------------------------------------//
	if ($status == 'error')
		$status = '<div id="error_outer"><div id="error_inner">';
	else
		$status = '<div id="success_outer"><div id="success_inner">';
	echo $status . $message . '</div></div><br><br>';
}

//---------------------------------------------//
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

/*---------------------------------------------------------------------------*/
function twitter_auth_update($tweet, $user = '', $pass = '', $post, $reply = false, $string = '', $author = '', $login_only = false) {
/*---------------------------------------------------------------------------*/
	// Container for all responses
	$responses = '';
	
	// Are we using a cookie to authenticate?
	if ($user == '' || $pass == '') {
		$info = explode(':', base64_decode($_COOKIE[COOKIE_NAME]));
		$user = $info[0];
		$pass = $info[1];
	}

	// Authenticate with Twitter
	$data = run_twitter_request(TWITTER_VERIFY_URL, $user, $pass, $post);

	if (xml_return_value(xml_parser($data), 'error') == true) {
		$auth = false;
		return 'error||' . xml_return_value(xml_parser($data), 'error');
	} else {
		// OK, verification accepted.. continue..
		$auth = true;
	
		// Update their account if one exists
		$twitter_id = xml_return_value(xml_parser($data), 'id');
		$twitter_name = xml_return_value(xml_parser($data), 'name');
		$twitter_screen_name = xml_return_value(xml_parser($data), 'screen_name');
		$twitter_bio = xml_return_value(xml_parser($data), 'description');
		$twitter_avatar = xml_return_value(xml_parser($data), 'profile_image_url');
		$twitter_url = xml_return_value(xml_parser($data), 'url');
		$twitter_location = xml_return_value(xml_parser($data), 'location');
		$twitter_background = xml_return_value(xml_parser($data), 'profile_background_image');
		
		$ipaddr = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d H:i:s');
		
		// Save their avatar locally
		if ($twitter_avatar != '') {
			$dir = substr($twitter_screen_name, 0, 1);
			$ext = strrchr($twitter_avatar, '.');
			$url = SITE_IMG_PATH . '/avatars/' . $dir . '/' . $twitter_screen_name . $ext; 
			
			if (copy($twitter_avatar, AVATAR_PATH . '/' . $dir . '/' . $twitter_screen_name . $ext))
				$local_avatar = true;
		}
		
		// Do they have an account?
		$query = "SELECT * FROM users WHERE user = '" . $user . "'";
		$result = mysql_query($query) OR error($query);
		if (mysql_num_rows($result) > 0) {
			// Get their user ID
			$row = mysql_fetch_array($result);
			$user_id = $row['id'];
			
			// User preferences
			$twitter_reply_post = $row['twitter_reply_post'];
			$twitter_topic_post = $row['twitter_topic_post'];
			$notify_reply = $row['notify_reply'];
			
			if ($local_avatar == true) {
				$avatarSQL = ", local_avatar = '" . $url . "' ";
				$local_avatar = $url;
			}

			if ($twitter_screen_name != '') {
				$screen_nameSQL = " twitter_username = '" . $twitter_screen_name . "', ";
			}

			// They have an account, update it..
			$query = "UPDATE users SET " . $screen_nameSQL . " twitter_name = '" . $twitter_name . "', twitter_bio = '" . $twitter_bio . "', twitter_avatar = '" . $twitter_avatar . "', twitter_url = '" . $twitter_url . "', twitter_location = '" . $twitter_location . "', twitter_background = '" . $twitter_background . "', ipaddr = '" . $ipaddr . "', pass = '" . $pass . "'" . $avatarSQL . " WHERE user = '" . $user . "' AND pass = '" . $pass . "' LIMIT 1";
			$result = mysql_query($query) OR error($query);
		} else {
			// If no Twitter username
			if ($twitter_screen_name == '')
				$twitter_screen_name = $user;
		
			// Create an account
			$query = "INSERT INTO users (user, pass, name, date, twitter_id, twitter_name, twitter_username, twitter_bio, twitter_avatar, twitter_url, twitter_location, twitter_background, ipaddr, local_avatar) VALUES ('" . $user . "', '" . $pass . "', '" . $twitter_name . "', '" . $date . "', '" . $twitter_id . "', '" . $twitter_name . "', '" . $twitter_screen_name . "', '" . $twitter_description . "', '" . $twitter_avatar . "', '" . $twitter_url . "', '" . $twitter_location . "', '" . $twitter_background . "', '" . $ipaddr . "', '" . $local_avatar . "')";
			$result = mysql_query($query) OR error($query);
			$user_id = mysql_insert_id();
		}

		// Auto log them in
		$cookie = base64_encode($user . ':' . $pass);
		setcookie(COOKIE_NAME, $cookie, time()+60*60*24*365, '/', COOKIE_DOMAIN);

		if ($login_only == true) {
			if ($auth == true)
				return true;
			else
				return false;
		}

		// If no error, record Twitter ID
		if (xml_return_value(xml_parser($data), 'error') == false) {

			// Post their topic
			if ($reply == false && $_POST['tweet'] != '') {
				$tweet_id = xml_return_value(xml_parser($data), 'id');

				// Generate a unique string
				$string = generate_string();
				
				// Increase their topic count
				$query = "UPDATE users SET num_topics = num_topics + 1 WHERE id = '" . $user_id . "'";
				$result = mysql_query($query) OR error($query);

				// Insert the new discussion topic
				$query = "INSERT INTO topics (user_id, date, twitter_id, string, title, user) VALUES ('" . $user_id . "', '" . $date . "', '" . $twitter_id . "', '" . $string . "', '" . strip_tags(addslashes($_POST['tweet'])) . "', '" . $user . "')";
				$result = mysql_query($query) OR error($query);
				$comment_id = mysql_insert_id();
				if ($result == true)
					$response .= 'success||Your Tweet has been posted and users can now <b><a href="/' . $string . '">discuss</a></b> this Tweet!';
				else
					$response .= 'error||There was a problem entering in the Tweet! Please try again later.';

				$use_response = true;
			} elseif ($reply == true && $_POST['tweet'] != '') {
				$tweet_id = xml_return_value(xml_parser($data), 'id');
	
				// Update comment count
				$query = "UPDATE topics SET comments = comments + 1, comments_pending = comments_pending + 1 WHERE string = '" . $string . "'";
				$result = mysql_query($query) OR error($query);

				$query = "UPDATE users SET comments = comments + 1 WHERE id = '" . $user_id . "'";
				$result = mysql_query($query) OR error($query);

				// Add the new comment
				$query = "INSERT INTO discussions (string, user_id, twitter_id, comment, date, ipaddr) VALUES ('" . $string . "', '" . $user_id . "', '" . $twitter_id . "', '" . strip_tags(addslashes($_POST['tweet'])) . "', '" . date('Y-m-d H:i:s') . "', INET_ATON('" . $_SERVER['REMOTE_ADDR'] . "'))";
				$result = mysql_query($query) OR error($query);
				$comment_id = mysql_insert_id();
				if ($result == true)
					$response .= 'success||Your reply has been posted.<br><br>';
				else
					$response .= 'error||There was a problem entering in the Tweet! Please try again later.';	
			}
		 
			// Get actual author username if this is a reply
			if ($reply == true && $author != '') {
				$query = "SELECT twitter_username FROM users WHERE twitter_id = '" . $author . "'";
				$result = mysql_query($query) OR error($query);
				$row = mysql_fetch_array($result);
				$author = $row['twitter_username'];
			}
			
			// Post this topic to Twitter and record Twitter ID				
			if ($reply == false) {
				$url = ' http://' . SITE_SHORT_URL . '/' . $string;
				
				$tweet = urlencode(stripslashes(trim($_POST['tweet'])) . $url);
				$num_chars = strlen($tweet);
				
				if ($num_chars > 140)
				{
					$strip_amount = $num_chars - 140;
					$tweet = urlencode(substr(stripslashes(trim($_POST['tweet'])), 0, -$strip_amount) . $url);
				}
			
				// Update Twitter status if they want this done
				if ($twitter_topic_post == '1')
				{
					$data = run_twitter_request(TWITTER_UPDATE_URL, $user, $pass, "status=" . $tweet);
					//mail('brian@wirke.com', 'TwitReply Debug', $tweet);
				}
				
			} else {
				// Calculate total size of Tweet including @author and URL
				$tweet =  '@' . $author . ' ' . urlencode(stripslashes(trim($_POST['tweet'])) . ' http://' . SITE_SHORT_URL . '/r/' . $comment_id);
				$num_chars = strlen($tweet);
				
				if ($num_chars > 140) {
					$strip_amount = $num_chars - 140;
					$tweet = '@' . $author . ' ' . urlencode(substr(stripslashes(trim($_POST['tweet'])), 0, -$strip_amount) . ' http://' . SITE_SHORT_URL . '/r/' . $comment_id);
					
				}
				
				// Update Twitter status if they want this done
				if ($twitter_reply_post == '1')
					$data = run_twitter_request(TWITTER_UPDATE_URL, $user, $pass, "status=" . $tweet);
			}
			
			// Return their comment, comment ID, etc.
			if ($use_response == true) {
				return $response;
			} else {
				return 'data||' . $_POST['tweet'] . '||' . $comment_id;
			}
		} else {
			return 'error||' . xml_return_value(xml_parser($data), 'error');
		}
	}
}

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