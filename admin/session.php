<?php
// ----------------------
// Use unique session name for admin
// ----------------------
session_name("admin_session");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ----------------------
// Session timeout (optional)
// ----------------------
$timeout = 1800; // 30 minutes
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    // Session expired
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();

// ----------------------
// Admin login check
// ----------------------
function requireAdminLogin() {
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
        // Not logged in as admin
        header("Location: ../auth.php"); // redirect to login page
        exit();
    }
}

// ----------------------
// Logout function
// ----------------------
function adminLogout() {
    // Clear session data
    $_SESSION = [];

    // Destroy session cookie if exists
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy session
    session_destroy();

    // Redirect after logout
    header("Location: ../auth.php"); 
    exit();
}

// ----------------------
// Optional: get admin username
// ----------------------
function getAdminUsername() {
    return $_SESSION['username'] ?? '';
}
?>
