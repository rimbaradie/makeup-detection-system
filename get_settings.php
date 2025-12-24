<?php
require 'connect.php';

$stmt = $conn->query("SELECT * FROM SiteSettings WHERE id = 1");
$data = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'status' => 'success',
    'settings' => $data
]);
?>
