<?php
session_start();
$student_id = $_SESSION['student_id'] ?? '';

// DB connection settings
$host = "localhost";
$user = "root";
$password = "";
$database = "edu_performance";

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch attendance records
$sql = "SELECT * FROM attendance WHERE student_id = '$student_id' ORDER BY date DESC";
$result = $conn->query($sql);

// Optional: Fetch student name from first record
$student_name = "";
if ($result->num_rows > 0) {
    $first_row = $result->fetch_assoc();
    $student_name = $first_row['student_name'];
    $result->data_seek(0); // Reset pointer for loop
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance Result</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header id="header">
    <nav>
      <div class="nav-container">
        <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
        <span>ATTENDANCE RECORD</span>
        <div class="nav-buttons">
          <a href="student_dashboard.html"><button>Back</button></a>
          <a href="student_dashboard.html"><button>Dashboard</button></a>
        </div>
      </div>
    </nav>
  </header>

  <main class="attendance-main">
    <section class="attendance-container">
      <h2><?php echo htmlspecialchars($student_id); ?> - <?php echo $student_name ? " $student_name" : ""; ?></h2>

      <?php if ($result->num_rows > 0): ?>
        <div class="attendance-table">
          <div class="table-header">
            <div class="table-cell">Date</div>
            <div class="table-cell">Course Unit</div>
            <div class="table-cell">Status</div>
            <div class="table-cell">Lecturer</div>
          </div>

          <?php while($row = $result->fetch_assoc()): ?>
            <div class="table-row">
              <div class="table-cell"><?php echo htmlspecialchars($row['date']); ?></div>
              <div class="table-cell"><?php echo htmlspecialchars($row['course_unit']); ?></div>
              <div class="table-cell"><?php echo ucfirst($row['status']); ?></div>
              <div class="table-cell"><?php echo htmlspecialchars($row['lecturer']); ?></div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <p>No attendance records found for Student ID: <?php echo htmlspecialchars($student_id); ?></p>
      <?php endif; ?>

    </section>
  </main>

  <footer>
    <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
  </footer>
</body>
</html>

<?php $conn->close(); ?>
