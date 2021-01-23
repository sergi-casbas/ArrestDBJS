<?php
# Databases and dsn files path, by default out of public path.
define("DATABASE_ROOT", "/../databases/");

# $dsn and $client are moved to <dbid>/dns.php file for each database.
# if you don't want to use multiplexer, put a dns.php file on ./0/
$dbid = strtolower(apache_request_headers()['Database']); 

# Only allow lowercase letters, numbers and dot or underscore.
if (!preg_match('/[^a-z0-9._]/', $dbid))
{
	$dbpath = $_SERVER['DOCUMENT_ROOT'].DATABASE_ROOT.$dbid;
}else{
	exit(ArrestDB::Reply(ArrestDB::$HTTP[403]));
}

# If the DB dsn file dosn't exists thrown an error else include database dns / hosts configurations.
if (!file_exists( $dbpath.'/config.php')) {
	exit(ArrestDB::Reply(ArrestDB::$HTTP[503]));
}else{
	include_once $dbpath.'/config.php';
}
?>