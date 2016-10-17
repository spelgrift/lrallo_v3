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
	pageURL 				= $('a#viewTab').attr('href');

	var $addTextModal = $('#addTextModal'),
	$addTextArea 		= $addTextModal.find('#newTextArea'),
	$submitText 		= $addTextModal.find('button#submitNewText'),
	$textMsg 			= $addTextModal.find('#textMsg'),
	textTemplate 		= $('#textTemplate').html().replace('amp;', '');

/**
 * 
 * BIND EVENTS
 * 
 */
	$submitText.click(submitText);

/**
 * 
 * CORE FUNCTIONS
 * 
 */
 	function submitText(ev) {
		ev.preventDefault();
		// Get user input
		var data = { text : $addTextArea.val() };
		// Validate
		if(data.text.length < 1) {
			return _.error('Please enter some text', $textMsg, $addTextArea);
		}
		// Post to server
		var url = pageURL + '/addText';
		_.post(url, data, submitSuccess, submitError);
	}

	function submitSuccess(data) {
		$addTextArea.val('');
		$addTextModal.modal('hide');
		var newTextObject = {
			contentID : data.results.contentID,
			textID : data.results.textID,
			text : newText
		};
		$contentArea.prepend(Mustache.render(textTemplate, newTextObject));
	}

	function submitError(data) {
		_.error(data.error_msg, $textMsg, $addTextArea);
	}
});