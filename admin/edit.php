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
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $entityType === 'user' ? 'Edit User' : ($entityType === 'group' ? ($id === 'new' ? 'Create Group' : 'Edit Group') : ($id === 'new' ? 'Create Subject' : 'Edit Subject')); ?></title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/edit.css">
</head>
<body>
    <div id="app-header"></div>
    <main class="admin-container">
        <div class="editForm">
            <h1><?php echo $entityType === 'user' ? 'Edit User' : ($entityType === 'group' ? ($id === 'new' ? 'Create Group' : 'Edit Group') : ($id === 'new' ? 'Create Subject' : 'Edit Subject')); ?></h1>
            <p class="subtitle"><?php echo $entityType === 'user' ? 'Update user information' : ($entityType === 'group' ? ($id === 'new' ? 'Add a new group to the system' : 'Update group information') : ($id === 'new' ? 'Add a new subject to the system' : 'Update subject information')); ?></p>
            
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
                    <input type="text" id="groupName" name="groupName" value="<?php echo htmlspecialchars($groupName); ?>" placeholder="Enter group name (e.g., Class 10-A)">

                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Enter group description (optional)" rows="4"><?php echo htmlspecialchars($description); ?></textarea>

                    <button type="submit" class="submit-button"><?php echo $id === 'new' ? 'Create Group' : 'Update Group'; ?></button>
                <?php elseif ($entityType === 'subject'): ?>
                    <label for="subjectName">Subject Name</label>
                    <input type="text" id="subjectName" name="subjectName" value="<?php echo htmlspecialchars($subjectName); ?>" placeholder="Enter subject name (e.g., Mathematics)">

                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Enter subject description (optional)" rows="4"><?php echo htmlspecialchars($description); ?></textarea>

                    <button type="submit" class="submit-button"><?php echo $id === 'new' ? 'Create Subject' : 'Update Subject'; ?></button>
                <?php endif; ?>
            </form>
            
            <div class="edit-links-text">
                <a href="<?php echo $entityType === 'user' ? 'users.php' : ($entityType === 'group' ? 'groups.php' : 'subjects.php'); ?>">Back to <?php echo $entityType === 'user' ? 'Users' : ($entityType === 'group' ? 'Groups' : 'Subjects'); ?></a>
            </div>
        </div>
    </main>
    <div id="app-footer"></div>
    <script src="../JS/HeaderFooter.js"></script>
    <script src="../JS/Auth.js"></script>
</body>
</html>