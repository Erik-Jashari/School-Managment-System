<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments Management</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/users.css">
    <link rel="stylesheet" href="css/assignments.css">
</head>
<body>
    <div id="app-header"></div>
    <main class="admin-container">
        <div class="header-actions">
            <h1>Assignments Management</h1>
            <a href='edit.php?AssignmentID=new' class='add-user-btn'>+ Add Assignment</a>
        </div>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Group</th>
                    <th>Subject</th>
                    <th>Lesson</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    include '../config/database.php';

                    $sql = "SELECT a.AssignmentID, a.Title, a.Description, a.DueDate,
                                   g.GroupName, s.Name AS SubjectName, l.Title AS LessonTitle
                            FROM Assignments a
                            LEFT JOIN Groups g ON a.GroupID = g.GroupID
                            LEFT JOIN Subjects s ON a.SubjectID = s.SubjectID
                            LEFT JOIN Lessons l ON a.LessonID = l.LessonID
                            ORDER BY a.DueDate DESC";
                    
                    $result = $connection->query($sql);
                    
                    if(!$result){
                        die("Error fetching assignments from database: " . $connection->error);
                    }

                    if($result->num_rows === 0){
                        echo "<tr><td colspan='8' class='no-data'>No assignments found. <a href='edit.php?AssignmentID=new'>Create one now</a></td></tr>";
                    } else {
                        $today = date('Y-m-d');
                        while($row = $result->fetch_assoc()){
                            $title = htmlspecialchars($row['Title']);
                            $description = htmlspecialchars($row['Description'] ?? 'N/A');
                            $group = htmlspecialchars($row['GroupName'] ?? 'N/A');
                            $subject = htmlspecialchars($row['SubjectName'] ?? 'N/A');
                            $lesson = htmlspecialchars($row['LessonTitle'] ?? 'N/A');
                            $dueDate = date('M j, Y', strtotime($row['DueDate']));
                            $isOverdue = $row['DueDate'] < $today;
                            $dueDateClass = $isOverdue ? 'due-date overdue' : 'due-date upcoming';
                            
                            echo "
                                <tr>
                                    <td>{$row['AssignmentID']}</td>
                                    <td class='title-cell'>{$title}</td>
                                    <td class='description-cell'>{$description}</td>
                                    <td class='{$dueDateClass}'>{$dueDate}</td>
                                    <td><span class='group-badge'>{$group}</span></td>
                                    <td><span class='subject-badge'>{$subject}</span></td>
                                    <td class='lesson-cell'>{$lesson}</td>
                                    <td class='actions-cell'>
                                        <a href='edit.php?AssignmentID={$row['AssignmentID']}' class='btn-edit'>Edit</a>
                                        <a href='delete.php?AssignmentID={$row['AssignmentID']}' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this assignment? All related submissions will also be deleted.\")'>Delete</a>
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
