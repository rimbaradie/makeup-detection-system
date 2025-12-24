<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require_once 'connect.php';   // عدّل المسار إذا لزم الأمر

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status"=>"error","message"=>"Invalid method"]);
    exit;
}

/* ✦ استقبل JSON أو x-www-form-urlencoded */
$data = json_decode(file_get_contents('php://input'), true);
if ($data) { $_POST = $data; }      // لو كانت JSON انسخها إلى $_POST

$user_id  = $_POST['user_id']  ?? '';
$fname    = $_POST['fname']    ?? '';
$lname    = $_POST['lname']    ?? '';
$username = $_POST['username'] ?? '';
$email    = $_POST['email']    ?? '';
$password = $_POST['password'] ?? '';
$skin     = $_POST['skin_type']?? '';
$profile = $_POST['profile'] ?? '';

if (!empty($profile)) {
    $imgData = base64_decode($profile);
    $imgPath = "uploads/profile_" . $user_id . ".jpg";
    file_put_contents($imgPath, $imgData);

    // ثم خزّن المسار في DB بدل base64
    $profile = $imgPath;
}


if (!$user_id || !$fname || !$lname || !$username || !$email || !$password || !$skin) {
    echo json_encode(["status"=>"error","message"=>"Missing fields"]);
    exit;
}

/* ✦ تشفير كلمة المرور */
$hashed = password_hash($password, PASSWORD_DEFAULT);

/* ✦ تحضير واستدعاء UPDATE */
$sql = "UPDATE Users SET
            fname     = ?,
            lname     = ?,
            username  = ?,
            email     = ?,
            password  = ?,
            skin_type = ?,
            profile   = ?
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status"=>"error","message"=>"Query prepare failed"]);
    exit;
}

$stmt->bind_param("sssssssi",
        $fname, $lname, $username, $email,
        $hashed, $skin, $profile, $user_id);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Profile updated"]);
} else {
    echo json_encode(["status"=>"error","message"=>"DB update failed"]);
}
$stmt->close();
$conn->close();
