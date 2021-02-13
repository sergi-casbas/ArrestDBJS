/* Send API key to the server to init session. */
function autenticate(serverURL, apikey, onSuccess=defaultOnSuccess, onError=defaultOnError){
	httpRequest(serverURL+"/", 'GET', onSuccess, onError, null, apikey);
}

/* Logoff from the API, a new autenticatin process is required for further requests. */
function deautenticate(onSuccess=defaultOnSuccess, onError=defaultOnError){
	httpRequest(serverURL+"/", 'GET', onSuccess, onError, null, "");
}

function keepAlive(onSuccess=defaultOnSuccess, onError=defaultOnError){
	httpRequest(serverURL+"/", 'GET', onSuccess, onError);
}