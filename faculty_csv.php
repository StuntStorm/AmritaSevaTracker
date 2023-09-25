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
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {  
            // Assuming your CSV columns are in this order: Name, Department, Contact
            $name = $data[0];
            $department = $data[1];
            $contact = $data[2];

            // Prepare the SQL and bind for the "login" table
            $stmt = $conn->prepare("INSERT INTO login (name, department, contact) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $department, $contact);
            $stmt->execute();
        }
        fclose($handle);

        echo "CSV content uploaded to the 'login' table successfully!";
    } else {
        echo "Error opening file.";
    }
    header("Location: main_coordinator.php"); // Redirect to the main coordinator page
    exit();
}

// Display the names from the "login" table
$query = "SELECT Name FROM login";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<br><br>Names in the 'login' table:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "Name: " . $row["Name"] . "<br>";
    }
} else {
    echo "0 results";
}

$conn->close();

?>

