<?php
// ✅ Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Database connection settings
$host = "localhost";
$user = "root";
$password = "";
$database = "edu_performance"; 

$conn = new mysqli($host, $user, $password, $database);

// ✅ Check for connection errors
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$inserted_count = 0;

// ✅ Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {

    // ✅ Get form data
    $student_ids = $_POST['student_id'];
    $student_names = $_POST['student_name'];
    $course_units = $_POST['course_unit'];
    $statuses = $_POST['status'];
    $dates = $_POST['date'];
    $lecturers = $_POST['lecturer'];

    for ($i = 0; $i < count($student_ids); $i++) {
      $student_id = isset($student_ids[$i]) ? $conn->real_escape_string(trim($student_ids[$i])) : '';
      $student_name = isset($student_names[$i]) ? $conn->real_escape_string(trim($student_names[$i])) : '';
      $course_unit = isset($course_units[$i]) ? $conn->real_escape_string(trim($course_units[$i])) : '';
      $status = isset($statuses[$i]) ? $conn->real_escape_string(trim($statuses[$i])) : '';
      $date = isset($dates[$i]) ? $conn->real_escape_string(trim($dates[$i])) : '';
      $lecturer = isset($lecturers[$i]) ? $conn->real_escape_string(trim($lecturers[$i])) : '';
  
      // 🚨 Debug output for each row
      echo "<p>Row $i: [$student_id, $student_name, $course_unit, $status, $date, $lecturer]</p>";
  
      if (empty($student_id) || empty($student_name) || empty($course_unit) || empty($status) || empty($date) || empty($lecturer)) {
          echo "<p style='color:orange;'>⚠️ Row $i skipped - missing field(s)</p>";
          continue;
      }
  
      $sql = "INSERT INTO attendance (student_id, student_name, course_unit, status, date, lecturer)
              VALUES ('$student_id', '$student_name', '$course_unit', '$status', '$date', '$lecturer')";
  
      if ($conn->query($sql)) {
          echo "<p style='color:green;'>✅ Row $i inserted successfully</p>";
          $inserted_count++;
      } else {
          echo "<p style='color:red;'>❌ MySQL Error on row $i: " . $conn->error . "</p>";
      }
  }
  

    $conn->close();
} else {
    die("❌ Invalid request — no POST data received.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance Submitted</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body>
  <main class="attendance-main">
    <section class="attendance-container">
      <i class="fa-solid fa-circle-check" style="color: #28a745; font-size: 60px; display: block; text-align: center; margin-bottom: 10px;"></i>

      <h2>Attendance Submission</h2>
      <p>
        <?php echo $inserted_count > 0
            ? "$inserted_count attendance record(s) successfully saved."
            : "No attendance records were saved. Please make sure the fields are filled correctly."; ?>
      </p>

      <div class="nav-buttons" style="margin-top: 20px; display: flex; justify-content: center; gap: 15px;">
        <a href="attendance.html"><button class="attendance_sub">Go Back</button></a>
        <a href="lecturer_dashboard.html"><button class="attendance_sub">Dashboard</button></a>
      </div>
    </section>
  </main>
</body>
</html>
