<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update user status to inactive
    if (isset($_SESSION['user_id'])) {
        $conn = mysqli_connect("localhost", "root", "", "db_bogor");
        $update_query = "UPDATE tbl_users SET status = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }

    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>