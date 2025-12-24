<?php
session_start();
header('Content-Type: application/json');
require 'connect.php';           //  $conn = new PDO(...)

if (!isset($_SESSION['user_id']))
    exit(json_encode(['success'=>false,'message'=>'Login required']));

$user_id    = (int)$_SESSION['user_id'];
$product_id = (int)($_POST['product_id'] ?? 0);
$rating     = (int)($_POST['rating'] ?? 0);

if ($product_id<=0 || $rating<1 || $rating>5)
    exit(json_encode(['success'=>false,'message'=>'Invalid data']));

$stmt = $conn->prepare("
    INSERT INTO ProductReviews (user_id, product_id, rating)
    VALUES (:uid, :pid, :rate)
    ON DUPLICATE KEY UPDATE rating = :rate
");                       // one rating per user/product
$stmt->execute([':uid'=>$user_id,':pid'=>$product_id,':rate'=>$rating]);

echo json_encode(['success'=>true]);
