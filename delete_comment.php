<?php
session_start();
require 'connect.php';

header('Content-Type: application/json');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$commentId = $_POST['comment_id'] ?? null;

// التحقق من وجود comment_id
if (!$commentId) {
    echo json_encode(['success' => false, 'message' => 'Missing comment ID']);
    exit;
}

// التحقق أن التعليق يخص المستخدم
$stmt = $conn->prepare("SELECT * FROM Comments WHERE comment_id = ? AND user_id = ?");
$stmt->execute([$commentId, $userId]);
$comment = $stmt->fetch();

if (!$comment) {
    echo json_encode(['success' => false, 'message' => 'Comment not found or not yours']);
    exit;
}

// حذف التعليق
$stmt = $conn->prepare("DELETE FROM Comments WHERE comment_id = ?");
$stmt->execute([$commentId]);

echo json_encode(['success' => true]);
?>
