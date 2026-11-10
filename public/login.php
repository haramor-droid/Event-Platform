<?php
// Settlong the rules for the cookie, literally cookie parameters
  session_set_cookie_params([
'httponly' => true, //Js cannot read this cookie(prevention of XSS attacks)
'samesite' => 'Lax', //Helps prevent csfr
'secure' => !empty($_SERVER['HTTPS']), //Only sends the Cookie over to HTTPS if the connection is secure

  ]);
// session cookies are what we use to remember the user when they are logged in.Each with a unique id

  session_start();
  require_once __DIR__ . '/../config/database.php';// brings database(to use pdo)

  //Initializing
  $message ='';
  $email='';
// Does this URL exist?
  if (isset($_GET['registered'])){
    $message='Registered! You can now log in';
  }
//
  if($_SERVER['REQUEST_METHOD'] ==='POST'){ //was the form actually submitted?
   $email = trim($_POST['email']?? ''); //trim removes accidental spaces from the begging to end,grab what the user posted
   $password = $_POST['password'] ?? ''; // ?? '', NULL coalescing operator: whether its empty or missing usse as an empty string
          
   // Asking the database to prepare a query, a query of all these details of a user w this email(rn its a placeholder)
      $stmt =$pdo->prepare('SELECT user_id, password_hash, account_status FROM users WHERE email= ?');

      $stmt->execute([$email]); //placeholder replaced
      $user = $stmt->fetch();// grabs the result

      //Checking users credentials
      //Does the password hash matches the password

      if ($user && password_verify($password, $user['password_hash'])){
        if($user['account_status']!=='active'){
            $message= 'This account is not available. ';
        }
        else{
            session_regenerate_id(true);
            $_SESSION['user_id']=(int) $user['user_id'];
            header('Location: dashboard.php');
            exit;
        }
        }
        else {
            $message= 'Invalid email or password';
        }
      };

  ?>
  <!DOCTYPE html>
  <html lang="en" >
    <head >
          <meta charset="utf-8">
          <meta name= "viewport" content ="width= device-width, initial-scale=1">
          <title>Log in</title>

    </head>
    <body>
        <?php if($message): ?><p><?= htmlspecialchars($message)?></p><?php endif; ?>
            
        <form method="post">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" maxlength="100" autocomplete="email" required value="<?= htmlspecialchars($email) ?>">
    
    <label for="password">Password</label>
    <input type="password" id="password" name="password" maxlength="72" autocomplete="current-password" required>

    <button>Log in</button>
</form>
          
    </body>
  </html>