<?php
require_once __DIR__ . '/../Helpers/auth.php';

$pageTitle = $pageTitle ?? 'Events';
$navUser   = current_user($pdo);

$menuName = 'Menu';
$items    = [];
$unread   = 0;
$showBell = false;
$current = basename($_SERVER['SCRIPT_NAME']);

if ($navUser) {
  $menuName = $navUser['full_name'];

  if ($navUser['role'] === 'admin') {
    $pending = (int) $pdo->query("SELECT COUNT(*) FROM events WHERE status = 'pending'")->fetchColumn();
    $items = [
      ['Events (' . $pending . ')', 'admin_events.php'],
      ['Activity',                  'admin_activity.php'],
      ['Users',                     'admin_users.php'],
      ['Categories',                'admin_categories.php'],
      ['Browse events',             'index.php'],
    ];
  } else {
    $showBell = true;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
    $stmt->execute([$navUser['user_id']]);
    $unread = (int) $stmt->fetchColumn();
    $notifLabel = 'Notifications' . ($unread > 0 ? ' (' . $unread . ')' : '');

    if ($navUser['role'] === 'organiser') {
      $stmt = $pdo->prepare('SELECT org_name FROM organiser_profiles WHERE user_id = ?');
      $stmt->execute([$navUser['user_id']]);
      $menuName = $stmt->fetchColumn() ?: $menuName;
      $items = [
        ['Browse events',        'index.php'],
        ['My events',            'my_events.php'],
        ['Create event',         'create_event.php'],
        ['Saved events',         'saved.php'],
        ['My calendar',          'calendar.php'],
        [$notifLabel,            'notifications.php'],
        ['Organisation profile', 'org_profile.php'],
      ];
    } else {
      $items = [
        ['Browse events', 'index.php'],
        ['Saved events',  'saved.php'],
        ['My calendar',   'calendar.php'],
        [$notifLabel,     'notifications.php'],
        ['Profile',       'profile.php'],
      ];
    }
  }
  $items[] = ['Log out', 'logout.php'];
} else {
  $items = [
    ['Browse events', 'index.php'],
    ['Register',      'register.php'],
    ['Log in',        'login.php'],
  ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>.btn-check:checked + .btn::before{content:"\2713\00a0"}</style>
</head>
<body>
<header class="border-bottom bg-white sticky-top">
  <div class="container-fluid d-flex align-items-center gap-2 py-2">
    <button class="btn btn-outline-dark" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#mainMenu" aria-controls="mainMenu" aria-label="Open menu">
      <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    <form class="flex-grow-1" action="index.php" method="get" role="search">
      <label for="q" class="visually-hidden">Search events</label>
      <input id="q" name="q" type="search" class="form-control" placeholder="Search"
             value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    </form>

    <?php if ($showBell): ?>
      <a class="btn btn-outline-dark position-relative" href="notifications.php"
         aria-label="Notifications, <?= $unread ?> unread">
        <i class="bi bi-bell" aria-hidden="true"></i>
        <?php if ($unread > 0): ?>
          <span class="badge text-bg-danger"><?= $unread ?></span>
        <?php endif; ?>
      </a>
    <?php elseif (!$navUser): ?>
      <!-- Register is hidden on very small screens (it is in the menu); NFR-01 -->
      <a class="btn btn-dark btn-sm d-none d-sm-inline-block" href="register.php">Register</a>
      <a class="btn btn-dark btn-sm" href="login.php">Log in</a>
    <?php endif; ?>
  </div>
</header>

<div class="offcanvas offcanvas-start" tabindex="-1" id="mainMenu" aria-labelledby="mainMenuLabel">
  <div class="offcanvas-header bg-secondary text-white">
    <h2 class="offcanvas-title h4" id="mainMenuLabel"><?= htmlspecialchars($menuName) ?></h2>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
  </div>
  <div class="offcanvas-body p-0">
    <div class="list-group list-group-flush">
      <?php foreach ($items as [$label, $href]): ?>
        <a class="list-group-item list-group-item-action py-3<?= $href === $current ? ' active' : '' ?>"
           href="<?= htmlspecialchars($href) ?>"<?= $href === $current ? ' aria-current="page"' : '' ?>>
          <?= htmlspecialchars($label) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<main class="container py-3">