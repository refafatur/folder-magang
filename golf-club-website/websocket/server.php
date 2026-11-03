<?php
require 'vendor/autoload.php';
require '../db/config.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\Factory;

class BookingNotification implements MessageComponentInterface {
    protected $clients;
    protected $pdo;
    protected $lastCheck;
    protected $loop;

    public function __construct($pdo, $loop) {
        $this->clients = new \SplObjectStorage;
        $this->pdo = $pdo;
        $this->lastCheck = time();
        $this->loop = $loop;
        
        // Check for new bookings every 5 seconds
        $this->loop->addPeriodicTimer(5, function () {
            $this->checkNewBookings();
        });
    }

    protected function checkNewBookings() {
        $stmt = $this->pdo->prepare("SELECT * FROM bookings WHERE created_at > ? ORDER BY created_at DESC");
        $stmt->execute([date('Y-m-d H:i:s', $this->lastCheck)]);
        $newBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($newBookings)) {
            foreach ($newBookings as $booking) {
                $this->broadcastBooking($booking);
            }
        }
        
        $this->lastCheck = time();
    }

    protected function broadcastBooking($booking) {
        try {
            $message = json_encode([
                'type' => 'new_booking',
                'data' => [
                    'id' => $booking['id'],
                    'booking_code' => $booking['booking_code'],
                    'name' => $booking['name'],
                    'email' => $booking['email'],
                    'phone' => $booking['phone'],
                    'booking_date' => $booking['booking_date'],
                    'booking_time' => $booking['booking_time'],
                    'players' => $booking['players'],
                    'status' => 'pending'
                ]
            ]);

            echo "Broadcasting new booking: {$booking['booking_code']}\n";
            
            foreach ($this->clients as $client) {
                $client->send($message);
            }
        } catch (\Exception $e) {
            echo "Error broadcasting: " . $e->getMessage() . "\n";
        }
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Broadcast the message to all connected clients
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

$loop = Factory::create();
$host = $argv[1] ?? '0.0.0.0';
$port = $argv[2] ?? '8001';

try {
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new BookingNotification($pdo, $loop)
            )
        ),
        $port,
        $host
    );
    
    echo "WebSocket server running on {$host}:{$port}\n";
    $server->run();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
