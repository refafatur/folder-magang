<?php
session_start();
require_once '../includes/auth_helper.php';
checkUserStatus();

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Sales ID not provided']);
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "db_bogor");
if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$sales_id = intval($_GET['id']);

// Get sales history with user and unit information
$query = "SELECT f.*, u.nama as user_nama, un.name as unit_name 
          FROM tbl_follow_up f
          LEFT JOIN tbl_users u ON f.id_user = u.id
          LEFT JOIN tbl_unit un ON f.id_unit = un.id
          WHERE f.id_sales = ?
          ORDER BY f.tgl_follow_up DESC";

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Query preparation failed']);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $sales_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$history = [];
while ($row = mysqli_fetch_assoc($result)) {
    $history[] = [
        'id' => $row['id'],
        'media' => htmlspecialchars($row['media']),
        'hasil' => htmlspecialchars($row['hasil']),
        'tgl_follow_up' => $row['tgl_follow_up'],
        'user_nama' => htmlspecialchars($row['user_nama']),
        'unit_name' => $row['unit_name'] ? htmlspecialchars($row['unit_name']) : null
    ];
}

mysqli_close($conn);
echo json_encode($history);
