var $ = require('jquery');
require('../libs/jquery-ui.sortable');
$(function() {
	var $editSequence = $('#editSequence'),
	galURL = $('a#viewTab').attr('href');


/*
*
* BIND EVENTS
*
*/
	// Show/hide controls on mouseover
	$editSequence.on({
		mouseenter: function() {
			$(this).find('.galImageControls').stop(false,true).fadeIn('fast');
		},
		mouseleave: function() {
			$(this).find('.galImageControls').stop(false,true).fadeOut('fast');
		}
	}, '.adminThumb');

	// Sortable
	$editSequence.sortable({
		handle: '.handle',
		update: updateSortable
	});

	// Refresh sortable on page load
	updateSortable();

	// Trash Image
	$editSequence.on('click', '.trashImage', trashImage);
	
/*
 *
 * CORE FUNCTIONS
 *
 */
 	function trashImage(ev) {
 		ev.preventDefault();
		var contentID = $(this).attr('id'),
		$thisImage = $(this).closest('.adminThumb');
		if(!confirm('Are you sure you want to trash this image?')) { 
			return false;
		}
 		$.ajax({
			type: 'DELETE',
			url: galURL + '/trashContent/' + contentID,
			dataType: 'json',
			success: function(data) {
				if(!data.error) {
					$thisImage.fadeOut(300, function() {
						$(this).remove();
						updateSortable();
					});
				}
			}
		});
 	}

 	function updateSortable() {
 		var order = $editSequence.sortable('serialize');
 		$.ajax({
 			url: galURL + '/sortGalImages/',
 			type: 'POST',
 			data: order,
 			success: function(){}
 		});
 	}		
});