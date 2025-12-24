<?php
if (isset($_POST['image_data'])) {
    $imageData = $_POST['image_data'];
    $img = base64_decode($imageData);
    file_put_contents("temp.jpg", $img);

    $apiKey = "fmqs2u76wmY0POvSF3xz4JnSF9M18EAj";
    $apiSecret = "FoLQS6sxqmHmYdt3UE68_Wi2PbmuKFN6";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api-us.faceplusplus.com/facepp/v1/skinanalyze",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'image_file' => new CURLFile("temp.jpg"),
        ]
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    // تأكد أن الرد ليس فارغ
    if (!$response) {
        echo json_encode(['error' => 'Empty response from Face++']);
        exit;
    }

    $data = json_decode($response, true);

    // تأكد أنه فيه result
    if (isset($data['result'])) {
        echo json_encode($data); // يرجعه كما هو إلى الأندرويد
    } else {
        echo json_encode(['error' => 'No result found', 'raw' => $data]);
    }

} else {
    echo json_encode(['error' => 'No image_data received']);
}
