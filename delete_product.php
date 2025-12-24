<?php
require 'connect.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // الحصول على بيانات JSON
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['product_id'])) {
        $response['message'] = 'Product ID is required.';
        echo json_encode($response);
        exit;
    }

    $product_id = (int)$input['product_id'];

    // جلب مسارات الصور أولاً
    $stmt = $conn->prepare("SELECT image_product, image2_product FROM Products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $response['message'] = 'Product not found.';
        echo json_encode($response);
        exit;
    }

    // حذف الصور إن وُجدت
    if (!empty($product['image_product']) && file_exists($product['image_product'])) {
        unlink($product['image_product']);
    }
    if (!empty($product['image2_product']) && file_exists($product['image2_product'])) {
        unlink($product['image2_product']);
    }

    // حذف المنتج من قاعدة البيانات
    $deleteStmt = $conn->prepare("DELETE FROM Products WHERE product_id = ?");
    $success = $deleteStmt->execute([$product_id]);

    if ($success) {
        $response['status']  = 'success';
        $response['message'] = 'Product deleted successfully.';
    } else {
        $response['message'] = 'Failed to delete product.';
    }

} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
?>
