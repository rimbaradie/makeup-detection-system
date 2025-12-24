<?php
require 'connect.php';

try {
    // This query assumes you have a `created_at` column in your Users table storing registration datetime
    // Group registrations by week number and year, then count users per week
    $stmt = $conn->prepare("
        SELECT 
            YEAR(created_at) AS year,
            WEEK(created_at, 1) AS week,
            COUNT(*) AS registrations
        FROM Users
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
        GROUP BY year, week
        ORDER BY year DESC, week DESC
        LIMIT 4
    ");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data as arrays of labels and counts for the last 4 weeks
    $labels = [];
    $data = [];

    // Reverse the array to get chronological order (oldest first)
    $results = array_reverse($results);

    foreach ($results as $row) {
        $labels[] = "Week " . $row['week'] . " - " . $row['year'];
        $data[] = (int)$row['registrations'];
    }

    echo json_encode(['labels' => $labels, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}