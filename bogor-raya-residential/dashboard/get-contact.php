<?php
$conn = mysqli_connect("localhost", "root", "", "db_bogor");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = mysqli_prepare($conn, "SELECT * FROM tbl_contacts WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    echo json_encode($user);
}

mysqli_close($conn);
