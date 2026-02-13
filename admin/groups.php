<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups Management</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/users.css">
    <link rel="stylesheet" href="css/groups.css">
</head>
<body>
    <div id="app-header"></div>
    <main class="admin-container">
        <div class="header-actions">
            <h1>Groups Management</h1>
            <a href='edit.php?GroupID=new' class='add-user-btn'>+ Add Group</a>
        </div>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Group Name</th>
                    <th>Description</th>
                    <th>Total Students</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    include '../config/database.php';

                    $sql = "SELECT g.GroupID, g.GroupName, g.Description, COUNT(sg.UsersID) AS StudentCount
                            FROM Groups g
                            LEFT JOIN Student_Groups sg ON g.GroupID = sg.GroupID
                            GROUP BY g.GroupID, g.GroupName, g.Description
                            ORDER BY g.GroupID ASC";
                    
                    $result = $connection->query($sql);
                    
                    if(!$result){
                        die("Error fetching groups from database: " . $connection->error);
                    }

                    if($result->num_rows === 0){
                        echo "<tr><td colspan='5' class='no-data'>No groups found. <a href='edit.php?GroupID=new'>Create one now</a></td></tr>";
                    } else {
                        while($row = $result->fetch_assoc()){
                            $description = htmlspecialchars($row['Description'] ?? 'N/A');
                            $groupName = htmlspecialchars($row['GroupName']);
                            
                            echo "
                                <tr>
                                    <td>{$row['GroupID']}</td>
                                    <td>{$groupName}</td>
                                    <td class='description-cell'>{$description}</td>
                                    <td class='student-count'>{$row['StudentCount']}</td>
                                    <td class='actions-cell'>
                                        <a href='edit.php?GroupID={$row['GroupID']}' class='btn-edit'>Edit</a>
                                        <a href='delete.php?GroupID={$row['GroupID']}' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this group? This cannot be undone.\")'>Delete</a>
                                    </td>
                                </tr>
                            ";
                        }
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
