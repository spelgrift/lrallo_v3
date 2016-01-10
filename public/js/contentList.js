var contentList = (function() {
	// Cache DOM
	var $contentList = $('#contentList'),
	$typeFilter = $contentList.find('select#filterContentList');

	// Bind events
	$typeFilter.on('change', function() {
		var type = $(this).val();
		displayRows(type);
	});

	function displayRows(type)
	{
		// Get rows to show and hide
		var $visibleRows = $contentList.find('tr.contentListRow.visible');
		if(type == 'all') {
			var $targetRows = $contentList.find('tr.contentListRow');
			$contentList.find('span.listPad').show();
		} else {
			var $targetRows = $contentList.find('tr.contentListRow.'+type);
			if(type != 'page') {
				$contentList.find('span.listPad').hide();
			} else {
				$contentList.find('span.listPad').show();
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