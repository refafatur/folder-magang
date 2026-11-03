<?php
require_once '../db/config.php';
session_start();

header('Content-Type: application/json');

// Initialize shown bookings array in session if not exists
if (!isset($_SESSION['shown_bookings'])) {
    $_SESSION['shown_bookings'] = [];
}

// Get new bookings that haven't been shown
$stmt = $pdo->prepare("
    SELECT * FROM bookings 
    WHERE status = 'pending'
    AND created_at > ? 
    ORDER BY created_at DESC 
    LIMIT 1
");

$lastCheck = date('Y-m-d H:i:s', strtotime('-1 minute'));
$stmt->execute([$lastCheck]);
$newBooking = $stmt->fetch(PDO::FETCH_ASSOC);

$response = ['hasNew' => false];

if ($newBooking && !in_array($newBooking['id'], $_SESSION['shown_bookings'])) {
    // Add to shown bookings
    $_SESSION['shown_bookings'][] = $newBooking['id'];
    
    // Keep only last 100 shown bookings
    if (count($_SESSION['shown_bookings']) > 100) {
        array_shift($_SESSION['shown_bookings']);
    }
    
    $response = [
        'hasNew' => true,
        'data' => $newBooking
    ];
}

echo json_encode($response);
