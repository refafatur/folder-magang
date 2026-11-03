<?php
require 'db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate inputs
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone']) || 
            empty($_POST['date']) || empty($_POST['time']) || empty($_POST['players'])) {
            throw new Exception("All fields are required");
        }

        // Validate date and time format
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $_POST['date']) || 
            !preg_match("/^\d{2}:\d{2}:\d{2}$/", $_POST['time'])) {
            throw new Exception("Invalid date or time format");
        }

        // Check if date is within allowed range
        $minDate = date('Y-m-d');
        $maxDate = date('Y-m-d', strtotime('+30 days'));
        if ($_POST['date'] < $minDate || $_POST['date'] > $maxDate) {
            throw new Exception("Selected date is not within allowed range");
        }

        // Check availability again before processing
        $query = "SELECT COUNT(*) as booked FROM bookings 
                  WHERE booking_date = ? AND booking_time = ? 
                  AND status != 'cancelled'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $_POST['date'], $_POST['time']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['booked'] >= 4) {
            throw new Exception("Selected time slot is no longer available");
        }

        // Continue with booking process
        $booking_code = 'BK' . date('ymd') . rand(1000, 9999);
        
        $stmt = $conn->prepare("INSERT INTO bookings (booking_code, name, email, phone, booking_date, booking_time, players, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        
        $stmt->bind_param("ssssssi", 
            $booking_code,
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['date'],
            $_POST['time'],
            $_POST['players']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to process booking");
        }

        // Send email confirmation
        $to = $_POST['email'];
        $subject = "Golf Booking Confirmation - " . $booking_code;
        $message = "Thank you for booking with us!\n\n";
        $message .= "Booking Details:\n";
        $message .= "Booking Code: " . $booking_code . "\n";
        $message .= "Date: " . $_POST['date'] . "\n";
        $message .= "Time: " . $_POST['time'] . "\n";
        $message .= "Players: " . $_POST['players'] . "\n";
        
        mail($to, $subject, $message);
        
        header("Location: booking-confirmation.php?code=" . $booking_code);
        exit;

    } catch (Exception $e) {
        header("Location: booking.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}
