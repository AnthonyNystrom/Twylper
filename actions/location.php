<?php 

/* Load required lib files. */
session_start();
require_once('../twitteroauth/twitteroauth.php');
require_once('../includes/config.php');

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

$ip = $_SERVER['REMOTE_ADDR'];
$places = $connection->post('geo/search', array('ip' => $ip));

?>
<script>
$(document).ready(function() {		
	$('#place_link').click(
		function() {
        	$('.places_list').toggle();
		}
	);
	
	$('ul.places_list > li').click(function() {
		var place = $(this).attr("id");
		var place_name = $(this).children('.place_noicon').html();
		$('#place_name').html(place_name);
		$('ul.places_list').children().removeClass('selected');
		$('#' + place).addClass('selected');
		
		$('#place').val(place);
		$('.places_list').toggle();
	});

	$('#place').val('<?php echo $places->result->places[0]->id; ?>');
});
</script>

<span id="place_content">
near <a href="#" id="place_link"><span id="place_name"><?php echo $places->result->places[0]->full_name; ?></span> ▾</a>&nbsp;<span class="crosshairs">&nbsp;</span>
<ul class="round places_list">
<?php
	foreach($places->result->places as $key => $place) {
		if ($key == 0) {
			$selected = 'class="selected"';
		} else {
			$selected = "";
		}
?>
	<li id="<?php echo $place->id; ?>" <?php echo $selected; ?>>
		<span class="place_item_icon">&nbsp;</span><span class="place_noicon"><?php echo $place->full_name; ?></span>
	</li>
<?php
	}
?>
</ul>
</span>