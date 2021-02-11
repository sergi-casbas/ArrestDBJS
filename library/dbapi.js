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
function defaultSuccessFunction(response){
    console.debug("OK");
}
function defaultFailFunction(response){
    console.debug("KO\n"+JSON.stringify(response.JSON, null, 4));
}

/* API  functions
(C)reate > POST   /table
(R)ead   > GET    /table[/id]
(R)ead   > GET    /table[/column/content]
(U)pdate > PUT    /table/id
(D)elete > DELETE /table/id
*/
function create(serverURL, tableName, itemJSON, successFunction=defaultSuccessFunction, failFunction=defaultFailFunction){
    httpRequest(serverURL + "/" + tableName , 'POST', successFunction, failFunction, itemJSON);
}

function read(serverURL, tableName, itemId, successFunction=defaultSuccessFunction, failFunction=defaultFailFunction){
	httpRequest(serverURL + "/" + tableName + "/" + itemId, 'GET', successFunction, failFunction);
}

function readAll(serverURL, tableName, successFunction=defaultSuccessFunction, failFunction=defaultFailFunction){
	httpRequest(serverURL+"/"+tableName, 'GET', successFunction, failFunction);
}

function update(serverURL, tableName, itemId, itemJSON, successFunction=defaultSuccessFunction, failFunction=defaultFailFunction){
    httpRequest(serverURL + "/" + tableName + "/" + itemId, 'PUT', successFunction, failFunction, itemJSON);
}

function remove(serverURL, tableName, itemId, successFunction=defaultSuccessFunction, failFunction=defaultFailFunction){
    httpRequest(serverURL + "/" + tableName + "/" + itemId, 'DELETE', successFunction, failFunction);
}

