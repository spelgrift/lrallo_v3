var $ = require('jquery');
var _ = require('./utilityFunctions.js'); // helper functions

$(function() {
/**
 * 
 * CACHE DOM
 * 
 */
 	var $postList = $('#postList');

/**
 * 
 * BIND EVENTS
 * 
 */
 	$postList.on('click', 'a.trashPost', trashPost);
 	$postList.on('click', 'a.togglePublic', togglePublic);


/**
 * 
 * CORE FUNCTIONS
 * 
 */
 	function togglePublic(ev) {
 		ev.preventDefault();
 		var contentID = $(this).attr('id'),
 		$thisButton = $(this);
 		$.ajax({
 			url: baseURL+blogURL+"/togglePublic/"+contentID,
 			type: 'POST',
 			success: function(data) {
 				if(!data.error) {
					$thisButton.html(data);
				}
 			}
 		});


 	}

	function trashPost(ev) {
		ev.preventDefault();
		if(!confirm('Are you sure you want to trash this post?')){
			return false;
		}
		var contentID = $(this).attr('id'),
		$thisRow = $(this).closest('tr');
		$.ajax({
			type: 'DELETE',
			url: baseURL + 'page/trashContent/' + contentID,
			dataType: 'json',
			success: function(data) {
				if(!data.error) {
					$thisRow.fadeOut(function() {
						$(this).remove();
					});
				}
			}
		});
	}

});