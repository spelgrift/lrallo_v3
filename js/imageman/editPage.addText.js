var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var _ = require('./utilityFunctions.js'); // helper functions

$(function() {
/**
 * 
 * CONFIG
 * 
 */
 	var defaultText = "<p>New text. Click to edit.</p>";
/**
 * 
 * CACHE DOM
 * 
 */
 	var $contentArea 	= $('#contentArea'),
	$addTab 				= $('a.addTab'),
	textTemplate 		= $('#textTemplate').html().replace('amp;', '');
	pageURL 				= _.getURL();

/**
 * 
 * BIND EVENTS
 * 
 */
	$addTab.click(submitText);

/**
 * 
 * CORE FUNCTIONS
 * 
 */
 	function submitText(ev) {
		ev.preventDefault();
		if($(this).attr('data-id') !== 'text'){ return false;	}
		var data = { text : defaultText },
		url = pageURL + '/addText';
		_.post(url, data, submitSuccess, submitError);
	}

	function submitSuccess(data) {
		var newTextObject = {
			contentID : data.results.contentID,
			textID : data.results.textID,
			text : data.results.text
		};
		$contentArea.prepend(Mustache.render(textTemplate, newTextObject));
		events.emit('newText');
	}

	function submitError(data) {
		console.log('Error: '+data.error_msg);
	}
});