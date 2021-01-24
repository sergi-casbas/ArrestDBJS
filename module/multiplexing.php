<?php
# Databases and dsn files path, by default out of public path.
define("MULTIPLEX_ROOT", "/multiplexing/");

# $dsn and $client are moved to <dbid>/dns.php file for each database for security reasons.
$dbid = strtolower(apache_request_headers()['Database']); 

# Only process multiplexing if a database header is sent making backward compatible.
if ( $dbid != '' )
{
	# Only allow lowercase letters, numbers and dot or underscore.
	if (!preg_match('/[^a-z0-9._]/', $dbid))
	{
		$multiplexer = $_SERVER['DOCUMENT_ROOT'].MULTIPLEX_ROOT.$dbid.'.php';
	}else{
		exit(ArrestDB::Reply(ArrestDB::$HTTP[403]));
	}

	# If the DB dsn file dosn't exists thrown an error else include database dns / hosts configurations.
	if (!file_exists( $multiplexer)) {
		exit(ArrestDB::Reply(ArrestDB::$HTTP[503]));
	}else{
		include_once $multiplexer;
	}
}
?>