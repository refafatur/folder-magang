<?php

function getUserPermissions($conn, $userId) {
    $stmt = mysqli_prepare($conn, "SELECT m.permissions 
                                 FROM tbl_management m 
                                 INNER JOIN tbl_users u ON u.level = m.id 
                                 WHERE u.id = ?");
    if (!$stmt) {
        return array(); // Return empty array if query fails
    }
    
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        return array(); // Return empty array if no results
    }
    
    $row = mysqli_fetch_assoc($result);
    $permissions = json_decode($row['permissions'], true);
    
    return $permissions ?: array(); // Return decoded permissions or empty array
}

function hasPermission($permissions, $module, $action) {
    if (empty($permissions) || !is_array($permissions)) {
        return false;
    }
    
    return isset($permissions[$module][$action]) && $permissions[$module][$action] === 1;
}

function checkPermission($permissions, $module, $action) {
    if (!hasPermission($permissions, $module, $action)) {
        if ($_SESSION['level'] != '0') { // Allow admin to bypass
            http_response_code(403);
            die("Access denied");
        }
    }
}
?>