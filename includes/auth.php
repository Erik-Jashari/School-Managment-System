<?php
// AUTH.PHP - Authentication & Session Management

session_start();
require_once __DIR__ . '/../config/database.php';

// ========== PASSWORD FUNCTIONS ==========

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

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

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

// ========== LOGIN / LOGOUT FUNCTIONS ==========

function login($email, $password) {
    $pdo = getConnection();
    
    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !verifyPassword($password, $user['password'])) {
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
    header("Location: /School-Managment-System/Login.html");
    exit;
}

// ========== PAGE PROTECTION FUNCTIONS ==========

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /School-Managment-System/Login.html");
        exit;
    }
}

function requireAdmin() {
    if (!isLoggedIn()) {
        header("Location: /School-Managment-System/Login.html");
        exit;
    }
    if (!isAdmin()) {
        header("Location: /School-Managment-System/Dashboard.html");
        exit;
    }
}

function requireStudent() {
    if (!isLoggedIn()) {
        header("Location: /School-Managment-System/Login.html");
        exit;
    }
    if (!isStudent()) {
        header("Location: /School-Managment-System/admin/");
        exit;
    }
}

// ========== REGISTRATION FUNCTION ==========

function register($name, $email, $password, $role = 'Student') {
    $pdo = getConnection();
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Insert new user with hashed password
    $hashedPassword = hashPassword($password);
    $stmt = $pdo->prepare("INSERT INTO Users (name, email, password, role) VALUES (?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, $email, $hashedPassword, $role]);
        return ['success' => true, 'message' => 'Registration successful'];
    } catch (Exception $e) {
        error_log("Registration Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed'];
    }
}
?>
