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
    $batchSemester = $_POST["batch_semester"];

    // Fetch student details for the selected Batch + Semester
    $sql = "SELECT s.`Name` AS student_name, s.`RollNumber` AS roll_number, sd.`Seva Name` AS seva_name
            FROM students s
            LEFT JOIN seva_assignments sa ON s.`SID` = sa.`Student ID`
            LEFT JOIN seva_details sd ON sa.`Seva Id` = sd.`Seva Id`
            WHERE CONCAT(s.`batch`, ' ', s.`semester`) = '$batchSemester'";

    $result = mysqli_query($con, $sql);

    if ($result) {
        $studentDetails = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $student_name = $row['student_name'];
            $roll_number = $row['roll_number'];
            $seva_name = $row['seva_name'];

            $studentDetails[] = array(
                'student_name' => $student_name,
                'roll_number' => $roll_number,
                'seva_name' => $seva_name
            );
        }

        // Encode the student details as JSON and return it
        header('Content-Type: application/json');
        echo json_encode($studentDetails);
    } else {
        // Handle the query error here
        echo "Error: " . mysqli_error($con);
    }
}
?>
