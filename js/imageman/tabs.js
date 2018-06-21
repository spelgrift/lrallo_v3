var $ = require('jquery');

$(function() {

	var transitionSpeed = 200;

	$('#adminNav').on('click', 'a.adminTab', adminNav);

	$('body').on('click', '.tabLink', function(ev) {
		ev.preventDefault();
		switchTabs($(this));
	});
		
	function adminNav(ev) {
		ev.preventDefault();

		if($(this).hasClass('active')) {
			return false;
		}

		var nav = $(this).closest('ul.nav');
		nav.find('a.active').removeClass('active');
		if(!$(this).hasClass('dropdown-toggle')){
			$(this).addClass('active');
		}

		switchTabs($(this));
	}

	function switchTabs($selector) {
		// Figure out which panel to show
		var panelToShow = $selector.attr('data-tab');
		// Hide current panel
		$('.tabPanel.active').hide().removeClass('active');
		// Show next panel
		$('#'+panelToShow).show().addClass('active');
	}


});