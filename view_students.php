<?php
include 'db_connect.php';

$sql = "SELECT student_id, name, email, course, year FROM students ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table class='attendance-table'>";
    echo "<tr class='table-header'>
            <th class='table-cell'>Student ID</th>
            <th class='table-cell'>Name</th>
            <th class='table-cell'>Email</th>
            <th class='table-cell'>Course</th>
            <th class='table-cell'>Year</th>
          </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='table-row'>
                <td class='table-cell'>" . htmlspecialchars($row['student_id']) . "</td>
                <td class='table-cell'>" . htmlspecialchars($row['name']) . "</td>
                <td class='table-cell'>" . htmlspecialchars($row['email']) . "</td>
                <td class='table-cell'>" . htmlspecialchars($row['course']) . "</td>
                <td class='table-cell'>" . htmlspecialchars($row['year']) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p style='text-align:center;'>No students found.</p>";
}

$conn->close();
?>
