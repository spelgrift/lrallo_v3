var $ = require('jquery');
var _ = require('./utilityFunctions.js'); // helper functions
var tinymce = require('../libs/tinymce/tinymce.min.js');

$(function() {
/**
 * 
 * CONFIG
 * 
 */
 	var titleInputConfig = {
 		selector: 'h2#blogTitleInput',
		skin_url: baseURL+'public/css',
		theme: 'inlite',
		inline: true,
		plugins: "paste",
		paste_as_text: true,
		selection_toolbar: '',
		insert_toolbar: '',
		init_instance_callback: function(editor) {
			editor.on('blur', errorCheck);
			editor.on('change', madeChanges);
		},
 	},
 	bodyInputConfig = {
 		selector: 'div#blogBodyInput',
		skin_url: baseURL+'public/css',
		theme: 'inlite',
		inline: true,
		plugins: "paste",
		paste_as_text: true,
		selection_toolbar: 'bold italic | alignleft aligncenter alignright alignjustify | quicklink h1 h2 h3',
		insert_toolbar: '',
		init_instance_callback: function(editor) {
			editor.on('change', madeChanges);
		},
 	};

 	var defaultTitle = 'Untitled';

/**
 * 
 * CACHE DOM
 * 
 */
 	var $msg			= $('#editPostMsg'),
 	$titleInput 	= $('#blogTitleInput'),
 	$bodyInput 		= $('#blogBodyInput'),
 	$publishTab		= $('#adminNav').find('#publishTab'),
 	$trashTab		= $('#adminNav').find('#trashTab'),
 	$contentArea 	= $('#contentArea');

 	var contentID 	= $publishTab.attr('data-id'),
 	isNew				= false,
 	saveEnabled		= true,
 	pendingChanges = false;

 	// Determine if this is a new post or editing an old one
 	if((window.location.href).includes(blogURL+"/newpost")) {
 		isNew = true;
 		pendingChanges = true;
 	} else {
 		var $viewTab = $("#viewTab");
 	}


/**
 * 
 * INIT TINYMCE
 * 
 */
	tinymce.init(titleInputConfig);
	tinymce.init(bodyInputConfig);

/**
 * 
 * BIND EVENTS
 * 
 */
 	$publishTab.click(publishPost);
 	$trashTab.click(trashPost);
 	$(window).on('beforeunload', confirmUnsavedChanges);
 	events.on('newText', reInit);

/**
 * 
 * CORE FUNCTIONS
 * 
 */
 	function confirmUnsavedChanges() {
 		if(pendingChanges){
 			return "test";
 		}
 	}
 	function publishPost(ev){
 		ev.preventDefault();
 		if(!saveEnabled){ return false; }

 		var data = {
 			'title' : tinymce.get('blogTitleInput').getContent(),
 			'body' : tinymce.get('blogBodyInput').getContent()
 		},
 		url = baseURL + blogURL +'/publishPost/'+contentID;
 		_.post(url, data, submitBlogSuccess, submitBlogError);	
 	}

 	function submitBlogSuccess(data) {
		pendingChanges = false;
		// If this is a new post, redirect to the blog mainpage to see the new post in action
		if(isNew){
			window.location.replace(baseURL+blogURL+'/');
		} else {
			_.error("<p class='bg-success'>Changes saved!</p>", $msg);
			// Update view link and window URL
			$viewTab.attr('href', data.viewPath);
			document.title = data.pageTitle;
			window.history.replaceState({}, data.title, data.windowPath);
		}
	}

	function submitBlogError(data) {
		_.error("<p class='bg-danger'>"+data.error_msg+"</p>", $msg, $titleInput);
	}

	function trashPost(ev) {
		ev.preventDefault();
		if(!confirm('Are you sure you want to trash this post?')){
			return false;
		}
		$.ajax({
			type: 'DELETE',
			url: baseURL + 'page/trashContent/' + contentID,
			dataType: 'json',
			success: function(data) {
				if(!data.error) {
					pendingChanges = false;
					window.location.replace(baseURL+blogURL+'/manage/');
				}
			}
		});
	}

 	function errorCheck(){
 		var title = tinymce.get('blogTitleInput').getContent();
 		if(title === "") {
 			tinymce.get('blogTitleInput').setContent(defaultTitle);
 			_.error("<p class='bg-danger'>Title cannot be blank</p>", $msg, $titleInput);
 			saveEnabled = false;
 			setTimeout(function(){
 				saveEnabled = true;
 			}, 500);
 			return false;
 		}
 		return true;
 	}

 	function madeChanges(){
 		pendingChanges = true;
 	}

 	function reInit(){
 		setTimeout(function(){
 			tinymce.init(titleInputConfig);
			tinymce.init(bodyInputConfig);
 		},50);
 		
 	}

});