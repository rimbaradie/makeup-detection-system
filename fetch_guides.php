<?php
include 'connect.php';

$stmt = $conn->prepare("SELECT * FROM help_guides");
$stmt->execute();
$guides = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($guides);
?>