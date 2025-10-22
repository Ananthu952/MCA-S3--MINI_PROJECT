<?php
session_start();

if ($_POST['action'] === 'send_signup_otp') {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);

    $_SESSION['signup_otp'] = $otp;
    $_SESSION['signup_email'] = $email;

    // Send OTP via email
    mail($email, "EcoCycle Signup OTP", "Your OTP is: $otp");

    echo "OTP_SENT";
}

if ($_POST['action'] === 'verify_signup_otp') {
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    if (isset($_SESSION['signup_otp'], $_SESSION['signup_email']) &&
        $_SESSION['signup_email'] === $email &&
        $_SESSION['signup_otp'] == $otp) {
        echo "OTP_VALID";
    } else {
        echo "OTP_INVALID";
    }
}
?>
