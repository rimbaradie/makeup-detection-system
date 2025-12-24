<?php
session_start();
require 'connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT p.product_id, p.name, p.price, p.image_product, p.skin_type
    FROM Likes l
    JOIN Products p ON l.product_id = p.product_id
    WHERE l.user_id = ?
    ORDER BY l.liked_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($favorites);
?>
