var $ = require('jquery');

$(function() {
/**
 * 
 * CACHE DOM
 * 
 */
	var $settings 		= $('#settings'),
	type 					= $settings.attr('data-type'),
	$nameInput 			= $settings.find('#settingsNameInput'),
	$urlInput 			= $settings.find('#settingsUrlInput'),
	$parentSelect 		= $settings.find('#settingsParentInput'),
	$navCheck 			= $settings.find('#settingsNavCheck'),
	$hiddenCheck 		= $settings.find('#settingsHiddenCheck'),
	$settingsSubmit 	= $settings.find('#settingsSubmit'),
	$trashPage 			= $settings.find('#settingsTrashPage'),
	$settingsMsg 		= $settings.find('#settingsMsg'),
	$mainNav 			= $('#mainNav').children('ul.navbar-nav'),
	$adminNavName 		= $('#adminNav').find('#adminNavName'),
	pageURL 				= $('a#viewTab').attr('href'),
	origName 			= $nameInput.val(),
	origURL 				= $urlInput.val();

	// Type specific attributes
	switch(type) {
		case 'gallery':
			var $autoplayCheck	= $settings.find('#settingsAutoplayCheck'),
			$durationInput			= $settings.find('#settingsDurationInput'),
			$animationSelect		= $settings.find('#settingsAnimationSelect'),
			$displayRadio			= $settings.find('#settingsDisplayRadio');
		break;
		case 'video':
			var $linkInput = $settings.find('#settingsLinkInput'),
			$descInput = $settings.find('#settingsDescInput');
		break;
	}

/**
 * 
 * BIND EVENTS
 * 
 */

	$settingsSubmit.click(saveSettings);

	$trashPage.click(trashPage);

	// Update URL field on name update
	$nameInput.keyup(autoFillURL);

/**
 * 
 * MAIN FUNCIONS
 * 
 */
	function saveSettings(ev) {
		ev.preventDefault();
		// Get user input
		var data = {
			name 		: $nameInput.val(),
			url 		: $urlInput.val(),
			parent 	: $parentSelect.val(),
			origName : origName,
			origURL 	: origURL
		};
		if($navCheck.prop('checked')) {
			data.nav = "1";
		} else {
			data.nav = "0";
		}
		if($hiddenCheck.prop('checked')) {
			data.hidden = "1";
		} else {
			data.hidden = "0";
		}
		// Add type specific input
		switch(type) {
			case 'gallery':
				data.duration 	= $durationInput.val();
				data.animation = $animationSelect.val();
				data.display 	= $displayRadio.find("input[name='settingsDisplayRadio']:checked").val();
				if($autoplayCheck.prop('checked')) {
					data.autoplay = "1";
				} else {
					data.autoplay = "0";
				}
			break;
			case 'video':
				data.link 			= $linkInput.val();
				data.description 	= $descInput.val();
			break;
		}
		// Validate
		if(!validateInput(data)) {
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

	function trashPage(ev) {
		ev. preventDefault();
		if(!confirm('Are you sure you want to send this page to the trash?')) {
			return false;
		}	
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

	function autoFillURL() {
		var str = $(this).val();
		$urlInput.val(makeURL(str));
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

 	function validateInput(data) {
 		// Validate
		if(data.name.length < 1) {
			settingsError($nameInput, "<p class='text-danger'>Name cannot be blank!</p>");
			return false;
		}
		if(data.url.length < 1) {
			settingsError($urlInput, "<p class='text-danger'>URL cannot be blank!</p>");
			return false;
		}
		if(!validateURL(data.url)) {
			settingsError($urlInput, "<p class='text-danger'>URL can only contain letters, numbers, dashes (-) and underscores (_).</p>");
			return false;
		}

		switch(type) {
			case 'gallery' :
				if(data.duration.length < 1 || !isInt(data.duration)) {
					settingsError($durationInput, "<p class='text-danger'>You must enter a number.");
					return false;
				}
			break;
			case 'video' :
				if(data.link.length < 1) {
					settingsError($linkInput, "<p class='text-danger'>Link cannot be blank!</p>");
					return false;
				}
			break;
		}
		return true;
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

	function makeURL(str) {
		var url = str.replace(/[^a-z.0-9_]+/ig, "-");
		url = url.toLowerCase();
		return url;
	}

	function isInt(n) {
		n = parseInt(n);
		if(isNaN(n)) { return false; }
		return Number(n) === n && n % 1 === 0;
	}

	function reloadNav() {
		$mainNav.load(baseURL + 'dashboard/reloadNav');
	}
});