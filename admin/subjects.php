<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subjects Management</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/users.css">
    <link rel="stylesheet" href="css/subjects.css">
</head>
<body>
    <div id="app-header"></div>
    <main class="admin-container">
        <div class="header-actions">
            <h1>Subjects Management</h1>
            <a href='edit.php?SubjectID=new' class='add-user-btn'>+ Add Subject</a>
        </div>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                    <th>Description</th>
                    <th>Lessons</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    include '../config/database.php';

                    $sql = "SELECT s.SubjectID, s.Name, s.Description, COUNT(l.LessonID) AS LessonCount
                            FROM Subjects s
                            LEFT JOIN Lessons l ON s.SubjectID = l.SubjectID
                            GROUP BY s.SubjectID, s.Name, s.Description
                            ORDER BY s.SubjectID ASC";
                    
                    $result = $connection->query($sql);
                    
                    if(!$result){
                        die("Error fetching subjects from database: " . $connection->error);
                    }

                    if($result->num_rows === 0){
                        echo "<tr><td colspan='5' class='no-data'>No subjects found. <a href='edit.php?SubjectID=new'>Create one now</a></td></tr>";
                    } else {
                        while($row = $result->fetch_assoc()){
                            $description = htmlspecialchars($row['Description'] ?? 'N/A');
                            $subjectName = htmlspecialchars($row['Name']);
                            
                            echo "
                                <tr>
                                    <td>{$row['SubjectID']}</td>
                                    <td>{$subjectName}</td>
                                    <td class='description-cell'>{$description}</td>
                                    <td class='lesson-count'>{$row['LessonCount']}</td>
                                    <td class='actions-cell'>
                                        <a href='edit.php?SubjectID={$row['SubjectID']}' class='btn-edit'>Edit</a>
                                        <a href='delete.php?SubjectID={$row['SubjectID']}' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this subject? This cannot be undone.\")'>Delete</a>
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
