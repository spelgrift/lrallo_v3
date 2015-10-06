$(function(){

	// List Pages
	$.get(pageVars.basePath + '/listPages/', function(data){
		var basePath = data[0];
		for(var i = 0; i < data[1].length; i++)
		{
			$('#pageList').append('<div>' + data[1][i].name + '<a class="delete" rel="' + data[1][i].pageID + '" href="#">Delete</a><a href="' + basePath + data[1][i].url + '" >View</a></div>');
		}

	}, 'json');

	// Add Page
	$('#addPageForm').submit(function(ev){
		var url = $(this).attr('action');
		var data = $(this).serialize();
		
		$.post(url, data, function(res){
			// console.log(res);
			$('#pageList').append('<div>' + res.name + '<a class="delete" rel="' + res.pageID + '" href="#">Delete</a><a href="' + res.url + '" >View</a></div>');
			$("input[name='pageName'").val("");
			reloadNav();
		}, 'json');

		ev.preventDefault();
	});

	// Delete Page
	$('body').on('click', '.delete', function(ev){
		var thisItem = $(this);
		var id = $(this).attr('rel');

		if(confirm('Delete this page?'))
		{
			$.post(pageVars.basePath + '/deletePage', {'id': id}, function(res){
				thisItem.parent().remove();
				reloadNav();
			});
		}

		ev.preventDefault();
	})

	// Reload Nav
	function reloadNav()
	{
		$('#mainNav').children('ul.navbar-nav').load(pageVars.basePath + '/reloadNav')
	}

});