<?php
header('Content-Type: application/json');
require_once '../db/config.php';

try {
    $query = "SELECT * FROM bookings ORDER BY booking_date DESC, booking_time DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($bookings);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
