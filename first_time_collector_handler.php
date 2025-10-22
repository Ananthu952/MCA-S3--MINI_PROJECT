<?php
// first_time_collector_handler.php
session_start();
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'first_time_set') {
    if (!isset($_SESSION['first_login_collector'])) {
        echo "Session expired. Please log in again.";
        exit;
    }

    $collector_id = $_SESSION['first_login_collector'];
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    if (strlen($new_password) < 6) {
        echo "Password too short.";
        exit;
    }

    $hashed = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE tbl_collector SET password=?, status='active' WHERE collector_id=?");
    $stmt->bind_param("si", $hashed, $collector_id);

    if ($stmt->execute()) {
        unset($_SESSION['first_login_collector']); // clear first-time session
        echo "PASSWORD_UPDATED";
    } else {
        echo "Database error. Try again.";
    }

    $stmt->close();
    $conn->close();
}
