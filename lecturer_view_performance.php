<?php
session_start();
include 'db_connect.php';

$lecturer = $_SESSION['lecturer_name'] ?? '';

if (!$lecturer) {
  echo "Access denied.";
  exit;
}

$stmt = $conn->prepare("SELECT * FROM student_marks WHERE lecturer = ? ORDER BY course_unit, student_id");
$stmt->bind_param("s", $lecturer);
$stmt->execute();
$result = $stmt->get_result();

$student_marks = [];
while ($row = $result->fetch_assoc()) {
  $student_marks[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Student Performance - Lecturer</title>
  <link rel="stylesheet" href="style.css">
  <style>
    main {
      padding: 30px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .table-container {
      width: 95%;
      margin: auto;
      overflow-x: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }
    th {
      background-color: #007bff;
      color: white;
    }
    .search-bar {
      width: 95%;
      margin: 20px auto;
      text-align: center;
    }
    .search-bar input {
      padding: 10px;
      width: 300px;
    }
  </style>
</head>
<body>
  <header id="header">
    <nav>
      <div class="nav-container">
        <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
        <span>LECTURER: STUDENT PERFORMANCE</span>
        <div class="nav-buttons">
          <a href="lecturer_dashboard.html"><button>← Dashboard</button></a>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <h2>📊 Student Performance for Your Courses</h2>

    <div class="search-bar">
      <input type="text" id="searchBox" placeholder="Search by Student ID or Course Unit...">
    </div>

    <div class="table-container">
      <table id="performanceTable">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Course Unit</th>
            <th>Marks</th>
            <th>Grade</th>
            <th>CU</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($student_marks as $row): ?>
            <tr data-search="<?= strtolower($row['student_id'] . ' ' . $row['course_unit']) ?>">
              <td><?= htmlspecialchars($row['student_id']) ?></td>
              <td><?= htmlspecialchars($row['student_name']) ?></td>
              <td><?= htmlspecialchars($row['course_unit']) ?></td>
              <td><?= $row['marks'] ?></td>
              <td><?= $row['grade'] ?></td>
              <td><?= $row['cu'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <footer>
    <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
  </footer>

  <script>
    const searchBox = document.getElementById("searchBox");
    const rows = document.querySelectorAll("#performanceTable tbody tr");

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
