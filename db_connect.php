<?php
$host = "localhost";
$port = 3308; // Your MariaDB runs on port 3308
$user = "root";
$password = "";
$dbname = "edu_performance";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
