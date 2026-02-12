<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - Admin</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/users.css">
    <link rel="stylesheet" href="css/reviews.css">
</head>
<body>
    <div id="app-header"></div>
    <main class="admin-container">
        <div class="header-actions">
            <h1>Reviews Management</h1>
        </div>

        <?php
            include '../config/database.php';
        ?>

        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Lesson</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT r.ReviewsID, r.Comment, r.Rating, r.CreatedAt,
                                   u.Name AS StudentName, u.UsersID,
                                   l.Title AS LessonTitle, l.LessonID
                            FROM Reviews r
                            JOIN Users u ON r.UsersID = u.UsersID
                            JOIN Lessons l ON r.LessonID = l.LessonID
                            ORDER BY r.CreatedAt DESC";

                    $result = $connection->query($sql);

                    if (!$result) {
                        die("Error fetching reviews from the database.");
                    }

                    if ($result->num_rows === 0) {
                        echo "<tr><td colspan='7' class='no-reviews'>No reviews found.</td></tr>";
                    }

                    while ($row = $result->fetch_assoc()) {
                        // Build star display
                        $stars = '';
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $row['Rating']) {
                                $stars .= '<span>★</span>';
                            } else {
                                $stars .= '<span class="empty">★</span>';
                            }
                        }

                        $comment = htmlspecialchars($row['Comment'] ?? '—');
                        $studentName = htmlspecialchars($row['StudentName']);
                        $lessonTitle = htmlspecialchars($row['LessonTitle']);

                        echo "
                            <tr>
                                <td>{$row['ReviewsID']}</td>
                                <td>{$studentName}</td>
                                <td>{$lessonTitle}</td>
                                <td><span class='star-rating'>{$stars}</span></td>
                                <td class='review-comment' title='{$comment}'>{$comment}</td>
                                <td>{$row['CreatedAt']}</td>
                                <td>
                                    <a href='delete.php?ReviewsID={$row['ReviewsID']}' onclick='return confirm(\"Are you sure you want to delete this review?\")'>Delete</a>
                                </td>
                            </tr>
                        ";
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
