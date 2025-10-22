<?php
// ----------------------
// Use unique session name
// ----------------------
session_name("user_session");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================
// Login utilities
// =========================

/**
 * Check if user is logged in
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Require login for a page
 */
function requireLogin() {
    if (!isUserLoggedIn()) {
        header("Location: ../auth.php");  // Adjust path if needed
        exit();
    }
}

/**
 * Logout user
 */
function logoutUser() {
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

    // Destroy the session
    session_destroy();
}

// =========================
// Session utilities
// =========================

/**
 * Save the current page in session
 */
function setCurrentPage($page) {
    $_SESSION['current_page'] = $page;
}

/**
 * Get the current page safely
 */
function getCurrentPage() {
    if (isset($_GET['page'])) {
        $_SESSION['current_page'] = $_GET['page'];
        return $_GET['page'];
    }

    if (isset($_SESSION['current_page'])) {
        return $_SESSION['current_page'];
    }

    return "dashboard"; // default fallback
}

/**
 * Store user info in session after successful login
 */
function setUserSession($user) {
    // $user should be an array with keys: user_id, name, email
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['name'] ?? '';
    $_SESSION['email'] = $user['email'] ?? '';
}

/**
 * Get username from session
 */
function getUsername() {
    return $_SESSION['username'] ?? '';
}
?>
