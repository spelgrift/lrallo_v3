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
 	var $contentArea = $('#contentArea'),
	pageURL = _.getURL();

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
 	// Create new editor when new text element added
 	events.on('newText', newEditor);
/**
 * 
 * CORE FUNCTIONS
 * 
 */
	function saveText(e) {
		var data = { text : tinymce.activeEditor.getContent() },
		contentID = $(tinymce.activeEditor.getElement()).siblings('.contentControlMenu').attr('id'),
		url = pageURL + '/updateText/'+contentID;
		_.post(url, data, submitSuccess, submitError);
	}

	function submitSuccess(data) {
		console.log('Changes saved!');
	}

	function submitError(data) {
		console.log('Error');
	}

	function newEditor() {
		tinymce.remove();
		tinymce.init(editorConfig);
	}

});