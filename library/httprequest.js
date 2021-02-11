function httpRequest(url, operation, onSuccess = null, onError = null, objectJSON = null){
	/* Prepare all request values, fallback, etc.. */
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = onReadyStateChange;

	/* Set callback functions if they are set. */
	if (onSuccess) {xhttp.onSuccess = onSuccess;}
	if (onError) {xhttp.onError = onError;}

	/* Prepare asyncronous connection. */
	xhttp.open(operation, url, true);

	/* Automate autentication / authorization headers sent (managed through cookies). */
	if (existsCookie('Apikey')){ // Apikey have exclusive priority than authorization.
		xhttp.setRequestHeader("Apikey", getCookie('Apikey'));
	}else if(existsCookie('Authorization')){
		xhttp.setRequestHeader("Authorization", getCookie('Authorization'));
	}

	/* Automate the use of database multiplexing. */
	if (existsCookie('Database')){
		xhttp.setRequestHeader("Database", getCookie('Database'));
	}

	/* Send the request with JSON payload if exists. */
	if (objectJSON){
		xhttp.setRequestHeader('Content-Type', 'application/json');
		xhttp.send(JSON.stringify(objectJSON));
	}else{
		xhttp.send();
	}
}

function onReadyStateChange(){
	/* https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/readyState
	Value	State	Description
	0	UNSENT	Client has been created. open() not called yet.
	1	OPENED	open() has been called.
	2	HEADERS_RECEIVED	send() has been called, and headers and status are available.
	3	LOADING	Downloading; responseText holds partial data.
	4	DONE	The operation is complete.
	*/
	if (this.readyState == 2){ //HEADERS_RECEIVED
		if (this.getResponseHeader('Authorization')){
			authHeader = this.getResponseHeader('Authorization');
			if (authHeader.substr(0, 6) == 'Bearer'){	
				setCookie('Authorization', authHeader) ;
			}else{
				delCookie('Authorization');
			}
		}
	}else if (this.readyState == 4){ //DONE
		if (this.status != 200)		{
			/* If the connection is ready but not 200, return as error (this behaviour is unexpected). */
			if (this.onError != null) {this.onError(this);}
		}else{
			this.JSON = JSON.parse(this.response);
			if ('error' in this.JSON){
				/*If the response contain error, put on variables and return as error.*/
				this.HTTPStatus = this.JSON['error']['code'];
				this.HTTPStatusText = this.JSON['error']['status'];
				if (this.onError != null) {this.onError(this);}
			}else{
				this.HTTPStatus = 200
				if (this.onSuccess != null) {this.onSuccess(this);}
			}
		}
	}
}