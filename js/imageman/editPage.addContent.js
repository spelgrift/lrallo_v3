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

	// Add Gallery
	var $addGalModal = $('#addGalModal'),
	$galNameInput = $addGalModal.find('input#newGalName'),
	galDZtemplate = $('#galDZTemplate').html(),
	$submitGal = $addGalModal.find('button#submitNewGal'),
	$galMsg = $addGalModal.find('#galleryMsg'),
	$galProgress = $addGalModal.find('#galleryProgress'),
	$galProcessing = $addGalModal.find('#galleryLoading'),
	galleryTemplate = $('#galleryTemplate').html();

	// Add Video Page
	var $addVideoModal = $('#addVideoModal'),
	$vidNameInput = $addVideoModal.find('input#newVideoName'),
	$vidNameMsg = $addVideoModal.find('#videoNameMsg'),
	$vidLinkInput = $addVideoModal.find('input#newVideoLink'),
	$vidLinkMsg = $addVideoModal.find('#videoLinkMsg'),
	$submitVid = $addVideoModal.find('#submitNewVideo'),
	videoTemplate = $('#videoTemplate').html();

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

	//
	// Gallery Dropzone
	//
	var $galleryDropzone = new Dropzone('div.addGalleryDropzone', {
		url : pageURL + '/uploadGalImages/',
		autoProcessQueue : false,
		uploadMultiple : true,
		parallelUploads: 50,
		maxFilesize : 3,
		acceptedFiles : "image/*,.jpg,.JPG",
		thumbnailWidth : 125,
		thumbnailHeight : 125,
		previewTemplate : galDZtemplate,
		dictDefaultMessage : "Drop image files here<br>(or click)"
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

	// Submit Gallery
	$submitGal.click(submitGal);

	// Submit Video
	$submitVid.click(submitVideo);

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
	// DROPZONE EVENTS
	//

	// Handle Dropzone success
	$singleImgDropzone.on("success", singleImgDZsuccess);

	$galleryDropzone.on("successmultiple", galleryDZsuccess);

	// Enable Submit button when file added
	$singleImgDropzone.on("addedfile", function(file) {
		$submitImage.removeAttr('disabled');
	});

	$galleryDropzone.on("addedfile", function(file) {
		$submitGal.removeAttr('disabled');
	});

	// Disable Submit button when no files
	$singleImgDropzone.on("removedfile", function(file) {
		if($singleImgDropzone.files.length === 0) {
			$submitImage.attr('disabled', 'disabled');
		}
	});

	$galleryDropzone.on("removedfile", function(file) {
		if($galleryDropzone.files.length === 0) {
			$submitGal.attr('disabled', 'disabled');
			$galProcessing.hide();
		}
	});

	// Remove files when modal closed
	$addImageModal.on('hidden.bs.modal', function() {
		$singleImgDropzone.removeAllFiles();
	});

	$addGalModal.on('hidden.bs.modal', function() {
		$galleryDropzone.removeAllFiles();
	});

	// Remove file if more than 1 added (Single Image)
	$singleImgDropzone.on("maxfilesexceeded", function(file) {
		this.removeFile(file);
	});

	// Update total progress bar (Gallery)
	$galleryDropzone.on('totaluploadprogress', galleryDZprogress);


	

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
			return error('You must enter a name', $pageMsg, $pageNameInput);
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
					error(data.error_msg, $pageMsg, $pageNameInput);
				}
			}
		});
	}

	function submitVideo(ev) {
		ev.preventDefault();
		// Get user input
		var data = {
			'name' : $vidNameInput.val(),
			'link' : $vidLinkInput.val()
		};
		// Validate
		if(data.name.length < 1) {
			return error("You must enter a name!", $vidNameMsg, $vidNameInput);
		}
		if(data.link.length < 1) {
			return error("You must enter a link!", $vidLinkMsg, $vidLinkInput);
		}
		// POST
		$.ajax({
			type: 'POST',
			url: pageURL + '/addVideo',
			data: data,
			dataType: 'json',
			success: function(data) {
				if(!data.error) { // Success
					// Hide modal
					$addVideoModal.modal('hide');
					$contentArea.prepend(Mustache.render(videoTemplate, data.results));
				} else {
					error(data.error_msg, $vidLinkMsg, $vidLinkInput);
				}
			}
		});
	}

	function submitGal(ev) {
		ev.preventDefault();
		// Get user input
		var galName = $galNameInput.val();
		// Validate
		if(galName.length < 1) {
			return error("You must enter a name!", $galMsg, $galNameInput);
		}
		// Post to server
		$.ajax({
			type: 'POST',
			url: pageURL + '/addGallery',
			data: { name : galName },
			dataType: 'json',
			success: function( data ) {
				if(!data.error) { // Success
					$galleryDropzone.options.params = {
						galID : data.results.galID,
						galURL : data.results.galURL
					};
					$galProgress.show();
					$submitGal.attr('disabled', 'disabled');
					$galleryDropzone.processQueue();
				} else { // Error
					error(data.error_msg, $galMsg, $galNameInput);
				}
			}
		});
	}

	function galleryDZprogress(progress) {
		if(progress < 100) {
			$galProgress.find('.progress-bar').css('width', progress + '%');
		} else {
			$galProgress.hide().find('.progress-bar').css('width', '0%');
			$galProcessing.show();
		}
	}

	function galleryDZsuccess(files, data) {
		data = JSON.parse(data);

		$galleryDropzone.removeAllFiles();
		$galNameInput.val('');
		$galProcessing.hide();

		if(!data.error) { // Success
			$addGalModal.modal('hide');
			$contentArea.prepend(Mustache.render(galleryTemplate, data.results));
		} else { // Error!
			if(data.hasOwnProperty('error_details')) {
				$galMsg.html(data.error_msg);
				$.each(data.error_details, function() {
					$galMsg.append(this.name+" : "+this.error+"<br>");
				});
				$contentArea.prepend(Mustache.render(galleryTemplate, data.results));
			}
		}
	}

	function submitText(ev) {
		ev.preventDefault();
		// Get user input
		var newText = $addTextArea.val();
		// Validate
		if(newText.length < 1) {
			return error('Please enter some text', $textMsg, $addTextArea);
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
					error(data.error_msg, $textMsg, $addTextArea);
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
			case 'gallery' :
				$addGalModal.modal('show');
			break;
			case 'video' :
				$addVideoModal.modal('show');
			break;
			case 'spacer' :
				$addSpacerModal.modal('show');
			break;
			case 'singleImage' :
				$addImageModal.modal('show');
			break;
		}
	}

	function error(message, $msg, $input) {
		$msg.html(message);
		$input.focus();
		clearMsg($msg);
		return false;
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