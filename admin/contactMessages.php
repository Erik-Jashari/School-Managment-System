<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/users.css">
    <style>
        /* Override alternating row colors */
        tr:nth-child(even) {
            background-color: white;
        }
        /* Highlight unread messages */
        .unread-message {
            background-color: #fff8e6 !important;
        }
        .status-badge { 
            padding: 3px 8px; 
            border-radius: 4px; 
            font-size: 12px; 
            color: white;
        }
        .badge-read { 
            background-color: #28a745; 
        }
        .badge-unread { 
            background-color: #ffc107; 
            color: black; 
        }
    </style>
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
                    include '../config/database.php';

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
                            ? "<a href='markAsRead.php?CM_ID={$row['CM_ID']}&action=unread'>Mark As Unread</a>"
                            : "<a href='markAsRead.php?CM_ID={$row['CM_ID']}&action=read'>Mark As Read</a>";
                        
                        echo "
                            <tr class='{$rowClass}'>
                                <td>{$row['CM_ID']}</td>
                                <td>{$row['Name']}</td>
                                <td>{$row['Email']}</td>
                                <td>{$row['Message']}</td>
                                <td>{$row['CreatedAt']}</td>
                                <td>{$statusBadge}</td>
                                <td>{$toggleLink}</td>
                            </tr>
                        ";
                    }
                ?>
            </tbody>
        </table>
    </main>
    <div id="app-footer"></div>
    <script src="../JS/HeaderFooter.js"></script>
</body>
</html>