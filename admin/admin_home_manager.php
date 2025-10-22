<?php
// ------------------- SESSION & LOGIN CHECK -------------------
require_once(__DIR__ . '/../session.php');
// custom helper for admin authentication

// ------------------- DATABASE CONNECTION -------------------
require_once(__DIR__ . '/../db.php');

// ------------------- HANDLE AJAX REQUEST -------------------
$action = $_GET['action'] ?? $_POST['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {

    // ------------------- GET ALL USERS -------------------
     case 'get_users':
        header('Content-Type: application/json; charset=utf-8');

        $sql = "SELECT user_id, name, email, phone, address, pincode, status 
                FROM tbl_user ORDER BY user_id DESC";
        $result = $conn->query($sql);

        if (!$result) {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
            exit;
        }

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = [
                "id"      => (int)$row['user_id'],
                "name"    => $row['name'],
                "email"   => $row['email'],
                "phone"   => $row['phone'],
                "address" => $row['address'],
                "pincode" => $row['pincode'],
                "status"  => $row['status'] === 'active' ? 'Active' : 'Blocked',
                "avatar"  => null
            ];
        }

        echo json_encode(['status' => 'success', 'data' => $users]);
        exit;

    // ------------------- TOGGLE USER STATUS -------------------
    case 'toggle_status':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
                exit;
            }

            $stmt = $conn->prepare("SELECT status FROM tbl_user WHERE user_id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            if (!$res) {
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
                exit;
            }
            $newStatus = $res['status'] === 'active' ? 'blocked' : 'active';

            $stmt = $conn->prepare("UPDATE tbl_user SET status=? WHERE user_id=?");
            $stmt->bind_param("si", $newStatus, $id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => "User status updated", 'newStatus' => ucfirst($newStatus)]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
        }}
        break;

    // ------------------- SEND NOTIFICATION -------------------
    case 'notify_user':
        header('Content-Type: application/json'); // Always return JSON

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $message = trim($_POST['message'] ?? "You have a new notification");

            if ($id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
                exit;
            }

            // Make sure $conn exists (include db.php at the top of this file)
            $stmt = $conn->prepare("INSERT INTO tbl_notification (user_id, message, status, created_at) VALUES (?, ?, 'unread', NOW())");
            $stmt->bind_param("is", $id, $message);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Notification sent']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send notification']);
            }

            $stmt->close();
            exit; // prevent falling through
        }

    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
   // ------------------- VIEW USER DETAILS -------------------
    case 'get_user_details':
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
            exit;
        }
        $stmt = $conn->prepare("SELECT user_id, name, email, phone, address, pincode, status FROM tbl_user WHERE user_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            echo json_encode([
                'status' => 'success',
                'data'   => [
                    "id"      => (int)$row['user_id'],
                    "name"    => $row['name'],
                    "email"   => $row['email'],
                    "phone"   => $row['phone'],
                    "address" => $row['address'],
                    "pincode" => $row['pincode'],
                    "status"  => ucfirst($row['status'])
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
        break;

    case 'add_user':
    header('Content-Type: application/json'); // always return JSON

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        exit;
    }

    // Collect POST data safely
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $pincode  = trim($_POST['pincode'] ?? '');

    // Basic validation
    if (!$name || !$email || !$phone || !$password || !$address || !$pincode) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Set register date and default status
    $register_date = date('Y-m-d H:i:s');
    $status = 'active';

    // Prepare SQL
    $stmt = $conn->prepare("INSERT INTO tbl_user (name, email, phone, password, address, pincode, register_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $email, $phone, $passwordHash, $address, $pincode, $register_date, $status);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'User added successfully', 'user_id' => $stmt->insert_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add user: ' . $stmt->error]);
    }

    $stmt->close();
    exit;

// ------------------- GET ALL COLLECTORS -------------------
case 'get_collectors':
    $sql = "SELECT c.collector_id, c.name, c.email, 
                   GROUP_CONCAT(a.pincode SEPARATOR ', ') AS area
            FROM tbl_collector c
            LEFT JOIN tbl_collector_area a ON c.collector_id = a.collector_id
            GROUP BY c.collector_id
            ORDER BY c.collector_id DESC";

    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
        exit;
    }

    $collectors = [];
    while ($row = $result->fetch_assoc()) {
        $collectors[] = [
            "id"    => str_pad($row['collector_id'], 3, "0", STR_PAD_LEFT),
            "name"  => $row['name'],
            "email" => $row['email'],
            "area"  => $row['area'] ?? "N/A"
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $collectors]);
    break;

