var contentList = (function() {
	// Cache DOM
	var $contentList = $('#contentList'),
	$trashList = $('#trash'),
	$contentTypeFilter = $contentList.find('select#filterContentList'),
	$trashTypeFilter = $trashList.find('select#filterTrashList');

	// Bind events
	$contentTypeFilter.on('change', function() {
		var type = $(this).val();
		displayRows(type, 'content');
	});

	$trashTypeFilter.on('change', function() {
		var type = $(this).val();
		displayRows(type, 'trash');
	});

	events.on('changeContentFilter', function(type) {
		displayRows(type, 'content');
	});

	// Show/Hide rows
	function displayRows(type, list)
	{
		// Select list
		switch(list) {
			case 'content' :
				var $targetList = $contentList;
			break;
			case 'trash' :
				var $targetList = $trashList;
			break;
		}
		// Get rows to show and hide
		var $visibleRows = $targetList.find('tr.contentListRow.visible');
		if(type == 'all') {
			var $targetRows = $targetList.find('tr.contentListRow');
			if(list == 'content') {
				$targetList.find('span.listPad').show();
			}
		} else {
			var $targetRows = $targetList.find('tr.contentListRow.'+type);
			if(list == 'content') {
				// If list contains pages, show the parent pad
				if(type != 'page') {
					$targetList.find('span.listPad').hide();
				} else {
					$targetList.find('span.listPad').show();
				}	
			}
		}

		// Hide visible rows
		$visibleRows.each(function(){
			$(this).removeClass('visible');
		});

		// Add visible class to target rows
		$targetRows.each(function(){
			$(this).addClass('visible');
		});
	}
})();