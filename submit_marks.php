<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // ✅ Start session to get lecturer identity

header('Content-Type: text/html; charset=utf-8');
include 'db_connect.php';

// Get logged-in lecturer
$lecturer = $_SESSION['lecturer_name'] ?? 'unknown';
 // fallback if not logged in

// Convert mark to grade
function calculateGrade($mark) {
    if ($mark >= 80) return "A";
    elseif ($mark >= 75) return "B+";
    elseif ($mark >= 70) return "B";
    elseif ($mark >= 65) return "C+";
    elseif ($mark >= 60) return "C";
    elseif ($mark >= 55) return "D+";
    elseif ($mark >= 50) return "D";
    else return "F";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST["student_name"] ?? "";
    $student_id = $_POST["student_id"] ?? "";
    $course = $_POST["course"] ?? "";

    $units = $_POST["unit"] ?? [];
    $marks = $_POST["mark"] ?? [];
    $cus = $_POST["cu"] ?? [];

    // Normalize to 6 entries
    $units = array_pad($units, 6, "");
    $marks = array_pad($marks, 6, 0);
    $cus = array_pad($cus, 6, "");
    $grades = [];

    for ($i = 0; $i < 6; $i++) {
        $marks[$i] = is_numeric($marks[$i]) ? (int)$marks[$i] : 0;
        $grades[$i] = calculateGrade($marks[$i]);
        $units[$i] = trim($units[$i]);
    }

    // Validate student ID
    $check = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
    $check->bind_param("s", $student_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        echo "<style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 100px; background:#fff; }
            .error-box { background: #fff; padding: 30px; border-radius: 10px; max-width: 600px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.2); }
            .error-box h2 { color: red; }
            a { text-decoration: none; padding: 10px 20px; background: #007bff; color: white; border-radius: 5px; display: inline-block; margin-top: 20px; }
        </style>";
        echo "<div class='error-box'>
            <h2>❌ Student ID not found!</h2>
            <p>Please check and try again.</p>
            <a href='student_mark_sheet.html'>Go Back</a>
        </div>";
        exit();
    }

    // Loop through course units and either insert or update
    for ($i = 0; $i < 6; $i++) {
        if (!empty($units[$i])) {
            $unit = $units[$i];
            $mark = $marks[$i];
            $grade = $grades[$i];
            $cu = $cus[$i];

            // Check if this course unit already exists
            $check = $conn->prepare("SELECT id FROM student_marks WHERE student_id = ? AND course_unit = ?");
            $check->bind_param("ss", $student_id, $unit);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                // ✅ Update existing mark
                $update = $conn->prepare("UPDATE student_marks 
                    SET marks = ?, grade = ?, cu = ?, student_name = ?, course = ?, lecturer = ?, updated_at = NOW()
                    WHERE student_id = ? AND course_unit = ?");
                $update->bind_param("isisssss", $mark, $grade, $cu, $student_name, $course, $lecturer, $student_id, $unit);
                $update->execute();
                $update->close();
            } else {
                // ✅ Insert new mark
                $insert = $conn->prepare("INSERT INTO student_marks 
                    (student_name, student_id, course, course_unit, marks, grade, cu, lecturer) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $insert->bind_param("ssssisis", $student_name, $student_id, $course, $unit, $mark, $grade, $cu, $lecturer);
                $insert->execute();
                $insert->close();
            }
        }
    }

    echo "<style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 100px; background: #f0fff0; }
        .success-box { background: #fff; padding: 30px; border-radius: 10px; max-width: 600px; margin: auto; box-shadow: 0 0 10px rgba(0,255,0,0.2); }
        .success-box h2 { color: green; }
        a { text-decoration: none; padding: 10px 20px; background: #007bff; color: white; border-radius: 5px; display: inline-block; margin: 10px; }
    </style>";
    echo "<div class='success-box'>
        <h2>✅ Marks saved successfully!</h2>
        <a href='student_mark_sheet.html'>Add More Marks</a>
        <a href='lecturer_dashboard.html'>Go to Dashboard</a>
    </div>";

    $conn->close();
}
?>
