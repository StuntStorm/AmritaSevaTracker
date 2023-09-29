<?php
session_start();

if (isset($_SESSION['otp']) && isset($_SESSION['mail'])) {
    $otp = $_SESSION['otp'];
    $email = $_SESSION['mail'];

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
    $mail->Body = "<p>Dear user, </p> <h3>Your verify OTP code is $otp <br></h3>
    <br><br>
    <p>With regards,</p>
    <b>Amrita Seva Tracking Team <3</b>
    ";

    if (!$mail->send()) {
        ?>
        <script>
            alert("Register Failed, Invalid Email");
            window.location.href = 'login.php'; // Redirect to login.php
        </script>
        <?php
    } else {
        ?>
        <script>
            alert("Register Successfully, OTP sent to <?php echo $email; ?> || Check your JUNK mail as well.");
            window.location.href = 'login.php'; // Redirect to login.php
        </script>
        <?php
    }
} else {
    ?>
    <script>
        alert("Session data not found. Please complete the registration form first.");
        window.location.href = 'login.php'; // Redirect to login.php
    </script>
    <?php
}
?>
