$(function() {
	var gallery = (function() {

		// Cache DOM
		var $viewerTab = $('#viewer'),
		$slideshow = $viewerTab.find('.slideshow');

		// Initilize slideshow
		$slideshow.slideMan();
	})();
});