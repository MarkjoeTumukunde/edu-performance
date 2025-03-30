<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $student_id = $_SESSION['student_id'] ?? '';
    $type = $_POST['complaint_type'] ?? '';
    $description = $_POST['complaint'] ?? '';

    // Basic validation
    if (empty($student_id) || empty($type) || empty($description)) {
        echo "<p style='color:red; text-align:center;'>❌ All fields are required.</p>";
        echo "<div style='text-align:center;'><a href='submit_complaint.html'>Go Back</a></div>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO student_complaints (student_id, complaint_type, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $student_id, $type, $description);

    if ($stmt->execute()) {
        echo "<style>
            body { font-family: Arial, sans-serif; background: #f0fff0; text-align: center; padding: 80px; }
            .success-box {
              background: #ffffff; padding: 30px; border-radius: 10px;
              box-shadow: 0 0 10px rgba(0,255,0,0.2); display: inline-block;
            }
            .success-box h2 { color: green; }
            a {
              text-decoration: none; padding: 10px 20px; background: #007bff;
              color: white; border-radius: 5px; margin-top: 20px; display: inline-block;
            }
        </style>";
        echo "<div class='success-box'>
            <h2>✅ Complaint submitted successfully!</h2>
            <p>We will review your complaint and get back to you soon.</p>
            <a href='student_dashboard.html'>Return to Dashboard</a>
        </div>";
    } else {
        echo "<p style='color:red; text-align:center;'>❌ Failed to submit complaint. Please try again.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
