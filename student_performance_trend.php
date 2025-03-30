<?php
session_start();
include 'db_connect.php';

$student_id = $_SESSION['student_id'] ?? $_GET['student_id'] ?? '';

if (!$student_id) {
    echo "No student ID provided.";
    exit;
}

$query = $conn->prepare("SELECT course_unit, marks FROM student_marks WHERE student_id = ? ORDER BY id ASC");
$query->bind_param("s", $student_id);
$query->execute();
$result = $query->get_result();

$labels = [];
$marks = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['course_unit'];
    $marks[] = (int) $row['marks'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Performance Trend</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 30px;
      background: #f5f5f5;
    }
    canvas {
      max-width: 800px;
      margin: 0 auto;
      display: block;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .back-btn {
      display: block;
      text-align: center;
      margin-top: 20px;
    }
    .back-btn a {
      text-decoration: none;
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      border-radius: 4px;
    }
  </style>
</head>
<body>

<h2 style="text-align:center;">📊 Student Performance Trend</h2>

<canvas id="performanceChart"></canvas>

<div class="back-btn">
  <a href="student_dashboard.html">← Back to Dashboard</a>
</div>

<script>
  const ctx = document.getElementById('performanceChart').getContext('2d');

  const chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?= json_encode($labels) ?>,
      datasets: [{
        label: 'Marks',
        data: <?= json_encode($marks) ?>,
        borderColor: '#007bff',
        backgroundColor: 'rgba(0, 123, 255, 0.2)',
        tension: 0.3,
        fill: true,
        pointBackgroundColor: '#007bff'
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
          title: {
            display: true,
            text: 'Marks'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Course Units'
          }
        }
      }
    }
  });
</script>

</body>
</html>
