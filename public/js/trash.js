var trash = (function() {
/**
 * 
 * CACHE DOM
 * 
 */
	var $contentList = $('#contentList'),
	$contentTypeFilter = $contentList.find('select#filterContentList'),
	$contentTbody = $contentList.find('tbody'),
	$trashList = $('#trash'),
	$trashTypeFilter = $trashList.find('select#filterTrashList'),
	$trashTbody = $trashList.find('tbody'),
	$checkAll = $trashList.find('#trashCheckAll')
	$mainNav = $('#mainNav').children('ul.navbar-nav');

/**
 * 
 * BIND EVENTS
 * 
 */
	// Trash button clicked
	$contentList.on('click', '.trashContent', function(ev) {
		ev.preventDefault();
		var contentID = $(this).attr('id'),
		$thisRow = $(this).closest('tr');

		if($thisRow.hasClass('page')) {
			var confirmMessage = 'Are you sure you want to trash this page? Associated content and subpages will be orphaned';
		} else {
			var confirmMessage = 'Are you sure you want to trash this item?';
		}
		if(confirm(confirmMessage)) {
			trashContent(contentID);
		}	
	});

	// Delete button
	$trashList.on('click', '.deleteContent', function(ev) {
		ev.preventDefault();
		var contentID = $(this).attr('id'),
		$thisRow = $(this).closest('tr');

		if($thisRow.hasClass('page')) {
			var confirmMessage = 'Are you sure you want to PERMANENTLY DELETE this page? Associated content and subpages will also be deleted. This action cannot be undone.';
		} else {
			var confirmMessage = 'Are you sure you want to PERMANENTLY DELETE this item?';
		}
		if(confirm(confirmMessage)) {
			deleteContent(contentID, $thisRow);
		}	
	});

	// Restore button
	$trashList.on('click', '.restoreContent', function(ev) {
		ev.preventDefault();
		var contentID = $(this).attr('id'),
		$thisRow = $(this).closest('tr');

		restoreContent(contentID, $thisRow);
	});

	// Check/uncheck all
	$checkAll.on('change', function() {
		var $checkBoxes = $trashList.find('.trashCheck');
		$checkBoxes.prop('checked', !$checkBoxes.prop('checked'));
	})


/**
 * 
 * MAIN FUNCTIONS
 * 
 */
 	// Trash content
 	function trashContent(contentID)
 	{
 		$.ajax({
 			type: 'DELETE',
 			url: baseURL + 'dashboard/trashContent/' + contentID,
 			dataType: 'json',
 			success: function(data) {
 				if(!data.error) {
 					var $trashedRows = $contentList.find('tr#' +data.affectedRows.join(',tr#'));
			 		$trashedRows.fadeOut(300, function() {
			 			$(this).remove();
			 		});
			 		reloadTrash();
			 		reloadNav();
 				}
 			}
 		});
 	}

 	// Delete content
 	function deleteContent(contentID, $thisRow)
 	{
 		$.ajax({
 			type: 'DELETE',
 			url: baseURL + 'dashboard/deleteContent/' + contentID,
 			dataType: 'json',
 			success: function(data) {
 				if(!data.error) {
			 		$thisRow.fadeOut(300, function() {
			 			$(this).remove();
			 		});
 				}
 			}
 		});
 	}

 	// Restore content
 	function restoreContent(contentID, $thisRow)
 	{
 		$.ajax({
 			type: 'POST',
 			url: baseURL + 'dashboard/restoreContent/' + contentID,
 			dataType: 'json',
 			success: function(data) {
 				if(!data.error) {
			 		$thisRow.fadeOut(300, function() {
			 			$(this).remove();
			 		});
			 		reloadContentList();
			 		reloadNav();
 				}
 			}
 		});
 	}

/**
 * 
 * UTLITY FUNCTIONS
 * 
 */
 	function reloadTrash()
 	{
 		$trashTypeFilter.val('all');
 		$trashTbody.load(baseURL + 'dashboard/reloadTrash/');
 	}

 	function reloadContentList()
 	{
 		$contentTypeFilter.val('page');
 		$contentTbody.load(baseURL + 'dashboard/reloadContentList/')
 	}

 	function reloadNav() {
		$mainNav.load(baseURL + 'dashboard/reloadNav', function() {
			events.emit('reloadNav');
		});
	}

})();