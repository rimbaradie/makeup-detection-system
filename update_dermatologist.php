<?php

require 'connect.php';

// Validate required POST fields
$requiredFields = ['user_id', 'username', 'phone_number', 'specialty', 'email', 'location', 'next_available'];

foreach ($requiredFields as $field) {
    if (empty($_POST[$field]) && !isset($_POST[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
        exit;
    }
}

$id = $_POST['user_id'];

$username = $_POST['username'];
$phone_number = $_POST['phone_number'];
$email = $_POST['email'];
$specialty = $_POST['specialty'];
$location = $_POST['location'];
$next_available = $_POST['next_available'];


$uploadDir = 'pic/';
$newFileName = null;

// Handle profile image upload
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

// Check if password is provided to update
$updatePassword = false;
$hashedPassword = null;
if (!empty($_POST['password'])) {
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $updatePassword = true;
}

try {
    // Build base query and parameters
    $query = "UPDATE Users SET username = ?, email = ?, phone_number = ?, specialty = ?, location = ?, next_available = ?";
$params = [$username, $email, $phone_number, $specialty, $location, $next_available];


    // Add profile if uploaded
    if ($newFileName) {
        $query .= ", profile = ?";
        $params[] = $newFileName;
    }

    // Add password if updated
    if ($updatePassword) {
        $query .= ", password = ?";
        $params[] = $hashedPassword;
    }

    $query .= " WHERE user_id = ?";
    $params[] = $id;

    $stmt = $conn->prepare($query);
    $stmt->execute($params);

  header("Location: edit_dermatologist.php?success=1");
exit;

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}