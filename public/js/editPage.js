var addContent = (function(){

	// Cache DOM
	var $addTab = $('a.addTab');
	var $addModal = $('#addContentModal');
	var $modalContent = $addModal.find('.modal-content');

	// Bind Events
	$addTab.on('click', function(ev){
		ev.preventDefault();
		loadForm($(this).attr('data-id'));
		$addModal.modal('show');
	});

	function loadForm(type){
		$modalContent.html("");
		if(type == 'text')
		{
			// Actually just skip this and
			$modalContent.load(baseURL + 'views/inc/addContentForms/addText.php');
		}
		else if(type == 'page')
		{
			$modalContent.load(baseURL + 'views/inc/addContentForms/addPage.php');
		}
	}


})();