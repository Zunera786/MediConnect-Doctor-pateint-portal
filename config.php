<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mediconnect');

// Create connection
try {
    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if(mysqli_connect_errno()) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    // Set charset
    mysqli_set_charset($con, "utf8mb4");
    
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Check if user is logged in
function checkAuth($user_type = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    
    if ($user_type && $_SESSION['user_type'] !== $user_type) {
        header("Location: unauthorized.php");
        exit();
    }
}

// Sanitize input data
function sanitize($data) {
    global $con;
    return mysqli_real_escape_string($con, trim($data));
}
?>