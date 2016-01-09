var addContent = (function(){
	/**
	 * 
	 * CACHE DOM
	 * 
	 */
	var $contentList = $('#contentList'),
	$tableBody = $contentList.find('tbody'),
	$addTab = $('a.addTab');

	// Type specific elements

	// Add Page
	var $addPageModal = $('#addPageModal'),
	$pageNameInput = $addPageModal.find('input#newPageName'),
	$submitPage = $addPageModal.find('button#submitNewPage'),
	$pageMsg = $addPageModal.find('#pageMsg'),
	pageListTemplate = $('#pageListTemplate').text();

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
				if(!data.error) {
					// Success
					$pageNameInput.val("");
					$addPageModal.modal('hide');

					$tableBody.prepend(Mustache.render(pageListTemplate, data));
				} else {
					// Error
					$pageMsg.html("<p class='text-danger'>"+data.error_msg+"</p>");
					$pageNameInput.focus();
					clearMsg($pageMsg);
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
		if(type == 'page')
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