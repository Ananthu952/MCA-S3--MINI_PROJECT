<?php
include 'db.php'; // your DB connection
header('Content-Type: application/json');

// Fetch all scrap types
$sql = "SELECT scrap_name, unit, price_per_unit FROM tbl_scrap_type";
$result = $conn->query($sql);

$prices = [];
while ($row = $result->fetch_assoc()) {
    $prices[] = $row;
}
// Return JSON
echo json_encode(['prices' => $prices]);