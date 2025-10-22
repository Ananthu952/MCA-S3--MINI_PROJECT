<?php
// logout.php
session_start();

// Destroy all session variables
$_SESSION = array();

// Destroy the session cookie (optional but safer)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

// Redirect back to index page with message
header("Location: ../index.php"); // use index.html if it's still HTML
exit();
?>
