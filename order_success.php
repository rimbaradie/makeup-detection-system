<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    echo "Order ID not found.";
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// تحقق أن الطلب للمستخدم الحالي
$checkSql = "SELECT * FROM Orders WHERE order_id = ? AND user_id = ?";
$stmt = $conn->prepare($checkSql);
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found.";
    exit();
}

// جلب تفاصيل الطلب
$sql = "
    SELECT oi.product_id, p.name, oi.quantity, oi.price,
           (oi.quantity * oi.price) AS subtotal
    FROM OrderItems oi
    JOIN Products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_price = $order['total_amount'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmed</title>
</head>
<body>
    <h2>Order #<?= $order_id ?> Confirmed!</h2>
    <ul>
        <?php foreach ($items as $item): ?>
            <li>
                <?= htmlspecialchars($item['name']) ?> 
                (x<?= $item['quantity'] ?>) - 
                $<?= number_format($item['price'], 2) ?> each 
                → $<?= number_format($item['subtotal'], 2) ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <p><strong>Total:</strong> $<?= number_format($total_price, 2) ?></p>
</body>
</html>
