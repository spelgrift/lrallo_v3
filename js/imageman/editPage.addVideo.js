var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var _ = require('./utilityFunctions.js'); // helper functions

$(function() {
/**
 * 
 * CACHE DOM
 * 
 */
 	var $contentArea 		= $('#contentArea'),
	$addTab 					= $('a.addTab'),
	pageURL 					= _.getURL();

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
		var url = pageURL + '/addVideo';
		_.post(url, data, submitSuccess, submitError);
	}

	function submitSuccess(data) {
		$addVideoModal.modal('hide');
		$contentArea.prepend(Mustache.render(videoTemplate, data.results));
	}

	function submitError(data) {
		_.error(data.error_msg, $vidLinkMsg, $vidLinkInput);
	}
});