<?php
session_start();
require_once 'db.php'; // adjust path if needed
header("Content-Type: text/plain"); // frontend expects raw text like "OTP_SENT"

// ✅ include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

try {
    if (empty($_POST['action'])) {
        throw new Exception("No action provided");
    }

    $action = $_POST['action'];

    // --- SEND OTP ---
    if ($action === "send_otp_create") {
        $email = trim($_POST['email'] ?? '');
        $name  = "User"; // or fetch from db / form

        if (!$email) {
            throw new Exception("Email is required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
            throw new Exception("Invalid Gmail address");
        }

        // Check if already registered
        $stmt = $conn->prepare("SELECT user_id FROM tbl_user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            throw new Exception("Email already registered");
        }
        $stmt->close();

        // Generate OTP
        $otp = strval(random_int(100000, 999999));
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + 600; // 10 minutes
        $_SESSION['otp_email'] = $email;

        // --- Send OTP via PHPMailer ---
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ananthus952@gmail.com'; // your Gmail
            $mail->Password = 'rdchqkttrvfbkrno'; // app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('ananthus952@gmail.com', 'EcoCycle');
            $mail->addAddress($email, $name);
            $mail->isHTML(false);
            $mail->Subject = "EcoCycle OTP Verification";
            $mail->Body = "Hi $name,\n\nYour OTP is: $otp\n\nIt will expire in 10 minutes.";

            $mail->send();
            echo "OTP_SENT";
            exit;
        } catch (Exception $e) {
            throw new Exception("Failed to send OTP.");
        }
    }

    // --- VERIFY OTP ---
    if ($action === "verify_otp_create") {
        $otp   = trim($_POST['otp'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!$otp || !$email) {
            throw new Exception("OTP and email required");
        }

        if (empty($_SESSION['otp']) || empty($_SESSION['otp_expiry']) || empty($_SESSION['otp_email'])) {
            throw new Exception("No OTP session found");
        }

        if ($email !== $_SESSION['otp_email']) {
            throw new Exception("Email mismatch");
        }

        if (time() > $_SESSION['otp_expiry']) {
            unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['otp_email']);
            throw new Exception("OTP expired");
        }

        if ($otp !== $_SESSION['otp']) {
            throw new Exception("Invalid OTP");
        }

        // ✅ OTP valid
        $_SESSION['otp_verified'] = true;
        echo "OTP_VALID";
        exit;
    }

    throw new Exception("Unknown action");

} catch (Exception $e) {
    echo $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
