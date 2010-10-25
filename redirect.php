<?php

include 'includes/config.php';
include 'includes/functions.php';

// Get the string and discussion ID
$query = "SELECT * FROM discussions WHERE id = '" . makeSafe($_GET['d_id']) . "'";
$result = mysql_query($query) OR error($query);

if (mysql_num_rows($result) > 0) {
	$row = mysql_fetch_array($result);
	
	header('Location: /' . $row['string'] . '#comment-' . $row['id']);
	exit();
}

?>