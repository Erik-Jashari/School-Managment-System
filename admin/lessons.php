<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lessons Management</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/users.css">
    <link rel="stylesheet" href="css/lessons.css">
</head>
<body>
    <div id="app-header"></div>
    <main class="admin-container">
        <div class="header-actions">
            <h1>Lessons Management</h1>
            <a href='edit.php?LessonID=new' class='add-user-btn'>+ Add Lesson</a>
        </div>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Group</th>
                    <th>Subject</th>
                    <th>Uploaded By</th>
                    <th>Upload Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    include '../config/database.php';

                    $sql = "SELECT l.LessonID, l.Title, l.Description, l.UploadTime,
                                   g.GroupName, s.Name AS SubjectName, u.Name AS UploaderName
                            FROM Lessons l
                            LEFT JOIN Groups g ON l.GroupID = g.GroupID
                            LEFT JOIN Subjects s ON l.SubjectID = s.SubjectID
                            LEFT JOIN Users u ON l.UsersID = u.UsersID
                            ORDER BY l.UploadTime DESC";
                    
                    $result = $connection->query($sql);
                    
                    if(!$result){
                        die("Error fetching lessons from database: " . $connection->error);
                    }

                    if($result->num_rows === 0){
                        echo "<tr><td colspan='8' class='no-data'>No lessons found. <a href='edit.php?LessonID=new'>Create one now</a></td></tr>";
                    } else {
                        while($row = $result->fetch_assoc()){
                            $title = htmlspecialchars($row['Title']);
                            $description = htmlspecialchars($row['Description'] ?? 'N/A');
                            $group = htmlspecialchars($row['GroupName'] ?? 'N/A');
                            $subject = htmlspecialchars($row['SubjectName'] ?? 'N/A');
                            $uploader = htmlspecialchars($row['UploaderName'] ?? 'N/A');
                            $uploadTime = date('M j, Y', strtotime($row['UploadTime']));
                            
                            echo "
                                <tr>
                                    <td data-label='ID'>{$row['LessonID']}</td>
                                    <td data-label='Title' class='title-cell'>{$title}</td>
                                    <td data-label='Description' class='description-cell'>{$description}</td>
                                    <td data-label='Group'><span class='group-badge'>{$group}</span></td>
                                    <td data-label='Subject'><span class='subject-badge'>{$subject}</span></td>
                                    <td data-label='Uploaded By'>{$uploader}</td>
                                    <td data-label='Upload Time' class='upload-time'>{$uploadTime}</td>
                                    <td class='actions-cell'>
                                        <a href='edit.php?LessonID={$row['LessonID']}' class='btn-edit'>Edit</a>
                                        <a href='delete.php?LessonID={$row['LessonID']}' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this lesson? This cannot be undone.\")'>Delete</a>
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
