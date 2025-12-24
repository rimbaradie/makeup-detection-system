<?php
ob_clean();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'connect.php';

// جلب البيانات من JSON
$data = json_decode(file_get_contents('php://input'), true);
if ($data) { $_POST = $data; }

$id = $_POST['cart_item_id'] ?? 0;
if (!$id) {
    echo json_encode(["status"=>"error","message"=>"cart_item_id missing"]);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM Cart_Items WHERE cart_item_id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["status" => "success"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
