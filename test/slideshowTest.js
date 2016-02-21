$(function() {

	// Variablels and DOM references
	var speed = 500,
	slideDuration = 3000,
	autoSlideshow = true,
	animationType = 'slide',
	$next = $('.arrow-right'),
	$prev = $('.arrow-left'),
	$slideshow = $('.slideshow'),
	$slides = $slideshow.find('.slides'),
	maxSlide = $slides.children().length - 1,
	$currSlide = $slides.find('.slide.active'),
	currSlide = parseInt($currSlide.attr('data-order')),
	throttle = false,
	slideTimeout;

	// Bind Controls
	$next.click(function() {
		changeSlide(currSlide + 1);
		autoSlideshow = false;
	});

	$prev.click(function() {
		changeSlide(currSlide - 1);
		autoSlideshow = false;
	});

	// If auto, set new timeout
	if(autoSlideshow) slideTimeout = setTimeout(nextSlide, slideDuration);

	// Functionality
	function changeSlide(newSlide) {
		// Make sure an animation is not in progress
		if(throttle) return;
		throttle = true;

		// Clear any timeout
		clearTimeout(slideTimeout);

		if(newSlide > currSlide) {
			var direction = 'fromRight';
		} else {
			var direction = 'fromLeft';
		}

		// Make sure newSlide is not too high or too low
		if (newSlide > maxSlide) {
			newSlide = 0;
		} else if (newSlide < 0) {
			newSlide = maxSlide;
		}

		// Select transition classes based on animationType
		switch(animationType) {
			case 'slide' :
				var transitionClass = 'transition-slide-'+direction,
				queuedClass = 'queued-slide-'+direction;
				break;
			case 'fade' :
				var transitionClass = 'transition-fade',
				queuedClass = 'queued-fade';
				break;
		}

		// Save copies of $currSlide and $newSlide to this function instance
		var $_newSlide = $slides.find("[data-order='"+newSlide+"']"),
		$_currSlide = $currSlide;

		// Queue the new slide (move to the right of frame)
		$_newSlide.addClass(queuedClass);

		// Set delay to allow slide to queue
		setTimeout(function() {
			// Set CSS duration
			addCSSTransition();
			// Set currSlide to transition
			$_currSlide.addClass(transitionClass).removeClass('active');
			$_newSlide.addClass('active').removeClass(queuedClass);
		}, 10);

		// After animation finishes remove transition class from currSlide and reset CSS duration
		setTimeout(function(){
			removeCSSTransition();
			$_currSlide.removeClass(transitionClass);
			throttle = false;
			if(autoSlideshow) slideTimeout = setTimeout(nextSlide, slideDuration);
		}, 10 + speed);

		// Update currSlide selector and value
		$currSlide = $_newSlide;
		currSlide = newSlide;	
	}

	function nextSlide() {
		changeSlide(currSlide + 1);
	}

	function addCSSTransition() {
		$slides.find('.slide').each(function(){
			var slide = this;
			slide.style['transitionDuration'] = speed+'ms';
			switch(animationType) {
				case 'slide' :
					slide.style['transitionProperty'] = 'transform';
					break;
				case 'fade' :
					slide.style['transitionProperty'] = 'opacity';
					break;
			}
		});
	}

	function removeCSSTransition() {
		$slides.find('.slide').each(function(){
			this.style['transitionDuration'] = '';
			this.style['transitionProperty'] = '';
		});
	}
});