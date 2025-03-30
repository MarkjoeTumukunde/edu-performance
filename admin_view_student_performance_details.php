<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

$student_id = $_GET['student_id'] ?? '';

if (!$student_id) {
    echo "Invalid student ID";
    exit;
}

function calculateGrade($mark) {
    if ($mark >= 80) return 'A';
    if ($mark >= 75) return 'B+';
    if ($mark >= 70) return 'B';
    if ($mark >= 65) return 'C+';
    if ($mark >= 60) return 'C';
    if ($mark >= 55) return 'D+';
    if ($mark >= 50) return 'D';
    return 'F';
}

function gradePoint($grade) {
    return [
        'A' => 5, 'B+' => 4.5, 'B' => 4,
        'C+' => 3.5, 'C' => 3, 'D+' => 2.5,
        'D' => 2, 'F' => 0
    ][$grade] ?? 0;
}

// Get student info
$student_query = $conn->query("SELECT name FROM students WHERE student_id = '$student_id'");
$student = $student_query->fetch_assoc();
$name = $student['name'];

// Get marks
$results = $conn->query("
  SELECT sm.*, au.name AS lecturer_name 
  FROM student_marks sm
  LEFT JOIN admin_users au ON sm.lecturer = au.name
  WHERE sm.student_id = '$student_id'
");



// GPA calculation
$total_cu = 0;
$total_points = 0;
$retakes = [];

$marks_data = [];

while ($row = $results->fetch_assoc()) {
    $grade = calculateGrade($row['marks']);
    $points = gradePoint($grade) * $row['cu'];
    $total_cu += $row['cu'];
    $total_points += $points;

    if ($row['marks'] < 50) {
        $retakes[] = $row['course_unit'];
    }

    $marks_data[] = [
      'course_unit' => $row['course_unit'],
      'marks' => $row['marks'],
      'grade' => $grade,
      'cu' => $row['cu'],
      'lecturer' => $row['lecturer_name'] ?? $row['lecturer'] // fallback if name not found
  ];  
}

$gpa = $total_cu ? round($total_points / $total_cu, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Performance Details - <?= htmlspecialchars($name) ?></title>
  <link rel="stylesheet" href="style.css">
  <style>
    main {
      padding: 20px;
    }
    .details-heading {
      text-align: center;
      margin: 30px 0 20px;
    }
    .student-info {
      width: 86%;
      margin: 0 auto 30px;
      padding: 20px;
      background: #f9f9f9;
    }
    .student-info p {
      margin: 5px 0;
      font-size: 16px;
    }
    .details-table {
      width: 90%;
      margin: 0 auto 30px;
      border-collapse: collapse;
      background-color: #fff;
    }

    .student-info h2 {
      color: #0056b3;
      font-weight: 600;
      margin-bottom: 0;
    }
    .details-table th, .details-table td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }
    .details-table th {
      background-color: #007bff;
      color: white;
    }
    .actions {
      width: 90%;
      margin: 20px auto;
      text-align: center;
    }
    .actions button {
      margin: 10px;
      padding: 10px 20px;
      border: none;
      background-color: #007bff;
      color: white;
      border-radius: 4px;
      cursor: pointer;
    }
    .actions button:hover {
      background-color: #0056b3;
    }

  </style>
</head>
<body>

<header id="header">
  <nav>
    <div class="nav-container">
      <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
      <span>ADMIN</span>
      <div class="nav-buttons">
        <a href="admin_view_student_performance.php"><button>← Back</button></a>
      </div>
    </div>
  </nav>
</header>

<main>
  <h2 class="details-heading">Performance Details</h2>

  <div class="student-info">
  <h2> STUDENT PERFORMANCE DETAILS</h2>
  <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
  <p><strong>Student ID:</strong> <?= htmlspecialchars($student_id) ?></p>
  <p><strong>Total CU:</strong> <?= $total_cu ?></p>
  <p><strong>GPA / CGPA:</strong> <?= $gpa ?></p>
  <p><strong>Retake Courses:</strong> <?= !empty($retakes) ? implode(', ', $retakes) : 'None' ?></p>
  </div>

  <?php if (empty($marks_data)): ?>
    <div style="text-align: center; margin:10px 0; font-size: 18px; color: red;">
      No results found for this student yet.
    </div>
  <?php else: ?>
    <table class="details-table" id="performanceTable">
      <thead>
        <tr>
          <th>Course Unit</th>
          <th>Marks</th>
          <th>Grade</th>
          <th>Credit Units (CU)</th>
          <th>Lecturer</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($marks_data as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['course_unit']) ?></td>
            <td><?= $row['marks'] ?></td>
            <td><?= $row['grade'] ?></td>
            <td><?= $row['cu'] ?></td>
            <td><?= htmlspecialchars($row['lecturer'] ?? 'N/A') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

</main>
<div class="actions">
    <?php if (!empty($marks_data)): ?>
      <button onclick="window.print()">🖨️ Print Page</button>
      <button onclick="exportToCSV()">📄 Export to CSV</button>
    <?php endif; ?>
</div>


<footer>
  <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
</footer>

<script>
  function exportToCSV() {
    let table = document.getElementById("performanceTable");
    let rows = table.querySelectorAll("tr");
    let csv = [];

    rows.forEach(row => {
      let cols = row.querySelectorAll("th, td");
      let rowData = Array.from(cols).map(col => `"${col.innerText.trim()}"`);
      csv.push(rowData.join(","));
    });

    let csvContent = csv.join("\n");
    let blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    let link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "student_performance_<?= $student_id ?>.csv";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
</script>

</body>
</html>
