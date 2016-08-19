var $ = require('jquery');
$(function() {
	// Cache DOM
	var $loginLink = $('a#login'),
	$loginModal = $('#loginModal'),
	$submitLogin = $loginModal.find('#loginSubmit'),
	$userNameInput = $loginModal.find('input#userName'),
	$passwordInput = $loginModal.find('input#pwd'),
	$loginMsg = $loginModal.find('.loginMsg');

	// Bind Events
	$loginLink.click(function(ev) {
		$loginModal.modal('show');
		ev.preventDefault();
	});

	$submitLogin.click(submitLogin);

	// Core Functions
	function submitLogin(ev) {
		ev.preventDefault();
		// Get user input
		var userName = $userNameInput.val(),
		password = $passwordInput.val();

		// Validate
		if(userName.length < 1) {
			$loginMsg.html("<p class='text-danger'>Please enter a username.</p>");
			$userNameInput.focus();
			clearMsg();
			return false;
		}
		if(password.length <1) {
			$loginMsg.html("<p class='text-danger'>Please enter a password.</p>");
			$passwordInput.focus();
			clearMsg();
			return false;
		}
		// Post to server
		$.ajax({
			type: 'POST',
			url: baseURL + 'login/run',
			data: {
				login : userName,
				password : password
			},
			dataType: 'json',
			success: function( data ) {
				if(data == 'error') {
					$loginMsg.html("<p class='text-danger'>Invalid username or password.</p>");
					$userNameInput.focus();
					clearMsg();
					return false;
				}
				if(data == 'success') {
					if(window.location.href == baseURL) {
						window.location.href = baseURL + 'dashboard';
						return;
					}
					location.reload();
				}
			}
		});
	}

	function clearMsg() {
		setTimeout(function(){
			$loginMsg.fadeOut('slow', function() {
				$loginMsg.html('');
				$loginMsg.show();
			});
		}, 4000);
	}
});	