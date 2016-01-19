var addContent = (function(){
	/**
	 * 
	 * CACHE DOM
	 * 
	 */
	var $contentList = $('#contentList'),
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

	// Submit Nav Link
	$submitNavLink.on('click', function(ev) {
		submitNavLink();
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
				if(!data.error) { // Success
					$pageNameInput.val("");
					$addPageModal.modal('hide');
					if($tableBody.find('tr.placeholderRow').length > 0) {
						$tableBody.find('tr.placeholderRow').remove();
					}

					$tableBody.prepend(Mustache.render(pageListTemplate, data));
				} else { // Error
					$pageMsg.html("<p class='text-danger'>"+data.error_msg+"</p>");
					$pageNameInput.focus();
					clearMsg($pageMsg);
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
			break;

			case 'navLink' :
				$addNavLinkModal.modal('show');
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


})();