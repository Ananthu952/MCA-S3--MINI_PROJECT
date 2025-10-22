<?php
session_start();
require_once 'db.php';  // Include your DB connection

// Only process POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method.";
    exit;
}

// Get and sanitize inputs
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$pincode = trim($_POST['pincode'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Basic validations
if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($pincode) || empty($password) || empty($confirm_password)) {
    echo "All fields are required.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format.";
    exit;
}

if (!preg_match('/^\d{10}$/', $phone)) {
    echo "Phone number must be 10 digits.";
    exit;
}

if (!preg_match('/^\d{6}$/', $pincode)) {
    echo "Pincode must be 6 digits.";
    exit;
}

if ($password !== $confirm_password) {
    echo "Passwords do not match.";
    exit;
}

// ✅ Check if pincode exists in tbl_collector_area
$stmt = $conn->prepare("SELECT 1 FROM tbl_collector_area WHERE pincode = ?");
if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
}
$stmt->bind_param("s", $pincode);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo "Sorry, service not available in your area (pincode not covered).";
    $stmt->close();
    exit;
}
$stmt->close();

// Check if email or phone already exists
$stmt = $conn->prepare("SELECT user_id FROM tbl_user WHERE email = ? OR phone = ?");
if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
}
$stmt->bind_param("ss", $email, $phone);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "Email or phone already registered. Try logging in.";
    $stmt->close();
    exit;
}
$stmt->close();

// Hash password securely
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Prepare insert statement
$stmt = $conn->prepare("INSERT INTO tbl_user (name, email, phone, password, address, pincode, register_date, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'active')");
if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
}
$stmt->bind_param("ssssss", $name, $email, $phone, $hashed_password, $address, $pincode);

// Execute insert and check result
if ($stmt->execute()) {
    echo "Signup successful! You can now login.";
} else {
    echo "Signup failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
