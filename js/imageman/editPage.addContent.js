var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var Dropzone = require('../libs/dropzone.js');

$(function() {
/**
 * 
 * CACHE DOM
 * 
 */
	var $contentArea = $('#contentArea');
	var $addTab = $('a.addTab');
	var pageURL = $('a#viewTab').attr('href');

	// Type specific elements

	// Add Page
	var $addPageModal = $('#addPageModal'),
	$pageNameInput = $addPageModal.find('input#newPageName'),
	$submitPage = $addPageModal.find('button#submitNewPage'),
	$pageMsg = $addPageModal.find('#pageMsg'),
	pageTemplate = $('#pageTemplate').html();

	// Add Text
	var $addTextModal = $('#addTextModal'),
	$addTextArea = $addTextModal.find('#newTextArea'),
	$submitText = $addTextModal.find('button#submitNewText'),
	$textMsg = $addTextModal.find('#textMsg'),
	textTemplate = $('#textTemplate').html().replace('amp;', '');

	// Add Single  Image
	var $addImageModal = $('#addImageModal'),
	$submitImage = $addImageModal.find('button#submitNewImage'),
	singleImgDZTemplate = $('#singleImgDZTemplate').html(),
	$imageMsg = $addImageModal.find('#imageMsg'),
	singleImgTemplate = $('#singleImgTemplate').html();

	// Add Spacer
	var $addSpacerModal = $('#addSpacerModal'),
	$submitSpacer = $addSpacerModal.find('button#submitNewSpacer'),
	spacerTemplate = $('#spacerTemplate').html();

/**
 * 
 * DROPZONES
 * 
 */
	Dropzone.autoDiscover = false;

	//
	// Single Image Dropzone
	//
	var $singleImgDropzone = new Dropzone('div.singleImageDropzone', {
		url : pageURL + '/addSingleImage',
		autoProcessQueue : false,
		maxFiles : 1,
		maxFilesize : 3,
		thumbnailWidth : 125,
		thumbnailHeight : 125,
		previewTemplate : singleImgDZTemplate,
		dictDefaultMessage : "Drop file here to upload<br>(or click)"
	});

/**
 * 
 * BIND EVENTS
 * 
 */

	// Display modal based on which type is clicked
	$addTab.click(selectModal);

	// Submit Page
	$submitPage.click(submitPage);

	// Submit Text
	$submitText.click(submitText);

	// Submit Spacer
	$submitSpacer.click(submitSpacer);

	// Submit Single Image
	$submitImage.click(function(ev) {
		ev.preventDefault();
		$singleImgDropzone.processQueue();
	});

	//
	// SINGLE IMAGE DROPZONE EVENTS
	//

	// Handle Dropzone success
	$singleImgDropzone.on("success", singleImgDZsuccess);

	// Enable Submit button when file added
	$singleImgDropzone.on("addedfile", function(file) {
		$submitImage.removeAttr('disabled');
	});

	// Disable Submit button when no files
	$singleImgDropzone.on("removedfile", function(file) {
		if($singleImgDropzone.files.length === 0) {
			$submitImage.attr('disabled', 'disabled');
		}
	});

	// Remove file if more than 1 added
	$singleImgDropzone.on("maxfilesexceeded", function(file) {
		this.removeFile(file);
	});

	// Remove files when modal closed
	$addImageModal.on('hidden.bs.modal', function() {
		$singleImgDropzone.removeAllFiles();
	});

	

/**
 * 
 * MAIN FUNCTIONS
 * 
 */
	function submitPage(ev) {
		ev.preventDefault();
		// Get user input
		var pageName = $pageNameInput.val();
		// Validate
		if(pageName.length < 1) {
			$pageMsg.html("<p class='text-danger'>You must enter a name!</p>");
			$pageNameInput.focus();
			clearMsg($pageMsg);
			return false;
		}
		// Check if taken here?

		// Post to server
		$.ajax({
			type: 'POST',
			url: pageURL + '/addPage',
			data: { name : pageName },
			dataType: 'json',
			success: function( data ) {
				if(!data.error) {
					// Success
					$pageNameInput.val("");
					$addPageModal.modal('hide');
					var newPageObject = {
						contentID : data.results.contentID,
						pageID : data.results.pageID,
						name : pageName,
						url : data.results.url
					};
					$contentArea.prepend(Mustache.render(pageTemplate, newPageObject));
				} else {
					// Error
					$pageMsg.html("<p class='text-danger'>"+data.error_msg+"</p>");
					$pageNameInput.focus();
					clearMsg($pageMsg);
				}
			}
		});
	}

	function submitText() {
		// Get user input
		var newText = $addTextArea.val();
		// Validate
		if(newText.length < 1) {
			$textMsg.html("<p class='text-danger'>Please enter some text!</p>");
			$addTextArea.focus();
			clearMsg($textMsg);
			return false;
		}
		// Post to server
		$.ajax({
			type: 'POST',
			url: pageURL + '/addText',
			data: { text : newText },
			dataType: 'json',
			success: function( data ) {
				if(!data.error) {
					// Success
					$addTextArea.val('');
					$addTextModal.modal('hide');
					var newTextObject = {
						contentID : data.results.contentID,
						textID : data.results.textID,
						text : newText
					};
					$contentArea.prepend(Mustache.render(textTemplate, newTextObject));

					// events.js - refresh content, etc.
				} else {
					// Error
					$textMsg.html("<p class='text-danger'>"+data.error_msg+"</p>");
					$addTextArea.focus();
					clearMsg($textMsg);
				}
			}
		});
	}

	function singleImgDZsuccess(file, data) {
		data = JSON.parse(data);
		if(!data.error) { // Success!
			// Hide modal
			$addImageModal.modal('hide');
			// Render template
			$(Mustache.render(singleImgTemplate, data.results)).hide().prependTo($contentArea).fadeIn('slow');
		} else { // Error!		
			$singleImgDropzone.emit("error", file, data.error_msg);
		}
	}

	function submitSpacer(ev) {
		ev.preventDefault();
		// Post to server
		$.ajax({
			type: 'POST',
			url: pageURL + '/addSpacer',
			dataType: 'json',
			success: function( data ) {
				if(!data.error) {
					// Success
					$addSpacerModal.modal('hide');
					var newSpacerObj = {	contentID : data.results.contentID };
					$contentArea.prepend(Mustache.render(spacerTemplate, newSpacerObj));
				}
			}

		});
	}

/**
 * 
 * HELPER FUNCTIONS
 * 
 */
	function selectModal(ev) {
		ev.preventDefault();
		var type = $(this).attr('data-id');
		switch(type) {
			case 'text' :
				$addTextModal.modal('show');
			break;

			case 'page' :
				$addPageModal.modal('show');
			break;

			case 'spacer' :
				$addSpacerModal.modal('show');
			break;

			case 'singleImage' :
				$addImageModal.modal('show');
			break;
		}
	}

	function clearMsg(selector, timeout) {
		if (timeout === undefined) {
			timeout = 4000;
		}
		setTimeout(function(){
			selector.fadeOut('slow', function() {
				selector.html('');
				selector.show();
			});
		}, timeout);
	}

});