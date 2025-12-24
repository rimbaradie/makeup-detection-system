<?php
header('Content-Type: application/json');
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sale_id = $_POST['sale_id'];
    $product_id = $_POST['product_id'];
    $discount_percentage = $_POST['discount_percentage'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Validate sale exists
    $check = $conn->prepare("SELECT sale_id FROM ProductSales WHERE sale_id = ?");
    $check->execute([$sale_id]);
    if ($check->rowCount() === 0) {
        echo json_encode(["status" => "error", "message" => "Invalid Discount ID."]);
        exit;
    }

    // Update discount
    $stmt = $conn->prepare("UPDATE ProductSales SET product_id = ?, discount_percentage = ?, start_date = ?, end_date = ? WHERE sale_id = ?");
    $result = $stmt->execute([$product_id, $discount_percentage, $start_date, $end_date, $sale_id]);

    if ($result) {
        echo json_encode(["status" => "success", "message" => "Discount updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update discount."]);
    }
}
?>
