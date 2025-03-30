<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

$student_id = $_SESSION['student_id'] ?? 'S001';

$sql = "SELECT * FROM student_complaints WHERE student_id = ? ORDER BY submitted_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Complaints - Educational Performance Tracking</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>

    .complaint-form-container{
      width: 850px;
    }

    .complaints-table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;

    }

    .complaints-table th,
    .complaints-table td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    .complaints-table th {
      background-color: #f2f2f2;
    }

    .status-pending {
      color: red;
      font-weight: bold;
    }

    .status-in-progress {
      color: orange;
      font-weight: bold;
    }

    .status-resolved {
      color: green;
      font-weight: bold;
    }

    .complaint-actions {
      margin-bottom: 20px;
    }

    .complaint-actions a {
      display: inline-block;
      margin-right: 10px;
      padding: 10px 14px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      transition: background-color 0.3s ease;
    }

    .complaint-actions a:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <header id="header">
    <nav>
      <div class="nav-container">
        <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
        <span>VIEW MY COMPLAINTS</span>
        <div class="nav-buttons">
          <a href="student_dashboard.html"><button>BACK</button></a>

        </div>
      </div>
    </nav>
  </header>

  <main class="complaint-main">
    <section class="complaint-form-container">
      <h2>My Submitted Complaints</h2>

      <?php if ($result->num_rows > 0): ?>
        <table class="complaints-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Type</th>
              <th>Status</th>
              <th id="admin-replies">Admin Reply</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['submitted_at']) ?></td>
                <td><?= htmlspecialchars($row['complaint_type']) ?></td>
                <td>
                  <?php
                    $status = strtolower(str_replace(' ', '-', $row['status']));
                    echo "<span class='status-{$status}'>" . htmlspecialchars($row['status']) . "</span>";
                  ?>
                </td>
                <td>
                  <?= isset($row['remark']) && $row['remark']
                    ? htmlspecialchars($row['remark'])
                    : '<em>Not yet replied</em>' ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>You have not submitted any complaints yet.</p>
      <?php endif; ?>

      <br />

      <div class="complaint-actions">
        <a href="complaint.html">Submit a New Complaint</a>
      </div>
    </section>
  </main>

  <footer>
    <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
  </footer>
</body>
</html>
