<?php 
    include '../config/database.php';

    $errorMessage = '';
    $successMessage = '';

    if (!isset($_GET['UsersID'])) {
        header("Location: users.php");
        exit;
    }

    $id = $_GET['UsersID'];

    // Merr të dhënat ekzistuese të klientit

    $sql = "SELECT * FROM users WHERE UsersID=$id";
    $result = $connection->query($sql);

    if (!$result || $result->num_rows == 0) {
        die("Klienti nuk ekziston.");
    }

    $client = $result->fetch_assoc();
    $name = $client['Name'];
    $email = $client['Email'];
    $role = $client['Role'];

    // Kontrollo nëse forma u dërgua (POST)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        //Validimi
        if(empty($name) || empty($email) || empty($role)){
            $errorMessage = "Te gjitha fushat duhet te plotesohen";
        } else {
            // Përditëso të dhënat duke përdorur ID nga URL
            $sql = "UPDATE users 
                    SET Name='$name', Email='$email', Role='$role' 
                    WHERE UsersID=$id";

            $result = $connection->query($sql);

            if (!$result) {
                $errorMessage = "Gabim gjatë përditësimit të klientit: " . $connection->error;
            } else {
                $successMessage = "Klienti u përditësua me sukses!";
                header("Location: users.php");
                exit;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/edit.css">
</head>
<body>
    <div id="app-header"></div>
    <main class="admin-container">
        <div class="editForm">
            <h1>Edit User</h1>
            <p class="subtitle">Update user information</p>
            
            <?php 
                if(!empty($errorMessage)){
                    echo "<div class='error-message' style='display: block;'>$errorMessage</div>";
                }
                if(!empty($successMessage)){
                    echo "<div class='success-message' style='display: block;'>$successMessage</div>";
                }
            ?>
            
            <form method="post">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo $name; ?>" placeholder="Enter name">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" placeholder="Enter email">

                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="Student" <?php echo ($role == 'Student') ? 'selected' : ''; ?>>Student</option>
                    <option value="Admin" <?php echo ($role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                </select>

                <button type="submit" class="submit-button">Update User</button>
            </form>
            
            <div class="edit-links-text">
                <a href="users.php">Back to Users</a>
            </div>
        </div>
    </main>
    <div id="app-footer"></div>
    <script src="../JS/HeaderFooter.js"></script>
    <script src="../JS/Auth.js"></script>
</body>
</html>