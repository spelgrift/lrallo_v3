var $ = require('jquery');
require('./slideMan.js'); // Image slider
require('../libs/flex-images.js'); // Collage view

$(function() {
	// Config
	var animateCollage = false;

	// Cache DOM
	var $viewer= $('#viewer'),
	$slideshow = $viewer.find('.slideshow'),
	$caption = $viewer.find('#caption').find('p'),
	$thumbnails = $('#thumbnails'),
	$collage = $('#collage'),
	$collageImages = $collage.find('.collageImage'),
	$showThumbs = $slideshow.find('#showThumbs'),
	$showCollage = $slideshow.find('#showCollage'),
	$thumbs = $('.thumb');

	// Initilize 
	$slideshow.slideMan();
	initFlex();
	updateCaption();

	// Bind Events
	$showThumbs.click(showThumbs);
	$showCollage.click(showCollage);
	$thumbs.click(goToSlide);
	events.on('slideChanged', updateCaption);

	// Functions

	function updateCaption() {
		var $activeSlide = $slideshow.find('.slide.active'),
		caption = $activeSlide.find('img').attr('title');
		$caption.html(caption);
	}

	function showThumbs(ev) {
		ev.preventDefault();
		$viewer.removeClass('active');
		$thumbnails.addClass('active');
	}

	function showCollage(ev) {
		ev.preventDefault();
		$viewer.removeClass('active');
		$collage.addClass('active');
		initFlex();
		if(animateCollage) {
			$collageImages.hide();
			$collageImages.each(function(i) {
				$(this).delay(i * 100).fadeIn(500);
			});
		}
	}

	function goToSlide(ev) {
		ev.preventDefault();
		var slide = $(ev.target).closest('.thumb').attr('data-slide');
		$slideshow.slideMan().changeSlide(slide, false);
		$('.tabPanel.active').removeClass('active');
		$viewer.addClass('active');
	}

	function initFlex() {
		$collage.flexImages({rowHeight: 400});
	}

});