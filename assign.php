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
        $query = "SELECT `name` FROM login WHERE EID = ?";

        // Use prepared statement to avoid SQL injection
        $stmtSelectFacultyName = mysqli_prepare($con, $query);

        if (!$stmtSelectFacultyName) {
            die("Error in preparing the select statement: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmtSelectFacultyName, "i", $facultyId);

        if (mysqli_stmt_execute($stmtSelectFacultyName)) {
            $result = mysqli_stmt_get_result($stmtSelectFacultyName);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $faculty_name = $row['name'];
            }
        }

        // Check if the Seva coordinator is already assigned
        $checkQuery = "SELECT * FROM seva_details WHERE `Seva Id` = ? AND `Seva Coordinator` = ?";

        // Use prepared statement to avoid SQL injection
        $stmtCheckSevaCoordinator = mysqli_prepare($con, $checkQuery);

        if (!$stmtCheckSevaCoordinator) {
            die("Error in preparing the check query: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmtCheckSevaCoordinator, "ii", $sevaId, $facultyId);

        mysqli_stmt_execute($stmtCheckSevaCoordinator);
        $resultCheck = mysqli_stmt_get_result($stmtCheckSevaCoordinator);

        if (mysqli_num_rows($resultCheck) == 0) {
            // Update the Seva coordinator in the seva_details table
            $updateQuery = "UPDATE seva_details SET `Seva Coordinator` = ?, `EID` = ? WHERE `Seva Id` = ?";

            // Use prepared statement to avoid SQL injection
            $stmtUpdateSevaCoordinator = mysqli_prepare($con, $updateQuery);

            if (!$stmtUpdateSevaCoordinator) {
                die("Error in preparing the update statement: " . mysqli_error($con));
            }

            mysqli_stmt_bind_param($stmtUpdateSevaCoordinator, "sii", $faculty_name, $facultyId, $sevaId);

            if (mysqli_stmt_execute($stmtUpdateSevaCoordinator)) {
                // Now, update the user_role to "seva_coordinator"
                $updateUserRoleQuery = "UPDATE login SET user_type = 'seva_coordinator' WHERE EID = ?";

                // Use prepared statement to avoid SQL injection
                $stmtUpdateUserRole = mysqli_prepare($con, $updateUserRoleQuery);

                if (!$stmtUpdateUserRole) {
                    die("Error in preparing the user role update statement: " . mysqli_error($con));
                }

                mysqli_stmt_bind_param($stmtUpdateUserRole, "i", $facultyId);

                if (mysqli_stmt_execute($stmtUpdateUserRole)) {
                    echo "Seva coordinator assigned successfully.";
                } else {
                    echo "Error updating user role: " . mysqli_error($con);
                }

                mysqli_stmt_close($stmtUpdateUserRole);
            } else {
                echo "Error assigning Seva coordinator: " . mysqli_error($con);
            }

            mysqli_stmt_close($stmtUpdateSevaCoordinator);
            header("Location: main_coordinator.php");
            exit();
        }
    } elseif (isset($_POST["add_new_seva"])) {
        // Handle adding a new Seva code here
        $newSevaName = $_POST["new_seva_name"];
        $newStartTime = $_POST["start_time"]; // Retrieve the selected start time
        $newEndTime = $_POST["end_time"]; // Retrieve the selected end time

        // Check if the Seva name is not empty
        if (!empty($newSevaName) && !empty($newStartTime) && !empty($newEndTime)) {
            // Insert the new Seva into the database
            $insertSevaQuery = "INSERT INTO seva_details (`Seva Name`, `StartTime`, `EndTime`) VALUES ('$newSevaName', '$newStartTime', '$newEndTime')";
            if (mysqli_query($con, $insertSevaQuery)) {
                echo "New Seva added successfully.";
            } else {
                echo "Error adding new Seva: " . mysqli_error($con);
            }
        } else {
            echo "Seva name, start time, and end time cannot be empty.";
        }
        header("Location: main_coordinator.php");
        exit();
    } elseif (isset($_POST["add_student"])) {

        $rollNumber = $_POST["roll_number"];
        $studentName = $_POST["student_name"];
        $batch = $_POST["batch"];
        $contact = $_POST["contact"];
        $semester = $_POST["semester"];

        // Check if the required fields are not empty
        if (!empty($rollNumber) && !empty($studentName) && !empty($batch) && !empty($contact) && !empty($semester)) {
            // Insert the new student into the database
            $insertStudentQuery = "INSERT INTO students (`Name`, `RollNumber`, `Contact`, `Semester`, `Batch`, `user_type`) VALUES ('$studentName', '$rollNumber', '$contact', $semester, '$batch', 'student')";

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
        $contact = $_POST["contact"];

        // Check if the required fields are not empty
        if (!empty($facultyName) && !empty($department) && !empty($contact)) {
            // Insert the new faculty member into the `login` table
            $insertFacultyQuery = "INSERT INTO login (`name`, `department`, `contact`, `user_type`) VALUES ('$facultyName', '$department', '$contact', 'faculty')";

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
    } elseif (isset($_POST["assign_faculty"])) {
        // Retrieve form data
        $seva_id = $_POST["seva_id"];
        $faculty_ids = $_POST["faculty"];

        // Prepare an array to hold the faculty IDs
        $unique_faculty_ids = array_unique($faculty_ids);

        // Insert data into seva_assignments table for all selected faculty members in one query
        $insertFacultyQuery = "INSERT INTO seva_assignments (`Seva Id`, `Faculty ID`) VALUES (?, ?)";
        $stmtFaculty = mysqli_prepare($con, $insertFacultyQuery);

        if (!$stmtFaculty) {
            die("Error in preparing the faculty insert statement: " . mysqli_error($con));
        }

        // Bind parameters for the faculty assignment query
        mysqli_stmt_bind_param($stmtFaculty, "ii", $seva_id, $faculty_id);

        // Insert all selected faculty assignments in one query
        foreach ($unique_faculty_ids as $faculty_id) {
            if (!mysqli_stmt_execute($stmtFaculty)) {
                die("Error in executing the faculty insert statement: " . mysqli_error($con));
            }
        }

        mysqli_stmt_close($stmtFaculty);

        echo "Faculty assignments have been successfully added.";

        // Redirect back to the main coordinator page or any other appropriate location
        header("Location: main_coordinator.php");
        exit();
    } else {
        // Retrieve form data
        $seva_id = $_POST["seva_id"];
        $student_ids = $_POST["students"];

        // Prepare an array to hold the student IDs
        $unique_student_ids = [];

        // Extract unique student IDs and populate the $unique_student_ids array
        foreach ($student_ids as $student) {
            $parts = explode('|', $student);
            $sid = $parts[1];
            if (!in_array($sid, $unique_student_ids)) {
                $unique_student_ids[] = $sid;
            }
        }

        // Check if each student is already assigned to the Seva
        $assignment_exists = false;
        foreach ($unique_student_ids as $student_id) {
            $checkQuery = "SELECT 1 FROM seva_assignments WHERE `Student ID` = ? AND `Seva Id` = ?";
            $stmtCheckAssignment = mysqli_prepare($con, $checkQuery);

            if (!$stmtCheckAssignment) {
                die("Error in preparing the check statement: " . mysqli_error($con));
            }

            mysqli_stmt_bind_param($stmtCheckAssignment, "ii", $student_id, $seva_id);

            mysqli_stmt_execute($stmtCheckAssignment);
            mysqli_stmt_store_result($stmtCheckAssignment);

            if (mysqli_stmt_num_rows($stmtCheckAssignment) > 0) {
                $assignment_exists = true;
                mysqli_stmt_close($stmtCheckAssignment);
                break; // Exit the loop if any assignment exists
            }

            mysqli_stmt_close($stmtCheckAssignment);
        }

        if (!$assignment_exists) {
            // Insert data into seva_assignments table for all students in one query
            $insertStudentQuery = "INSERT INTO seva_assignments (`Student ID`, `Seva Id`, `Faculty ID`) VALUES (?, ?, ?)";
            $stmtStudent = mysqli_prepare($con, $insertStudentQuery);

            if (!$stmtStudent) {
                die("Error in preparing the student insert statement: " . mysqli_error($con));
            }

            // Get the faculty ID from seva_details (You may need to modify this part)
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

                // Bind parameters for the student assignment query
                foreach ($unique_student_ids as $student_id) {
                    mysqli_stmt_bind_param($stmtStudent, "iii", $student_id, $seva_id, $faculty_id);

                    if (!mysqli_stmt_execute($stmtStudent)) {
                        die("Error in executing the student insert statement: " . mysqli_error($con));
                    }
                }

                mysqli_stmt_close($stmtStudent);

                echo "Seva assignments have been successfully added.";
            } else {
                echo "Error in adding seva assignments: " . mysqli_error($con);
            }
        } else {
            echo "Error: One or more students are already assigned to this Seva.";
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
