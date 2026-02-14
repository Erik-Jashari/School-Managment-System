<?php
require_once __DIR__ . '/includes/auth.php';

// Require user to be logged in
requireLogin();

// Get logged in user info
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

// Get selected group (default to first group)
$selectedGroup = isset($_GET['group']) ? (int)$_GET['group'] : 0;

// Fetch groups for sidebar based on user role
$groups = [];
if ($userRole === 'Admin') {
    // Admins see all groups
    $groupsResult = $connection->query("SELECT GroupID, GroupName FROM Groups ORDER BY GroupName ASC");
    while ($g = $groupsResult->fetch_assoc()) {
        $groups[] = $g;
    }
} else {
    // Students see only their assigned groups
    $stmt = $connection->prepare("
        SELECT g.GroupID, g.GroupName 
        FROM Groups g
        JOIN Student_Groups sg ON g.GroupID = sg.GroupID
        WHERE sg.UsersID = ?
        ORDER BY g.GroupName ASC
    ");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $groupsResult = $stmt->get_result();
    while ($g = $groupsResult->fetch_assoc()) {
        $groups[] = $g;
    }
    $stmt->close();
}

if ($selectedGroup === 0 && count($groups) > 0) {
    $selectedGroup = (int)$groups[0]['GroupID'];
}

// Verify user has access to the selected group (for students)
if ($userRole !== 'Admin' && $selectedGroup > 0) {
    $hasAccess = false;
    foreach ($groups as $g) {
        if ((int)$g['GroupID'] === $selectedGroup) {
            $hasAccess = true;
            break;
        }
    }
    if (!$hasAccess && count($groups) > 0) {
        $selectedGroup = (int)$groups[0]['GroupID'];
    } elseif (!$hasAccess) {
        $selectedGroup = 0;
    }
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

// Fetch assignments for the selected group
$assignmentsQuery = "
    SELECT a.AssignmentID, a.Title, a.Description, a.DueDate,
           s.Name AS SubjectName,
           sub.SubmissionID, sub.Status AS SubmissionStatus, sub.FilePath AS SubmissionFile, sub.SubmittedAt
    FROM Assignments a
    LEFT JOIN Subjects s ON a.SubjectID = s.SubjectID
    LEFT JOIN Submissions sub ON a.AssignmentID = sub.AssignmentID AND sub.UsersID = ?
    WHERE a.GroupID = ?
    ORDER BY a.DueDate ASC
";
$stmt = $connection->prepare($assignmentsQuery);
$stmt->bind_param('ii', $userId, $selectedGroup);
$stmt->execute();
$assignmentsResult = $stmt->get_result();
$assignments = [];
while ($row = $assignmentsResult->fetch_assoc()) {
    $assignments[] = $row;
}
$stmt->close();

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
    <title>Dashboard</title>
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
                            <td data-label="student-name">
                                <strong><?php echo htmlspecialchars($student['Name']); ?></strong>
                            </td>
                            <td data-label="today-attendance">
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

            <!-- Homework Upload Section -->
            <?php if ($userRole === 'Student' && $selectedGroup > 0): ?>
            <section class="homework-section">
                <div class="section-header">
                    <h2>Homework & Assignments</h2>
                </div>

                <?php if (count($assignments) === 0): ?>
                    <p class="no-assignments">No assignments for this group yet.</p>
                <?php else: ?>
                    <div class="assignments-list">
                        <?php foreach ($assignments as $assignment):
                            $isPastDue = strtotime($assignment['DueDate']) < strtotime($today);
                            $isSubmitted = !empty($assignment['SubmissionID']);
                            $statusLabel = 'Not Submitted';
                            $statusClass = 'status-pending';
                            if ($isSubmitted) {
                                $statusLabel = ucfirst($assignment['SubmissionStatus']);
                                if ($assignment['SubmissionStatus'] === 'Submitted') $statusClass = 'status-submitted';
                                elseif ($assignment['SubmissionStatus'] === 'Graded') $statusClass = 'status-graded';
                            } elseif ($isPastDue) {
                                $statusLabel = 'Past Due';
                                $statusClass = 'status-overdue';
                            }
                        ?>
                        <div class="assignment-card">
                            <div class="assignment-info">
                                <h4><?php echo htmlspecialchars($assignment['Title']); ?></h4>
                                <?php if ($assignment['SubjectName']): ?>
                                    <span class="assignment-subject"><?php echo htmlspecialchars($assignment['SubjectName']); ?></span>
                                <?php endif; ?>
                                <?php if ($assignment['Description']): ?>
                                    <p class="assignment-desc"><?php echo htmlspecialchars($assignment['Description']); ?></p>
                                <?php endif; ?>
                                <div class="assignment-meta">
                                    <span class="due-date <?php echo $isPastDue ? 'overdue' : ''; ?>">
                                        Due: <?php echo date('M d, Y', strtotime($assignment['DueDate'])); ?>
                                    </span>
                                    <span class="submission-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                                </div>
                                <?php if ($isSubmitted && $assignment['SubmissionFile']): ?>
                                    <p class="submitted-file">Submitted: <a href="<?php echo htmlspecialchars($assignment['SubmissionFile']); ?>" target="_blank"><?php echo htmlspecialchars(basename($assignment['SubmissionFile'])); ?></a>
                                    <span class="submitted-date"> on <?php echo date('M d, Y h:i A', strtotime($assignment['SubmittedAt'])); ?></span></p>
                                <?php endif; ?>
                            </div>
                            <div class="assignment-upload">
                                <?php if (!$isSubmitted || $assignment['SubmissionStatus'] === 'Pending'): ?>
                                    <form class="upload-form" data-assignment-id="<?php echo $assignment['AssignmentID']; ?>" enctype="multipart/form-data">
                                        <label class="file-label">
                                            <input type="file" name="homework_file" class="file-input" accept=".pdf,.doc,.docx,.txt,.zip,.rar,.jpg,.jpeg,.png,.pptx,.xlsx" />
                                            <span class="file-btn">Choose File</span>
                                            <span class="file-name">No file chosen</span>
                                        </label>
                                        <button type="submit" class="upload-btn" disabled>Upload</button>
                                    </form>
                                    <p class="upload-hint">Accepted: PDF, DOC, DOCX, TXT, ZIP, images (max 10MB)</p>
                                <?php else: ?>
                                    <span class="already-submitted">✔ Submitted</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <div id="app-footer"></div>

    <script src="JS/HeaderFooter.js"></script>
    <script src="JS/Dashboard.js"></script>
</body>
</html>