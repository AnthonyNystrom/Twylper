<?php

include '../includes/config.php';
require_once('functions.php');
require_once('httpResponses.php');

// Authenticate with Twitter
$data = run_twitter_request(TWITTER_VERIFY_URL, $_POST['username'], $_POST['password']);
		
if (xml_return_value(xml_parser($data), 'error') == true) {
	echo('error||' . xml_return_value(xml_parser($data), 'error'));
	notFound();
	return;
} else {
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
	//		if ($twitter_avatar != '') {
	//			$dir = substr($twitter_screen_name, 0, 1);
	//			$ext = strrchr($twitter_avatar, '.');
	//			$url = SITE_IMG_PATH . '/avatars/' . $dir . '/' . $twitter_screen_name . $ext; 
	//			
	//			if (copy($twitter_avatar, AVATAR_PATH . '/' . $dir . '/' . $twitter_screen_name . $ext))
	//				$local_avatar = true;
	//		}
	//		
			
	// Do they have an account?
	$query = "SELECT * FROM users WHERE user = '" . $_POST['username'] . "'";
			
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
		$query = "UPDATE users SET " . $screen_nameSQL . " twitter_name = '" . $twitter_name . "', twitter_bio = '" . $twitter_bio . "', twitter_avatar = '" . $twitter_avatar . "', twitter_url = '" . $twitter_url . "', twitter_location = '" . $twitter_location . "', twitter_background = '" . $twitter_background . "', ipaddr = '322223', pass = '" . $pass . "'" . $avatarSQL . " WHERE user = '" . $_POST['username'] . "' AND pass = '" . $_POST['password'] . "' LIMIT 1";
		$result = mysql_query($query) OR error($query);
	} else {
		// If no Twitter username
		if ($twitter_screen_name == '')
			$twitter_screen_name = $_POST['username'];
				
			// Create an account
			$query = "INSERT INTO users (user, pass, name, email, date, twitter_id, twitter_name, twitter_username, twitter_bio, twitter_avatar, twitter_url, twitter_location, twitter_background, ipaddr, local_avatar) VALUES ('" . $_POST['username'] . "', '" . $_POST['password'] . "', '" . $twitter_name . "', '', '" . $date . "', '" . $twitter_id . "', '" . $twitter_name . "', '" . $twitter_screen_name . "', '" . $twitter_description . "', '" . $twitter_avatar . "', '" . $twitter_url . "', '" . $twitter_location . "', '" . $twitter_background . "', '322223', '" . $local_avatar . "')";
			$result = mysql_query($query) OR error($query);
			$user_id = mysql_insert_id();
		}
			
		created();
	}

           
?>