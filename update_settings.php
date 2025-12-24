<?php
require 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$site_name = $data['site_name'] ?? '';
$contact_email = $data['contact_email'] ?? '';
$currency = $data['currency'] ?? '';

if ($site_name && $contact_email && $currency) {
    $stmt = $conn->prepare("UPDATE SiteSettings SET site_name = ?, contact_email = ?, currency = ? WHERE id = 1");
    $result = $stmt->execute([$site_name, $contact_email, $currency]);

    echo json_encode([
        'status' => $result ? 'success' : 'error',
        'message' => $result ? 'Settings updated.' : 'Update failed.'
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
}
?>
