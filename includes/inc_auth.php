<?php

global $user, $pass;

session_start();

if (isset($_COOKIE['istream']))
{
        $authorized=true;
}

# checkup login and password
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
{
    if (($user == $_SERVER['PHP_AUTH_USER']) && ($pass == ($_SERVER['PHP_AUTH_PW'])) && isset($_SESSION['auth']))
    {
    $authorized = true;
    setcookie ("istream", "true", time()+60*60*24*30);
    }
}

# login
if (!$authorized)
{
    header('WWW-Authenticate: Basic Realm="Login please"');
    header('HTTP/1.0 401 Unauthorized');
    $_SESSION['auth'] = true;
    echo "Login";
    exit;
}

?>
