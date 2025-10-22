<?php
require_once 'db.php';

// ---------------------------------
// Common Variables
// ---------------------------------
$emailOrUsername = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($emailOrUsername) || empty($password)) {
    echo "Please enter email/username and password.";
    exit;
}

// ==================================================
// 1. ADMIN LOGIN
// ==================================================
$stmt = $conn->prepare("SELECT admin_id, username, email, password, status 
                        FROM tbl_admin 
                        WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (strtolower($row['status']) !== 'active') {
        echo "Your admin account is not active.";
        exit;
    }
    if (password_verify($password, $row['password']) || $password === $row['password']) {

        // Unique session for admin
        session_name("admin_session");
        session_start();

        $_SESSION['admin_id'] = $row['admin_id'];
        $_SESSION['role']     = 'admin';
        $_SESSION['username'] = $row['username'];

        echo "success:admin";
        exit;
    } else {
        echo "Incorrect password.";
        exit;
    }
}
$stmt->close();


// ==================================================
// 2. COLLECTOR LOGIN
// ==================================================
$stmt = $conn->prepare("SELECT collector_id, name, email, password, status 
                        FROM tbl_collector 
                        WHERE email = ?");
$stmt->bind_param("s", $emailOrUsername);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password']) || $password === $row['password']) {
        $status = strtolower($row['status']);

        if ($status === 'inactive') {
            echo "Your collector account is not active.";
            exit;
        } elseif ($status === 'first-time') {
            // First time login — set temporary session
            session_name("collector_session");
            session_start();
            $_SESSION['first_login_collector'] = $row['collector_id'];
            echo "first-time";
            exit;
        } else {
            // Normal collector login
            session_name("collector_session");
            session_start();

            $_SESSION['collector_id'] = $row['collector_id'];
            $_SESSION['role']         = 'collector';
            $_SESSION['name']         = $row['name'];
            $_SESSION['email']        = $row['email'];

            echo "success:collector";
            exit;
        }
    } else {
        echo "Incorrect password.";
        exit;
    }
}
$stmt->close();


// ==================================================
// 3. USER LOGIN
// ==================================================
$stmt = $conn->prepare("SELECT user_id, name, email, password, status 
                        FROM tbl_user 
                        WHERE email = ?");
$stmt->bind_param("s", $emailOrUsername);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (strtolower($row['status']) !== 'active') {
        echo "Your user account is not active.";
        exit;
    }
    if (password_verify($password, $row['password']) || $password === $row['password']) {

        // Unique session for user
        session_name("user_session");
        session_start();

        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['role']    = 'user';
        $_SESSION['name']    = $row['name'];
        $_SESSION['email']   = $row['email'];

        echo "success:user";
        exit;
    } else {
        echo "Incorrect password.";
        exit;
    }
}
$stmt->close();


// ==================================================
// NO ACCOUNT FOUND
// ==================================================
echo "No account found with this email/username.";
$conn->close();
