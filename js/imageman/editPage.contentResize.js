var $ = require('jquery');
var _ = require('./utilityFunctions.js'); // helper functions

$(function() {

	var $contentArea = $('#contentArea'),
	pageURL = baseURL+"page",
	currentScreen;

	// Config
	var minColWidth = 3,
	defaultClasses = 'contentItem editContent col-xs-12',
	resizeControlsHeight = '171px';

	getScreenSize();

/*
 *
 * BIND EVENTS
 *
 */

 	// Update currentScreen on window resize
	$( window ).resize(function() {
		getScreenSize();
		updateScreenSizeUI();
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

	function initResize($thisItem) {
		// Cache DOM for this block
		var $thisContent = $thisItem.find('.content'),
		$thisControls = $thisItem.find('ul.contentControlMenu'),
		$thisResizeControls = $thisItem.find('.resizeContentControls'),
		targetSize = $thisItem.find('select.targetSize').val(),
		target,
		startingClasses = getClassArray($thisItem),
		controlHtml = $thisControls.html(),
		contentID = $thisControls.attr('id');

		$thisItem.css('min-height', resizeControlsHeight);
		updateScreenSizeUI();

		// Show resize controls and hide content but maintain space on the page
		$thisContent.css('visibility', 'hidden');
		$thisResizeControls.show();
		$thisControls.html('');

		target = buildTarget($thisItem, targetSize);

		// On target size change, update target object with classes and values
		$thisItem.on('change', '.targetSize', function() {
			targetSize = $(this).val();
			target = buildTarget($thisItem, targetSize);
		});

		// Increase Width
		$thisItem.on('click', '.increaseWidth', function() {
			var newClass, newWidth;
			if(target.width + target.offset < 12){
				newWidth = target.width + 1;
				newClass = "col-"+targetSize+"-"+newWidth.toString();
				if(target.widthClass.length > 0) {
					$thisItem.removeClass(target.widthClass);
				}
				$thisItem.addClass(newClass);
				target.width = newWidth;
				target.widthClass = newClass;
			}
		});

		// Decrease Width
		$thisItem.on('click', '.decreaseWidth', function() {
			var newClass, newWidth;
			if(target.width > minColWidth){
				newWidth = target.width - 1;
				newClass = "col-"+targetSize+"-"+newWidth.toString();
				if(target.widthClass.length > 0) {
					$thisItem.removeClass(target.widthClass);
				}
				$thisItem.addClass(newClass);
				target.width = newWidth;
				target.widthClass = newClass;
			}
		});

		// Increase Offset
		$thisItem.on('click', '.increaseOffset', function() {
			var newClass, newOffset;
			if(target.offset + target.width < 12){
				newOffset = target.offset + 1;
				newClass = "col-"+targetSize+"-offset-"+newOffset.toString();
				if(target.offsetClass.length > 0) {
					$thisItem.removeClass(target.offsetClass);
				}
				$thisItem.addClass(newClass);
				target.offset = newOffset;
				target.offsetClass = newClass;
			}
		});

		// Decrease Offset
		$thisItem.on('click', '.decreaseOffset', function() {
			var newClass, newOffset;
			if(target.offset > 0){
				newOffset = target.offset - 1;
				newClass = "col-"+targetSize+"-offset-"+newOffset.toString();
				if(target.offsetClass.length > 0) {
					$thisItem.removeClass(target.offsetClass);
				}
				$thisItem.addClass(newClass);
				target.offset = newOffset;
				target.offsetClass = newClass;
			}
		});

		// Reset
		$thisItem.on('click', '.resetBlock', function() {
			$thisItem.removeClass().addClass(defaultClasses);
			targetSize = $thisItem.find('select.targetSize').val();
			target = buildTarget($thisItem, targetSize);
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

	function saveResize(thisItem, contentID, controlHtml) {
		var classes = $.grep(getClassArray(thisItem), function(a) {
 			return (a !== "contentItem" && a !== "editContent");
 		}).join(" ");
 		$.ajax({
			type: 'POST',
			url: pageURL + '/saveResize/' + contentID,
			data: { classes : classes },
			dataType: 'json',
			success: function(data) {
				if(!data.error) {
					thisItem.find('.resizeContentControls').hide();
					// thisItem.find('.content').show();
					thisItem.find('.content').css('visibility', '');
					thisItem.find('ul.contentControlMenu').html(controlHtml);
					thisItem.css('min-height', '');
				}
			}
		});

	}

	function cancelResize(thisItem, controlHtml, startingClasses) {
		thisItem.removeClass().addClass(startingClasses.join(" "));
		thisItem.find('.resizeContentControls').hide();
		thisItem.find('.content').css('visibility', '');
		thisItem.find('ul.contentControlMenu').html(controlHtml);
		thisItem.css('min-height', '');
	}

/*
 *
 * UTILITY FUNCTIONS
 *
 */

 	function getClassArray(selector) {
 		return selector.attr('class').split(/\s+/);
 	}

 	function buildTarget($thisItem, targetSize) {
		var testWidthClass,
		testOffsetClass,
		testSize,
		target = {},
		classList = $.grep(getClassArray($thisItem), function(a) {
			return (a !== "contentItem" && a !== "editContent");
		});

		// Get target classes for current target size
		target.widthClass = getTargetClass(classList, targetSize, 'width');
		target.offsetClass = getTargetClass(classList, targetSize, 'offset');

		// Get current width and offset (int) for target size
		// If no class yet, get width/offset of the next size down until target is found. This is messy :(
		if(target.widthClass.length > 0) {
			target.width = getTarget(target.widthClass, 'width');
		} else {
			switch(targetSize) {
				case "lg":
					testSize = "md";
					testWidthClass = getTargetClass(classList, testSize, 'width');
					if(testWidthClass.length === 0) {
						testSize = "sm";
						testWidthClass = getTargetClass(classList, testSize, 'width');
						if(testWidthClass.length === 0) {
							testSize = "xs";
							testWidthClass = getTargetClass(classList, testSize, 'width');
							break;
						} else {
							break;
						}
					} 
					break;
				case "md":
					testSize = "sm";
					testWidthClass = getTargetClass(classList, testSize, 'width');
					if(testWidthClass.length === 0) {
						testSize = "xs";
						testWidthClass = getTargetClass(classList, testSize, 'width');
						break;
					}
					break;
				case "sm":
					testSize = "xs";
					testWidthClass = getTargetClass(classList, testSize, 'width');
					break;
			}
			target.width = getTarget(testWidthClass, 'width');
		}

		if(target.offsetClass.length > 0) {
			target.offset = getTarget(target.offsetClass, 'offset');
		} else {
			switch(targetSize) {
				case "lg":
					testSize = "md";
					testOffsetClass = getTargetClass(classList, testSize, 'offset');
					if(testOffsetClass.length === 0) {
						testSize = "sm";
						testOffsetClass = getTargetClass(classList, testSize, 'offset');
						if(testOffsetClass.length === 0) {
							testSize = "xs";
							testOffsetClass = getTargetClass(classList, testSize, 'offset');
							break;
						} else {
							break;
						}
					} 
					break;
				case "md":
					testSize = "sm";
					testOffsetClass = getTargetClass(classList, testSize, 'offset');
					if(testOffsetClass.length === 0) {
						testSize = "xs";
						testOffsetClass = getTargetClass(classList, testSize, 'offset');
						break;
					}
					break;
				case "sm":
					testSize = "xs";
					testOffsetClass = getTargetClass(classList, testSize, 'offset');
					break;
				case "xs":
					testOffsetClass = target.offsetClass;
					break;
			}
			target.offset = getTarget(testOffsetClass, 'offset');
		}

		// console.log(target);

		return target;
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
		if(targetClass.length === 0 && type == 'width') {
			return 12;
		} else if (targetClass.length === 0 && type == 'offset') {
			return 0;
		}
		return Number(targetClass.match(/\d+/)[0]);
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
 			$contentArea.find('.screenSize').html('Mobile');
 		} else if(currentScreen === 'sm') {
 			$contentArea.find('.screenSize').html('Tablet');
 		} else if(currentScreen === 'md') {
 			$contentArea.find('.screenSize').html('Desktop');
 		} else if(currentScreen === 'lg') {
 			$contentArea.find('.screenSize').html('Large Desktop');
 		}
 	}

});