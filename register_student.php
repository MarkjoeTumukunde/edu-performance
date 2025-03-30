<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"] ?? "";
    $email = $_POST["email"] ?? "";
    $gender = $_POST["gender"] ?? "";
    $course = $_POST["course"] ?? "";
    $year = $_POST["year"] ?? "";
    $raw_password = $_POST["password"] ?? "";
    $activities = $_POST["role"] ?? [];
    $activities_str = implode(", ", $activities);

    // Generate unique student ID
    $student_id = "STU" . rand(10000, 99999);


    $domain = substr(strrchr($email, "@"), 1);
    if (!checkdnsrr($domain, "MX")) {
        echo "<p style='color:red; text-align:center;'>❌ Email domain does not exist.</p>";
        echo "<div style='text-align:center;'><a href='admin_register.html' style='text-decoration:none; background:#007bff; padding:10px 20px; color:#fff; border-radius:5px;'>Go Back</a></div>";
        exit();
    }


    // Check if email already exists
    $check_email = $conn->prepare("SELECT id FROM students WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        echo "<style>
            body {
                background: linear-gradient(to right, #ffe6e6, #ffffff);
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }
            .error-box {
                max-width: 500px;
                margin: 200px auto;
                padding: 50px;
                background: #fff0f0;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                text-align: center;
            }
            .error-box .icon {
                font-size: 60px;
                color: #dc3545;
                margin-bottom: -20px;
            }
            .error-box h2 {
                color: #dc3545;
                margin-bottom: 10px;
            }
            .error-box p {
                color: #333;
                font-size: 16px;
            }
            .error-box a {
                display: inline-block;
                margin-top: 10px;
                margin-bottom: 30px;
                padding: 10px 25px;
                background-color: #dc3545;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                transition: background 0.3s;
            }
            .error-box a:hover {
                background-color: #b02a37;
            }
        </style>";

        echo "<div class='error-box'>
            <div class='icon'>❗</div>
            <h2>Email Already Registered!</h2>
            <p>The email is already registred. Please use a different email</p>
            <a href='register_student.html'>Back to Student Registration</a>
        </div>";
    } else {
        $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

        // Insert student data including student_id and gender
        $sql = "INSERT INTO students (student_id, name, email, gender, course, year, password, activities) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $student_id, $name, $email, $gender, $course, $year, $hashed_password, $activities_str);


        if ($stmt->execute()) {
            echo "<style>
                body {
                    background: linear-gradient(to right, #e0f7fa, #ffffff);
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }
                .success-box {
                    max-width: 500px;
                    margin: 100px auto;
                    padding: 30px;
                    background: #f8f9fa;
                    border-radius: 10px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                    text-align: center;
                }
                .success-box h2 {
                    color: #28a745;
                    margin-bottom: 10px;
                }
                .success-box p {
                    font-size: 16px;
                    color: #333;
                }
                .success-box a {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 25px;
                    background-color: #007bff;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                    transition: background 0.3s;
                }
                .success-box a:hover {
                    background-color: #0056b3;
                }
            </style>";

            echo "<div class='success-box'>
                <h2>🎉 Student Registered Successfully!</h2>
                <p><strong>Your Student ID is:</strong> <span style='color: #007bff;'>$student_id</span></p>
                <p>Please copy and save this ID. It will be used for login and accessing your academic records.</p>
                <a href='login.html'>Proceed to Login</a>
            </div>";
        } else {
            echo "<p style='color:red; font-family: Arial; text-align: center; margin-top: 100px;'>❌ Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    $check_email->close();
    $conn->close();
} else {
    echo "<p style='font-family: Arial; text-align: center;'>Form not submitted properly.</p>";
}
?>
