<?php
function addNotification($conn, $message, $icon = 'bell', $color = 'primary', $link = '#') {
    // Get all users
    $sql = "SELECT id FROM tbl_users WHERE level IN ('0', '1', '2')"; // Only for valid user levels
    $result = $conn->query($sql);
    
    if (!$result) {
        error_log("Error fetching users: " . $conn->error);
        return false;
    }

    // Prepare notification insert statement
    $sql = "INSERT INTO tbl_user_notifications (user_id, message, icon, color, link) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Error preparing statement: " . $conn->error);
        return false;
    }

    // Add notification for each user
    $success = true;
    while ($user = $result->fetch_assoc()) {
        $stmt->bind_param("issss", $user['id'], $message, $icon, $color, $link);
        if (!$stmt->execute()) {
            error_log("Error inserting notification for user {$user['id']}: " . $stmt->error);
            $success = false;
        }
    }

    $stmt->close();
    return $success;
}

// Helper function to automatically add notifications for various events
function triggerNotification($conn, $type, $data = []) {
    switch ($type) {
        case 'new_product':
            addNotification(
                $conn,
                "New product added: " . $data['nama'],
                "building",
                "warning",
                "dashboard/produk.php"
            );
            break;
            
        case 'new_news':
            addNotification(
                $conn,
                "New news article: " . $data['nama'],
                "newspaper",
                "info",
                "dashboard/news.php"
            );
            break;
            
        case 'new_order':
            addNotification(
                $conn,
                "New order received from: " . $data['nama'],
                "shopping-cart",
                "success",
                "dashboard/contact.php"
            );
            break;
            
        case 'new_user':
            addNotification(
                $conn,
                "New user registered: " . $data['nama'],
                "user",
                "primary",
                "dashboard/tambah-user.php"
            );
            break;

        case 'edit_product':
            addNotification(
                $conn,
                "Product updated: " . $data['nama'],
                "pencil",
                "warning",
                "dashboard/produk.php"
            );
            break;
            
        case 'delete_product':
            addNotification(
                $conn,
                "Product deleted: " . $data['nama'],
                "trash",
                "danger",
                "dashboard/produk.php"
            );
            break;

        case 'edit_news':
            addNotification(
                $conn,
                "News article updated: " . $data['nama'],
                "pencil",
                "info",
                "dashboard/news.php"
            );
            break;
            
        case 'delete_news':
            addNotification(
                $conn,
                "News article deleted: " . $data['nama'],
                "trash",
                "danger",
                "dashboard/news.php"
            );
            break;

        case 'edit_user':
            addNotification(
                $conn,
                "User updated: " . $data['nama'],
                "user-edit",
                "primary",
                "dashboard/tambah-user.php"
            );
            break;
            
        case 'delete_user':
            addNotification(
                $conn,
                "User deleted: " . $data['nama'],
                "user-minus",
                "danger",
                "dashboard/tambah-user.php"
            );
            break;

        case 'edit_contact':
            addNotification(
                $conn,
                "Contact updated: " . $data['nama'],
                "address-book",
                "info",
                "dashboard/contact.php"
            );
            break;
            
        case 'delete_contact':
            addNotification(
                $conn,
                "Contact deleted: " . $data['nama'],
                "trash",
                "danger", 
                "dashboard/contact.php"
            );
            break;

        case 'new_testimonial':
            addNotification(
                $conn,
                "New testimonial added from: " . $data['nama'],
                "comment",
                "success",
                "dashboard/testimoni.php"
            );
            break;
            
        case 'edit_testimonial':
            addNotification(
                $conn,
                "Testimonial updated from: " . $data['nama'],
                "edit",
                "info",
                "dashboard/testimoni.php"
            );
            break;
            
        case 'delete_testimonial':
            addNotification(
                $conn,
                "Testimonial deleted from: " . $data['nama'],
                "trash",
                "danger",
                "dashboard/testimoni.php"
            );
            break;

        case 'new_sales':
            addNotification(
                $conn,
                "New lead added: " . $data['nama'],
                "user-plus",
                "success",
                "dashboard/sales.php"
            );
            break;
            
        case 'edit_sales':
            addNotification(
                $conn,
                "Lead updated: " . $data['nama'],
                "user-edit",
                "info",
                "dashboard/sales.php"
            );
            break;
            
        case 'delete_sales':
            addNotification(
                $conn,
                "Lead deleted: " . $data['nama'],
                "user-minus",
                "danger",
                "dashboard/sales.php"
            );
            break;
            
        case 'new_followup':
            addNotification(
                $conn,
                "New follow-up for: " . $data['nama'],
                "phone",
                "primary",
                "dashboard/sales.php"
            );
            break;
    }
}
