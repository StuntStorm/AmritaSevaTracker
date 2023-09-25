<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'newuniversity';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['seva_id'], $_POST['start_time'], $_POST['end_time'], $_POST['students']) && !empty($_POST['students'])) {
        $seva_id = $_POST['seva_id'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $students = $_POST['students'];

        // Loop through the selected students and mark their attendance
        foreach ($students as $student_id) {
            $student_id = intval($student_id);

            // Insert attendance data into the attendance_students table
            $insert_sql = "INSERT INTO attendance_students (seva_id, student_id, start_time, end_time, attendance) VALUES (?, ?, ?, ?, 'yes')";

            $stmt = mysqli_prepare($con, $insert_sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iiss", $seva_id, $student_id, $startTime, $endTime);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }

        // Redirect back to the attendance page or any other page
        header("Location: seva_coordinator.php");
        exit();
    }
}
?>
