<?php
# Set CORS headers if $CORS_Origin variable is set.
if ( $CORS_Origin != '' )
{
    header("Access-Control-Allow-Origin: ".$CORS_Origin);
}
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE");
header("Access-Control-Expose-Headers: *, Authorization");
?>