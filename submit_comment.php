<?php
session_start(); // required if you track user sessions
require 'connect.php';

header('Content-Type: application/json');

// Replace with your actual login/session user ID logic
$user_id = $_SESSION['user_id'] ?? null;
$product_id = $_POST['product_id'] ?? null;
$comment_text = trim($_POST['comment_text'] ?? '');

if (!$user_id || !$product_id || !$comment_text) {
    echo json_encode(['success' => false, 'message' => 'Missing data.']);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO Comments (user_id, product_id, comment_text)
    VALUES (?, ?, ?)
");
$success = $stmt->execute([$user_id, $product_id, $comment_text]);

echo json_encode(['success' => $success]);
?>
