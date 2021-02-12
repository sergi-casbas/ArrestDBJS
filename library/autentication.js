/* Send API key to the server to gather (if success) the authorization bearer. */
function autenticate(serverURL, apikey, onSuccess = null, onError = null){
	deautenticate();
	setCookie('Apikey', apikey);
	httpRequest(serverURL+"/", 'GET', onSuccess, onError);
	delCookie('Apikey');
}

/* Logoff from the API, a new autenticatin process is required for further requests. */
function deautenticate(){
	delCookie('Apikey');
	delCookie('Authorization');
}

