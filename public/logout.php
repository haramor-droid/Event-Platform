<?php
 session_start();

 // Clearing all session data
 $_SESSION=[];

 //Delete the session cookie in the browser

 if(ini_get('session.use_cookies')){
    $p = session_get_cookie_params();
    setcookie(session_name(), '',[
       'expires'=> time() -42000,
       'path' => $p['path'],
       'domain' => $p['domain'],
       'secure' => $p['secure'],
       'httponly' => $p['httponly'],
       'samesite'=> 'Lax',
    ]);
 }

 // End the session on the server, then go back to the login
 session_destroy();
 header('Location: login.php');
 exit;