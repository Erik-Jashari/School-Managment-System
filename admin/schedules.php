<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedules Management</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/users.css">
    <link rel="stylesheet" href="css/groups.css">
</head>
<body>
    <div id="app-header"></div>
    <main class="admin-container">
        <div class="header-actions">
            <h1>Schedules Management</h1>
            <a href='edit.php?ScheduleID=new' class='add-user-btn'>+ Add Schedule</a>
        </div>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Group</th>
                    <th>Subject</th>
                    <th>Day</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Klasa</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    include '../config/database.php';

                    $sql = "SELECT sch.ScheduleID, sch.Day, sch.StartTime, sch.EndTime, sch.Klasa,
                                   g.GroupName, sub.Name AS SubjectName
                            FROM Schedules sch
                            LEFT JOIN Groups g ON sch.GroupID = g.GroupID
                            LEFT JOIN Subjects sub ON sch.SubjectID = sub.SubjectID
                            ORDER BY sch.ScheduleID ASC";

                    $result = $connection->query($sql);
                    if(!$result){
                        die("Error fetching schedules from database: " . $connection->error);
                    }

                    if($result->num_rows === 0){
                        echo "<tr><td colspan='8' class='no-data'>No schedules found. <a href='edit.php?ScheduleID=new'>Create one now</a></td></tr>";
                    } else {
                        while($row = $result->fetch_assoc()){
                            $groupName = htmlspecialchars($row['GroupName'] ?? '—');
                            $subjectName = htmlspecialchars($row['SubjectName'] ?? '—');
                            $day = htmlspecialchars($row['Day']);
                            $start = htmlspecialchars($row['StartTime']);
                            $end = htmlspecialchars($row['EndTime']);
                            $klasa = htmlspecialchars($row['Klasa'] ?? '');

                            echo '<tr>' .
                                 '<td>' . $row['ScheduleID'] . '</td>' .
                                 '<td>' . $groupName . '</td>' .
                                 '<td class="description-cell">' . $subjectName . '</td>' .
                                 '<td>' . $day . '</td>' .
                                 '<td>' . $start . '</td>' .
                                 '<td>' . $end . '</td>' .
                                 '<td>' . $klasa . '</td>' .
                                 '<td class="actions-cell">'
                                    . '<a href="edit.php?ScheduleID=' . $row['ScheduleID'] . '" class="btn-edit">Edit</a>'
                                    . '<a href="delete.php?ScheduleID=' . $row['ScheduleID'] . '" class="btn-delete" onclick="return confirm(\'Are you sure you want to delete this schedule? This cannot be undone.\')">Delete</a>'
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
