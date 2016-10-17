var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var Dropzone = require('../libs/dropzone.js');
var _ = require('./utilityFunctions.js'); // helper functions

$(function() {
/**
 * 
 * CACHE DOM
 * 
 */
 	var $contentArea 	= $('#contentArea'),
	$addTab 				= $('a.addTab'),
	pageURL 				= $('a#viewTab').attr('href');

	var $addGalModal 	= $('#addGalleryModal'),
	$galNameInput 		= $addGalModal.find('input#newGalName'),
	galDZtemplate 		= $('#galDZTemplate').html(),
	$submitGal 			= $addGalModal.find('button#submitNewGal'),
	$galMsg 				= $addGalModal.find('#galleryMsg'),
	$galProgress 		= $addGalModal.find('#galleryProgress'),
	$galProcessing 	= $addGalModal.find('#galleryLoading'),
	galleryTemplate 	= $('#galleryTemplate').html();

/**
 * 
 * DROPZONES
 * 
 */
	Dropzone.autoDiscover = false;

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
 	// Submit 
	$submitGal.click(submitGal);

	// Handle Dropzone success
	$galleryDropzone.on("successmultiple", galleryDZsuccess);

	// Enable Submit button when file added
	$galleryDropzone.on("addedfile", function(file) {
		$submitGal.removeAttr('disabled');
	});

	// Disable Submit button when no files
	$galleryDropzone.on("removedfile", function(file) {
		if($galleryDropzone.files.length === 0) {
			$submitGal.attr('disabled', 'disabled');
			$galProcessing.hide();
		}
	});

	// Remove files when modal closed
	$addGalModal.on('hidden.bs.modal', function() {
		$galleryDropzone.removeAllFiles();
	});

	// Reset modal when modal shown
	$addGalModal.on('show.bs.modal', function () {
		$galProcessing.hide();
	});

	// Update total progress bar (Gallery)
	$galleryDropzone.on('totaluploadprogress', galleryDZprogress);
	
/**
 * 
 * CORE FUNCTIONS
 * 
 */
 	function submitGal(ev) {
		ev.preventDefault();
		// Get user input
		var data = {
			'name' : $galNameInput.val()
		};
		// Validate
		if(data.name.length < 1) {
			return _.error("You must enter a name!", $galMsg, $galNameInput);
		}
		var url = pageURL + '/addGallery';
		_.post(url, data, submitSuccess, submitError);
	}

	function submitSuccess(data) {
		$galleryDropzone.options.params = {
			'galID' : data.results.galID,
			'galURL' : data.results.galURL
		};
		$galProgress.show();
		$submitGal.attr('disabled', 'disabled');
		$galleryDropzone.processQueue();
	}

	function submitError(data) {
		_.error(data.error_msg, $galMsg, $galNameInput);
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
});