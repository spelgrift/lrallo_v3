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
	$addPageModal 			= $('#addPageModal'),
	$pageNameInput 		= $addPageModal.find('input#newPageName'),
	$submitPage 			= $addPageModal.find('button#submitNewPage'),
	$pageMsg 				= $addPageModal.find('#pageMsg'),
	pageListTemplate 		= $('#pageListTemplate').text();

/**
 * 
 * BIND EVENTS
 * 
 */
 	$submitPage.click(submitPage);
 	$addPageModal.on('show.bs.modal', resetModal);

/**
 * 
 * MAIN FUNCTIONS
 * 
 */
 	function submitPage(ev) {
		ev.preventDefault();
		// Get user input
		var data = { name : $pageNameInput.val() };
		// Validate
		if(data.name.length < 1) {
			return _.error("You must enter a name!", $pageMsg, $pageNameInput);
		}
		var url = baseURL + 'dashboard/addPage';
		_.post(url, data, submitSuccess, submitError);
	}

	function submitSuccess(data) {
		$pageNameInput.val("");
		$addPageModal.modal('hide');
		if($tableBody.find('tr.placeholderRow').length > 0) {
			$tableBody.find('tr.placeholderRow').remove();
		}

		$tableBody.prepend(Mustache.render(pageListTemplate, data.results));
	}

	function submitError(data) {
		_.error(data.error_msg, $pageMsg, $pageNameInput);
	}

	function resetModal() {
		$pageNameInput.val('');
		$pageMsg.html('');
	}
});