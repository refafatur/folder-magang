<?php
function getRolePermissions($conn, $level) {
    $stmt = mysqli_prepare($conn, "SELECT permissions FROM tbl_management WHERE id = ?");
    if (!$stmt) {
        return [];
    }
    
    mysqli_stmt_bind_param($stmt, "i", $level);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        mysqli_stmt_close($stmt);
        return [];
    }
    
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return json_decode($row['permissions'], true) ?: [];
}

function getNamaRole($conn, $level) {
    $stmt = mysqli_prepare($conn, "SELECT role FROM tbl_management WHERE id = ?");
    if (!$stmt) {
        return 'Tidak Diketahui';
    }
    
    mysqli_stmt_bind_param($stmt, "i", $level);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        mysqli_stmt_close($stmt);
        return 'Tidak Diketahui';
    }
    
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return $row['role'] ?: 'Tidak Diketahui';
}
?>
