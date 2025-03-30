<?php
session_start();

$student_id = $_SESSION['student_id'] ?? '';
$student_name = $_SESSION['student_name'] ?? '';

if (empty($student_id)) {
    echo "<p style='color: red; text-align: center;'>❌ You must be logged in to view activities.</p>";
    exit();
}

include 'db_connect.php';

$sql = "SELECT activity_name, is_participating FROM student_activity_status WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Activities</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header id="header">
    <nav>
      <div class="nav-container">
        <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
        <span>STUDENT ACTIVITIES</span>
        <div class="nav-buttons">
          <a href="student_dashboard.html"><button>Back</button></a>
        </div>
      </div>
    </nav>
  </header>

  <main class="marksheet-main">
    <section class="marksheet-container">
      <div class="student-info" style="text-align: left; margin-bottom: 20px;">
        <p style="font-size: 20px; margin: 5px 0;"><strong>Name:</strong> <span style="color:#007bff;"><?php echo htmlspecialchars($student_name); ?></span></p>
        <p style="font-size: 18px; margin: 0;"><strong>Student ID:</strong> <span style="color:#007bff;"><?php echo htmlspecialchars($student_id); ?></span></p>
      </div>

      <div class="marks-table" style="background:#fff; border-radius: 10px; padding: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.05); color: #000;">
        <h3 style="margin-bottom: 10px; color:rgb(0, 0, 0);">🎯 Activity Participation</h3>
        <hr style="margin: 10px 0;" />

        <?php if (!empty($activities)): ?>
          <div class="table-row table-header">
            <div class="table-cell">Activity</div>
            <div class="table-cell">Status</div>
          </div>

          <?php foreach ($activities as $row): ?>
            <div class="table-row">
              <div class="table-cell"><?php echo htmlspecialchars($row['activity_name']); ?></div>
              <div class="table-cell">
                <?php if ($row['is_participating']): ?>
                  <span style="color: green;">✅ Participating</span>
                <?php else: ?>
                  <span style="color: red;">❌ Not Participating</span>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="font-size: 16px; color: #888;">No activities found.</p>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <footer>
    <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
  </footer>
</body>
</html>
