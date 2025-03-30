<?php
session_start();
include 'db_connect.php';

$id = $_GET['id'] ?? '';
$role = $_GET['role'] ?? '';
$success = false;
$error = '';

if (!$id || !$role) {
  die("Invalid access.");
}

// Fetch user
if ($role === 'student') {
  $stmt = $conn->prepare("SELECT student_id AS id, name, email FROM students WHERE student_id = ?");
} else {
  $stmt = $conn->prepare("SELECT id, name, email, role FROM admin_users WHERE id = ?");
}
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
  die("User not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'] ?? '';
  $email = $_POST['email'] ?? '';
  $new_role = $_POST['user_role'] ?? $role;

  if ($role === 'student') {
    $update = $conn->prepare("UPDATE students SET name = ?, email = ? WHERE student_id = ?");
    $update->bind_param("sss", $name, $email, $id);
  } else {
    $update = $conn->prepare("UPDATE admin_users SET name = ?, email = ?, role = ? WHERE id = ?");
    $update->bind_param("ssss", $name, $email, $new_role, $id);
  }

  if ($update->execute()) {
    $success = true;
    // Refresh data
    $user['name'] = $name;
    $user['email'] = $email;
    if ($role !== 'student') {
      $user['role'] = ucfirst($new_role);
    }
  } else {
    $error = "Failed to update user.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .edit-form-container {
      max-width: 600px;
      margin: 40px auto;
      background: #f9f9f9;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .edit-form-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .edit-form label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }

    .edit-form input, .edit-form select {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      margin-top: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .edit-form button {
      margin-top: 20px;
      padding: 12px;
      width: 100%;
      font-size: 16px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .edit-form button:hover {
      background-color: #0056b3;
    }

    .status-message {
      text-align: center;
      margin-top: 15px;
      font-weight: bold;
    }

    .status-message.success { color: green; }
    .status-message.error { color: red; }
  </style>
</head>
<body>

<header id="header">
  <nav>
    <div class="nav-container">
      <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
      <span>EDIT USER</span>
      <div class="nav-buttons">
        <a href="manage_users.php"><button>← Back</button></a>
      </div>
    </div>
  </nav>
</header>

<main>
  <div class="edit-form-container">
    <h2>Edit <?= ucfirst($role) ?></h2>

    <?php if ($success): ?>
      <p class="status-message success">✅ User updated successfully!</p>
    <?php elseif ($error): ?>
      <p class="status-message error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" class="edit-form">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required value="<?= htmlspecialchars($user['name']) ?>">

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">

      <?php if ($role !== 'student'): ?>
        <label for="user_role">Role:</label>
        <select name="user_role" id="user_role" required>
          <option value="lecturer" <?= $user['role'] === 'Lecturer' ? 'selected' : '' ?>>Lecturer</option>
          <option value="administrator" <?= $user['role'] === 'Administrator' ? 'selected' : '' ?>>Administrator</option>
        </select>
      <?php endif; ?>

      <button type="submit">Update User</button>
    </form>
  </div>
</main>

<footer>
  <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
</footer>

</body>
</html>
