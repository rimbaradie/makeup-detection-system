<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

try {
    $conn = new PDO("mysql:host=localhost;dbname=SkinGlam", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
    exit;
}

// للتأكد من وصول الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "This endpoint accepts POST requests only"]);
    exit;
}

// طباعة كل بيانات POST الواردة (للتجربة فقط، بعد التأكد امسحها)
if (empty($_POST)) {
    echo json_encode(["success" => false, "message" => "No POST data received"]);
    exit;
}
// echo json_encode(["success" => true, "received_data" => $_POST]);
// exit;

if (!isset($_POST['user_id'], $_POST['old_password'], $_POST['new_password'])) {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit;
}

$user_id = intval($_POST['user_id']);
$old_password = $_POST['old_password'];
$new_password = $_POST['new_password'];

try {
    // جلب كلمة السر المشفرة من قاعدة البيانات
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(["success" => false, "message" => "User not found"]);
        exit;
    }

    $hashed_password_db = $result['password'];

    // تحقق من كلمة السر القديمة
    if (!password_verify($old_password, $hashed_password_db)) {
        echo json_encode(["success" => false, "message" => "Incorrect old password"]);
        exit;
    }

    // تشفير كلمة السر الجديدة
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // تحديث كلمة السر
    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    if ($update_stmt->execute([$new_hashed_password, $user_id])) {
        echo json_encode(["success" => true, "message" => "Password updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update password"]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    exit;
}
?>
