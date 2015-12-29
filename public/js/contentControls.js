var contentControls = (function() {
	var $contentArea = $('#contentArea');
	var pageURL = $('a#viewTab').attr('href');

/*
 *
 * BIND EVENTS
 *
 */

	 // Show/hide controls on mouseover
	$contentArea.on({
		mouseenter: function() {
			$(this).find('ul.contentControlMenu').stop(false,true).fadeIn('fast');
		},
		mouseleave: function() {
			$(this).find('ul.contentControlMenu').stop(false,true).fadeOut('fast');
		}
	}, '.contentItem');

	// Trash Content
	$contentArea.on('click', 'a.trashContent', function(ev) {
		ev.preventDefault();
		var contentID = $(this).closest('.contentControlMenu').attr('id'),
		$thisItem = $(this).closest('.contentItem');

		if(confirm('Are you sure you want to trash this item?')){
			trashContent($thisItem, contentID);
		}
	});

	// Content Sortable
	$contentArea.sortable({
		handle : '.handle',
		update : updateSortable
	});

/*
 *
 * CORE FUNCTIONS
 *
 */

 	function updateSortable() {
 		var order = $(this).sortable('serialize');
 		$.ajax({
 			url: pageURL + '/sortContent/',
 			type: 'POST',
 			data: order,
 			success: function(){
 				// console.log("sorted!");
 			}
 		})
 	}

	function trashContent(thisItem, contentID) {
		$.ajax({
			type: 'DELETE',
			url: pageURL + '/trashContent/' + contentID,
			dataType: 'json',
			success: function(data) {
				if(!data.error) {
					thisItem.fadeOut(300, function() {
						$(this).remove();
					});
				}
			}
		});
	}

})();