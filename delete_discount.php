<?php
header('Content-Type: application/json');
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['sale_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing sale_id']);
        exit;
    }

    $sale_id = $_POST['sale_id'];

    try {
        $stmt = $conn->prepare("DELETE FROM ProductSales WHERE sale_id = ?");
        $result = $stmt->execute([$sale_id]);

        if ($result && $stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No record deleted, maybe wrong ID']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
