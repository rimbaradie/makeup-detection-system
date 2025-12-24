<?php
require 'connect.php'; // Database connection

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="orders_export.csv"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// CSV column headers (remove 'Price' and 'Quantity')
fputcsv($output, ['Order ID', 'User ID', 'Order Date', 'Total Amount', 'Status', 'Payment Method']);

// Fetch data without price and quantity
$stmt = $conn->query("SELECT order_id, user_id, order_date, total_amount, status, payment_method FROM orders");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit;