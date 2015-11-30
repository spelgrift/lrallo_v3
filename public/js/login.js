var login = (function() {
	var $loginLink = $('a#login');
	var $loginModal = $('#loginModal');
	var $submitLogin = $('#loginSubmit');
	var $userNameInput = $('input#userName');
	var $passwordInput = $('input#pwd');
	var $loginMsg = $('.loginMsg');

	// Bind Events
	$loginLink.on('click', function(ev) {
		$loginModal.modal('show');
		ev.preventDefault();
	});

	$submitLogin.on('click', function(ev) {
		submitLogin();
		ev.preventDefault();
	});

	function submitLogin() {
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
					window.location.href = baseURL + 'dashboard';
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


})();
