var contentControls = (function() {
	var $contentArea = $('#contentArea');

	// Show/hide controls on mouseover
	$contentArea.on({
		mouseenter: function() {
			$(this).children('.adminContentControls').children('ul:first').stop(false,true).fadeIn('fast');
		},
		mouseleave: function () {
    		$(this).children('.adminContentControls').children('ul:first').stop(false,true).fadeOut('fast');
		}          
	}, '.contentItem');
})();