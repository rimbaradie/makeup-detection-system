<?php
include 'connect.php';

try {
    // Collect POST data
    $name           = $_POST['name']          ?? '';
    $brand          = $_POST['brand']         ?? '';
    $category       = $_POST['category']      ?? '';
    $skin_type      = $_POST['skin_type']     ?? '';
    $price          = $_POST['price']         ?? '';
    $stock_quantity = $_POST['stock_quantity']?? '';
    $description    = $_POST['description']   ?? '';

    // Validate required fields
    if (empty($name) || empty($brand) || empty($category) || empty($price) || empty($stock_quantity)) {
        echo json_encode(["status" => "error", "message" => "Please fill in all required fields."]);
        exit;
    }

    // File‑upload directory
    $uploadDir      = 'pictures/';
    $image_product  = '';
    $image2_product = '';

    // ===== image_product =====
    if (isset($_FILES['image_product']) && $_FILES['image_product']['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES['image_product']['name']);
        // احرص على إنشاء المجلد إذا لم يكن موجوداً
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
        move_uploaded_file($_FILES['image_product']['tmp_name'], $uploadDir . $filename);
        // نزود المسار الكامل ليُحفظ في الجدول مع pictures/
        $image_product = $uploadDir . $filename;
    }

    // ===== image2_product =====
    if (isset($_FILES['image2_product']) && $_FILES['image2_product']['error'] === UPLOAD_ERR_OK) {
        $filename2 = basename($_FILES['image2_product']['name']);
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
        move_uploaded_file($_FILES['image2_product']['tmp_name'], $uploadDir . $filename2);
        $image2_product = $uploadDir . $filename2;
    }

    // Insert into database
    $sql = "INSERT INTO Products
              (name, brand, category, skin_type, price, stock_quantity, image_product, image2_product, description)
            VALUES
              (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $name,
        $brand,
        $category,
        $skin_type,
        $price,
        $stock_quantity,
        $image_product,
        $image2_product,
        $description
    ]);

    $lastId = $conn->lastInsertId();

    echo json_encode([
        "status"     => "success",
        "message"    => "Product added successfully.",
        "product_id" => $lastId
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
