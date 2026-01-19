<?php 
    include '../config/database.php';

    if (isset($_GET['UsersID'])) {
        $id = $_GET['UsersID'];

        $sql = "DELETE FROM users WHERE UsersID=$id";
        $connection->query($sql);
    }

    header("Location: users.php");
    exit;
?>