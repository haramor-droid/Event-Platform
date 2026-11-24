
<?php 
require_once __DIR__ . '/../Helpers/auth.php';

$pageTitle = $pageTitle ?? 'Events';
$navUser = (isset($user)&& is_array($user)) ? $user : current_user($pdo); 
$current =  basename($_SERVER['SCRIPT_NAME']);

$menuName = 'Menu';
$items = [];
$unread = 0;
$showBell = false;

if ($navUser) {
   $menuName = $navUser['full_name'];
   
   if ($navUser['role']==='admin'){
        $pending = (int) $pdo->query("SELECT COUNT(*) FROM events WHERE status = 'pending'")->fetchColumn();
        $items = [
            ['Events (' . $pending . ')' , 'admin_events.php'],
            ['Activity',                  'admin_activity.php'],
            ['Users',                         'admin_users.php'],
            ['Categories',                     'admin_categories.php'],
            ['Browse events',                   'index.php'],
        ];
    
   } else {
    $showBall = true;
    $stmt = $pdo->prepare('SSELECT org_name FROM organiser_profile WHERE user_id=?');
    $stmt->execute([$navUser[user_id]]);
    $menuName = $stmt->fetchColumn() ?; $menuNmae;
    $items = [
       ['Browse events',           'index.php'],
       ['My events',               'my_events.php'],
       ['Create events',            'create_event.php'],
       ['Saved events',             '']


    ];
   }
    

}





































?>