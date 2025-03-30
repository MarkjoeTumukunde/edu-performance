<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

// Capture filters
$selected_year = $_GET['year'] ?? 'all';
$selected_activity = $_GET['activity'] ?? 'all';

// Get unique activity names for filter dropdown
$activity_result = $conn->query("SELECT DISTINCT activity_name FROM student_activity_status ORDER BY activity_name ASC");

// Build dynamic SQL
$sql = "SELECT s.student_id, s.name, s.year, a.activity_name, a.is_participating, a.last_updated
        FROM students s
        JOIN student_activity_status a ON s.student_id = a.student_id";

$conditions = [];
$params = [];
$types = "";

// Add conditions dynamically
if ($selected_year !== 'all') {
    $conditions[] = "s.year = ?";
    $params[] = $selected_year;
    $types .= "i";
}
if ($selected_activity !== 'all') {
    $conditions[] = "a.activity_name = ?";
    $params[] = $selected_activity;
    $types .= "s";
}

if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Student Activities - Admin</title>
  <link rel="stylesheet" href="style.css" />
  <style>

    .complaint-form-container {
      width: 85%;
      margin-top: -30px;
    }


    .filter-form {
      margin: 30px auto;
      text-align: center;
    }

    .filter-form select {
      padding: 10px;
      font-size: 16px;
      border-radius: 6px;
      margin: 0 10px;
    }

    .filter-form button {
      padding: 10px 16px;
      font-size: 16px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .filter-form button:hover {
      background-color: #0056b3;
    }

    .activity-table {
      width: 95%;
      margin: 0 auto 50px;
      border-collapse: collapse;
      background-color: #fff;
    }

    .activity-table th,
    .activity-table td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    .activity-table th {
      background-color: #f0f0f0;
    }

    .status-yes {
      color: green;
      font-weight: bold;
    }

    .status-no {
      color: red;
      font-weight: bold;
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
          <a href="admin_dashboard.html"><button>BACK</button></a>
        </div>
      </div>
    </nav>
  </header>

  <main class="complaint-main">
    <section class="complaint-form-container">
      <h2>Student Activity Participation</h2>

      <form class="filter-form" method="GET" action="">
        <label for="year">Year:</label>
        <select name="year" id="year">
          <option value="all" <?= $selected_year === 'all' ? 'selected' : '' ?>>All</option>
          <option value="1" <?= $selected_year === '1' ? 'selected' : '' ?>>Year 1</option>
          <option value="2" <?= $selected_year === '2' ? 'selected' : '' ?>>Year 2</option>
          <option value="3" <?= $selected_year === '3' ? 'selected' : '' ?>>Year 3</option>
          <option value="4" <?= $selected_year === '4' ? 'selected' : '' ?>>Year 4</option>
        </select>

        <label for="activity">Activity:</label>
        <select name="activity" id="activity">
          <option value="all" <?= $selected_activity === 'all' ? 'selected' : '' ?>>All</option>
          <?php while($row = $activity_result->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['activity_name']) ?>"
              <?= $selected_activity === $row['activity_name'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($row['activity_name']) ?>
            </option>
          <?php endwhile; ?>
        </select>

        <button type="submit">Filter</button>
      </form>

      <?php if ($result->num_rows > 0): ?>
        <table class="activity-table">
          <thead>
            <tr>
              <th>Student ID</th>
              <th>Name</th>
              <th>Year</th>
              <th>Activity</th>
              <th>Status</th>
              <th>Last Updated</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['student_id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= 'Year ' . htmlspecialchars($row['year']) ?></td>
                <td><?= htmlspecialchars($row['activity_name']) ?></td>
                <td>
                  <?= $row['is_participating']
                      ? '<span class="status-yes">Participating</span>'
                      : '<span class="status-no">Not Participating</span>' ?>
                </td>
                <td><?= htmlspecialchars($row['last_updated']) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p style="text-align:center;">No activity data found for the selected filters.</p>
      <?php endif; ?>
    </section>
  </main>

  <footer>
    <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
  </footer>
</body>
</html>
