
<?php
function displayAlert($message) {
    echo "<script>alert('$message');</script>";
    ?>
    <script>window.location.href = 'sregister.php'; // Redirect to login.php</script>
    <?php
}
?>
