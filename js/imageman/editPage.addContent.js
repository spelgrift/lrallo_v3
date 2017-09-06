var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var _ = require('./utilityFunctions.js'); // helper functions

$(function() {
/**
 * 
 * CACHE DOM
 * 
 */
	var $contentArea 	= $('#contentArea'),
	$addTab 				= $('a.addTab'),
	pageURL 				= _.getURL();

	var $addPageModal = $('#addPageModal'),
	$addGalModal 		= $('#addGalleryModal'),
	$addSSModal 		= $('#addSSModal'),
	$addVideoModal 	= $('#addVideoModal'),
	$addEVModal 		= $('#addEmbedVideoModal'),
	$addImageModal 	= $('#addImageModal'),
	$addSpacerModal 	= $('#addSpacerModal'),
	$submitSpacer 		= $addSpacerModal.find('button#submitNewSpacer'),
	spacerTemplate 	= $('#spacerTemplate').html();

/**
 * 
 * BIND EVENTS
 * 
 */
	// Display modal based on which type is clicked
	$addTab.click(selectModal);

	// Submit Spacer
	$submitSpacer.click(submitSpacer);	

/**
 * 
 * MAIN FUNCTIONS
 * 
 */
 	function selectModal(ev) {
		ev.preventDefault();
		var type = $(this).attr('data-id');
		switch(type) {
			case 'page' :
				$addPageModal.modal('show');
			break;
			case 'gallery' :
				$addGalModal.modal('show');
			break;
			case 'slideshow':
				$addSSModal.modal('show');
			break;
			case 'video' :
				$addVideoModal.modal('show');
			break;
			case 'embedVideo' :
				$addEVModal.modal('show');
			break;
			case 'spacer' :
				$addSpacerModal.modal('show');
			break;
			case 'singleImage' :
				$addImageModal.modal('show');
			break;
		}
	}

	function submitSpacer(ev) {
		ev.preventDefault();
		// Post to server
		$.ajax({
			type: 'POST',
			url: pageURL + '/addSpacer',
			dataType: 'json',
			success: function( data ) {
				if(!data.error) {
					// Success
					$addSpacerModal.modal('hide');
					var newSpacerObj = {	contentID : data.results.contentID };
					$contentArea.prepend(Mustache.render(spacerTemplate, newSpacerObj));
				}
			}

		});
	}	
});