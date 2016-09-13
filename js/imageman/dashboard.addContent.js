var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var Dropzone = require('../libs/dropzone.js');

$(function() {
/**
 * 
 * CACHE DOM
 * 
 */
	var $contentList = $('#contentList'),
	$contentTypeFilter = $contentList.find('select#filterContentList'),
	$mainNav = $('#mainNav').children('ul.navbar-nav'),
	$tableBody = $contentList.find('tbody'),
	$addTab = $('a.addTab');

	// Type specific elements

	// Add Page
	var $addPageModal = $('#addPageModal'),
	$pageNameInput = $addPageModal.find('input#newPageName'),
	$submitPage = $addPageModal.find('button#submitNewPage'),
	$pageMsg = $addPageModal.find('#pageMsg'),
	pageListTemplate = $('#pageListTemplate').text();

	// Add Gallery
	var $addGalModal = $('#addGalModal'),
	$galNameInput = $addGalModal.find('input#newGalName'),
	galDZtemplate = $('#galDZTemplate').html(),
	$submitGal = $addGalModal.find('button#submitNewGal'),
	$galMsg = $addGalModal.find('#galleryMsg'),
	$galProgress = $addGalModal.find('#galleryProgress'),
	$galProcessing = $addGalModal.find('#galleryLoading');

	// Add Video
	var $addVideoModal = $('#addVideoModal'),
	$vidNameInput = $addVideoModal.find('input#newVideoName'),
	$vidNameMsg = $addVideoModal.find('#videoNameMsg'),
	$vidLinkInput = $addVideoModal.find('input#newVideoLink'),
	$vidLinkMsg = $addVideoModal.find('#videoLinkMsg'),
	$submitVid = $addVideoModal.find('#submitNewVideo');

	// Add Nav Link
	var $addNavLinkModal = $('#addNavLinkModal'),
	$navLinkNameInput = $addNavLinkModal.find('input#newNavName'),
	$navLinkUrlInput = $addNavLinkModal.find('input#newNavUrl'),
	$submitNavLink = $addNavLinkModal.find('button#submitNewNavLink'),
	$navLinkMsg = $addNavLinkModal.find('#navLinkMsg');

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
	// Display modal based on which type is clicked
	$addTab.click(selectModal);

	// Submit Page
	$submitPage.click(submitPage);

	// Submit Nav Link
	$submitNavLink.click(submitNavLink);

	// Submit Video
	$submitVid.click(submitVideo);

	// Submit Gallery
	$submitGal.click(submitGal);

	// Handle Gallery Dropzone success
	$galleryDropzone.on("successmultiple", galleryDZsuccess);

	// Update total progress bar
	$galleryDropzone.on('totaluploadprogress', galleryDZprogress);

	// Enable Submit button when file added
	$galleryDropzone.on("addedfile", function(file) {
		$submitGal.removeAttr('disabled');
	});

	// Disable Submit button when no files
	$galleryDropzone.on("removedfile", function(file) {
		if($galleryDropzone.files.length === 0) {
			$submitGal.attr('disabled', 'disabled');
		}
	});

	// Remove files when modal closed
	$addGalModal.on('hidden.bs.modal', function() {
		$galleryDropzone.removeAllFiles();
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
			return error("<p class='text-danger'>You must enter a name!</p>", $pageMsg, $pageNameInput);
		}
		// Post to server
		$.ajax({
			type: 'POST',
			url: baseURL + 'dashboard/addPage',
			data: { name : pageName },
			dataType: 'json',
			success: function( data ) {
				if(!data.error) { // Success
					$pageNameInput.val("");
					$addPageModal.modal('hide');
					if($tableBody.find('tr.placeholderRow').length > 0) {
						$tableBody.find('tr.placeholderRow').remove();
					}

					$tableBody.prepend(Mustache.render(pageListTemplate, data.results));
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
			url: baseURL + 'dashboard/addVideo',
			data: data,
			dataType: 'json',
			success: function(data) {
				if(!data.error) { // Success
					// Hide modal
					$addVideoModal.modal('hide');
					// Switch view to show videos
					$contentTypeFilter.val('video');
					events.emit('changeContentFilter', 'video');
					// Render template
					$tableBody.prepend(Mustache.render(pageListTemplate, data.results));
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
			url: baseURL + 'dashboard/addGallery',
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

	function submitNavLink(ev) {
		ev.preventDefault();
		// Get user input
		var navLinkName = $navLinkNameInput.val(),
		navLinkUrl = $navLinkUrlInput.val();
		// Validate
		if(navLinkName.length < 1) {
			return error('You must enter a name', $navLinkMsg, $navLinkNameInput);
		}
		if(navLinkUrl.length < 1) {
			return error('You must enter a url', $navLinkMsg, $navLinkUrlInput);
		}
		// Post to server
		$.ajax({
			type: 'POST',
			url: baseURL + 'dashboard/addNavLink',
			data: { 
				name : navLinkName,
				url : navLinkUrl
			},
			dataType: 'json',
			success: function( data ) {
				if(!data.error) { // Success
					$navLinkNameInput.val("");
					$navLinkUrlInput.val("");
					$addNavLinkModal.modal('hide');
					reloadNav();	
				} else { // Error
					error(data.error_msg, $navLinkMsg, $navLinkNameInput);
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
		switch($(this).attr('data-id')) {
			case 'page' :
				$addPageModal.modal('show');
				$pageNameInput.val('');
				$pageMsg.html('');
			break;
			case 'gallery' :
				$addGalModal.modal('show');
				$galNameInput.val('');
				$galMsg.html('');
			break;
			case 'video' :
				$addVideoModal.modal('show');
				$vidNameInput.val('');
				$vidLinkInput.val('');
			break;
			case 'navLink' :
				$addNavLinkModal.modal('show');
				$navLinkNameInput.val('');
				$navLinkUrlInput.val('');
				$navLinkMsg.html('');
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

	function reloadNav() {
		$mainNav.load(baseURL + 'dashboard/reloadNav', function() {
			events.emit('reloadNav');
		});
	}



});