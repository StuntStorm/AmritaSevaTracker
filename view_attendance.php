<?php
// Include your database connection code here
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'newuniversity';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seva_id = $_POST["seva_id"];
    $attendance_date = $_POST["attendance_date"];

    // Retrieve Seva details for display
    $seva_query = "SELECT `Seva Name`, StartTime, EndTime FROM seva_details WHERE `Seva Id` = $seva_id";
    $seva_result = mysqli_query($con, $seva_query);
    $seva_row = mysqli_fetch_assoc($seva_result);

    // Fetch the attendance data for the selected Seva, date, and shift
    $attendance_query = "SELECT a.`student_id`, s.`Name`, a.`is_present`
                         FROM attendance_students a
                         JOIN students s ON a.`student_id` = s.`SID`
                         WHERE a.`seva_id` = $seva_id
                         AND a.`attendance_date` = '$attendance_date'";
    $attendance_result = mysqli_query($con, $attendance_query);

    if ($attendance_result) {
        $attendance_data = array();

        while ($attendance_row = mysqli_fetch_assoc($attendance_result)) {
            $student_name = $attendance_row['Name'];
            $is_present = $attendance_row['is_present'] ? 'Present' : 'Absent';

            $attendance_data[] = array(
                'student_name' => $student_name,
                'is_present' => $is_present
            );
        }

        // Encode the attendance data as JSON and return it
        header('Content-Type: application/json');
        echo json_encode($attendance_data);
    } else {
        // Handle the query error here
        echo "Error: " . mysqli_error($con);
    }
}
?>
