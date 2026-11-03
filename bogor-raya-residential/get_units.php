<?php
$conn = mysqli_connect("localhost", "root", "", "db_bogor");

$product_id = $_GET['product_id'] ?? 0;
$query = "SELECT * FROM tbl_unit WHERE product_id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$units = [];
while ($row = mysqli_fetch_assoc($result)) {
    $units[] = $row;
}

header('Content-Type: application/json');
echo json_encode($units);
