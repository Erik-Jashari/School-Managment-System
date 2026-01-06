<?php
    require_once 'database.php';

    $pdo = getConnection();

    if ($pdo) {
        echo "Database connection successful!";

        $stmt = $pdo->query("SELECT COUNT(*) as total from Users");
        $result = $stmt->fetch();
        echo " Total Users: " . $result['total'];
    }
?>