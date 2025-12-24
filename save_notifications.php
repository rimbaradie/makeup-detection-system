<?php
session_start();
require 'connect.php';

$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$notify_new_order = isset($data['notify_new_order']) ? (int)$data['notify_new_order'] : 0;
$notify_new_user = isset($data['notify_new_user']) ? (int)$data['notify_new_user'] : 0;
$notify_low_stock = isset($data['notify_low_stock']) ? (int)$data['notify_low_stock'] : 0;

// Check if row exists
$stmt = $conn->prepare("SELECT COUNT(*) FROM NotificationSettings WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$exists = $stmt->fetchColumn();

if ($exists) {
    // Update existing
    $stmt = $conn->prepare("UPDATE NotificationSettings SET notify_new_order = :notify_new_order, notify_new_user = :notify_new_user, notify_low_stock = :notify_low_stock WHERE user_id = :user_id");
} else {
    // Insert new row
    $stmt = $conn->prepare("INSERT INTO NotificationSettings (user_id, notify_new_order, notify_new_user, notify_low_stock) VALUES (:user_id, :notify_new_order, :notify_new_user, :notify_low_stock)");
}

$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':notify_new_order', $notify_new_order);
$stmt->bindParam(':notify_new_user', $notify_new_user);
$stmt->bindParam(':notify_low_stock', $notify_low_stock);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>