var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var _ = require('./functions.dialogError.js'); // helper functions

$(function() {
/**
 * 
 * CACHE DOM
 * 
 */
 	var $contentArea 		= $('#contentArea'),
	$addTab 					= $('a.addTab'),
	pageURL 					= $('a#viewTab').attr('href');

	var $addVideoModal 	= $('#addVideoModal'),
	$vidNameInput 			= $addVideoModal.find('input#newVideoName'),
	$vidNameMsg 			= $addVideoModal.find('#videoNameMsg'),
	$vidLinkInput 			= $addVideoModal.find('input#newVideoLink'),
	$vidLinkMsg 			= $addVideoModal.find('#videoLinkMsg'),
	$submitVid 				= $addVideoModal.find('#submitNewVideo'),
	videoTemplate 			= $('#videoTemplate').html();

/**
 * 
 * BIND EVENTS
 * 
 */
 	// Submit Video
	$submitVid.click(submitVideo);

/**
 * 
 * CORE FUNCTIONS
 * 
 */
 	function submitVideo(ev) {
		ev.preventDefault();
		// Get user input
		var data = {
			'name' : $vidNameInput.val(),
			'link' : $vidLinkInput.val()
		};
		// Validate
		if(data.name.length < 1) {
			return _.error("You must enter a name!", $vidNameMsg, $vidNameInput);
		}
		if(data.link.length < 1) {
			return _.error("You must enter a link!", $vidLinkMsg, $vidLinkInput);
		}
		// POST
		$.ajax({
			type: 'POST',
			url: pageURL + '/addVideo',
			data: data,
			dataType: 'json',
			success: function(data) {
				if(!data.error) { // Success
					// Hide modal
					$addVideoModal.modal('hide');
					$contentArea.prepend(Mustache.render(videoTemplate, data.results));
				} else {
					_.error(data.error_msg, $vidLinkMsg, $vidLinkInput);
				}
			}
		});
	}
});