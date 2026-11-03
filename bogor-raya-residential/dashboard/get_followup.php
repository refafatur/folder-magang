<?php
session_start();
require_once '../includes/auth_helper.php';
checkUserStatus();

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Follow up ID not provided']);
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "db_bogor");
if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$followup_id = intval($_GET['id']);

$query = "SELECT * FROM tbl_follow_up WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $followup_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$followup = mysqli_fetch_assoc($result);

mysqli_close($conn);
echo json_encode($followup);
