<?php
session_start();
require_once '../includes/auth_helper.php';
checkUserStatus();

if (!isset($_SESSION['nama']) || !isset($_GET['id_sales'])) {
    exit(json_encode(['error' => 'Invalid request']));
}

$conn = mysqli_connect("localhost", "root", "", "db_bogor");
$id_sales = intval($_GET['id_sales']);

$query = "SELECT f.*, u.nama as user_nama, un.name as unit_name 
          FROM tbl_follow_up f
          LEFT JOIN tbl_users u ON f.id_user = u.id
          LEFT JOIN tbl_unit un ON f.id_unit = un.id
          WHERE f.id_sales = ?
          ORDER BY f.tgl_follow_up DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_sales);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$history = array();
while ($row = mysqli_fetch_assoc($result)) {
    $history[] = $row;
}

echo json_encode($history);
