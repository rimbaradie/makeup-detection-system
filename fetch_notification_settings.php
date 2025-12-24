<?php
require 'connect.php';

// Fetch global notification settings (assuming 1 row for all admins)
$stmt = $conn->query("SELECT notify_new_order, notify_new_user, notify_low_stock FROM NotificationSettings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div id="notifications-settings" class="tab-content fade-in">
    <h3>Admin Notification Preferences</h3>

    <div class="form-group checkbox-group">
        <label>Receive notifications for:</label>
    </div>

    <div class="form-group checkbox-group">
        <input type="checkbox" id="notify-new-order" <?php if ($settings['notify_new_order']) echo 'checked'; ?>>
        <label for="notify-new-order">New Orders</label>
    </div>

    <div class="form-group checkbox-group">
        <input type="checkbox" id="notify-new-user" <?php if ($settings['notify_new_user']) echo 'checked'; ?>>
        <label for="notify-new-user">New User Registrations</label>
    </div>

    <div class="form-group checkbox-group">
        <input type="checkbox" id="notify-low-stock" <?php if ($settings['notify_low_stock']) echo 'checked'; ?>>
        <label for="notify-low-stock">Low Product Stock Alerts</label>
    </div>

    <button id="save-notification-btn" class="btn btn-primary animate-btn">
        <i class="fas fa-save"></i> Save Notification Preferences
    </button>
</div>
