<?php
session_start();
include 'db_connect.php';

$filter_role = $_GET['role'] ?? 'all';
$users = [];

// Get students
if ($filter_role === 'student' || $filter_role === 'all') {
    $studentQuery = "SELECT student_id AS id, name, email, 'Student' AS role FROM students";
    $studentResult = $conn->query($studentQuery);
    while ($studentResult && $row = $studentResult->fetch_assoc()) {
        $users[] = $row;
    }
}

// Get admins/lecturers
if ($filter_role === 'lecturer' || $filter_role === 'administrator' || $filter_role === 'all') {
    $adminQuery = "SELECT id, name, email, role FROM admin_users";
    if ($filter_role !== 'all') {
        $safeRole = $conn->real_escape_string($filter_role);
        $adminQuery .= " WHERE role = '$safeRole'";
    }
    $adminResult = $conn->query($adminQuery);
    while ($adminResult && $row = $adminResult->fetch_assoc()) {
        $row['role'] = ucfirst($row['role']);
        $users[] = $row;
    }
}

// Count totals
$studentCount = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'] ?? 0;
$lecturerCount = $conn->query("SELECT COUNT(*) AS total FROM admin_users WHERE role = 'lecturer'")->fetch_assoc()['total'] ?? 0;
$adminCount = $conn->query("SELECT COUNT(*) AS total FROM admin_users WHERE role = 'administrator'")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - View Users</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .filter-form {
      text-align: center;
      margin-left: 110px;
      padding-right: 70px;
    }

    .filter-form select, .filter-form button {
      padding: 10px;
      font-size: 16px;
      margin-left: 10px;

    }

    .filter-form button {
      padding: 12px 20px;
    }

    .summary-cards {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-top: 30px;
    flex-wrap: wrap;
  }

  .summary-card {
    padding: 20px 30px;
    border-radius: 10px;
    font-size: 18px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    color: white;
    min-width: 220px;
    text-align: center;
    font-weight: bold;
  }

  /* Specific roles with their own colors */
  .summary-card.student {
    background-color: #007bff;
  }

  .summary-card.lecturer {
    background-color: #28a745;
  }

  .summary-card.admin {
    background-color: #ffc107;
    color: #000; /* Yellow background needs dark text */
  }
   .all_form {
    margin-bottom: 50px;
    margin-top: -100px;
   }

    .user-table {
      width: 90%;
      margin: 30px auto;
      border-collapse: collapse;
      background: white;
    }

    .user-table th, .user-table td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: left;
    }

    .user-table th {
      background-color: #007bff;
      color: white;
    }

    .no-users {
      text-align: center;
      color: gray;
      margin-top: 30px;
    }

    .filter_roles {
      border-radius: none;
      padding: 10px;
      background-color: #fff;
      padding: 10px 30px;
      margin-left: -50px;
      margin-right: 20px;
    }

    .table-input2 {
      border-radius: none;
    }

    .select_role {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 0;
    flex-grow: 1;
    box-sizing: border-box;
    width: 755px;
    text-align: center;
    margin-right: 20px;
    appearance: none; /* Remove default OS styling */
    -webkit-appearance: none; /* Safari */
    -moz-appearance: none; /* Firefox */
    color: #333;
  }
  </style>
</head>
<body>

<header id="header">
  <nav>
    <div class="nav-container">
      <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
      <span>ADMIN: VIEW USERS</span>
      <div class="nav-buttons">
        <a href="admin_dashboard.html"><button>Dashboard</button></a>
      </div>
    </div>
  </nav>
</header>

<main>
  <div class="all_form">
    <form method="GET" class="filter-form">  
    <label for="role" class="filter_roles"><strong>Filter by Role:</strong></label>
      <select name="role" class="select_role" >
        <option value="all"<?= $filter_role === 'all' ? 'selected' : '' ?>>All</option>
        <option value="student" <?= $filter_role === 'student' ? 'selected' : '' ?>>Students</option>
        <option value="lecturer" <?= $filter_role === 'lecturer' ? 'selected' : '' ?>>Lecturers</option>
        <option value="administrator" <?= $filter_role === 'administrator' ? 'selected' : '' ?>>Administrators</option>
      </select>
      <button type="submit" class="filter_button">Filter</button>
    </form>

  <?php if (count($users) > 0): ?>
    <table class="user-table">
      <thead>
        <tr>
          <th>User ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="no-users">No users found for the selected role.</p>
  <?php endif; ?>

  <div class="summary-cards">
    <div class="summary-card student">Students: <strong><?= $studentCount ?></strong></div>
    <div class="summary-card lecturer">Lecturers: <strong><?= $lecturerCount ?></strong></div>
    <div class="summary-card admin">Administrators: <strong><?= $adminCount ?></strong></div>
  </div>
  </div>
</main>

<footer>
  <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
</footer>

</body>
</html>
