<?php
session_start();
include 'db_connect.php';

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

$students = $conn->query("SELECT * FROM students");
$all_performance = [];

while ($student = $students->fetch_assoc()) {
    $student_id = $student['student_id'];
    $name = $student['name'];
    $result = $conn->query("SELECT * FROM student_marks WHERE student_id = '$student_id'");

    $total_cu = 0;
    $total_points = 0;
    $retakes = [];

    while ($row = $result->fetch_assoc()) {
        $grade = calculateGrade($row['marks']);
        $points = gradePoint($grade) * $row['cu'];

        $total_cu += $row['cu'];
        $total_points += $points;

        if ($row['marks'] < 50) {
            $retakes[] = $row['course_unit'];
        }
    }

    $gpa = $total_cu ? round($total_points / $total_cu, 2) : 0;

    $all_performance[] = [
        'student_id' => $student_id,
        'name' => $name,
        'gpa' => $gpa,
        'cu' => $total_cu,
        'retakes' => $retakes
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Student Performance</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .search-container {
      width: 90%;
      margin: 30px auto;
      text-align: center;
    }
    .search-container input {
      padding: 10px;
      width: 300px;
      margin-bottom: 20px;
    }

    .search-container button {
      padding: 12px;
      margin-left: 600px;
      background-color: #ffc107;
      border: none;
      color: #000;
      cursor: pointer;
    }

    .search-container button:hover {
      background-color: #218838;
      color: #fff;
    }

    .performance-table {
      width: 90%;
      margin: 20px auto;
      border-collapse: collapse;
      background: #fff;
    }
    .performance-table th, .performance-table td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }
    
    .performance-table th {
      background-color: #007bff;
      color: white;
    }
    .retakes {
      color: red;
      font-weight: bold;
    }
    .action-btn {
      background-color: #28a745;
      color: white;
      padding: 6px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .action-btn:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>

<header id="header">
  <nav>
    <div class="nav-container">
      <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
      <span>ADMIN: STUDENT PERFORMANCE</span>
      <div class="nav-buttons">
        <a href="admin_dashboard.html"><button>← Dashboard</button></a>
      </div>
    </div>
  </nav>
</header>

<main>
  <div class="search-container">
    <input type="text" id="searchBox" placeholder="Search by Student ID or Name...">
    <a href="admin_all_students_trend.php"><button>View Perfomance Graph</button></a>
  </div>

  <table class="performance-table" id="performanceTable">
    <thead>
      <tr>
        <th>Student ID</th>
        <th>Name</th>
        <th>Total CU</th>
        <th>GPA / CGPA</th>
        <th>Retake Courses</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($all_performance as $student): ?>
        <tr class="student-row" data-search="<?= strtolower($student['student_id'] . ' ' . $student['name']) ?>">
          <td><?= htmlspecialchars($student['student_id']) ?></td>
          <td><?= htmlspecialchars($student['name']) ?></td>
          <td><?= $student['cu'] ?></td>
          <td><?= $student['gpa'] ?></td>
          <td class="<?= !empty($student['retakes']) ? 'retakes' : '' ?>">
            <?= !empty($student['retakes']) ? implode(', ', $student['retakes']) : 'None' ?>
          </td>
          <td>
            <a href="admin_view_student_performance_details.php?student_id=<?= urlencode($student['student_id']) ?>">
              <button class="action-btn">View Details</button>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>

<footer>
  <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
</footer>

<script>
  const searchBox = document.getElementById("searchBox");
  const rows = document.querySelectorAll(".student-row");

  searchBox.addEventListener("input", function () {
    const query = this.value.toLowerCase();
    rows.forEach(row => {
      const content = row.getAttribute("data-search");
      row.style.display = content.includes(query) ? "table-row" : "none";
    });
  });
</script>

</body>
</html>
