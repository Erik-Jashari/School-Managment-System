<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submissions Management</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/users.css">
    <link rel="stylesheet" href="css/groups.css">
</head>
<body>
    <div id="app-header"></div>
    <main class="admin-container">
        <div class="header-actions">
            <h1>Submissions Management</h1>
        </div>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Assignment</th>
                    <th>Student</th>
                    <th>Submitted At</th>
                    <th>Grade</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    include '../config/database.php';

                    $sql = "SELECT s.SubmissionID, s.AssignmentID, s.UsersID, s.SubmittedAt, s.Grade, s.Status,
                                   a.Title AS AssignmentTitle, u.Name AS StudentName
                            FROM Submissions s
                            JOIN Assignments a ON s.AssignmentID = a.AssignmentID
                            JOIN Users u ON s.UsersID = u.UsersID
                            ORDER BY s.SubmittedAt DESC";

                    $result = $connection->query($sql);
                    if(!$result){
                        die("Error fetching submissions from database: " . $connection->error);
                    }

                    if($result->num_rows === 0){
                        echo "<tr><td colspan='7' class='no-data'>No submissions found.</td></tr>";
                    } else {
                        while($row = $result->fetch_assoc()){
                            $assignmentTitle = htmlspecialchars($row['AssignmentTitle']);
                            $studentName = htmlspecialchars($row['StudentName']);
                            $grade = $row['Grade'] !== null ? htmlspecialchars($row['Grade']) : '—';
                            $status = htmlspecialchars($row['Status']);

                            echo '<tr>' .
                                 '<td>' . $row['SubmissionID'] . '</td>' .
                                 '<td class="description-cell">' . $assignmentTitle . '</td>' .
                                 '<td>' . $studentName . '</td>' .
                                 '<td>' . $row['SubmittedAt'] . '</td>' .
                                 '<td>' . $grade . '</td>' .
                                 '<td>' . $status . '</td>' .
                                 '<td class="actions-cell">'
                                    . '<a href="edit.php?SubmissionID=' . $row['SubmissionID'] . '" class="btn-edit">Edit</a>'
                                    . '<a href="delete.php?SubmissionID=' . $row['SubmissionID'] . '" class="btn-delete" onclick="return confirm(\'Are you sure you want to delete this submission? This cannot be undone.\')">Delete</a>'
                                 . '</td>' .
                                 '</tr>';
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
