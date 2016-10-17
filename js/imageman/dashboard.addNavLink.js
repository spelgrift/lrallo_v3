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
	$mainNav 				= $('#mainNav').children('ul.navbar-nav'),
	$addTab 					= $('a.addTab'),

	$addNavLinkModal 		= $('#addNavLinkModal'),
	$navLinkNameInput 	= $addNavLinkModal.find('input#newNavName'),
	$navLinkUrlInput 		= $addNavLinkModal.find('input#newNavUrl'),
	$submitNavLink 		= $addNavLinkModal.find('button#submitNewNavLink'),
	$navLinkMsg				= $addNavLinkModal.find('#navLinkMsg');

/**
 * 
 * BIND EVENTS
 * 
 */
 	$submitNavLink.click(submitNavLink);
 	$addNavLinkModal.on('show.bs.modal', resetModal);

/**
 * 
 * MAIN FUNCTIONS
 * 
 */
 	function submitNavLink(ev) {
		ev.preventDefault();
		// Get user input
		var data = {
			name : $navLinkNameInput.val(),
			url : $navLinkUrlInput.val()
		};
		// Validate
		if(data.name.length < 1) {
			return error('You must enter a name', $navLinkMsg, $navLinkNameInput);
		}
		if(data.url.length < 1) {
			return error('You must enter a url', $navLinkMsg, $navLinkUrlInput);
		}
		var url = baseURL + 'dashboard/addNavLink';
		_.post(url, data, submitSuccess, submitError);
	}

	function submitSuccess(data) {
		$addNavLinkModal.modal('hide');
		reloadNav();
	}

	function submitError(data) {
		_.error(data.error_msg, $navLinkMsg, $navLinkNameInput);
	}

	function resetModal() {
		$navLinkNameInput.val("");
		$navLinkUrlInput.val("");
		$navLinkMsg.html('');
	}

	function reloadNav() {
		$mainNav.load(baseURL + 'dashboard/reloadNav', function() {
			events.emit('reloadNav');
		});
	}
});