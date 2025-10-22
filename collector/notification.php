<?php
function notifyUser($conn, $user_id, $request_id, $status) {
    $status_message = ucfirst($status);
    switch (strtolower($status)) {
        case 'assigned':
            $message = "Your scrap request #$request_id has been assigned to a collector.";
            break;
        case 'collected':
            $message = "Your scrap request #$request_id has been picked up.";
            break;
        case 'completed':
            $message = "Your scrap request #$request_id has been completed successfully.";
            break;
        case 'cancelled':
            $message = "Your scrap request #$request_id has been cancelled.";
            break;
        default:
            $message = "The status of your scrap request #$request_id has been updated to '$status_message'.";
    }

    $stmt = $conn->prepare("
        INSERT INTO tbl_notification (user_id, request_id, message, status, created_at)
        VALUES (?, ?, ?, 'unread', NOW())
    ");
    $stmt->bind_param("iis", $user_id, $request_id, $message);
    $stmt->execute();
}
?>
