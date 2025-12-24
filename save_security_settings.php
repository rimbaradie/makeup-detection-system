<?php
require 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "No data received"]);
    exit;
}

$twoFactor = isset($data['two_factor']) ? 1 : 0;
$strictPass = isset($data['strict_password']) ? 1 : 0;
$timeout = isset($data['session_timeout']) ? (int)$data['session_timeout'] : 60;

// Update each setting
$settings = [
    'two_factor_auth' => $twoFactor,
    'strict_password' => $strictPass,
    'session_timeout' => $timeout
];

foreach ($settings as $key => $value) {
    $stmt = $conn->prepare("REPLACE INTO Settings (setting_key, setting_value) VALUES (?, ?)");
    $stmt->execute([$key, $value]);
}

echo json_encode(["success" => true, "message" => "Settings updated successfully"]);
?>
