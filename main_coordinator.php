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
        <button class="tab-button" onclick="openTab('seva')">View Seva</button>
        <button class="tab-button" onclick="openTab('add_seva')">Add Seva</button>
        <button class="tab-button" onclick="openTab('assign_seva_coordinator')">Assign Seva Coordinator</button>
        <button class="tab-button" onclick="openTab('faculty')">View Faculty</button>
        <button class="tab-button" onclick="openTab('add_faculty')">Add Faculty</button>
        <button class="tab-button" onclick="openTab('student')">View Students</button>
        <button class="tab-button" onclick="openTab('add_student')">Add Students</button>
        <button class="tab-button" onclick="openTab('assign')">Assign Students to Seva</button>
        <button class="tab-button" onclick="openTab('assign_faculty')">Assign Faculty to Seva</button>
        <button class="tab-button" onclick="openTab('view_assigned_seva')">View Student Assigned</button>
        <button class="tab-button" onclick="openTab('view_assigned_seva_faculty')">View Faculty Assigned</button>
        <button class="tab-button" onclick="window.location.href='upload.html'">Upload Students CSV</button>

    </div>
    <div id="seva" class="tab" style="display: block;">
        <br>
        <h3>Seva Details</h3>
        <table>
            <tr>
                <th>Seva Name</th>
                <th>Seva Coordinator</th>
                <th>StartTime</th>
                <th>EndTime</th>
            </tr>
            <?php
            // PHP code to fetch and display seva details
            $sql = "SELECT `Seva Id`, `Seva Name`, `Seva Coordinator`, `StartTime`, `EndTime`
            FROM seva_details";
            $result = mysqli_query($con, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['Seva Name'] . "</td>";
                echo "<td>" . $row['Seva Coordinator'] . "</td>";
                echo "<td>" . $row['StartTime'] . "</td>";
                echo "<td>" . $row['EndTime'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <div id="add_seva" class="tab">
        <br>
        <h3>Add New Seva</h3>
        <form action="assign.php" method="post">
            <label for="new_seva_name">Add New Seva:</label>
            <input type="text" id="new_seva_name" name="new_seva_name">
            <br><br>
            <label for="shift">Shift:</label>
            <div id="shift">
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time">

                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time">
            </div>
            <br>


            <input type="submit" name="add_new_seva" value="Add New Seva">
            <br><br>
        </form>
    </div>

    <div id="assign_seva_coordinator" class="tab">
        <br>
        <h3>Assign Seva Coordinator</h3>
        <form action="assign.php" method="post">
        <label for="seva_select">Select a Seva:</label>
            <select id="seva_select" name="seva_id">
                <?php
                //PHP code to fetch seva names, IDs, and shifts from the database
                $sql = "SELECT `Seva Id`, `Seva Name`, StartTime, EndTime FROM seva_details";
                $result = mysqli_query($con, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    $sevaId = $row['Seva Id'];
                    $sevaName = $row['Seva Name'];
                    $startTime = $row['StartTime'];
                    $endTime = $row['EndTime'];

                    // Display Seva name along with shift in parentheses
                    $sevaLabel = "$sevaName ($startTime - $endTime)";

                    echo "<option value='$sevaId'>$sevaLabel</option>";
                }
                ?>
            </select>
            <br>

            <label for="faculty_select">Select a Faculty Member:</label>
            <select id="faculty_select" name="faculty_id">
                <?php
                // PHP code to fetch faculty names and IDs from the database
                $sql = "SELECT EID, Name FROM login WHERE user_type = 'faculty'";
                $result = mysqli_query($con, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['EID'] . "'>" . $row['Name'] . "</option>";
                }
                ?>
            </select>
            <br>

            <input type="submit" name="assign_seva_coordinator" value="Assign Seva Coordinator">
        </form>
    </div>

    <div id="student" class="tab">
        <br>
        <h3>Student Details</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Roll No</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Semester</th>
                <th>Batch</th>
            </tr>
            <?php
            // // PHP code to fetch and display student details
            $sql = "SELECT * FROM students";
            $result = mysqli_query($con, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['Name'] . "</td>";
                echo "<td>" . $row['RollNumber'] . "</td>";
                echo "<td>" . $row['Email'] . "</td>";
                echo "<td>" . $row['Contact'] . "</td>";
                echo "<td>" . $row['Semester'] . "</td>";
                echo "<td>" . $row['Batch'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <div id="add_student" class="tab">
        <br>
        <h3>Add Student</h3>
        <form action="assign.php" method="post">
            <label for="student_name">Student Name:</label>
            <input type="text" id="student_name" name="student_name">
            <label for="roll_number">Roll Number:</label>
            <input type="text" id="roll_number" name="roll_number">
            <label for="contact">Contact:</label>
            <input type="text" id="contact" name="contact">
            <label for="semester">Semester:</label>
            <input type="text" id="semester" name="semester">
            <label for="batch">Batch:</label>
            <input type="text" id="batch" name="batch">
            <br><br>

            <input type="submit" name="add_student" value="Add Student">
        </form>
    </div>

    <div id="mark_attendance" class="tab">
        <br>
        <h3>Mark Attendance</h3>
        <form action="process_attendance.php" method="POST">
            <label for="attendance_date">Attendance Date:</label>
            <input type="date" id="attendance_date" name="attendance_date" required><br>

            <label for="shift">Shift:</label>
            <select id="shift" name="shift" required>
                <option value="morning">Morning</option>
                <option value="afternoon">Afternoon</option>
            </select><br>

            <input type="submit" value="Submit">
        </form>
    </div>

    <div id="faculty" class="tab">
        <br>
        <h3>Faculty Details</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Dept</th>
                <th>Email</th>
                <th>Contact</th>
            </tr>
            <?php
            $sql = "SELECT * FROM login where user_type = 'faculty'";
            $result = mysqli_query($con, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['department'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['contact'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <div id="add_faculty" class="tab">
        <br>
        <h3>Add Faculty</h3>
        <form action="assign.php" method="post">
            <label for="faculty_name">Faculty Name:</label>
            <input type="text" id="faculty_name" name="faculty_name">
            <label for="department">Department: </label>
            <input type="text" id="department" name="department">
            <label for="contact">Contact:</label>
            <input type="text" id="contact" name="contact">
            <br><br>

            <input type="submit" name="add_faculty" value="Add Faculty">
        </form>
    </div>

    <div id="assign" class="tab">
        <br>
        <h3>Assign Tasks</h3>
        <form action="assign.php" method="post">
            <label for="seva_select">Select a Seva:</label>
            <select id="seva_select" name="seva_id">
                <?php
                //PHP code to fetch seva names, IDs, and shifts from the database
                $sql = "SELECT `Seva Id`, `Seva Name`, StartTime, EndTime FROM seva_details";
                $result = mysqli_query($con, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    $sevaId = $row['Seva Id'];
                    $sevaName = $row['Seva Name'];
                    $startTime = $row['StartTime'];
                    $endTime = $row['EndTime'];

                    // Display Seva name along with shift in parentheses
                    $sevaLabel = "$sevaName ($startTime - $endTime)";

                    echo "<option value='$sevaId'>$sevaLabel</option>";
                }
                ?>
            </select>
            <br>

            <div class="assignment-container">
                <?php
                // Fetch students grouped by semester and batch
                $sql = "SELECT `SID`, Semester, Batch, GROUP_CONCAT(Name, ' (', RollNumber, ')') as Students FROM students GROUP BY `SID`, Semester, Batch";
                $result = mysqli_query($con, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    $sid = $row['SID'];
                    $semester = $row['Semester'];
                    $batch = $row['Batch'];
                    $students = explode(',', $row['Students']);

                    echo "<div class='assignment-section student'>";
                    echo "<h4>Semester $semester, Batch $batch:</h4>";
                    echo "<label><input type='checkbox' class='group-checkbox'> Select All</label><br>";

                    foreach ($students as $student) {
                        $student = trim($student);
                        echo "<input type='checkbox' name='students[]' value='$student|$sid'> $student<br>";
                        echo "<input type='hidden' name='sids[]' value='$sid'>"; // Hidden input for SID
                    }
                    echo "</div>";
                }
                ?>
            </div>
            <br><br>

            <input type="submit" value="Assign Tasks">
        </form>
    </div>

    <div id="assign_faculty" class="tab">
    <h3>Assign Faculty to Seva</h3>
    <form action="assign_faculty.php" method="POST">
    <label for="seva_select">Select a Seva:</label>
            <select id="seva_select" name="seva_id">
                <?php
                //PHP code to fetch seva names, IDs, and shifts from the database
                $sql = "SELECT `Seva Id`, `Seva Name`, StartTime, EndTime FROM seva_details";
                $result = mysqli_query($con, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    $sevaId = $row['Seva Id'];
                    $sevaName = $row['Seva Name'];
                    $startTime = $row['StartTime'];
                    $endTime = $row['EndTime'];

                    // Display Seva name along with shift in parentheses
                    $sevaLabel = "$sevaName ($startTime - $endTime)";

                    echo "<option value='$sevaId'>$sevaLabel</option>";
                }
                ?>
            </select>
            <br>
        <label for="faculty_select">Select a Faculty:</label>
        <select id="faculty_select" name="faculty_id">
            <?php
            // PHP code to fetch Faculty names and IDs from the database
            $facultyQuery = "SELECT EID, `name` FROM login WHERE user_type = 'faculty'";
            $facultyResult = mysqli_query($con, $facultyQuery);

            while ($facultyRow = mysqli_fetch_assoc($facultyResult)) {
                echo "<option value='" . $facultyRow['EID'] . "'>" . $facultyRow['name'] . "</option>";
            }
            ?>
        </select>
        <br>

        <input type="submit" name="assign_faculty" value="Assign Faculty">
    </form>
</div>


    <div id="view_assigned_seva_faculty" class="tab">
    <br>
    <h3>View Faculty Assigned Seva</h3>
    <table>
    <tr>
        <th>Seva ID</th>
        <th>Seva Name</th>
        <th>Assigned Faculty</th>
        <th>Faculty Contact</th>
        <th>Start Time</th>
        <th>End Time</th>
    </tr>
    <?php
    // PHP code to fetch and display assigned Faculty for Seva
    $sql = "SELECT faculty_assignment.SevaId, seva_details.`Seva Name`, login.name AS Assigned_Faculty,
        login.contact AS Faculty_Contact, faculty_assignment.StartTime, faculty_assignment.EndTime
    FROM faculty_assignment
    LEFT JOIN seva_details ON faculty_assignment.SevaId = seva_details.`Seva Id`
    LEFT JOIN login ON faculty_assignment.FacultyId = login.EID
    GROUP BY faculty_assignment.SevaId, seva_details.`Seva Name`, login.name,
        login.contact, faculty_assignment.StartTime, faculty_assignment.EndTime"; // Group by Seva ID, Faculty Name, and Faculty Contact
    $result = mysqli_query($con, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['SevaId'] . "</td>";
        echo "<td>" . $row['Seva Name'] . "</td>";
        echo "<td>" . $row['Assigned_Faculty'] . "</td>";
        echo "<td>" . $row['Faculty_Contact'] . "</td>";
        echo "<td>" . $row['StartTime'] . "</td>";
        echo "<td>" . $row['EndTime'] . "</td>";
        echo "</tr>";
    }
    ?>
</table>

</div>

<div id="view_assigned_seva" class="tab">
    <br>
    <h3>View Assigned Seva</h3>
    <table>
        <tr>
            <th>Seva Name</th>
            <th>Assigned Students</th>
            <th>Start Time</th>
            <th>End Time</th>
        </tr>
        <?php
        // PHP code to fetch and display assigned Seva tasks without considering Faculty ID
        $sql = "SELECT seva_details.`Seva Name`, GROUP_CONCAT(students.Name) AS Assigned_Students,
            seva_assignments.`StartTime`, seva_assignments.`EndTime`
        FROM seva_assignments
        LEFT JOIN seva_details ON seva_assignments.`Seva Id` = seva_details.`Seva Id`
        LEFT JOIN students ON seva_assignments.`Student ID` = students.SID
        GROUP BY seva_details.`Seva Id`"; // Group by Seva Id only
        $result = mysqli_query($con, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Seva Name'] . "</td>";
            echo "<td>" . $row['Assigned_Students'] . "</td>";
            echo "<td>" . $row['StartTime'] . "</td>";
            echo "<td>" . $row['EndTime'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>



    <div class="button-container">
        <form action="logout.php" method="post">
            <button type="submit" name="logout" class="tab-button" style="background-color: #FF0000;">Logout</button>
        </form>
    </div>

    <script>
        function openTab(tabName) {
            var i, tabcontent;
            tabcontent = document.getElementsByClassName("tab");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            document.getElementById(tabName).style.display = "block";
        }
        // JavaScript code to handle select all functionality
        const groupCheckboxes = document.querySelectorAll('.group-checkbox');
        groupCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkboxes = this.closest('.assignment-section').querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(innerCheckbox => {
                    innerCheckbox.checked = this.checked;
                });
            });
        });
    </script>
</body>