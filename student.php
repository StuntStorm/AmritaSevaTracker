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
        <button class="tab-button" onclick="openTab('view_assigned_seva')">Assigned</button>
        <button class="tab-button" onclick="openTab('profile')">Profile</button>
    </div>

    <div id="view_assigned_seva" class="tab">
        <br>
        <h3>View Assigned Seva</h3>
        <table>
            <tr>
                <th>Seva Name</th>
                <th>Assigned Faculty</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
            <?php
            // Get the student ID from the session
            $student_id = $_SESSION['id'];

            // PHP code to fetch and display assigned seva tasks for the current student
            $sql = "SELECT seva_details.`Seva Name`, login.Name AS Assigned_Faculty,
                    seva_assignments.`StartTime`, seva_assignments.`EndTime`
                FROM seva_assignments
                LEFT JOIN seva_details ON seva_assignments.`Seva Id` = seva_details.`Seva Id`
                LEFT JOIN `login` ON seva_assignments.`Faculty ID` = login.EID
                WHERE seva_assignments.`Student ID` = ?
                GROUP BY seva_details.`Seva Id`, login.Name"; // Group by Seva Id and Faculty Name

            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $student_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['Seva Name'] . "</td>";
                echo "<td>" . $row['Assigned_Faculty'] . "</td>";
                echo "<td>" . $row['StartTime'] . "</td>";
                echo "<td>" . $row['EndTime'] . "</td>";
                echo "</tr>";
            }

            mysqli_stmt_close($stmt);
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