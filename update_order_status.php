<?php
require 'connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['order_id'], $data['new_status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

$orderId = $data['order_id'];
$newStatus = $data['new_status'];

// Optional: Validate allowed statuses
$allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
if (!in_array(strtolower($newStatus), $allowedStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE Orders SET status = :status WHERE order_id = :order_id");
    $stmt->execute([
        ':status' => $newStatus,
        ':order_id' => $orderId
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>