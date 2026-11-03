<?php
session_start();
require_once '../includes/auth_helper.php';
checkUserStatus();

$conn = mysqli_connect("localhost", "root", "", "db_bogor");
$response = ['success' => false];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $followup_id = intval($_POST['id']);
    
    $query = "DELETE FROM tbl_follow_up WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $followup_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['notification'] = [
            'type' => 'warning',
            'icon' => 'trash',
            'title' => 'Berhasil!',
            'message' => 'Follow up berhasil dihapus'
        ];
        $response['success'] = true;
    }
    mysqli_stmt_close($stmt);
}

header('Content-Type: application/json');
echo json_encode($response);
