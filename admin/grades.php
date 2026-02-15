<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

global $connection;

// Get selected group
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

// Get selected assignment (optional)
$selectedAssignment = isset($_GET['assignment']) ? (int)$_GET['assignment'] : 0;

// Fetch assignments for the selected group
$assignments = [];
if ($selectedGroup > 0) {
    $stmt = $connection->prepare("
        SELECT a.AssignmentID, a.Title, a.DueDate, s.Name AS SubjectName
        FROM Assignments a
        LEFT JOIN Subjects s ON a.SubjectID = s.SubjectID
        WHERE a.GroupID = ?
        ORDER BY a.DueDate DESC
    ");
    $stmt->bind_param('i', $selectedGroup);
    $stmt->execute();
    $assignResult = $stmt->get_result();
    while ($a = $assignResult->fetch_assoc()) {
        $assignments[] = $a;
    }
    $stmt->close();
}

// Default to first assignment if none selected
if ($selectedAssignment === 0 && count($assignments) > 0) {
    $selectedAssignment = (int)$assignments[0]['AssignmentID'];
}

// Get current assignment title
$currentAssignmentTitle = '';
$currentAssignmentDue = '';
foreach ($assignments as $a) {
    if ((int)$a['AssignmentID'] === $selectedAssignment) {
        $currentAssignmentTitle = $a['Title'];
        $currentAssignmentDue = $a['DueDate'];
        break;
    }
}

// Handle grade submission (POST)
$successMessage = '';
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grades'])) {
    $assignmentId = (int)$_POST['assignment_id'];
    $grades = $_POST['grades'];
    $hasError = false;

    foreach ($grades as $userId => $gradeValue) {
        $userId = (int)$userId;
        $gradeValue = trim($gradeValue);

        if ($gradeValue === '') {
            // Skip empty grades
            continue;
        }

        $grade = (float)$gradeValue;
        if ($grade < 0 || $grade > 100) {
            $errorMessage = "Grades must be between 0 and 100.";
            $hasError = true;
            break;
        }

        // Check if submission exists
        $checkStmt = $connection->prepare("SELECT SubmissionID, Grade FROM Submissions WHERE AssignmentID = ? AND UsersID = ?");
        $checkStmt->bind_param('ii', $assignmentId, $userId);
        $checkStmt->execute();
        $existing = $checkStmt->get_result()->fetch_assoc();
        $checkStmt->close();

        if ($existing) {
            // Update existing submission with grade
            $updateStmt = $connection->prepare("UPDATE Submissions SET Grade = ?, Status = 'Graded' WHERE SubmissionID = ?");
            $updateStmt->bind_param('di', $grade, $existing['SubmissionID']);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // Create submission record with grade
            $insertStmt = $connection->prepare("INSERT INTO Submissions (AssignmentID, UsersID, Grade, Status) VALUES (?, ?, ?, 'Graded')");
            $insertStmt->bind_param('iid', $assignmentId, $userId, $grade);
            $insertStmt->execute();
            $insertStmt->close();
        }
    }

    if (!$hasError) {
        $successMessage = "Grades saved successfully!";
    }

    // Refresh to show updated grades
    $selectedAssignment = $assignmentId;
}

