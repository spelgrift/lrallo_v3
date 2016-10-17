var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var _ = require('./utilityFunctions.js'); // helper functions

$(function() {
	
/**
 * 
 * CACHE DOM
 * 
 */
 	var $contentList 		= $('#contentList'),
	$contentTypeFilter 	= $contentList.find('select#filterContentList'),
	$tableBody 				= $contentList.find('tbody'),
	$addVideoModal 		= $('#addVideoModal'),
	$vidNameInput 			= $addVideoModal.find('input#newVideoName'),
	$vidNameMsg 			= $addVideoModal.find('#videoNameMsg'),
	$vidLinkInput 			= $addVideoModal.find('input#newVideoLink'),
	$vidLinkMsg 			= $addVideoModal.find('#videoLinkMsg'),
	$submitVid 				= $addVideoModal.find('#submitNewVideo'),
	pageListTemplate 		= $('#pageListTemplate').text();

/**
 * 
 * BIND EVENTS
 * 
 */
 	$submitVid.click(submitVideo);
 	$addVideoModal.on('show.bs.modal', resetModal);

/**
 * 
 * MAIN FUNCTIONS
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
		var url = baseURL + 'dashboard/addVideo';
		_.post(url, data, submitSuccess, submitError);
	}

	function submitSuccess(data) {
		$addVideoModal.modal('hide');
		$contentTypeFilter.val('video');
		events.emit('changeContentFilter', 'video');
		$tableBody.prepend(Mustache.render(pageListTemplate, data.results));
	}

	function submitError(data) {
		_.error(data.error_msg, $vidLinkMsg, $vidLinkInput);
	}

	function resetModal() {
		$vidNameInput.val('');
		$vidLinkInput.val('');
		$vidNameMsg.html('');
		$vidLinkMsg.html('');
	}
});