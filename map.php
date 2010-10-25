<?php 

$hide_sidebar = 1;
$map = 1;
include 'includes/config.php';
include 'includes/functions.php';
$page_title = SITE_NAME . ' - ' . 'Tweet. Discuss. Simple.';
include 'includes/header.php';

?>
<div id="map"></div>


		</div>
		<div style="float: left; width: 300px; position: relative; left: 55px;">
			
			<div id="places_search" style="padding-top: 10px; padding-bottom: 10px;">Displaying topics near <span id="search_location"></span> 
				<a href="#" id="search_location_change">(Change)</a>
				<div id="places_search_form" style="padding-top: 5px; display:none;">
					<form id="place_search_form">
						<table class="geo_place_search_table"><tbody>
							<tr>
								<td class="geo_place_search_col1 geo_place_search_place">City</td>
								<td class="geo_place_search_place"><input type="text" title="Enter a city, state" class="round-left help-focusable" autocomplete="off" id="place_search_query"><span title="Search" class="place_search_submit round-right">&nbsp;</span></td>
							</tr>
							<tr>
								<td></td>
								<td><div id="place_search_results" style="background-color: #FFF; border: 1px solid #AAAAAA !important; margin-top: -1px; position: absolute; z-index: 5; width: 230px; padding-bottom: 5px;"></div></td>
							</tr>
						</tbody></table></form></div>
			</div>
			<div id="result_container"></div>
			<br clear=both>
			
		</div><br clear="both">

<?php
include 'includes/footer.php';
?>