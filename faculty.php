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
        <button class="tab-button" onclick="openTab('batch_info')">Batch Info</button>
        <button class="tab-button" onclick="openTab('mark_attendance')">Mark Attendance</button>
        <button class="tab-button" onclick="openTab('view_attendance')">View Attendance</button>
        <button class="tab-button" onclick="openTab('view_assigned_seva')">View Assigned</button>
        <button class="tab-button" onclick="openTab('profile')">Profile</button>

    </div>

    <div id="student" class="tab">
        <br>
        <h3>Student Details</h3>

        <!-- Dropdown to select the Seva for filtering students -->
        <label for="seva_select_students">Select a Seva:</label>
        <select id="seva_select_students" name="seva_id">
            <?php
            // PHP code to fetch Seva names and IDs from the database
            $sql = "SELECT `Seva Id`, `Seva Name` FROM seva_details";
            $result = mysqli_query($con, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['Seva Id'] . "'>" . $row['Seva Name'] . "</option>";
            }
            ?>
        </select>
        <button id="fetch_students_button" class="tab-button">Fetch Students</button>
        <button type="button" class="tab-button" onclick="exportStudentTableToCSV()">Export Student Data to CSV</button>
        <br><br>

        <!-- Container to display students -->
        <div id="students_container"></div>
    </div>

    <div id="batch_info" class="tab">
        <br>
        <h3>Batch Info</h3>

        <!-- Dropdown to select the Batch + Semester for filtering students -->
        <label for="batch_semester_select">Select Batch:</label>
        <select id="batch_semester_select" name="batch_semester">
            <?php
            // PHP code to fetch unique Batch + Semester combinations from the database
            $sql = "SELECT DISTINCT CONCAT(`batch`, ' ', `semester`) AS batch_semester FROM students";
            $result = mysqli_query($con, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['batch_semester'] . "'>" . $row['batch_semester'] . "</option>";
            }
            ?>
        </select>
        <button id="fetch_student_details_button" class="tab-button">Fetch Student Details</button>
        <button type="button" class="tab-button" onclick="exportBatchInfoTableToCSV()">Export Batch Info Data to CSV</button>
        <br><br>

        <!-- Container to display students -->
        <div id="student_details_container"></div>
    </div>


    <div id="mark_attendance" class="tab">
        <br>
        <h3>Mark Attendance</h3>
        <form action="mark_attendance.php" method="POST">
            <!-- Dropdown to select the seva/task -->
            <label for="seva_select">Select a Seva:</label>
            <select id="seva_select" name="seva_id">
                <?php
                // PHP code to fetch seva names, IDs, and shifts from the database
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

            <label for="attendance_date">Attendance Date:</label>
            <input type="date" id="attendance_date" name="attendance_date" required><br><br>
            

            <input type="submit" value="Go Mark">
        </form>
    </div>

    <div id="view_attendance" class="tab">
        <br>
        <h3>View Attendance</h3>
        <!-- Dropdown to select the seva/task -->
        <label for="seva_select_view">Select a Seva:</label>
        <select id="seva_select_view" name="seva_id">
            <?php
            // PHP code to fetch seva names, IDs, and shifts from the database
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

        <label for="attendance_date_view">Attendance Date:</label>
        <input type="date" id="attendance_date_view" name="attendance_date" required><br><br>

        <input type="submit" value="View Attendance" id="view_attendance_button">
        <button type="button" class="tab-button" onclick="exportViewAttendanceTableToCSV()">Export View Attendance Data to CSV</button>
        <div id="attendance_result"></div> <!-- Display attendance here -->
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
        document.addEventListener('DOMContentLoaded', function() {
            // Add an event listener to the "View Attendance" button
            document.querySelector('#view_attendance_button').addEventListener('click', function() {
                const sevaId = document.querySelector('#seva_select_view').value;
                const attendanceDate = document.querySelector('#attendance_date_view').value;
                const attendanceResultDiv = document.querySelector('#attendance_result');

                // Make an AJAX request to fetch attendance data
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'view_attendance.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const attendanceData = JSON.parse(xhr.responseText);

                        // Create and populate the table
                        let tableHtml = '';
                        tableHtml += '<table border="1" style="border-collapse:collapse">';
                        tableHtml += '<tr><th>Student Name</th><th>Roll Number</th><th>Semester</th><th>Batch</th><th>Attendance</th></tr>';

                        for (const entry of attendanceData) {
                            tableHtml += `<tr><td>${entry.student_name}</td><td>${entry.roll_number}</td><td>${entry.semester}</td><td>${entry.batch}</td><td>${entry.is_present}</td></tr>`;
                        }

                        tableHtml += '</table>';

                        // Update the attendance result div with the table
                        attendanceResultDiv.innerHTML = tableHtml;
                    }
                };

                // Send the POST request with seva_id and attendance_date
                xhr.send(`seva_id=${sevaId}&attendance_date=${attendanceDate}`);
            });

            document.getElementById('fetch_students_button').addEventListener('click', function() {
                var sevaId = document.getElementById('seva_select_students').value;
                var studentsContainer = document.getElementById('students_container');

                // Clear the existing student list
                studentsContainer.innerHTML = '';

                // Fetch students who are part of the selected Seva using AJAX
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'fetch_students.php?seva_id=' + sevaId, true);

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var students = JSON.parse(xhr.responseText);

                        // Create a table to display student details
                        var table = document.createElement('table');
                        table.border = '1'; // Add borders to the table for better formatting
                        table.style.borderCollapse = 'collapse'; // Collapse borders

                        // Create table header row
                        var headerRow = table.insertRow(0);
                        var headers = ['Name', 'Roll No', 'Semester', 'Batch'];
                        for (var i = 0; i < headers.length; i++) {
                            var th = document.createElement('th');
                            th.textContent = headers[i];
                            headerRow.appendChild(th);
                        }

                        // Populate the table with student data
                        students.forEach(function(student) {
                            var row = table.insertRow(-1);

                            // Add Name, Roll No, Semester, and Batch to each row
                            var nameCell = row.insertCell(0);
                            nameCell.textContent = student.Name;

                            var rollNoCell = row.insertCell(1);
                            rollNoCell.textContent = student.RollNumber;

                            var semesterCell = row.insertCell(2);
                            semesterCell.textContent = student.Semester;

                            var batchCell = row.insertCell(3);
                            batchCell.textContent = student.Batch;
                        });

                        // Clear existing student details (if any) and append the new table
                        var studentsContainer = document.querySelector('#students_container');
                        studentsContainer.innerHTML = '';
                        studentsContainer.appendChild(table);
                    }
                };

                xhr.onerror = function() {
                    console.error('Request failed');
                    studentsContainer.innerHTML = '<p>Request failed.</p>';
                };

                xhr.send();
            });

            document.querySelector('#fetch_student_details_button').addEventListener('click', function() {
                const batchSemester = document.querySelector('#batch_semester_select').value;
                const studentDetailsContainer = document.querySelector('#student_details_container');
                // Clear the existing student details
                studentDetailsContainer.innerHTML = '';

                // Make an AJAX request to fetch student details
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'fetch_student_details.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                // Add event listeners to handle the response
                xhr.addEventListener('load', function() {
                    if (xhr.status === 200) {
                        const studentDetails = JSON.parse(xhr.responseText);
                        // Create and populate the table
                        let tableHtml = '';
                        tableHtml += '<table border="1" style="border-collapse:collapse">';
                        tableHtml += '<tr><th>Student Name</th><th>Roll Number</th><th>Seva</th></tr>';

                        for (const entry of studentDetails) {
                            tableHtml += `<tr><td>${entry.student_name}</td><td>${entry.roll_number}</td><td>${entry.seva_name}</td></tr>`;
                        }

                        tableHtml += '</table>';

                        // Update the student details container with the table
                        studentDetailsContainer.innerHTML = tableHtml;
                    } else {
                        // Handle HTTP error (e.g., 404, 500)
                        console.error('HTTP Error:', xhr.status, xhr.statusText);
                    }
                });

                xhr.addEventListener('error', function() {
                    // Handle network or other errors
                    console.error('Network Error');
                });

                xhr.addEventListener('abort', function() {
                    // Handle request abort (if needed)
                    console.warn('Request Aborted');
                });

                // Send the POST request with batch_semester
                xhr.send(`batch_semester=${batchSemester}`);
            });
        });
    </script>

