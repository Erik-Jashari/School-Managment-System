<?php
    $serverName = "localhost";
    $username = "root";
    $password = "";
    $dbname = "school_management_system";

    $connection = mysqli_connect($serverName, $username, $password, $dbname);

    if (!$connection) {
        die("Database connection failed");
    }
?>