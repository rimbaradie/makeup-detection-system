<?php
require 'connect.php';

$response = ['status' => 'error', 'message' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate product ID
    if (empty($_POST['product_id'])) {
        $response['message'] = 'Product ID is required.';
        echo json_encode($response);
        exit;
    }
    
    $product_id = (int) $_POST['product_id'];
    $name = trim($_POST['name'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $skin_type = trim($_POST['skin_type'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock_quantity = intval($_POST['stock_quantity'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if (!$name) {
        $response['message'] = 'Product name is required.';
        echo json_encode($response);
        exit;
    }

    // Fetch current product info to get current images (for replacement or keeping)
    $stmt = $conn->prepare("SELECT image_product, image2_product FROM Products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existingProduct) {
        $response['message'] = 'Product not found.';
        echo json_encode($response);
        exit;
    }

    // Handle image uploads
    $uploadDir = 'pictures/';
    $image_product = $existingProduct['image_product'];
    $image2_product = $existingProduct['image2_product'];

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['product_image']['tmp_name'];
        $filename = basename($_FILES['product_image']['name']);
        $targetFile = $uploadDir . uniqid() . '_' . $filename;

        if (move_uploaded_file($tmpName, $targetFile)) {
            // Optionally delete old image file if exists
            if ($image_product && file_exists($uploadDir . $image_product)) {
                unlink($uploadDir . $image_product);
            }
            // Save new filename relative to 'pic/'
            $image_product = basename($targetFile);
        } else {
            $response['message'] = 'Failed to upload primary image.';
            echo json_encode($response);
            exit;
        }
    }

    if (isset($_FILES['product_image2']) && $_FILES['product_image2']['error'] === UPLOAD_ERR_OK) {
        $tmpName2 = $_FILES['product_image2']['tmp_name'];
        $filename2 = basename($_FILES['product_image2']['name']);
        $targetFile2 = $uploadDir . uniqid() . '_' . $filename2;

        if (move_uploaded_file($tmpName2, $targetFile2)) {
            // Optionally delete old second image
            if ($image2_product && file_exists($uploadDir . $image2_product)) {
                unlink($uploadDir . $image2_product);
            }
            $image2_product = basename($targetFile2);
        } else {
            $response['message'] = 'Failed to upload second image.';
            echo json_encode($response);
            exit;
        }
    }

    // Update product record in DB
    $updateStmt = $conn->prepare("UPDATE Products SET 
        name = ?, 
        brand = ?, 
        category = ?, 
        skin_type = ?, 
        price = ?, 
        stock_quantity = ?, 
        description = ?, 
        image_product = ?, 
        image2_product = ? 
        WHERE product_id = ?");

    $success = $updateStmt->execute([
        $name,
        $brand,
        $category,
        $skin_type,
        $price,
        $stock_quantity,
        $description,
        $image_product,
        $image2_product,
        $product_id
    ]);

    if ($success) {
        $response['status'] = 'success';
        $response['message'] = 'Product updated successfully.';
    } else {
        $response['message'] = 'Failed to update product in database.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>