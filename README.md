#PHPDBAPI Suite

PHPDBAPI is a "plug-n-play" RESTful API for SQLite, MySQL and PostgreSQL databases with a Vanilla JS library to easy interact with it from http pages.

This is based on ArrestDB by alixaxel (https://github.com/alixaxel/ArrestDB).

PHPDBAPI provides a REST API that maps directly to your database stucture with no configuation.

Lets suppose you have set up PHPDBAPI at `http://api.example.com/` and that your database has a table named `customers`.
To get a list of all the customers in the table you would simply need to do:

	GET http://api.example.com/customers/

As a response, you would get a JSON formatted list of customers.

Or, if you only want to get one customer, then you would append the customer `id` to the URL:

	GET http://api.example.com/customers/123/

##Requirements

- PHP 5.4+ & PDO
- SQLite / MySQL / PostgreSQL

##Installation (single database)

Edit `index.php` and change the `$dsn` variable located at the top, here are some examples:

- SQLite: `$dsn = 'sqlite://./path/to/database.sqlite';`
- MySQL: `$dsn = 'mysql://[user[:pass]@]host[:port]/db/;`
- PostgreSQL: `$dsn = 'pgsql://[user[:pass]@]host[:port]/db/;`

If you want to restrict access to allow only specific IP addresses, add them to the `$clients` array:

```php
$clients = array
(
	'127.0.0.1',
	'127.0.0.2',
	'127.0.0.3',
);
```

After you're done editing the file, place it in a public directory (feel free to change the filename).

If you're using Apache, you can use the following `mod_rewrite` rules in a `.htaccess` file:

```apache
<IfModule mod_rewrite.c>
	RewriteEngine	On
	RewriteCond		%{REQUEST_FILENAME}	!-d
	RewriteCond		%{REQUEST_FILENAME}	!-f
	RewriteRule		^(.*)$ index.php/$1	[L,QSA]
</IfModule>
```

***Nota bene:*** You must access the file directly, including it from another file won't work.

##API Design

The actual API design is very straightforward and follows the design patterns of the majority of APIs.

	(C)reate > POST   /table
	(R)ead   > GET    /table[/id]
	(R)ead   > GET    /table[/column/content]
	(U)pdate > PUT    /table/id
	(D)elete > DELETE /table/id

To put this into practice below are some example of how you would use the PHPDBAPI API:

	# Get all rows from the "customers" table
	GET http://api.example.com/customers/

	# Get a single row from the "customers" table (where "123" is the ID)
	GET http://api.example.com/customers/123/

	# Get all rows from the "customers" table where the "country" field matches "Australia" (`LIKE`)
	GET http://api.example.com/customers/country/Australia/

	# Get 50 rows from the "customers" table
	GET http://api.example.com/customers/?limit=50

	# Get 50 rows from the "customers" table ordered by the "date" field
	GET http://api.example.com/customers/?limit=50&by=date&order=desc

	# Create a new row in the "customers" table where the POST data corresponds to the database fields
	POST http://api.example.com/customers/

	# Update customer "123" in the "customers" table where the PUT data corresponds to the database fields
	PUT http://api.example.com/customers/123/

	# Delete customer "123" from the "customers" table
	DELETE http://api.example.com/customers/123/

Please note that `GET` calls accept the following query string variables:

- `by` (column to order by)
  - `order` (order direction: `ASC` or `DESC`)
- `limit` (`LIMIT x` SQL clause)
  - `offset` (`OFFSET x` SQL clause)

Additionally, `POST` and `PUT` requests accept JSON-encoded and/or zlib-compressed payloads.

> `POST` and `PUT` requests are only able to parse data encoded in `application/x-www-form-urlencoded`.
> Support for `multipart/form-data` payloads will be added in the future.

If your client does not support certain methods, you can use the `X-HTTP-Method-Override` header:

- `PUT` = `POST` + `X-HTTP-Method-Override: PUT`
- `DELETE` = `GET` + `X-HTTP-Method-Override: DELETE`

Alternatively, you can also override the HTTP method by using the `_method` query string parameter.

Since 1.5.0, it's also possible to atomically `INSERT` a batch of records by POSTing an array of arrays.

##Multiplexing (Multiple databases)

Since 2.0.0 it's possible to acces more than one database using the same server installation. 


##Autentication and authorization

Since 2.0.0 it's possible to use autentication and authorization bearers to be sure that the access to your 
database is only done by a trusted source. The used tokens are stateless and don't require to be stored on 
anywhere.

To use it fill the variable $api_key (directly on index.php or in a multiplexing config file) with the secret
to exchange with your client. 

The autentication workflow is:
- Client send Apikey header with the $api_key config value.
- If is correct, Server Send a header with "Bearer tokenxxxxxxx".
- During the lifecycle (1h by default) of the token no need to send Apikey again is required.
- If a 'Authorization: autentication fail' header is recivied means error during autentication.
- If a 'Authorization: invalid' header is recived means invalid or inexistent token.


##Responses

All responses are in the JSON format. A `GET` response from the `customers` table might look like this:

```json
[
    {
        "id": "114",
        "customerName": "Australian Collectors, Co.",
        "contactLastName": "Ferguson",
        "contactFirstName": "Peter",
        "phone": "123456",
        "addressLine1": "636 St Kilda Road",
        "addressLine2": "Level 3",
        "city": "Melbourne",
        "state": "Victoria",
        "postalCode": "3004",
        "country": "Australia",
        "salesRepEmployeeNumber": "1611",
        "creditLimit": "117300"
    },
    ...
]
```

Successful `POST` responses will look like:

```json
{
    "success": {
        "code": 201,
        "status": "Created"
    }
}
```

Successful `PUT` and `DELETE` responses will look like:

```json
{
    "success": {
        "code": 200,
        "status": "OK"
    }
}
```

Errors are expressed in the format:

```json
{
    "error": {
        "code": 400,
        "status": "Bad Request"
    }
}
```

The following codes and message are avaiable:

* `200` OK
* `201` Created
* `204` No Content
* `400` Bad Request
* `403` Forbidden
* `404` Not Found
* `409` Conflict
* `503` Service Unavailable

Also, if the `callback` query string is set *and* is valid, the returned result will be a [JSON-P response](http://en.wikipedia.org/wiki/JSONP):

```javascript
callback(JSON);
```

Ajax-like requests will be minified, whereas normal browser requests will be human-readable.

##Changelog

- **2.0.0** ~~added multiplexing and autentication.
- **1.2.0** ~~support for JSON payloads in `POST` and `PUT` (optionally gzipped)~~
- **1.3.0** ~~support for JSON-P responses~~
- **1.4.0** ~~support for HTTP method overrides using the `X-HTTP-Method-Override` header~~
- **1.5.0** ~~support for bulk inserts in `POST`~~
- **1.6.0** ~~added support for PostgreSQL~~
- **1.7.0** ~~fixed PostgreSQL connection bug, other minor improvements~~
- **1.8.0** ~~fixed POST / PUT bug introduced in 1.5.0~~
- **1.9.0** ~~updated to PHP 5.4 short array syntax~~

##Credits

PHPDBAPI is a complete rewrite of [Arrest-MySQL](https://github.com/gilbitron/Arrest-MySQL) with several optimizations and additional features.

##License (MIT)

Copyright (c) 2014 Alix Axel (alix.axel+github@gmail.com).
