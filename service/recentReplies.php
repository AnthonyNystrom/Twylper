<?php

include '../includes/config.php';
require_once('functions.php');
require_once('httpResponses.php');

$topics = array();
$userId = getUserId($_POST['username'], $_POST['password']);
if ($userId == -1)
{
    unauthorized();
    return;
}		
$query = "SELECT * FROM discussions ORDER BY date DESC LIMIT " . NUM_THREADS;;
	
$result = mysql_query($query) OR error($query);
		
if (mysql_num_rows($result) > 0) {
while($topic = mysql_fetch_array($result)) {
    array_push($topics, $topic);	
    }
}
else
{
    notFound();
    return;
}
mysql_free_result($result);
		
generateResponseData($topics, 'topics', 'topic' );	
ok();

?>