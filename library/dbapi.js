/* Include external required javascript files */
include('../library/cookies.js');
include('../library/httprequest.js');
function include(scriptPath){
    var script = document.createElement('script'); 
    script.src = scriptPath; 
    document.head.appendChild(script);
}

/* Default response functions, intended only for installation and debugging purposes.
   Is recommended to use your own function on function call.
   Use null if you don't want any return of the call.
 */
function defaultOnSuccess(response){
    defaultOnError(response);
}
function defaultOnError(response){
    console.log('x-auth-message: ' + response.getResponseHeader('x-auth-message'));
    console.table(response.JSON);
    
}

/* API  functions
(C)reate > POST   /table
(R)ead   > GET    /table[/id]
(R)ead   > GET    /table[/column/content]
(U)pdate > PUT    /table/id
(D)elete > DELETE /table/id
*/
function create(serverURL, tableName, itemJSON, onSuccess=defaultOnSuccess, onError=defaultOnError){
    httpRequest(serverURL + "/" + tableName , 'POST', onSuccess, onError, itemJSON);
}

function read(serverURL, tableName, itemId, onSuccess=defaultOnSuccess, onError=defaultOnError){
	httpRequest(serverURL + "/" + tableName + "/" + itemId, 'GET', onSuccess, onError);
}

function readAll(serverURL, tableName, onSuccess=defaultOnSuccess, onError=defaultOnError){
	httpRequest(serverURL+"/"+tableName, 'GET', onSuccess, onError);
}

function update(serverURL, tableName, itemId, itemJSON, onSuccess=defaultOnSuccess, onError=defaultOnError){
    httpRequest(serverURL + "/" + tableName + "/" + itemId, 'PUT', onSuccess, onError, itemJSON);
}

function remove(serverURL, tableName, itemId, onSuccess=defaultOnSuccess, onError=defaultOnError){
    httpRequest(serverURL + "/" + tableName + "/" + itemId, 'DELETE', onSuccess, onError);
}

