<?php
session_start();
require 'connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_item_id = isset($_POST['cart_item_id']) ? intval($_POST['cart_item_id']) : 0;

if ($cart_item_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item ID']);
    exit();
}

try {
    // احذف العنصر فقط إذا كان يخص المستخدم الحالي
    $sql = "DELETE FROM Cart_Items WHERE cart_item_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$cart_item_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found or not authorized']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error deleting item: ' . $e->getMessage()]);
}
