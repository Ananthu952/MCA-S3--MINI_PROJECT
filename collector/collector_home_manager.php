<?php
// ------------------- SESSION & LOGIN CHECK -------------------
require_once(__DIR__ . "/session.php");

// ------------------- DATABASE CONNECTION -------------------
require_once(__DIR__ . "/../db.php"); // $conn should be defined in db.php

require_once(__DIR__ . "/notification.php");


header('Content-Type: application/json');

// ------------------- HANDLE AJAX REQUEST -------------------
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    case 'update_pickup_status':
    $data = json_decode(file_get_contents('php://input'), true);

    $pickup_id = $data['request_id'] ?? '';
    $new_status = $data['status'] ?? '';

    if (!$pickup_id || !$new_status) {
        echo json_encode(['status'=>'error','message'=>'Missing data']);
        exit();
    }

    // Update request status
    $stmt = mysqli_prepare($conn, "UPDATE tbl_scrap_request SET status = ? WHERE request_id = ?");
    mysqli_stmt_bind_param($stmt, "si", $new_status, $pickup_id);
    mysqli_stmt_execute($stmt);

    // ✅ Fetch user_id from request
    $userResult = mysqli_query($conn, "SELECT user_id FROM tbl_scrap_request WHERE request_id = $pickup_id");
    $user = mysqli_fetch_assoc($userResult);

    if ($user && isset($user['user_id'])) {
        // ✅ Notify the user
        notifyUser($conn, $user['user_id'], $pickup_id, $new_status);
    }

    echo json_encode(['status'=>'success','message'=>'Status updated and user notified']);
    break;


    // =======================================================
    // Dashboard Data
    // =======================================================
