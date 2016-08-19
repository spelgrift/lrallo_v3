var $ = require('jquery');

$(function() {
	// Cache DOM
	var $galSettings 	= $('#galSettings'),
	// Common attributes
	$nameInput 			= $galSettings.find('#settingsNameInput'),
	$urlInput 			= $galSettings.find('#settingsUrlInput'),
	$parentSelect 		= $galSettings.find('#settingsParentInput'),
	$navCheck 			= $galSettings.find('#settingsNavCheck'),
	// Privacy related items

	// Gallery-specific attributes
	$autoplayCheck		= $galSettings.find('#settingsAutoplayCheck'),
	$durationInput		= $galSettings.find('#settingsDurationInput'),
	$animationSelect	= $galSettings.find('#settingsAnimationSelect'),
	$displayRadio		= $galSettings.find('#settingsDisplayRadio'),

	$settingsSubmit 	= $galSettings.find('#settingsSubmit'),
	$trashPage 			= $galSettings.find('#settingsTrashPage'),
	$settingsMsg 		= $galSettings.find('#settingsMsg'),
	$mainNav 			= $('#mainNav').children('ul.navbar-nav'),
	$adminNavName 		= $('#adminNav').find('#adminNavName'),
	pageURL 				= $('a#viewTab').attr('href'),
	origName 			= $nameInput.val(),
	origURL 				= $urlInput.val();

	// Bind Events

	$settingsSubmit.on('click', function(ev) {
		ev.preventDefault();
		saveSettings();
	});

	$trashPage.on('click', function(ev) {
		ev. preventDefault();
		if(confirm('Are you sure you want to send this page to the trash?')) {
			trashPage();
		}	
	});
	
	// Update URL field on name update
	$nameInput.keyup(function() {
		var str = $(this).val();
		$urlInput.val(makeURL(str));
	});


	// Main Functions

	function trashPage() {
		$.ajax({
			type: 'DELETE',
			url: pageURL + '/trashContent/',
			dataType: 'json',
			success: function(data) {
				if(!data.error) {
					window.location.href = baseURL + "dashboard/";
				} else {
					$settingsMsg.html("<p class='text-danger'>"+data.error_msg+"</p>");
					clearMsg($settingsMsg, 6000);
				}
			}
		});
	}

	function saveSettings() {
		// Get user input
		var data = {
			name : $nameInput.val(),
			url : $urlInput.val(),
			parent : $parentSelect.val(),
			origName : origName,
			origURL : origURL,
			duration : $durationInput.val(),
			animation : $animationSelect.val(),
			display : $displayRadio.find("input[name='settingsDisplayRadio']:checked").val()
		};
		if($navCheck.prop('checked')) {
			data.nav = "1";
		} else {
			data.nav = "0";
		}

		if($autoplayCheck.prop('checked')) {
			data.autoplay = "1";
		} else {
			data.autoplay = "0";
		}

		// Validate
		if(data.name.length < 1) {
			settingsError($nameInput, "<p class='text-danger'>Name cannot be blank!</p>");
			return false;
		}
		if(data.url.length < 1) {
			settingsError($nameInput, "<p class='text-danger'>URL cannot be blank!</p>");
			return false;
		}
		if(!validateURL(data.url)) {
			settingsError($nameInput, "<p class='text-danger'>URL can only contain letters, numbers, dashes (-) and underscores (_).</p>");
			return false;
		}
		if(data.duration.length < 1 || !isInt(data.duration)) {
			settingsError($durationInput, "<p class='text-danger'>You must enter a number.");
			return false;
		}

		// Post to server
		$.ajax({
			type: 'POST',
			url: pageURL + '/updateSettings',
			data: data,
			dataType: 'json',
			success: function( data ) {
				if(!data.error) { // Success
					saveSuccess(data);
				} else { // Error
					saveError(data);
				}
			}
		});
	}

	function saveSuccess(data) {
		$settingsMsg.html("<p class='text-success'>Changes saved!</p>");
		clearMsg($settingsMsg, 6000);
		// Update page elements
		reloadNav();
		$adminNavName.html("<strong>Edit: </strong>"+data.name);
		$('a#viewTab').attr('href', data.viewPath);
		document.title = "Edit Page: "+data.name;
		window.history.replaceState({}, data.name, data.windowPath);
		// Update JS vars
		origName = data.name;
		origURL = data.url;
		pageURL = data.viewPath;
	}

	function saveError(data) {
		$settingsMsg.html("<p class='text-danger'>"+data.error_msg+"</p>");
		if(data.error_field == 'name') {
			$nameInput.focus();
		} else if(data.error_field == 'url') {
			$urlInput.focus();
		}
		clearMsg($settingsMsg, 6000);
	}

/*
 *
 * UTILITY FUNCTIONS
 *
 */
 	function settingsError($toFocus, msg) {
 		$settingsMsg.html(msg);
		$toFocus.focus();
		clearMsg($settingsMsg);
 	}

	function clearMsg(selector, timeout) {
		if (timeout === undefined) {
			timeout = 4000;
		}
		setTimeout(function(){
			selector.fadeOut('slow', function() {
				selector.html('');
				selector.show();
			});
		}, timeout);
	}

	function validateURL(url) {
		if(/^[a-zA-Z0-9-_]*$/.test(url)) {
			return true;
		} else {
			return false;
		}
	}

	function isInt(n) {
		n = parseInt(n);
		if(isNaN(n)) { return false; }
		return Number(n) === n && n % 1 === 0;
	}

	function makeURL(str) {
		var url = str.replace(/[^a-z.0-9_]+/ig, "_");
		url = url.toLowerCase();
		return url;
	}

	function reloadNav() {
		$mainNav.load(baseURL + 'dashboard/reloadNav');
	}
});