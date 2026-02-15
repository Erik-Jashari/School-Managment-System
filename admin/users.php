<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/users.css">
    <link rel="stylesheet" href="css/groups.css">
</head>
<body>
    <div id="app-header"></div>
    <main class="admin-container">
        <div class="header-actions">
            <h1>Users Management</h1>
            <a href='addUser.php' class='add-user-btn'>+ Shto User</a>
        </div>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    include '../config/database.php';

                    $sql = "SELECT * FROM users";
                    $result = $connection->query($sql);
                    if(!$result){
                        die("Gabim gjate marrjes se te dhenave nga databaza");
                    }
                    while($row = $result->fetch_assoc()){
                        echo "
                            <tr>
                                <td>{$row['UsersID']}</td>
                                <td>{$row['Name']}</td>
                                <td>{$row['Email']}</td>
                                <td>{$row['Role']}</td>
                                <td>{$row['CreatedAt']}</td>
                                <td class=\"actions-cell\">
                                    <a href='edit.php?UsersID={$row['UsersID']}' class='btn-edit'>Edit</a>
                                    <a href='delete.php?UsersID={$row['UsersID']}' class='btn-delete' onclick=\"return confirm('Are you sure you want to delete this user?')\">Delete</a>
                                </td>
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