<script>
        // Function to export the student table to CSV
        function exportStudentTableToCSV() {
            // Get the table data from the students_container div
            const table = document.getElementById("students_container").querySelector("table");

            // Extract the table rows
            const rows = table.querySelectorAll("tr");

            // Initialize a CSV string with headers
            let csv = "Name, Roll No, Semester, Batch\n";

            // Loop through rows and extract data
            for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
                const columns = rows[i].querySelectorAll("td");
                const name = columns[0].textContent;
                const rollNo = columns[1].textContent;
                const semester = columns[2].textContent;
                const batch = columns[3].textContent;
                csv += `${name}, ${rollNo}, ${semester}, ${batch}\n`;
            }

            // Create a Blob with the CSV data
            const blob = new Blob([csv], { type: 'text/csv' });

            // Create a link to download the CSV file
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "student_data.csv";

            // Trigger the download
            link.click();
        }

        // Function to export the batch info table to CSV
        function exportBatchInfoTableToCSV() {
            // Get the table data from the student_details_container div
            const table = document.getElementById("student_details_container").querySelector("table");

            // Extract the table rows
            const rows = table.querySelectorAll("tr");

            // Initialize a CSV string with headers
            let csv = "Batch Name, Roll Number, Seva\n";

            // Loop through rows and extract data
            for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
                const columns = rows[i].querySelectorAll("td");
                const batchName = columns[0].textContent;
                const rollno = columns[1].textContent;
                const seva = columns[2].textContent;
                csv += `${batchName}, ${rollno}, ${seva}\n`;
            }

            // Create a Blob with the CSV data
            const blob = new Blob([csv], { type: 'text/csv' });

            // Create a link to download the CSV file
            const links = document.createElement("a");
            links.href = URL.createObjectURL(blob);
            links.download = "batch_info_data.csv";

            // Trigger the download
            links.click();
        }
    </script>
    <script>
    // Function to export the view attendance table to CSV
    function exportViewAttendanceTableToCSV() {
        // Get the table data from the view_attendance div
        const table = document.getElementById("view_attendance").querySelector("table");

        // Extract and format the data as CSV according to the specified structure
        let csv = "Student Name, Roll Number, Semester, Batch, Attendance\n";

        // Loop through the rows and extract data (modify this part accordingly)
        for (const row of table.querySelectorAll("tr")) {
            const columns = row.querySelectorAll("td");
            if (columns.length === 5) {
                const studentName = columns[0].textContent;
                const rollNumber = columns[1].textContent;
                const semester = columns[2].textContent;
                const batch = columns[3].textContent;
                const attendance = columns[4].textContent;
                csv += `${studentName}, ${rollNumber}, ${semester}, ${batch}, ${attendance}\n`;
            }
        }

        // Create a Blob with the CSV data
        const blob = new Blob([csv], { type: 'text/csv' });

        // Create a link to download the CSV file
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "view_attendance_data.csv";

        // Trigger the download
        link.click();
    }
</script>
</body>

</html>