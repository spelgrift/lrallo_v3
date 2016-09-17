var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var _ = require('./functions.addContent.js');

$(function() {
/**
 * 
 * CACHE DOM
 * 
 */
 	var $contentArea 	= $('#contentArea'),
	$addTab 				= $('a.addTab'),
	pageURL 				= $('a#viewTab').attr('href');

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

	function submitPage(ev) {
		ev.preventDefault();
		// Get user input
		var pageName = $pageNameInput.val();
		// Validate
		if(pageName.length < 1) {
			return _.error('You must enter a name', $pageMsg, $pageNameInput);
		}
		// Check if taken here?

		// Post to server
		$.ajax({
			type: 'POST',
			url: pageURL + '/addPage',
			data: { name : pageName },
			dataType: 'json',
			success: function( data ) {
				if(!data.error) { // Success
					$pageNameInput.val("");
					$addPageModal.modal('hide');
					$contentArea.prepend(Mustache.render(pageTemplate, data.results));
				} else { // Error
					_.error(data.error_msg, $pageMsg, $pageNameInput);
				}
			}
		});
	}
});

