<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'newuniversity';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Assuming you have a POST method to update seva_assignments
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Update seva_assignments table (you'll need to fill this out based on your form data)
    $update_seva_sql = "...";  // your SQL query to update seva_assignments
    mysqli_query($con, $update_seva_sql);

    // After updating seva_assignments, insert/update the record in attendance_students
    if (isset($_POST['Seva Id'], $_POST['Student ID'], $_POST['StartTime'], $_POST['EndTime'])) {
        $seva_id = $_POST['Seva Id'];
        $student_id = $_POST['Student ID'];
        $startTime = $_POST['StartTime'];
        $endTime = $_POST['EndTime'];

        // Check if record exists for this seva_id and student_id
        $check_sql = "SELECT * FROM attendance_students WHERE seva_id = ? AND student_id = ?";
        $stmt = mysqli_prepare($con, $check_sql);
        mysqli_stmt_bind_param($stmt, "ii", $seva_id, $student_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            // Update existing record
            $update_attendance_sql = "UPDATE attendance_students SET start_time = ?, end_time = ? WHERE seva_id = ? AND student_id = ?";
            $stmt = mysqli_prepare($con, $update_attendance_sql);
            mysqli_stmt_bind_param($stmt, "ssii", $startTime, $endTime, $seva_id, $student_id);
        } else {
            // Insert new record with default attendance as 'no'
            $insert_sql = "INSERT INTO attendance_students (seva_id, student_id, start_time, end_time, attendance) VALUES (?, ?, ?, ?, 'no')";
            $stmt = mysqli_prepare($con, $insert_sql);
            mysqli_stmt_bind_param($stmt, "iiss", $seva_id, $student_id, $startTime, $endTime);
        }
        
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Redirect back or provide a message
    header("Location: your_redirect_page.php");
    exit();
}

?>
