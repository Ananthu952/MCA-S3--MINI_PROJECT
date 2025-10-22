<?php 
// ------------------- SESSION & LOGIN CHECK -------------------
require_once(__DIR__ . "/../session.php");
requireLogin();                // Redirect if not logged in

// ------------------- DATABASE CONNECTION -------------------
require_once(__DIR__ . "/../db.php");   // DB connection

// ------------------- USER INFO -------------------
$user_name = "Guest"; // Default fallback
$unread_count = 0;    // Default for notifications

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch username
    $query = $conn->prepare("SELECT name FROM tbl_user WHERE user_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_name = $row['name'];
    }

    // Fetch unread notifications count
    $query2 = $conn->prepare("SELECT COUNT(*) AS unread_count 
                              FROM tbl_notification 
                              WHERE user_id = ? AND status = 'unread'");
    $query2->bind_param("i", $user_id);
    $query2->execute();
    $result2 = $query2->get_result()->fetch_assoc();
    $unread_count = $result2['unread_count'] ?? 0;
}
?>
