<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

// Prevent caching of admin pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

global $connection;

// Get counts for dashboard stats
$usersCount = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM Users"))[0];
$studentsCount = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM Users WHERE Role = 'Student'"))[0];
$messagesCount = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM Contact_Messages"))[0];
$reviewsCount = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM Reviews"))[0];
$groupsCount = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM Groups"))[0];
$subjectsCount = mysqli_fetch_row(mysqli_query($connection, "SELECT COUNT(*) FROM Subjects"))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - School Management System</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div id="app-header"></div>

    <main class="admin-container">
        
        <!-- Admin Header -->
        <div class="admin-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p class="welcome-text">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-info">
                    <h4>Total Users</h4>
                    <p><?php echo $usersCount; ?></p>
                </div>
                <div class="stat-icon">👥</div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h4>Students</h4>
                    <p><?php echo $studentsCount; ?></p>
                </div>
                <div class="stat-icon">🎓</div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h4>Messages</h4>
                    <p><?php echo $messagesCount; ?></p>
                </div>
                <div class="stat-icon">✉️</div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h4>Reviews</h4>
                    <p><?php echo $reviewsCount; ?></p>
                </div>
                <div class="stat-icon">⭐</div>
            </div>
        </div>

        <!-- Navigation Cards -->
        <div class="admin-section">
            <div class="section-header">
                <h2>Manage System</h2>
            </div>
            
            <div class="admin-nav">
                <!-- Student 1 Tables -->
                <a href="users.php" class="nav-card">
                    <div class="nav-icon">👥</div>
                    <div class="nav-info">
                        <h3>Users</h3>
                        <p>Manage all users</p>
                    </div>
                </a>
                
                <a href="contactMessages.php" class="nav-card">
                    <div class="nav-icon">✉️</div>
                    <div class="nav-info">
                        <h3>Messages</h3>
                        <p>View contact messages</p>
                    </div>
                </a>
                
                <a href="reviews.php" class="nav-card">
                    <div class="nav-icon">⭐</div>
                    <div class="nav-info">
                        <h3>Reviews</h3>
                        <p>Manage reviews</p>
                    </div>
                </a>

                <!-- Student 2 Tables -->
                <a href="groups.php" class="nav-card">
                    <div class="nav-icon">📚</div>
                    <div class="nav-info">
                        <h3>Groups</h3>
                        <p><?php echo $groupsCount; ?> groups</p>
                    </div>
                </a>
                
                <a href="subjects.php" class="nav-card">
                    <div class="nav-icon">📖</div>
                    <div class="nav-info">
                        <h3>Subjects</h3>
                        <p><?php echo $subjectsCount; ?> subjects</p>
                    </div>
                </a>
                
                <a href="schedules.php" class="nav-card">
                    <div class="nav-icon">📅</div>
                    <div class="nav-info">
                        <h3>Schedules</h3>
                        <p>Manage timetables</p>
                    </div>
                </a>

                <!-- Student 3 Tables -->
                <a href="lessons.php" class="nav-card">
                    <div class="nav-icon">📝</div>
                    <div class="nav-info">
                        <h3>Lessons</h3>
                        <p>Manage lessons</p>
                    </div>
                </a>
                
                <a href="assignments.php" class="nav-card">
                    <div class="nav-icon">📋</div>
                    <div class="nav-info">
                        <h3>Assignments</h3>
                        <p>Manage assignments</p>
                    </div>
                </a>
                
                <a href="submissions.php" class="nav-card">
                    <div class="nav-icon">📤</div>
                    <div class="nav-info">
                        <h3>Submissions</h3>
                        <p>View submissions</p>
                    </div>
                </a>
                <a href="adminDashboard.php" class="nav-card">
                    <div class="nav-icon">📊</div>
                    <div class="nav-info">
                        <h3>Analytics</h3>
                        <p>View student analytics</p>
                    </div>
                </a>
            </div>
        </div>

    </main>

    <div id="app-footer"></div>
    <script src="../JS/HeaderFooter.js"></script>
    <script src="../JS/Auth.js"></script>
</body>
</html>
