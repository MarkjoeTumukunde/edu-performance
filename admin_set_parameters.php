<?php
include 'db_connect.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $params = [
    'academic_year' => $_POST['academic_year'] ?? '',
    'current_term' => $_POST['current_term'] ?? '',
    'pass_mark' => $_POST['pass_mark'] ?? ''
  ];

  foreach ($params as $key => $value) {
    $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value)
                            VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    $stmt->bind_param("ss", $key, $value);
    $stmt->execute();
  }
  $message = "✅ Settings updated successfully.";
}

// Fetch current values
$settings = [
  'academic_year' => '',
  'current_term' => '',
  'pass_mark' => ''
];

$result = $conn->query("SELECT setting_key, setting_value FROM system_settings");
while ($row = $result->fetch_assoc()) {
  if (isset($settings[$row['setting_key']])) {
    $settings[$row['setting_key']] = $row['setting_value'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Set System Parameters - Admin</title>
  <link rel="stylesheet" href="style.css">
  <style>
    main {
      padding: 40px;
      max-width: 700px;
      margin: auto;
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
    }
    form {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .form-group {
      margin-bottom: 20px;
    }
    label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }
    input[type="text"], input[type="number"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .message {
      text-align: center;
      color: green;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <header id="header">
    <nav>
      <div class="nav-container">
        <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
        <span>ADMIN: SYSTEM PARAMETERS</span>
        <div class="nav-buttons">
          <a href="admin_dashboard.html"><button>← Dashboard</button></a>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <h2>🛠 Set System Parameters</h2>

    <?php if (!empty($message)): ?>
      <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label for="academic_year">Academic Year</label>
        <input type="text" id="academic_year" name="academic_year" value="<?= htmlspecialchars($settings['academic_year']) ?>" required>
      </div>
      <div class="form-group">
        <label for="current_term">Current Term</label>
        <input type="text" id="current_term" name="current_term" value="<?= htmlspecialchars($settings['current_term']) ?>" required>
      </div>
      <div class="form-group">
        <label for="pass_mark">Minimum Pass Mark</label>
        <input type="number" id="pass_mark" name="pass_mark" value="<?= htmlspecialchars($settings['pass_mark']) ?>" required>
      </div>
      <button type="submit">💾 Save Settings</button>
    </form>
  </main>

  <footer>
    <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
  </footer>
</body>
</html>
