var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var _ = require('./functions.addContent.js'); // helper functions

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
 	// Submit 
	$submitText.click(submitText);

/**
 * 
 * CORE FUNCTIONS
 * 
 */
 	function submitText(ev) {
		ev.preventDefault();
		// Get user input
		var newText = $addTextArea.val();
		// Validate
		if(newText.length < 1) {
			return _.error('Please enter some text', $textMsg, $addTextArea);
		}
		// Post to server
		$.ajax({
			type: 'POST',
			url: pageURL + '/addText',
			data: { text : newText },
			dataType: 'json',
			success: function( data ) {
				if(!data.error) { // Success
					$addTextArea.val('');
					$addTextModal.modal('hide');
					var newTextObject = {
						contentID : data.results.contentID,
						textID : data.results.textID,
						text : newText
					};
					$contentArea.prepend(Mustache.render(textTemplate, newTextObject));
				} else { // Error
					_.error(data.error_msg, $textMsg, $addTextArea);
				}
			}
		});
	}
});