<?php
include 'connect.php';

try {
    $stmt = $conn->prepare("SELECT * FROM email_campaigns ORDER BY created_at DESC");
    $stmt->execute();
    $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($campaigns);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
