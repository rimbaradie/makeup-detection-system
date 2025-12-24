<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['campaign_id'] ?? null;
    $title = $_POST['title'] ?? '';
    $status = $_POST['status'] ?? '';
    $last_sent = $_POST['last_sent'] ?? null;
    $opens_percentage = $_POST['opens_percentage'] ?? 0;
    $recipient = $_POST['recipient'] ?? '';

    if (!$id) {
        echo "Campaign ID missing";
        exit;
    }

    try {
        $stmt = $conn->prepare("UPDATE email_campaigns SET title = ?, status = ?, last_sent = ?, opens_percentage = ?, recipient = ? WHERE campaign_id = ?");
        $stmt->execute([$title, $status, $last_sent, $opens_percentage, $recipient, $id]);
        echo "Campaign updated successfully";
    } catch (PDOException $e) {
        echo "Update failed: " . $e->getMessage();
    }
}
?>
