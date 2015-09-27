var pageVars = {
	basePath: stripTrailingSlash(window.location.pathname) // The relative path stripped of any trailing slash (/). Use before ajax URLs.
};

function stripTrailingSlash(str) {
	if(str.substr(-1) === '/') {
   	return str.substr(0, str.length - 1);
	}
	return str;
}
