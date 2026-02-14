<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();

// Prevent caching of protected pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Students only — redirect admins to admin panel
if (isAdmin()) {
    header('Location: /School-Managment-System/admin/index.php');
    exit;
}

// ========== FETCH STUDENT DATA ==========
$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];
$userRole = $_SESSION['role'];

// Generate initials from name
$nameParts = explode(' ', $userName);
$initials = '';
foreach ($nameParts as $part) {
    $initials .= strtoupper(mb_substr($part, 0, 1));
}
$initials = mb_substr($initials, 0, 2);

// Get join date
$stmtUser = $connection->prepare("SELECT CreatedAt FROM Users WHERE UsersID = ?");
$stmtUser->bind_param("i", $userId);
$stmtUser->execute();
$userResult = $stmtUser->get_result()->fetch_assoc();
$joinDate = $userResult ? date('F Y', strtotime($userResult['CreatedAt'])) : 'N/A';
$stmtUser->close();

// Get student's groups
$stmtGroups = $connection->prepare("SELECT g.GroupName FROM Student_Groups sg JOIN Groups g ON sg.GroupID = g.GroupID WHERE sg.UsersID = ?");
$stmtGroups->bind_param("i", $userId);
$stmtGroups->execute();
$groupsResult = $stmtGroups->get_result();
$groups = [];
while ($row = $groupsResult->fetch_assoc()) {
    $groups[] = $row['GroupName'];
}
$stmtGroups->close();
$groupDisplay = !empty($groups) ? implode(', ', $groups) : 'No group assigned';

// Get student's group IDs for further queries
$stmtGroupIds = $connection->prepare("SELECT GroupID FROM Student_Groups WHERE UsersID = ?");
$stmtGroupIds->bind_param("i", $userId);
$stmtGroupIds->execute();
$groupIdsResult = $stmtGroupIds->get_result();
$groupIds = [];
while ($row = $groupIdsResult->fetch_assoc()) {
    $groupIds[] = $row['GroupID'];
}
$stmtGroupIds->close();

// STAT 1: Submissions count
$stmtSubs = $connection->prepare("SELECT COUNT(*) AS total FROM Submissions WHERE UsersID = ?");
$stmtSubs->bind_param("i", $userId);
$stmtSubs->execute();
$submissionsCount = $stmtSubs->get_result()->fetch_assoc()['total'] ?? 0;
$stmtSubs->close();

// STAT 2: Average grade
$stmtAvg = $connection->prepare("SELECT AVG(Grade) AS avg_grade FROM Submissions WHERE UsersID = ? AND Grade IS NOT NULL");
$stmtAvg->bind_param("i", $userId);
$stmtAvg->execute();
$avgResult = $stmtAvg->get_result()->fetch_assoc();
$avgGrade = $avgResult['avg_grade'] !== null ? number_format((float)$avgResult['avg_grade'], 1) : 'N/A';
$stmtAvg->close();

// STAT 3: Attendance rate
$stmtAtt = $connection->prepare("SELECT COUNT(*) AS total, SUM(CASE WHEN Status = 'Present' THEN 1 ELSE 0 END) AS present FROM Attendance WHERE UsersID = ?");
$stmtAtt->bind_param("i", $userId);
$stmtAtt->execute();
$attResult = $stmtAtt->get_result()->fetch_assoc();
if ($attResult['total'] > 0) {
    $attendanceRate = round(($attResult['present'] / $attResult['total']) * 100) . '%';
} else {
    $attendanceRate = 'N/A';
}
$stmtAtt->close();

// STAT 4: Upcoming assignments
$upcomingAssignments = 0;
if (!empty($groupIds)) {
    $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
    $types = str_repeat('i', count($groupIds));
    $stmtUpcoming = $connection->prepare("SELECT COUNT(*) AS total FROM Assignments WHERE GroupID IN ($placeholders) AND DueDate >= NOW()");
    $stmtUpcoming->bind_param($types, ...$groupIds);
    $stmtUpcoming->execute();
    $upcomingAssignments = $stmtUpcoming->get_result()->fetch_assoc()['total'] ?? 0;
    $stmtUpcoming->close();
}

