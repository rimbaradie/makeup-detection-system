<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "SkinGlam");
$conn->set_charset("utf8");

if (!$conn || $conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$sql = "SELECT fname, lname, username, email, skin_type FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result) {
    echo json_encode(['status' => 'success', 'data' => $result]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}
