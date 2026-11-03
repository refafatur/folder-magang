<?php
// Start WebSocket server as background process
$cmd = 'start /B php websocket/server.php ' . $_SERVER['SERVER_NAME'] . ' ' . $_SERVER['SERVER_PORT'];
pclose(popen($cmd, 'r'));

// Handle WebSocket connections
if (isset($_SERVER['HTTP_UPGRADE']) && $_SERVER['HTTP_UPGRADE'] == 'websocket') {
    require __DIR__ . '/websocket/server.php';
    exit;
}

// Regular PHP request handling
require __DIR__ . $_SERVER['REQUEST_URI'];