// Recent submissions (last 3)
$stmtRecent = $connection->prepare("SELECT s.SubmittedAt, s.Grade, s.Status, a.Title AS AssignmentTitle 
    FROM Submissions s 
    JOIN Assignments a ON s.AssignmentID = a.AssignmentID 
    WHERE s.UsersID = ? 
    ORDER BY s.SubmittedAt DESC LIMIT 3");
$stmtRecent->bind_param("i", $userId);
$stmtRecent->execute();
$recentResult = $stmtRecent->get_result();
$recentSubmissions = [];
while ($row = $recentResult->fetch_assoc()) {
    $recentSubmissions[] = $row;
}
$stmtRecent->close();

// Get subjects for the student's groups
$subjects = [];
if (!empty($groupIds)) {
    $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
    $types = str_repeat('i', count($groupIds));
    $stmtSubjects = $connection->prepare("SELECT DISTINCT sub.Name FROM Schedules sch JOIN Subjects sub ON sch.SubjectID = sub.SubjectID WHERE sch.GroupID IN ($placeholders)");
    $stmtSubjects->bind_param($types, ...$groupIds);
    $stmtSubjects->execute();
    $subjectsResult = $stmtSubjects->get_result();
    while ($row = $subjectsResult->fetch_assoc()) {
        $subjects[] = $row['Name'];
    }
    $stmtSubjects->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="CSS/Global.css">
    <link rel="stylesheet" href="CSS/Student-Profile.css">
</head>
<body>
    <!-- Navigation Bar -->
    <div id="app-header"></div>

    <main class="dashboard-container"></main>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Profile Header Card -->
        <div class="profile-card">
            <div class="profile-header">
                <!-- Avatar Section -->
                <div class="avatar-container">
                    <div class="avatar"><?php echo htmlspecialchars($initials); ?></div>
                </div>

                <!-- User Info -->
                <div class="user-info">
                    <div class="user-header">
                        <div class="user-name-section">
                            <h1><?php echo htmlspecialchars($userName); ?></h1>
                            <p class="user-role"><?php echo htmlspecialchars($userRole); ?></p>
                        </div>
                    </div>

                    <div class="user-details">
                        <span class="detail-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <?php echo htmlspecialchars($userEmail); ?>
                        </span>
                        <span class="detail-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            <?php echo htmlspecialchars($groupDisplay); ?>
                        </span>
                        <span class="detail-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            Joined <?php echo htmlspecialchars($joinDate); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
                <p class="stat-number"><?php echo $submissionsCount; ?></p>
                <p class="stat-label">Submissions</p>
            </div>
            <div class="stat-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="7"></circle>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                </svg>
                <p class="stat-number"><?php echo $avgGrade; ?></p>
                <p class="stat-label">Average Grade</p>
            </div>
            <div class="stat-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <p class="stat-number"><?php echo $attendanceRate; ?></p>
                <p class="stat-label">Attendance Rate</p>
            </div>
            <div class="stat-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <p class="stat-number"><?php echo $upcomingAssignments; ?></p>
                <p class="stat-label">Upcoming Assignments</p>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Recent Submissions -->
            <div class="content-card">
                <h2>Recent Submissions</h2>
                <div class="lessons-list">
                    <?php if (empty($recentSubmissions)): ?>
                        <div class="lesson-item">
                            <h3>No submissions yet</h3>
                            <div class="lesson-meta">
                                <span>Submit your assignments to see them here</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentSubmissions as $sub): ?>
                            <div class="lesson-item">
                                <h3><?php echo htmlspecialchars($sub['AssignmentTitle']); ?></h3>
                                <div class="lesson-meta">
                                    <span class="status-<?php echo strtolower($sub['Status']); ?>">
                                        <?php echo htmlspecialchars($sub['Status']); ?>
                                        <?php if ($sub['Grade'] !== null): ?>
                                            — Grade: <?php echo htmlspecialchars($sub['Grade']); ?>
                                        <?php endif; ?>
                                    </span>
                                    <span><?php echo date('M j, Y', strtotime($sub['SubmittedAt'])); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <a href="Dashboard.php"><button class="outline-button">View All Assignments</button></a>
            </div>

            <!-- Groups & Quick Actions -->
            <div class="content-card">
                <h2>My Groups & Subjects</h2>
                <div class="badges-container">
                    <?php if (!empty($groups)): ?>
                        <?php foreach ($groups as $group): ?>
                            <span class="badge"><?php echo htmlspecialchars($group); ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="badge">No groups</span>
                    <?php endif; ?>

                    <?php foreach ($subjects as $subject): ?>
                        <span class="badge badge-subject"><?php echo htmlspecialchars($subject); ?></span>
                    <?php endforeach; ?>
                </div>

                <h3 class="quick-actions-title">Quick Actions</h3>
                <div class="actions-container">
                    <a href="Dashboard.php"><button class="primary-button">View Assignments</button></a>
                    <a href="Contact.php"><button class="outline-button">Contact Us</button></a>
                    <a href="includes/logout.php"><button class="outline-button">Logout</button></a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <div id="app-footer"></div>

    <script src="JS/HeaderFooter.js"></script>
    <script src="JS/Auth.js"></script>
</body>
</html>
