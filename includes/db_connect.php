<?php
/**
 * MediConnect Database Connection Configuration
 */

// --- Database Credentials ---
$servername = "localhost";        
$username   = "root";  
$password   = ""; 
$dbname     = "mediconnect_db";    

// --- Attempt to Create Connection ---
$conn = new mysqli($servername, $username, $password, $dbname);

// --- Check Connection Status ---
if ($conn->connect_error) {
    // Stop execution and show a generic error
    die("❌ Server Error: Could not establish a database connection. Please contact support.");
}

// Optional: Set character set
$conn->set_charset("utf8mb4");

// The $conn object is now available.
?>