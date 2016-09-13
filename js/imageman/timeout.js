var inactivity = (function() {
	var t,
	maxIdleTime = 1800; // Seconds 
	window.onload = resetTimer;
	document.onmousemove = resetTimer;
	document.onkeypress = resetTimer;

	function logout() {
		alert('You have been logged Out after '+ (maxIdleTime / 60) +' minutes of inactivity');
		location.href=baseURL + 'login/logout/';
	}

	function resetTimer() {
		clearTimeout(t);
		t = setTimeout(logout, maxIdleTime * 1000);
	}
})();