// ------------------- ADD COLLECTOR -------------------
case 'add_collector':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');

        // --- Get POST data ---
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pincodes = trim($_POST['area'] ?? '');

        // Convert comma-separated pincodes to array
        $pincodes = array_map('trim', explode(',', $pincodes));
        $pincodes = array_filter($pincodes); // remove empty values

        // --- Validate ---
        if ($name === '' || $email === '' || empty($pincodes)) {
            echo json_encode(['status'=>'error','message'=>'All fields are required']);
            exit;
        }

        // --- Check email uniqueness ---
        $checkSql = "
            SELECT email FROM tbl_user WHERE email=? 
            UNION
            SELECT email FROM tbl_collector WHERE email=? 
            UNION
            SELECT email FROM tbl_admin WHERE email=?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("sss", $email, $email, $email);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            echo json_encode(['status'=>'error','message'=>'Email already exists!']);
            exit;
        }

        // --- Generate password ---
        $tempPassword = bin2hex(random_bytes(4));
        $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

        // --- Insert collector ---
        $stmt = $conn->prepare("INSERT INTO tbl_collector (name,email,password,status,created_at) VALUES (?,?,?,'first-time',NOW())");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if (!$stmt->execute()) {
            echo json_encode(['status'=>'error','message'=>'Failed to add collector']);
            exit;
        }

        $collectorId = $conn->insert_id;

        // --- Insert pincodes ---
        if (!empty($pincodes)) {
            $stmt2 = $conn->prepare("INSERT INTO tbl_collector_area (collector_id,pincode) VALUES (?,?)");
            foreach ($pincodes as $pin) {
                if ($pin !== '') {
                    $stmt2->bind_param("is", $collectorId, $pin);
                    $stmt2->execute();
                }
            }
        }

        // --- Send Email ---
        try {
            require __DIR__ . '/../phpmailer/src/Exception.php';
            require __DIR__ . '/../phpmailer/src/PHPMailer.php';
            require __DIR__ . '/../phpmailer/src/SMTP.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ananthus952@gmail.com';
            $mail->Password = 'rdchqkttrvfbkrno';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('ananthus952@gmail.com', 'EcoCycle');
            $mail->addAddress($email, $name);
            $mail->isHTML(false);
            $mail->Subject = "EcoCycle Collector Registration";
            $mail->Body = "Hi $name,\nLogin Email: $email\nTemporary Password: $tempPassword\nPlease log in and change your password.";

            $mail->send();
            $mailSent = true;
        } catch (Exception $e) {
            $mailSent = false;
            $mailMessage = $e->getMessage();
        }

        // --- Return JSON ---
        if ($mailSent) {
            echo json_encode(['status'=>'success','message'=>"Collector added successfully! Email sent to $email"]);
        } else {
            echo json_encode([
                'status'=>'warning',
                'message'=>"Collector added, but failed to send email. Error: $mailMessage"
            ]);
        }

        exit;
    }
    break;
