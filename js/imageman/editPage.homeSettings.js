var $ = require('jquery');
var _ = require('./utilityFunctions.js'); // helper functions
$(function() {
/*
*
* CACHE DOM
*
*/
var $settings 		= $('#homeSettings'),
$homeTypeRadio		= $settings.find('#settingsHomeType'),
$homeTargetSelect	= $settings.find('#settingsHomeTargetSelect'),
$submit				= $settings.find('#homeSettingsSubmit'),
$msg					= $settings.find('#settingsMsg'),

$adminNav			= $('#adminNav'),
$layoutLi			= $adminNav.find('#layoutLi'),
$addLi				= $adminNav.find('#addLi'),

pageURL 				= _.getURL();

/**
 * 
 * BIND EVENTS
 * 
 */
 	// Enable select only if use-existing selected
 	$homeTypeRadio.change(enableSelect);

 	// Save settings
 	$submit.click(saveSettings);

/**
 * 
 * MAIN FUNCIONS
 * 
 */
 	function saveSettings(ev) {
 		ev.preventDefault();
 		var data = {
 			'type' : $homeTypeRadio.find("input[name='settingsHomeType']:checked").val(),
 			'target' : $homeTargetSelect.val()
 		},
 		url = pageURL + '/updateHomeSettings';
 		_.post(url, data, submitSuccess, submitError);
 	}

 	function submitSuccess(data) {
 		if(data.type == 'link') {
 			$layoutLi.addClass('hidden');
 			$addLi.addClass('hidden');
 		} else {
 			$layoutLi.removeClass('hidden');
 			$addLi.removeClass('hidden');
 		}
 		$msg.html("<p class='text-success'>Changes saved!</p>");
 		_.clearMsg($msg, 6000);
 	}

 	function submitError(data) {
		_.error("<p class='text-danger'>"+data.error_msg+"</p>", $msg);
	}

 	function enableSelect(){
 		var type = $homeTypeRadio.find("input[name='settingsHomeType']:checked").val();
 		if(type == 'link') {
 			$homeTargetSelect.prop('disabled', false);
 		} else {
 			$homeTargetSelect.prop('disabled', true);
 		}
 	}

});