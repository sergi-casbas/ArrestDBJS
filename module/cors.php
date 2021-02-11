<?php
# WARNING: Enabling CORS module can allow any malicious web page 
# read information from your database. Use it with caution.
# More info on: https://en.wikipedia.org/wiki/Cross-origin_resource_sharing

# Set CORS headers if $CORS_Origin variable is set.
if ( $CORS_Origin != '' )
{
    header("Access-Control-Allow-Origin: ".$CORS_Origin);
}
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE");
header("Access-Control-Expose-Headers: *, Authorization");
?>