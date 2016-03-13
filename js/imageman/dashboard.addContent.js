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

	// Add Nav Link
	var $addNavLinkModal = $('#addNavLinkModal'),
	$navLinkNameInput = $addNavLinkModal.find('input#newNavName'),
	$navLinkUrlInput = $addNavLinkModal.find('input#newNavUrl'),
	$submitNavLink = $addNavLinkModal.find('button#submitNewNavLink'),
	$navLinkMsg = $addNavLinkModal.find('#navLinkMsg');

	/**
	 * 
	 * BIND EVENTS
	 * 
	 */

	// Display modal based on which type is clicked
	$addTab.on('click', function(ev) {
		selectModal($(this).attr('data-id'));
		ev.preventDefault();
	});

	// Submit Page
	$submitPage.on('click', function(ev) {
		submitPage();
		ev.preventDefault();
	});

	// Submit Page
	$submitGal.on('click', function(ev) {
		submitGal();
		ev.preventDefault();
	});

	// Submit Nav Link
	$submitNavLink.on('click', function(ev) {
		submitNavLink();
		ev.preventDefault();
	});

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

	// Handle Dropzone success
	$galleryDropzone.on("successmultiple", function(files, data) {
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
				
				$galMsg.html("<p class='text-danger'>"+data.error_msg+"</p>");
				$.each(data.error_details, function() {
					$galMsg.append("<p class='text-danger'>"+this.name+" : "+this.error+"</p>");
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
	});

	// Update total progress bar
	$galleryDropzone.on('totaluploadprogress', function(progress) {
		if(progress < 100) {
			$galProgress.find('.progress-bar').css('width', progress + '%');
		} else {
			$galProgress.hide().find('.progress-bar').css('width', '0%');
			$galProcessing.show();
		}
	});

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
	 * ADD CONTENT FUNCTIONS
	 * 
	 */
	function submitPage() {
		// Get user input
		var pageName = $pageNameInput.val();
		// Validate
		if(pageName.length < 1) {
			$pageMsg.html("<p class='text-danger'>You must enter a name!</p>");
			$pageNameInput.focus();
			clearMsg($pageMsg);
			return false;
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
					$pageMsg.html("<p class='text-danger'>"+data.error_msg+"</p>");
					$pageNameInput.focus();
					clearMsg($pageMsg);
				}
			}
		});
	}

	function submitGal() {
		// Get user input
		var galName = $galNameInput.val();
		// Validate
		if(galName.length < 1) {
			$galMsg.html("<p class='text-danger'>You must enter a name!</p>");
			$galNameInput.focus();
			clearMsg($galMsg);
			return false;
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
					$galMsg.html("<p class='text-danger'>"+data.error_msg+"</p>");
					$galNameInput.focus();
					clearMsg($galMsg);
				}
			}
		});
	}

	function submitNavLink() {
		// Get user input
		var navLinkName = $navLinkNameInput.val(),
		navLinkUrl = $navLinkUrlInput.val();
		// Validate
		if(navLinkName.length < 1) {
			$navLinkMsg.html("<p class='text-danger'>You must enter a name.</p>");
			$navLinkNameInput.focus();
			clearMsg($navLinkMsg);
			return false;
		}
		if(navLinkUrl.length < 1) {
			$navLinkMsg.html("<p class='text-danger'>You must enter a url</p>");
			$navLinkUrlInput.focus();
			clearMsg($navLinkMsg);
			return false;
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
					$navLinkMsg.html("<p class='text-danger'>"+data.error_msg+"</p>");
					$navLinkNameInput.focus();
					clearMsg($navLinkMsg);
				}
			}
		});
	}

	/**
	 * 
	 * HELPER FUNCTIONS
	 * 
	 */
	function selectModal(type) {
		switch(type) {
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
			case 'navLink' :
				$addNavLinkModal.modal('show');
				$navLinkNameInput.val('');
				$navLinkUrlInput.val('');
				$navLinkMsg.html('');
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

	function reloadNav() {
		$mainNav.load(baseURL + 'dashboard/reloadNav', function() {
			events.emit('reloadNav');
		});
	}
});