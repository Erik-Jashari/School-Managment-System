<?php
    require_once 'database.php';

    if ($connection) {
        echo "Database connection successful!";

        $result = mysqli_query($connection, "SELECT COUNT(*) as total FROM Users");
        $row = mysqli_fetch_assoc($result);
        echo " Total Users: " . $row['total'];
    }
?>