<?php
require 'connect.php';

$sql = "SELECT 
            CASE 
                WHEN TIMESTAMPDIFF(YEAR, created_at, CURDATE()) BETWEEN 18 AND 24 THEN '18-24'
                WHEN TIMESTAMPDIFF(YEAR, created_at, CURDATE()) BETWEEN 25 AND 34 THEN '25-34'
                WHEN TIMESTAMPDIFF(YEAR, created_at, CURDATE()) BETWEEN 35 AND 44 THEN '35-44'
                WHEN TIMESTAMPDIFF(YEAR, created_at, CURDATE()) BETWEEN 45 AND 54 THEN '45-54'
                ELSE '55+'
            END AS age_range,
            COUNT(*) AS user_count
        FROM Users
        GROUP BY age_range";

$stmt = $conn->prepare($sql);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>