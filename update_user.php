<?php
header('Content-Type: application/json');
require 'connect.php';

if (
    isset($_POST['user_id']) &&
    isset($_POST['username']) &&
    isset($_POST['email']) &&
    isset($_POST['role']) &&
    isset($_POST['skin_type']) &&
    isset($_POST['fname']) &&
    isset($_POST['lname'])
) {
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $skin_type = $_POST['skin_type'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];

    // Handle profile image if uploaded
    $profilePath = null;
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'pic/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = basename($_FILES['profile']['name']);
        $targetFile = $uploadDir . time() . "_" . $filename;

        if (move_uploaded_file($_FILES['profile']['tmp_name'], $targetFile)) {
            $profilePath = $targetFile;
        }
    }

    try {
        $sql = "UPDATE Users SET username = ?, email = ?, role = ?, skin_type = ?, fname = ?, lname = ?";

        $params = [$username, $email, $role, $skin_type, $fname, $lname];

        if ($profilePath) {
            $sql .= ", profile = ?";
            $params[] = $profilePath;
        }

        $sql .= " WHERE user_id = ?";
        $params[] = $userId;

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['status' => 'updated']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
}
?>