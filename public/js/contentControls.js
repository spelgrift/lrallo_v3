var contentControls = (function() {
	var $contentArea = $('#contentArea');
	var pageURL = $('a#viewTab').attr('href');
	var $screenSizeUI = $contentArea.find('.screenSize');
	var currentScreen;

	getScreenSize();


/*
 *
 * BIND EVENTS
 *
 */

	 // Show/hide controls on mouseover
	$contentArea.on({
		mouseenter: showControls,
		mouseleave: hideControls
	}, '.contentItem');

	// Update currentScreen on window resize
	$( window ).resize(function() {
		getScreenSize();
		updateScreenSizeUI();

	})

	// Trash Content
	$contentArea.on('click', 'a.trashContent', function(ev) {
		ev.preventDefault();
		var contentID = $(this).closest('.contentControlMenu').attr('id'),
		$thisItem = $(this).closest('.contentItem');

		if(confirm('Are you sure you want to trash this item?')){
			trashContent($thisItem, contentID);
		}
	});

	// Resize Content
	$contentArea.on('click', 'a.resizeContent', function(ev) {
		ev.preventDefault();
		initResize($(this).closest('.contentItem'));
	});

/*
 *
 * CORE FUNCTIONS
 *
 */

	function trashContent(thisItem, contentID) {
		$.ajax({
			type: 'DELETE',
			url: pageURL + '/trashContent/' + contentID,
			dataType: 'json',
			success: function(data) {
				if(!data.error) {
					thisItem.fadeOut(300, function() {
						$(this).remove();
					});
				}
			}
		});
	}

	function initResize(thisItem) {
		// Cache DOM for this block
		var $thisItem = thisItem,
		$thisContent = $thisItem.find('.content'),
		$thisControls = $thisItem.find('ul.contentControlMenu'),
		$thisResizeControls = $thisItem.find('.resizeContentControls'),
		controlHtml = $thisControls.html();

		updateScreenSizeUI();

		// Show resize controls
		$thisContent.hide();
		$thisResizeControls.show();
		$thisControls.html('');

		// Get current classes
		var classList = getClassArray($thisItem);
		console.log(classList);

		// Get starting width + offset for current screen size

		// Bind Cancel Button
		$thisItem.on('click', '.cancelResize', function() {
			cancelResize($thisItem, controlHtml);
		})
	}

	function cancelResize(thisItem, controlHtml) {
		thisItem.find('.resizeContentControls').hide();
		thisItem.find('.content').show();
		thisItem.find('ul.contentControlMenu').html(controlHtml);
	}

/*
 *
 * UTILITY FUNCTIONS
 *
 */

 	function getClassArray(selector) {
 		var classList = selector.attr('class').split(/\s+/);
 		classList = $.grep(classList, function( a ) {
 			return a !== "contentItem";
 		})
 		return classList;
 	}

 	function getScreenSize() {
 		var currentWidth = window.innerWidth;
 		if(currentWidth < 768) {
 			currentScreen = 'xs';
 		} else if(currentWidth > 768 && currentWidth < 992) {
 			currentScreen = 'sm';
 		} else if(currentWidth > 992 && currentWidth < 1200) {
 			currentScreen = 'md';
 		} else if(currentWidth > 1200) {
 			currentScreen = 'lg';
 		}
 	}

 	function updateScreenSizeUI() {
 		if(currentScreen === 'xs') {
 			$screenSizeUI.html('Mobile');
 		} else if(currentScreen === 'sm') {
 			$screenSizeUI.html('Tablet');
 		} else if(currentScreen === 'md') {
 			$screenSizeUI.html('Desktop');
 		} else if(currentScreen === 'lg') {
 			$screenSizeUI.html('Large Desktop');
 		}
 	}

	function showControls() {
		$(this).find('ul.contentControlMenu').stop(false,true).fadeIn('fast');
	}

	function hideControls() {
		$(this).find('ul.contentControlMenu').stop(false,true).fadeOut('fast');
	}


})();