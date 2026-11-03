<?php
require_once 'includes/notifications.php';

$conn = new mysqli("localhost", "root", "", "db_bogor");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Test adding a notification
$result = addNotification(
    $conn,
    "Test notification",
    "bell",
    "primary",
    "#"
);

if ($result) {
    echo "Notification added successfully!";
} else {
    echo "Error adding notification!";
}

// Test specific event notification
triggerNotification($conn, 'new_product', [
    'name' => 'Test Product'
]);

$conn->close();
