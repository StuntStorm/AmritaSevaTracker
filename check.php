<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'newuniversity';

    $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
    if (mysqli_connect_errno()) {
        exit('Failed to connect to MySQL: ' . mysqli_connect_error());
    }

    // Prepare and execute the SQL query for the login table
    $stmt_login = $con->prepare('SELECT EID, password, user_type FROM login WHERE email = ?');
    $stmt_login->bind_param('s', $email);
    $stmt_login->execute();

    // Bind the user_type to a new variable
    $stmt_login->bind_result($user_id, $hashed_password, $user_type);

    // Check if the user is found in the login table
    if ($stmt_login->fetch() && password_verify($password, $hashed_password)) {
        // Password is correct
        $_SESSION['id'] = $user_id; // Set the user's ID in the session
        $_SESSION['user_type'] = $user_type;
        // Echo the user_type instead of 'success'
        echo $user_type;

        $stmt_login->close();
    } else {
        // If the user is not found in the login table, check the students table
        $stmt_students = $con->prepare('SELECT SID, password, user_type FROM students WHERE Email = ?');
        $stmt_students->bind_param('s', $email);
        $stmt_students->execute();

        // Bind the StudentID and password
        $stmt_students->bind_result($student_id, $student_password, $user_type);

        // Check if the user is found in the students table
        if ($stmt_students->fetch() && password_verify($password, $student_password)) {
            // Password is correct
            $_SESSION['id'] = $student_id; // Set the student's ID in the session
            $_SESSION['user_type'] = $user_type;
            echo 'student'; // Indicate that it's a student

            $stmt_students->close();
        } else {
            echo 'failure'; // User not found in either table
        }
    }
}
?>
