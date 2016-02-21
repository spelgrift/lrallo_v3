$(function() {
	var galleryImgSettings = (function() {
		var $editSequence = $('#editSequence'),
		galURL = $('a#viewTab').attr('href');
/*
*
* BIND EVENTS
*
*/
		// Show/hide controls on mouseover
		$editSequence.on({
			mouseenter: function() {
				$(this).find('.galImageControls').stop(false,true).fadeIn('fast');
			},
			mouseleave: function() {
				$(this).find('.galImageControls').stop(false,true).fadeOut('fast');
			}
		}, '.adminThumb');

		// Sortable
		$editSequence.sortable({
			handle: '.handle',
			update: updateSortable
		});
/*
 *
 * CORE FUNCTIONS
 *
 */

	 	function updateSortable() {
	 		var order = $(this).sortable('serialize');
	 		$.ajax({
	 			url: galURL + '/sortGalImages/',
	 			type: 'POST',
	 			data: order,
	 			success: function(){
	 				// console.log("sorted!");
	 			}
	 		});
	 	}		

	})();
});