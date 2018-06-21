var $ = require('jquery');
var _ = require('./utilityFunctions.js'); // helper functions
require('./slideMan.js'); // Image slider

$(function() {
/**
 * 
 * SET UP VARS
 * 
 */
 	var $allPosts 	= $('#allPosts'),
 	lastID 			= getLastID(),
 	threshold 		= getThreshold(),
 	loadPending 	= false,
 	reachedEnd		= false;

/**
 * 
 * BIND EVENTS
 * 
 */
 	// Reset threshold on window resize
 	$(window).resize(function(){
 		threshold = getThreshold();
 	});

 	$(window).on('scroll', loadPosts);

/**
 * 
 * CORE FUNCTIONS
 * 
 */
 	function getLastID() {
 		return $('.blogPost').last().attr('data-id');
 	}

 	function getThreshold() {
 		return document.body.clientHeight - (1.5 * window.innerHeight);
 	}

 	function loadPosts() {
 		if(reachedEnd){ return; }
 		if(loadPending){ return; }
 		if(window.pageYOffset < threshold) {
 			return;
 		}
 		loadPending = true;
 		var url = baseURL + blogURL + '/loadMorePosts/' + lastID;
 		$.ajax({
 			type: 'POST',
 			url: url,
 			data: {},
 			success: loadSuccess
 		});
 	}

 	function loadSuccess(data) {
 		if(data.length === 0) {
 			reachedEnd = true;
 			return;
 		}
 		// console.log('loaded more posts');
 		var $newContent = $(data);
 		$newContent.appendTo($allPosts);
 		setTimeout(function(){
 			var $slideshows = $newContent.find('.slideshow');
 			_.loadSlides($slideshows);
 			lastID = getLastID();
 			threshold = getThreshold();
 			loadPending = false;
 		}, 500);
 	}

 });