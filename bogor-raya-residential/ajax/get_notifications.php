<?php
session_start();
require_once '../includes/management.php';

header('Content-Type: application/json');

try {
    $conn = new mysqli("localhost", "root", "", "db_bogor");
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not logged in");
    }

    $sql = "SELECT 
        id,
        message,
        link,
        timestamp,
        CASE 
            WHEN TIMESTAMPDIFF(MINUTE, timestamp, NOW()) < 60 
                THEN CONCAT(TIMESTAMPDIFF(MINUTE, timestamp, NOW()), ' menit yang lalu')
            WHEN TIMESTAMPDIFF(HOUR, timestamp, NOW()) < 24 
                THEN CONCAT(TIMESTAMPDIFF(HOUR, timestamp, NOW()), ' jam yang lalu')
            WHEN TIMESTAMPDIFF(DAY, timestamp, NOW()) < 7
                THEN CONCAT(TIMESTAMPDIFF(DAY, timestamp, NOW()), ' hari yang lalu')
            ELSE DATE_FORMAT(timestamp, '%d %M %Y')
        END AS time_ago
        FROM tbl_user_notifications 
        WHERE user_id = ? 
        ORDER BY timestamp DESC 
        LIMIT 10";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Query preparation failed");
    }

    $stmt->bind_param("i", $_SESSION['user_id']);
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed");
    }

    $result = $stmt->get_result();
    $notifications = [];

    while ($row = $result->fetch_assoc()) {
        // Add color and icon based on message content
        if (strpos(strtolower($row['message']), 'pemesanan') !== false) {
            $row['color'] = 'primary';
            $row['icon'] = 'envelope';
        } elseif (strpos(strtolower($row['message']), 'sales') !== false) {
            $row['color'] = 'success';
            $row['icon'] = 'chart-line';
        } elseif (strpos(strtolower($row['message']), 'news') !== false) {
            $row['color'] = 'warning';
            $row['icon'] = 'newspaper';
        } else {
            $row['color'] = 'info';
            $row['icon'] = 'bell';
        }
        
        $notifications[] = $row;
    }

    echo json_encode($notifications);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

