<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activity_name = $_POST['activity_name'] ?? '';
    $participationData = $_POST['participating'] ?? [];

    if (empty($activity_name) || empty($participationData)) {
        header("Location: lecturer_view_activities.php?activity=" . urlencode($activity_name) . "&success=0");
        exit();
    }

    // Prepare statement
    $stmt = $conn->prepare("
        INSERT INTO student_activity_status (student_id, activity_name, is_participating, last_updated)
        VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            is_participating = VALUES(is_participating),
            last_updated = NOW()
    ");

    foreach ($participationData as $student_id => $status) {
        $stmt->bind_param("ssi", $student_id, $activity_name, $status);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    header("Location: lecturer_view_activities.php?activity=" . urlencode($activity_name) . "&success=1");
    exit();
}
?>
