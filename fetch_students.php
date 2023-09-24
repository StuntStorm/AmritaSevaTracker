<?php
$seva_id = $_GET['seva_id'];

// PHP code to fetch students who are part of the selected seva
$sql = "SELECT students.SID, students.Name, students.RollNumber
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

    // Return students as JSON
    header('Content-Type: application/json');
    echo json_encode($students);
} else {
    // Handle error
    echo json_encode(array());
}
?>
