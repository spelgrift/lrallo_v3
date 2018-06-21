var $ = require('jquery');
var _ = require('./utilityFunctions.js'); // helper functions
require('../libs/jquery-ui.sortable');
require('../libs/jquery.ui.touch-punch.min.js');

$(function() {

// Page or Post?
var isPost = false,
postID = "";
if((window.location.href).includes(baseURL+blogURL+"/")) {
	isPost = true;
	postID = $('#adminNav').attr('data-id');
}

/*
 *
 * CACHE DOM
 *
 */
	var $contentArea 			= $('#contentArea'),
	$contentSettingsModal 	= $('#contentSettingsModal'),
	$parentSelect				= $contentSettingsModal.find('#contentParentSelect'),
	$settingsSave				= $contentSettingsModal.find('#saveContentSettings'),
	$contentParentMsg			= $contentSettingsModal.find('#contentParentMsg'),
	pageID						= $('#adminNav').attr('data-id'),
	pageURL 						= _.getURL(isPost);

/*
 *
 * BIND EVENTS
 *
 */
 	// Content Settings
 	$contentArea.on('click', 'a.editContentSettings', loadSettingsModal);
 	$settingsSave.click(submitSettings);

	// Trash Content
	$contentArea.on('click', 'a.trashContent', trashContent);

	// Delete Spacer
	$contentArea.on('click', 'a.deleteSpacer', deleteSpacer);

	// Content Sortable
	$contentArea.sortable({
		handle : '.handle',
		update : updateSortable
	});

/*
 *
 * CORE FUNCTIONS
 *
 */
 	function submitSettings(ev) {
 		ev.preventDefault();
 		var data = { parent : $parentSelect.val() },
 		contentID = $(this).attr('data-id');
 		if(data.parent == pageID) {
 			$contentSettingsModal.modal('hide');
 			return;
 		}
 		var url = pageURL + '/updateContentSettings/' + contentID;
 		_.post(url, data, saveSuccess, saveError);
 	}

 	function saveSuccess(data) {
 		var $thisBlock = $contentArea.find('#'+data.contentID).closest('.contentItem');
 		if(data.newParent != pageID) {
 			$thisBlock.remove();
 		}
 		$contentSettingsModal.modal('hide');
 	}

 	function saveError(data) {
 		_.error(data.error_msg, $contentParentMsg);
 	}

 	function loadSettingsModal(ev) {
 		ev.preventDefault();
 		var contentID	 	= $(this).closest('.contentControlMenu').attr('id');
 		$settingsSave.attr('data-id', contentID);
 		$contentSettingsModal.modal('show');
 	}
 	function updateSortable() {
 		var order = $(this).sortable('serialize');
 		$.ajax({
 			url: baseURL + 'page/sortContent/',
 			type: 'POST',
 			data: order,
 			success: function(){}
 		});
 	}

	function trashContent(ev) {
		ev.preventDefault();
		var contentID = $(this).closest('.contentControlMenu').attr('id'),
		$thisItem = $(this).closest('.contentItem');

		if(!confirm('Are you sure you want to trash this item?')){
			return false;
		}

		$.ajax({
			type: 'DELETE',
			url: baseURL+'page/trashContent/' + contentID,
			dataType: 'json',
			success: function(data) {
				if(!data.error) {
					$thisItem.fadeOut(300, function() {
						$(this).remove();
						events.emit('contentRemoved');
					});
				}
			}
		});
	}

	function deleteSpacer(ev) {
		ev.preventDefault();
		var contentID = $(this).closest('.spacerControls').attr('id'),
		$thisItem = $(this).closest('.contentItem');

		if(!confirm('Are you sure you want to delete this spacer?')){
			return false;
		}

		$.ajax({
			type: 'DELETE',
			url: baseURL + 'page/deleteSpacer/' + contentID,
			success: function() {
				$thisItem.fadeOut(300, function() {
					$(this).remove();
					events.emit('contentRemoved');
				});
				
			}
		});
	}

});