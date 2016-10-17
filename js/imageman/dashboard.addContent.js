var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');

$(function() {

/**
 * 
 * CACHE DOM
 * 
 */
	var $contentList = $('#contentList'),
	$addTab = $('a.addTab');

/**
 * 
 * BIND EVENTS
 * 
 */
	// Display modal based on which type is clicked
	$addTab.click(selectModal);

/**
 * 
 * MAIN FUNCTIONS
 * 
 */
	function selectModal(ev) {
		ev.preventDefault();
		var Type = uc_first($(this).attr('data-id')),
		$targetModal = $('#add'+Type+'Modal');
		$targetModal.modal('show');
	}

	function uc_first(string) {
		return string.charAt(0).toUpperCase() + string.slice(1);
	}
});