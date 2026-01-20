<?php
// TEST FILE - Logs you in as Admin to test the dashboard
// DELETE THIS FILE after testing!

require_once __DIR__ . '/includes/auth.php';

// Set admin session manually (from seed.sql data)
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Admin User';
$_SESSION['user_email'] = 'admin@school.com';
$_SESSION['role'] = 'Admin';

echo "Logged in as Admin!<br><br>";
echo "<a href='admin/index.php'>Go to Admin Dashboard →</a>";
?>
