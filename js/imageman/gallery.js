var $ = require('jquery');
require('./slideMan.js'); // Image slider
require('../libs/flex-images.js'); // Collage view
require('../libs/jquery.mobile.custom.min.js'); // Touch functionality
$(function() {
	// Config
	var animateCollage = false;
	var rowHeight = 450;
	var fadeInOutTime = 200;

	// Cache DOM
	var $viewer= $('#viewer'),
	$slideshow = $viewer.find('.slideshow'),
	$next = $slideshow.find('.arrow-right'),
	$prev = $slideshow.find('.arrow-left'),
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

	// Keyboard events
	$(document).keydown(keyboardHandler);

	// Touch events
	$slideshow.on('swipeleft', nextSlide);
	$slideshow.on('swiperight', prevSlide);

	// Functions

	function nextSlide(){
		if($viewer.hasClass('active')){
			$next.click();
		}	
	}

	function prevSlide(){
		if($viewer.hasClass('active')){
			$prev.click();
		}	
	}

	function updateCaption() {
		var $activeSlide = $slideshow.find('.slide.active'),
		caption = $activeSlide.find('img').attr('title');
		$caption.html(caption);
	}

	function showThumbs(ev) {
		ev.preventDefault();
		fadeToPanel($viewer, $thumbnails, function(){});
	}

	function showCollage(ev) {
		ev.preventDefault();
		fadeToPanel($viewer, $collage, function(){
			initFlex();
			if(animateCollage) {
				$collageImages.hide();
				$collageImages.each(function(i) {
					$(this).delay(i * 100).fadeIn(500);
				});
			}
		});
	}

	function goToSlide(ev) {
		ev.preventDefault();
		var slide = $(ev.target).closest('.thumb').attr('data-slide'),
		$activePanel = $('.tabPanel.active');
		$slideshow.slideMan().changeSlide(slide, false);

		fadeToPanel($activePanel, $viewer, function(){});
	}

	function fadeToPanel($current, $new, callback) {
		$current.fadeOut(fadeInOutTime, function(){
			$current.removeClass('active').removeAttr('style');
			$new.fadeIn(fadeInOutTime, function(){
				$new.addClass('active').removeAttr('style');
				callback();
			});
		});
	}

	function initFlex() {
		$collage.flexImages({rowHeight: rowHeight});
	}

	function keyboardHandler(ev){
		if(ev.keyCode === 39) {
			nextSlide();
		}
		if(ev.keyCode === 37) {
			prevSlide();
		}
	}

});