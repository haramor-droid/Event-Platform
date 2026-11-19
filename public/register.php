<?php
require_once __DIR__ . '/../config/database.php';

// Categories for the interest chips, in table order (same order everywhere)
$categories = $pdo->query(
  'SELECT category_id, name FROM categories WHERE is_active = 1 ORDER BY category_id'
)->fetchAll();
$validIds = array_map('intval', array_column($categories, 'category_id'));

$message     = '';
$fullName    = '';
$email       = '';
$accountType = '';
$orgName     = '';
$interests   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullName    = trim($_POST['full_name'] ?? '');
  $email       = trim($_POST['email'] ?? '');
  $password    = $_POST['password'] ?? '';
  $confirm     = $_POST['confirm_password'] ?? '';
  $accountType = $_POST['account_type'] ?? '';
  $orgName     = trim($_POST['org_name'] ?? '');

  // Keep only real category IDs 
  $interests = array_map('intval', (array) ($_POST['interests'] ?? []));
  $interests = array_values(array_unique(array_intersect($interests, $validIds)));

  if ($fullName === '' || mb_strlen($fullName) > 255) {
    $message = 'Please enter your full name.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
    $message = 'Invalid email (max 100 characters).';}
  elseif (strlen($password) < 8 ){
  $message = 'Password must be at least 8 characters.';
}
  elseif(strlen($password)>72){
    $message = 'Password must not be greater 72 characters';}

   elseif ($password !== $confirm) {
    $message = 'Passwords do not match.';
  } elseif (!in_array($accountType, ['attendee', 'organiser'], true)) {
    $message = 'Please choose a role.';
  } elseif ($accountType === 'organiser'
            && ($orgName === '' || mb_strlen($orgName) > 150)) {
    $message = 'Organisers must enter an organisation name (max 150 characters).';
  } else {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
      $pdo->beginTransaction();

      $stmt = $pdo->prepare(
        'INSERT INTO users (email, full_name, password_hash, role) VALUES (?, ?, ?, ?)'
      );
      $stmt->execute([$email, $fullName, $hash, $accountType]);
      $userId = (int) $pdo->lastInsertId();

      if ($accountType === 'organiser') {
        $stmt = $pdo->prepare(
          'INSERT INTO organiser_profiles (user_id, org_name) VALUES (?, ?)'
        );
        $stmt->execute([$userId, $orgName]);
      } elseif ($interests) {
        $stmt = $pdo->prepare(
          'INSERT INTO user_interests (user_id, category_id) VALUES (?, ?)'
        );
        foreach ($interests as $categoryId) {
          $stmt->execute([$userId, $categoryId]);
        }
      }

      $pdo->commit();
      header('Location: login.php?registered=1');
      exit;
    } catch (PDOException $e) {
      if ($pdo->inTransaction()) {
        $pdo->rollBack();
      }
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
  <h1>Register</h1>

  <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>

  <form method="post">
    <label for="full_name">Full name</label>
    <input type="text" id="full_name" name="full_name" maxlength="255"
           autocomplete="name" required value="<?= htmlspecialchars($fullName) ?>">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" maxlength="100"
           autocomplete="email" required value="<?= htmlspecialchars($email) ?>">

    <label for="password">Password <small>(minimum 8 characters)</small></label>
    <input type="password" id="password" name="password" minlength="8" maxlength="72"
           autocomplete="new-password" aria-describedby="pw-hint" required>

    <label for="confirm_password">Confirm password</label>
    <input type="password" id="confirm_password" name="confirm_password"
           minlength="8" maxlength="72" autocomplete="new-password" required>

    <label for="account_type">Role</label>
    <select id="account_type" name="account_type" required>
      <option value="">Choose a role</option>
      <option value="attendee" <?= $accountType === 'attendee' ? 'selected' : '' ?>>Attendee</option>
      <option value="organiser" <?= $accountType === 'organiser' ? 'selected' : '' ?>>Organiser</option>
    </select>

    <!-- Shown only for attendees -->
    <fieldset id="interests-box">
      <legend>What are you interested in? (pick a few, optional)</legend>
      <?php foreach ($categories as $c): ?>
        <label>
          <input type="checkbox" name="interests[]" value="<?= (int) $c['category_id'] ?>"
                 <?= in_array((int) $c['category_id'], $interests, true) ? 'checked' : '' ?>>
          <?= htmlspecialchars($c['name']) ?>
        </label>
      <?php endforeach; ?>
    </fieldset>

    <!-- Shown only for organisers -->
    <div id="org-box">
      <label for="org_name">Organisation name</label>
      <input type="text" id="org_name" name="org_name" maxlength="150"
             value="<?= htmlspecialchars($orgName) ?>">
    </div>

    <button>Register</button>
  </form>

  <p>Have an account? <a href="login.php">Log in</a></p>

  <script>
    // Show the interests for attendees and the organisation name for organisers.
    // The server ignores whichever one doesn't apply, so this is only for the layout.
    var role = document.getElementById('account_type');
    var interestsBox = document.getElementById('interests-box');
    var orgBox = document.getElementById('org-box');

    function toggleSections() {
      interestsBox.hidden = role.value !== 'attendee';
      orgBox.hidden = role.value !== 'organiser';
    }

    role.addEventListener('change', toggleSections);
    toggleSections();
  </script>
</body>
</html>