case 'get_dashboard_data':
    if (!isset($_SESSION['collector_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        exit();
    }

    $collector_id = $_SESSION['collector_id'];

    // Only show requests in this collector's assigned area
    $sql = "
        SELECT 
            r.request_id AS id,
            r.user_id,
            u.name AS user,
            u.phone,
            u.pincode,
            IFNULL(GROUP_CONCAT(st.scrap_name SEPARATOR ', '), '') AS scrapType,
            IFNULL(SUM(i.quantity), 0) AS weight,
            r.status,
            r.pickup_date,
            r.pickup_slot,
            r.collector_id,
            DATE(r.request_date) AS date,
            TIME(r.request_time) AS time
        FROM tbl_scrap_request r
        JOIN tbl_user u ON r.user_id = u.user_id
        JOIN tbl_collector_area ca ON u.pincode = ca.pincode
        LEFT JOIN tbl_scrap_request_item i ON r.request_id = i.request_id
        LEFT JOIN tbl_scrap_type st ON i.scrap_type = st.scrap_id
        WHERE ca.collector_id = ? 
          AND (r.collector_id IS NULL OR r.collector_id = ?)
        GROUP BY r.request_id, r.user_id, u.name, u.phone, u.pincode, r.status, r.pickup_date, r.pickup_slot, r.collector_id, r.request_date, r.request_time
        ORDER BY r.request_date DESC
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $collector_id, $collector_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $requests = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // ✅ Count pending requests
    $pendingCount = 0;
    foreach ($requests as $r) {
        if (strtolower($r['status']) === 'pending') {
            $pendingCount++;
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $requests,
        'pending_count' => $pendingCount
    ]);
    break;

    
    case 'assign_pickup':
        if (!isset($_SESSION['collector_id'])) {
            echo json_encode(['status'=>'error','message'=>'Not logged in']);
            exit();
        }

        $collector_id = $_SESSION['collector_id'];
        $data = json_decode(file_get_contents('php://input'), true);
        $request_id = $data['request_id'] ?? 0;

        if (!$request_id) {
            echo json_encode(['status'=>'error','message'=>'Invalid request']);
            exit();
        }

        $sql = "UPDATE tbl_scrap_request SET collector_id = ?, status = 'assigned' WHERE request_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $collector_id, $request_id);
        $success = mysqli_stmt_execute($stmt);

        if ($success) {
            echo json_encode(['status'=>'success']);
        } else {
            echo json_encode(['status'=>'error','message'=>'DB update failed']);
        }
        break;

    // =======================================================
    // Get Assigned Pickups
    // =======================================================
  case 'get_assigned_pickups':
    if (!isset($_SESSION['collector_id'])) {
        echo json_encode(['status'=>'error','message'=>'Not logged in']);
        exit();
    }

    $collector_id = $_SESSION['collector_id'];

    // Only pickups assigned to THIS collector
    $sql = "
        SELECT r.request_id, u.name AS user_name, u.phone, u.email, u.address,u.pincode, r.status,
               DATE(r.request_date) AS request_date, TIME(r.request_time) AS request_time,r.pickup_slot,r.pickup_date
        FROM tbl_scrap_request r
        JOIN tbl_user u ON r.user_id = u.user_id
        WHERE r.collector_id = ? AND r.status IN ('assigned')
        ORDER BY r.request_date DESC
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $collector_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $pickups = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Attach scrap items + images
    foreach ($pickups as &$p) {
        $itemsRes = mysqli_query($conn, "
            SELECT ri.item_id, s.scrap_name, ri.quantity, s.unit
            FROM tbl_scrap_request_item ri
            JOIN tbl_scrap_type s ON ri.scrap_type = s.scrap_id
            WHERE ri.request_id = {$p['request_id']}
        ");
        $items = mysqli_fetch_all($itemsRes, MYSQLI_ASSOC);

        if (empty($items)) {
            $items[] = ['scrap_name' => 'N/A', 'quantity' => 0, 'unit' => '', 'user_image' => null];
        } else {
            foreach ($items as &$item) {
                $imgRes = mysqli_query($conn, "
                    SELECT image_path 
                    FROM tbl_scrap_image 
                    WHERE request_id = {$p['request_id']} AND collector_id IS NULL
                    LIMIT 1
                ");
                $img = mysqli_fetch_assoc($imgRes);
                $item['user_image'] = $img['image_path'] ?? null;
            }
        }

        $p['items'] = $items;
    }

    echo json_encode(['status'=>'success','data'=>$pickups]);
    break;



// ===============================================
// Update Pickup Weight + Collector Images
// ===============================================
case 'update_pickup_weight':
    header('Content-Type: application/json; charset=utf-8');
    $pickup_id = $_POST['pickup_id'] ?? null;
    $new_weights = $_POST['weight'] ?? null;
    $collector_images = $_FILES['collector_images'] ?? [];

    if (!$pickup_id || !$new_weights) {
        echo json_encode(['status'=>'error','message'=>'Missing pickup ID or weights']);
        exit();
    }

    $uploadDir = __DIR__ . "/uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $errors = [];

    foreach ($new_weights as $item_id => $weight) {
        $weight = floatval($weight);

        $stmt = mysqli_prepare($conn, "UPDATE tbl_scrap_request_item SET collector_weight = ? WHERE item_id = ?");
        mysqli_stmt_bind_param($stmt, "di", $weight, $item_id);
        if (!mysqli_stmt_execute($stmt)) $errors[] = "Failed to update weight for item $item_id";

        if (isset($collector_images['name'][$item_id]) && !empty($collector_images['name'][$item_id])) {
            $filename = uniqid('col_') . '_' . basename($collector_images['name'][$item_id]);
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($collector_images['tmp_name'][$item_id], $targetPath)) {
                $relativePath = "collector/uploads/" . $filename;
                $stmt2 = mysqli_prepare($conn, "INSERT INTO tbl_scrap_image (request_id, collector_id, image_path, uploaded_at) VALUES (?, ?, ?, NOW())");
                mysqli_stmt_bind_param($stmt2, "iis", $pickup_id, $_SESSION['collector_id'], $relativePath);
                if (!mysqli_stmt_execute($stmt2)) $errors[] = "Failed to save image for item $item_id";
            } else {
                $errors[] = "Failed to upload image for item $item_id";
            }
        } else {
            $errors[] = "Missing image for item $item_id";
        }
    }

    if (!empty($errors)) {
        echo json_encode(['status'=>'error','message'=>implode(", ", $errors)]);
    } else {
        echo json_encode(['status'=>'success','message'=>'Collector weight & images updated successfully']);
    }
    exit;
// Get Collector Name (for Welcome Message)
// =======================================================
case 'get_collector_name':
    if (!isset($_SESSION['collector_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        exit();
    }

    $collector_id = $_SESSION['collector_id'];

    $stmt = $conn->prepare("SELECT name FROM tbl_collector WHERE collector_id = ?");
    $stmt->bind_param("i", $collector_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $collector = $result->fetch_assoc();

    if ($collector && !empty(trim($collector['name']))) {
        echo json_encode([
            'status' => 'success',
            'name' => $collector['name']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Collector not found'
        ]);
    }
    exit();

    // =======================================================
    // Notifications: Get
    // =======================================================
case 'get_notifications':
    if (!isset($_SESSION['collector_id'])) {
        echo json_encode(['status'=>'error','message'=>'Not logged in']);
        exit();
    }
   $collector_id = $_SESSION['collector_id'];
$sql = "
    SELECT 
        n.notification_id AS id,
        n.message,
         DATE_FORMAT(n.created_at, '%Y-%m-%d') AS date,
        DATE_FORMAT(n.created_at, '%h:%i %p') AS time,
        CASE WHEN n.status = 'unread' THEN 1 ELSE 0 END AS unread
    FROM tbl_notification n
    WHERE 
        n.target = 'collector'
        OR
        (n.target = 'all' AND (n.user_id IS NULL OR n.user_id = ?))
    ORDER BY n.created_at DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $collector_id); // only needed for user_id check in 'all' target
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$notifications = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode(['status'=>'success','data'=>$notifications]);
exit();

$user_id = 0; // or null converted to int
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $collector_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$notifications = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode(['status'=>'success','data'=>$notifications]);
exit(); 

// =======================================================
// Notifications: Mark One as Read
// =======================================================
case 'mark_notification_read':
    $id = $_POST['id'] ?? '';
    if (!$id) {
        echo json_encode(['status'=>'error','message'=>'Notification ID missing']);
        exit();
    }
    $stmt = mysqli_prepare($conn, "UPDATE tbl_notification SET status = 'read' WHERE notification_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    echo json_encode(['status'=>'success','message'=>'Notification marked as read']);
    break;

// =======================================================
// Notifications: Mark All as Read
// =======================================================
case 'mark_all_notifications':
    if (!isset($_SESSION['collector_id'])) {
        echo json_encode(['status'=>'error','message'=>'Not logged in']);
        exit();
    }
    $collector_id = $_SESSION['collector_id'];
    $stmt = mysqli_prepare($conn, "UPDATE tbl_notification SET status = 'read' WHERE collector_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $collector_id);
    mysqli_stmt_execute($stmt);
    echo json_encode(['status'=>'success','message'=>'All notifications marked as read']);
    break;

case 'get_register_date':

    $collector_id = $_SESSION['collector_id'] ?? null;
    if (!$collector_id) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        exit;
    }

    // ✅ Use the correct column name
    $stmt = $conn->prepare("SELECT created_at FROM tbl_collector WHERE collector_id = ?");
    $stmt->bind_param("i", $collector_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    // ✅ Return the same key name expected by frontend (register_date)
    echo json_encode(['status' => 'success', 'register_date' => $result['created_at'] ?? null]);
    exit;


    // =======================================================
    // Collector Settings: Get Profile
    // =======================================================
    case 'get_profile':
        if (!isset($_SESSION['collector_id'])) {
            echo json_encode(['status'=>'error','message'=>'Not logged in']);
            exit();
        }
        $collector_id = $_SESSION['collector_id'];
        $stmt = mysqli_prepare($conn, "SELECT name, email, phone FROM tbl_collector WHERE collector_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $collector_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $profile = mysqli_fetch_assoc($result);
        echo json_encode(['status'=>'success','data'=>$profile]);
        break;

    // =======================================================
    // Collector Settings: Update Profile
    // =======================================================
    case 'update_profile':
        if (!isset($_SESSION['collector_id'])) {
            echo json_encode(['status'=>'error','message'=>'Not logged in']);
            exit();
        }
        $collector_id = $_SESSION['collector_id'];
        $username = trim($_POST['username'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        if (!$username || !$phone) {
            echo json_encode(['status'=>'error','message'=>'All fields are required']);
            exit();
        }
        $stmt = mysqli_prepare($conn, "UPDATE tbl_collector SET name=?, phone=? WHERE collector_id=?");
        mysqli_stmt_bind_param($stmt, "ssi", $username, $phone, $collector_id);
        mysqli_stmt_execute($stmt);
        echo json_encode(['status'=>'success','message'=>'Profile updated successfully']);
        break;

    // =======================================================
    // Collector Settings: Change Password
    // =======================================================
    case 'change_password':
        if (!isset($_SESSION['collector_id'])) {
            echo json_encode(['status'=>'error','message'=>'Not logged in']);
            exit();
        }
        $collector_id = $_SESSION['collector_id'];
        $currentPassword = $_POST['currentPassword'] ?? '';
        $newPassword     = $_POST['newPassword'] ?? '';
        if (!$currentPassword || !$newPassword) {
            echo json_encode(['status'=>'error','message'=>'All fields are required']);
            exit();
        }
        // Fetch current password hash
        $stmt = mysqli_prepare($conn, "SELECT password FROM tbl_collector WHERE collector_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $collector_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        if (!$row || !password_verify($currentPassword, $row['password'])) {
            echo json_encode(['status'=>'error','message'=>'Current password is incorrect']);
            exit();
        }
        // Update with new password hash
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE tbl_collector SET password=? WHERE collector_id=?");
        mysqli_stmt_bind_param($stmt, "si", $hashed, $collector_id);
        mysqli_stmt_execute($stmt);
        echo json_encode(['status'=>'success','message'=>'Password changed successfully']);
        break;

    default:
        echo json_encode(['status'=>'error','message'=>'Invalid action']);
}
?>
