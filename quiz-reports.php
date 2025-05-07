<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../challngr/auth/login.html');
    exit();
}
?>

<h1>Reports</h1>

<?php
$content = ob_get_clean(); // Get the buffered content
include 'app-layout.php'; // Include the layout
?>