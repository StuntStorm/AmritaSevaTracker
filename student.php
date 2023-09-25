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
        <button class="tab-button" onclick="openTab('seva')">Seva</button>
        <button class="tab-button" onclick="openTab('view_assigned_seva')">Assigned</button>
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
    </script>
</body>