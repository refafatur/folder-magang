<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['nama'])) {
    http_response_code(403);
    die(json_encode(['error' => 'Not authorized']));
}

require_once '../includes/management.php';

$conn = mysqli_connect("localhost", "root", "", "db_bogor");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $query = "SELECT u.*, m.role as role_name, m.permissions as role_permissions 
              FROM tbl_users u 
              LEFT JOIN tbl_management m ON u.level = m.id 
              WHERE u.id = ?";
              
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($user = mysqli_fetch_assoc($result)) {
        $user['role_info'] = [
            'permissions' => json_decode($user['role_permissions'], true)
        ];
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
    
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
