<?php session_start() ?>


<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up Form</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>
</head>
<body>





<div class="container">
  <div class="form-box" style="border-radius: 10px;  width: 90%;
    max-width: 450px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px 50px 30px;
    text-align: center;
    transition: max-height 0.5s; /* Add transition for smooth resizing */">
    <h1 id="title">Verify Account</h1>
    <form id="myForm" action="#" method="POST" name="register">
      <div class="input-field">
        <i class="fa-solid fa-lock"></i>
        <input type="text" id="otp" placeholder="OTP" name="otp_code">
      </div>

      <input type="submit" value="Verify" name="verify" style="
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
<p id="quote" style="font-style: italic;"></p>


    </form>
  </div>
</div>

</body>
</html>
<?php 
    include('connect/connection.php');
    if(isset($_POST["verify"])){
      $otp = $_SESSION['otp'];
      $email = $_SESSION['mail'];
      $otp_code = $_POST['otp_code'];
  
      if($otp != $otp_code){
          ?>
         <script>
             alert("Invalid OTP code");
         </script>
         <?php
      } else {
          // Update the status to 1 in the students table
          $updateStudentQuery = mysqli_query($connect, "UPDATE students SET status = 1 WHERE Email = '$email'");
          
          // Update the status to 1 in the login table
          $updateLoginQuery = mysqli_query($connect, "UPDATE login SET status = 1 WHERE email = '$email'");
          
          if($updateStudentQuery && $updateLoginQuery) {
              ?>
               <script>
                   alert("Verify account done, you may sign in now");
                   window.location.replace("login.php");
               </script>
               <?php
          } else {
              ?>
               <script>
                   alert("Error updating status");
               </script>
               <?php
          }
      }
  }
  

?>