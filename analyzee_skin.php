<?php
$api_key = 'fmqs2u76wmY0POvSF3xz4JnSF9M18EAj';
$api_secret = 'FoLQS6sxqmHmYdt3UE68_Wi2PbmuKFN6';

// Determine which input is being used
if (isset($_FILES['skin_image'])) {
    $imagePath = $_FILES['skin_image']['tmp_name'];
} elseif (isset($_POST['image_data'])) {
    $imageData = str_replace('data:image/jpeg;base64,', '', $_POST['image_data']);
    $imageData = base64_decode($imageData);
    $imagePath = tempnam(sys_get_temp_dir(), 'skin') . '.jpg';
    file_put_contents($imagePath, $imageData);
} else {
    echo "No image input provided.";
    exit;
}

// Call Face++ API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api-us.faceplusplus.com/facepp/v1/skinanalyze",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'api_key' => $api_key,
        'api_secret' => $api_secret,
        'image_file' => new CURLFile($imagePath),
    ],
]);
$response = curl_exec($curl);
$error = curl_error($curl);
curl_close($curl);

// Clean up if image was base64
if (isset($_POST['image_data'])) {
    unlink($imagePath);
}

if ($error) {
    echo "cURL Error: " . $error;
    exit;
}

// Handle API response
$result = json_decode($response, true);
if (!isset($result['result'])) {
    echo "<pre>API response:\n" . htmlspecialchars($response) . "</pre>";
    echo "<p>No result found.</p>";
    exit;
}

$skinData = $result['result'];
echo "<h2>Skin Analysis Summary</h2>";

function interpret($name, $data) {
    $value = $data['value'];
    $confidence = round($data['confidence'] * 100, 2);
    if (in_array($name, ['Left Eyelids', 'Right Eyelids'])) {
        $levels = [0 => 'Not detected', 1 => 'Mild', 2 => 'Severe'];
        $status = $levels[$value] ?? "Unknown";
    } else {
        $status = $value == 1 ? "Detected" : "Not detected";
    }
    return "<li><strong>$name</strong>: $status (Confidence: $confidence%)</li>";
}

echo "<ul>";
echo interpret('Nasolabial Fold', $skinData['nasolabial_fold']);
echo interpret('Left Eyelids', $skinData['left_eyelids']);
echo interpret('Right Eyelids', $skinData['right_eyelids']);
echo interpret('Eye Pouch', $skinData['eye_pouch']);
echo interpret('Forehead Wrinkle', $skinData['forehead_wrinkle']);
echo interpret('Skin Spot', $skinData['skin_spot']);
echo interpret('Acne', $skinData['acne']);
echo interpret('Dark Circle', $skinData['dark_circle']);
echo interpret('Crows Feet', $skinData['crows_feet']);
echo interpret('Blackhead', $skinData['blackhead']);
echo interpret('Mole', $skinData['mole']);
echo "</ul>";

$skinTypes = [0 => "Oily", 1 => "Dry", 2 => "Combination", 3 => "Normal"];
$skinTypeCode = $skinData['skin_type']['skin_type'] ?? -1;
if ($skinTypeCode >= 0) {
    echo "<p><strong>Skin Type:</strong> " . ($skinTypes[$skinTypeCode] ?? "Unknown") . "</p>";
}
?>