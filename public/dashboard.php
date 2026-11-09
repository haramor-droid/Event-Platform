<?php
session_set_cookie_params([
    'httponly'=> true,
    'samesite' => 'Lax',
    'secure'=> !empty($_SERVER['HTTPS']),

]);

session_start();
require_once __DIR__ .'/../config/database.php';

//Guard: not logged in, so go to the login page

if(!isset($_SESSION['user_id'])){
    header('Location : login.php');
    exit;
}

//lod the user from the database (font trust the session for details)

$stmt=$pdo->prepare(
    'SELECT full_name, role, account_status FORM users WHERE user_id=?'
);

$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// If the user was deleted or suspended since logging in, end the session
if($user || $user['account_status'] !== 'active' ){
   header('Location : logout.php');
   exit;
}

?>
<!DOCTYPE html>
<html lang ="en">
    <head>
        <meta charset="utf-8">
        <meta name = "viewport" content= "width=device-width, initial-sacel=1">
        <title>Dashboard</title>
    </head>
    <body>
        <h1>Welcome, <?= htmlspecialchars($user['full_name']) ?></h1>
        <p>Your role <?= htmlspecialchars($user['role']) ?></p>
        <p><a href="logout.php">Log out</a></p>
    </body>

</html>