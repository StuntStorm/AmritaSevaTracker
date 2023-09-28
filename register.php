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
    <option value="Academic Cell">Academic Cell</option>
              <option value="Academic Innovations">Academic Innovations</option>
              <option value="Accounts">Accounts</option>
              <option value="Administrative Office">Administrative Office</option>
              <option value="Amrita Darshanam">Amrita Darshanam</option>
              <option value="Amrita Yoga">Amrita Yoga</option>
              <option value="Admission Office">Admission Office</option>
              <option value="AmritaCARE">AmritaCARE</option>
              <option value="AOC">AOC</option>
              <option value="Artificial intelligence">Artificial intelligence</option>
              <option value="ASAS_Principal_Office">ASAS_Principal_Office</option>
              <option value="Asset Management">Asset Management</option>
              <option value="Biotechnology">Biotechnology</option>
              <option value="Campus Clinic">Campus Clinic</option>
              <option value="Campus Planning and Development">Campus Planning and Development</option>
              <option value="Canteen">Canteen</option>
              <option value="Center-ICTS">Center-ICTS</option>
              <option value="Central Store">Central Store</option>
              <option value="Chemistry">Chemistry</option>
              <option value="Commerce and Management">Commerce and Management</option>
              <option value="Computer Science and Applications">Computer Science and Applications</option>
              <option value="Computer Science Engineering">Computer Science Engineering</option>
              <option value="Courier and Mail">Courier and Mail</option>
              <option value="Cyber Security - HR">Cyber Security - HR</option>
              <option value="Dean Administration">Dean Administration</option>
              <option value="Dean Office">Dean Office</option>
              <option value="Department of CIR">Department of CIR</option>
              <option value="Department of English">Department of English</option>
              <option value="Department of Management">Department of Management</option>
              <option value="Department of Physical Education">Department of Physical Education</option>
              <option value="Department of Social Work">Department of Social Work</option>
              <option value="Department of Students Affairs">Department of Students Affairs</option>
              <option value="Director of Special Projects">Director of Special Projects</option>
              <option value="Director Office">Director Office</option>
              <option value="Directorate of Alumni relations">Directorate of Alumni relations</option>
              <option value="E-Governance">E-Governance</option>
              <option value="Electrical and Electronics Engineering">Electrical and Electronics Engineering</option>
              <option value="Electronics and Communication Engineering">Electronics and Communication Engineering</option>
              <option value="Estate - Civil Construction">Estate - Civil Construction</option>
              <option value="Estate - Fabrication">Estate - Fabrication</option>
              <option value="Estate - General">Estate - General</option>
              <option value="Estate - House keeping">Estate - House keeping</option>
              <option value="Estate - Store">Estate - Store</option>
              <option value="Estate - Transport">Estate - Transport</option>
              <option value="Estate_Electrical Project">Estate_Electrical Project</option>
              <option value="Estate_Fire_and_Safety">Estate_Fire_and_Safety</option>
              <option value="Estate_Guest House -- Amritapuri">Estate_Guest House -- Amritapuri</option>
              <option value="Estate_PR_L">Estate_PR_L</option>
              <option value="Estate_Security">Estate_Security</option>
              <option value="General Administration">General Administration</option>
              <option value="Hostel - Gents">Hostel - Gents</option>
              <option value="Hostel - Ladies">Hostel - Ladies</option>
              <option value="Human Resource">Human Resource</option>
              <option value="HUTLabs">HUTLabs</option>
              <option value="Library">Library</option>
              <option value="Mathematics">Mathematics</option>
              <option value="Mechanical Engineering">Mechanical Engineering</option>
              <option value="Mind Brain Center">Mind Brain Center</option>
              <option value="Office of Academic Progression">Office of Academic Progression</option>
              <option value="Office of Dean Academic Progression">Office of Dean Academic Progression</option>
              <option value="Office of the Dean - PG Programs">Office of the Dean - PG Programs</option>
              <option value="Office of Youth Empowerment">Office of Youth Empowerment</option>
              <option value="Online Programs and Outreach">Online Programs and Outreach</option>
              <option value="PhD Office">PhD Office</option>
              <option value="Physics">Physics</option>
              <option value="PrincipalOffice_ASE">PrincipalOffice_ASE</option>
              <option value="Research Collaborations">Research Collaborations</option>
              <option value="Software Development Center">Software Development Center</option>
              <option value="Software Development Unit 2">Software Development Unit 2</option>
              <option value="Statistical Cell">Statistical Cell</option>
              <option value="Surveillance">Surveillance</option>
              <option value="Cyber Security">Cyber Security</option>
              <option value="WNA">WNA</option>
              <option value="ACIP">ACIP</option>
              <option value="PROVOST OFFICE">PROVOST OFFICE</option>
              <option value="CREATE">CREATE</option>
              <option value="TBI">TBI</option>
              <option value="AT">AT</option>
              <option value="CISAI">CISAI</option>
              <option value="ASF">ASF</option>
              <option value="Ammachi Lab">Ammachi Lab</option>

              
              
      

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
    var regex = /^[a-zA-Z0-9.-]+@[a-zA-Z0-9.-]+amrita\.edu$/;
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