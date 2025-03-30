<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// DB config
$host = "localhost";
$user = "root";
$password = "";
$database = "edu_performance";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// ✅ Get student ID from session (not POST)
$student_id = $_SESSION['student_id'] ?? null;

if (!$student_id) {
    echo json_encode(["error" => "Student not logged in."]);
    exit;
}

// ✅ Fetch data using the new one-row-per-unit structure
$sql = "SELECT course_unit, marks FROM student_marks WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$marks = [];

while ($row = $result->fetch_assoc()) {
    $unit = $row["course_unit"];
    $mark = $row["marks"];

    if (!empty($unit) && is_numeric($mark)) {
        $labels[] = $unit;
        $marks[] = (int)$mark;
    }
}

// ✅ Respond with data or error
if (empty($labels)) {
    echo json_encode([
        "labels" => [],
        "marks" => [],
        "error" => "No data found for student_id = " . $student_id
    ]);
} else {
    echo json_encode([
        "labels" => $labels,
        "marks" => $marks
    ]);
}
