<?php
include_once 'totp.php';

function generate_auth_key($key, $ttl, $iterations)
{
    $buffer = '';
    for ( $i = 1; $i<=$iterations; $i++)
        {$buffer = $buffer.dechex(generate_totp($key.$i, 'sha1', 10, $ttl));}
    return $buffer;
}

# Function to validate if a bearer is correct or not (if not exists is wrong).
function authorization_bearer_is_valid($headers, $bearer)
{
    # Split authorization header by the first space.
    $authorization = explode(" ",$headers['Authorization'], 2);
    
    # Only bearer type is allowed.
    if (strtolower($authorization[0]) != 'bearer') {return false;}

    # Return if authorization bearer supplied is equal to the computed bearer.
    return $authorization[1] == $bearer;
}

?>