<?php
   
# Set CORS headers if $CORS_Origin variable is set.
if ( $CORS_Origin != '' )
{
    header("Access-Control-Allow-Origin: ".$CORS_Origin);
}

# If api_key variable is not set, don't use autentication or authorization 
# for backward compatibility.
if ( $api_key != '' )
{

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
}
?>