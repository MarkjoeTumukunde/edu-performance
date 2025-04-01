<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Detect local vs live environment
$isLocal = in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);

// Set DB credentials based on environment
if ($isLocal) {
    // ✅ Local XAMPP/MySQL settings
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "edu_performance";
} else {
    // ✅ Live server (InfinityFree)
    $host = "sql103.infinityfree.com";
    $username = "if0_38636618";
    $password = "Crunk5174";
    $dbname = "if0_38636618_edu_performance";
}

// Connect to database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
