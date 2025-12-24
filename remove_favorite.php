<?php
session_start();
require 'connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the JSON input from fetch
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID missing']);
    exit();
}

$product_id = (int)$data['product_id'];

$sql = "DELETE FROM Likes WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt->execute([$user_id, $product_id])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete']);
}
?>
