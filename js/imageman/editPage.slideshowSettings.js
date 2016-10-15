var $ = require('jquery');
var _ = require('./functions.dialogError.js'); // helper functions
require('./slideMan.js'); // Image slider

// Define DOM attribute names to update settings
var autoClass 	= 'sm-auto',
fadeClass 		= 'sm-fade',
speedAttr 		= 'data-sm-speed',
durationAttr 	= 'data-sm-duration';

$(function() {
/*
*
* CACHE DOM
*
*/
	var $contentArea 			= $('#contentArea'),
	$shortcutSettingsModal 	= $('#slideshowSettingsModal'),
	$ssAutoPlayCheck 			= $shortcutSettingsModal.find('#ssAutoPlayCheck'),
	$ssDurationInput			= $shortcutSettingsModal.find('#ssDurationInput'),
	$ssAnimationSelect		= $shortcutSettingsModal.find('#ssAnimationSelect'),
	$ssSpeedInput				= $shortcutSettingsModal.find('#ssSpeedInput'),
	$saveButton					= $shortcutSettingsModal.find('#saveSSSettings'),
	$ssDurationMsg				= $shortcutSettingsModal.find('#ssDurationMsg'),
	$ssSpeedMsg					= $shortcutSettingsModal.find('#ssSpeedMsg'),
	pageURL 						= $('a#viewTab').attr('href'),
	$thisSS;

/*
*
* BIND EVENTS
*
*/
	$contentArea.on('click', '.slideshowSettings', loadSettingsModal);
	$ssDurationInput.keyup(validateDigit);
	$ssSpeedInput.keyup(validateDigit);
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
 			'autoplay' 			: $ssAutoPlayCheck.prop('checked') ? '1' : '0'
 		};
 		// if(data.animationSpeed.length < 1) {
 		// 	_.error('Cannot be blank', $ssSpeedMsg, $ssSpeedInput);
 		// 	return false;
 		// }
 		if(data.slideDuration.length < 1) {
 			_.error('Cannot be blank', $ssDurationMsg, $ssDurationInput);
 			return false;
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
 		$thisSS.slideMan().updateSettings();
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

 	function loadSettingsModal(ev) {
 		ev.preventDefault();
 		var contentID	 	= $(this).closest('.contentControlMenu').attr('id'),
 		$thisSlideshow 	= $(this).closest('.contentItem').find('.slideshow'),
 		origSettings		= {
 			'animationSpeed'	: $thisSlideshow.attr(speedAttr),
 			'slideDuration'	: $thisSlideshow.attr(durationAttr),
 			'animationType'	: $thisSlideshow.hasClass(fadeClass) ? 'fade' : 'slide',
 			'autoplay'			: $thisSlideshow.hasClass(autoClass) ? true : false
 		};
 		$thisSS = $thisSlideshow;
 		$ssAutoPlayCheck.prop('checked', origSettings.autoplay);
 		$ssDurationInput.val(origSettings.slideDuration);
 		$ssAnimationSelect.val(origSettings.animationType);
 		$ssSpeedInput.val(origSettings.animationSpeed);

 		$saveButton.attr('data-id', contentID);

 		$shortcutSettingsModal.modal('show');
 	}


});