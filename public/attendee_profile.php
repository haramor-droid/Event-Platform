<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Helpers/auth.php';

$user = require_login($pdo);
if ($user['role'] === 'organiser') {
  redirect('org_profile.php');
}
if ($user['role'] !== 'attendee') {
  http_response_code(403);
  exit('Access denied.');
}
$userId = (int) $user['user_id'];

// Interest chips, in table order (same order everywhere)
$categories = $pdo->query(
  'SELECT category_id, name FROM categories WHERE is_active = 1 ORDER BY category_id'
)->fetchAll();
$validIds = array_map('intval', array_column($categories, 'category_id'));

$stmt = $pdo->prepare('SELECT category_id FROM user_interests WHERE user_id = ?');
$stmt->execute([$userId]);
$selected = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

$fullName = $user['full_name'];
$email    = $user['email'];
$errors   = ['account' => '', 'password' => '', 'interests' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $section = post_str('section');

  if ($section === 'account') {
    $fullName = trim(post_str('full_name'));
    $email    = trim(post_str('email'));

    if ($fullName === '' || mb_strlen($fullName) > 255) {
      $errors['account'] = 'Please enter your full name.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
      $errors['account'] = 'Invalid email (max 100 characters).';
    } else {
      try {
        $stmt = $pdo->prepare('UPDATE users SET full_name = ?, email = ? WHERE user_id = ?');
        $stmt->execute([$fullName, $email, $userId]);
        flash_set('account', 'Account details saved.');
        redirect('profile.php');
      } catch (PDOException $e) {
        if ((int) $e->errorInfo[1] === 1062) {
          $errors['account'] = 'That email is already registered.';
        } else {
          throw $e;
        }
      }
    }

  } elseif ($section === 'password') {
    $errors['password'] = change_password(
      $pdo, $userId,
      post_str('current_password'), post_str('new_password'), post_str('confirm_password')
    );
    if ($errors['password'] === '') {
      flash_set('password', 'Password changed.');
      redirect('profile.php');
    }

  } elseif ($section === 'interests') {
    $selected = array_map('intval', (array) ($_POST['interests'] ?? []));
    $selected = array_values(array_unique(array_intersect($selected, $validIds)));

    try {
      $pdo->beginTransaction();
      $pdo->prepare('DELETE FROM user_interests WHERE user_id = ?')->execute([$userId]);
      $stmt = $pdo->prepare('INSERT INTO user_interests (user_id, category_id) VALUES (?, ?)');
      foreach ($selected as $categoryId) {
        $stmt->execute([$userId, $categoryId]);
      }
      $pdo->commit();
      flash_set('interests', 'Interests saved.');
      redirect('profile.php');
    } catch (PDOException $e) {
      if ($pdo->inTransaction()) {
        $pdo->rollBack();
      }
      throw $e;
    }
  }
}

$ok = [
  'account'   => flash_take('account'),
  'password'  => flash_take('password'),
  'interests' => flash_take('interests'),
];

$pageTitle = 'Profile';
require __DIR__ . '/../src/Views/header.php';
?>
<h1 class="h3 mb-3">Profile</h1>

<section class="card mb-3"><div class="card-body">
  <h2 class="h5">Account</h2>
  <?php if ($errors['account']): ?><div class="alert alert-danger"><?= htmlspecialchars($errors['account']) ?></div>
  <?php elseif ($ok['account']): ?><div class="alert alert-success"><?= htmlspecialchars($ok['account']) ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="section" value="account">
    <div class="mb-3">
      <label for="full_name" class="form-label">Full name</label>
      <input type="text" class="form-control" id="full_name" name="full_name" maxlength="255"
             autocomplete="name" required value="<?= htmlspecialchars($fullName) ?>">
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" maxlength="100"
             autocomplete="email" required value="<?= htmlspecialchars($email) ?>">
    </div>
    <button class="btn btn-dark w-100">Save changes</button>
  </form>
</div></section>

<section class="card mb-3"><div class="card-body">
  <h2 class="h5">Password</h2>
  <?php if ($errors['password']): ?><div class="alert alert-danger"><?= htmlspecialchars($errors['password']) ?></div>
  <?php elseif ($ok['password']): ?><div class="alert alert-success"><?= htmlspecialchars($ok['password']) ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="section" value="password">
    <div class="mb-3">
      <label for="current_password" class="form-label">Current password</label>
      <input type="password" class="form-control" id="current_password" name="current_password"
             maxlength="72" autocomplete="current-password" required>
    </div>
    <div class="mb-3">
      <label for="new_password" class="form-label">New password <small>(minimum 8 characters)</small></label>
      <input type="password" class="form-control" id="new_password" name="new_password"
             minlength="8" maxlength="72" autocomplete="new-password" required>
    </div>
    <div class="mb-3">
      <label for="confirm_password" class="form-label">Confirm new password</label>
      <input type="password" class="form-control" id="confirm_password" name="confirm_password"
             minlength="8" maxlength="72" autocomplete="new-password" required>
    </div>
    <button class="btn btn-dark w-100">Change password</button>
  </form>
</div></section>

<section class="card mb-3"><div class="card-body">
  <h2 class="h5">Interests</h2>
  <?php if ($errors['interests']): ?><div class="alert alert-danger"><?= htmlspecialchars($errors['interests']) ?></div>
  <?php elseif ($ok['interests']): ?><div class="alert alert-success"><?= htmlspecialchars($ok['interests']) ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="section" value="interests">
    <div class="mb-3">
      <?php foreach ($categories as $c): $id = (int) $c['category_id']; ?>
        <input type="checkbox" class="btn-check" id="int<?= $id ?>" name="interests[]"
               value="<?= $id ?>" autocomplete="off" <?= in_array($id, $selected, true) ? 'checked' : '' ?>>
        <label class="btn btn-outline-dark btn-sm m-1" for="int<?= $id ?>"><?= htmlspecialchars($c['name']) ?></label>
      <?php endforeach; ?>
    </div>
    <button class="btn btn-dark w-100">Save</button>
  </form>
</div></section>

<?php require __DIR__ . '/../src/Views/footer.php'; ?>