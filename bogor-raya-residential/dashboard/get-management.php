<?php
session_start();
if (!isset($_SESSION['nama']) || $_SESSION['level'] != '0') {
    http_response_code(403);
    die("Access denied");
}

$conn = mysqli_connect("localhost", "root", "", "db_bogor");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM tbl_management WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $management = mysqli_fetch_assoc($result);
    
    header('Content-Type: application/json');
    echo json_encode($management);
}
?>
