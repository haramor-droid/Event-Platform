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

  if(!isset($_COOKIE[session_name()])){
    return null;  // no session cookie, so nobody is logged in
  }

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

 //events.organiser points at organiser_profiles.organiser_id NOT users.user_id
 // Organiser pages look it up from the logged-in user.

 function current_organiser(PDO $pdo, int $userId): ?int{
$stmt = $pdo->prepare('SELECT organiser_id FROM organiser_profiles WHERE user_id=?');
$stmt-> execute([$userId]);
$id = $stmt->fetchColumn();
return $id === false ? null : (int) $id;

 }

// Redirect and stop
function redirect(string $url): never {
  header('Location: ' . $url);
  exit;
}

// Read a POST value as a string (an array or missing value becomes '')
function post_str(string $key): string {
  $v = $_POST[$key] ?? '';
  return is_string($v) ? $v : '';
}

// CSRF protection: every form that changes data includes csrf_field() and its handler calls csrf_check()
function csrf_token(): string {
  start_secure_session();
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf'];
}

function csrf_field(): string {
  return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_check(): void {
  start_secure_session();
  $sent = post_str('csrf');
  if ($sent === '' || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $sent)) {
    http_response_code(400);
    exit('Invalid request. Please go back and try again.');
  }
}

// One-time "saved" messages that survive a redirect
function flash_set(string $section, string $text): void {
  start_secure_session();
  $_SESSION['flash'][$section] = $text;
}

function flash_take(string $section): string {
  start_secure_session();
  $text = $_SESSION['flash'][$section] ?? '';
  unset($_SESSION['flash'][$section]);
  return $text;
}

// Returns an error message, or '' when the password was changed
function change_password(PDO $pdo, int $userId, string $current, string $new, string $confirm): string {
  if (strlen($new) < 8) {
    return 'New password must be at least 8 characters.';
  }
  if (strlen($new) > 72) {
    return 'New password is too long. Please use 72 characters or fewer.';
  }
  if ($new !== $confirm) {
    return 'The new passwords do not match.';
  }

  $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE user_id = ?');
  $stmt->execute([$userId]);
  $hash = $stmt->fetchColumn();

  if (!$hash || !password_verify($current, $hash)) {
    return 'Your current password is not correct.';
  }

  $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE user_id = ?');
  $stmt->execute([password_hash($new, PASSWORD_DEFAULT), $userId]);
  session_regenerate_id(true);
  return '';
}

?>