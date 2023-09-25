<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Registration Form</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>
</head>

<body style="width: 100%;
    height: 100vh;
    background-image: linear-gradient(rgba(0,0,50,0.1),rgba(0,0,50,0.1)), url(Background.jpg);
    background-position: center;
    background-size: cover;
    position: relative;">

  <?php session_start(); ?>
  <?php
    include('connect/connection.php');

    if(isset($_POST["register"])){
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $rawRollNumber = $_POST["rollNumber"]; // User input with dots
        $contact = $_POST["contact"];
        $semester = $_POST["semester"];
        $batch = $_POST["batch"];
        $user_type = "student"; // Hardcoded for students
    
        // Remove dots and convert to uppercase
        $rollNumber = strtoupper($rawRollNumber);
    
        // Check if the email address has the correct domain
        if (!endsWith($email, '@am.students.amrita.edu')) {
            ?>
            <script>
                alert("Incorrect email format. Please use your Amrita email address.");
            </script>
            <?php
            exit; // Exit the script if the email format is incorrect
        }

        $email_check_query = mysqli_query($connect, "SELECT * FROM students WHERE Email='$email'");
        $emailRowCount = mysqli_num_rows($email_check_query);
    
        if($emailRowCount > 0) {
            ?>
            <script>
                alert("Sorry, email already exists for another user.");
            </script>
            <?php
            exit; // Exit the script if the email already exists
        }
    
        $roll_check_query = mysqli_query($connect, "SELECT * FROM students WHERE RollNumber='$rollNumber'");
    $rollRowCount = mysqli_num_rows($roll_check_query);

    if($rollRowCount > 0) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $update_query = "UPDATE students SET Email='$email', password='$password_hash' WHERE RollNumber='$rollNumber'";
        $result = mysqli_query($connect, $update_query);
    
                if($result){
                    $otp = rand(100000,999999);
                    $_SESSION['otp'] = $otp;
                    $_SESSION['mail'] = $email;
                    require "Mail/phpmailer/PHPMailerAutoload.php";
                    $mail = new PHPMailer;
    
                    $mail->isSMTP();
                    $mail->Host='smtp.office365.com';
                    $mail->Port=587;
                    $mail->SMTPAuth=true;
                    $mail->SMTPSecure='tls';
    
                    $mail->Username='amritasevatracker@am.amrita.edu';
                    $mail->Password='Qoy43911';
    
                    $mail->setFrom('amritasevatracker@am.amrita.edu', 'Amrita - OTP Verification');
                    $mail->addAddress($_POST["email"]);
    
                    $mail->isHTML(true);
                    $mail->Subject="Your verify code";
                    $mail->Body="<p>Dear user, </p> <h3>Your verify OTP code is $otp <br></h3>
                    <br><br>
                    <p>With regards,</p>
                    <b>Amrita Seva Tracking Team <3</b>
                    ";
    
                    if(!$mail->send()){
                        ?>
                        <script>
                            alert("<?php echo "Register Failed, Invalid Email "?>");
                        </script>
                        <?php
                    } else {
                        ?>
                        <script>
                            alert("<?php echo "Register Successfully, OTP sent to " . $email . " || Check your JUNK mail as well." ?>");
                            window.location.replace('verification.php');
                        </script>
                        <?php
                    }
                }
                else {
                  $password_hash = password_hash($password, PASSWORD_BCRYPT);
                  $result = mysqli_query($connect, "INSERT INTO students (Name, RollNumber, Email, Contact, Semester, Batch, user_type, password) VALUES ('$name', '$rollNumber', '$email', '$contact', '$semester', '$batch', '$user_type', '$password_hash')");
            }
        }
    }
    

// Function to check if a string ends with a specific substring
function endsWith($string, $suffix) {
    return substr($string, -strlen($suffix)) === $suffix;
}

