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

	// Add Page
	var $addPageModal = $('#addPageModal'),
	$pageNameInput 	= $addPageModal.find('input#newPageName'),
	$submitPage 		= $addPageModal.find('button#submitNewPage'),
	$pageMsg 			= $addPageModal.find('#pageMsg'),
	pageTemplate 		= $('#pageTemplate').html();

/**
 * 
 * BIND EVENTS
 * 
 */
 	// Submit Page
	$submitPage.click(submitPage);

/**
 * 
 * CORE FUNCTIONS
 * 
 */
	function submitPage(ev) {
		ev.preventDefault();
		// Get user input
		var data = {
			'name' : $pageNameInput.val()
		};
		// Validate
		if(data.name.length < 1) {
			return _.error('You must enter a name', $pageMsg, $pageNameInput);
		}
		var url = pageURL + '/addPage';
		_.post(url, data, submitSuccess, submitError);
	}

	function submitSuccess(data) {
		$pageNameInput.val("");
		$addPageModal.modal('hide');
		$contentArea.prepend(Mustache.render(pageTemplate, data.results));
	}

	function submitError(data) {
		_.error(data.error_msg, $pageMsg, $pageNameInput);
	}
});