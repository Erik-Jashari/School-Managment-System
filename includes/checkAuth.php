<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Refresh session to prevent timeout during active use
if (isLoggedIn()) {
    $_SESSION['last_activity'] = time();
}

echo json_encode([
    'isLoggedIn' => isLoggedIn(),
    'role'       => isLoggedIn() ? $_SESSION['role'] : null,
    'name'       => isLoggedIn() ? $_SESSION['user_name'] : null,
    'email'      => isLoggedIn() ? $_SESSION['user_email'] : null
]);
?>
