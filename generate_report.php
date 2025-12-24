<?php
header('Content-Type: application/json');
require 'connect.php';

$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;

if (!$start_date || !$end_date) {
    echo json_encode(['error' => 'Start date and end date are required.']);
    exit;
}

try {
    // Total orders count between dates
    $stmtOrders = $conn->prepare("SELECT COUNT(*) as total_orders, SUM(total_amount) as total_sales FROM Orders WHERE order_date BETWEEN ? AND ?");
    $stmtOrders->execute([$start_date, $end_date]);
    $ordersData = $stmtOrders->fetch(PDO::FETCH_ASSOC);

    $total_orders = $ordersData['total_orders'] ?? 0;
    $total_sales = $ordersData['total_sales'] ?? 0;

    // Top selling product between dates
    $stmtTop = $conn->prepare("
        SELECT P.name, SUM(O.quantity) as total_qty
        FROM Orders O
        JOIN Products P ON O.product_id = P.product_id
        WHERE O.order_date BETWEEN ? AND ?
        GROUP BY O.product_id
        ORDER BY total_qty DESC
        LIMIT 1
    ");
    $stmtTop->execute([$start_date, $end_date]);
    $topProductRow = $stmtTop->fetch(PDO::FETCH_ASSOC);
    $top_product = $topProductRow['name'] ?? 'N/A';

    echo json_encode([
        'total_orders' => $total_orders,
        'total_sales' => $total_sales,
        'top_product' => $top_product
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}