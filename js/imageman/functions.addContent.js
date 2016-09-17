var helperFunctions = (function() {

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

	return {
		error : error,
		clearMsg : clearMsg
	};

})();

module.exports = helperFunctions;