// ------------------- UPDATE COLLECTOR -------------------
case 'update_collector':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pincode = trim($_POST['area'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid collector ID']);
            exit;
        }

        if ($password !== '') {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE tbl_collector SET name=?, email=?, password=? WHERE collector_id=?");
            $stmt->bind_param("sssi", $name, $email, $hashedPassword, $id);
        } else {
            $stmt = $conn->prepare("UPDATE tbl_collector SET name=?, email=? WHERE collector_id=?");
            $stmt->bind_param("ssi", $name, $email, $id);
        }

        if ($stmt->execute()) {
            // Update pincode in tbl_collector_area
            $stmt2 = $conn->prepare("UPDATE tbl_collector_area SET pincode=? WHERE collector_id=?");
            $stmt2->bind_param("si", $pincode, $id);
            $stmt2->execute();

            echo json_encode(['status' => 'success', 'message' => 'Collector updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update collector']);
        }
    }
    break;

    case 'choose_collectors':
    $sql = "SELECT collector_id, name FROM tbl_collector ORDER BY name ASC";
    $result = $conn->query($sql);

    $collectors = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $collectors[] = $row;
        }
    }

    echo json_encode($collectors);
    break;
            // ------------------- FETCH SCRAP REQUESTS -------------------
    case 'fetch_requests':
    $sql = "SELECT r.request_id AS id, r.request_date AS date, u.name AS user, 
                   u.phone, u.email, c.name AS collector, r.status, u.address,
                   r.pickup_date, r.pickup_slot
            FROM tbl_scrap_request r
            JOIN tbl_user u ON r.user_id = u.user_id
            LEFT JOIN tbl_collector c ON r.collector_id = c.collector_id
            ORDER BY r.request_date DESC";

    $result = $conn->query($sql);
    $requests = [];

    while ($row = $result->fetch_assoc()) {
        $requestId = (int)$row['id'];

        // --- Items with collector_weight and estimated_value ---
        $items = [];
        $itemRes = $conn->query("
            SELECT t.scrap_name, i.quantity, i.collector_weight, i.unit, t.price_per_unit
            FROM tbl_scrap_request_item i
            JOIN tbl_scrap_type t ON i.scrap_type = t.scrap_id
            WHERE i.request_id = $requestId
        ");
        while ($item = $itemRes->fetch_assoc()) {
            $weight = $item['collector_weight'] ?? $item['quantity'];
            $item['estimated_weight'] = $weight;
            $item['estimated_value']  = $weight * $item['price_per_unit'];
            $items[] = $item;
        }
        $row['items'] = $items;

        // --- Images ---
        $images = [];
        $imgRes = $conn->query("SELECT image_path FROM tbl_scrap_image WHERE request_id=$requestId");
        while ($img = $imgRes->fetch_assoc()) $images[] = $img['image_path'];
        $row['images'] = $images;

        $requests[] = $row;
    }

    echo json_encode(['status'=>'success', 'data'=>$requests]);
    break;

    // ---------------- Update request status ----------------
    case "update":
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$id || !$status) {
            echo json_encode(["success"=>false,"error"=>"Missing parameters"]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE tbl_scrap_request SET status=? WHERE request_id=?");
        $stmt->bind_param("si",$status,$id);
        $success = $stmt->execute();

        echo json_encode(['status'=>$success?'success':'error','message'=>$success?'Status updated':'Failed to update status']);
        break;

    // ---------------- Reassign collector ----------------
    case "reassign":
        $id = $_POST['id'] ?? null;
        $collectorId = $_POST['collector_id'] ?? null;

        if(!$id || !$collectorId){
            echo json_encode(["success"=>false,"error"=>"Missing parameters"]); 
            exit;
        }

        $stmt = $conn->prepare("UPDATE tbl_scrap_request SET collector_id=? WHERE request_id=?");
        $stmt->bind_param("ii",$collectorId,$id);
        $success = $stmt->execute();

        if($success){
            $stmt2 = $conn->prepare("SELECT name FROM tbl_collector WHERE collector_id=?");
            $stmt2->bind_param("i",$collectorId);
            $stmt2->execute();
            $res = $stmt2->get_result();
            $collector = $res->fetch_assoc();

            echo json_encode(['status'=>'success','data'=>["collector_id"=>$collectorId,"collector_name"=>$collector['name']??'Unknown']]);
        } else {
            echo json_encode(['status'=>'error','message'=>'Failed to update collector']);
        }
        break;

    // ------------------- FETCH SCRAP TYPES / CATEGORIES -------------------
// --- Get scrap types ---
case "get_scrap_types":
    $sql = "SELECT scrap_id, scrap_name, unit, price_per_unit 
            FROM tbl_scrap_type ORDER BY scrap_name ASC";
    $result = mysqli_query($conn, $sql);

    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = [
            "id"    => (int)$row['scrap_id'],
            "name"  => $row['scrap_name'],
            "unit"  => $row['unit'],
            "price" => (float)$row['price_per_unit']
        ];
    }

    echo json_encode(["status" => "success", "data" => $categories]);
    break;

// --- Update price ---
case "updatePrice":
    $id    = intval($_POST['id'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);

    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid scrap id"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE tbl_scrap_type 
                            SET price_per_unit = ? 
                            WHERE scrap_id = ?");
    $stmt->bind_param("di", $price, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Price updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    break;

// --- Delete scrap type ---
case "delete":
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid id"]);
        exit;
    }
    $stmt = $conn->prepare("DELETE FROM tbl_scrap_type WHERE scrap_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Scrap type deleted"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    break;

// --- Add new scrap type ---
case "add":
    $name  = trim($_POST['name'] ?? '');
    $unit  = trim($_POST['unit'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    if ($name === '' || $unit === '') {
        echo json_encode(["status" => "error", "message" => "Name and unit are required"]);
        exit;
    }

    // Prevent duplicates
    $checkStmt = $conn->prepare("SELECT scrap_id FROM tbl_scrap_type WHERE LOWER(scrap_name) = LOWER(?)");
    $checkStmt->bind_param("s", $name);
    $checkStmt->execute();
    $checkRes = $checkStmt->get_result();
    if ($checkRes && $checkRes->num_rows > 0) {
        echo json_encode(["status" => "warning", "message" => "Scrap type already exists"]);
        exit;
    }

    // Insert new scrap type
    $stmt = $conn->prepare("INSERT INTO tbl_scrap_type (scrap_name, unit, price_per_unit) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $unit, $price);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Scrap type added", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    break;
// ------------------- FETCH ALL NOTIFICATIONS -------------------
case 'get_notifications':
    $sql = "SELECT notification_id, user_id, collector_id, message, status, DATE(created_at) AS date, target
            FROM tbl_notification
            ORDER BY notification_id DESC";
    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
        exit;
    }

    $notifications = [];

    while ($row = $result->fetch_assoc()) {
        // Determine display target based on IDs and target type
        if ($row['target'] === 'user') {
            $displayTarget = $row['user_id'] ? "User #{$row['user_id']}" : "All Users";
        } elseif ($row['target'] === 'collector') {
            $displayTarget = $row['collector_id'] ? "Collector #{$row['collector_id']}" : "All Collectors";
        } elseif ($row['target'] === 'all') {
            if (is_null($row['user_id']) && is_null($row['collector_id'])) {
                $displayTarget = "All Users & Collectors"; // proper broadcast
            } elseif (!is_null($row['user_id'])) {
                $displayTarget = "User #{$row['user_id']}";
            } elseif (!is_null($row['collector_id'])) {
                $displayTarget = "Collector #{$row['collector_id']}";
            } else {
                $displayTarget = "All"; // fallback
            }
        } else {
            $displayTarget = "Unknown";
        }

        $notifications[] = [
            "id" => (int)$row['notification_id'],
            "message" => $row['message'],
            "target" => $row['target'], // raw target for logic
            "user_id" => $row['user_id'],
            "collector_id" => $row['collector_id'],
            "display_target" => $displayTarget, // for frontend display
            "status" => $row['status'],
            "date" => $row['date']
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $notifications]);
    break;

// ------------------- CREATE NOTIFICATION -------------------
    case 'create_notification':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = trim($_POST['message'] ?? '');
            $target = trim($_POST['target'] ?? 'all');
            $id = intval($_POST['id'] ?? 0); // id of user or collector if sending single

            if ($message === '') {
                echo json_encode(['status' => 'error', 'message' => 'Message is required']);
                exit;
            }

            // Default nulls
            $user_id = null;
            $collector_id = null;

            if ($target === 'user' && $id > 0) {
                $user_id = $id;
            } elseif ($target === 'collector' && $id > 0) {
                $collector_id = $id;
            } elseif ($target === 'all') {
                $user_id = null;
                $collector_id = null;
            }

            $stmt = $conn->prepare("INSERT INTO tbl_notification (user_id, collector_id, message, status, created_at, target)
                                    VALUES (?, ?, ?, 'unread', NOW(), ?)");
            $stmt->bind_param("iiss", $user_id, $collector_id, $message, $target);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'id' => $conn->insert_id]);
            } else {
                echo json_encode(['status' => 'error', 'message' => $stmt->error]);
            }
            $stmt->close();
        }
        break;

// ------------------- FETCH ALL FEEDBACK -------------------
// ------------------- GET FEEDBACKS -------------------
case 'get_feedbacks':
    header('Content-Type: application/json; charset=utf-8');

    $sql = "SELECT f.feedback_id, f.user_id, u.name AS user_name, f.request_id, 
                   f.message, f.rating, f.created_at, 
                   COALESCE(f.replied, 0) AS replied, 
                   COALESCE(f.reply_message, '') AS reply_message
            FROM tbl_feedback f
            JOIN tbl_user u ON f.user_id = u.user_id
            ORDER BY f.created_at DESC";

    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
        exit;
    }

    $feedbacks = [];
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = [
            "id"      => (int)$row['feedback_id'],
            "user"    => $row['user_name'],
            "message" => $row['message'],
            "rating"  => (int)$row['rating'],
            "date"    => $row['created_at'],
            "replied" => (bool)$row['replied'],
            "reply"   => $row['reply_message']
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $feedbacks]);
    exit;

