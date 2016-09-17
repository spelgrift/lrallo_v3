var $ = require('jquery');
require('./slideMan.js'); // Image slider

$(function() {
	var $allSlideshows = $('.slideshow');

	$allSlideshows.each(function() {
		var $slideshow = $(this),
		galID = $slideshow.attr('data-gal-id'),
		$slides = $slideshow.find('.slides');

		$.ajax({
			url: baseURL + 'page/loadSlides/'+galID,
			success: function(data) {
				$(data).appendTo($slides).hide().fadeIn('300', function() {
					$slideshow.slideMan();
				});
			}
		});
	});

});