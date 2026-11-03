<?php
session_start();
require_once '../includes/auth_helper.php';
checkUserStatus();

$conn = mysqli_connect("localhost", "root", "", "db_bogor");
$id = intval($_GET['id']);

$query = "SELECT s.*, src.nama as sumber_nama 
          FROM tbl_sales s
          LEFT JOIN tbl_sumber src ON s.id_sumber = src.id
          WHERE s.id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$sales = mysqli_fetch_assoc($result);

header('Content-Type: application/json');
echo json_encode($sales);
