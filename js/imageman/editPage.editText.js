var $ = require('jquery');
var _ = require('./utilityFunctions.js'); // helper functions
var tinymce = require('../libs/tinymce/tinymce.min.js');

$(function() {
/**
 * 
 * CONFIG
 * 
 */
 	var editorConfig = {
 		selector: 'div.text-edit',
		skin_url: baseURL+'public/css',
		theme: 'inlite',
		inline: true,
		selection_toolbar: 'bold italic | alignleft aligncenter alignright alignjustify | quicklink h1 h2 h3',
		insert_toolbar: '',
		init_instance_callback: function(editor) {
			editor.on('change', saveText);
		},
 	};
/**
 * 
 * CACHE DOM
 * 
 */
 	var $contentArea 	= $('#contentArea'),
 	$editHTMLmodal 	= $('#editHTMLmodal'),
 	$editHTMLarea 		= $editHTMLmodal.find('#editHTMLarea'),
 	$submitHTML 		= $editHTMLmodal.find('#submitHTML'),
 	$textMsg 			= $editHTMLmodal.find('#textMsg'),
	pageURL 				= baseURL+"page";

/**
 * 
 * INIT TINYMCE
 * 
 */
	tinymce.init(editorConfig);

/**
 * 
 * BIND EVENTS
 * 
 */
 	events.on('newText', newEditor);
 	$contentArea.on('click', '.editHTML', editHTML);
 	$submitHTML.click(saveHTML);

/**
 * 
 * CORE FUNCTIONS
 * 
 */
 	function editHTML(ev) {
 		ev.preventDefault();
 		var contentID 	= $(this).closest('.contentControlMenu').attr('id'),
 		editorID 		= $(this).closest('.contentControlMenu').siblings('.text-edit').attr('id'),
 		content 			= tinymce.get(editorID).getContent();

 		$editHTMLarea.val(content);
 		$submitHTML.attr('data-id', contentID);
 		$editHTMLmodal.modal('show');
 	}

 	function saveHTML(ev) {
 		ev.preventDefault();
 		var contentID = $(this).attr('data-id'),
 		data = {	text : $editHTMLarea.val() };
 		url = pageURL + '/updateText/'+contentID;
		_.post(url, data, submitHTMLsuccess, submitHTMLerror);
 	}

 	function submitHTMLsuccess(data) {
 		$contentArea.find('#'+data.results.contentID).siblings('.text-edit').html(data.results.text);
 		$editHTMLarea.val('');
 		$editHTMLmodal.modal('hide');
 	}

 	function submitHTMLerror(data) {
 		_.error('Error saving content', $textMsg, $editHTMLarea);
 	}

	function saveText() {
		var data = { text : tinymce.activeEditor.getContent() },
		contentID = $(tinymce.activeEditor.getElement()).siblings('.contentControlMenu').attr('id'),
		url = pageURL + '/updateText/'+contentID;
		_.post(url, data, submitTextSuccess, submitTextError);
	}

	function submitTextSuccess(data) {
		// console.log('Changes saved!');
	}

	function submitTextError(data) {
		console.log('Error');
	}

	function newEditor() {
		tinymce.remove();
		tinymce.init(editorConfig);
	}

});