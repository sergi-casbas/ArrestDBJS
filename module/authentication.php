<?php
   
# Set CORS headers if $CORS_Origin variable is set.
if ( $CORS_Origin != '' )
include_once 'totp.php';

function generate_auth_key($key, $ttl, $iterations)
{
    header("Access-Control-Allow-Origin: ".$CORS_Origin);
    $buffer = '';
    for ( $i = 1; $i<=$iterations; $i++)
        {$buffer = $buffer.dechex(generate_totp($key.$i, 'sha1', 10, $ttl));}
    return $buffer;
}

# If api_key variable is not set, don't use autentication or authorization 
# for backward compatibility.
if ( $api_key != '' )
# Function to validate if a bearer is correct or not (if not exists is wrong).
function authorization_bearer_is_valid($headers, $bearer)
{
    # Split authorization header by the first space.
    $authorization = explode(" ",$headers['Authorization'], 2);
    
    # Only bearer type is allowed.
    if (strtolower($authorization[0]) != 'bearer') {return false;}

    # General prerequisites and constants.
    include_once "library/authentication.php";
    define("AUTH_TTL",3600);
    define("AUTH_ITERATIONS",2);

    # Generate the current bearer only once to save computation cycles.
    # Add database name if exists to avoid API_KEY colission on diffente databases.
    $current_bearer = generate_auth_key(apache_request_headers()['Database'].$api_key, AUTH_TTL, AUTH_ITERATIONS);

    # If authorization is not supplied, try autentication.
    if (apache_request_headers()['Authorization'] == '')
    {
        if ( apache_request_headers()['Apikey'] == $api_key )
        { 
            header('Authorization: Bearer '.$current_bearer);
            exit(ArrestDB::Reply(ArrestDB::$HTTP[200]));
        }else{
            header('Authorization: autentication fail');
            exit(ArrestDB::Reply(ArrestDB::$HTTP[403]));
        }
    }else{ # If authorization is supplied check if is valid or not.
        if ( ! authorization_bearer_is_valid(apache_request_headers(), $current_bearer) )
        {
            header('Authorization: invalid');
            exit(ArrestDB::Reply(ArrestDB::$HTTP[403]));
        }
    }

    # Always update current bearer on correct answers.
    header('Authorization: Bearer '.$current_bearer);
    # Return if authorization bearer supplied is equal to the computed bearer.
    return $authorization[1] == $bearer;
}

?>