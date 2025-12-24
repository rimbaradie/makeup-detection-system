<?php
header('Content-Type: application/json');
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = $_POST['product_id'];
    $discount_percentage = $_POST['discount_percentage'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Step 1: Check if product exists
    $check = $conn->prepare("SELECT product_id FROM Products WHERE product_id = ?");
    $check->execute([$product_id]);

    if ($check->rowCount() === 0) {
        echo json_encode(["status" => "error", "message" => "Invalid Product ID: No such product found."]);
        exit;
    }

    // Step 2: Insert into ProductSale
    $stmt = $conn->prepare("INSERT INTO ProductSales (product_id, discount_percentage, start_date, end_date) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$product_id, $discount_percentage, $start_date, $end_date]);

    if ($result) {
        echo json_encode(["status" => "success", "message" => "Discount added successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add discount."]);
    }
}
?>
