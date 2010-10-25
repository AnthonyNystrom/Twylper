<?php 
	if (!isset($hide_sidebar)) {
?>
		</div>
		<div style="float: left; width: 300px; position: relative; left: 55px">
			
			<h4>Top <?php echo SITE_NAME; ?> Users</h4><br>
			
			<?php
			
			// Show top users according to total comments
			$query = "SELECT * FROM users ORDER BY num_topics DESC LIMIT " . NUM_TOP_USERS;
			$result = mysql_query($query) OR error($query);
			
			if (mysql_num_rows($result) > 0) {
				while($row = mysql_fetch_array($result)) {
				
					if ($row['num_topics'] == 1)
						$s = '';
					else
						$s = 's';
						
					// Avatar
					if ($row['profile_image_url'] != '') {
						$avatar = $row['profile_image_url'];
					} else {
						$avatar = AVATAR_DEFAULT;
					}
				
					echo '
					<div id="top_user_row_mini"><div id="top_user_img_mini"><img src="' . $avatar . '"></div><div id="top_user_name_mini"><a href="/profile/' . $row['screen_name'] . '" title="Twitter Replies By ' . $row['screen_name'] . '">' . $row['screen_name'] . '</a></div><div id="top_user_stats_mini">' . $row['num_topics'] . ' topic' . $s . '</div></div>
					';
				}
			}
			
			?>
			
			<br clear=both>
			
		</div><br clear="both">
<?php 
	}
?>
	</div><br><br>

	<div id="footerwrap">
		<div id="footer">
			<div id="footer_copyright">&copy;2010 <a href="http://7touchgroup.com/">7touch group, inc.</a></div>
			<div id="footer_nav"><a href="/about">About</a> &nbsp;|&nbsp; <a href="http://7touchgroup.com/contact">Contact</a> &nbsp;|&nbsp; <a href="http://7touchgroup.com/privacy">Privacy Policy</a>
		</div>
	</div>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-282101-13']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>

</html>

<?php ob_flush(); ?>