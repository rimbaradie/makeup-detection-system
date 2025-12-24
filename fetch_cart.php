<?php
ob_clean();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'connect.php';

$data = json_decode(file_get_contents('php://input'), true);
if ($data) { $_POST = $data; }

$user_id = $_POST['user_id'] ?? $_GET['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(["status"=>"error","message"=>"user_id missing"]);
    exit;
}

$sql = "SELECT ci.cart_item_id,
               ci.quantity,
               p.product_id,
               p.name,
               p.price,
               p.image_product
        FROM Cart_Items ci
        JOIN Products p ON ci.product_id = p.product_id
        WHERE ci.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);

$items = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row['subtotal'] = $row['price'] * $row['quantity'];

    // تأكد من أن جميع القيم Strings مشفّرة بصيغة UTF-8
    foreach ($row as $key => $value) {
        if (is_string($value)) {
            $row[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }
    }

    $items[] = $row;
}

echo json_encode(["status"=>"success","items"=>$items], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