// Function to validate Roll Number format
function isValidRollNumber($rollNumber) {
    $regex = '/^[A-Z]{2}\.[A-Z]{2}\.[A-Z0-9]{8}$/';
    return preg_match($regex, $rollNumber);
}
?>

  <div class="container">
    <div class="form-box" style="border-radius: 10px;  width: 90%;
    max-width: 450px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px 50px 240px;
    text-align: center;
    transition: max-height 0.5s; /* Add transition for smooth resizing */">
      <h3 id="title">Student<br></h3>
      <h1 id="title">Register</h1>
      <form id="myForm" action="sregister.php" method="POST" name="register">
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

          <div class="input-field">
            <i class="fa-solid fa-user"></i>
            <input type="text" placeholder="Roll Number (e.g., XX.XX.XXXXXXXXX)" name="rollNumber">
          </div>

          <div class="input-field">
            <i class="fa-solid fa-phone"></i>
            <input type="text" placeholder="Contact Number" name="contact">
          </div>

          <!-- Semester Dropdown -->
          <div class="input-field" style="position: relative;">
            <i class="fa-solid fa-calendar"></i>
            <select placeholder="Semester" name="semester"
              style="width: 100%; padding: 10px 15px; border: none; outline: none; background-color: #eaeaea; color: #333; border-radius: 3px; cursor: pointer;">
              <option value="1">Semester 1</option>
              <option value="2">Semester 2</option>
              <option value="3">Semester 3</option>
              <option value="4">Semester 4</option>
              <option value="5">Semester 5</option>
              <option value="6">Semester 6</option>
              <option value="7">Semester 7</option>
              <option value="8">Semester 8</option>
              <option value="9">Semester 9</option>
              <option value="10">Semester 10</option>
              <!-- Add more semester options as needed -->
            </select>
          </div>

          <!-- Batch Dropdown -->
          <div class="input-field" style="position: relative;">
            <i class="fa-solid fa-calendar"></i>
            <select placeholder="Batch" name="batch"
              style="width: 100%; padding: 10px 15px; border: none; outline: none; background-color: #eaeaea; color: #333; border-radius: 3px; cursor: pointer;">
              <option value="BCA">BCA</option>
              <option value="BCA DS">BCA DS</option>
              <option value="MCA">MCA</option>
              <option value="ME">ME</option>
              <option value="EAC">EAC</option>
              <option value="ELC">ELC</option>
              <option value="EEE">EEE</option>
              <option value="CYS">CYS</option>
              <option value="AIE A">AIE A</option>
              <option value="AIE B">AIE B</option>
              <option value="AI DS">AI DS</option>
              <option value="CSE A">CSE A</option>
              <option value="CSE B">CSE B</option>
              <option value="CSE C">CSE C</option>
              <option value="CSE D">CSE D</option>
              <option value="ECE A">ECE A</option>
              <option value="ECE B">ECE B</option>
              <option value="BBA">BBA</option>
              <option value="MBA">MBA</option>
              <option value="Bcom">Bcom</option>
              <option value="MSW">MSW</option>

              <!-- Add more batch options as needed -->
            </select>
          </div>

          <!-- User Type Field (Hidden) -->
          <input type="hidden" name="user_type" value="student">

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
          <br><br>
          <p id="quote" style="font-style: italic;">
          </p>
        </div>
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
        var email = emailInput.value;

        if (!isValidEmail(email) || passwordInput.value === '') {
            loginButton.disabled = true;
        } else {
            loginButton.disabled = false;
        }

        // Check if email contains the required domain
        if (!email.endsWith('@am.students.amrita.edu')) {
            emailWarning.innerHTML = 'Incorrect email format. Please use your Amrita email address.';
            loginButton.disabled = true;
        } else {
            emailWarning.innerHTML = '';
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

    // Add a click event listener to the register button
    loginButton.addEventListener('click', function(e) {
        // Check if the button is disabled (i.e., there's an error)
        if (loginButton.disabled) {
            // Prevent the form from submitting and reload the page
            e.preventDefault();
            location.reload();
        }
    });
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