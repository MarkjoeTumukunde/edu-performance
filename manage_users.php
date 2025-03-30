<?php
session_start();
include 'db_connect.php';

$filter_role = $_GET['role'] ?? 'all';
$search = $_GET['search'] ?? '';
$users = [];

// Search condition
$search_condition = $search ? "AND (name LIKE '%$search%' OR email LIKE '%$search%')" : "";

// Get students
if ($filter_role === 'student' || $filter_role === 'all') {
    $studentQuery = "SELECT student_id AS id, name, email, 'Student' AS role FROM students 
                     WHERE 1=1 $search_condition";
    $result = $conn->query($studentQuery);
    while ($result && $row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Get admins/lecturers
if ($filter_role === 'lecturer' || $filter_role === 'administrator' || $filter_role === 'all') {
    $adminQuery = "SELECT id, name, email, role FROM admin_users 
                   WHERE 1=1 " . ($filter_role !== 'all' ? "AND role = '$filter_role' " : '') . $search_condition;
    $result = $conn->query($adminQuery);
    while ($result && $row = $result->fetch_assoc()) {
        $row['role'] = ucfirst($row['role']);
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Users</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .filter-form {
      text-align: center;
      margin-left: 110px;
      padding-right: 70px;
    }

    .filter-form select, .filter-form input[type="text"], .filter-form button {
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

    .summary-card.student { background-color: #007bff; }
    .summary-card.lecturer { background-color: #28a745; }
    .summary-card.admin { background-color: #ffc107; color: #000; }

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
      padding: 10px 30px;
      background-color: #fff;
      margin-left: -50px;
      margin-right: 20px;
    }

    .select_role {
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 0;
      width: 200px;
      text-align: center;
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      color: #333;
      margin-right: 20px;
    }

    .action-btns button {
      padding: 8px 12px;
      font-size: 14px;
      border: none;
      border-radius: 5px;
      margin-right: 8px;
      cursor: pointer;
    }

    .edit-btn {
      background-color: #28a745;
      color: white;
    }

    .delete-btn {
      background-color: #dc3545;
      color: white;
    }

    .add-btn {
      background-color: #ffc107;
      color: black;
      padding: 10px 20px;
      border-radius: 5px;
      border: none;
      font-weight: bold;
      cursor: pointer;
      float: right;
      margin-right: 90px;
      margin-bottom: 20px;
    }

    .add-btn {
    display: inline-block;
    background-color: #ffc107;
    color: black;
    padding: 10px 20px;
    border-radius: 5px;
    font-weight: bold;
    text-decoration: none;
    font-size: 16px;
  }

  </style>
</head>
<body>

<header id="header">
  <nav>
    <div class="nav-container">
      <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
      <span>ADMIN: MANAGE USERS</span>
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
      <select name="role" class="select_role">
        <option value="all" <?= $filter_role === 'all' ? 'selected' : '' ?>>All</option>
        <option value="student" <?= $filter_role === 'student' ? 'selected' : '' ?>>Students</option>
        <option value="lecturer" <?= $filter_role === 'lecturer' ? 'selected' : '' ?>>Lecturers</option>
        <option value="administrator" <?= $filter_role === 'administrator' ? 'selected' : '' ?>>Administrators</option>
      </select>
      <input type="text" name="search" placeholder="Search name or email" value="<?= htmlspecialchars($search) ?>">
      <button type="submit" class="filter_button">Search</button>
      <a href="add_user.php" class="add-btn">➕ Add User</a>

    </form>

    <?php if (count($users) > 0): ?>
      <table class="user-table">
        <thead>
          <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= htmlspecialchars($user['id']) ?></td>
              <td><?= htmlspecialchars($user['name']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= htmlspecialchars($user['role']) ?></td>
              <td class="action-btns">
                <a href="edit_user.php?id=<?= urlencode($user['id']) ?>&role=<?= strtolower($user['role']) ?>"><button class="edit-btn">Edit</button></a>
                <a href="delete_user.php?id=<?= urlencode($user['id']) ?>&role=<?= strtolower($user['role']) ?>" onclick="return confirm('Are you sure you want to deactivate this user?');"><button class="delete-btn">Deactivate</button></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="no-users">No users found for the selected role.</p>
    <?php endif; ?>
  </div>
</main>

<footer>
  <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
</footer>

</body>
</html>
