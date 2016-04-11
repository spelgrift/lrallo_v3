var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var Dropzone = require('../libs/dropzone.js');

$(function() {
/**
 * 
 * CACHE DOM
 * 
 */
 	var pageURL = $('a#viewTab').attr('href'),
 	$thumbList = $('#editSequence'),
 	$addImages = $('a.addImages'),
 	$addImageModal = $('#addGalImagesModal'),
 	galDZtemplate = $('#galDZTemplate').html(),
	$submitGal = $addImageModal.find('button#submitImages'),
	$galMsg = $addImageModal.find('#galleryMsg'),
	$galProgress = $addImageModal.find('#galleryProgress'),
	$galProcessing = $addImageModal.find('#galleryLoading'),
	thumbTemplate = $('#thumbTemplate').html();

/**
 * 
 * BIND EVENTS
 * 
 */
 	// Display modal
 	$addImages.click(function(ev) {
 		ev.preventDefault();
 		$addImageModal.modal('show');
 	});

 	// Handle submit
 	$submitGal.on('click', submitGal);

/**
 * 
 * MAIN FUNCIONS
 * 
 */

	function submitGal(ev) {
		ev.preventDefault();
		$galProgress.show();
		$submitGal.attr('disabled', 'disabled');
		$addImageDropzone.processQueue();
	}

	function appendThumbs(images) {
		var delay = 100;
		$.each(images, function(i, image) {
			setTimeout(function() {
				$(Mustache.render(thumbTemplate, image)).appendTo($thumbList).hide().fadeIn();
			}, (i * delay));
			
		});
	}

 /**
 * 
 * DROPZONE
 * 
 */
	Dropzone.autoDiscover = false;

	//
	// Gallery Dropzone
	//
	var $addImageDropzone = new Dropzone('div.addImageDropzone', {
		url : pageURL + '/addGalImages',
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

	// Handle Dropzone success
	$addImageDropzone.on("successmultiple", function(files, data) {
		data = JSON.parse(data);

		$addImageDropzone.removeAllFiles();
		$galProcessing.hide();

		if(!data.error) { // Success!
			// Hide modal
			$addImageModal.modal('hide');
			// Append new images to the page
			appendThumbs(data.results.images);


		} else { // Error!		
			if(data.hasOwnProperty('error_details')) {
				
				$galMsg.html("<p class='text-danger'>"+data.error_msg+"</p>");
				$.each(data.error_details, function() {
					$galMsg.append("<p class='text-danger'>"+this.name+" : "+this.error+"</p>");
				});
				// Append new images to the page
				appendThumbs(data.results.images);
			} else {
				// Display general error
			}
		}
	});

	// Update total progress bar
	$addImageDropzone.on('totaluploadprogress', function(progress) {
		if(progress < 100) {
			$galProgress.find('.progress-bar').css('width', progress + '%');
		} else {
			$galProgress.hide().find('.progress-bar').css('width', '0%');
			$galProcessing.show();
		}
	});

	// Enable Submit button when file added
	$addImageDropzone.on("addedfile", function(file) {
		$submitGal.removeAttr('disabled');
	});

	// Disable Submit button when no files
	$addImageDropzone.on("removedfile", function(file) {
		if($addImageDropzone.files.length === 0) {
			$submitGal.attr('disabled', 'disabled');
		}
	});

	// Remove files when modal closed
	$addImageModal.on('hidden.bs.modal', function() {
		$addImageDropzone.removeAllFiles();
	});

});