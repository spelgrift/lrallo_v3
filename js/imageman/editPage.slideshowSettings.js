var $ = require('jquery');
var _ = require('./utilityFunctions.js'); // helper functions
require('./slideMan.js'); // Image slider

// Define DOM attribute names to update settings
var autoClass 		= 'sm-auto',
controlsClass		= 'sm-hide-controls',
fadeClass 			= 'sm-fade',
speedAttr 			= 'data-sm-speed',
durationAttr 		= 'data-sm-duration',
aspectClassPrefix	= 'sm-aspect-';

$(function() {
/*
*
* CACHE DOM
*
*/
	var $contentArea 			= $('#contentArea'),
	$shortcutSettingsModal 	= $('#slideshowSettingsModal'),
	$ssControlsCheck 			= $shortcutSettingsModal.find('#ssControlsCheck'),
	$ssAutoPlayCheck 			= $shortcutSettingsModal.find('#ssAutoPlayCheck'),
	$ssDurationInput			= $shortcutSettingsModal.find('#ssDurationInput'),
	$ssAnimationSelect		= $shortcutSettingsModal.find('#ssAnimationSelect'),
	$ssSpeedInput				= $shortcutSettingsModal.find('#ssSpeedInput'),
	$ssAspectSelect			= $shortcutSettingsModal.find('#ssAspectSelect'),
	$saveButton					= $shortcutSettingsModal.find('#saveSSSettings'),
	$ssDurationMsg				= $shortcutSettingsModal.find('#ssDurationMsg'),
	$ssSpeedMsg					= $shortcutSettingsModal.find('#ssSpeedMsg'),
	pageURL 						= baseURL + 'page',
	$thisSS,
	oldAspectClass;

/*
*
* BIND EVENTS
*
*/
	$contentArea.on('click', '.slideshowSettings', loadSettingsModal);
	// Validate
	$ssDurationInput.keyup(validateDigit);
	$ssSpeedInput.keyup(validateDigit);
	// Checkbox behavior
	$ssControlsCheck.on('change', handleCheck);
	$ssAutoPlayCheck.on('change', handleCheck);
	$saveButton.click(saveSettings);

/*
 *
 * CORE FUNCTIONS
 *
 */
 	function saveSettings(ev) {
 		ev.preventDefault();
 		var contentID = $(this).attr('data-id'),
 		data = {
 			'animationSpeed' 	: $ssSpeedInput.val(),
 			'slideDuration' 	: $ssDurationInput.val(),
 			'animationType' 	: $ssAnimationSelect.val(),
 			'autoplay' 			: $ssAutoPlayCheck.prop('checked') ? '1' : '0',
 			'hideControls'		: $ssControlsCheck.prop('checked') ? '1' : '0',
 			'aspectRatio'		: $ssAspectSelect.val()
 		};
 		// if(data.animationSpeed.length < 1) {
 		// 	_.error('Cannot be blank', $ssSpeedMsg, $ssSpeedInput);
 		// 	return false;
 		// }
 		if(data.slideDuration.length < 1) {
 			return _.error('Cannot be blank', $ssDurationMsg, $ssDurationInput);
 		}
 		var url = pageURL + '/updateSlideshow/' + contentID;
 		_.post(url, data, saveSuccess, saveError);
 	}

 	function saveSuccess(data) {
 		$shortcutSettingsModal.modal('hide');
 		refreshSS(data.results);
 	}

 	function saveError(data) {
 		console.log(data);
 	}

 	function refreshSS(settings) {
 		$thisSS.attr(speedAttr, settings.animationSpeed);
 		$thisSS.attr(durationAttr, settings.slideDuration);
 		if(settings.animationType === 'fade') {
 			$thisSS.removeClass(fadeClass).addClass(fadeClass);
 		} else {
 			$thisSS.removeClass(fadeClass);
 		}
 		if(settings.autoplay === '1') {
 			$thisSS.removeClass(autoClass).addClass(autoClass);
 		} else {
 			$thisSS.removeClass(autoClass);
 		}
 		if(settings.hideControls === '1') {
 			$thisSS.removeClass(controlsClass).addClass(controlsClass);
 		} else {
 			$thisSS.removeClass(controlsClass);
 		}
 		$thisSS.removeClass(oldAspectClass).addClass(aspectClassPrefix+settings.aspectRatio);
 		$thisSS.slideMan().updateSettings();
 	}

 	function loadSettingsModal(ev) {
 		ev.preventDefault();
 		var contentID	 	= $(this).closest('.contentControlMenu').attr('id'),
 		$thisSlideshow 	= $(this).closest('.contentItem').find('.slideshow'),
 		origSettings		= {
 			'hideControls'		: $thisSlideshow.hasClass(controlsClass) ? true : false,
 			'animationSpeed'	: $thisSlideshow.attr(speedAttr),
 			'slideDuration'	: $thisSlideshow.attr(durationAttr),
 			'animationType'	: $thisSlideshow.hasClass(fadeClass) ? 'fade' : 'slide',
 			'autoplay'			: $thisSlideshow.hasClass(autoClass) ? true : false,
 			'aspect'				: getAspectRatio($thisSlideshow)
 		};
 		$thisSS = $thisSlideshow;
 		oldAspectClass = aspectClassPrefix+origSettings.aspect;
 		$ssControlsCheck.prop('checked', origSettings.hideControls);
 		$ssAutoPlayCheck.prop('checked', origSettings.autoplay);
 		$ssDurationInput.val(origSettings.slideDuration);
 		$ssAnimationSelect.val(origSettings.animationType);
 		$ssSpeedInput.val(origSettings.animationSpeed);
 		$ssAspectSelect.val(origSettings.aspect);

 		$saveButton.attr('data-id', contentID);

 		$shortcutSettingsModal.modal('show');
 	}

 	function handleCheck() {
 		// If hide controls is changed to true and autoplay is false, check autoplay
 		if($(this).attr('id') == 'ssControlsCheck'){
 			if($(this).prop('checked') && !$ssAutoPlayCheck.prop('checked')) {
 				$ssAutoPlayCheck.prop('checked', true);
 			}
 		} else {	// If autoplay is changed to false and hide controls is set to true, uncheck controls
 			if(!$(this).prop('checked') && $ssControlsCheck.prop('checked')) {
 				$ssControlsCheck.prop('checked', false);
 			}
 		}
 	}

 	
/*
 *
 * HELPER FUNCTIONS
 *
 */
 	function getAspectRatio($slideshow) {
 		var classList = $slideshow.attr('class').split(/\s+/),
 		aspect;
 		$.each(classList, function(index, item) {
 			if(item.includes(aspectClassPrefix)) {
 				aspect = item.replace(aspectClassPrefix, '');
 			}
 		});
 		return aspect;
 	}

 	function validateDigit(ev) {
 		var value = $(this).val(),
 		$thisInput = $(this),
 		$thisMsg = $thisInput.closest('.form-group').find('.error-block');

 		var isnum = /^\d+$/.test(value);
 		if(!isnum) {
 			_.error('You must enter a number', $thisMsg, $thisInput);
 			$thisInput.val('');
 		}
 	}
});