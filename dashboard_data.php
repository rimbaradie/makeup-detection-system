<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require 'connect.php'; // Your PDO connection file

try {
    $data = [];

    // Total users
    $stmt = $conn->query("SELECT COUNT(*) FROM Users");
    $data['total_users'] = $stmt->fetchColumn();

    // Pending orders
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Orders WHERE status = 'pending'");
    $stmt->execute();
    $data['pending_orders'] = $stmt->fetchColumn();

    // New comments (last 24 hours)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Comments WHERE comment_date >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
    $stmt->execute();
    $data['new_comments'] = $stmt->fetchColumn();

    // Monthly revenue (completed orders in current month)
    $month = date('m');
    $year = date('Y');
    $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM Orders WHERE status IN ('shipped', 'delivered') AND MONTH(order_date) = ? AND YEAR(order_date) = ?");
    $stmt->execute([$month, $year]);
    $data['monthly_revenue'] = $stmt->fetchColumn();

    // Active dermatologists count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Users WHERE role = 'Dermatologist' AND status = 'active'");
    $stmt->execute();
    $data['active_dermatologists'] = $stmt->fetchColumn();

    // Product SKUs count
    $stmt = $conn->query("SELECT COUNT(*) FROM Products");
    $data['product_skus'] = $stmt->fetchColumn();

    

    // Sales chart: Monthly sales from Orders
    $sales_stmt = $conn->query("
        SELECT MONTHNAME(order_date) AS month, SUM(total_amount) AS total
        FROM Orders
        WHERE status = 'completed'
        GROUP BY MONTH(order_date)
        ORDER BY MONTH(order_date)
    ");
    $sales_labels = [];
    $sales_values = [];
    while ($row = $sales_stmt->fetch(PDO::FETCH_ASSOC)) {
        $sales_labels[] = $row['month'];
        $sales_values[] = (float)$row['total'];
    }
    $data['sales_data'] = [
        'labels' => $sales_labels,
        'values' => $sales_values
    ];

    // Registrations chart: Monthly new users
    $reg_stmt = $conn->query("
        SELECT MONTHNAME(created_at) AS month, COUNT(*) AS total
        FROM Users
        GROUP BY MONTH(created_at)
        ORDER BY MONTH(created_at)
    ");
    $reg_labels = [];
    $reg_values = [];
    while ($row = $reg_stmt->fetch(PDO::FETCH_ASSOC)) {
        $reg_labels[] = $row['month'];
        $reg_values[] = (int)$row['total'];
    }
    $data['registrations_data'] = [
        'labels' => $reg_labels,
        'values' => $reg_values
    ];

    echo json_encode(['status' => 'success', 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}