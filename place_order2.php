<?php

header('Content-Type: application/json');
require 'connect.php';   // $conn = new PDO(...)

// استقبل user_id من JSON القادم من Android
$input = json_decode(file_get_contents("php://input"), true);
$user_id = isset($input['user_id']) ? (int)$input['user_id'] : 0;
$payment_method = isset($input['payment_method']) ? trim($input['payment_method']) : 'cash';

if ($user_id === 0) {
    echo json_encode(['success' => false, 'message' => 'User ID is missing']);
    exit;
}


$cartSQL = "
    SELECT ci.product_id,
           ci.quantity,
           p.price                                                  AS base_price,
           COALESCE(ps.discount_percentage, 0)                      AS discount
    FROM   Cart_Items        ci
    JOIN   Products          p  ON p.product_id = ci.product_id
    LEFT JOIN ProductSales   ps ON ps.product_id = ci.product_id
                                AND CURDATE() BETWEEN ps.start_date AND ps.end_date
    WHERE  ci.user_id = ?
";
$cartStmt = $conn->prepare($cartSQL);
$cartStmt->execute([$user_id]);
$cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit;
}

$total_amount   = 0.00;
$total_quantity = 0;
foreach ($cartItems as $item) {
    $effectivePrice = round($item['base_price'] * (1 - $item['discount'] / 100), 2);
    $subtotal       = $effectivePrice * $item['quantity'];
    $total_amount  += $subtotal;
    $total_quantity += $item['quantity'];
}

try {
    $conn->beginTransaction();

    /* 3.1  Insert row in Orders                                      */
    $orderIns = $conn->prepare("
        INSERT INTO Orders
               (user_id, order_date, total_amount, quantity, status, payment_method)
        VALUES (?,       CURDATE(), ?,           ?,        'pending', ?)
    ");
    $orderIns->execute([$user_id, $total_amount, $total_quantity, $payment_method]);

    $order_id = $conn->lastInsertId();

    /* 3.2  Insert each item in OrderItems & update stock             */
    $itemIns  = $conn->prepare("
        INSERT INTO OrderItems
               (order_id, product_id, quantity, price)
        VALUES (?,        ?,          ?,        ?)
    ");
    $stockUpd = $conn->prepare("
        UPDATE Products
           SET stock_quantity = stock_quantity - ?
         WHERE product_id     = ?
    ");

    foreach ($cartItems as $item) {
        $effectivePrice = round($item['base_price'] * (1 - $item['discount'] / 100), 2);

        // add line‑item
        $itemIns->execute([
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $effectivePrice
        ]);

        // decrement stock
        $stockUpd->execute([
            $item['quantity'],
            $item['product_id']
        ]);
    }

    /* 3.3  Clear the user’s cart                                    */
    $clearCart = $conn->prepare("DELETE FROM Cart_Items WHERE user_id = ?");
    $clearCart->execute([$user_id]);

    $conn->commit();

    echo json_encode(['success' => true, 'order_id' => $order_id]);
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    error_log('Order creation failed: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to place order. Please try again.']);
    exit;
}
?>
