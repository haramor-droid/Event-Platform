
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Helpers/auth.php';
require_once  __DIR__ . '/../Helpers/events.php';
require_once __DIR__ . '/../src/Helpers/uploads.php';

$user       = require_role($pdo, 'organiser');
$organiserId = current_organiser_id($pdo, (int) $user['user_id']);
if ($organiserId === null){
 http_response_code(404);
 exit('Organisation profile not found.');
}
$categories = $pdo->query(
    'SELECT category_id, name FROM categories WHERE is_active = 1 ORDER BY category_id'
)->fetchAll();
$validIds = array_map('intval', array_column($categories, 'category_id'));

$next = 'my_event.php';
$f    = event_defaults();
$message ='';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    csrf_check();
    $action  = post_str('action');
    $f     = event_input_from_post();
    $hasFile = ($_FILES['poster']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

    if(!in_array($action,['draft','submit'], true)){
        $message = 'Please use the Save draft or Submit for approval button. ';
    }
    else {
        $message = validate_event($f, $validate);
    }

    if($message === '' && $action === 'submit' && !$hasFile){
        $message= 'Please upload a poster before submitting for approval.You can save a draft without one';
    }

    // ONly handles the file once everything else is valid, so failed forms leave no stray files
    $poster = null;
    if ($message === '' && $hasFile){
        [$poster, $message]= save_poster($_FILES['poster']);
    }
    if ($message === ''){
        try {
            $now = (new DateTimeImmutable('now', new DateTimeZone('Africa/Windhoek')))->format('Y-m-d H:i:s');

            $stmt = $pdo->prepare(
                'INSERT INTO events
                (organiser_id,category_id, title, description,poster_path,video_url, venue_name,address,town,price_type,price_min,price_max,ticket_url,ticket_note,start_datetime, end_datetime,status,submitted_at)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
            );
              $stmt->execute([
        $organiserId, $f['category_id'], $f['title'], $f['description'], $poster,
        $f['video_url'] === '' ? null : $f['video_url'],
        $f['venue_name'], $f['address'], $f['town'],
        $f['price_type'], $f['price_min_db'], $f['price_max_db'],
        $f['ticket_url'] === '' ? null : $f['ticket_url'],
        $f['ticket_note'] === '' ? null : $f['ticket_note'],
        $f['start_datetime'], $f['end_datetime'],
        $action === 'submit' ? 'pending' : 'draft',
        $action === 'submit' ? $now : null,
      ]);
        flash_set('event', $action === 'submit'
        ? 'Submitted for approval. We aim to review it within 24 hours.'
        : 'Draft saved.');
      redirect($next);
    } catch (PDOException $e) {
      delete_poster($poster);
      throw $e;
    }
        }
        elseif($hasFile){
            $message .= 'Please choose the poster again. '; // browser clear file fields after an error
        }
    }
   $ok = flash_take('event');
   $pageTitle = 'Create event';
   require __DIR__ . '/../src/Views/header.php';
?>
<div class ="d-flex justify-content-between align-items-center mb_3">
    <h1 class="h3 mb-0">Create event</h1>
    <a href="index.php">Cancel</a>
</div>
<?php if ($ok): ?><div class="alert alert-success"><?=  htmlspecialchars($ok) ?></div><?php endif; ?>
    <?php if ($message): ?><div class = "alert alert-danger"><?=  htmlspecialchars($message) ?></div><?php endif; ?>
        <?php require __DIR__ . '/../src/Views/event_form.php'; ?>
        <?php require __DIR__ . '/../src/Views/footer.php'; ?>