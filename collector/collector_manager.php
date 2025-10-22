<?php
// ------------------- SESSION & LOGIN CHECK -------------------
require_once(__DIR__ . "/session.php");

requireCollectorLogin(); // Redirect if not logged in as collector

// ------------------- DATABASE CONNECTION -------------------
require_once(__DIR__ . "/../db.php"); // ✅ using $conn (MySQLi)

// ------------------- COLLECTOR INFO -------------------
$collector_name = "Collector"; // Default fallback
$unread_count   = 0;           // Default for notifications

if (isset($_SESSION['collector_id'])) {
    $collector_id = $_SESSION['collector_id'];
    // Fetch collector name from DB (MySQLi)
    $stmt = $conn->prepare("SELECT name FROM tbl_collector WHERE collector_id = ?");
    $stmt->bind_param("i", $collector_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $collector_name = $row['name'];
    }
    $stmt->close();

    // Count unread notifications (status = 'unread')
    $stmt2 = $conn->prepare("
        SELECT COUNT(*) AS cnt 
        FROM tbl_notification 
        WHERE collector_id = ? 
          AND status = 'unread'
    ");
    $stmt2->bind_param("i", $collector_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    if ($countRow = $result2->fetch_assoc()) {
        $unread_count = $countRow['cnt'];
    }
    $stmt2->close();
}
?>

