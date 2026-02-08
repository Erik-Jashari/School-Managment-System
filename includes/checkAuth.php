<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

echo json_encode([
    'isLoggedIn' => isLoggedIn(),
    'role'       => isLoggedIn() ? $_SESSION['role'] : null,
    'name'       => isLoggedIn() ? $_SESSION['user_name'] : null,
    'email'      => isLoggedIn() ? $_SESSION['user_email'] : null
]);
?>
