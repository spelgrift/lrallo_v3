var addContent = (function(){

	/**
	 * 
	 * CACHE DOM
	 * 
	 */
	var $addTab = $('a.addTab');
	var pageURL = $('a#viewTab').attr('href');

	// Type specific elements

	// Add Page
	var $addPageModal = $('#addPageModal');
	var $pageNameInput = $('input#newPageName');
	var $submitPage = $('button#submitNewPage');
	var $pageMsg = $('#pageMsg');

	// Add Text
	var $addTextModal = $('#addTextModal');

	/**
	 * 
	 * BIND EVENTS
	 * 
	 */
	$addTab.on('click', function(ev) {
		selectModal($(this).attr('data-id'));
		ev.preventDefault();
	});

	$submitPage.on('click', function(ev) {
		submitPage();
		ev.preventDefault();
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
			return false
		}
		// Check if taken here?

		// Post to server
		$.ajax({
			type: 'POST',
			url: pageURL + '/addPage',
			data: { name : pageName },
			success: function( data ) {
				if(data == "noName") {
					$pageMsg.html("<p class='text-danger'>You must enter a name!</p>");
					$pageNameInput.focus();
					clearMsg($pageMsg);
					return false
				}
				if(data == "nameExists") {
					$pageMsg.html("<p class='text-danger'>A page with that name already exists.</p>");
					$pageNameInput.focus();
					clearMsg($pageMsg);
					return false
				}
				if(data == "success") {
					$pageNameInput.val("");
					$addPageModal.modal('hide');
					// events.js - refresh content, etc.
				}
			},
			dataType: 'json'
		});
	}

	/**
	 * 
	 * HELPER FUNCTIONS
	 * 
	 */
	function selectModal(type) {
		if(type == 'text')
		{
			// Maybe skip this and add a new empty text block right onto the page?
			$addTextModal.modal('show');
		}
		else if(type == 'page')
		{
			$addPageModal.modal('show');
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


})();