// ------------------- REPLY TO FEEDBACK -------------------
case 'reply_feedback':
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        $reply = trim($_POST['reply'] ?? '');

        if ($id <= 0 || $reply === '') {
            echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE tbl_feedback 
                                SET replied = 1, reply_message = ? 
                                WHERE feedback_id = ?");
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
            exit;
        }

        $stmt->bind_param("si", $reply, $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Reply saved successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
    // ------------------- CHANGE ADMIN PASSWORD -------------------
    case 'change_password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            $adminId = $_SESSION['admin_id'] ?? 0;

            $currentPassword = trim($_POST['currentPassword'] ?? '');
            $newPassword     = trim($_POST['newPassword'] ?? '');

            if (!$adminId || !$currentPassword || !$newPassword) {
                echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
                exit;
            }

            // Fetch current password hash from DB
            $stmt = $conn->prepare("SELECT password FROM tbl_admin WHERE admin_id=?");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if (!$res) {
                echo json_encode(['status' => 'error', 'message' => 'Admin not found']);
                exit;
            }

            // Verify current password
            if (!password_verify($currentPassword, $res['password'])) {
                echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
                exit;
            }

            // Hash new password
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password in DB
            $stmt = $conn->prepare("UPDATE tbl_admin SET password=? WHERE admin_id=?");
            $stmt->bind_param("si", $newHash, $adminId);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Password changed successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
            }
        }
        break;

    // ------------------- FETCH ALL PAYMENTS -------------------
