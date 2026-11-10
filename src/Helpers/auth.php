<?php
 // This file is a helper that holds the login checks in one place, so every page doesnt repeat them

 function start_secure_session():void {
  if(session_status()=== PHP_SESSION_NONE){
  session_set_cookie_params([
    'httponly'=> true,
    'samesite'=> 'Lax',
    'path'=> !empty($_SERVER['HTTPS']),
  ]);
  session_start();
  }

 }

 //Returns the logged-in users details form the database or null if nobody is logged in
 function current_user(PDO $pdo): ?array{
    start_secure_session();
    if(!isset($_SESSION['user_id'])){
        return null;
    }
    $stmt = $pdo->prepare(
        'SELECT user_id, full_name,email,role, account_status FROM users WHERE user_id = ? ');

        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        return $user ?: null;
 }
 // Use at the top of any page that needs a login. Returns the user.
 function require_login(PDO $pdo): array {
   $user=current_user($pdo);
   if($user === null){
    header('Location: login.php');
    exit;
   }
// Deleted or suspended since logging in: end the session
if($user['account_status'] !== 'active'){
    header('Location: logout.php');
    exit;
}
return $user;

 }
 // Use on role-restricted pages, e.g $user = require_role($pdo, 'organise','admin');
 function require_role(PDO $pdo, string ...$roles): array {
    $user = require_login($pdo);

    if(!in_array($user['role'],$roles, true)) {
        http_response_code(403);
        exit('Access denied.');
    }
    return $user;

 }


?>