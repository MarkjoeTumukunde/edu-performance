<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
include 'db_connect.php';
echo "✅ Connected successfully!";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $role = $_POST['role']; // ← must not be empty
  if (!in_array($role, ['admin', 'lecturer'])) {
    echo "<script>alert('❌ Invalid role selected!'); window.history.back();</script>";
    exit();
  }
  $course_units = $_POST['course_units'] ?? '';
  $check_email = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
  $check_email->bind_param("s", $email);
  $check_email->execute();
  $check_email->store_result();

  if ($check_email->num_rows > 0) {
      echo "<script>alert('❌ This email is already registered. Please use another.'); window.history.back();</script>";
      exit();
  }

  $stmt = $conn->prepare("INSERT INTO admin_users (name, email, password, role, course_units) VALUES (?, ?, ?, ?, ?)");
  $allowed_roles = ['lecturer', 'administrator', 'it'];
  if (!in_array($role, $allowed_roles)) {
    die("❌ Invalid role selected.");
  }
  echo "Role submitted: " . htmlspecialchars($role);


  $stmt->bind_param("sssss", $name, $email, $hashed_password, $role, $course_units);


  if ($stmt->execute()) {
    echo "<script>alert('User added successfully!'); window.location.href='admin_view_users.php';</script>";
  } else {
    echo "<script>alert('Failed to add user.'); window.history.back();</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add User</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: url('Images/body.jpg') no-repeat center center fixed;
      background-size: cover;
    }

    main {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: calc(100vh - 100px);
      padding: 100px 20px 50px;
    }

    .add-user-box {
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 500px;
    }

    .add-user-box h2 {
      text-align: center;
      color: #12254F;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      text-align: left;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border-radius: 5px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }

    button {
      background-color: #007bff;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
    }

    button:hover {
      background-color: #0056b3;
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
        <a href="admin_dashboard.html"><button>← Dashboard</button></a>
      </div>
    </div>
  </nav>
</header>

<main>
  <div class="add-user-box">
    <h2>➕ Add User</h2>
    <form action="" method="post">
      <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
      </div>

      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
      </div>

      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
      </div>

      <div class="form-group">
        <label for="role">Role:</label>
        <select name="role" id="role" required>
          <option value="">Select Role</option>
          <option value="administrator">Administrator</option>
          <option value="lecturer">Lecturer</option>
          <option value="it">IT</option>
        </select>
      </div>

      <div class="form-group">
        <label for="course_units">Course Units (comma-separated):</label>
        <textarea id="course_units" name="course_units" rows="3"></textarea>
      </div>

      <button type="submit">Add User</button>
    </form>
  </div>
</main>

<footer>
  <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
</footer>

</body>
</html>
