<?php
include 'db_connect.php';

// Fetch all students who have activities
$sql = "SELECT student_id, activities FROM students WHERE activities IS NOT NULL AND activities != ''";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $student_id = $row['student_id'];
        $activity_list = array_map('trim', explode(',', $row['activities']));

        foreach ($activity_list as $activity_name) {
            // Check if already inserted
            $check = $conn->prepare("SELECT id FROM student_activity_status WHERE student_id = ? AND activity_name = ?");
            $check->bind_param("ss", $student_id, $activity_name);
            $check->execute();
            $check->store_result();

            if ($check->num_rows === 0) {
                // Insert into activity status
                $insert = $conn->prepare("INSERT INTO student_activity_status (student_id, activity_name, is_participating) VALUES (?, ?, 1)");
                $insert->bind_param("ss", $student_id, $activity_name);
                $insert->execute();
                $insert->close();
            }

            $check->close();
        }
    }

    echo "<p style='color: green; font-family: sans-serif; text-align: center;'>✅ Activity status initialized successfully!</p>";
} else {
    echo "<p style='color: orange; font-family: sans-serif; text-align: center;'>⚠️ No student activities found to process.</p>";
}

$conn->close();
?>
