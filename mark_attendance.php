<?php

session_start();

// Check if the user is not authenticated (not logged in)
if (!isset($_SESSION['id'])) {
    // Redirect to the login page or display an error message
    header("Location: login.php");
    exit();
}

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

    // Fetch the list of students assigned to the selected Seva
    $student_query = "SELECT s.`SID`, s.`Name`, s.`RollNumber`, s.`semester`, s.`batch`
                  FROM students s
                  JOIN seva_assignments ss ON s.`SID` = ss.`Student ID`
                  WHERE ss.`Seva Id` = $seva_id";
    $student_result = mysqli_query($con, $student_query);
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="home.css">
    <style>
        .tab {
            display: none;
        }

        .tab-button {
            padding: 10px;
            cursor: pointer;
        }

        .tab-container {
            display: flex;
        }

        .tab-left {
            flex: 1;
            padding: 20px;
        }

        .tab-right {
            flex: 1;
            padding: 20px;
        }

        table {
            border: 1px solid;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <div class="button-container">
        <form action="insert_attendance.php" method="POST">
            <table>
                <tr>
                    <th>Student Name</th>
                    <th>Roll Number</th>
                    <th>Semester</th>
                    <th>Batch</th>
                    <th>Present</th>
                </tr>
                <?php
                while ($student_row = mysqli_fetch_assoc($student_result)) {
                    $student_id = $student_row['SID'];
                    $student_name = $student_row['Name'];
                    $roll_number = $student_row['RollNumber'];
                    $semester = $student_row['semester'];
                    $batch = $student_row['batch'];
                
                    echo '<tr>';
                    echo "<td>$student_name</td>";
                    echo "<td>$roll_number</td>";
                    echo "<td>$semester</td>";
                    echo "<td>$batch</td>";
                    echo "<td><input type='checkbox' name='student_$student_id' value='present'></td>";
                    echo '</tr>';
                }                
                ?>
            </table>
            <br>
            <input type="hidden" name="seva_id" value="<?php echo $seva_id; ?>">
            <input type="hidden" name="seva_name" value="<?php echo $seva_row['Seva Name']; ?>">
            <input type="hidden" name="attendance_date" value="<?php echo $attendance_date; ?>">

            <!-- Include the code here to pass the current page's URL as a query parameter -->
            <?php
            $current_page_url = urlencode($_SERVER['REQUEST_URI']);
            $redirect_url = "insert_attendance.php?previous_url=$current_page_url";
            echo "<input type='hidden' name='previous_url' value='$redirect_url'>";
            ?>

            <input type="submit" name="submit_attendance" value="Submit Attendance">
        </form>
        <br>
        <!-- Replace the existing anchor tag with a JavaScript button -->
        <button class="tab-button" onclick="goBack()">Go back to Seva selection</button>
    </div>
    <script>
        function goBack() {
            var redirect_url = '<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'login.php'; ?>';
            window.location.href = redirect_url;
        }
    </script>

</body>

</html>