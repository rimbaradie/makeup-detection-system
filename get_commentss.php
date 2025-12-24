<?php
header('Content-Type: application/json');
require 'connect.php';  // Your database connection file

try {
    $stmt = $conn->prepare("SELECT comment_id, user_id, product_id, comment_text, comment_date FROM Comments ORDER BY comment_date DESC");
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($comments);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}