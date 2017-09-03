var $ = require('jquery');
var _ = require('./utilityFunctions.js'); // helper functions
require('../libs/jquery-ui.sortable');

$(function() {
	var $contentArea 	= $('#contentArea');
	var pageURL 		= _.getURL();

/*
 *
 * BIND EVENTS
 *
 */

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

 	function updateSortable() {
 		var order = $(this).sortable('serialize');
 		$.ajax({
 			url: pageURL + '/sortContent/',
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
			url: pageURL + '/trashContent/' + contentID,
			dataType: 'json',
			success: function(data) {
				if(!data.error) {
					$thisItem.fadeOut(300, function() {
						$(this).remove();
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
			url: pageURL + '/deleteSpacer/' + contentID,
			success: function() {
				$thisItem.fadeOut(300, function() {
					$(this).remove();
				});
			}
		});
	}

});