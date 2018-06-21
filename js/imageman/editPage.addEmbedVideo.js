var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var _ = require('./utilityFunctions.js'); // helper functions

$(function() {
/**
 * 
 * CONFIG
 * 
 */
 	var vimeoSrc = "https://player.vimeo.com/video/",
 	youtubeSrc 		= "https://www.youtube.com/embed/";

 	// Page or Post?
	var isPost = false,
	postID = "";
	if((window.location.href).includes(baseURL+blogURL+"/")) {
		isPost = true;
		postID = $('#adminNav').attr('data-id');
	}

/**
 * 
 * CACHE DOM
 * 
 */
 	var $contentArea 	= $('#contentArea'),
	$addTab 				= $('a.addTab'),
	pageURL 				= _.getURL(isPost);

	var $addEVModal 	= $('#addEmbedVideoModal'),
	$evSelect 			= $addEVModal.find('#evSelect'),
	$evNameInput 		= $addEVModal.find('input#newEVName'),
	$evLinkInput 		= $addEVModal.find('input#newEVLink'),
	$submitEV 			= $addEVModal.find('button#submitNewEV'),
	$evNameMsg 			= $addEVModal.find('#evNameMsg'),
	$evLinkMsg 			= $addEVModal.find('#evLinkMsg'),
	$evProgress 		= $addEVModal.find('#evProgress'),
	$evProcessing 		= $addEVModal.find('#evLoading'),
	evTemplate			= $('#evTemplate').html();

/**
 * 
 * BIND EVENTS
 * 
 */
 	// Submit
 	$submitEV.click(handleSubmit);

 	// Disable new form when existing vid selected
	$evSelect.change(changeEVselect);

	// Disable select when name or link entered/enable on no entry
	$evNameInput.keyup(changeEVname);
	$evLinkInput.keyup(changeEVname);

	// Reset modal when closed
	$addEVModal.on('hidden.bs.modal', resetModal);

/**
 * 
 * CORE FUNCTIONS
 * 
 */
 	function submitNewEV() {
 		// Get user input
 		var data = {
			'name' : $evNameInput.val(),
			'link' : $evLinkInput.val()
		};
		// Validate
		if(data.name.length < 1) {
			return _.error("You must enter a name!", $evNameMsg, $evNameInput);
		}
		if(data.link.length < 1) {
			return _.error("You must enter a link!", $evLinkMsg, $evLinkInput);
		}
		// POST
		var url = pageURL + '/addEmbedVideo/';
		if(isPost) { url = pageURL+/addNewEV/+postID; }
		_.post(url, data, submitSuccess, submitError);
 	}

 	function submitSuccess(data) {
		$addEVModal.modal('hide');
		renderEV(data.results);
		events.emit('contentAdded');
 	}

 	function submitError(data) {
 		_.error(data.error_msg, $evLinkMsg, $evLinkInput);
 	}

 	function submitExistingEV() {
 		var videoID = $evSelect.val(),
 		url = pageURL + '/addEmbedVideo/' + videoID + "/" + postID;
 		_.post(url, {}, submitSuccess, submitError);
 	}

 	function renderEV(data) {
 		var src, newEVobject = {
 			'evID' : data.evID,
 			'contentID' : data.contentID
 		};
 		if(data.source == 'youtube') {
 			src = youtubeSrc + data.link;
 		} else if(data.source == 'vimeo') {
 			src = vimeoSrc + data.link;
 		}
 		var $newEV = $(Mustache.render(evTemplate, newEVobject));
 		$newEV.prependTo($contentArea).hide().find('.embed-responsive-item').attr('src', src);
 		$newEV.fadeIn('300');
 	}

 	function changeEVselect() {
 		if($(this).val() !== '0') {
 			// Video selected, disable new form
 			$evNameInput.attr('disabled', 'disabled');
 			$evLinkInput.attr('disabled', 'disabled');
 			$submitEV.removeAttr('disabled');
 			$submitEV.attr('data-new', false);
 		} else {
 			// No selection, enable new form
 			$evNameInput.removeAttr('disabled');
 			$evLinkInput.removeAttr('disabled');
 			$submitEV.attr('disabled', 'disabled');
 			$submitEV.attr('data-new', true);
 		}
 	}

 	function changeEVname() {
 		if($evNameInput.val() !== '' || $evLinkInput.val() !== '') {
 			// Name or link entered, disable select
 			$evSelect.attr('disabled', 'disabled');
 			$submitEV.removeAttr('disabled').attr('data-new', true);
 		} else {
 			// Enable select, disable submit button
 			$evSelect.removeAttr('disabled');
 			$submitEV.attr('disabled', 'disabled');
 		}
 	}

 	function resetModal() {
 		$evSelect.val('0').removeAttr('disabled');
 		$evNameInput.val('').removeAttr('disabled');
 		$evLinkInput.val('').removeAttr('disabled');
 		$submitEV.attr('disabled', 'disabled');
 	}

 	function handleSubmit(ev) {
 		ev.preventDefault();
 		if($(this).attr('data-new') == 'true') {
 			submitNewEV();
 		} else {
 			submitExistingEV();
 		}
 	}

});