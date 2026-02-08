<?php
// AUTH.PHP - Authentication & Session Management

session_start();
require_once __DIR__ . '/../config/database.php';

// ========== SESSION CHECK FUNCTIONS ==========

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'Admin';
}

function isStudent() {
    return isLoggedIn() && $_SESSION['role'] === 'Student';
}

// ========== LOGIN / LOGOUT FUNCTIONS ==========

function login($email, $password) {
    global $connection;
    
    $email = mysqli_real_escape_string($connection, $email);
    $query = "SELECT UsersID AS id, Name AS name, Email AS email, Password AS password, Role AS role FROM Users WHERE Email = '$email'";
    $result = mysqli_query($connection, $query);
    $user = mysqli_fetch_assoc($result);
    
    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    // Store user info in session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    
    return ['success' => true, 'message' => 'Login successful', 'user' => $user];
}

function logout() {
    $_SESSION = [];
    session_destroy();
        header("Location: /School-Managment-System/Login.php");
    exit;
}

// ========== PAGE PROTECTION FUNCTIONS ==========

function sendNoCacheHeaders() {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

function requireLogin() {
    sendNoCacheHeaders();
    if (!isLoggedIn()) {
           header("Location: /School-Managment-System/Login.php");
        exit;
    }
}

function requireAdmin() {
    sendNoCacheHeaders();
    if (!isLoggedIn()) {
            header("Location: /School-Managment-System/Login.php");
        exit;
    }
    if (!isAdmin()) {
        header("Location: /School-Managment-System/Dashboard.html");
        exit;
    }
}

function requireStudent() {
    sendNoCacheHeaders();
    if (!isLoggedIn()) {
            header("Location: /School-Managment-System/Login.php");
        exit;
    }
    if (!isStudent()) {
        header("Location: /School-Managment-System/admin/");
        exit;
    }
}

// ========== REGISTRATION FUNCTION ==========

function register($name, $email, $password, $role = 'Student') {
    global $connection;
    
    // Check if email exists
    $email = mysqli_real_escape_string($connection, $email);
    $query = "SELECT id FROM Users WHERE email = '$email'";
    $result = mysqli_query($connection, $query);
    
    if (mysqli_fetch_assoc($result)) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Insert new user with hashed password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $name = mysqli_real_escape_string($connection, $name);
    $role = mysqli_real_escape_string($connection, $role);
    
    $query = "INSERT INTO Users (Name, Email, Password, Role) VALUES ('$name', '$email', '$hashedPassword', '$role')";
    
    if (mysqli_query($connection, $query)) {
        return ['success' => true, 'message' => 'Registration successful'];
    } else {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}
?>
