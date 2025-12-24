<?php
require 'connect.php';

$stmt = $conn->prepare("SELECT * FROM Comments LIMIT 5");
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($comments);
?>
