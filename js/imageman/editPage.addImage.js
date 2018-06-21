var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var _ = require('./utilityFunctions.js'); // helper functions
var Dropzone = require('../libs/dropzone.js');

$(function() {

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
 	var $contentArea 		= $('#contentArea'),
	$addTab 					= $('a.addTab'),
	pageURL 					= _.getURL(isPost);

	var $addImageModal 	= $('#addImageModal'),
	$submitImage 			= $addImageModal.find('button#submitNewImage'),
	singleImgDZTemplate 	= $('#singleImgDZTemplate').html(),
	$imageMsg 				= $addImageModal.find('#imageMsg'),
	singleImgTemplate 	= $('#singleImgTemplate').html();

/**
 * 
 * DROPZONES
 * 
 */
	Dropzone.autoDiscover = false;

	var $singleImgDropzone = new Dropzone('div.singleImageDropzone', {
		url : pageURL + '/addSingleImage/'+postID,
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
 	// Submit Single Image
	$submitImage.click(function(ev) {
		ev.preventDefault();
		$singleImgDropzone.processQueue();
	});

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

	// Remove files when modal closed
	$addImageModal.on('hidden.bs.modal', function() {
		$singleImgDropzone.removeAllFiles();
	});

	// Remove file if more than 1 added
	$singleImgDropzone.on("maxfilesexceeded", function(file) {
		this.removeFile(file);
	});

/**
 * 
 * CORE FUNCTIONS
 * 
 */
 	function singleImgDZsuccess(file, data) {
		data = JSON.parse(data);
		if(!data.error) { // Success!
			// Hide modal
			$addImageModal.modal('hide');
			// Render template
			$(Mustache.render(singleImgTemplate, data.results)).hide().prependTo($contentArea).fadeIn('slow');
			events.emit('contentAdded');
		} else { // Error!		
			$singleImgDropzone.emit("error", file, data.error_msg);
		}
	}
});