// Fetch students in the group with their grade for the selected assignment
$students = [];
if ($selectedGroup > 0 && $selectedAssignment > 0) {
    $stmt = $connection->prepare("
        SELECT u.UsersID, u.Name,
               sub.Grade, sub.Status AS SubmissionStatus, sub.SubmittedAt
        FROM Users u
        JOIN Student_Groups sg ON u.UsersID = sg.UsersID
        LEFT JOIN Submissions sub ON sub.UsersID = u.UsersID AND sub.AssignmentID = ?
        WHERE sg.GroupID = ? AND u.Role = 'Student'
        ORDER BY u.Name ASC
    ");
    $stmt->bind_param('ii', $selectedAssignment, $selectedGroup);
    $stmt->execute();
    $studentsResult = $stmt->get_result();
    while ($s = $studentsResult->fetch_assoc()) {
        $students[] = $s;
    }
    $stmt->close();
}

// Helper for grade badge
function getGradeBadge($grade) {
    if ($grade === null) return ['—', ''];
    $g = round($grade);
    if ($g >= 90) return [$g . '%', 'grade-a'];
    if ($g >= 80) return [$g . '%', 'grade-b'];
    if ($g >= 70) return [$g . '%', 'grade-c'];
    if ($g >= 60) return [$g . '%', 'grade-d'];
    return [$g . '%', 'grade-f'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Students</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/adminDashboard.css">
    <link rel="stylesheet" href="css/grades.css">
</head>
<body>
    <div id="app-header"></div>

    <main class="dashboard-container">
        
        <!-- Group Sidebar -->
        <aside class="class-sidebar">
            <h3>Groups</h3>
            <ul class="period-list">
                <?php foreach ($groups as $g): ?>
                    <li class="period-item <?php echo ((int)$g['GroupID'] === $selectedGroup) ? 'active' : ''; ?>">
                        <a href="grades.php?group=<?php echo $g['GroupID']; ?>">
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

            <!-- Assignment Selector -->
            <div class="assignment-selector">
                <h2>Grade Students</h2>
                <?php if (count($assignments) > 0): ?>
                <div class="assignment-tabs">
                    <label for="assignment-select">Assignment:</label>
                    <select id="assignment-select" onchange="window.location.href='grades.php?group=<?php echo $selectedGroup; ?>&assignment=' + this.value">
                        <?php foreach ($assignments as $a): ?>
                            <option value="<?php echo $a['AssignmentID']; ?>" <?php echo ((int)$a['AssignmentID'] === $selectedAssignment) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($a['Title']); ?>
                                <?php if ($a['SubjectName']): ?>(<?php echo htmlspecialchars($a['SubjectName']); ?>)<?php endif; ?>
                                — Due: <?php echo $a['DueDate']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else: ?>
                    <p class="no-assignments">No assignments found for this group.</p>
                <?php endif; ?>
            </div>

            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-error"><?php echo $errorMessage; ?></div>
            <?php endif; ?>

            <?php if ($selectedAssignment > 0 && count($students) > 0): ?>
            <!-- Grading Table -->
            <section class="roster-section">
                <div class="section-header">
                    <h2><?php echo htmlspecialchars($currentAssignmentTitle); ?></h2>
                    <span class="due-date">Due: <?php echo $currentAssignmentDue; ?></span>
                </div>

                <form method="POST" action="grades.php?group=<?php echo $selectedGroup; ?>&assignment=<?php echo $selectedAssignment; ?>">
                    <input type="hidden" name="assignment_id" value="<?php echo $selectedAssignment; ?>">
                    
                    <table class="student-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Submission Status</th>
                                <th>Submitted At</th>
                                <th>Current Grade</th>
                                <th>Set Grade (0-100)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student):
                                list($gradeText, $gradeClass) = getGradeBadge($student['Grade']);
                                $submissionStatus = $student['SubmissionStatus'] ?? 'Not Submitted';
                                $submittedAt = $student['SubmittedAt'] ?? '—';
                                $statusClass = '';
                                switch ($submissionStatus) {
                                    case 'Graded': $statusClass = 'status-graded'; break;
                                    case 'Submitted': $statusClass = 'status-submitted'; break;
                                    case 'Pending': $statusClass = 'status-pending'; break;
                                    default: $statusClass = 'status-missing'; break;
                                }
                            ?>
                            <tr>
                                <td data-label="Student Name">
                                    <strong><?php echo htmlspecialchars($student['Name']); ?></strong>
                                </td>
                                <td data-label="Status">
                                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo $submissionStatus; ?></span>
                                </td>
                                <td data-label="Submitted At"><?php echo $submittedAt; ?></td>
                                <td data-label="Current Grade">
                                    <span class="grade-badge <?php echo $gradeClass; ?>"><?php echo $gradeText; ?></span>
                                </td>
                                <td data-label="Set Grade">
                                    <input 
                                        type="number" 
                                        name="grades[<?php echo $student['UsersID']; ?>]" 
                                        class="grade-input" 
                                        min="0" max="100" step="1"
                                        value="<?php echo $student['Grade'] !== null ? round($student['Grade']) : ''; ?>"
                                        placeholder="—"
                                    >
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="form-actions">
                        <button type="submit" class="btn-save-grades">Save Grades</button>
                    </div>
                </form>
            </section>
            <?php elseif ($selectedAssignment > 0 && count($students) === 0): ?>
                <div class="empty-state">
                    <p>No students found in this group.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <div id="app-footer"></div>
    <script src="../JS/HeaderFooter.js"></script>
    <script src="../JS/Auth.js"></script>
</body>
</html>
