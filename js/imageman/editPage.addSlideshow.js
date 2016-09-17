var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var Dropzone = require('../libs/dropzone.js');
var _ = require('./functions.addContent.js'); // helper functions

$(function() {
/**
 * 
 * CACHE DOM
 * 
 */
 	var $contentArea 	= $('#contentArea'),
	$addTab 				= $('a.addTab'),
	pageURL 				= $('a#viewTab').attr('href');

	var $addSSModal 	= $('#addSSModal'),
	$ssSelect 			= $addSSModal.find('#ssSelect'),
	$ssNameInput 		= $addSSModal.find('input#newSSName'),
	$submitSS 			= $addSSModal.find('button#submitNewSS'),
	$ssMsg 				= $addSSModal.find('#ssMsg'),
	$ssProgress 		= $addSSModal.find('#ssProgress'),
	$ssProcessing 		= $addSSModal.find('#ssLoading'),
	DZtemplate 			= $('#galDZTemplate').html(),
	ssTemplate			= $('#slideshowTemplate').html();

	// Vars to hold the new slideshowID + contentID
	var ssID, contentID;

/**
 * 
 * DROPZONES
 * 
 */
	Dropzone.autoDiscover = false;

	var $ssDropzone = new Dropzone('div.addSSDropzone', {
		url : pageURL + '/uploadGalImages/',
		autoProcessQueue : false,
		uploadMultiple : true,
		parallelUploads: 50,
		maxFilesize : 3,
		acceptedFiles : "image/*,.jpg,.JPG",
		thumbnailWidth : 125,
		thumbnailHeight : 125,
		previewTemplate : DZtemplate,
		dictDefaultMessage : "Drop image files here<br>(or click)"
	});

/**
 * 
 * BIND EVENTS
 * 
 */
 	// Submit
 	$submitSS.click(handleSubmit);

 	// Handle DZ success
 	$ssDropzone.on("successmultiple", ssDZsuccess);

 	// Disable DZ when existing gal selected
	$ssSelect.change(changeSSselect);

	// Disable select when name entered
	$ssNameInput.keyup(changeSSname);

	// Disable select/enable submit when image added
	$ssDropzone.on('addedfile', addedFile);

	// Enable select/disable submit when no images
	$ssDropzone.on('removedfile', removedFile);

	// Reset modal when closed
	$addSSModal.on('hidden.bs.modal', resetModal);

	// Update total progress bar
	$ssDropzone.on('totaluploadprogress', ssDZprogress);

/**
 * 
 * CORE FUNCTIONS
 * 
 */
 	function submitExistingSS() {
 		// Get user input
 		var galID = $ssSelect.val();
 		// Post to server
 		$.ajax({
 			type: 'POST',
 			url: pageURL + '/addSlideshow/' + galID,
 			dataType: ['json'],
 			success: function( data ) {
 				if(!data.error) { // Success
					$addSSModal.modal('hide');
					ssID = data.results.slideshowID;
					contentID = data.results.contentID;
					renderSlideshow(data.results);
				} else { // Error!
					_.error(data.error_msg, $ssMsg, $ssSelect);
				}
 			}
 		});
 	}

 	function submitNewSS() {
 		// Get user input
		var ssName = $ssNameInput.val();
		// Validate
		if(ssName.length < 1) {
			return _.error("You must enter a name!", $ssMsg, $ssNameInput);
		}
		// Post to server
		$.ajax({
			type: 'POST',
			url: pageURL + '/addSSgallery',
			data: { name : ssName },
			dataType: 'json',
			success: function( data ) {
				if(!data.error) { // Success
					ssID = data.results.ssID;
					contentID = data.results.contentID;
					$ssDropzone.options.params = {
						galID : data.results.galID,
						galURL : data.results.galURL
					};
					$ssProgress.show();
					$submitSS.attr('disabled', 'disabled');
					$ssDropzone.processQueue();
				} else { // Error
					_.error(data.error_msg, $ssMsg, $ssNameInput);
				}
			}
		});
 	}

 	function ssDZsuccess(files, data) {
 		data = JSON.parse(data);

		$ssDropzone.removeAllFiles();
		$ssNameInput.val('');
		$ssProcessing.hide();

		if(!data.error) { // Success
			$addSSModal.modal('hide');
			renderSlideshow(data.results);
		} else { // Error!
			if(data.hasOwnProperty('error_details')) {
				$ssMsg.html(data.error_msg);
				$.each(data.error_details, function() {
					$ssMsg.append(this.name+" : "+this.error+"<br>");
				});
				renderSlideshow(data.results);
			}
		}
 	}

 	function ssDZprogress(progress) {
 		if(progress < 100) {
			$ssProgress.find('.progress-bar').css('width', progress + '%');
		} else {
			$ssProgress.hide().find('.progress-bar').css('width', '0%');
			$ssProcessing.show();
		}
 	}

 	function renderSlideshow(data) {
 		// Render template
		var newSSobject = {
			'galleryID': data.galleryID,
			'contentID': contentID,
			'slideshowID': ssID
		},
		$newSlideshow = $(Mustache.render(ssTemplate, newSSobject));
		$newSlideshow.prependTo($contentArea).hide();
		var $slides = $newSlideshow.find('.slides');
		// Load images
		$.ajax({
			url: baseURL + 'page/loadSlides/'+data.galleryID,
			success: function(data) {
				$(data).appendTo($slides);
				$newSlideshow.fadeIn('300', function() {
					$newSlideshow.slideMan();
				});
			}
		});
 	}

 	function handleSubmit(ev) {
 		ev.preventDefault();
 		if($(this).attr('data-new') == 'true') {
 			submitNewSS();
 		} else {
 			submitExistingSS();
 		}
 	}

 	function resetModal() {
 		$ssDropzone.removeAllFiles();
 		$ssNameInput.val('');
 		$ssSelect.val('0');
 		$ssSelect.removeAttr('disabled');
 		$ssNameInput.removeAttr('disabled');
 		$ssDropzone.enable();
 	}

 	function removedFile(file) {
 		if($ssDropzone.files.length === 0) {
 			$submitSS.attr('disabled', 'disabled');
			$ssProcessing.hide();
			if($ssNameInput.val() === '') {
				$ssSelect.removeAttr('disabled');
			}
 		}
 	}

 	function addedFile(file) {
 		$submitSS.removeAttr('disabled');
 		$ssSelect.attr('disabled', 'disabled');
 		$submitSS.attr('data-new', true);
 	}

 	function changeSSname() {
 		if($ssNameInput.val() !== '') {
 			// Name entered, disable select
 			$ssSelect.attr('disabled', 'disabled');
 			$submitSS.attr('data-new', true);
 		} else {
 			// Enable select
 			$ssSelect.removeAttr('disabled');
 		}
 	}

 	function changeSSselect() {
 		if($(this).val() !== '0') {
 			// Gallery selected
 			$ssNameInput.attr('disabled', 'disabled');
 			$ssDropzone.disable();
 			$submitSS.removeAttr('disabled');
 			$submitSS.attr('data-new', false);
 		} else {
 			// Enable new gal form
 			$ssNameInput.removeAttr('disabled');
 			$ssDropzone.enable();
 			$submitSS.attr('disabled', 'disabled');
 			$submitSS.attr('data-new', true);
 		}
 	}


});