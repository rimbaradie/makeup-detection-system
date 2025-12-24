<?php

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

if ($skin_type !== 'all') {
    $sql .= " AND p.skin_type = '" . $conn->real_escape_string($skin_type) . "'";
}

if ($brand !== 'all') {
    $sql .= " AND p.brand = '" . $conn->real_escape_string($brand) . "'";
}

$result = $conn->query($sql);

$products = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // تأكد من تحويل discount_percentage لرقم عشري (float)
        $row['discount_percentage'] = floatval($row['discount_percentage']);
        $products[] = $row;
    }
}

echo json_encode($products);

$conn->close();
?>
