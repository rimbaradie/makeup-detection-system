<?php
// بدء الجلسة إذا لم تكن مفعلة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json; charset=UTF-8");

require 'connect.php';

// تأكد من ضبط الترميز للاتصال بقاعدة البيانات
$conn->exec("SET NAMES utf8mb4");

try {
    // جلب التعليقات مع بيانات المستخدم والمنتج
    $commentsStmt = $conn->prepare("
        SELECT 
            c.comment_id, 
            c.comment_text, 
            c.comment_date, 
            u.username, 
            p.name AS product_name,
            p.product_id,
            p.brand,
            p.category
        FROM Comments c
        LEFT JOIN Users u ON c.user_id = u.user_id
        LEFT JOIN Products p ON c.product_id = p.product_id
        ORDER BY c.comment_date DESC
        LIMIT 100
    ");
    $commentsStmt->execute();
    $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // جلب الإعجابات مع بيانات المستخدم والمنتج
    $likesStmt = $conn->prepare("
        SELECT 
            l.like_id, 
            l.liked_at, 
            u.username, 
            p.name AS product_name,
            p.product_id,
            p.brand
        FROM Likes l
        LEFT JOIN Users u ON l.user_id = u.user_id
        LEFT JOIN Products p ON l.product_id = p.product_id
        ORDER BY l.liked_at DESC
        LIMIT 100
    ");
    $likesStmt->execute();
    $likes = $likesStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // بناء مصفوفة الاستجابة مع تضمين بيانات المنتج بالشكل المطلوب
    $response = [
        'success' => true,
        'comments' => array_map(function($comment) {
            return [
                'comment_id' => $comment['comment_id'],
                'comment_text' => $comment['comment_text'],
                'comment_date' => $comment['comment_date'],
                'username' => $comment['username'],
                'product' => [
                    'product_id' => $comment['product_id'],
                    'name' => $comment['product_name'],
                    'brand' => $comment['brand'],
                    'category' => $comment['category']
                ]
            ];
        }, $comments),
        'likes' => array_map(function($like) {
            return [
                'like_id' => $like['like_id'],
                'liked_at' => $like['liked_at'],
                'username' => $like['username'],
                'product' => [
                    'product_id' => $like['product_id'],
                    'name' => $like['product_name'],
                    'brand' => $like['brand']
                ]
            ];
        }, $likes)
    ];

    // إرسال JSON مع إلغاء هروب Unicode والشرط المائل
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    error_log("PDOException in fetch_comments_likes.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log("Exception in fetch_comments_likes.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred'
    ], JSON_UNESCAPED_UNICODE);
}
?>
