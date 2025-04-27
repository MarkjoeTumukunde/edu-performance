<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Determine server environment
$server_name = $_SERVER['SERVER_NAME'];
$isLocal = in_array($server_name, ['localhost', '127.0.0.1']);

// Assign credentials
if ($isLocal) {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "edu_performance";
} else {
    $host = "sql103.infinityfree.com";
    $username = "if0_38636618";
    $password = "Crunk5174";
    $dbname = "if0_38636618_edu_performance";
}

// Attempt connection
$conn = new mysqli($host, $username, $password, $dbname);

// Report connection status
if ($conn->connect_error) {
    die("❌ Failed to connect on " . ($isLocal ? "LOCAL" : "LIVE") . " server <br>
         Server: $server_name<br>
         Host: $host<br>
         User: $username<br>
         DB: $dbname<br>
         Error: " . $conn->connect_error);
}

// echo "Connection successful on " . ($isLocal ? "LOCAL" : "LIVE") . " server!";
?>
