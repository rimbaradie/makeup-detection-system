<?php
require 'connect.php';

// Fetch all users with role 'Dermatologist' sorted by fname and lname
$stmt = $conn->prepare("SELECT * FROM Users WHERE role = 'Dermatologist' ORDER BY fname ASC, lname ASC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $u) {
    $fname = htmlspecialchars($u['fname']);
    $lname = htmlspecialchars($u['lname']);
    $fullName = trim($fname . ' ' . $lname);
    $specialty = htmlspecialchars($u['specialty']);
    $profile = htmlspecialchars($u['profile']);
    $status = isset($u['status']) ? htmlspecialchars($u['status']) : 'Pending';
    $statusClass = $status === 'Approved' ? 'status-active' : 'status-pending';
    $nextAvailable = !empty($u['next_available']) ? htmlspecialchars($u['next_available']) : 'N/A';

    echo '<div class="derm-card">';
    echo    '<img src="pic/' . ($profile ?: 'default.png') . '" alt="' . $fullName . '" class="derm-avatar">';
    echo    '<div class="derm-details">';
    echo        '<h3>' . $fullName . '</h3>';
    echo        '<p>Specialty: ' . $specialty . '</p>';
    echo        '<p>Status: <span class="' . $statusClass . '">' . $status . '</span></p>';
    echo        '<p>Next Available: ' . $nextAvailable . '</p>';
    echo        '<div class="derm-actions">';
    echo          '<button class="btn btn-sm btn-info tooltip view-derm-btn" data-tooltip="View Profile" data-id="' . $u['user_id'] . '"><i class="fas fa-eye"></i></button>';
    echo          '<button class="btn btn-sm btn-warning tooltip edit-derm-btn" data-tooltip="Edit Profile" data-id="' . $u['user_id'] . '"><i class="fas fa-edit"></i></button>';
    echo          '<button class="btn btn-sm btn-danger tooltip delete-derm-btn" data-tooltip="Delete Profile" data-id="' . $u['user_id'] . '"><i class="fas fa-trash"></i></button>';
    echo        '</div>';
    echo    '</div>';
    echo '</div>';
}
?>