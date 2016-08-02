var $ = require('jquery');
require('../libs/jquery-ui.sortable');
$(function() {
	var $editSequence = $('#editSequence'),
	galURL = $('a#viewTab').attr('href'),
	$imageSettingsModal = $('#galImageSettingsModal'),
	$settingsThumb = $imageSettingsModal.find('#settingsThumb'),
	$captionField = $imageSettingsModal.find('#captionField'),
	$saveCaption = $imageSettingsModal.find('#saveCaption');


/*
*
* BIND EVENTS
*
*/
	// Sortable
	$editSequence.sortable({
		handle: '.handle',
		update: updateSortable
	});

	// Refresh sortable on page load
	updateSortable();

	// Trash Image
	$editSequence.on('click', '.trashImage', trashImage);

	// Image settings button
	$editSequence.on('click', '.imageOptions', loadSettingsModal);

	// Save Caption
	$imageSettingsModal.on('click', '#saveCaption', saveCaption);
	
/*
 *
 * CORE FUNCTIONS
 *
 */
 	function saveCaption(ev) {
 		ev.preventDefault();
 		var imgID = $(this).attr('data-id'),
 		caption = $captionField.val();

 		$.ajax({
 			type: 'POST',
 			url: galURL + '/updateCaption/' + imgID,
 			data: { caption: caption },
 			dataType: 'json',
 			success: function(data) {
 				if(!data.error) {
 					$editSequence.find('img#'+imgID).attr('title', caption);
 					$imageSettingsModal.modal('hide');
 				}
 			}
 		});


 	}
 	function trashImage(ev) {
 		ev.preventDefault();
		var contentID = $(this).attr('id'),
		$thisImage = $(this).closest('.thumbnail');
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
 			success: function() {
 				// Update thumbnail links
 				$editSequence.find('a.thumbLink').each(function(i) {
 					var href = galURL + "/slide/" + i;
 					$(this).attr('href', href);
 				});
 			}
 		});
 	}	

 	function loadSettingsModal(ev) {
 		ev.preventDefault();
 		var imgID = $(this).attr('id'),
 		$thisBlock = $(this).closest('.thumbnail'),
 		$thisThumb = $thisBlock.find('img'),
 		thumbSrc = $thisThumb.attr('src'),
 		caption = $thisThumb.attr('title');

 		$settingsThumb.html('').html("<img src='"+thumbSrc+"' class='img-responsive img-rounded'>");
 		$captionField.val('').val(caption);
 		$saveCaption.attr('data-id', imgID);

 		$imageSettingsModal.modal('show');
 	}	
});