<?php
session_start();
require_once '../includes/auth_helper.php';
checkUserStatus();

if (!isset($_SESSION['nama'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "db_bogor");

// Get units
$query = "SELECT id, name FROM tbl_unit ORDER BY name";  // Removed WHERE status='1' since column doesn't exist
$result = mysqli_query($conn, $query);

if (!$result) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    exit();
}

$units = [];
while ($row = mysqli_fetch_assoc($result)) {
    $units[] = [
        'id' => $row['id'],
        'name' => $row['name']
    ];
}

header('Content-Type: application/json');
echo json_encode($units);
