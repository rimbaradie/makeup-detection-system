<?php
require 'connect.php';

if (!isset($_GET['order_id'])) {
    echo "No order ID specified.";
    exit;
}

$order_id = intval($_GET['order_id']);

try {
    $stmt = $conn->prepare("
        SELECT o.order_id, u.username, o.order_date, o.total_amount, o.status, o.payment_method
        FROM Orders o
        LEFT JOIN Users u ON o.user_id = u.user_id
        WHERE o.order_id = :order_id
    ");
    $stmt->execute(['order_id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "Order not found.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Invoice - Order #<?php echo htmlspecialchars($order['order_id']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        .total { font-weight: bold; }
        @media print {
            button { display: none; }
        }
    </style>
</head>
<body>
    <h1>Invoice</h1>
    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
    <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['username'] ?? 'Unknown'); ?></p>
    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>

    <table>
        <thead>
            <tr>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo '$' . number_format($order['total_amount'], 2); ?></td>
            </tr>
        </tbody>
    </table>

    <button onclick="window.print()">Print Invoice</button>
</body>
</html>