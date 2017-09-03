var $ = require('jquery');
var utilityFunctions = (function() {

	var error = function(message, $msg, $input) {
		$msg.html(message);
		$input.focus();
		clearMsg($msg);
		return false;
	};

	var clearMsg = function(selector, timeout) {
		if (timeout === undefined) {
			timeout = 4000;
		}
		setTimeout(function(){
			selector.fadeOut('slow', function() {
				selector.html('');
				selector.show();
			});
		}, timeout);
	};

	var post = function(url, data, success, error) {
		$.ajax({
			type: 'POST',
			url: url,
			data: data,
			dataType: 'json',
			success: function(data) {
				if(!data.error) {
					// Success
					success(data);
				} else {
					error(data);
				}
			}
		});
	};

	var getURL = function(){
		if(window.baseURL === $('a#viewTab').attr('href')){
			return window.baseURL + 'page';
		} else {
			return $('a#viewTab').attr('href');
		}
	};

	return {
		error : error,
		clearMsg : clearMsg,
		post : post,
		getURL : getURL
	};

})();

module.exports = utilityFunctions;