;(function($, window, document, undefined) {

	var imSlider = (function(element, settings) {

		// Constructor
		function _imSlider(element, settings) {
			// Default values
			this.defaults = {
				speed : 500,
				slideDuration : 3000,
				autoSlideshow : false,
				animationType : 'slide'
			};
			// Merge user supplied options with defaults
			this.settings = $.extend({}, this, this.defaults, settings);

			// Plugin-wide properties
			this.initials = {
				$slides : null,
				$next : null,
				$prev : null,
				currSlide : 0,
				$currSlide : null,
				maxSlide : 0,
				throttle : false,
				slideTimeout : null,
				cssTransitions : false,
				cssTransform : false
			}
			// Merge as direct properties of Slider
			$.extend(this, this.initials)

			// Store reference to Slideshow DOM element
			this.$slideshow = $(element);

			// Ensure that the value of 'this' always references the main class
			this.nextSlide = $.proxy(this.nextSlide,this);

			// Call initiate function
			this.init();
		}

		return _imSlider;
	})();

	/**
	 *
	 *	init - calls methods to setup and activate the slider
	 *
	 */
	imSlider.prototype.init = function() {
		// Test to see if css animations and transforms are available
		this.cssTest();
		// Store DOM references and initial values
		this.cacheDOM();
		// Bind events to slider controls
		this.events();
		// Activate autoSlideshow (if enabled)
		this.activate();
	};

	/**
	 * Appropriated out of Modernizr v2.8.3
	 * Creates a new DOM element and tests existence of properties on it's
	 * Style object to see if CSSTransitions are available
	 * @params void
	 * @returns void
	 *
	 */
	imSlider.prototype.cssTest = function(){
		var elem = document.createElement('modernizr');
		//A list of properties to test for
		var transitionProps = ["transition","WebkitTransition","MozTransition","OTransition","msTransition"],
		transformProps = ["transform","WebkitTransform","MozTransform","OTransform","msTransform"];
		//Iterate through our new element's Style property to see if these properties exist
		for ( var i in transitionProps ) {
			var prop = transitionProps[i];
			var result = elem.style[prop] !== undefined ? prop : false;
			if (result){
				this.cssTransitions = result;
				break;
			} 
		} 
		for ( var i in transformProps ) {
			var prop = transformProps[i];
			var result = elem.style[prop] !== undefined ? prop : false;
			if (result){
				this.cssTransform = result;
				break;
			} 
		} 
	};

	imSlider.prototype.cacheDOM = function() {
		this.$slides = this.$slideshow.find('.slides');
		this.maxSlide = this.$slides.children().length - 1;
		this.$next = this.$slideshow.find('.arrow-right');
		this.$prev = this.$slideshow.find('.arrow-left');
		this.$currSlide = this.$slides.find('.slide.active');
		this.currSlide = parseInt(this.$currSlide.attr('data-order'));
	};

	imSlider.prototype.events = function() {
		var _ = this;
		this.$next.click(function() {
			_.changeSlide(_.currSlide + 1);
			_.settings.autoSlideshow = false;
		});
		this.$prev.click(function() {
			_.changeSlide(_.currSlide - 1);
			_.settings.autoSlideshow = false;
		});
	};

	imSlider.prototype.activate = function() {
		var _ = this;
		if(this.settings.autoSlideshow) {
			this.slideTimeout = setTimeout(this.nextSlide, this.settings.slideDuration);
		}
	};

	imSlider.prototype.addCSStransition = function() {
		var _ = this;
		this.$slides.find('.slide').each(function() {
			this.style[_.cssTransitions+'Duration'] = _.settings.speed+'ms';
			switch(_.settings.animationType) {
				case 'slide' : 
					this.style[_.cssTransitions+'Property'] = _.cssTransform;
					break;
				case 'fade' :
					this.style[_.cssTransitions+'Property'] = 'opacity';
					break;
			}
		});
	};

	imSlider.prototype.removeCSStransition = function() {
		var _ = this;
		this.$slides.find('.slide').each(function() {
			this.style['transitionDuration'] = '';
			this.style['transitionProperty'] = '';
		});
	};

	imSlider.prototype.nextSlide = function() {
		this.changeSlide(this.currSlide + 1);
	};

	imSlider.prototype.changeSlide = function(newSlide) {
		var _ = this;
		
		// Make sure an animation is not in progress
		if(this.throttle) return;
		this.throttle = true;

		// Clear any timeout
		clearTimeout(this.slideTimeout);

		// Determine direction to slide from
		if(newSlide > this.currSlide) {
			var direction = 'fromRight';
		} else {
			var direction = 'fromLeft';
		}

		// Make sure newSlide is not too high or too low
		if(newSlide > this.maxSlide) {
			newSlide = 0;
		} else if(newSlide < 0) {
			newSlide = this.maxSlide;
		}

		// Select transition classes based on animation type
		switch(this.settings.animationType) {
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
		var $_newSlide = this.$slides.find("[data-order='"+newSlide+"']"),
		$_currSlide = this.$currSlide;

		// Queue the new slide
		$_newSlide.addClass(queuedClass);

		// Set delay to allow slide to queue
		setTimeout(function() {
			// Add CSS transition properties
			_.addCSStransition();
			// Set currSlide to transition
			$_currSlide.addClass(transitionClass).removeClass('active');
			// Set newSlide to active
			$_newSlide.addClass('active').removeClass(queuedClass);
		}, 10);

		// After animation finishes, remove transition class and CSS transition props
		setTimeout(function() {
			_.removeCSStransition();
			$_currSlide.removeClass(transitionClass);
			_.throttle = false;
			if(_.settings.autoSlideshow) _.slideTimeout = setTimeout(_.nextSlide, _.settings.slideDuration);
		}, 10 + _.settings.speed);

		// Update currSlide selector and value
		this.$currSlide = $_newSlide;
		this.currSlide = newSlide;
	};

	/**
	 *
	 *	Initialize the plugin once for each DOM object passed to jQuery
	 *
	 */
	$.fn.imSlider = function(options) {
		return this.each(function() {
			var el = $(this);
			// Return early if this element already has a plugin instance
			if(el.data('imSlider')) return;

			plugin = new imSlider(el, options);
			// Store plugin object in this elements data
			el.data('imSlider', plugin);
		});
	};



}(jQuery, window, document));