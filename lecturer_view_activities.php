<?php
session_start();
include 'db_connect.php';

$selected_activity = $_GET['activity'] ?? '';

// Fetch all unique activities
$activityQuery = "SELECT DISTINCT activity_name FROM student_activity_status ORDER BY activity_name";
$activityResult = $conn->query($activityQuery);

// Fetch students for selected activity
$students = [];
if (!empty($selected_activity)) {
  $stmt = $conn->prepare("
    SELECT s.student_id, s.name AS student_name, sa.activity_name, sa.is_participating
    FROM students s
    LEFT JOIN student_activity_status sa 
      ON s.student_id = sa.student_id AND sa.activity_name = ?
    ORDER BY s.name ASC
  ");

  $stmt->bind_param("s", $selected_activity);
  $stmt->execute();
  $students = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Student Activities</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .activity-container {
      max-width: 800px;
      margin: 40px auto;
      background: #f9f9f9;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .activity-table {
      margin-top: 20px;
      width: 100%;
      border-collapse: collapse;
    }
    .activity-table th, .activity-table td {
      border-bottom: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }
    .activity-table th {
      background: #007bff;
      color: #fff;
    }
    .register-btn {
      padding: 10px 25px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 20px;
    }

    @media (max-width: 768px) {
      .marksheet-container {
        width: 95%;
        padding: 20px;
      }

      .table-cell {
        font-size: 14px;
      }

      .register-btn {
        width: 100%;
      }

      select, label {
        font-size: 14px;
        width: 100%;
        margin: 10px 0;
      }
    }
  </style>
</head>
<body>
  <header id="header">
    <nav>
      <div class="nav-container">
        <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
        <span>LECTURER: ACTIVITY PARTICIPATION</span>
        <div class="nav-buttons">
          <a href="lecturer_dashboard.html"><button>Dashboard</button></a>
        </div>
      </div>
    </nav>
  </header>

  <main class="marksheet-main">
    <section class="marksheet-container">
      <div class="student-info" style="text-align: left; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 22px; color: #007bff;">Lecturer - Activity Participation Panel</h2>
      </div>

      <!-- Activity Filter Form -->
      <form method="GET" action="" style="margin-bottom: 25px;">
        <label for="activity" style="font-size: 16px; margin-right: 10px;"><strong>Select Activity:</strong></label>
        <select name="activity" id="activity" required style="padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc;">
          <option value="">-- Choose Activity --</option>
          <?php while ($row = $activityResult->fetch_assoc()): ?>
            <option value="<?php echo $row['activity_name']; ?>" <?php if ($row['activity_name'] == $selected_activity) echo 'selected'; ?>>
              <?php echo $row['activity_name']; ?>
            </option>
          <?php endwhile; ?>
        </select>
        <button type="submit" style="padding: 10px 16px; margin-left: 10px; border-radius: 5px; background-color: #007bff; color: white; border: none; cursor: pointer; font-size: 14px;">
          Filter
        </button>
      </form>

      <?php if (!empty($selected_activity) && $students->num_rows > 0): ?>
        <!-- Participation Update Form -->
        <form method="POST" action="update_activity_status.php" onsubmit="return validateParticipation();">
          <input type="hidden" name="activity_name" value="<?php echo htmlspecialchars($selected_activity); ?>">

          <div class="marks-table">
            <div class="table-row table-header">
              <div class="table-cell">Student ID</div>
              <div class="table-cell">Student Name</div>
              <div class="table-cell">Activity</div>
              <div class="table-cell">Participation</div>
            </div>

            <?php while ($row = $students->fetch_assoc()): ?>
              <div class="table-row">
                <div class="table-cell"><?php echo htmlspecialchars($row['student_id']); ?></div>
                <div class="table-cell"><?php echo htmlspecialchars($row['student_name']); ?></div>
                <div class="table-cell"><?php echo htmlspecialchars($selected_activity); ?></div>
                <div class="table-cell">
                  <div style="display: flex; gap: 20px; align-items: center;">
                    <?php $participation = $row['is_participating']; ?>
                    <label style="font-weight: bold; color: #28a745;">
                      <input type="radio" name="participating[<?php echo $row['student_id']; ?>]" value="1"
                        <?php echo $participation === "1" || $participation === 1 ? 'checked' : ''; ?>> Yes
                    </label>
                    <label style="font-weight: bold; color: #dc3545;">
                      <input type="radio" name="participating[<?php echo $row['student_id']; ?>]" value="0"
                        <?php echo $participation === "0" || $participation === 0 ? 'checked' : ''; ?>> No
                    </label>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>

          <button type="submit" class="register-btn">Save Participation</button>
        </form>
      <?php elseif (!empty($selected_activity)): ?>
        <p style="font-size: 16px; color: gray;">No students found under this activity.</p>
      <?php endif; ?>

      <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div style="
          background: #d4edda;
          color: #155724;
          padding: 15px;
          border-radius: 8px;
          margin-bottom: 20px;
          font-size: 16px; margin-top: 20px;
        ">
          ✅ Participation updated successfully!
        </div>
      <?php endif; ?>
    </section>
  </main>

  <footer>
    <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
  </footer>

  <script>
    function validateParticipation() {
      const rows = document.querySelectorAll(".table-row:not(.table-header)");
      for (let row of rows) {
        const radios = row.querySelectorAll("input[type='radio']");
        const checked = Array.from(radios).some(r => r.checked);
        if (!checked) {
          alert("⚠️ Please select participation status (Yes/No) for every student before submitting.");
          return false;
        }
      }
      return true;
    }
  </script>
</body>
</html>
