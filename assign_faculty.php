<?php
// Establish a database connection (replace with your database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "newuniversity";

$con = mysqli_connect($servername, $username, $password, $dbname);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST["assign_faculty"])) {
    $sevaId = $_POST["seva_id"];
    $facultyId = $_POST["faculty_id"];
    
    // Check if the assignment already exists for this Seva and Faculty
    $checkQuery = "SELECT * FROM faculty_assignment WHERE SevaId = ? AND FacultyId = ?";
    $stmtCheckAssignment = mysqli_prepare($con, $checkQuery);

    if (!$stmtCheckAssignment) {
        die("Error in preparing the check statement: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmtCheckAssignment, "ii", $sevaId, $facultyId);

    mysqli_stmt_execute($stmtCheckAssignment);
    mysqli_stmt_store_result($stmtCheckAssignment);

    if (mysqli_stmt_num_rows($stmtCheckAssignment) == 0) {
        // Assignment does not exist, fetch StartTime and EndTime from seva_details
        $fetchQuery = "SELECT StartTime, EndTime FROM seva_details WHERE `Seva Id` = ?";
        $stmtFetchSevaDetails = mysqli_prepare($con, $fetchQuery);

        if (!$stmtFetchSevaDetails) {
            die("Error in preparing the fetch statement: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmtFetchSevaDetails, "i", $sevaId);

        mysqli_stmt_execute($stmtFetchSevaDetails);
        mysqli_stmt_bind_result($stmtFetchSevaDetails, $startTime, $endTime);
        mysqli_stmt_fetch($stmtFetchSevaDetails);
        mysqli_stmt_close($stmtFetchSevaDetails);

        // Insert assignment into the faculty_assignment table with fetched StartTime and EndTime
        $insertQuery = "INSERT INTO faculty_assignment (SevaId, FacultyId, StartTime, EndTime) VALUES (?, ?, ?, ?)";
        $stmtInsertAssignment = mysqli_prepare($con, $insertQuery);

        if (!$stmtInsertAssignment) {
            die("Error in preparing the insert statement: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($stmtInsertAssignment, "iiss", $sevaId, $facultyId, $startTime, $endTime);

        if (mysqli_stmt_execute($stmtInsertAssignment)) {
            echo "Faculty assigned to Seva successfully.";
        } else {
            echo "Error assigning faculty to Seva: " . mysqli_error($con);
        }

        mysqli_stmt_close($stmtInsertAssignment);
    } else {
        echo "Faculty is already assigned to this Seva.";
    }
    mysqli_stmt_close($stmtCheckAssignment);

    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'login.php';
    header('Location: ' . $redirect_url);
    exit();
}

// Fetch faculties from your database to populate the dropdown/select list
$facultyQuery = "SELECT EID, name FROM login WHERE user_type = 'faculty'";
$facultyResult = mysqli_query($con, $facultyQuery);
?>

<?php
// Close the database connection
mysqli_close($con);
?>