case 'get_payments':
    $sql = "SELECT 
                r.request_id,
                r.user_id,
                u.name AS user,
                u.email,
                u.upi_id,
                GROUP_CONCAT(t.scrap_name SEPARATOR ', ') AS scrapType,
                COALESCE(SUM(i.quantity), 0) AS totalWeight,
                COALESCE(SUM(COALESCE(i.quantity, 0) * COALESCE(t.price_per_unit, 0)), 0) AS amount,
                COALESCE(p.status, 'Pending') AS status,
                r.request_date AS date,
                r.pickup_date AS collectionDate,
                p.payment_id AS id
            FROM tbl_scrap_request r
            JOIN tbl_user u ON r.user_id = u.user_id
            JOIN tbl_scrap_request_item i ON r.request_id = i.request_id
            JOIN tbl_scrap_type t ON i.scrap_type = t.scrap_id
            LEFT JOIN tbl_payment p ON r.request_id = p.request_id
            WHERE r.status = 'Collected'
            GROUP BY r.request_id
            ORDER BY r.pickup_date DESC";

    $result = $conn->query($sql);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
        exit;
    }

    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $weight = floatval($row['totalWeight']);
        $amount = floatval($row['amount']);
        $rate   = ($weight > 0) ? round($amount / $weight, 2) : 0.00;
        $payment_id = $row['id'];

        // ✅ If no payment record exists yet, insert one
        if (!$payment_id && $amount > 0) {
            $insert = $conn->prepare("
                INSERT INTO tbl_payment (request_id, user_id, amount, status)
                VALUES (?, ?, ?, 'Pending')
            ");
            $insert->bind_param("iid", $row['request_id'], $row['user_id'], $amount);
            if ($insert->execute()) {
                $payment_id = $conn->insert_id;
            }
        }

        $payments[] = [
            "payment_id"     => $payment_id,
            "request_id"     => $row['request_id'],
            "userId"         => (int)$row['user_id'],
            "user"           => $row['user'],
            "email"          => $row['email'],
            "upi_id"         => $row['upi_id'],
            "scrapType"      => $row['scrapType'],
            "weight"         => $weight,
            "amount"         => $amount,
            "status"         => ucfirst($row['status']),
            "date"           => $row['date'],
            "collectionDate" => $row['collectionDate'],
            "rate"           => $rate
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $payments]);
    break;

