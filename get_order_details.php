<?php
require 'connect.php';

if (!isset($_GET['order_id'])) {
    echo json_encode(['error' => 'Order ID not provided']);
    exit;
}

$orderId = $_GET['order_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM Orders WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['error' => 'Order not found']);
    } else {
        echo json_encode($order);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>