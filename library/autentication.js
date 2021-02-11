/* Send API key to the server to gather (if success) the authorization bearer. */
function autenticate(serverURL, apikey, autentication_success = null, autentication_fail = null){
	deautenticate();
	setCookie('Apikey', apikey);
	httpRequest(serverURL+"/", 'GET', autentication_success, autentication_fail);
	delCookie('Apikey');
}

/* Logoff from the API, a new autenticatin process is required for further requests. */
function deautenticate(){
	delCookie('Apikey');
	delCookie('Authorization');
}

