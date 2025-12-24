<?php

header('Content-Type: application/json');
session_start();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error',
                      'message' => 'Access denied: POST only']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error',
                      'message' => 'User not logged in.']);
    exit;
}
$user_id = (int)$_SESSION['user_id'];

/*---------------------------------------------------------------*/
/* 2. الاتصال بقاعدة البيانات                                    */
/*---------------------------------------------------------------*/
$servername   = "localhost";
$username_db  = "root";
$password_db  = "";
$dbname       = "SkinGlam";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    echo json_encode(['status'  => 'error',
                      'message' => 'Database connection failed: ' .
                                   $conn->connect_error]);
    exit;
}
$conn->set_charset('utf8mb4');   // دعم يونيكود

/*---------------------------------------------------------------*/
/* 3. قراءة وإسناد مدخلات النموذج                                */
/*---------------------------------------------------------------*/
$fname     = trim($_POST['fname']     ?? '');
$lname     = trim($_POST['lname']     ?? '');
$username  = trim($_POST['username']  ?? '');
$email     = trim($_POST['email']     ?? '');
$skin_type = trim($_POST['skin_type'] ?? '');

if ($fname === '' || $lname === '' || $username === '' ||
    $email === '' || $skin_type === '') {
    echo json_encode(['status'  => 'error',
                      'message' => 'Please fill out all required fields.']);
    exit;
}

/*---------------------------------------------------------------*/
/* 4. رفع صورة البروفايل (اختياري)                               */
/*---------------------------------------------------------------*/
$profilePath = '';        // المسار الذي سيُخزّن (إن وُجد)
if (isset($_FILES['profile']) && $_FILES['profile']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['profile']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status'  => 'error',
                          'message' => 'Upload error code: ' .
                                       $_FILES['profile']['error']]);
        exit;
    }

    // التحقق من الامتداد
    $allowed = ['jpg','jpeg','png','gif','webp'];
    $ext = strtolower(pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        echo json_encode(['status'  => 'error',
                          'message' => 'Invalid image type.']);
        exit;
    }

    // مجلد pic/
    $targetDir = __DIR__ . DIRECTORY_SEPARATOR . 'pic' . DIRECTORY_SEPARATOR;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // اسم ملف فريد
    $fileName = 'user_' . $user_id . '_' . time() . '.' . $ext;
    $destination = $targetDir . $fileName;

    if (!move_uploaded_file($_FILES['profile']['tmp_name'], $destination)) {
        echo json_encode(['status'  => 'error',
                          'message' => 'Failed to move uploaded file.']);
        exit;
    }

    // المسار الذي سيُخزَّن في قاعدة البيانات (نسبي للمشروع)
  $profilePath = $fileName;  // فقط الاسم بدون مجلد

}

/*---------------------------------------------------------------*/
/* 5. تنفيذ التحديث                                              */
/*---------------------------------------------------------------*/
try {
    // نص SQL مرن: نضيف عمود profile فقط إذا رُفع ملف
    if ($profilePath !== '') {
        $sql = "UPDATE Users
                   SET fname      = ?,
                       lname      = ?,
                       username   = ?,
                       email      = ?,
                       skin_type  = ?,
                       profile    = ?
                 WHERE user_id   = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssi',
                          $fname, $lname, $username,
                          $email, $skin_type, $profilePath,
                          $user_id);
    } else {
        $sql = "UPDATE Users
                   SET fname      = ?,
                       lname      = ?,
                       username   = ?,
                       email      = ?,
                       skin_type  = ?
                 WHERE user_id   = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssi',
                          $fname, $lname, $username,
                          $email, $skin_type,
                          $user_id);
    }

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status'  => 'success',
                          'message' => 'Settings updated successfully.']);
    } else {
        // صفر صفوف متأثرة → إما لا تغيير أو user_id غير موجود
        echo json_encode(['status'  => 'warning',
                          'message' => 'No changes made.']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status'  => 'error',
                      'message' => 'Update failed: ' . $e->getMessage()]);
}

$conn->close();
?>
