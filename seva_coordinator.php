<?php

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
        <button class="tab-button" onclick="openTab('seva')">View Seva</button>
        <button class="tab-button" onclick="openTab('faculty')">View Faculty</button>
        <button class="tab-button" onclick="openTab('add_faculty')">Add Faculty</button>
        <button class="tab-button" onclick="openTab('student')">View Students</button>
        <button class="tab-button" onclick="openTab('attendance')">Mark Attendance</button>
        <button class="tab-button" onclick="openTab('add_student')">Add Students</button>
        <button class="tab-button" onclick="openTab('assign')">Assign Students & Faculty to Seva</button>
        <button class="tab-button" onclick="openTab('view_assigned_seva')">View Assigned</button>
        <button class="tab-button" onclick="window.location.href='upload.html'">Upload Students CSV</button>
    </div>
    <div id="seva" class="tab" style="display: block;">
        <br>
        <h3>Seva Details</h3>
        <table>
            <tr>
                <th>Seva Name</th>
                <th>Seva Coordinator</th>
            </tr>
            <?php
            // PHP code to fetch and display seva details
            $sql = "SELECT * FROM seva_details";
            $result = mysqli_query($con, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['Seva Name'] . "</td>";
                echo "<td>" . $row['Seva Coordinator'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
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
            <!-- Dropdown to select the seva/task -->
            <label for="seva_select">Select a Seva:</label>
            <select id="seva_select" name="seva_id">
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

            <!-- Shift section -->
            <label for="shift">Shift:</label>
            <div id="shift">
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" required>

                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" required>
            </div>
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

            <!-- Submit button to assign tasks -->
            <input type="submit" value="Assign Tasks">
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
            GROUP BY seva_details.`Seva Id`, login.Name"; // Group by both Seva Id and Faculty Name
            $result = mysqli_query($con, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
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