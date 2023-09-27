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
        <button class="tab-button" onclick="openTab('view_assigned_seva')">Student Assigned</button>
        
        <button class="tab-button" onclick="openTab('view_assigned_seva_faculty')">Faculty Assigned</button>
        <button class="tab-button" onclick="openTab('profile')">Profile</button>
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

    <!-- Add the Profile tab content here -->
    <div id="profile" class="tab" style="display: block;">
        <br>
        <h3>User Profile</h3>
        <?php
        // Retrieve and display user profile information
        $user_id = $_SESSION['id'];
        $profile_sql = "SELECT * FROM students WHERE SID = ?";
        $profile_stmt = mysqli_prepare($con, $profile_sql);
        mysqli_stmt_bind_param($profile_stmt, 'i', $user_id);
        mysqli_stmt_execute($profile_stmt);
        $profile_result = mysqli_stmt_get_result($profile_stmt);

        if ($profile_row = mysqli_fetch_assoc($profile_result)) {
            // Start the table
            echo "<table border='1'>";
            
            // Display user profile information in table rows
            echo "<tr><th>Name</th><td>" . $profile_row['Name'] . "</td></tr>";
            echo "<tr><th>Email</th><td>" . $profile_row['Email'] . "</td></tr>";
            echo "<tr><th>Roll Number</th><td>" . $profile_row['RollNumber'] . "</td></tr>";
            echo "<tr><th>Contact</th><td>" . $profile_row['Contact'] . "</td></tr>";
            echo "<tr><th>Semester</th><td>" . $profile_row['Semester'] . "</td></tr>";
            echo "<tr><th>Batch</th><td>" . $profile_row['Batch'] . "</td></tr>";
            
            // Close the table
            echo "</table>";
            
            // Add more profile information fields as needed
        }
        
        else {
            // Handle the case where no profile information is found
            echo "<p>No profile information available.</p>";
        }

        mysqli_stmt_close($profile_stmt);
        ?>
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
    </script>
</body>