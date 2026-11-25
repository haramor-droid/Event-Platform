<?php
require_once __DIR__ . '/../config/database.php'; // brings database (to use PDO)
require_once __DIR__ . '/../src/Helpers/auth.php';

// Note: require_login() handles the secure session start, the cookie parameters 
// (httponly, samesite, secure), and the "Guard" check (if not logged in, redirect to login.php).
// It also handles loading the user from the database to ensure we don't trust the session for details.
// If the user was deleted or suspended since logging in, require_login() sends them to logout.php.
$user = require_login($pdo);

$pageTitle = 'Dashboard';
require __DIR__ . '/../src/Views/header.php'; // Load the new Bootstrap header
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h1 class="h3 mb-3">Welcome, <?= htmlspecialchars($user['full_name']) ?></h1>
            
            <!-- Display the user's role -->
            <p class="text-muted">Your role: <strong><?= htmlspecialchars($user['role']) ?></strong></p>
            
            <!-- Add some placeholder content for the dashboard based on your wireframes -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <p class="card-text">What would you like to do today?</p>
                    
                    <?php if ($user['role'] === 'organiser'): ?>
                        <a href="create_event.php" class="btn btn-dark">Create an Event</a>
                        <a href="my_events.php" class="btn btn-outline-dark">View My Events</a>
                    <?php else: ?>
                        <a href="index.php" class="btn btn-dark">Browse Events</a>
                        <a href="saved.php" class="btn btn-outline-dark">View Saved Events</a>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php require __DIR__ . '/../src/Views/footer.php'; ?>