<?php
include 'db_connect.php';
header('Content-Type: application/json');

$student_id = $_GET['student_id'] ?? '';
$year1 = $_GET['year1'] ?? '';
$year2 = $_GET['year2'] ?? '';

$datasets = [];
$labels = [];
$title = "Average Student Performance";

if ($student_id && $year1 && $year2) {
  // Compare two years for one student
  $title = "Performance Comparison for Student $student_id: $year1 vs $year2";
  foreach ([$year1, $year2] as $year) {
    $stmt = $conn->prepare("SELECT course_unit, marks FROM student_marks WHERE student_id = ? AND year = ? ORDER BY course_unit");
    $stmt->bind_param("ss", $student_id, $year);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    $labels = []; // reset and use the latest loop
    while ($row = $result->fetch_assoc()) {
      $labels[] = $row['course_unit'];
      $data[] = (int)$row['marks'];
    }

    $datasets[] = [
      'label' => "Year $year",
      'data' => $data,
      'borderColor' => $year === $year1 ? '#007bff' : '#28a745',
      'backgroundColor' => 'transparent',
      'tension' => 0.3,
      'fill' => false
    ];
  }

} elseif ($student_id) {
  // Trend for a single student
  $title = "Performance Trend for Student $student_id";
  $stmt = $conn->prepare("SELECT course_unit, marks FROM student_marks WHERE student_id = ? ORDER BY id ASC");
  $stmt->bind_param("s", $student_id);
  $stmt->execute();
  $result = $stmt->get_result();

  $data = [];
  while ($row = $result->fetch_assoc()) {
    $labels[] = $row['course_unit'];
    $data[] = (int)$row['marks'];
  }

  $datasets[] = [
    'label' => 'Marks',
    'data' => $data,
    'borderColor' => '#007bff',
    'backgroundColor' => 'rgba(0,123,255,0.2)',
    'tension' => 0.3,
    'fill' => true
  ];

} else {
  // Average marks for all students
  $title = "Average Performance of All Students";
  $query = $conn->query("SELECT course_unit, AVG(marks) as avg_marks FROM student_marks GROUP BY course_unit ORDER BY course_unit");
  $data = [];
  while ($row = $query->fetch_assoc()) {
    $labels[] = $row['course_unit'];
    $data[] = round((float)$row['avg_marks'], 2);
  }

  $datasets[] = [
    'label' => 'Average Marks',
    'data' => $data,
    'borderColor' => '#17a2b8',
    'backgroundColor' => 'rgba(23,162,184,0.2)',
    'tension' => 0.3,
    'fill' => true
  ];
}

echo json_encode([
  'labels' => $labels,
  'datasets' => $datasets,
  'title' => $title
]);
