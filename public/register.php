<?php
require_once __DIR__ . '/../config/database.php';

$message     = '';
$fullName    = '';
$email       = '';
$accountType = 'attendee';          // NEW: default choice
$orgName ='';      //New needs to match database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullName    = trim($_POST['full_name'] ?? '');
  $email       = trim($_POST['email'] ?? '');
  $password    = $_POST['password'] ?? '';
  $accountType = $_POST['account_type'] ?? 'attendee';   // NEW: read from the form
  $orgName     = trim($_POST['org_name']?? ''); //NEW

  if ($fullName === '' || mb_strlen($fullName) > 255) {
    $message = 'Please enter your full name.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
    $message = 'Invalid email (max 100 characters).';
  } elseif (strlen($password) < 8 || strlen($password) > 72) {
    $message = 'Password must be 8 to 72 characters.';
  } elseif (!in_array($accountType, ['attendee', 'organiser'], true)) {   // NEW: the elseif
    $message = 'Please choose a valid account type.';
  } elseif($accountType === 'orgganiser' 
                                     && ($orgName === '' || mb_strlen($orgName)>150)) {
                        $message = 'Organisers must enter an organisation name (max 150 characters).';
                                     }// New
  
  else {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
      $pdo->beginTransaction();                                          // NEW

      $stmt = $pdo->prepare(
        'INSERT INTO users (email, full_name, password_hash, role) VALUES (?, ?, ?, ?)'
      );
      $stmt->execute([$email, $fullName, $hash, $accountType]);          // CHANGED: role added

      if ($accountType === 'organiser') {                                // NEW
        $userId = (int) $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO organiser_profiles (user_id, org_name) VALUES (?,?)');// Changed
        $stmt->execute([$userId,$orgName]);
      }

      $pdo->commit();                                                    // NEW
      header('Location: login.php?registered=1');
      exit;
    } catch (PDOException $e) {
      if ($pdo->inTransaction()) {                                       // NEW
        $pdo->rollBack();
      }
      if ((int) $e->errorInfo[1] === 1062) {
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

    <!-- NEW: account type choice -->
    <fieldset>
      <legend>I am registering as</legend>
      <label><input type="radio" name="account_type" value="attendee"
             <?= $accountType === 'attendee' ? 'checked' : '' ?>> Attendee</label>
      <label><input type="radio" name="account_type" value="organiser"
             <?= $accountType === 'organiser' ? 'checked' : '' ?>> Organiser</label>
    </fieldset>

    <!-- New: This is for the organisers ONLY -->
     <label for="org_name">Organisation name (organisers only)</label>
     <input type="text" id ="org_name" name= "org_name" maxlength = "150" value= "<?= htmlspecialchars($orgName) ?>" >

    <button>Register</button>
  </form>
</body>
</html>