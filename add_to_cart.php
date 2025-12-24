<?php
session_start();
header('Content-Type: application/json');



// التحقق من تسجيل دخول المستخدم
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

require 'connect.php'; // يحتوي على $conn ككائن PDO

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;
$quantity = intval($_POST['quantity'] ?? 1);


if (!$product_id || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
    exit;
}

try {
    // تحقق إذا كان المنتج موجود
    $stmt = $conn->prepare("SELECT * FROM Products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    // تحقق هل المنتج موجود مسبقًا في السلة
    $checkStmt = $conn->prepare("SELECT * FROM Cart_Items WHERE user_id = ? AND product_id = ?");
    $checkStmt->execute([$user_id, $product_id]);
    $exists = $checkStmt->fetch();

    if ($exists) {
        // تحديث الكمية
        $updateStmt = $conn->prepare("UPDATE Cart_Items SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
        $updateStmt->execute([$quantity, $user_id, $product_id]);
    } else {
        // إدخال جديد
        $insertStmt = $conn->prepare("INSERT INTO Cart_Items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insertStmt->execute([$user_id, $product_id, $quantity]);
    }

    echo json_encode(['success' => true]);
    exit;

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}
