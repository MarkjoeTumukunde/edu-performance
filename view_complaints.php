<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';
session_start();

// ✅ Handle individual reply submission (same file)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['complaint_id']) && isset($_POST['remark'])) {
    $complaint_id = $_POST['complaint_id'];
    $remark = trim($_POST['remark']);

    if (!empty($remark)) {
        $stmt = $conn->prepare("UPDATE student_complaints SET remark = ?, status = 'Resolved' WHERE id = ?");
        $stmt->bind_param("si", $remark, $complaint_id);
        $stmt->execute();
        $stmt->close();

        // Reload the same page with success message
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit();
    }
}

// ✅ Fetch all complaints
$sql = "SELECT * FROM student_complaints ORDER BY submitted_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Complaints - Admin Panel</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .complaints-container {
      max-width: 1000px;
      margin: 20px auto;
      padding: 30px;
      background: #f9f9f9;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .complaints-header {
      font-size: 24px;
      margin-bottom: 20px;
      color: #007bff;
    }
    .complaints-table {
      width: 100%;
      border-collapse: collapse;
    }
    .complaints-table th, .complaints-table td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      text-align: left;
      vertical-align: top;
    }
    .complaints-table th {
      background-color: #007bff;
      color: white;
    }
    .no-complaints {
      text-align: center;
      color: gray;
      margin-top: 50px;
      font-size: 18px;
    }
    .success-message {
      background: #d4edda;
      color: #155724;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 16px;
      text-align: center;
    }
    .remark-form textarea {
      width: 100%;
      padding: 8px;
      margin-bottom: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .remark-form button {
      background-color: #007bff;
      color: white;
      padding: 6px 14px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .remark-form button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <header id="header">
    <nav>
      <div class="nav-container">
        <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo"></a>
        <span>COMPLAINTS</span>
        <div class="nav-buttons">
          <a href="admin_dashboard.html"><button>Dashboard</button></a>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <section class="complaints-container">
      <div class="complaints-header">All Submitted Complaints</div>

      <?php if (isset($_GET['success'])): ?>
        <div class="success-message">✅ Complaint remark updated successfully!</div>
      <?php endif; ?>

      <?php if ($result->num_rows > 0): ?>
        <table class="complaints-table">
          <tr>
            <th>Student ID</th>
            <th>Complaint Type</th>
            <th>Description</th>
            <th>Status</th>
            <th>Submitted At</th>
            <th>Action</th>
            <th>Admin Remark</th>
          </tr>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['student_id']); ?></td>
              <td><?php echo htmlspecialchars($row['complaint_type']); ?></td>
              <td><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
              <td>
                <span style="color:<?php echo $row['status'] === 'Resolved' ? 'green' : 'orange'; ?>; font-weight:bold;">
                  <?php echo $row['status']; ?>
                </span>
              </td>

              <td><?php echo htmlspecialchars($row['submitted_at']); ?></td>
              <td>
                <?php if ($row['status'] !== 'Resolved'): ?>
                  <form method="POST" class="remark-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                    <textarea name="remark" rows="2" placeholder="Type your reply..." required></textarea>
                    <button type="submit">Reply</button>
                  </form>
                <?php else: ?>
                  <em style="color:gray;">Already Replied</em>
                <?php endif; ?>
              </td>

              <td><?php echo isset($row['remark']) ? nl2br(htmlspecialchars($row['remark'])) : ''; ?></td>
            </tr>
          <?php endwhile; ?>
        </table>
      <?php else: ?>
        <p class="no-complaints">No complaints have been submitted yet.</p>
      <?php endif; ?>
    </section>
  </main>

  <footer>
    <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
  </footer>
</body>
</html>
<?php $conn->close(); ?>
