<?php
include 'db_connect.php';

session_start();
$student_id = $_SESSION['student_id'] ?? '';
$student_name = $_SESSION[''] ??'';

if (empty($student_id)) {
    echo "<p style='text-align:center; color:red;'>❌ Student ID is required.</p>";
    exit();
}

$sql = "SELECT * FROM student_marks WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

function gradeToPoint($grade) {
    switch ($grade) {
        case "A": return 5.0;
        case "B+": return 4.5;
        case "B": return 4.0;
        case "C+": return 3.5;
        case "C": return 3.0;
        case "D+": return 2.5;
        case "D": return 2.0;
        default: return 0.0;
    }
}

if ($result->num_rows > 0) {
    // Fetch all rows into an array
    $rows = [];
    $student_name = '';
    
    while ($row = $result->fetch_assoc()) {
        if (empty($student_name)) {
            $student_name = htmlspecialchars($row['student_name']);
        }
        $rows[] = $row;
    }

    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Grades for $student_name</title>
        <link rel='stylesheet' href='style.css'>
    </head>
    <body>
        <header id='header'>
            <nav>
                <div class='nav-container'>
                    <a href='index.html'><img src='Images/home2.png' alt='Logo' id='logo'></a>
                    <span>STUDENT GRADES</span>
                    <div class='nav-buttons'>
                        <a href='student_dashboard.html'><button>BACK</button></a>
                    </div>
                </div>
            </nav>
        </header>

        <main class='marksheet-main'>
            <section class='marksheet-container'>
                <div class='student-info' style='text-align: left; margin-bottom: 20px;
                display: flex;'>
                    <p style='font-size: 20px; margin: 5px 0;'><strong>Name:</strong> <span style='color:#007bff;'>$student_name</span></p>
                    <p style='font-size: 18px; margin-top: 0; margin: 8px 0 0 200px; '><strong>Student ID:</strong><span style='color:#007bff;'> $student_id</span></p>
                </div>

                <div class='marks-table'>
                    <div class='table-row table-header'>
                        <div class='table-cell'>Course Unit</div>
                        <div class='table-cell'>Marks</div>
                        <div class='table-cell'>Grade</div>
                    </div>";

    $totalPoints = 0;
    $totalCU = 0;
    $retakes = [];

    foreach ($rows as $row) {
        $unit = htmlspecialchars($row["course_unit"]);
        $mark = (int)$row["marks"];
        $grade = $row["grade"] ?? '';
        $cu = (int)$row["cu"];

        echo "
        <div class='table-row'>
            <div class='table-cell'>$unit</div>
            <div class='table-cell'>$mark</div>
            <div class='table-cell'>$grade</div>
        </div>";

        $totalPoints += gradeToPoint($grade) * $cu;
        $totalCU += $cu;

        if ($mark < 50) {
            $retakes[] = $unit;
        }
    }

    $cgpa = $totalCU > 0 ? round($totalPoints / $totalCU, 2) : 0;
    $retakeList = !empty($retakes) ? implode(', ', $retakes) : 'None';

    echo "
                </div> <!-- End of marks-table -->
                <div class='marks-summary' style='margin-top: 30px; font-size: 18px; display: flex; justify-content: space-around; gap: 40px; flex-wrap: wrap; background: #f9f9f9; padding: 15px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>
                    <div><strong>Total CU:</strong> $totalCU</div>
                    <div><strong>GPA / CGPA:</strong> $cgpa</div>
                    <div><strong>Retake Course(s):</strong> $retakeList</div>
                </div>
            </section>
        </main>

        <footer>
            <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
        </footer>
    </body>
    </html>";

} else {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>No Grades Found</title>
        <link rel='stylesheet' href='style.css'>
        <style>
        .no-data-box {
            max-width: 600px;
            margin: 250px auto;
            padding: 40px;
            background: #fff;
            border-radius: 10px;
            text-align: center;
            font-family: Arial, sans-serif;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .no-data-box h2 {
            color: #dc3545;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .no-data-box p {
            font-size: 16px;
            color: #555;
        }
        .no-data-box a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .no-data-box a:hover {
            background-color: #0056b3;
        }
    </style>
    </head>
    <body>
    <div class='no-data-box'>
        <h2>❌ No Grades Found</h2>
        <p>We couldn't find any results for <strong>$student_id</strong>. You may not have been graded yet.</p>
        <a href='student_dashboard.html'>Back to Dashboard</a>
    </div>
    </body>
    </html>
    ";
}


$stmt->close();
$conn->close();
?>
