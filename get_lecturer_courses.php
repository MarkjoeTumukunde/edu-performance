<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['course_units'])) {
    echo json_encode([
        "courses" => $_SESSION['course_units']
    ]);
} else {
    echo json_encode([
        "error" => "No courses found."
    ]);
}
?>
