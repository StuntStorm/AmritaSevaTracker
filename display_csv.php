<?php

$servername = "localhost";
$username = "root";  // Change to your MySQL username
$password = "";      // Change to your MySQL password
$dbname = "newuniversity";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['csvFile'])) {

    $file = $_FILES['csvFile']['tmp_name'];

    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {  // assuming comma-separated CSV
            // Assuming your CSV columns are now in this order: Name, RollNumber, Contact, Semester, Batch
            $name = $data[0];
            $rollNumber = $data[1];
            $contact = $data[2];
            $semester = $data[3];
            $batch = $data[4];

            // Set default values for SID, user_type, and password
            $sid = NULL;  // Auto-incremented
            $userType = 'student';
            $password = '';

            // Updated the SQL and binding to exclude Email
            $stmt = $conn->prepare("INSERT INTO students (SID, Name, RollNumber, Contact, Semester, Batch, user_type, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $sid, $name, $rollNumber, $contact, $semester, $batch, $userType, $password);
            $stmt->execute();
        }
        fclose($handle);

        echo "CSV content uploaded to the database successfully!";
    } else {
        echo "Error opening file.";
    }
    header("Location: main_coordinator.php"); // Redirect to the main coordinator page
    exit();
}

// Display the names from the database
$query = "SELECT Name FROM students";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<br><br>Names in the database:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "Name: " . $row["Name"] . "<br>";
    }
} else {
    echo "0 results";
}

$conn->close();

