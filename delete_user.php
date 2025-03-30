<?php
session_start();
include 'db_connect.php';

$id = $_GET['id'] ?? '';
$role = $_GET['role'] ?? '';

if (!$id || !$role) {
  die("Invalid request.");
}

if ($role === 'student') {
  $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
} else {
  $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ?");
}

$stmt->bind_param("s", $id);
if ($stmt->execute()) {
  header("Location: manage_users.php?deleted=1");
  exit();
} else {
  echo "❌ Failed to delete user.";
}
?>
