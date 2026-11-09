<?php
 session_set_cookie_params([
 'httponly' => true,
 'samesite' => 'Lax',
 'secure' => !empty($_SERVER['HTTPS']),

 ]);
 
 session_start();
 require_once __DIR__ .'/../config/database.php';

 $message ='';
 $email ='';

 if(isset($_GET['registered'])){
    $message = 'Registered! You can now log in.';
 }

 if($_SERVER['REQUEST_METHOD']==='POST'){
    $email   =trim($_POST['email']?? '');
    $password  =$_POST['password'] ?? '';

    $stmt =$pdo->prepare(
        'SELECT user_id,password_hash,account_status FROM users WHERE email= ?'
    );

    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user && password_verify($password,$user['password_hash'])){
    if($user['account_status'] !=='active'){$message = 'This account is not available.';}

    else{
        session_regenerate_id(true);
        $SESSION['user_id'] = (int) $user['user_id'];
        header('Location: dashboard.php');
        exit;
    }
    }
    else {$message = 'Invalid email or password. ';}

  }
 
 ?>
 <!DOCTYPE html>
<html lang="end">
<head>
 <meta charset= "utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>Log in </title>
</head>
 <body>
   <?php if ($message): ?><p<?= htmlspecialchars($message) ?></p><?php endif; ?>

    <form method="post">
        <label for="email">
          <input type="email" id="email" maxlength="100" autocomplete="email" required value="<?=  htmlspecialchars($email) ?>">
          <label for="password">Password</label>
          <input type="password" name="password" maxlength="72" autocomplete="current-password" required>

          <button>Log in</button>
          
    
    </form>

 </body>
</html>