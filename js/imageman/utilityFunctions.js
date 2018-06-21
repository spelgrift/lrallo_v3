var $ = require('jquery');
require('./slideMan.js'); // Image slider

var utilityFunctions = (function() {

	var error = function(message, $msg, $input) {
		if($input !== undefined) {
			$input.focus();
		}
		$msg.html(message);
		clearMsg($msg);
		return false;
	};

	var clearMsg = function(selector, timeout) {
		if (timeout === undefined) {
			timeout = 4000;
		}
		setTimeout(function(){
			selector.fadeOut('slow', function() {
				selector.html('');
				selector.show();
			});
		}, timeout);
	};

	var post = function(url, data, success, error) {
		$.ajax({
			type: 'POST',
			url: url,
			data: data,
			dataType: 'json',
			success: function(data) {
				if(!data.error) {
					// Success
					success(data);
				} else {
					error(data);
				}
			}
		});
	};

	var getURL = function(isPost){
		if(isPost){
			return window.baseURL + blogURL;
		}
		if(window.baseURL === $('a#viewTab').attr('href')){
			return window.baseURL + 'page';
		} else {
			return $('a#viewTab').attr('href');
		}
	};

	var loadSlides = function($allSlideshows){

		$allSlideshows.each(function() {
			var $slideshow = $(this),
			galID = $slideshow.attr('data-gal-id'),
			$slides = $slideshow.find('.slides');

			if(isNaN(galID)){
				return;
			}

			$.ajax({
				url: baseURL + 'page/loadSlides/'+galID,
				success: function(data) {
					$(data).appendTo($slides).hide().fadeIn('300', function() {
						$slideshow.slideMan();
					});
				}
			});
		});
	};

	return {
		error : error,
		clearMsg : clearMsg,
		post : post,
		getURL : getURL,
		loadSlides : loadSlides
	};

})();

module.exports = utilityFunctions;