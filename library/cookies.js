/** simple cookies management **/
/** this functions are based on https://www.w3schools.com/js/js_cookies.asp **/
function setCookie(cname, cvalue, expiration_minutes = 30) {
	var d = new Date();
	d.setTime(d.getTime() + (expiration_minutes * 60 * 1000));
	var expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return null;
}

/** This extensions are added by me to make some operations easier.*/
/* Remove a cookie setting the expiration in the past. */
function delCookie(cname) {setCookie(cname, '', -1);}

/* Check if the cookie is deleted or inexistent (empty values means existence). */
function existsCookie(cname){return (!(!(getCookie(cname))));}