<?php
require 'connect.php';

$sql = "SELECT category, SUM(price * stock_quantity) AS total_sales
        FROM Products
        GROUP BY category";

$stmt = $conn->prepare($sql);
$stmt->execute();

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}

echo json_encode($data);
?>