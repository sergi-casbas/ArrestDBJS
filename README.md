# PHPDBAPI Suite

PHPDBAPI is a "plug-n-play" RESTful API for SQLite, MySQL and PostgreSQL databases with a Vanilla JS library to easy interact with it from http pages.

## JS Library
With this library you don't neet do deal directly with the API to use it in your web pages. The library maps all internall functions and parameters to do with a single function call all required operations (prepare data, send http, manage errors, and handle asyncronous function calls).

The library supports both multiplexing and autentication, wich makes your connections more secure and flexible over internet.

Library API mapped available functions:
* create(serverURL, tableName, itemJSON, successFunction, failFunction)
* read(serverURL, tableName, itemId, successFunction, failFunction)
* readAll(serverURL, tableName, successFunction, failFunction)
* update(serverURL, tableName, itemId, itemJSON, successFunction, failFunction)
* remove(serverURL, tableName, itemId, successFunction, failFunction)

Multiplexing functions:
* openDatabase(databaseName)
* closeDatabase()

Autentication functions:
* autenticate(serverURL, method, apikey, successFunction, failFunction)
* deautenticate()
* keepAlive(successFunction, failFunction)


## PHPAPI
The API is based on ArrestDB by alixaxel (https://github.com/alixaxel/ArrestDB) and only have added the modules inclusion.
