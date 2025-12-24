<?php

include("connect.php");  // تأكد أن $conn هو كائن PDO

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->user_id)) {
    echo json_encode(["success" => false, "message" => "Missing or invalid user_id"]);
    exit;
}

$user_id = intval($data->user_id);

$query = "SELECT fname, lname, username, email, skin_type, profile FROM users WHERE user_id = :user_id";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Prepare failed"]);
    exit;
}

$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // تعديل: بناء رابط الصورة بدلاً من إرجاع اسمها فقط
    $imageName = $user['profile'];
    if (!empty($imageName)) {
        // الرابط حسب السيرفر واسم مجلد الصور
        $imageUrl = "http://192.168.1.108/SkinGlamProject/pic/" . $imageName;
    } else {
        $imageUrl = ""; // أو رابط صورة افتراضية
    }
    // إضافة الرابط إلى مصفوفة المستخدم
    $user['profile_url'] = $imageUrl;

    echo json_encode(["success" => true, "user" => $user]);
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}

$conn = null;
?>
