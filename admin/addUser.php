<?php 
    include '../config/database.php';

    $name = "";
    $email = "";
    $password = "";
    $role = "";

    $errorMessage = "";
    $successMessage = "";

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $name = $_POST['Name'];
        $email = $_POST['Email'];
        $password = $_POST['Password'];
        $role = $_POST['Role'];

        if(empty($name) || empty($email) || empty($password) || empty($role)){
            $errorMessage = "Te gjitha fushat duhet te plotesohen";
        }else{
            //shto userin ne databaze 
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users(Name, Email, Password, Role, CreatedAt) VALUES ('$name', '$email', '$hashedPassword', '$role', NOW())";
            $result = $connection->query($sql);

            if(!$result){
                $errorMessage = "Gabim ne shtimin e userit: " . $connection->error;
            }else{
                //pastroni fushat dhe vendosni mesazhin per sukses
                $name = "";
                $email = "";
                $password = "";
                $role = "";

                $successMessage = "Useri u regjistrua me sukses";
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
    <title>Register</title>
    <link rel="stylesheet" href="../CSS/Global.css">
    <link rel="stylesheet" href="css/addUsers.css">
</head>
<body>
    <div id="app-header"></div>

    <main class="admin-container">
        <div class="auth-card">
            <h1>Krijo Llogari</h1>

            <?php 
                if(!empty($errorMessage)){
                    echo "<div class='error-message'>$errorMessage</div>";
                }
                if(!empty($successMessage)){
                    echo "<div class='success-message'>$successMessage</div>";
                }
            ?>

            <form class="auth-form" id="register-form" method="POST" action="addUser.php">
                <label for="Name">Name</label>
                <input type="text" id="Name" name="Name" placeholder="Enter name" required value="<?php echo htmlspecialchars($name); ?>">

                <label for="Email">Email</label>
                <input type="email" id="Email" name="Email" placeholder="Enter email" required value="<?php echo htmlspecialchars($email); ?>">

                <label for="Password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="Password" name="Password" placeholder="Create a password" required minlength="8">
                    <button type="button" class="toggle-password" onclick="togglePassword('Password')">Show</button>
                </div>

                <label for="Role">Role</label>
                <select id="Role" name="Role" required>
                    <option value="" <?php echo empty($role) ? 'selected' : ''; ?>>Select a role</option>
                    <option value="Student" <?php echo ($role == 'Student') ? 'selected' : ''; ?>>Student</option>
                    <option value="Admin" <?php echo ($role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                </select>

                <button type="submit" class="submit-button">Create User</button>
            </form>
    </div>
    </main>

    <div id="app-footer"></div>
    <script src="../JS/HeaderFooter.js"></script>
    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const button = event.target;
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                button.textContent = 'Hide';
            } else {
                passwordField.type = 'password';
                button.textContent = 'Show';
            }
        }
    </script>
    <script src="../JS/Auth.js"></script>
</body>
</html>
