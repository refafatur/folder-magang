<?php
function checkUserStatus() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }

    $conn = mysqli_connect("localhost", "root", "", "db_bogor");
    $user_id = $_SESSION['user_id'];
    $query = "SELECT status FROM tbl_users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_close($conn);

    if ($user['status'] == 0) {
        session_unset();
        session_destroy();
        header("Location: ../login.php?error=account_inactive");
        exit();
    }
}
