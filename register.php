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

<?php session_start(); ?>
<?php
    include('connect/connection.php');

    if(isset($_POST["register"])){
      $name = $_POST["name"];
      $email = $_POST["email"];
      $password = $_POST["password"];
      $department = $_POST["department"];
      $contact = $_POST["phone"];
      $user_type = $_POST["user_type"];

        $check_query = mysqli_query($connect, "SELECT * FROM login where email ='$email'");
        $rowCount = mysqli_num_rows($check_query);

        if(!empty($email) && !empty($password)){
            if($rowCount > 0){
                ?>
                <script>
                    alert("User with email already exist!");
                </script>
                <?php
            }else{
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                $result = mysqli_query($connect, "INSERT INTO login (email, password, status, name, department, contact) VALUES ('$email', '$password_hash', 0, '$name', '$department', '$contact')");
    
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
                    <p>With regrads,</p>
                    <b>Amrita Seva Tracking Team</b>
                    ";
    
                            if(!$mail->send()){
                                ?>
                                    <script>
                                        alert("<?php echo "Register Failed, Invalid Email "?>");
                                    </script>
                                <?php
                            }else{
                                ?>
                                <script>
                                    alert("<?php echo "Register Successfully, OTP sent to " . $email ?>");
                                    window.location.replace('verification.php');
                                </script>
                                <?php
                            }
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
        <option value="Department 1">Department 1</option>
        <option value="Department 2">Department 2</option>
        <option value="Department 3">Department 3</option>
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