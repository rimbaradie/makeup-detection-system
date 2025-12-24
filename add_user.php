<?php
header('Content-Type: application/json');
require 'connect.php';

if (
    isset($_POST['username']) &&
    isset($_POST['email']) &&
    isset($_POST['password']) &&
    isset($_POST['role']) &&
    isset($_POST['skin_type']) &&
    isset($_POST['fname']) &&
    isset($_POST['lname'])
) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $skin_type = $_POST['skin_type'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];

    // ✅ Generate unique_id
    $unique_id = rand(1000000000, 2147483647);

    $profileName = null;
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'pic/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg','jpeg','png','gif'];

        if (!in_array($ext, $allowedExts)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type']);
            exit;
        }

        $profileName = uniqid('profile_', true) . '.' . $ext;
        $targetPath = $uploadDir . $profileName;

        if (!move_uploaded_file($_FILES['profile']['tmp_name'], $targetPath)) {
            echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
            exit;
        }
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO Users (unique_id, username, email, password, role, skin_type, fname, lname, profile)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $success = $stmt->execute([
            $unique_id, $username, $email, $password, $role,
            $skin_type, $fname, $lname, $profileName
        ]);

        if ($success) {
            echo json_encode(['status' => 'success']);
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['status' => 'error', 'message' => 'SQL error: ' . $errorInfo[2]]);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
}
