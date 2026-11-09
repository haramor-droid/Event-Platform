<?php
require_once __DIR__ . '/../config/database.php';

$message  = '';
$fullName = '';
$email    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullName = trim($_POST['full_name'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($fullName === '' || mb_strlen($fullName) > 255) {
    $message = 'Please enter your full name.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
    $message = 'Invalid email (max 100 characters).';
  } elseif (strlen($password) < 8 || strlen($password) > 72) {
    $message = 'Password must be 8 to 72 characters.';
  } else {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
      $stmt = $pdo->prepare(
        'INSERT INTO users (email, full_name, password_hash) VALUES (?, ?, ?)'
      );
      $stmt->execute([$email, $fullName, $hash]);

      header('Location: login.php?registered=1');
      exit;
    } catch (PDOException $e) {
      if ((int) $e->errorInfo[1] === 1062) {      // duplicate email only
        $message = 'That email is already registered.';
      } else {
        throw $e;
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
</head>
<body>
  <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>

  <form method="post">
    <label for="full_name">Full name</label>
    <input type="text" id="full_name" name="full_name" maxlength="255"
           autocomplete="name" required value="<?= htmlspecialchars($fullName) ?>">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" maxlength="100"
           autocomplete="email" required value="<?= htmlspecialchars($email) ?>">

    <label for="password">Password</label>
    <input type="password" id="password" name="password" minlength="8"
           maxlength="72" autocomplete="new-password" required>

    <button>Register</button>
  </form>
</body>
</html> 