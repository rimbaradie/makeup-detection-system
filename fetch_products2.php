<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "SkinGlam");
$conn->set_charset("utf8"); 

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed']);
    exit;
}

$skin_type = $_GET['skin_type'] ?? 'all';
$brand = $_GET['brand'] ?? 'all';

// نستخدم LEFT JOIN لجلب خصم إن وجد والتأكد من أن الخصم فعال اليوم
$sql = "
SELECT p.product_id, p.name, p.price, p.description, p.image_product, 
       COALESCE(ps.discount_percentage, 0) AS discount_percentage
FROM Products p
LEFT JOIN ProductSales ps 
  ON p.product_id = ps.product_id
  AND CURDATE() BETWEEN ps.start_date AND ps.end_date
WHERE 1=1
";

if ($skin_type !== 'All') {
    $sql .= " AND LOWER(p.skin_type) = '" . strtolower($conn->real_escape_string($skin_type)) . "'";
}

if ($brand !== 'All') {
    $sql .= " AND LOWER(p.brand) = '" . strtolower($conn->real_escape_string($brand)) . "'";
}


$result = $conn->query($sql);

$products = [];

$imageBaseUrl = "http://192.168.1.108/SkinGlamProject/";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['discount_percentage'] = floatval($row['discount_percentage']);
        $row['image_product'] = $imageBaseUrl . $row['image_product'];

        $products[] = $row;
    }
    echo json_encode($products);
} else {
    // ما في نتائج → رجّع رسالة واضحة
    echo json_encode(["message" => "Sorry, no products"]);
}


$conn->close();
?>
