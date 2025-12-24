<?php
session_start();
require 'connect.php'; // تأكد أن ملف الاتصال بـ PDO جاهز ويشتغل

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // جلب عناصر السلة المرتبطة بالمستخدم
    $sql = "
    SELECT ci.cart_item_id, ci.product_id, p.name, ci.quantity, p.price, p.image_product, p.brand
    FROM Cart_Items ci
    JOIN Products p ON ci.product_id = p.product_id
    WHERE ci.user_id = ?
    ORDER BY ci.added_at DESC
";


    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'items' => $items
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching cart items: ' . $e->getMessage()
    ]);
}
