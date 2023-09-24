<?php
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'newuniversity';

// Create a connection to the database
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

// Check if the connection was successful
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["assign_seva_coordinator"])) {
        $sevaId = $_POST["seva_id"];
        $facultyId = $_POST["faculty_id"];
        $faculty_name = "";
        $query = "SELECT `name` FROM login WHERE EID = '$facultyId'";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $faculty_name = $row['name'];
        }

        mysqli_stmt_store_result($stmt);

        // Check if the Seva coordinator is already assigned
        $checkQuery = "SELECT * FROM seva_details WHERE `Seva Id` = $sevaId AND `Seva Coordinator` = $facultyId";
        $result = mysqli_query($con, $checkQuery);

        if (mysqli_num_rows($result) == 0) {
            // Update the Seva coordinator in the seva_details table
            $updateQuery = "UPDATE seva_details SET `Seva Coordinator` = ?, `Faculty ID` = ? WHERE `Seva Id` = ?";
            $stmtUpdate = mysqli_prepare($con, $updateQuery);

            if (!$stmtUpdate) {
                die("Error in preparing the update statement: " . mysqli_error($con));
            }

            mysqli_stmt_bind_param($stmtUpdate, "sii", $faculty_name, $facultyId, $sevaId);

            if (mysqli_stmt_execute($stmtUpdate)) {
                echo "Seva coordinator assigned successfully.";
            } else {
                echo "Error assigning Seva coordinator: " . mysqli_error($con);
            }

            mysqli_stmt_close($stmtUpdate);
            header("Location: main_coordinator.php");
            exit();
        }
    } elseif (isset($_POST["add_new_seva"])) {
        // Handle adding a new Seva code here
        $newSevaName = $_POST["new_seva_name"];

        // Check if the Seva name is not empty
        if (!empty($newSevaName)) {
            // Insert the new Seva into the database
            $insertSevaQuery = "INSERT INTO seva_details (`Seva Name`) VALUES ('$newSevaName')";
            if (mysqli_query($con, $insertSevaQuery)) {
                echo "New Seva added successfully.";
            } else {
                echo "Error adding new Seva: " . mysqli_error($con);
            }
        } else {
            echo "Seva name cannot be empty.";
        }
        header("Location: main_coordinator.php");
        exit();
    } elseif (isset($_POST["add_student"])) {

        $rollNumber = $_POST["roll_number"];
        $studentName = $_POST["student_name"];
        $batch = $_POST["batch"];
        $email = $_POST["email"];
        $contact = $_POST["contact"];
        $semester = $_POST["semester"];

        // Check if the required fields are not empty
        if (!empty($rollNumber) && !empty($studentName) && !empty($batch) && !empty($email) && !empty($contact) && !empty($semester)) {
            // Insert the new student into the database
            $insertStudentQuery = "INSERT INTO students (`Name`, `RollNumber`, `Email`, `Contact`, `Semester`, `Batch`, `user_type`) VALUES ('$studentName', '$rollNumber', '$email', '$contact', $semester, '$batch', 'student')";

            if (mysqli_query($con, $insertStudentQuery)) {
                echo "New student added successfully.";
            } else {
                echo "Error adding new student: " . mysqli_error($con);
            }
        } else {
            echo "All fields are required.";
        }

        header("Location: main_coordinator.php"); // Redirect to the main coordinator page
        exit();
    } elseif (isset($_POST["add_faculty"])) {

        $facultyName = $_POST["faculty_name"];
        $department = $_POST["department"];
        $email = $_POST["email"];
        $contact = $_POST["contact"];

        // Check if the required fields are not empty
        if (!empty($facultyName) && !empty($department) && !empty($email) && !empty($contact)) {
            // Insert the new faculty member into the `login` table
            $insertFacultyQuery = "INSERT INTO login (`name`, `department`, `email`, `contact`, `user_type`) VALUES ('$facultyName', '$department', '$email', '$contact', 'faculty')";

            if (mysqli_query($con, $insertFacultyQuery)) {
                echo "New faculty member added successfully.";
            } else {
                echo "Error adding new faculty member: " . mysqli_error($con);
            }
        } else {
            echo "All fields are required.";
        }

        header("Location: main_coordinator.php"); // Redirect to the main coordinator page
        exit();
    } else {
        // Retrieve form data
        $seva_id = $_POST["seva_id"];
        $start_time = $_POST["start_time"];
        $end_time = $_POST["end_time"];
        $sql = "SELECT `EID` FROM seva_details WHERE `Seva Id` = ?";
        $stmtSelect = mysqli_prepare($con, $sql);

        if (!$stmtSelect) {
            die("Error in preparing the select statement: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmtSelect, "i", $seva_id);
        if (mysqli_stmt_execute($stmtSelect)) {
            mysqli_stmt_bind_result($stmtSelect, $faculty_id);
            mysqli_stmt_fetch($stmtSelect);

            mysqli_stmt_close($stmtSelect);

            $students = $_POST["students"];

            // Prepare an array to hold the student IDs
            $student_ids = [];

            // Extract student IDs and populate the $student_ids array
            foreach ($students as $student) {
                $parts = explode('|', $student);
                $sid = $parts[1];
                $student_ids[] = $sid;
            }

            // Insert data into seva_assignments table for all students in one query
            $insertStudentQuery = "INSERT INTO seva_assignments (`Student ID`, `Seva Id`, `Faculty ID`, `StartTime`, `EndTime`) VALUES (?, ?, ?, ?, ?)";
            $stmtStudent = mysqli_prepare($con, $insertStudentQuery);

            if (!$stmtStudent) {
                die("Error in preparing the student insert statement: " . mysqli_error($con));
            }

            // Bind parameters for the student assignment query
            mysqli_stmt_bind_param($stmtStudent, "iiiss", $student_id, $seva_id, $faculty_id, $start_time, $end_time);

            // Insert all student assignments in one query
            foreach ($student_ids as $student_id) {
                if (!mysqli_stmt_execute($stmtStudent)) {
                    die("Error in executing the student insert statement: " . mysqli_error($con));
                }
            }

            mysqli_stmt_close($stmtStudent);

            echo "Seva assignments have been successfully added.";
        } else {
            echo "Error in adding seva assignments: " . mysqli_error($con);
        }

        // Redirect back to the main coordinator page or any other appropriate location
        header("Location: main_coordinator.php");
        exit();
    }
} else {
    // If the form was not submitted via POST, redirect to the main coordinator page
    header("Location: main_coordinator.php");
    exit();
}

// Close the database connection at the end of the script
mysqli_close($con);
?>