<?php
session_start();
$student_name = $_SESSION['student_name'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Dashboard - Educational Performance Tracking</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
</head>
<body>
  <header id="header">
    <nav>
      <div class="nav-container">
        <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
        <p class="welcome-msg">Welcome, <strong><?php echo htmlspecialchars($student_name); ?></strong> 👋</p>
        <span>STUDENT DASHBOARD</span>
        <div class="nav-buttons">
          <a href="login.html"><button>LOGOUT</button></a> 
        </div>
      </div>
    </nav>
  </header>

  <main class="dashboard-main">
    <section class="dashboard-buttons">
      <a href="fetch_student_grades.php" class="dashboard-button view-grades">View Grades</a>
      <a href="view_attendance_result.php" class="dashboard-button attendance-record">Attendance Record</a>
      <a href="performance_trends.html" class="dashboard-button performance-trends">Performance Trends</a>
      <a href="view_activities.php" class="dashboard-button view-activities">View Activities</a>
      <a href="complaint.html" class="dashboard-button submit-complaints">Submit Complaints</a>
    </section>
  </main>

  <footer>
    <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
  </footer>
</body>
</html>
