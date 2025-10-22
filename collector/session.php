<?php
// ----------------------
// Start session (with unique session name for collectors)
// ----------------------
if (session_status() === PHP_SESSION_NONE) {
    session_name("collector_session"); // unique session name
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
// Collector login check
// ----------------------
function requireCollectorLogin() {
    if (!isset($_SESSION['collector_id']) || empty($_SESSION['collector_id'])) {
        header("Location: ../auth.php");
        exit();
    }
}

// ----------------------
// Logout function
// ----------------------
function collectorLogout() {
    session_unset();
    session_destroy();
    header("Location: ../auth.php"); // redirect after logout
    exit();
}
?>
