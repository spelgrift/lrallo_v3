var jQuery = require('jquery');
require('../libs/encapsulatedPlugin');

(function($) {

	// Define DOM attribute names to update settings
	var autoClass 	= 'sm-auto',
	fadeClass 		= 'sm-fade',
	speedAttr 		= 'data-sm-speed',
	durationAttr 	= 'data-sm-duration';

	// Test if browser supports a given CSS property
	var cssTest = function(property)
	{
		var Property = property.charAt(0).toUpperCase() + property.slice(1),
		props = [property, 'Webkit'+Property, 'Moz'+Property, 'O'+Property, 'ms'+Property],
		elem = document.createElement('modernizr');
		for(var i = 0; i < props.length; i++) {
			if(elem && elem.style[props[i]] !== undefined) {
				return props[i];
			}
		}
		return false;
	};

	// Plugin definition
	var slideMan = function(element, options)
	{
		// Set up plugin vars + DOM refs
		var $slideshow = $(element),
		obj = this,
		defaults = {
			speed : 500,
			slideDuration : 3000,
			autoSlideshow : false,
			animationType : 'slide'
		},
		settings = $.extend(defaults, options || {}),
		$next = $slideshow.find('.arrow-right'),
		$prev = $slideshow.find('.arrow-left'),
		$slides = $slideshow.find('.slides'),
		maxSlide = $slides.children().length - 1,
		$currSlide = $slides.find('.slide.active'),
		currSlide = parseInt($currSlide.attr('data-order')),
		slideTimeout,
		throttle = false,
		cssTransition = cssTest('transition'),
		cssTransform = cssTest('transform');

		// Change to next slide
		var nextSlide = function()
		{
			obj.changeSlide(currSlide + 1);
		};

		// If slideshow set to auto, set a timeout to go to next slide
		var activate = function() {
			if(settings.autoSlideshow) {
				slideTimeout = setTimeout(nextSlide, settings.slideDuration);
			}
		};

		// Get settings from DOM attributes (public method)
		this.updateSettings = function()
		{
			var _speed = $slideshow.attr(speedAttr),
			_duration = $slideshow.attr(durationAttr);
			settings.autoSlideshow = $slideshow.hasClass(autoClass) ? true : false;
			settings.animationType = $slideshow.hasClass(fadeClass) ? 'fade' : 'slide';
			if(typeof _speed !== typeof undefined && _speed !== false) {
				settings.speed = parseInt(_speed);
			}
			if(typeof _duration !== typeof undefined && _duration !== false) {
				settings.slideDuration = parseInt(_duration);
			}
			// Clear any timeout
			clearTimeout(slideTimeout);
			activate();
		};
		obj.updateSettings();

		// Bind Events
		$next.click(function() {
			obj.changeSlide(currSlide + 1);
			settings.autoSlideshow = false;
		});
		$prev.click(function() {
			obj.changeSlide(currSlide - 1);
			settings.autoSlideshow = false;
		});

		// Add CSS transition duration/proprety to slides - called right before slides are animated
		var addCSStransition = function()
		{
			$slides.find('.slide').each(function() {
				this.style[cssTransition+'Duration'] = settings.speed+'ms';
				switch(settings.animationType) {
					case 'slide' :
						this.style[cssTransition+'Property'] = cssTransform;
						break;
					case 'fade' :
						this.style[cssTransition+'Property'] = 'opacity';
						break;
				}
			});
		};
		// Removes CSS transition duration/property - called right after animation
		var removeCSStransition = function()
		{
			$slides.find('.slide').each(function() {
				this.style[cssTransition+'Duration'] = '';
				this.style[cssTransition+'Property'] = '';
			});
		};

		// Change to a new slide (public method)
		this.changeSlide = function(newSlide, animate)
		{
			var direction, transitionClass, queuedClass;
			// if animate is not set, it defaults to true
			if(typeof animate === 'undefined') animate = true;
			// Make sure an animation is not in progress
			if(throttle) return;
			throttle = true;
			// Clear any timeout
			clearTimeout(slideTimeout);
			// Determine direction to slide from
			if(newSlide > currSlide) {
				direction = 'fromRight';
			} else {
				direction = 'fromLeft';
			}

			// Make sure newSlide is not too high or too low
			if(newSlide > maxSlide) {
				newSlide = 0;
			} else if(newSlide < 0) {
				newSlide = maxSlide;
			}

			// Select transition classes based on animation type
			switch(settings.animationType) {
				case 'slide' :
					transitionClass = 'transition-slide-'+direction;
					queuedClass = 'queued-slide-'+direction;
					break;
				case 'fade' :
					transitionClass = 'transition-fade';
					queuedClass = 'queued-fade';
					break;
			}

			// Save copies of $currSlide and $newSlide to this function instance
			var $_newSlide = $slides.find("[data-order='"+newSlide+"']"),
			$_currSlide = $currSlide;

			// Queue the new slide
			$_newSlide.addClass(queuedClass);
			if(animate) {
				// Set delay to allow slide to queue
				setTimeout(function() {
					// Add CSS transition properties
					addCSStransition();
					// Set currSlide to transition
					$_currSlide.addClass(transitionClass).removeClass('active');
					// Set newSlide to active
					$_newSlide.addClass('active').removeClass(queuedClass);
				}, 10);

				// After animation finishes, remove transition class and CSS transition props
				setTimeout(function() {
					removeCSStransition();
					$_currSlide.removeClass(transitionClass);
					throttle = false;
					// Emit slide changed event
					events.emit('slideChanged');
					if(settings.autoSlideshow) slideTimeout = setTimeout(nextSlide, settings.slideDuration);
				}, 10 + settings.speed);
			} else {
				// Change to new slide without animating
				$_currSlide.removeClass('active');
				$_newSlide.addClass('active').removeClass(queuedClass);
				throttle = false;
				// Emit slide changed event
				events.emit('slideChanged');
			}
			
			// Update currSlide selector and value
			$currSlide = $_newSlide;
			currSlide = parseInt(newSlide);
			// console.log('current slide: '+currSlide);


		};
	};
	// Register plugin
	$.fn.slideMan = function(options) {
		return $.fn.encapsulatedPlugin('slideMan', slideMan, this, options);
	};
})(jQuery);