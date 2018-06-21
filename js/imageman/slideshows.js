var $ = require('jquery');
var _ = require('./utilityFunctions.js'); // helper functions
require('./slideMan.js'); // Image slider

$(function() {
	var $allSlideshows = $('.slideshow');
	_.loadSlides($allSlideshows);
});