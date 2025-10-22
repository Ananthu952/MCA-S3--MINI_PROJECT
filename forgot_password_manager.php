<?php
require_once 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request.";
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'send_otp':
        $email = trim($_POST['email'] ?? '');
        if (empty($email)) { echo "Please enter your email."; exit; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { echo "Invalid email format."; exit; }

        // Check if email exists
        $stmt = $conn->prepare("SELECT user_id, name FROM tbl_user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            echo "This email is not registered.";
            $stmt->close();
            $conn->close();
            exit;
        }

        $stmt->bind_result($user_id, $name);
        $stmt->fetch();
        $stmt->close();

        // Generate OTP & expiry
        $otp = rand(100000, 999999);
        $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $stmt = $conn->prepare("UPDATE tbl_user SET otp_code = ?, otp_expiry = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $otp, $expiry, $user_id);
        $stmt->execute();
        $stmt->close();

        // Send OTP
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ananthus952@gmail.com';
            $mail->Password = 'rdchqkttrvfbkrno';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('ananthus952@gmail.com', 'EcoCycle');
            $mail->addAddress($email, $name);
            $mail->isHTML(false);
            $mail->Subject = "EcoCycle Password Reset OTP";
            $mail->Body = "Hi $name,\n\nYour OTP is: $otp\n\nIt will expire in 10 minutes.";

            $mail->send();
            echo "OTP_SENT";
        } catch (Exception $e) {
            echo "Failed to send OTP.";
        }
        $conn->close();
        break;

    case 'verify_otp':
        $email = trim($_POST['email'] ?? '');
        $otp = trim($_POST['otp'] ?? '');
        if (empty($email) || empty($otp)) { echo "INVALID"; exit; }

        $stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM tbl_user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($dbOtp, $dbExpiry);

        if ($stmt->fetch() && $dbOtp == $otp && strtotime($dbExpiry) > time()) {
            echo "OTP_VALID";
        } else {
            echo "INVALID";
        }
        $stmt->close();
        $conn->close();
        break;

    default:
        echo "Invalid action.";
        break;
}
