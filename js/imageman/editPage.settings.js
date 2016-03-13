var $ = require('jquery');

$(function() {
	// Cache DOM
	var $pageSettings = $('#pageSettings'),
	$nameInput = $pageSettings.find('#settingsNameInput'),
	$urlInput = $pageSettings.find('#settingsUrlInput'),
	$parentSelect = $pageSettings.find('#settingsParentInput'),
	$navCheck = $pageSettings.find('#settingsNavCheck'),
	$settingsSubmit = $pageSettings.find('#settingsSubmit'),
	$trashPage = $pageSettings.find('#settingsTrashPage'),
	$settingsMsg = $pageSettings.find('#settingsMsg'),
	$mainNav = $('#mainNav').children('ul.navbar-nav'),
	$adminNavName = $('#adminNav').find('#adminNavName'),
	pageURL = $('a#viewTab').attr('href'),
	origName = $nameInput.val(),
	origURL = $urlInput.val();

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
			origURL : origURL
		};
		if($navCheck.prop('checked')) {
			data.nav = "1";
		} else {
			data.nav = "0";
		}

		// Validate
		if(data.name.length < 1) {
			$settingsMsg.html("<p class='text-danger'>Name cannot be blank!</p>");
			$nameInput.focus();
			clearMsg($settingsMsg);
			return false;
		}
		if(data.url.length < 1) {
			$settingsMsg.html("<p class='text-danger'>URL cannot be blank!</p>");
			$urlInput.focus();
			clearMsg($settingsMsg);
			return false;
		}
		if(!validateURL(data.url)) {
			$settingsMsg.html("<p class='text-danger'>URL can only contain letters, numbers, dashes (-) and underscores (_).</p>");
			$urlInput.focus();
			clearMsg($settingsMsg, 6000);
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
		var url = str.replace(/[^a-z.0-9_]+/ig, "_");
		url = url.toLowerCase();
		return url;
	}

	function reloadNav() {
		$mainNav.load(baseURL + 'dashboard/reloadNav');
	}
});