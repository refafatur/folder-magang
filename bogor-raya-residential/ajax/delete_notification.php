<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Not authenticated']));
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$notification_id = isset($data['notification_id']) ? intval($data['notification_id']) : 0;

if ($notification_id <= 0) {
    die(json_encode(['success' => false, 'message' => 'Invalid notification ID']));
}

// Connect to database
$conn = new mysqli("localhost", "root", "", "db_bogor");

// Prepare and execute delete query (only delete if notification belongs to current user)
$stmt = $conn->prepare("DELETE FROM tbl_user_notifications WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $notification_id, $_SESSION['user_id']);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
