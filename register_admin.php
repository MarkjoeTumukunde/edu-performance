<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"] ?? "";
    $email = $_POST["email"] ?? "";
    $role = $_POST["role"] ?? "";
    $gender = $_POST["gender"] ?? "";
    $course_units = $_POST["course_units"] ?? "";
    $raw_password = $_POST["password"] ?? "";

    $domain = substr(strrchr($email, "@"), 1);
    if (!checkdnsrr($domain, "MX")) {
        echo "<p style='color:red; text-align:center;'>❌ Email domain does not exist.</p>";
        echo "<div style='text-align:center;'><a href='admin_register.html' style='text-decoration:none; background:#007bff; padding:10px 20px; color:#fff; border-radius:5px;'>Go Back</a></div>";
        exit();
    }


    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<p style='color:red; text-align:center;'>❌ Email is already registered.</p>";
        echo "<div style='text-align:center;'><a href='admin_register.html' style='text-decoration:none; background:#007bff; padding:10px 20px; color:#fff; border-radius:5px;'>Go Back</a></div>";
    } else {
        $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

        if ($role === "lecturer") {
            // Insert with course_units
            $stmt = $conn->prepare("INSERT INTO admin_users (name, email, gender, role, course_units, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $email, $gender, $role, $course_units, $hashed_password);
        } else {
            // Insert without course_units
            $stmt = $conn->prepare("INSERT INTO admin_users (name, email, gender, role, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $gender, $role, $hashed_password);
        }

        if ($stmt->execute()) {
            echo "<div style='max-width: 500px; margin: 100px auto; text-align:center; font-family: Arial;'>
                    <h2 style='color:green;'>🎉 Registration Successful!</h2>
                    <p>$name has been registered as <strong>$role</strong>.</p>
                    <a href='login.html' style='background:#007bff; color:#fff; padding:10px 20px; border-radius:5px; text-decoration:none;'>Proceed to Login</a>
                  </div>";
        } else {
            echo "<p style='color:red; text-align:center;'>❌ Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    $check->close();
    $conn->close();
}
?>