// ------------------- GET SINGLE PAYMENT DETAILS -------------------
case 'get_payment_details':
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid payment ID']);
        exit;
    }

    $stmt = $conn->prepare("SELECT 
                                p.payment_id AS id,
                                p.request_id,
                                p.user_id,
                                u.name AS user,
                                u.email,
                                r.scrap_name AS scrapType,
                                r.quantity AS weight,
                                p.amount,
                                p.status,
                                p.payment_date AS date,
                                r.pickup_date AS collectionDate
                            FROM tbl_payment p
                            JOIN tbl_scrap_request r ON p.request_id = r.request_id
                            JOIN tbl_user u ON p.user_id = u.user_id
                            WHERE p.payment_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if ($res) {
        $weight = floatval($res['weight']);
        $res['status'] = ucfirst($res['status']);
        $res['rate'] = ($weight > 0) ? round(floatval($res['amount']) / $weight, 2) : 0.00;
        echo json_encode(['status' => 'success', 'data' => $res]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Payment not found']);
    }
    break;

    case 'prepare_razorpay':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['payment_id'] ?? 0);
        if($id <= 0){
            echo json_encode(['status'=>'error','message'=>'Invalid payment ID']);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT 
                p.payment_id,
                p.amount,
                u.user_id,
                u.name,
                u.email,
                u.upi_id,   -- ✅ include UPI ID
                r.request_id,
                GROUP_CONCAT(t.scrap_name SEPARATOR ', ') AS scrapType,
                SUM(i.quantity) AS weight
            FROM tbl_payment p
            JOIN tbl_user u ON p.user_id = u.user_id
            JOIN tbl_scrap_request r ON p.request_id = r.request_id
            JOIN tbl_scrap_request_item i ON r.request_id = i.request_id
            JOIN tbl_scrap_type t ON i.scrap_type = t.scrap_id
            WHERE p.payment_id=?
            GROUP BY p.payment_id
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if(!$res){
            echo json_encode(['status'=>'error','message'=>'Payment not found']);
            exit;
        }

        echo json_encode(['status'=>'success','data'=>$res]);
    }
    break;

        case 'razorpay_payment':
    $id = intval($_POST['payment_id'] ?? 0);
    $razorpay_payment_id = $_POST['razorpay_payment_id'] ?? '';

    if ($id > 0 && !empty($razorpay_payment_id)) {

        // ✅ Update tbl_payment
        $stmt = $conn->prepare("
            UPDATE tbl_payment 
            SET status = 'earned',
                payment_date = NOW()
            WHERE payment_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // ✅ Also update tbl_scrap_request (linked to same request)
        $stmt2 = $conn->prepare("
            UPDATE tbl_scrap_request 
            SET status = 'earned'
            WHERE request_id = (
                SELECT request_id FROM tbl_payment WHERE payment_id = ?
            )
        ");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Payment marked as Earned']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Payment update failed']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid payment data']);
    }
    break;

    case 'get_payment_history':
    $sql = "
        SELECT 
            p.payment_id,
            p.amount,
            p.status,
            p.payment_date,
            u.name,
            u.email,
            r.request_id,
            GROUP_CONCAT(t.scrap_name SEPARATOR ', ') AS scrapType,
            SUM(i.quantity) AS weight
        FROM tbl_payment p
        JOIN tbl_user u ON p.user_id = u.user_id
        JOIN tbl_scrap_request r ON p.request_id = r.request_id
        JOIN tbl_scrap_request_item i ON r.request_id = i.request_id
        JOIN tbl_scrap_type t ON i.scrap_type = t.scrap_id
        GROUP BY p.payment_id
        ORDER BY p.payment_date DESC
    ";

    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $rows]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No payments found']);
    }
    break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

