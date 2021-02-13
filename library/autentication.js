/* Send API key to the server to init session. */
function autenticate(serverURL, apikey, onSuccess = null, onError = null){
	httpRequest(serverURL+"/", 'GET', onSuccess, onError, null, apikey);
}

/* Logoff from the API, a new autenticatin process is required for further requests. */
function deautenticate(onSuccess = null, onError = null){
	httpRequest(serverURL+"/", 'GET', onSuccess, onError, null, "");
}

