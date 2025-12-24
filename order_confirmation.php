<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT ci.product_id, p.name, ci.quantity, 
           p.price AS original_price,
           COALESCE(ps.discount_percentage, 0) AS discount
    FROM Cart_Items ci
    JOIN Products p ON ci.product_id = p.product_id
    LEFT JOIN ProductSales ps ON ci.product_id = ps.product_id 
        AND CURDATE() BETWEEN ps.start_date AND ps.end_date
    WHERE ci.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$items = [];
$total_price = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $price_after_discount = round($row['original_price'] * (1 - $row['discount'] / 100), 2);
    $subtotal = $price_after_discount * $row['quantity'];
    $total_price += $subtotal;

    $items[] = [
        'product_id' => $row['product_id'],
        'name' => $row['name'],
        'quantity' => $row['quantity'],
        'original_price' => $row['original_price'],
        'unit_price' => $price_after_discount,
        'subtotal' => $subtotal
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <style>
        body {
            background: rgba(0, 0, 0, 0.4);
            font-family: Arial;
        }
        .modal {
            background: #fff0f5;
            width: 600px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            color: #d63384;
        }
        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 12px 0;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .item span {
            flex: 1;
        }
        .price-info {
            text-align: right;
        }
        .total {
            font-weight: bold;
            font-size: 18px;
            text-align: right;
            margin-top: 20px;
        }
        .actions {
            text-align: center;
            margin-top: 30px;
        }
        .actions button {
            padding: 10px 20px;
            font-size: 16px;
            margin: 0 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .confirm {
            background-color: #d63384;
            color: white;
        }
        .cancel {
            background-color: #6c757d;
            color: white;
        }
       .delete-btn {
    background: yellow !important;
    border: 2px solid red !important;
    color: red !important;
    font-size: 50px !important;
    cursor: pointer !important;
    padding: 5px 10px !important;
}


    </style>
</head>
<body>
    <div class="modal">
        <h2>Order Confirmation</h2>

        <?php if (empty($items)): ?>
            <p style="text-align: center;">Your cart is empty.</p>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <div class="item" data-product-id="<?= $item['product_id'] ?>">
                    <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
                    <div class="price-info">
                        <div style="color: grey;"><del>$<?= number_format($item['original_price'], 2) ?></del></div>
                        <div style="color: #d63384;">$<?= number_format($item['unit_price'], 2) ?> each</div>
                        <strong>$<?= number_format($item['subtotal'], 2) ?></strong>
                    </div>
                  <button class="delete-btn" onclick="deleteItem(<?= $item['product_id'] ?>)">🗑️</button>


                </div>
            <?php endforeach; ?>

            <div class="total">Total: $<?= number_format($total_price, 2) ?></div>

            <div class="actions">
                <form method="post" action="place_order.php" style="display:inline;">
                    <button type="submit" class="confirm">Confirm Order</button>
                </form>
                <form method="get" action="test.html" style="display:inline;">
                    <button type="submit" class="cancel">Cancel</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
    function deleteItem(productId) {
        if (!confirm("Are you sure you want to delete this item?")) return;

        fetch('remove_from_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-product-id="${productId}"]`).remove();
                location.reload(); // Optional: update total
            } else {
                alert("Failed to remove item.");
            }
        })
        .catch(err => console.error(err));
    }
    </script>
</body>
</html>
