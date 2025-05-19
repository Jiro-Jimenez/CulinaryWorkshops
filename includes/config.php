<?php
// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'culinary_workshop');

// Establish database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Site settings
define('SITE_NAME', 'Culinary Workshop');
define('SITE_URL', 'http://localhost/culinary-workshop');

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Function to check if user is a client
function isClient() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'client';
}

// Function to redirect
function redirect($location) {
    header("Location: $location");
    exit;
}

// Function to sanitize input
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($input));
};
?>