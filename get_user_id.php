<?php
session_start();
require 'connect.php';
header('Content-Type: application/json');
echo json_encode([
    'user_id' => $_SESSION['user_id'] ?? null
]);
