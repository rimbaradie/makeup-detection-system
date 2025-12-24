<?php
include 'connect.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No campaign ID provided']);
    exit;
}

$campaignId = intval($_GET['id']);

try {
    $stmt = $conn->prepare("SELECT * FROM email_campaigns WHERE campaign_id = ?");
    $stmt->execute([$campaignId]);
    $campaign = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$campaign) {
        http_response_code(404);
        echo json_encode(['error' => 'Campaign not found']);
        exit;
    }

    echo json_encode($campaign);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
