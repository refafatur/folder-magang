<?php
require_once __DIR__ . '/../db/config.php';

class BookingHandler {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getLatestBookings($lastId = 0) {
        $stmt = $this->pdo->prepare("SELECT * FROM bookings WHERE id > ? ORDER BY id DESC");
        $stmt->execute([$lastId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function broadcastNewBooking($booking) {
        return json_encode([
            'type' => 'new_booking',
            'data' => $booking
        ]);
    }
}
