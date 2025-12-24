<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'connect.php';

header('Content-Type: application/json; charset=UTF-8');
$conn->exec("SET NAMES utf8mb4"); // ← هذا يضمن دعم الرموز مثل ’ و é

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not logged in'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "
        SELECT ci.product_id, p.name, p.image_product, ci.quantity,
               p.price AS original_price,
               COALESCE(ps.discount_percentage, 0) AS discount
        FROM Cart_Items ci
        JOIN Products p ON ci.product_id = p.product_id
        LEFT JOIN ProductSales ps 
            ON ci.product_id = ps.product_id AND CURDATE() BETWEEN ps.start_date AND ps.end_date
        WHERE ci.user_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $items = [];
    $total_price = 0;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $price_after_discount = round($row['original_price'] * (1 - $row['discount'] / 100), 2);
        $subtotal = $price_after_discount * $row['quantity'];
        $total_price += $subtotal;
        $items[] = [
            'product_name' => $row['name'],
            'image_url' => $row['image_product'],
            'quantity' => $row['quantity'],
            'unit_price' => $price_after_discount,
            'subtotal' => $subtotal
        ];
    }

    echo json_encode(['success' => true, 'items' => $items, 'total' => $total_price], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
