<?php
include 'connect.php'; // Use your PDO connection

if (isset($_POST['image_data'])) {
    $image_data = $_POST['image_data'];
    $image_data = str_replace('data:image/png;base64,', '', $image_data);
    $image_data = str_replace(' ', '+', $image_data);
    $image = base64_decode($image_data);

    // Create GD image from raw data (in memory)
    $img = imagecreatefromstring($image);
    if (!$img) {
        echo "Invalid image.";
        exit;
    }

    $width = imagesx($img);
    $height = imagesy($img);
    $totalBrightness = 0;
    $pixelCount = 0;

    // Sample every 10 pixels for performance
    for ($x = 0; $x < $width; $x += 10) {
        for ($y = 0; $y < $height; $y += 10) {
            $rgb = imagecolorat($img, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            $brightness = ($r + $g + $b) / 3;
            $totalBrightness += $brightness;
            $pixelCount++;
        }
    }

    imagedestroy($img);

    $avgBrightness = $totalBrightness / $pixelCount;

    // Basic skin analysis based on brightness
    if ($avgBrightness > 200) {
        $analysis = "Your skin appears oily or shiny.";
    } elseif ($avgBrightness > 100) {
        $analysis = "Your skin appears normal.";
    } else {
        $analysis = "Your skin appears dry or dull.";
    }

    // Optionally store the result in the database
    $stmt = $conn->prepare("INSERT INTO SkinAnalysis (analysis_result) VALUES (:result)");
    $stmt->execute([':result' => $analysis]);

    echo "Live analysis: " . $analysis;
}
?>
