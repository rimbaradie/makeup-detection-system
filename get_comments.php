<?php
header('Content-Type: application/json');
require 'connect.php';
session_start();

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// Select comment_id and user_id to support deletion
$stmt = $conn->prepare("
    SELECT c.comment_id, c.comment_text, c.comment_date, c.user_id, u.fname, u.lname 
    FROM Comments c 
    JOIN Users u ON c.user_id = u.user_id 
    WHERE c.product_id = ?
    ORDER BY c.comment_date DESC
");
$stmt->execute([$product_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Combine first name and last name into 'full_name'
foreach ($comments as &$comment) {
    $comment['full_name'] = $comment['fname'] . ' ' . $comment['lname'];
    unset($comment['fname'], $comment['lname']);
}

echo json_encode(['success' => true, 'comments' => $comments]);
?>
