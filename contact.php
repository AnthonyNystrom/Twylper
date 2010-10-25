<?php

$page_title = 'Contact Us';

include 'includes/config.php';
include 'includes/functions.php';
include 'includes/header.php';

$success	= false;

echo '<h3>Contact Us</h3><br>';


if ($_POST['submit'] == true) {
	$required	= array('name' => $_POST['name'], 'email' => $_POST['email'], 'message' => $_POST['message']);
	$error_msg	= '';
	
	foreach ($required AS $key => $value) {
		if ($value == '')
			$error_msg	.= '<li> &nbsp;You must enter in your ' . $key;
	}
	
	if ($error_msg == '') {
		$message = "Hello TwitReply Founders -\n\nContact Information:\n\nName: " . $_POST['name'] . "\nE-mail: " . $_POST['email'] . "\n\nMessage: " . $_POST['message'] . "\n\nRegards,\nTwitReply Contact Form\nhttp://www.twitrep.ly\n";
		$subject = 'TwitReply - ' . $_POST['level'];
		$headers = "From: " . $_POST['email'] . "\nReply-To: " . $_POST['email'] . "\nX-Mailer: PHP/" . phpversion();

		if (mail('mail@twitrep.ly', $subject, $message, $headers)) {
			echo '<b>Success!</b> Your message has been sent. Thanks for your interest in TwitReply.<br><br>';
			$success	= true;
		} else {
			echo '<div style="color: red;">The e-mail could not be sent! Please try again, and again, and again, and again...</div>';
		}
	} else {
		echo '<div style="color: red">We found the following errors. Please correct them.<ul>' . $error_msg . '</ul></div>';
	}
}

if ($success == false) { ?>

<form method="post" action="/contact">
<div id="form_field">Your name:</div><div id="form_data"><input type="text" name="name" value="<?php echo $_POST['name']; ?>" size="45"></div>
<div id="form_field">Your e-mail:</div><div id="form_data"><input type="text" name="email" value="<?php echo $_POST['email']; ?>" size="45"></div>
<div id="form_field">Message:</div><div id="form_data"><textarea name="message" rows="3" cols="50" style="min-height: 80px" class="expanding"><?php echo $_POST['message']; ?></textarea></div>
<div id="form_field">&nbsp;</div><div id="form_data"><input type="submit" name="submit" value="Contact Us"></div>
</form>

<?php }

include 'includes/footer.php';

?>