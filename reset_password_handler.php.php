<?php
session_start();
include 'db.php'; // your database connection

header('Content-Type: text/plain'); // simple text responses

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    if (empty($email) || empty($new_password)) {
        echo "Email or new password missing";
        exit;
    }

    // Optional: validate password strength server-side
    if (!preg_match('/[A-Z]/', $new_password) ||
        !preg_match('/[a-z]/', $new_password) ||
        !preg_match('/\d/', $new_password) ||
        !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password) ||
        strlen($new_password) < 6
    ) {
        echo "Password does not meet security requirements.";
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password in database
    $stmt = $conn->prepare("UPDATE tbl_user SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);
    if ($stmt->execute()) {
        echo "PASSWORD_RESET";
    } else {
        echo "Failed to reset password.";
    }
} else {
    echo "Invalid request method";
}
