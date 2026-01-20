<?php
    include '../config/database.php';

    if (isset($_GET['CM_ID'])) {
        $id = $_GET['CM_ID'];
        $action = $_GET['action'];

        $isRead = ($action === 'read') ? 1 : 0;

        $sql = "UPDATE contact_messages SET IsRead = $isRead WHERE CM_ID=$id";
        if(!$connection->query($sql)){
            die("Error: " . $connection->error);
        }
    }
    header("Location: contactMessages.php");
    exit();
?>