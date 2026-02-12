<?php
require_once __DIR__ . '/config/database.php';

// Get selected group (default to first group)
$selectedGroup = isset($_GET['group']) ? (int)$_GET['group'] : 0;

// Fetch all groups for sidebar
$groupsResult = $connection->query("SELECT GroupID, GroupName FROM Groups ORDER BY GroupName ASC");
$groups = [];
while ($g = $groupsResult->fetch_assoc()) {
    $groups[] = $g;
}
if ($selectedGroup === 0 && count($groups) > 0) {
    $selectedGroup = (int)$groups[0]['GroupID'];
}

// Get current group name
$currentGroupName = 'No Group';
foreach ($groups as $g) {
    if ((int)$g['GroupID'] === $selectedGroup) {
        $currentGroupName = $g['GroupName'];
        break;
    }
}

// Count total students in selected group
$stmt = $connection->prepare("SELECT COUNT(*) as total FROM Student_Groups WHERE GroupID = ?");
$stmt->bind_param('i', $selectedGroup);
$stmt->execute();
$totalStudents = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Count assignments due (future due dates) for this group
$stmt = $connection->prepare("SELECT COUNT(*) as total FROM Assignments WHERE GroupID = ? AND DueDate >= CURDATE()");
$stmt->bind_param('i', $selectedGroup);
$stmt->execute();
$assignmentsDue = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Calculate attendance rate for group (all time)
$attendanceRate = 0;
$attQuery = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN a.Status = 'Present' THEN 1 ELSE 0 END) as present
    FROM Attendance a
    JOIN Student_Groups sg ON a.UsersID = sg.UsersID
    WHERE sg.GroupID = ?
";
$stmt = $connection->prepare($attQuery);
$stmt->bind_param('i', $selectedGroup);
$stmt->execute();
$attRow = $stmt->get_result()->fetch_assoc();
$stmt->close();
if ($attRow['total'] > 0) {
    $attendanceRate = round(($attRow['present'] / $attRow['total']) * 100);
}

// Fetch students in the selected group
$studentsQuery = "
    SELECT u.UsersID, u.Name
    FROM Users u
    JOIN Student_Groups sg ON u.UsersID = sg.UsersID
    WHERE sg.GroupID = ? AND u.Role = 'Student'
    ORDER BY u.Name ASC
";
$stmt = $connection->prepare($studentsQuery);
$stmt->bind_param('i', $selectedGroup);
$stmt->execute();
$studentsResult = $stmt->get_result();
$stmt->close();

// Today's date for attendance lookup
$today = date('Y-m-d');

// Helper to get letter grade
function getLetterGrade($pct) {
    if ($pct === null) return ['—', ''];
    $pct = round($pct);
    if ($pct >= 90) return [$pct . '% (A)', 'grade-a'];
    if ($pct >= 80) return [$pct . '% (B)', 'grade-b'];
    if ($pct >= 70) return [$pct . '% (C)', 'grade-c'];
    if ($pct >= 60) return [$pct . '% (D)', 'grade-d'];
    return [$pct . '% (F)', 'grade-f'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Class Manager</title>
    <link rel="stylesheet" href="CSS/Global.css">
    <link rel="stylesheet" href="CSS/Dashboard.css">
</head>
<body>
    <!-- Navigation Bar -->
    <div id="app-header"></div>

    <main class="dashboard-container">
        
        <aside class="class-sidebar">
            <h3>Groups</h3>
            <ul class="period-list">
                <?php foreach ($groups as $g): ?>
                    <li class="period-item <?php echo ((int)$g['GroupID'] === $selectedGroup) ? 'active' : ''; ?>">
                        <a href="Dashboard.php?group=<?php echo $g['GroupID']; ?>" style="color:inherit;text-decoration:none;">
                            <?php echo htmlspecialchars($g['GroupName']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <?php if (count($groups) === 0): ?>
                    <li class="period-item">No groups found</li>
                <?php endif; ?>
            </ul>
        </aside>

        <div class="dashboard-main">
            
            <div class="stats-bar">
                <div class="stat-card">
                    <div class="stat-info">
                        <h4>Total Students</h4>
                        <p><?php echo $totalStudents; ?></p>
                    </div>
                    <div class="stat-icon">👥</div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h4>Attendance Rate</h4>
                        <p><?php echo $attendanceRate; ?>%</p>
                    </div>
                    <div class="stat-icon">📅</div>
                </div>
            </div>

            <section class="roster-section">
                <div class="section-header">
                    <h2><?php echo htmlspecialchars($currentGroupName); ?> - Roster</h2>
                </div>

                <table class="student-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Today's Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $studentsResult->fetch_assoc()):
                            // Get most recent attendance for this student in this group
                            $attStmt = $connection->prepare("
                                SELECT a.Status FROM Attendance a
                                JOIN Schedules sch ON a.ScheduleID = sch.ScheduleID
                                WHERE a.UsersID = ? AND a.Date = ? AND sch.GroupID = ?
                                LIMIT 1
                            ");
                            $attStmt->bind_param('isi', $student['UsersID'], $today, $selectedGroup);
                            $attStmt->execute();
                            $attRes = $attStmt->get_result()->fetch_assoc();
                            $attStmt->close();
                            $todayAtt = $attRes ? $attRes['Status'] : 'N/A';
                            $attClass = $todayAtt === 'N/A' ? 'not-available' : strtolower($todayAtt);
                        ?>
                        <tr>
                            <td data-label="Student Name">
                                <strong><?php echo htmlspecialchars($student['Name']); ?></strong>
                            </td>
                            <td data-label="Today's Attendance">
                                <span class="attendance toggle <?php echo $attClass; ?>"><?php echo $todayAtt; ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($totalStudents === 0): ?>
                        <tr><td colspan="2" style="text-align:center;padding:1rem;">No students in this group.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <div id="app-footer"></div>

    <script src="JS/HeaderFooter.js"></script>
    <script src="JS/Dashboard.js"></script>
</body>
</html>