$(document).ready(function() {

	// Disable caching
	$.ajaxSetup({
		cache: false
	});
	
	$('textarea.expanding').autogrow();
	
	//var remaining = 116 - $("textarea[id=tweet]").val().length;
	//$('#characters').text(remaining + ' characters remaining');
	
	// Show them a countdown and disallow over 116 characters
	$('#tweet').keypress(function() {
	
		// If we are at or over the limit
		if (this.value.length >= 92) {
			// Trim whatever else they added
			//this.value = this.value.substring(0, 116);
		}
		
		// Remaining characters
		var remaining = 92 - this.value.length;
		
		// New value
		if (remaining > 92) {
			$('#characters').text(remaining + ' characters remaining');
			$('#characters').css('color','red');
		} else if (remaining <= 92) {
			$('#characters').text(remaining + ' characters remaining');
			$('#characters').css('color','#363636');
		}
	});

	// click comment reply
	$("textarea[id=comment_reply_text]").blur(function() {
		if ($(this).attr('name') != 'undefined' && $(this).val() == '') {
			$(this).css('background-image','url(/images/comment_reply_bg.png)');
		}
	});	
	$("textarea[id=comment_reply_text]").focus(function() {
		$(this).css('background-image','none');
	});
	
	// post tweet
	$("textarea[id=tweet]").blur(function() {
		if ($(this).attr('name') != 'undefined' && $(this).val() == '') {
			$(this).css('background-image','url(/images/post_bg.png)');
		}
	});	
	$("textarea[id=tweet]").focus(function() {
		$(this).css('background-image','none');
	});
	
	// click login name text 
	$("input[name=user]").blur(function() {
		if ($(this).attr('name') != 'undefined' && ($(this).val() == '' || $(this).val() == 'twitter username')) {
			$(this).val('twitter username');
			$(this).css('color','#b2b2b2');
		}
	});
	$("input[name=user]").focus(function() {
		$(this).css('color','#000');
		if ($(this).val() == 'twitter username') {
			$(this).val('');
		}
	});
	
	// click login password text 
	$("input[name=pass]").blur(function() {
		if ($(this).attr('name') != 'undefined' && ($(this).val() == '' || $(this).val() == 'password')) {
			$(this).val('');
			$(this).css('color','#b2b2b2');
		}
	});
	
	$("input[name=pass]").focus(function() {
		$(this).css('color','#000');
		if ($(this).val() == 'password') {
			$(this).val('');
		}
	});
	
	// login link
	$("a[id=log_in]").click(function() {
		requireLogin();
	});
	
	// require login
	function requireLogin() {
		$('#basicModalContent').modal(); // jQuery object; this demo
		$.modal(document.getElementById('basicModalContent')); // DOM
	}

	// New topic
	$('#topic_submit').click(function() {
	
		// Show the loading AJAX bar
		$('#responsemsg').empty().append('<center><img src="/images/ajax-loader.gif"><br>Posting to Twitter...<br><br></center>');

		var error	= 0;

		// Vars we need
	   	var topic = $('textarea[name=tweet]').val();
	   	var author = $('input[name=author]').val();
	   	var user = $('input[name=user]').val();
	   	var pass = $('input[name=pass]').val();

	   	// Comment is too short
	   	if (topic.length < 3) {
	   		$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">Your topic is too short.</div></div>');
			error	= 1;
	   	}
	   	
	   	if (user == 'twitter username') {
	   		$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">You must enter in a username.</div></div>');
			error	= 1;
	   	}
	   	
	   	if (user.length < 3) {
	   		$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">You must enter in a username.</div></div>');
			error	= 1;
	   	}
	   	
	   	if (pass.length < 3) {
	   		$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">You must enter in a password.</div></div>');
			error	= 1;
	   	}

	   	if (pass == 'password') {
	   		$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">You must enter in a password.</div></div>');
			error	= 1;
	   	}
	   	
	   	if (error == 1) {
	   		$('#responsemsg').fadeIn('slow');
	   	}

	   	if (error == 0) {
			$.ajax({
	   			data: 'user=' + user + '&pass=' + pass + '&author=' + author + '&tweet=' + topic,
	   			type: 'POST',
				cache: false,
				url: '/actions/comments_wave.php',
				success: function(response) {

					// Figure out if this is an error or success
					var test = new Array();
					test = response.split('||');

					if (test[0] != 'error') {
			   			// Get the newly created string so we can link them to their topic
						var new_response = test[1];

			   			// Remove post topic box
			   			$('form[name=post_topic]').fadeOut('slow');
			   			$('form[name=post_topic]').remove();

						// Let them know it's a success
						$('#responsemsg').empty();
						$('#responsemsg').append('<div id="success_outer"><div id="success_inner">' + new_response + '</div></div>');
						
					} else {
						$('#responsemsg').empty();
						$('#responsemsg').append('<div id="error_outer"><div id="error_inner">Sorry, there has been a problem. ' + test[1] + '</div></div>');
		   			}
		   			return false;
		   		}
			});
		} else {
			// Show error messages
			$('#responsemsg').fadeIn('slow');
		}
		return false;
	});

	// New comment
	$('#comment_submit').click(function() {
	
		// Show the loading AJAX bar
		$('#responsemsg').empty().append('<center><img src="/images/ajax-loader.gif"><br>Posting to Twitter...<br><br></center>');

		var error	= 0;

		// Vars we need
	   	var comment	= $('textarea[name=tweet]').val();
	   	var comment_id = $('input[name=comment_id]').val();
	   	var author = $('input[name=author]').val();
	   	var user = $('input[name=user]').val();
	   	var pass = $('input[name=pass]').val();
		var string = $('input[name=string]').val();
		var reply = '';
		reply = $('input[name=reply]').val();

		// Account for line breaks
		//comment		= URLEncode(comment.replace(/[\r\n]+/g, '-linebreak-'));

	   	// Blank comment
	   	if (comment == '') {
	   		$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">You must enter in a comment.</div></div>');
	       	error	= 1;
	   	}

	   	// Comment is too short
	   	if (comment.length < 3) {
	   		$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">Your comment is too short.</div></div>');
			error	= 1;
	   	}
	   	
	   	if (user == 'twitter username') {
	   		$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">You must enter in a username.</div></div>');
			error	= 1;
	   	}
	   	
	   	if (user.length < 3) {
	   		$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">You must enter in a username.</div></div>');
			error	= 1;
	   	}
	   	
	   	if (pass.length < 3) {
	   		$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">You must enter in a password.</div></div>');
			error	= 1;
	   	}

	   	if (pass == 'password') {
	   		$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">You must enter in a password.</div></div>');
			error	= 1;
	   	}
	    
	   	if (error == 0) {
			$.ajax({
	   			data: 'reply=' + reply + '&user=' + user + '&pass=' + pass + '&author=' + author + '&string=' + string + '&comment_id=' + comment_id + '&tweet=' + comment,
	   			type: 'POST',
				cache: false,
				url: '/actions/comments.php',
				success: function(response) {

					// Figure out if this is an error or success
					var test = new Array();
					test = response.split('||');

					if (test[0] != 'error') {
					
			   			//var comment_id = $('input[name=comment_id]').val();
						var reply_comment_id = test[2];

			   			// Remove post comment box
			   			$('form[name=post_comment]').fadeOut('slow');
			   			$('form[name=post_comment]').remove();
		
						// Show this comment
						var com_id	= response;
						$('#new_comment').load('/actions/refresh_comments.php?id=' + reply_comment_id + '', '', function() {
							$('#new_comment').fadeIn('slow');
						});
						
						// Let them know it's a success
						$('#responsemsg').empty();
						$('#responsemsg').append('<div id="success_outer"><div id="success_inner">Your comment has been posted.</div></div>');
						
					} else {
						$('#responsemsg').empty();
						$('#responsemsg').append('<div id="error_outer"><div id="error_inner">Sorry, there has been a problem. ' + test[1] + '</div></div>');
		   			}
		   			return false;
		   		}
			});
		} else {
			// Show error messages
			$('#responsemsg').fadeIn('slow');
		}
		return false;
	});
	
	// Save e-mail
	$('#save_email').click(function() {
		var email = $('input[name=email]').val();
		var error = 0;
	
		if (email.length < 4) {
			$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">You must enter in a valid e-mail.</div></div>');
			error = 1;
		}
		
		if (email == '') {
			$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">Your e-mail cannot be blank.</div></div>');
			error = 1;
		}
		
		if (email == 'you@user.com') {
			$('#responsemsg').empty().append('<div id="error_outer"><div id="error_inner">You must enter in a valid e-mail.</div></div>');
			error = 1;
		}
		
	   	if (error == 0) {
			$.ajax({
	   			data: 'email=' + email,
	   			type: 'POST',
				cache: false,
				url: '/actions/account.php',
				success: function(response) {

					// Figure out if this is an error or success
					var test = new Array();
					test = response.split('||');

					if (test[0] != 'error') {

			   			// Remove email form
			   			$('#notify_text').fadeOut('slow').remove();
			   			$('form[name=save_email]').fadeOut('slow');
			   			$('form[name=save_email]').remove();

						// Let them know it's a success
						$('#responsemsg').empty().append('<div id="success_outer"><div id="success_inner">Your e-mail has been saved.</div></div>');

						// Show notifications
						$('#notify_options').fadeIn('slow');
						
						// Fade out success
						$('#responsemsg').fadeOut(7500, function() {
							$('#responsemsg').empty();
						});
						
					} else {
						$('#responsemsg').empty();
						$('#responsemsg').append('<div id="error_outer"><div id="error_inner">' + test[1] + '</div></div>');
		   			}
		   			return false;
		   		}
			});
		} else {
			// Show error messages
			$('#responsemsg').fadeIn('slow');
		}
		return false;
	
	});
	
});