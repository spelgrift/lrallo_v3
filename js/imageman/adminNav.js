var $ = require('jquery');

$(function() {
	var adminNavigation = (function() {
		var $adminNav = $('#adminNav');

		$adminNav.on('click', 'a.adminTab', function(ev) {
			ev.preventDefault();

			if($(this).hasClass('active')) {
				return false;
			}

			var nav = $(this).closest('ul.nav');
			nav.find('a.active').removeClass('active');
			if(!$(this).hasClass('dropdown-toggle')){
				$(this).addClass('active');
			}

			// Figure out which panel to show
			var panelToShow = $(this).attr('data-tab');

			// Hide current panel
			$('.tabPanel.active').hide().removeClass('active');
			// Show next panel
			$('#'+panelToShow).show().addClass('active');
		});
	})();
});