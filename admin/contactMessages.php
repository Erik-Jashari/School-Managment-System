<?php
include '../config/database.php';

if (isset($_GET['CM_ID'], $_GET['action'])) {
    $id = (int)$_GET['CM_ID'];
    $action = $_GET['action'];

    $isRead = ($action === 'read') ? 1 : 0;

    $stmt = $connection->prepare('UPDATE contact_messages SET IsRead = ? WHERE CM_ID = ?');
    if ($stmt) {
        $stmt->bind_param('ii', $isRead, $id);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: contactMessages.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/users.css">
    <link rel="stylesheet" href="css/contactMessages.css">
</head>
<body>
    <div id="app-header"></div>
    <main class="admin-container">
        <div class="header-actions">
            <h1>Contact Messages</h1>
        </div>
        <table border="1">
            <thead>
                <tr>
                    <th>CM_ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $sql = "SELECT * FROM contact_messages ORDER BY IsRead ASC, CreatedAt DESC";
                    $result = $connection->query($sql);
                    if(!$result){
                        die("Gabim gjate marrjes se te dhenave nga databaza");
                    }
                    while($row = $result->fetch_assoc()){
                        $isRead = $row['IsRead'];
                        $rowClass = $isRead ? 'read-message' : 'unread-message';
                        $statusBadge = $isRead 
                            ? '<span class="status-badge badge-read">Read</span>' 
                            : '<span class="status-badge badge-unread">Unread</span>';
                        $toggleLink = $isRead 
                            ? "<a href='contactMessages.php?CM_ID={$row['CM_ID']}&action=unread'>Mark As Unread</a>"
                            : "<a href='contactMessages.php?CM_ID={$row['CM_ID']}&action=read'>Mark As Read</a>";
                        $deleteLink = "<a href='delete.php?CM_ID={$row['CM_ID']}' onclick=\"return confirm('Are you sure you want to delete this message?');\">Delete</a>";
                        
                        echo "
                            <tr class='{$rowClass}'>
                                <td>{$row['CM_ID']}</td>
                                <td>{$row['Name']}</td>
                                <td>{$row['Email']}</td>
                                <td>{$row['Message']}</td>
                                <td>{$row['CreatedAt']}</td>
                                <td>{$statusBadge}</td>
                                <td>{$toggleLink} {$deleteLink}</td>
                            </tr>
                        ";
                    }
                ?>
            </tbody>
        </table>
    </main>
    <div id="app-footer"></div>
    <script src="../JS/HeaderFooter.js"></script>
    <script src="../JS/Auth.js"></script>
</body>
</html>