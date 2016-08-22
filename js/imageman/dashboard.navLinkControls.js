var $ = require('jquery');

$(function() {

/**
 * 
 * CACHE DOM
 * 
 */
	var $mainNav = $('#mainNav').children('ul.navbar-nav'),
	$navLinks,
	$editModal = $('#editNavLinkModal'),
	$editNameInput = $editModal.find('#editNavName'),
	$editUrlInput = $editModal.find('#editNavUrl'),
	$editSubmit = $editModal.find('#submitEditNavLink'),
	$deleteLink = $editModal.find('#deleteNavLink'),
	$editNavLinkMsg = $editModal.find('#editNavLinkMsg'),
	controlHTML = "<a class='navLinkControl' href='#'><i class='fa fa-cog'></i></a>";

	initNavLinkControls();

/**
 * 
 * BIND EVENTS
 * 
 */

	events.on('reloadNav', function() {
		initNavLinkControls();
	});

/**
 * 
 * MAIN FUNCTIONS
 * 
 */

	function initNavLinkControls()
	{
		$navLinks = $mainNav.find('li.navLink');

		// Append controls to Nav Links
		$navLinks.each(function() {
			$(this).css('margin-right', '10px').prepend(controlHTML);
		});

		// Bind control click event
		$mainNav.on('click', '.navLinkControl', editNavLink);
	}

	function editNavLink(ev)
	{
		ev.preventDefault();
		var $thisItem = $(this).closest('li'),
		contentID = $thisItem.attr('data-id'),
		origName = $thisItem.find('a.navLink').html(),
		origURL = $thisItem.find('a.navLink').attr('href');

		$editNameInput.val(origName);
		$editUrlInput.val(origURL);
		$editModal.modal('show');

		// Save Button
		$editSubmit.on('click', function() {
			saveNavLink(contentID);
		});

		// Delete Button
		$deleteLink.on('click', function() {
			if(confirm('Are you sure you want to trash this link?')) {
				deleteLink($thisItem, contentID);
			}
		});
	}

	function saveNavLink(contentID)
	{
		// Get user input
		var navLinkName = $editNameInput.val(),
		navLinkUrl = $editUrlInput.val();
		// Validate
		if(navLinkName.length < 1) {
			$editNavLinkMsg.html("<p class='text-danger'>You must enter a name.</p>");
			$editNameInput.focus();
			clearMsg($editNavLinkMsg);
			return false;
		}
		if(navLinkUrl.length < 1) {
			$editNavLinkMsg.html("<p class='text-danger'>You must enter a url</p>");
			$editUrlInput.focus();
			clearMsg($editNavLinkMsg);
			return false;
		}

		// Post to server
		$.ajax({
			type: 'POST',
			url: baseURL + 'dashboard/editNavLink/' + contentID,
			data: { 
				name : navLinkName,
				url : navLinkUrl
			},
			dataType: 'json',
			success: function( data ) {
				if(!data.error) { // Success
					reloadNav();
					$editModal.modal('hide');
				} else { // Error
					$editNavLinkMsg.html("<p class='text-danger'>"+data.error_msg+"</p>");
					switch(data.error_field) {
						case 'name' :
							$editNameInput.focus();
						break;
						case 'url' :
							$editUrlInput.focus();
						break;
					}
					clearMsg($editNavLinkMsg);
				}
			}
		});
	}

	function deleteLink($thisItem, contentID)
	{
		$.ajax({
 			type: 'DELETE',
 			url: baseURL + 'dashboard/trashNavLink/' + contentID,
 			dataType: 'json',
 			success: function(data) {
 				if(!data.error) {
 					$editModal.modal('hide');
			 		$thisItem.fadeOut(300, function() {
			 			$(this).remove();
			 			reloadNav();
			 		});
			 		events.emit('reloadTrash');
 				}
 			}
 		});
	}

/**
 * 
 * UTLITY FUNCTIONS
 * 
 */

	function reloadNav() {
		$mainNav.load(baseURL + 'dashboard/reloadNav', function() {
			events.emit('reloadNav');
		});
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
});