var $ = require('jquery');
require('../libs/jquery-ui.sortable');
$(function() {
/*
*
* CONFIG
*
*/
	var currentCoverHTML = '<p>This is the gallery cover</p>';

/*
*
* CACHE DOM
*
*/
	var $editSequence = $('#editSequence'),
	galURL = $('a#viewTab').attr('href'),
	coverID = $editSequence.attr('data-coverID'),
	$imageSettingsModal = $('#galImageSettingsModal'),
	$settingsThumb = $imageSettingsModal.find('#settingsThumb'),
	$captionField = $imageSettingsModal.find('#captionField'),
	$coverMsg = $imageSettingsModal.find('#coverMsg'),
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

	// Set Cover button
	$imageSettingsModal.on('click', '#makeCover', makeCover);

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

 	function makeCover(ev) {
 		ev.preventDefault();
 		var imgID = $(this).attr('data-id');

 		$.ajax({
 			type: 'POST',
 			url: galURL + '/newCover/' + imgID,
 			dataType: 'json',
 			success: function(data) {
 				if(!data.error) {
 					coverID = imgID;
 					$coverMsg.html(currentCoverHTML);
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
 		// Populate thumb and caption field with values for this image
 		$settingsThumb.html('').html("<img src='"+thumbSrc+"' class='img-responsive img-rounded'>");
 		$captionField.val('').val(caption);
 		$saveCaption.attr('data-id', imgID);
 		// If image is cover say so, otherwise display button to set this image as cover
 		if(imgID == coverID) {
 			$coverMsg.html(currentCoverHTML);
 		} else {
 			$coverMsg.html("<button type='button' id='makeCover' data-id='"+imgID+"' class='btn btn-success'>Set as Gallery Cover</button>");
 		}

 		$imageSettingsModal.modal('show');
 	}	
});