<?php
// ------------------- SESSION & LOGIN CHECK -------------------
require_once(__DIR__ . "../session.php");
requireAdminLogin(); // Redirect if not logged in as admin

// ------------------- DATABASE CONNECTION -------------------
require_once(__DIR__ . "../db.php"); // DB connection

// ------------------- ADMIN INFO -------------------
$admin_name = "Admin"; // Default fallback
$unread_count = 0;      // Default for notifications

if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
}
?>
