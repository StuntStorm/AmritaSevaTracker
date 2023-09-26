<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Amrita Seva Tracker</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>
</head>
<body style="width: 100%;
    height: 100vh;
    background-image: linear-gradient(rgba(0,0,50,0.1),rgba(0,0,50,0.1)), url(Background.jpg);
    background-position: center;
    background-size: cover;
    position: relative;">

<!-------------------------------------->
<?php
session_start();
include('connect/connection.php');

if (isset($_POST["register"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $department = $_POST["department"];
    $contact = $_POST["phone"];

    $check_email_query = mysqli_query($connect, "SELECT * FROM login WHERE email ='$email'");
    $emailRowCount = mysqli_num_rows($check_email_query);

    if ($emailRowCount > 0) {
        // If email exists, show the error message and stop further processing
        echo "<script>alert('The provided email already exists in our system.');</script>";
    } else {
        // Check if the contact number already exists
        $check_contact_query = mysqli_query($connect, "SELECT * FROM login WHERE contact ='$contact'");
        $contactRowCount = mysqli_num_rows($check_contact_query);

        if (!empty($email) && !empty($password)) {
            if ($contactRowCount > 0) {
                // Contact exists, so update email and password for this contact
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $update_query = "UPDATE login SET email='$email', password='$password_hash', name='$name', department='$department' WHERE contact='$contact'";
                mysqli_query($connect, $update_query);
            } else {
                // Contact doesn't exist, so insert a new record in students table
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $insert_student_query = "INSERT INTO login (name, email, password, department, contact) VALUES ('$name', '$email', '$password_hash', '$department', '$contact')";
                mysqli_query($connect, $insert_student_query);
            }

            // Sending OTP for email verification remains the same
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['mail'] = $email;
            require "Mail/phpmailer/PHPMailerAutoload.php";
            $mail = new PHPMailer;

            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';

            $mail->Username = 'amritasevatracker@am.amrita.edu';
            $mail->Password = 'Qoy43911';

            $mail->setFrom('amritasevatracker@am.amrita.edu', 'Amrita - OTP Verification');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Your verify code";
            $mail->Body = "<p>Dear user, </p> <h3>Your verify OTP code is $otp <br></h3><br><br><p>With regards,</p><b>Amrita Seva Tracking Team</b>";

            if (!$mail->send()) {
                ?>
                <script>
                    alert("Register Failed, Invalid Email");
                </script>
                <?php
            } else {
                ?>
                <script>
                    alert("Register Successfully, OTP sent to <?php echo $email ?>");
                    window.location.replace('verification.php');
                </script>
                <?php
            }
        }
    }
}
?>


<!-------------------------------------->




<div class="container">
<div class="form-box" style="border-radius: 10px;  width: 90%;
    max-width: 450px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px 50px 150px;
    text-align: center;
    transition: max-height 0.5s; /* Add transition for smooth resizing */">
    <h3 id="title">[Faculty/Seva Coordinator]<br></h3>
    <h1 id="title">Register</h1>
    <form id="myForm" action="#" method="POST" name="register">
      <div class="input-group" id="nameGroup">
        <div class="input-field" id="nameField">
          <i class="fa-solid fa-user"></i>
          <input type="text" placeholder="Name [Full Name]" name="name">
        </div>

        <div class="input-field">
          <i class="fa-solid fa-envelope"></i>
          <input id="emailInput" type="email" placeholder="Email [Amrita Email]" name="email">
        </div>

        <span id="emailWarning" style="color: red;"></span>

        <div class="input-field">
          <i class="fa-solid fa-lock"></i>
          <input type="password" placeholder="Password" name="password">
        </div>

        <!-- Department Dropdown -->
        <div class="input-field" style="position: relative;">
    <i class="fa-solid fa-building" style=""></i>
    <select placeholder="Department" name="department" style="width: 100%; padding: 10px 15px; border: none; outline: none; background-color: #eaeaea; color: #333; border-radius: 3px; cursor: pointer;">
    <option value="Computer Science">Computer Science</option>
              <option value="BCA DS">BCA DS</option>
              <option value="MCA">MCA</option>
        <!-- Add more department options as needed -->
    </select>
</div>


        <!-- Phone Number Input -->
        <div class="input-field">
          <i class="fa-solid fa-phone"></i>
          <input type="text" placeholder="Phone Number" name="phone">
        </div>

        <input type="submit" value="Register" name="register" style="
          background-color: #0000FF;
          color: #FFFFFF;
          border: none;
          cursor: pointer;
          transition: background-color 0.3s ease;
          width: 200px; /* Adjust as needed */
          height: 50px; /* Adjust as needed */
          border-radius: 25px; /* Adjust as needed */
        " onmouseover="this.style.backgroundColor='#000099'"
        onmouseout="this.style.backgroundColor='#0000FF'">
        <br><br><p id="quote" style="font-style: italic;">

</p>

</p>

        
      </form>
    </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    var emailInput = document.getElementById('emailInput');
    var passwordInput = document.getElementById('password');
    var loginButton = document.getElementById('register');
    var emailWarning = document.getElementById('emailWarning');
    loginButton.disabled = true;
    function updateLoginButtonState() {
      
        if (!isValidEmail(emailInput.value) || passwordInput.value === '') {
            loginButton.disabled = true;
        } else {
            loginButton.disabled = false;
        }
    }

    emailInput.addEventListener('input', function() {
        if (!isValidEmail(emailInput.value)) {
            emailWarning.innerHTML = 'Invalid email address';
        } else {
            emailWarning.innerHTML = '';
        }
        updateLoginButtonState();
    });

    passwordInput.addEventListener('input', updateLoginButtonState);
});

function isValidEmail(email) {
    // A basic regex for validating an email format
    var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+amrita\.edu$/;
    return regex.test(email);
}

fetch('https://api.quotable.io/random')
  .then(response => response.json())
  .then(data => {
    var quote = data.content;
    document.getElementById('quote').innerText = quote;
  })
  .catch(error => console.error(error));
</script>
</body>
</html>