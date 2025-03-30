<?php
include 'db_connect.php';

header('Content-Type: application/json');

$student_id = $_POST['student_id'] ?? '';

if (!$student_id) {
    echo json_encode(["error" => "No student ID provided"]);
    exit;
}

$stmt = $conn->prepare("SELECT name, course, year FROM students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "name" => $row["name"],
        "course" => $row["course"],
        "year" => $row["year"] ?? ""
    ]);
} else {
    echo json_encode(["error" => "Student not found"]);
}

$conn->close();
?>
