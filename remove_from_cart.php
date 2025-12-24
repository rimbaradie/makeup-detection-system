<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$product_id = $data['product_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Product ID missing']);
    exit;
}

$sql = "DELETE FROM Cart_Items WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt->execute([$user_id, $product_id])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Deletion failed']);
}
?>
