<?php
include 'connect.php';

header('Content-Type: application/json');

if (isset($_POST['user_id']) && isset($_POST['product_id'])) {
    $user_id = intval($_POST['user_id']);
    $product_id = intval($_POST['product_id']);

    try {
        $sql = "INSERT INTO Likes (user_id, product_id) VALUES (:user_id, :product_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(["status" => "success", "message" => "Like inserted"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing parameters"]);
}
?>
