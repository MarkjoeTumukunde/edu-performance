<?php
session_start();
include 'db_connect.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $role = $_POST['role'];
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);

  if ($role === 'student') {
    $student_id = trim($_POST['student_id']) ?: 'STU' . rand(10000, 99999);
    $stmt = $conn->prepare("INSERT INTO students (student_id, name, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $student_id, $name, $email);
  } else {
    $stmt = $conn->prepare("INSERT INTO admin_users (name, email, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $role);
  }

  if ($stmt->execute()) {
    $success = true;
  } else {
    $error = "❌ Failed to add user. Email may already exist.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add New User</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .add-container {
      max-width: 600px;
      margin: 50px auto;
      background: #f9f9f9;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .add-container h2 {
      text-align: center;
      margin-bottom: 25px;
    }

    .form-group {
      display: flex;
      align-items: center;
      margin-bottom: 18px;
    }

    .form-group label {
      flex: 0 0 200px;
      font-weight: bold;
      margin-right: 15px;
      text-align: left;
    }

    .form-group input,
    .form-group select {
      flex: 1;
      padding: 10px;
      font-size: 16px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .form-group input, #role {
      margin-left: -60px;
    }

    #studentIDField {
      display: none;
    }

    .add-btn {
      margin-top: 30px;
      padding: 12px;
      width: 20%;
      background-color: #28a745;
      color: white;
      border: none;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

    #role {
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
      flex-grow: 1;
      box-sizing: border-box;
      width: 100%;
      text-align: center;
      appearance: none; /* Remove default OS styling */
      -webkit-appearance: none; /* Safari */
      -moz-appearance: none; /* Firefox */
      color: #333;
    }

    .add-btn:hover {
      background-color: #218838;
    }

    .status {
      margin-top: 15px;
      text-align: center;
      font-weight: bold;
    }

    .status.success { color: green; }
    .status.error { color: red; }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #007bff;
    }
  </style>
</head>
<body>

<header id="header">
  <nav>
    <div class="nav-container">
      <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
      <span>ADMIN: ADD USER</span>
      <div class="nav-buttons">
        <a href="manage_users.php"><button>← Back</button></a>
      </div>
    </div>
  </nav>
</header>

<main>
  <div class="add-container">
    <h2>➕ Add New User</h2>

    <?php if ($success): ?>
      <p class="status success">✅ User added successfully!</p>
    <?php elseif ($error): ?>
      <p class="status error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="role">Select Role:</label>
        <select name="role" id="role" required onchange="toggleStudentID(this.value)">
          <option value="">-- Choose Role --</option>
          <option value="student">Student</option>
          <option value="lecturer">Lecturer</option>
          <option value="administrator">Administrator</option>
        </select>
      </div>

      <div class="form-group" id="studentIDField">
        <label for="student_id">Student ID (optional):</label>
        <input type="text" name="student_id" id="student_id" placeholder="Auto-generated if left empty">
      </div>

      <div class="form-group">
        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" required>
      </div>

      <div class="form-group">
        <label for="email">Email Address:</label>
        <input type="email" name="email" id="email" required>
      </div>

      <button type="submit" class="add-btn">Add User</button>
    </form>
  </div>
</main>

<footer>
  <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
</footer>

<script>
  function toggleStudentID(role) {
    document.getElementById('studentIDField').style.display = (role === 'student') ? 'flex' : 'none';
  }
</script>

</body>
</html>
