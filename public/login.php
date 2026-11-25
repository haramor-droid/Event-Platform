<?php
require_once __DIR__ . '/../config/database.php'; // brings database (to use PDO)
require_once __DIR__ . '/../src/Helpers/auth.php';

// Note: start_secure_session() handles session_start() and the cookie parameters 
// (httponly for XSS prevention, samesite=Lax for CSRF prevention, secure for HTTPS).
start_secure_session(); 

// Initializing
$message = '';
$email   = '';

// Does this URL parameter exist? (e.g., login.php?registered=1)
if (isset($_GET['registered'])) {
  $message = 'Registered! You can now log in.';
}

// Was the form actually submitted?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  // trim removes accidental spaces from the beginning and end, grab what the user posted
  // ?? '', NULL coalescing operator: whether it's empty or missing, use as an empty string
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  // Asking the database to prepare a query, a query of all these details of a user with this email
  // (right now it's a placeholder '?')
  $stmt = $pdo->prepare(
    'SELECT user_id, password_hash, account_status FROM users WHERE email = ?'
  );
  
  // Placeholder replaced
  $stmt->execute([$email]); 
  
  // Grabs the result
  $user = $stmt->fetch(); 

  // Checking user's credentials: Does the password hash match the password?
  if ($user && password_verify($password, $user['password_hash'])) {
    
    if ($user['account_status'] !== 'active') {
      $message = 'This account is not available.';
    } else {
      // Regenerate session ID to prevent session fixation attacks
      session_regenerate_id(true);
      
      // Remember the user when they are logged in
      $_SESSION['user_id'] = (int) $user['user_id'];
      
      header('Location: dashboard.php');
      exit;
    }
  } else {
    $message = 'Invalid email or password.';
  }
}

$pageTitle = 'Log in';
require __DIR__ . '/../src/Views/header.php'; // Load the new Bootstrap header
?>

<h1 class="h3 mb-3">Log in</h1>

<?php if ($message): ?>
  <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="post">
  <div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" class="form-control" id="email" name="email" maxlength="100"
           autocomplete="email" required value="<?= htmlspecialchars($email) ?>">
  </div>
  
  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" id="password" name="password"
           maxlength="72" autocomplete="current-password" required>
  </div>
  
  <button class="btn btn-dark w-100">Log in</button>
</form>

<p class="mt-3">No account? <a href="register.php">Register</a></p>

<?php require __DIR__ . '/../src/Views/footer.php'; // Load the new Bootstrap footer ?>