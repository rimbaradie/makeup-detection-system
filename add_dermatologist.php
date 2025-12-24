<?php
header('Content-Type: application/json');
require 'connect.php';

if (
    !empty($_POST['fname']) &&
    !empty($_POST['lname']) &&
    !empty($_POST['username']) &&
    !empty($_POST['password']) &&
    !empty($_POST['phone_number']) &&
    !empty($_POST['specialty']) &&
    !empty($_POST['email']) &&
    !empty($_POST['location']) &&
    !empty($_POST['next_available']) &&
    !empty($_POST['status'])
) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $phone_number = $_POST['phone_number'];
    $specialty = $_POST['specialty'];
    $email = $_POST['email'];
    $location = $_POST['location'];
    $next_available = $_POST['next_available'];
    $status = $_POST['status'];
    $role = 'Dermatologist';

    // Generate unique ID
    $unique_id = rand(1000000000, 2147483647);

    // Profile image upload
    $uploadDir = 'pic/';
    $newFileName = null;

    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $profile = $_FILES['profile'];
        $fileName = basename($profile['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExt, $allowedExts)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid image file type.']);
            exit;
        }

        $newFileName = uniqid('profile_', true) . '.' . $fileExt;
        $targetFilePath = $uploadDir . $newFileName;

        if (!move_uploaded_file($profile['tmp_name'], $targetFilePath)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload profile image.']);
            exit;
        }
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO Users (
                fname, lname, username, password, phone_number, specialty, email, location,
                profile, next_available, role, status, unique_id, created_at
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $fname, $lname, $username, $hashedPassword, $phone_number, $specialty,
            $email, $location, $newFileName, $next_available, $role, $status, $unique_id
        ]);

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
}
