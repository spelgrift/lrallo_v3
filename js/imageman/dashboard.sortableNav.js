var $ = require('jquery');
require('../libs/jquery-ui.sortable');

$(function() {
	// Cache DOM
	var $mainNav,
		$navList,
		$navItems,
		sortHandle = "<i class='navHandle fa fa-plus'></i>";

	// Bind Events
	events.on('reloadNav', function() {
		$navList.sortable('destroy');
		initSortable();
	});

	// Main Functions
	initSortable();

	function initSortable() {
		$mainNav = $('#mainNav');
		$navList = $mainNav.find('ul.navbar-nav');
		$navItems = $navList.find('li');

		// Append handles and IDs to nav items
		$navItems.each(function(i) {
			$(this).css({'position' : 'relative', 'margin-left' : '15px'}).append(sortHandle);
		});

		// Init Sortable
		$navList.sortable({
			handle : '.navHandle',
			update : updateSortable
		});
	}

	function updateSortable() {
		var order = $(this).sortable('serialize');
		$.ajax({
			url: baseURL + 'dashboard/sortNav',
			type: 'POST',
			data: order,
			success: function() {
				// console.log('sorted!');
			}
		});
	}
});