var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var Dropzone = require('../libs/dropzone.js');
var _ = require('./utilityFunctions.js'); // helper functions

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
 	var $contentArea 	= $('#contentArea'),
	$addTab 				= $('a.addTab'),
	pageURL 				= _.getURL(isPost);

	var $addSSModal 	= $('#addSSModal'),
	$ssSelect 			= $addSSModal.find('#ssSelect'),
	$ssNameInput 		= $addSSModal.find('input#newSSName'),
	$submitSS 			= $addSSModal.find('button#submitNewSS'),
	$ssMsg 				= $addSSModal.find('#ssMsg'),
	$ssProgress 		= $addSSModal.find('#ssProgress'),
	$ssProcessing 		= $addSSModal.find('#ssLoading'),
	$dropzone 			= $addSSModal.find('.addSSDropzone'),
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
		url : baseURL + 'page/uploadGalImages/',
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
 		var galID = $ssSelect.val(),
 		url = pageURL + '/addSlideshow/' + galID + "/" + postID;
 		_.post(url, {}, submitExistingSuccess, submitError);
 	}

 	function submitExistingSuccess(data) {
 		$addSSModal.modal('hide');
		ssID = data.results.slideshowID;
		contentID = data.results.contentID;
		renderSlideshow(data.results);
		events.emit('contentAdded');
 	}

 	function submitNewSS() {
 		// Get user input
		var data = {
			'name' : $ssNameInput.val()
		};
		// Validate
		if(data.name.length < 1) {
			return _.error("You must enter a name!", $ssMsg, $ssNameInput);
		}
		// Post to server
		var url = pageURL + '/addSSgallery/' + postID;
		_.post(url, data, submitNewSuccess, submitError);
	}

	function submitNewSuccess(data) {
		ssID = data.results.ssID;
		contentID = data.results.contentID;
		$ssDropzone.options.params = {
			galID : data.results.galID,
			galURL : data.results.galURL
		};
		$ssProgress.show();
		$submitSS.attr('disabled', 'disabled');
		$ssDropzone.processQueue();
		events.emit('contentAdded');
	}

	function submitError(data) {
 		_.error(data.error_msg, $ssMsg, $ssSelect);
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

/**
 * 
 * HELPER FUNCTIONS
 * 
 */
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
 		$dropzone.removeClass('disabled');
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
 			$dropzone.addClass('disabled');
 			$submitSS.removeAttr('disabled');
 			$submitSS.attr('data-new', false);
 		} else {
 			// Enable new gal form
 			$ssNameInput.removeAttr('disabled');
 			$ssDropzone.enable();
 			$dropzone.removeClass('disabled');
 			$submitSS.attr('disabled', 'disabled');
 			$submitSS.attr('data-new', true);
 		}
 	}
});