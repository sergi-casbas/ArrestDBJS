<?php
# If api_key variable is not set , don't use autentication or sessions for backward compatibility.
if ( $api_key != '' )
{
    # Manage sessions through PHP session management.
    ini_set("session.cookie_lifetime","120"); //DEBUG MODE
    session_start();

    # Split authorization header by the first space.
    $headers = apache_request_headers();
    $header = strtolower($headers['Authorization']);
    $authorization = explode(" ", $header, 2);

    # If the session is open but authorization empty, abort session.
    if  ($_SESSION['authorized'] == True){
        if ( key_exists('Authorization', $headers) ){
            if ($authorization[1] == "") {# An empty authorization means logout.
                $_SESSION = array();
                unset($_SESSION);
                session_destroy();
                header('x-auth-message: logout');
                exit(ArrestDB::Reply(ArrestDB::$HTTP[200]));
            }else{
                header('x-auth-message: not required');
            }
        }else{
            header('x-auth-message: in session');
            # if url is empty, is a keep alive message return 200.
            if ( $_SERVER['REQUEST_URI']=="/" ){
                exit(ArrestDB::Reply(ArrestDB::$HTTP[200]));
            }


        }
    }else{ # If is not authorized validate authorization header.
        # An empty Authorization header means close session.
        if (!key_exists('Authorization', $headers))
        {
            header('x-auth-message: authorization missing');
            session_abort();
            exit(ArrestDB::Reply(ArrestDB::$HTTP[403]));
        }        

        # Only bearer type is allowed.
        if ( $authorization[0] != 'bearer'){
            header('x-auth-message: wrong format');
            session_abort();
            exit(ArrestDB::Reply(ArrestDB::$HTTP[403]));
        }

        # Compare bearer with api key
        if ( $authorization[1] != strtolower($api_key)){
            header('x-auth-message: wrong key');
            session_abort();
            exit(ArrestDB::Reply(ArrestDB::$HTTP[403]));
        }

        # If we don't have previous error, start local session and return ok.
        $_SESSION['authorized'] = True;
        header('x-auth-message: successfull');
        exit(ArrestDB::Reply(ArrestDB::$HTTP[200]));
    }
}

?>