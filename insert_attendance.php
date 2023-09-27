<?php

session_start();

// Check if the user is not authenticated (not logged in)
if (!isset($_SESSION['id'])) {
    // Redirect to the login page or display an error message
    header("Location: login.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'newuniversity';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit_attendance"])) {
        $seva_id = $_POST["seva_id"];
        $seva_name = $_POST["seva_name"];
        $attendance_date = $_POST["attendance_date"];

        // Fetch the list of students assigned to the selected Seva
        $student_query = "SELECT s.`SID`, s.`Name`, s.`RollNumber`
                          FROM students s
                          JOIN seva_assignments ss ON s.`SID` = ss.`Student ID`
                          WHERE ss.`Seva Id` = ?";

        $student_stmt = mysqli_stmt_init($con);
        if (!mysqli_stmt_prepare($student_stmt, $student_query)) {
            // Handle the SQL error here
            echo "Student Query Error: " . mysqli_error($con);
        } else {
            // Bind the Seva ID parameter
            mysqli_stmt_bind_param($student_stmt, "i", $seva_id);
            mysqli_stmt_execute($student_stmt);

            $student_result = mysqli_stmt_get_result($student_stmt);

            while ($row = mysqli_fetch_assoc($student_result)) {
                $student_id = $row['SID'];
                $is_present = isset($_POST["student_$student_id"]) ? 1 : 0;

                // Check if attendance record for the same date and student exists
                $existing_query = "SELECT attendance_id FROM attendance_students WHERE seva_id = ? AND attendance_date = ? AND student_id = ?";
                $existing_stmt = mysqli_stmt_init($con);
                mysqli_stmt_prepare($existing_stmt, $existing_query);
                mysqli_stmt_bind_param($existing_stmt, "iss", $seva_id, $attendance_date, $student_id);
                mysqli_stmt_execute($existing_stmt);
                $existing_result = mysqli_stmt_get_result($existing_stmt);

                if (mysqli_num_rows($existing_result) > 0) {
                    // Update existing record
                    $update_query = "UPDATE attendance_students SET is_present = ? WHERE seva_id = ? AND attendance_date = ? AND student_id = ?";
                    $update_stmt = mysqli_stmt_init($con);
                    mysqli_stmt_prepare($update_stmt, $update_query);
                    mysqli_stmt_bind_param($update_stmt, "iiss", $is_present, $seva_id, $attendance_date, $student_id);
                    mysqli_stmt_execute($update_stmt);
                } else {
                    // Insert new record
                    $insert_query = "INSERT INTO attendance_students (seva_id, student_id, start_time, end_time, is_present, attendance_date)
                                     VALUES (?, ?, ?, ?, ?, ?)";

                    $stmt = mysqli_stmt_init($con);
                    if (!mysqli_stmt_prepare($stmt, $insert_query)) {
                        // Handle the SQL error here
                        echo "SQL Error: " . mysqli_error($con);
                    } else {
                        // Bind parameters and execute the statement
                        mysqli_stmt_bind_param($stmt, "iissis", $seva_id, $student_id, $seva_row['StartTime'], $seva_row['EndTime'], $is_present, $attendance_date);
                        mysqli_stmt_execute($stmt);
                    }
                }
            }

            // Close the statement
            mysqli_stmt_close($student_stmt);

            // Display a success message
            echo "<script>alert('Attendance updated successfully.');</script>";

            // Redirect to the previous page
            if ($_SESSION['user_type'] == 'faculty') {
                // Redirect students to a specific page
                $redirect_url = 'faculty.php'; // Change this to the appropriate page
            } elseif ($_SESSION['user_type'] == 'seva_coordinator') {
                // Redirect admins to a different page
                $redirect_url = 'seva_coordinator.php'; // Change this to the appropriate page
            }
    
            header("Location: $redirect_url");
            exit();
        }
    }
}
