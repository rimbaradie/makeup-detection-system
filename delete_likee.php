<?php
require 'connect.php';

if (!isset($_POST['like_id'])) {
    echo "Like ID is missing";
    exit;
}

$likeId = $_POST['like_id'];

try {
    $stmt = $conn->prepare("DELETE FROM Likes WHERE like_id = ?");
    $stmt->execute([$likeId]);
    echo "Like deleted successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}