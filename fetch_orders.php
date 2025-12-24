<?php
require 'connect.php';

try {
    $stmt = $conn->prepare("
        SELECT o.order_id, u.username, o.order_date, o.total_amount, o.status, o.payment_method
        FROM Orders o
        LEFT JOIN Users u ON o.user_id = u.user_id
        ORDER BY o.order_date DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orders as $order) {
        $statusClass = 'status-' . strtolower($order['status']);
        $totalFormatted = '$' . number_format($order['total_amount'], 2);

        $orderId = htmlspecialchars($order['order_id']);
        $username = htmlspecialchars($order['username'] ?? 'Unknown');
        $orderDate = htmlspecialchars($order['order_date']);
        $status = htmlspecialchars($order['status']);
        $paymentMethod = htmlspecialchars($order['payment_method'] ?? '');

        echo '<tr data-order-id="' . $orderId . '">';
        echo    '<td>' . $orderId . '</td>';
        echo    '<td>' . $username . '</td>';
        echo    '<td>' . $orderDate . '</td>';
        echo    '<td>' . $totalFormatted . '</td>';
        echo    '<td><span class="' . $statusClass . '">' . $status . '</span></td>';
        echo    '<td>' . $paymentMethod . '</td>';
        echo    '<td>';
        echo        '<button class="btn btn-sm btn-info tooltip view-order-btn" data-id="' . $orderId . '" data-tooltip="View Details"><i class="fas fa-info-circle"></i></button>';
        echo        '<button class="btn btn-sm btn-warning tooltip change-status-btn" data-tooltip="Change Status"><i class="fas fa-redo"></i></button>';
        echo        '<button class="btn btn-sm btn-success tooltip print-invoice-btn" data-tooltip="Print Invoice"><i class="fas fa-print"></i></button>';
        echo    '</td>';
        echo '</tr>';
    }
} catch (PDOException $e) {
    echo '<tr><td colspan="7">Error loading orders: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
}
?>