<?php
require __DIR__ . '/../config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullName = trim($_POST['full_name'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($fullName === '' || strlen($fullName) > 255) {
    $message = 'Please enter your full name.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    $message = 'Invalid email (max 100 characters).';   // column is VARCHAR(100)
  } elseif (strlen($password) < 8) {
    $message = 'Password must be at least 8 characters.';
  } else {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
      // role and account_status are left out on purpose: the DB defaults
      // give every new user 'attendee' and 'active'.
      $stmt = $pdo->prepare(
        'INSERT INTO users (email, full_name, password_hash) VALUES (?, ?, ?)'
      );
      $stmt->execute([$email, $fullName, $hash]);
      $message = 'Registered! You can now log in.';
    } catch (PDOException $e) {
      if ($e->getCode() === '23000') {     // violates uq_users_email
        $message = 'That email is already registered.';
      } else {
        throw $e;
      }
    }
  }
}
?>
<?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
<form method="post">
  <input type="text" name="full_name" placeholder="Full name" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" minlength="8" placeholder="Password" required>
  <button>Register</button>
</form>