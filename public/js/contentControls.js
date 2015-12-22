var contentControls = (function() {
	var $contentArea = $('#contentArea');
	var pageURL = $('a#viewTab').attr('href');
	var $screenSizeUI = $contentArea.find('.screenSize');
	var currentScreen;

	// Config
	var minColWidth = 3;

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
		targetSize = $thisItem.find('select.targetSize').val(),
		targetWidthClass,
		targetOffsetClass,
		targetWidth,
		targetOffset,
		startingClasses = getClassArray($thisItem),
		controlHtml = $thisControls.html(),
		contentID = $thisControls.attr('id');

		updateScreenSizeUI();

		// Show resize controls
		$thisContent.hide();
		$thisResizeControls.show();
		$thisControls.html('');

		// Remove contentItem class
		var classList = $.grep(startingClasses, function(a) {
 			return a !== "contentItem";
 		});

		// Get target classes for current target size
		targetWidthClass = getTargetClass(classList, targetSize, 'width');
		targetOffsetClass = getTargetClass(classList, targetSize, 'offset');

		// Get current width and offset for target size
		targetWidth = getTarget(targetWidthClass, 'width');
		targetOffset = getTarget(targetOffsetClass, 'offset');

		// On target size change, update target classes
		$thisItem.on('change', '.targetSize', function() {
			targetSize = $(this).val();
			targetWidthClass = getTargetClass(classList, targetSize, 'width');
			targetOffsetClass = getTargetClass(classList, targetSize, 'offset');
			targetWidth = getTarget(targetWidthClass, 'width');
			targetOffset = getTarget(targetOffsetClass, 'offset');
		});

		// Increase Width
		$thisItem.on('click', '.increaseWidth', function() {
			var newClass, newWidth;
			if(targetWidth + targetOffset < 12){
				newWidth = targetWidth + 1;
				newClass = "col-"+targetSize+"-"+newWidth.toString();
				if(targetWidthClass.length > 0) {
					$thisItem.removeClass(targetWidthClass);
				}
				$thisItem.addClass(newClass);
				targetWidth = newWidth;
				targetWidthClass = newClass;
			}
		});

		// Decrease Width
		$thisItem.on('click', '.decreaseWidth', function() {
			var newClass, newWidth;
			if(targetWidth > minColWidth){
				newWidth = targetWidth - 1;
				newClass = "col-"+targetSize+"-"+newWidth.toString();
				if(targetWidthClass.length > 0) {
					$thisItem.removeClass(targetWidthClass);
				}
				$thisItem.addClass(newClass);
				targetWidth = newWidth;
				targetWidthClass = newClass;
			}
		});

		// Increase Offset
		$thisItem.on('click', '.increaseOffset', function() {
			var newClass, newOffset;
			if(targetOffset + targetWidth < 12){
				newOffset = targetOffset + 1;
				newClass = "col-"+targetSize+"-offset-"+newOffset.toString();
				if(targetOffsetClass.length > 0) {
					$thisItem.removeClass(targetOffsetClass);
				}
				$thisItem.addClass(newClass);
				targetOffset = newOffset;
				targetOffsetClass = newClass;
			}
		});

		// Decrease Offset
		$thisItem.on('click', '.decreaseOffset', function() {
			var newClass, newOffset;
			if(targetOffset > 0){
				newOffset = targetOffset - 1;
				newClass = "col-"+targetSize+"-offset-"+newOffset.toString();
				if(targetOffsetClass.length > 0) {
					$thisItem.removeClass(targetOffsetClass);
				}
				$thisItem.addClass(newClass);
				targetOffset = newOffset;
				targetOffsetClass = newClass;
			}
		});

		// Save button
		$thisItem.on('click', '.saveResize', function() {
			saveResize($thisItem, contentID, controlHtml);
		});

		// Bind Cancel Button
		$thisItem.on('click', '.cancelResize', function() {
			cancelResize($thisItem, controlHtml, startingClasses);
		});
	}

	function getTargetClass(classList, targetSize, type) {
		var targetClass;
		if(type == 'width') {
			targetClass = $.grep(classList, function(value) {
				return (value.indexOf(targetSize) > -1 && value.indexOf('offset') == -1);
			});
		} else if(type == 'offset') {
			targetClass = $.grep(classList, function(value) {
				return (value.indexOf(targetSize) > -1 && value.indexOf('offset') > -1);
			});
		}
		return targetClass.join("");
	}

	function getTarget(targetClass, type) {
		if(targetClass.length == 0 && type == 'width') {
			return 12;
		} else if (targetClass.length == 0 && type == 'offset') {
			return 0;
		}
		return Number(targetClass.match(/\d+/)[0]);
	}

	function saveResize(thisItem, contentID, controlHtml) {
		var classes = $.grep(getClassArray(thisItem), function(a) {
 			return a !== "contentItem";
 		}).join(" ");
 		$.ajax({
			type: 'POST',
			url: pageURL + '/saveResize/' + contentID,
			data: { classes : classes },
			dataType: 'json',
			success: function(data) {
				if(!data.error) {
					thisItem.find('.resizeContentControls').hide();
					thisItem.find('.content').show();
					thisItem.find('ul.contentControlMenu').html(controlHtml);
				}
			}
		});

	}

	function cancelResize(thisItem, controlHtml, startingClasses) {
		thisItem.removeClass().addClass(startingClasses.join(" "));
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
 		return selector.attr('class').split(/\s+/);
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