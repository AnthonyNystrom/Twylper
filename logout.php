<?php

include 'includes/config.php';
//include 'includes/functions.php'; 

/* Load and clear sessions */
session_start();
session_destroy();

// Destroy cookie
setcookie(COOKIE_NAME, '', time()-60*60*24*365, '/', COOKIE_DOMAIN);
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();

?>