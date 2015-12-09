var contentControls = (function() {
	var $contentArea = $('#contentArea');
	var pageURL = $('a#viewTab').attr('href');

	// Show/hide controls on mouseover
	$contentArea.on({
		mouseenter: function() {
			$(this).children('.adminContentControls').children('ul:first').stop(false,true).fadeIn('fast');
		},
		mouseleave: function () {
    		$(this).children('.adminContentControls').children('ul:first').stop(false,true).fadeOut('fast');
		}          
	}, '.contentItem');

	/*
	 *
	 * BIND EVENTS
	 *
	 */

	// Trash Content
	$contentArea.on('click', 'a.trashContent', function(ev) {
		ev.preventDefault();
		var contentID = $(this).closest('.adminContentControls').attr('id');
		var $thisItem = $(this).closest('.contentItem');

		console.log(contentID);

		if(confirm('Are you sure you want to trash this item?')){
			trashContent($thisItem, contentID);
		}
	});

	/*
	 *
	 * FUNCTIONS
	 *
	 */
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