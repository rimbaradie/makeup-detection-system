<?php
$data = json_decode(file_get_contents('php://input'), true);
$imageData = $data['image'];

$image = str_replace('data:image/jpeg;base64,', '', $imageData);
$image = base64_decode($image);

file_put_contents('uploads/user_image.jpg', $image);

// Call Python script to analyze acne
$result = shell_exec("python3 acne_detector.py uploads/user_image.jpg");

echo json_encode(['result' => trim($result)]);
?>
