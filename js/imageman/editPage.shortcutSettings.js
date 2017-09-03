var $ = require('jquery');
var Mustache = require('../libs/mustache.min.js');
var Dropzone = require('../libs/dropzone.js');
var _ = require('./utilityFunctions.js'); // helper functions
$(function() {
/*
*
* CACHE DOM
*
*/
	var $contentArea = $('#contentArea'),
	$shortcutSettingsModal = $('#shortcutSettingsModal'),
	shortcutCoverDZTemplate = $('#shortcutCoverDZTemplate').html(),
	$nameInput = $shortcutSettingsModal.find('#inputShortcutName'),
	$saveButton = $shortcutSettingsModal.find('#saveShortcutSettings'),
	$nameMsg = $shortcutSettingsModal.find('#shortcutNameMsg'),
	$coverMsg = $shortcutSettingsModal.find('#shortcutCoverMsg'),
	coverTemplate = $('#galleryTemplate').find('a.coverLink').prop('outerHTML'),
	origName,
	$thisBlock,
	pageURL = _.getURL();

/*
*
* BIND EVENTS
*
*/
	$contentArea.on('click', '.shortcutSettings', loadSettingsModal);	
	$saveButton.click(saveSettings);

/*
 *
 * CORE FUNCTIONS
 *
 */
 	function saveSettings(ev) {
 		ev.preventDefault();
 		// Get user input/target contentID
 		var contentID = $(this).attr('data-id'),
 		data = {
 			name : $nameInput.val(),
 			type : $(this).attr('data-type')
 		};
 		// Check if user entered name
 		if(data.name.length < 1) {
 			return _.error('Name cannot be blank', $nameMsg, $nameInput);
 		}
 		// Check if name is same as original
 		if(data.name != origName) {
 			// Update shortcut name
 			var url = pageURL + '/updateShortcut/' + contentID;
 			_.post(url, data, saveSuccess, saveError);
 		} else {
 			// If no new cover image return...
 			if($updateCoverDropzone.files.length < 1) {
				$shortcutSettingsModal.modal('hide');
				return;
			}
			// ...otherwise, process Dropzone queue
 			$updateCoverDropzone.processQueue();
 		}
 	}

 	function saveSuccess(data) {
		// If no new cover image return
		if($updateCoverDropzone.files.length === 0) {
			console.log('success');
			$thisBlock.find('a.shortcutTitleOverlay').html(data.results.name);
			$shortcutSettingsModal.modal('hide');
			return;
		}
		// Process Dropzone queue
		$updateCoverDropzone.processQueue();
 	}

 	function saveError(data) {
 		_.error('Name cannot be blank', $nameMsg, $nameInput);
 	}

 	function dzSuccess(file, data) {
 		data = JSON.parse(data);
		if(!data.error) { // Success!
			// Get name and target from this block
			var target = $thisBlock.find('a.shortcutTitleOverlay').attr('href'),
			$oldCover = $thisBlock.find('a.coverLink');
			// Remove old cover
			if($oldCover.length > 0) {
				$oldCover.remove();
			}
			// Render template
			$thisBlock.find('.shortcut').prepend(Mustache.render(coverTemplate, data.results));
			// Fix target URL
			$thisBlock.find('a.coverLink').attr('href', target);
			// Hide modal
			$shortcutSettingsModal.modal('hide');
		} else { // Error!		
			$updateCoverDropzone.emit("error", file, data.error_msg);
		}
 	}

 	function loadSettingsModal(ev) {
 		ev.preventDefault();

 		var type = $(this).attr('data-type'),
 		contentID = $(this).attr('data-id');
 		$thisBlock = $(this).closest('.contentItem');
 		var name = $thisBlock.find('a.shortcutTitleOverlay').html();
 		origName = name;
 		$updateCoverDropzone.options.url = pageURL + '/updateShortcutCover/' + contentID + '/' + type;

 		$saveButton.attr('data-type', type).attr('data-id', contentID);
 		$nameInput.val(name);

 		$shortcutSettingsModal.modal('show');
 	}

/**
 * 
 * COVER DROPZONE
 * 
 */
	Dropzone.autoDiscover = false;

	var $updateCoverDropzone = new Dropzone('div.updateCoverDropzone', {
		url : pageURL + '/updateShortcutCover',
		autoProcessQueue : false,
		maxFiles : 1,
		maxFilesize : 3,
		thumbnailWidth : 125,
		thumbnailHeight : 125,
		previewTemplate : shortcutCoverDZTemplate,
		dictDefaultMessage : "Drop file here to update cover image"
	});

	// Remove file if more than 1 added
	$updateCoverDropzone.on("maxfilesexceeded", function(file) {
		this.removeFile(file);
	});

	// Remove files when modal closed
	$shortcutSettingsModal.on('hidden.bs.modal', function() {
		$updateCoverDropzone.removeAllFiles();
	});

	// Handle Dropzone success
	$updateCoverDropzone.on("success", dzSuccess);

/**
 * 
 * HELPER FUNCTIONS
 * 
 */
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