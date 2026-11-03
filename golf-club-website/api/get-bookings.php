<?php
header('Content-Type: application/json');
require_once '../db/config.php';

try {
    $sort = $_GET['sort'] ?? '';
    $query = "SELECT * FROM bookings";
    
    switch($sort) {
        case 'name_asc':
            $query .= " ORDER BY name ASC";
            break;
        case 'name_desc':
            $query .= " ORDER BY name DESC";
            break;
        case 'date_newest':
            $query .= " ORDER BY booking_date DESC, booking_time DESC";
            break;
        case 'date_oldest':
            $query .= " ORDER BY booking_date ASC, booking_time ASC";
            break;
        default:
            $query .= " ORDER BY booking_date DESC, booking_time DESC";
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $bookings]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
