<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";

    // ✅ Check in students table
    $sql_student = "SELECT student_id, name, password FROM students WHERE email = ?";
    $stmt_student = $conn->prepare($sql_student);
    $stmt_student->bind_param("s", $email);
    $stmt_student->execute();
    $stmt_student->store_result();

    if ($stmt_student->num_rows === 1) {
        $stmt_student->bind_result($student_id, $student_name, $hashed_password);
        $stmt_student->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['student_id'] = $student_id;

            // ✅ Output JavaScript to store name in browser and redirect
            echo "<script>
                sessionStorage.setItem('user_name', '" . addslashes($student_name) . "');
                sessionStorage.setItem('user_role', 'student');
                window.location.href = 'student_dashboard.html';
            </script>";
            exit();
        } else {
            echo "<p style='color:red; text-align:center;'>❌ Incorrect password.</p>";
            exit();
        }
    }
    $stmt_student->close();

    // ✅ Check in admin/lecturer table
    $sql_admin = "SELECT name, password, role, course_units FROM admin_users WHERE email = ?";
    $stmt_admin = $conn->prepare($sql_admin);
    $stmt_admin->bind_param("s", $email);
    $stmt_admin->execute();
    $stmt_admin->store_result();

    if ($stmt_admin->num_rows === 1) {
        $stmt_admin->bind_result($admin_name, $hashed_password, $role, $course_units);
        $stmt_admin->fetch();
        $_SESSION['lecturer_name'] = $admin_name;


        if (password_verify($password, $hashed_password)) {
            $_SESSION['admin_role'] = $role;
            $_SESSION['username'] = $email; // ✅ This is what gets saved in student_marks
        
            if ($role === "lecturer" && !empty($course_units)) {
                $_SESSION['course_units'] = array_map('trim', explode(',', $course_units));
            }
        
            // ✅ Output JavaScript to store name and role, and redirect
            $redirect = ($role === "lecturer") ? "lecturer_dashboard.html" : "admin_dashboard.html";
            echo "<script>
                sessionStorage.setItem('user_name', '" . addslashes($admin_name) . "');
                sessionStorage.setItem('user_role', '" . $role . "');
                window.location.href = '$redirect';
            </script>";
            exit();
        } else {
            echo "<p style='color:red; text-align:center;'>❌ Incorrect password.</p>";
        }
    } else {
        echo "<p style='color:red; text-align:center;'>❌ Account not found.</p>";
    }

    $stmt_admin->close();
    $conn->close();
}
ob_end_flush();
?>
