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

        table {
            border: 1px solid;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <div class="button-container">
        <button class="tab-button" onclick="openTab('student')">Student</button>
        <button class="tab-button" onclick="openTab('attendance')">Mark Attendance</button>
        <button class="tab-button" onclick="openTab('view_assigned_seva')">View Assigned</button>
        <button class="tab-button" onclick="openTab('profile')">Profile</button>

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
            // PHP code to fetch and display student details
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

    <div id="attendance" class="tab">
        <br>
        <h3>Mark Attendance</h3>
        <form action="mark_attendance.php" method="post">
            <!-- Dropdown to select the seva/task -->
            <label for="seva_select_attendance">Select a Seva:</label>
            <select id="seva_select_attendance" name="seva_id">
                <?php
                // PHP code to fetch seva names and IDs from the database
                $sql = "SELECT `Seva Id`, `Seva Name` FROM seva_details";
                $result = mysqli_query($con, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['Seva Id'] . "'>" . $row['Seva Name'] . "</option>";
                }
                ?>
            </select>
            <br>

            <!-- Dropdown to select the shift -->
            <label for="shift_select">Select a Shift:</label>
            <select id="shift_select" name="shift">
                <?php
                // PHP code to fetch distinct shifts from seva_assignments table
                $sql = "SELECT DISTINCT StartTime, EndTime FROM seva_assignments";
                $result = mysqli_query($con, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    $start_time = $row['StartTime'];
                    $end_time = $row['EndTime'];
                    echo "<option value='$start_time|$end_time'>$start_time to $end_time</option>";
                }
                ?>
            </select>
            <br><br>


            <!-- List of students for attendance -->
            <div class="attendance-container">
            </div>
            <br><br>


            <!-- Submit button to mark attendance -->
            <input type="submit" value="Mark Attendance">
        </form>
    </div>

    <div id="view_assigned_seva" class="tab">
        <br>
        <h3>View Assigned Seva</h3>
        <table>
            <tr>
                <th>Seva Name</th>
                <th>Assigned Students</th>
                <th>Assigned Faculty</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
            <?php
            // PHP code to fetch and display assigned seva tasks
            $sql = "SELECT seva_details.`Seva Name`, GROUP_CONCAT(students.Name) AS Assigned_Students, login.Name AS Assigned_Faculty,
        seva_assignments.`StartTime`, seva_assignments.`EndTime`
        FROM seva_assignments
        LEFT JOIN seva_details ON seva_assignments.`Seva Id` = seva_details.`Seva Id`
        LEFT JOIN students ON seva_assignments.`Student ID` = students.SID
        LEFT JOIN `login` ON seva_assignments.`Faculty ID` = login.EID
        WHERE login.EID = ? -- Add this condition to filter by faculty ID
        GROUP BY seva_details.`Seva Id`, login.Name";

            // Prepare and execute the SQL query with the faculty ID as a parameter
            $faculty_id = $_SESSION['id'];
            $profile_stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($profile_stmt, 'i', $faculty_id);
            mysqli_stmt_execute($profile_stmt);
            $profile_result = mysqli_stmt_get_result($profile_stmt);

            while ($row = mysqli_fetch_assoc($profile_result)) {
                echo "<tr>";
                echo "<td>" . $row['Seva Name'] . "</td>";
                echo "<td>" . $row['Assigned_Students'] . "</td>";
                echo "<td>" . $row['Assigned_Faculty'] . "</td>";
                echo "<td>" . $row['StartTime'] . "</td>";
                echo "<td>" . $row['EndTime'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <div id="profile" class="tab" style="display: block;">
        <br>
        <h3>Your Profile</h3>
        <table>
            <tr>
                <th>EID</th>
                <td><?php echo $_SESSION['id']; ?></td>
            </tr>
            <?php
            // PHP code to fetch and display additional profile information
            $sql = "SELECT `Name`, `Department`, `Email`, `Contact` FROM login WHERE EID = ?";
            $profile_stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($profile_stmt, 'i', $_SESSION['id']);
            mysqli_stmt_execute($profile_stmt);
            $profile_result = mysqli_stmt_get_result($profile_stmt);

            if ($profile_row = mysqli_fetch_assoc($profile_result)) {
                echo "<tr><th>Name</th><td>" . $profile_row['Name'] . "</td></tr>";
                echo "<tr><th>Department</th><td>" . $profile_row['Department'] . "</td></tr>";
                echo "<tr><th>Email</th><td>" . $profile_row['Email'] . "</td></tr>";
                echo "<tr><th>Contact</th><td>" . $profile_row['Contact'] . "</td></tr>";
            }
            mysqli_stmt_close($profile_stmt);
            ?>
        </table>
    </div>


    <div class="button-container">
        <!-- Add the logout button here -->
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
        const groupCheckboxes = document.querySelectorAll('.group-checkbox');
        groupCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkboxes = this.closest('.assignment-section').querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(innerCheckbox => {
                    innerCheckbox.checked = this.checked;
                });
            });
        });
        document.getElementById('seva_select_attendance').addEventListener('change', function() {
            var sevaId = this.value; // Get the selected Seva ID
            var studentsContainer = document.querySelector('.attendance-container');

            // Clear the existing student list
            studentsContainer.innerHTML = '';

            // Fetch students who are part of the selected Seva using AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_students.php?seva_id=' + sevaId, true);

            xhr.onload = function() {
                if (xhr.status === 200) {
                    var students = JSON.parse(xhr.responseText);

                    // Populate the list of students
                    students.forEach(function(student) {
                        var studentCheckbox = document.createElement('input');
                        studentCheckbox.type = 'checkbox';
                        studentCheckbox.name = 'students[]';
                        studentCheckbox.value = student.SID;

                        var studentLabel = document.createElement('label');
                        studentLabel.appendChild(studentCheckbox);
                        studentLabel.appendChild(document.createTextNode(student.Name + ' (' + student.RollNumber + ')'));

                        studentsContainer.appendChild(studentLabel);
                    });
                }
            };

            xhr.send();
        });
    </script>
</body>

</html>