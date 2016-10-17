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
 	var $contentList 		= $('#contentList'),
	$contentTypeFilter 	= $contentList.find('select#filterContentList'),
	$tableBody 				= $contentList.find('tbody'),
	$addGalModal 			= $('#addGalleryModal'),
	$galNameInput 			= $addGalModal.find('input#newGalName'),
	galDZtemplate 			= $('#galDZTemplate').html(),
	$submitGal 				= $addGalModal.find('button#submitNewGal'),
	$galMsg 					= $addGalModal.find('#galleryMsg'),
	$galProgress 			= $addGalModal.find('#galleryProgress'),
	$galProcessing 		= $addGalModal.find('#galleryLoading'),
	pageListTemplate 		= $('#pageListTemplate').text();

/**
 * 
 * DROPZONES
 * 
 */
	Dropzone.autoDiscover = false;

	//
	// Gallery Dropzone
	//
	var $galleryDropzone = new Dropzone('div.addGalleryDropzone', {
		url : baseURL + 'dashboard/uploadGalImages/',
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
 	// Submit Gallery
	$submitGal.click(submitGal);

	// Enable Submit button when file added
	$galleryDropzone.on("addedfile", enableSubmit);

	// Disable Submit button when no files
	$galleryDropzone.on("removedfile", disableSubmit);

	// Remove files/clear input when modal shown
	$addGalModal.on('show.bs.modal', resetModal);

	// Update total progress bar
	$galleryDropzone.on('totaluploadprogress', galleryDZprogress);

	// Handle Gallery Dropzone success
	$galleryDropzone.on("successmultiple", galleryDZsuccess);

/**
 * 
 * MAIN FUNCTIONS
 * 
 */
 	function submitGal(ev) {
		ev.preventDefault();
		// Get user input
		var data = { name : $galNameInput.val() };
		// Validate
		if(data.name.length < 1) {
			return _.error("You must enter a name!", $galMsg, $galNameInput);
		}
		var url = baseURL + 'dashboard/addGallery';
		_.post(url, data, submitSuccess, submitError);
	}

	function submitSuccess(data) {
		$galleryDropzone.options.params = {
			galID : data.results.galID,
			galURL : data.results.galURL
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

		if(!data.error) { // Success!
			// Hide modal
			$addGalModal.modal('hide');
			// Switch view to show galleries
			$contentTypeFilter.val('gallery');
			events.emit('changeContentFilter', 'gallery');
			// Render template
			$tableBody.prepend(Mustache.render(pageListTemplate, data.results));
		} else { // Error!		
			if(data.hasOwnProperty('error_details')) {
				
				$galMsg.html(data.error_msg);
				$.each(data.error_details, function() {
					$galMsg.append(this.name+" : "+this.error+"<br>");
				});
				// Switch view to show galleries
				$contentTypeFilter.val('gallery');
				events.emit('changeContentFilter', 'gallery');
				// Render template
				$tableBody.prepend(Mustache.render(pageListTemplate, data.results));
			} else {
				// Display error and delete gallery
			}
		}
	}

/**
 * 
 * HELPER FUNCTIONS
 * 
 */
	function disableSubmit(file) {
		if($galleryDropzone.files.length === 0) {
			$submitGal.attr('disabled', 'disabled');
		}
	}

	function enableSubmit(file) {
		$submitGal.removeAttr('disabled');
	}

	function resetModal() {
		$galleryDropzone.removeAllFiles();
		$galNameInput.val('');
		$galProcessing.hide();
		disableSubmit();
		$galMsg.html('');
	}
});