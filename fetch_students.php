<?php
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'newuniversity';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

$seva_id = $_GET['seva_id'];

// PHP code to fetch students who are part of the selected seva
$sql = "SELECT students.SID, students.Name, students.RollNumber, students.Semester, students.Batch
        FROM students
        INNER JOIN seva_assignments ON students.SID = seva_assignments.`Student ID`
        WHERE seva_assignments.`Seva Id` = ?";
$stmt = mysqli_prepare($con, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $seva_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $students = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }

    mysqli_stmt_close($stmt);

    // Check if students array is empty
    if (empty($students)) {
        $response = ["message" => "No students found for this Seva."];
    } else {
        $response = $students; // Assuming $students is an array of student data
    }

    // Return students as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Handle error
    $error_message = mysqli_error($con);
    $response = ["error" => "An error occurred while fetching data: $error_message"];
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
