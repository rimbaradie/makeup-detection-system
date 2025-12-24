<?php
require 'connect.php';

header('Content-Type: application/json');

if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
    exit;
}

$product_id = intval($_GET['product_id']);

$stmt = $conn->prepare("SELECT * FROM Products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit;
}

// Sanitize and format data
$product['name']           = htmlspecialchars($product['name']);
$product['brand']          = htmlspecialchars($product['brand']);
$product['category']       = htmlspecialchars($product['category']);
$product['skin_type']      = htmlspecialchars($product['skin_type']);
$product['price']          = number_format((float)$product['price'], 2);
$product['stock_quantity'] = (int)$product['stock_quantity'];

// احتفظ بالمسار كما هو في قاعدة البيانات (يشمل pictures/ بالفعل إذا كان موجوداً)
$product['image_product']  = !empty($product['image_product'])
                            ? htmlspecialchars($product['image_product'])
                            : 'https://via.placeholder.com/150';

$product['image2_product'] = !empty($product['image2_product'])
                            ? htmlspecialchars($product['image2_product'])
                            : '';

$product['description']    = htmlspecialchars($product['description']);

echo json_encode(['status' => 'success', 'product' => $product]);
?>
