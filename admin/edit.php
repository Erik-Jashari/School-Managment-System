<?php 
    include '../config/database.php';

    $errorMessage = '';
    $successMessage = '';

    // Determine what type of entity we're editing
    if (isset($_GET['UsersID'])) {
        $id = $_GET['UsersID'];
        $entityType = 'user';
    } elseif (isset($_GET['GroupID'])) {
        $id = $_GET['GroupID'];
        $entityType = 'group';
    } elseif (isset($_GET['SubjectID'])) {
        $id = $_GET['SubjectID'];
        $entityType = 'subject';
    } elseif (isset($_GET['LessonID'])) {
        $id = $_GET['LessonID'];
        $entityType = 'lesson';
    } else {
        header("Location: users.php");
        exit;
    }

    if ($entityType === 'user') {
        // Fetch all groups for the dropdown
        $groupsResult = $connection->query("SELECT GroupID, GroupName FROM Groups ORDER BY GroupName ASC");
        $groups = [];
        while ($g = $groupsResult->fetch_assoc()) {
            $groups[] = $g;
        }

        // Get user data
        $sql = "SELECT * FROM users WHERE UsersID=$id";
        $result = $connection->query($sql);

        if (!$result || $result->num_rows == 0) {
            die("User does not exist.");
        }

        $client = $result->fetch_assoc();
        $name = $client['Name'];
        $email = $client['Email'];
        $role = $client['Role'];

        // Get current group assignment
        $currentGroupId = '';
        $groupStmt = $connection->prepare("SELECT GroupID FROM Student_Groups WHERE UsersID = ? LIMIT 1");
        $groupStmt->bind_param('i', $id);
        $groupStmt->execute();
        $groupResult = $groupStmt->get_result();
        if ($groupRow = $groupResult->fetch_assoc()) {
            $currentGroupId = $groupRow['GroupID'];
        }
        $groupStmt->close();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $newGroupId = isset($_POST['groupId']) ? $_POST['groupId'] : '';

            // Validation
            if(empty($name) || empty($email) || empty($role)){
                $errorMessage = "All fields must be filled";
            } else {
                // Update user
                $sql = "UPDATE users 
                        SET Name='$name', Email='$email', Role='$role' 
                        WHERE UsersID=$id";

                $result = $connection->query($sql);

                if (!$result) {
                    $errorMessage = "Error updating user: " . $connection->error;
                } else {
                    // Update group assignment
                    $deleteStmt = $connection->prepare("DELETE FROM Student_Groups WHERE UsersID = ?");
                    $deleteStmt->bind_param('i', $id);
                    $deleteStmt->execute();
                    $deleteStmt->close();
                    
                    if(!empty($newGroupId)){
                        $newGroupId = (int)$newGroupId;
                        $insertStmt = $connection->prepare("INSERT INTO Student_Groups (UsersID, GroupID) VALUES (?, ?)");
                        $insertStmt->bind_param('ii', $id, $newGroupId);
                        $insertStmt->execute();
                        $insertStmt->close();
                    }
                    
                    $successMessage = "User updated successfully!";
                    header("Location: users.php");
                    exit;
                }
            }
        }
    } elseif ($entityType === 'group') {
        // Handle new group creation
        if ($id === 'new') {
            $groupName = '';
            $description = '';
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $groupName = $_POST['groupName'] ?? '';
                $description = $_POST['description'] ?? '';

                // Validation
                if(empty($groupName)){
                    $errorMessage = "Group name is required";
                } else {
                    // Insert new group
                    $groupName = $connection->real_escape_string($groupName);
                    $description = $connection->real_escape_string($description);
                    $sql = "INSERT INTO Groups (GroupName, Description) VALUES ('$groupName', '$description')";

                    $result = $connection->query($sql);

                    if (!$result) {
                        $errorMessage = "Error creating group: " . $connection->error;
                    } else {
                        $successMessage = "Group created successfully!";
                        header("Location: groups.php");
                        exit;
                    }
                }
            }
        } else {
            // Get existing group data
            $sql = "SELECT * FROM Groups WHERE GroupID=$id";
            $result = $connection->query($sql);

            if (!$result || $result->num_rows == 0) {
                die("Group does not exist.");
            }

            $group = $result->fetch_assoc();
            $groupName = $group['GroupName'];
            $description = $group['Description'];

            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $groupName = $_POST['groupName'] ?? '';
                $description = $_POST['description'] ?? '';

                // Validation
                if(empty($groupName)){
                    $errorMessage = "Group name is required";
                } else {
                    // Update group
                    $groupName = $connection->real_escape_string($groupName);
                    $description = $connection->real_escape_string($description);
                    $sql = "UPDATE Groups 
                            SET GroupName='$groupName', Description='$description' 
                            WHERE GroupID=$id";

                    $result = $connection->query($sql);

                    if (!$result) {
                        $errorMessage = "Error updating group: " . $connection->error;
                    } else {
                        $successMessage = "Group updated successfully!";
                        header("Location: groups.php");
                        exit;
                    }
                }
            }
        }
    } elseif ($entityType === 'subject') {
        // Handle new subject creation
        if ($id === 'new') {
            $subjectName = '';
            $description = '';
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $subjectName = $_POST['subjectName'] ?? '';
                $description = $_POST['description'] ?? '';

                // Validation
                if(empty($subjectName)){
                    $errorMessage = "Subject name is required";
                } else {
                    // Insert new subject
                    $subjectName = $connection->real_escape_string($subjectName);
                    $description = $connection->real_escape_string($description);
                    $sql = "INSERT INTO Subjects (Name, Description) VALUES ('$subjectName', '$description')";

                    $result = $connection->query($sql);

                    if (!$result) {
                        $errorMessage = "Error creating subject: " . $connection->error;
                    } else {
                        $successMessage = "Subject created successfully!";
                        header("Location: subjects.php");
                        exit;
                    }
                }
            }
        } else {
            // Get existing subject data
            $sql = "SELECT * FROM Subjects WHERE SubjectID=$id";
            $result = $connection->query($sql);

            if (!$result || $result->num_rows == 0) {
                die("Subject does not exist.");
            }

            $subject = $result->fetch_assoc();
            $subjectName = $subject['Name'];
            $description = $subject['Description'];

            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $subjectName = $_POST['subjectName'] ?? '';
                $description = $_POST['description'] ?? '';

                // Validation
                if(empty($subjectName)){
                    $errorMessage = "Subject name is required";
                } else {
                    // Update subject
                    $subjectName = $connection->real_escape_string($subjectName);
                    $description = $connection->real_escape_string($description);
                    $sql = "UPDATE Subjects 
                            SET Name='$subjectName', Description='$description' 
                            WHERE SubjectID=$id";

                    $result = $connection->query($sql);

                    if (!$result) {
                        $errorMessage = "Error updating subject: " . $connection->error;
                    } else {
                        $successMessage = "Subject updated successfully!";
                        header("Location: subjects.php");
                        exit;
                    }
                }
            }
        }
    } elseif ($entityType === 'lesson') {
        // Fetch groups and subjects for dropdowns
        $groupsResult = $connection->query("SELECT GroupID, GroupName FROM Groups ORDER BY GroupName ASC");
        $groupsList = [];
        while ($g = $groupsResult->fetch_assoc()) {
            $groupsList[] = $g;
        }

        $subjectsResult = $connection->query("SELECT SubjectID, Name FROM Subjects ORDER BY Name ASC");
        $subjectsList = [];
        while ($s = $subjectsResult->fetch_assoc()) {
            $subjectsList[] = $s;
        }

        if ($id === 'new') {
            $lessonTitle = '';
            $description = '';
            $selectedGroupId = '';
            $selectedSubjectId = '';

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $lessonTitle = $_POST['lessonTitle'] ?? '';
                $description = $_POST['description'] ?? '';
                $selectedGroupId = $_POST['groupId'] ?? '';
                $selectedSubjectId = $_POST['subjectId'] ?? '';

                if (empty($lessonTitle)) {
                    $errorMessage = "Lesson title is required";
                } else {
                    $lessonTitle = $connection->real_escape_string($lessonTitle);
                    $description = $connection->real_escape_string($description);
                    $groupVal = !empty($selectedGroupId) ? (int)$selectedGroupId : 'NULL';
                    $subjectVal = !empty($selectedSubjectId) ? (int)$selectedSubjectId : 'NULL';

                    $sql = "INSERT INTO Lessons (Title, Description, UsersID, GroupID, SubjectID) VALUES ('$lessonTitle', '$description', 1, $groupVal, $subjectVal)";
                    $result = $connection->query($sql);

                    if (!$result) {
                        $errorMessage = "Error creating lesson: " . $connection->error;
                    } else {
                        header("Location: lessons.php");
                        exit;
                    }
                }
            }
        } else {
            $sql = "SELECT * FROM Lessons WHERE LessonID=$id";
            $result = $connection->query($sql);

            if (!$result || $result->num_rows == 0) {
                die("Lesson does not exist.");
            }

            $lesson = $result->fetch_assoc();
            $lessonTitle = $lesson['Title'];
            $description = $lesson['Description'];
            $selectedGroupId = $lesson['GroupID'];
            $selectedSubjectId = $lesson['SubjectID'];

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $lessonTitle = $_POST['lessonTitle'] ?? '';
                $description = $_POST['description'] ?? '';
                $selectedGroupId = $_POST['groupId'] ?? '';
                $selectedSubjectId = $_POST['subjectId'] ?? '';

                if (empty($lessonTitle)) {
                    $errorMessage = "Lesson title is required";
                } else {
                    $lessonTitle = $connection->real_escape_string($lessonTitle);
                    $description = $connection->real_escape_string($description);
                    $groupVal = !empty($selectedGroupId) ? (int)$selectedGroupId : 'NULL';
                    $subjectVal = !empty($selectedSubjectId) ? (int)$selectedSubjectId : 'NULL';

                    $sql = "UPDATE Lessons SET Title='$lessonTitle', Description='$description', GroupID=$groupVal, SubjectID=$subjectVal WHERE LessonID=$id";
                    $result = $connection->query($sql);

                    if (!$result) {
                        $errorMessage = "Error updating lesson: " . $connection->error;
                    } else {
                        header("Location: lessons.php");
                        exit;
                    }
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $entityType === 'user' ? 'Edit User' : ($entityType === 'group' ? ($id === 'new' ? 'Create Group' : 'Edit Group') : ($entityType === 'subject' ? ($id === 'new' ? 'Create Subject' : 'Edit Subject') : ($id === 'new' ? 'Create Lesson' : 'Edit Lesson'))); ?></title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/edit.css">
</head>
<body>
    <div id="app-header"></div>
    <main class="admin-container">
        <div class="editForm">
            <h1><?php echo $entityType === 'user' ? 'Edit User' : ($entityType === 'group' ? ($id === 'new' ? 'Create Group' : 'Edit Group') : ($entityType === 'subject' ? ($id === 'new' ? 'Create Subject' : 'Edit Subject') : ($id === 'new' ? 'Create Lesson' : 'Edit Lesson'))); ?></h1>
            <p class="subtitle"><?php echo $entityType === 'user' ? 'Update user information' : ($entityType === 'group' ? ($id === 'new' ? 'Add a new group to the system' : 'Update group information') : ($entityType === 'subject' ? ($id === 'new' ? 'Add a new subject to the system' : 'Update subject information') : ($id === 'new' ? 'Add a new lesson to the system' : 'Update lesson information'))); ?></p>
            
            <?php 
                if(!empty($errorMessage)){
                    echo "<div class='error-message' style='display: block;'>$errorMessage</div>";
                }
                if(!empty($successMessage)){
                    echo "<div class='success-message' style='display: block;'>$successMessage</div>";
                }
            ?>
            
            <form method="post">
                <?php if ($entityType === 'user'): ?>
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo $name; ?>" placeholder="Enter name">

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" placeholder="Enter email">

                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="Student" <?php echo ($role == 'Student') ? 'selected' : ''; ?>>Student</option>
                        <option value="Admin" <?php echo ($role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>

                    <label for="groupId">Class/Group</label>
                    <select id="groupId" name="groupId">
                        <option value="">No class assigned</option>
                        <?php foreach($groups as $g): ?>
                            <option value="<?php echo $g['GroupID']; ?>" <?php echo ($currentGroupId == $g['GroupID']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($g['GroupName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="submit-button">Update User</button>
                <?php elseif ($entityType === 'group'): ?>
                    <label for="groupName">Group Name</label>
                    <input type="text" id="groupName" name="groupName" value="<?php echo htmlspecialchars($groupName); ?>" placeholder="Enter group name">

                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Enter group description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>

                    <button type="submit" class="submit-button"><?php echo $id === 'new' ? 'Create Group' : 'Update Group'; ?></button>
                <?php elseif ($entityType === 'subject'): ?>
                    <label for="subjectName">Subject Name</label>
                    <input type="text" id="subjectName" name="subjectName" value="<?php echo htmlspecialchars($subjectName); ?>" placeholder="Enter subject name">

                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Enter subject description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>

                    <button type="submit" class="submit-button"><?php echo $id === 'new' ? 'Create Subject' : 'Update Subject'; ?></button>
                <?php elseif ($entityType === 'lesson'): ?>
                    <label for="lessonTitle">Lesson Title</label>
                    <input type="text" id="lessonTitle" name="lessonTitle" value="<?php echo htmlspecialchars($lessonTitle); ?>" placeholder="Enter lesson title">

                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Enter lesson description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>

                    <label for="groupId">Group</label>
                    <select id="groupId" name="groupId">
                        <option value="">No group</option>
                        <?php foreach($groupsList as $g): ?>
                            <option value="<?php echo $g['GroupID']; ?>" <?php echo ($selectedGroupId == $g['GroupID']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($g['GroupName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="subjectId">Subject</label>
                    <select id="subjectId" name="subjectId">
                        <option value="">No subject</option>
                        <?php foreach($subjectsList as $s): ?>
                            <option value="<?php echo $s['SubjectID']; ?>" <?php echo ($selectedSubjectId == $s['SubjectID']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['Name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="submit-button"><?php echo $id === 'new' ? 'Create Lesson' : 'Update Lesson'; ?></button>
                <?php endif; ?>
            </form>
            
            <div class="edit-links-text">
                <a href="<?php echo $entityType === 'user' ? 'users.php' : ($entityType === 'group' ? 'groups.php' : ($entityType === 'subject' ? 'subjects.php' : 'lessons.php')); ?>">Back to <?php echo $entityType === 'user' ? 'Users' : ($entityType === 'group' ? 'Groups' : ($entityType === 'subject' ? 'Subjects' : 'Lessons')); ?></a>
            </div>
        </div>
    </main>
    <div id="app-footer"></div>
    <script src="../JS/HeaderFooter.js"></script>
    <script src="../JS/Auth.js"></script>
</body>
</html>