<?php
// ------------------- SESSION & LOGIN CHECK -------------------
require_once(__DIR__ . "/../session.php");
requireLogin();  // Redirect if not logged in

// ------------------- DATABASE CONNECTION -------------------
require_once(__DIR__ . "/../db.php");


error_reporting(E_ALL);
ini_set('display_errors', 1);


// ------------------- USER ID -------------------
$user_id = $_SESSION['user_id'] ?? 0;

// ------------------- HANDLE AJAX REQUEST -------------------
$action = $_GET['action'] ?? $_POST['action'] ?? '';

header('Content-Type: application/json');


switch ($action) {
    // ------------------- GET USERNAME -------------------
    case 'get_username':
    $username = $_SESSION['name'] ?? ''; // Using the helper function
    if ($username) {
        echo json_encode(['status' => 'success', 'username' => $username]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Username not found']);
    }
    break;

    // ------------------- UPDATE USER ADDRESS -------------------
    case 'update_address':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $address = $_POST['address'] ?? '';
            $pincode = $_POST['pincode'] ?? '';

            if (!$address || !$pincode) {
                echo json_encode(['status'=>'error', 'message'=>'Address and pincode cannot be empty']);
                exit;
            }

            $stmt = $conn->prepare("UPDATE tbl_user SET address = ?, pincode = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $address, $pincode, $user_id);
            if ($stmt->execute()) {
                echo json_encode(['status'=>'success', 'message'=>'Address updated successfully']);
            } else {
                echo json_encode(['status'=>'error', 'message'=>'Failed to update address']);
            }
        }
        break;

    // ------------------- GET USER ADDRESS -------------------
    case 'get_addresses':
        $stmt = $conn->prepare("SELECT address, pincode FROM tbl_user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $addresses = [];
        if ($row = $result->fetch_assoc()) {
            $addresses[] = [
                'id' => 1, // dummy ID for single address
                'address' => $row['address'],
                'pincode' => $row['pincode'] ?? ''
            ];
        }
        echo json_encode($addresses);
        break;

    // ------------------- GET SCRAP TYPES -------------------
    case 'get_scrap_types':
        $result = $conn->query("SELECT scrap_id, scrap_name, unit, price_per_unit FROM tbl_scrap_type");
        $types = [];
        while ($row = $result->fetch_assoc()) {
            $types[] = $row;
        }
        echo json_encode($types);
        break;
    
    // ------------------- GET AVAILABLE TIME SLOTS -------------------
    case 'get_time_slots':
    // Force Indian Standard Time
    date_default_timezone_set("Asia/Kolkata");

    $timeSlots = [
        ['value' => '9AM - 11AM', 'start' => 9],
        ['value' => '11AM - 1PM', 'start' => 11],
        ['value' => '2PM - 4PM', 'start' => 14],
        ['value' => '4PM - 6PM', 'start' => 16]
    ];

    $currentDate = date('Y-m-d');
    $currentHour = (int)date('H'); // 0–23 (e.g., 17 = 5 PM IST)

    echo json_encode([
        'slots' => $timeSlots,
        'today' => $currentDate,
        'currentHour' => $currentHour
    ]);
    break;
    // ------------------- SUBMIT SCRAP REQUEST -------------------
    case 'submit_scrap_request':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        ob_start();
        header('Content-Type: application/json');

        try {
            $pickup_date = $_POST['pickup_date'] ?? '';
            $pickup_slot = $_POST['pickup_slot'] ?? '';
            $scrap_types = $_POST['scrap_type'] ?? [];
            $quantities  = $_POST['quantity'] ?? []; // safe fetch

            if (!$pickup_date || !$pickup_slot || empty($scrap_types)) {
                throw new Exception('All fields are required');
            }

            foreach ($scrap_types as $i => $type_id) {
                if (!$type_id) {
                    throw new Exception('Scrap type is required');
                }
            }

            $today = date('Y-m-d');
            if ($pickup_date < $today) {
                throw new Exception('Pickup date cannot be in the past');
            }

            $currentHour = (int)date('H');
            $slotMap = [
                "9AM - 11AM" => 9,
                "11AM - 1PM" => 11,
                "2PM - 4PM"  => 14,
                "4PM - 6PM"  => 16
            ];
            if ($pickup_date === $today && isset($slotMap[$pickup_slot]) && $slotMap[$pickup_slot] <= $currentHour) {
                throw new Exception('Selected time slot is no longer available');
            }

            // Insert into tbl_scrap_request
            $stmt = $conn->prepare("
                INSERT INTO tbl_scrap_request 
                (user_id, collector_id, request_date, status, pickup_date, pickup_slot) 
                VALUES (?, NULL, NOW(), 'pending', ?, ?)
            ");
            $stmt->bind_param("iss", $user_id, $pickup_date, $pickup_slot);
            if (!$stmt->execute()) throw new Exception('Failed to submit request');

            $request_id = $stmt->insert_id;

            // Insert scrap items
            foreach ($scrap_types as $i => $type_id) {
                $quantity = isset($quantities[$i]) && $quantities[$i] !== '' ? (float)$quantities[$i] : null;

                // Fetch unit
                $stmt_unit = $conn->prepare("SELECT unit FROM tbl_scrap_type WHERE scrap_id = ?");
                $stmt_unit->bind_param("i", $type_id);
                $stmt_unit->execute();
                $unit = $stmt_unit->get_result()->fetch_assoc()['unit'] ?? '';

                if ($quantity === null) {
                    // Insert with NULL for quantity
                    $stmt_item = $conn->prepare("
                        INSERT INTO tbl_scrap_request_item (request_id, scrap_type, quantity, unit)
                        VALUES (?, ?, NULL, ?)
                    ");
                    $stmt_item->bind_param("iis", $request_id, $type_id, $unit);
                } else {
                    // Insert with actual quantity
                    $stmt_item = $conn->prepare("
                        INSERT INTO tbl_scrap_request_item (request_id, scrap_type, quantity, unit)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt_item->bind_param("iids", $request_id, $type_id, $quantity, $unit);
                }

                if (!$stmt_item->execute()) {
                    throw new Exception('Failed to insert scrap items: ' . $stmt_item->error);
                }
            }

            // Handle scrap images
            if (!empty($_FILES['scrap_images']['name'][0])) {
                $targetDir = __DIR__ . "/uploads/";
                if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

                foreach ($_FILES['scrap_images']['tmp_name'] as $j => $tmpName) {
                    if ($_FILES['scrap_images']['error'][$j] === UPLOAD_ERR_OK) {
                        $filename = basename($_FILES['scrap_images']['name'][$j]);
                        $uniqueName = time() . '_' . $filename;
                        $targetFile = $targetDir . $uniqueName;

                        if (move_uploaded_file($tmpName, $targetFile)) {
                            $relativePath = "user/uploads/" . $uniqueName;
                            $stmt_img = $conn->prepare("
                                INSERT INTO tbl_scrap_image (request_id, image_path) VALUES (?, ?)
                            ");
                            $stmt_img->bind_param("is", $request_id, $relativePath);
                            $stmt_img->execute();
                        }
                    }
                }
            }

            echo json_encode(['status' => 'success', 'message' => 'Scrap request submitted successfully']);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

        ob_end_flush();
    }
    break;


    // ------------------- GET PROFILE -------------------
    case 'get_profile':
        $stmt = $conn->prepare("SELECT name, email, phone, address, pincode FROM tbl_user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo json_encode(['status'=>'success','data'=>$row]);
        } else {
            echo json_encode(['status'=>'error','message'=>'User not found']);
        }
        break;

    // ------------------- UPDATE USER PROFILE -------------------
    case 'update_profile':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $phone    = trim($_POST['phone'] ?? '');
            $address  = trim($_POST['address'] ?? '');
            $pincode  = trim($_POST['pincode'] ?? '');

            if (!$username || !$phone) {
                echo json_encode(['status'=>'error','message'=>'Username and phone are required']);
                exit;
            }

            $stmt = $conn->prepare("UPDATE tbl_user SET name=?, phone=?, address=?, pincode=? WHERE user_id=?");
            $stmt->bind_param("ssssi", $username, $phone, $address, $pincode, $user_id);
            if ($stmt->execute()) {
                echo json_encode(['status'=>'success','message'=>'Profile updated successfully']);
            } else {
                echo json_encode(['status'=>'error','message'=>'Failed to update profile']);
            }
        }
        break;

    // ------------------- CHANGE PASSWORD -------------------
    case 'change_password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current = $_POST['currentPassword'] ?? '';
            $new     = $_POST['newPassword'] ?? '';

            if (!$current || !$new) {
                echo json_encode(['status'=>'error','message'=>'All fields are required']);
                exit;
            }

            $stmt = $conn->prepare("SELECT password FROM tbl_user WHERE user_id=?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if (!$row || !password_verify($current, $row['password'])) {
                echo json_encode(['status'=>'error','message'=>'Current password is incorrect']);
                exit;
            }

            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE tbl_user SET password=? WHERE user_id=?");
            $stmt->bind_param("si", $hashed, $user_id);

            if ($stmt->execute()) {
                echo json_encode(['status'=>'success','message'=>'Password changed successfully']);
            } else {
                echo json_encode(['status'=>'error','message'=>'Failed to change password']);
            }
        }
        break;

    // ------------------- NOTIFICATIONS -------------------
    case 'get_notifications':
        if (!isset($user_id)) { echo json_encode([]); break; }

        $stmt = $conn->prepare("
            SELECT notification_id, message, status, created_at 
            FROM tbl_notification 
            WHERE user_id = ? OR target IN ('user','all')
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = [
                'id' => $row['notification_id'],
                'title' => 'Notification',
                'message' => $row['message'],
                'is_read' => ($row['status'] === 'read'),
                'created_at' => $row['created_at'] ? date("M d, Y H:i", strtotime($row['created_at'])) : ''
            ];
        }
        echo json_encode($notifications);
        break;

        case 'get_user_register_date':
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(['status'=>'error','message'=>'Not logged in']);
        exit;
    }

    $stmt = $conn->prepare("SELECT register_date FROM tbl_user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode(['status'=>'success','register_date'=>$result['register_date'] ?? null]);
    exit;

    // ------------------- MARK ALL READ -------------------
    case 'mark_all_read':
        if (!isset($user_id)) { echo json_encode(['success'=>false]); break; }
        $stmt = $conn->prepare("
            UPDATE tbl_notification 
            SET status='read' 
            WHERE user_id=? OR target='user'
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        echo json_encode(['success'=>true]);
        break;

    // ------------------- MARK SINGLE READ -------------------
    case 'mark_read':
        $data = json_decode(file_get_contents("php://input"), true);
        $notification_id = $data['notification_id'] ?? 0;

        if ($notification_id > 0 && isset($user_id)) {
            $stmt = $conn->prepare("
                UPDATE tbl_notification 
                SET status='read' 
                WHERE notification_id=? AND (user_id=? OR target='user')
            ");
            $stmt->bind_param("ii", $notification_id, $user_id);
            $stmt->execute();
        }
        echo json_encode(['success'=>true]);
        break;

case 'get_request_items':
    // Get request_id from GET
    $request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;

    if (!$request_id) {
        echo json_encode([
            "items" => [],
            "collectorName" => null,
            "collectorPhone" => null,
            "error" => "Missing request_id"
        ]);
        exit;
    }

    // Fetch scrap items
    $stmt = $conn->prepare("
        SELECT i.quantity, i.unit, s.scrap_name, COALESCE(p.amount, 0) AS reward
        FROM tbl_scrap_request_item i
        LEFT JOIN tbl_scrap_type s ON i.scrap_type = s.scrap_id
        LEFT JOIN tbl_payment p ON i.request_id = p.request_id
        WHERE i.request_id = ?
    ");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            "name" => $row['scrap_name'],
            "quantity" => $row['quantity'] ?: "N/A",
            "unit" => $row['unit'] ?: "",
            "reward" => $row['reward']
        ];
    }

    // Fetch assigned collector info
    $stmt2 = $conn->prepare("
        SELECT c.name, c.phone
        FROM tbl_scrap_request r
        LEFT JOIN tbl_collector c ON r.collector_id = c.collector_id
        WHERE r.request_id = ?
    ");
    $stmt2->bind_param("i", $request_id);
    $stmt2->execute();
    $collectorResult = $stmt2->get_result();
    $collector = $collectorResult->fetch_assoc();

    echo json_encode([
        "items" => $items,
        "collectorName" => $collector['name'] ?? null,
        "collectorPhone" => $collector['phone'] ?? null
    ]);
    break;


        // ------------------- GET DASHBOARD DATA -------------------
    case 'get_dashboard_data':
    // 1. Get user's UPI ID
    $stmt = $conn->prepare("SELECT upi_id, address FROM tbl_user WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $userRow = $stmt->get_result()->fetch_assoc();
    $upiId = $userRow['upi_id'] ?? null;
    $address = $userRow['address'] ?? '';
    $stmt->close();

    // 2. Get recent transactions
    $stmt = $conn->prepare("
        SELECT p.payment_id, p.request_id, p.amount, p.status, p.payment_date
        FROM tbl_payment p
        WHERE p.user_id = ?
        ORDER BY p.payment_date DESC
        LIMIT 10
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $txResult = $stmt->get_result();
    $transactions = [];
    while ($tx = $txResult->fetch_assoc()) {
        $transactions[] = [
            "payment_id"    => $tx["payment_id"],
            "request_code"  => "REQ" . str_pad($tx["request_id"], 3, "0", STR_PAD_LEFT),
            "amount"        => $tx["amount"],
            "status"        => $tx["status"],
            "date"          => $tx["payment_date"] ? date("M d, Y H:i", strtotime($tx["payment_date"])) : "-"
        ];
    }
    $stmt->close();

    // 3. Scrap requests with items
    $sql = "SELECT r.request_id, r.request_date, r.pickup_date, r.pickup_slot, r.status,
            COALESCE(GROUP_CONCAT(
                CONCAT(
                    IF(i.quantity IS NOT NULL AND i.quantity != '', CONCAT(i.quantity, ' ', i.unit, ' '), ''),
                    s.scrap_name
                ) SEPARATOR '\n'
            ), 'N/A') AS scrap_list,
            COALESCE(p.amount,0) AS amount
            FROM tbl_scrap_request r
            LEFT JOIN tbl_scrap_request_item i ON r.request_id = i.request_id
            LEFT JOIN tbl_scrap_type s ON i.scrap_type = s.scrap_id
            LEFT JOIN tbl_payment p ON r.request_id = p.request_id
            WHERE r.user_id = ?
            GROUP BY r.request_id
            ORDER BY r.request_date DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = [
            "request_id"  => $row["request_id"],
            "formattedId" => "REQ" . str_pad($row["request_id"], 3, "0", STR_PAD_LEFT), 
            "created_at"  => $row["request_date"] ? date("M d, Y H:i", strtotime($row["request_date"])) : "-",
            "scrap_name"  => $row["scrap_list"],
            "status"      => $row["status"],
            "reward"      => $row["amount"],
            "pickup_date" => $row["pickup_date"] ? date("M d, Y", strtotime($row["pickup_date"])) : "-",
            "pickup_slot" => $row["pickup_slot"] ?: "-",
            "address"     => $address
        ];
    }
    $stmt->close();

    echo json_encode([
        "upi_id"       => $upiId,
        "transactions" => $transactions,
        "requests"     => $requests
    ]);
    break;

case 'submit_review':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $request_id = $_POST['request_id'] ?? null;
        $rating = $_POST['rating'] ?? null;
        $message = $_POST['message'] ?? '';
        if (!$request_id || !$rating) {
            echo json_encode(['status'=>'error','message'=>'Missing required fields']);
            exit;
        }

        // Assume $user_id comes from session:
        session_start();
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            echo json_encode(['status'=>'error','message'=>'Not logged in']);
            exit;
        }

        require_once '../db.php';
        $stmt = $conn->prepare("INSERT INTO tbl_feedback (user_id, request_id, rating, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiis", $user_id, $request_id, $rating, $message);

        if ($stmt->execute()) {
            echo json_encode(['status'=>'success','message'=>'Feedback submitted']);
        } else {
            echo json_encode(['status'=>'error','message'=>'Failed to submit feedback']);
        }
    } else {
        echo "No POST request";
    }
    break;

    case 'save_upi':
    // Get the UPI ID from POST
    $upi_id = trim($_POST['upi_id'] ?? '');
    
    // Validate UPI format (basic check)
    if (!preg_match("/^[\w.\-]+@[\w]+$/", $upi_id)) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid UPI ID format."
        ]);
        exit;
    }

    // Update UPI ID in database
    $stmt = $conn->prepare("UPDATE tbl_user SET upi_id=? WHERE user_id=?");
    $stmt->bind_param("si", $upi_id, $user_id);
    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "UPI ID saved successfully!"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to save UPI ID."
        ]);
    }
    $stmt->close();
    break;

}
