<?php
header('Content-Type: application/json');
require 'connect.php'; // Your PDO connection

try {
    // Conversion Rate = total users who ordered / total visitors (fake static traffic for now)
    $totalVisitors = 50000; // hardcoded for now

    // Total Orders
    $orderStmt = $conn->query("SELECT COUNT(*) as total_orders FROM Orders");
    $totalOrders = $orderStmt->fetch(PDO::FETCH_ASSOC)['total_orders'] ?? 0;

    // Avg Order Value
    $valueStmt = $conn->query("SELECT AVG(total_amount) as avg_value FROM Orders");
    $avgOrderValue = $valueStmt->fetch(PDO::FETCH_ASSOC)['avg_value'] ?? 0;

    // Top Selling Product
    $topStmt = $conn->query("
        SELECT P.name, SUM(OI.quantity) as total_sold
        FROM OrderItems OI
        JOIN Products P ON OI.product_id = P.product_id
        GROUP BY P.product_id
        ORDER BY total_sold DESC
        LIMIT 1
    ");
    $topProduct = $topStmt->fetch(PDO::FETCH_ASSOC)['name'] ?? 'N/A';

    echo json_encode([
        'conversion_rate' => number_format(($totalOrders / $totalVisitors) * 100, 1) . '%',
        'avg_order_value' => '$' . number_format($avgOrderValue, 2),
        'website_traffic' => '50k / Month',
        'top_product' => $topProduct
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to load dashboard metrics']);
}
?>
