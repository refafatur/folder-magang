<?php
session_start();
require_once '../includes/auth_helper.php';
checkUserStatus();

if (!isset($_SESSION['nama'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "db_bogor");
$input = json_decode(file_get_contents('php://input'), true);

$where_conditions = ["1=1"];
$params = [];
$types = "";

// Search filter
if (!empty($input['search'])) {
    $where_conditions[] = "(s.nama LIKE ? OR s.no_hp LIKE ? OR s.email LIKE ?)";
    $search_term = "%" . $input['search'] . "%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}

// Source filter
if (!empty($input['source'])) {
    $where_conditions[] = "s.id_sumber = ?";
    $params[] = $input['source'];
    $types .= "i";
}

// Status filter
if (!empty($input['status'])) {
    if ($input['status'] === 'new') {
        $where_conditions[] = "NOT EXISTS (SELECT 1 FROM tbl_follow_up f WHERE f.id_sales = s.id)";
    } else if ($input['status'] === 'followup') {
        $where_conditions[] = "EXISTS (SELECT 1 FROM tbl_follow_up f WHERE f.id_sales = s.id)";
    }
}

$query = "SELECT s.*, src.nama as sumber_nama, u.nama as user_nama,
          (SELECT MAX(tgl_follow_up) FROM tbl_follow_up WHERE id_sales = s.id) as last_followup 
          FROM tbl_sales s 
          LEFT JOIN tbl_sumber src ON s.id_sumber = src.id
          LEFT JOIN tbl_users u ON s.id_user = u.id
          WHERE " . implode(" AND ", $where_conditions) . "
          ORDER BY s.tgl_create DESC";

$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$sales = [];
$permissions = json_decode($_SESSION['permissions'], true);

while ($row = mysqli_fetch_assoc($result)) {
    $row['can_edit'] = isset($permissions['sales']['edit']) && $permissions['sales']['edit'] == 1;
    $row['can_delete'] = isset($permissions['sales']['delete']) && $permissions['sales']['delete'] == 1;
    $sales[] = $row;
}

header('Content-Type: application/json');
echo json_encode($sales);
