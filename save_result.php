<?php
include 'connect.php';

if (isset($_POST['analysis_result'])) {
    $result = $_POST['analysis_result'];

    $stmt = $conn->prepare("INSERT INTO SkinAnalysis (analysis_result) VALUES (:result)");
    $stmt->execute([':result' => $result]);

    echo "Result saved!";
}
?>
