<?php
session_start();
require_once '../includes/auth_helper.php';
require_once '../includes/notifications.php';  // Add this line
checkUserStatus();

if (!isset($_SESSION['nama'])) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "db_bogor");
$permissions = json_decode($_SESSION['permissions'], true);

// Check if user has permission to manage sales
if (!isset($permissions['sales']['view']) || $permissions['sales']['view'] != 1) {
    header("Location: ../dashboard.php");
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_source'])) {
        if (!isset($permissions['sales']['add']) || $permissions['sales']['add'] != 1) {
            echo "Permission denied";
            exit();
        }
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $id_user = $_SESSION['user_id'];
        
        $query = "INSERT INTO tbl_sumber (nama, id_user) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $nama, $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        $_SESSION['notification'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'title' => 'Berhasil!',
            'message' => 'Sumber berhasil ditambahkan'
        ];
        header("Location: sales.php");
        exit();
    }

    if (isset($_POST['edit_source'])) {
        if (!isset($permissions['sales']['edit']) || $permissions['sales']['edit'] != 1) {
            echo "Permission denied";
            exit();
        }
        $source_id = intval($_POST['source_id']);
        $source_name = mysqli_real_escape_string($conn, $_POST['source_name']);

        $query = "UPDATE tbl_sumber SET nama = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $source_name, $source_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['notification'] = [
            'type' => 'success',
            'icon' => 'edit',
            'title' => 'Berhasil!',
            'message' => 'Sumber berhasil diperbarui'
        ];
        header("Location: sales.php");
        exit();
    }

    if (isset($_POST['delete_source'])) {
        if (!isset($permissions['sales']['delete']) || $permissions['sales']['delete'] != 1) {
            echo "Permission denied";
            exit();
        }
        $source_id = intval($_POST['delete_source']);

        $query = "DELETE FROM tbl_sumber WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $source_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['notification'] = [
            'type' => 'warning',
            'icon' => 'trash',
            'title' => 'Berhasil!',
            'message' => 'Sumber berhasil dihapus'
        ];
        header("Location: sales.php");
        exit();
    }

    if (isset($_POST['add_sales'])) {
        if (!isset($permissions['sales']['add']) || $permissions['sales']['add'] != 1) {
            echo "Permission denied";
            exit();
        }
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $id_sumber = intval($_POST['id_sumber']);
        $pekerjaan = mysqli_real_escape_string($conn, $_POST['pekerjaan']);
        $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $id_user = $_SESSION['user_id'];

        $query = "INSERT INTO tbl_sales (nama, id_sumber, pekerjaan, no_hp, email, id_user) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sisssi", $nama, $id_sumber, $pekerjaan, $no_hp, $email, $id_user);
        if (mysqli_stmt_execute($stmt)) {
            triggerNotification($conn, 'new_sales', ['nama' => $nama]);
            $_SESSION['notification'] = [
                'type' => 'success',
                'icon' => 'user-plus',
                'title' => 'Berhasil!',
                'message' => 'Calon pembeli berhasil ditambahkan'
            ];
        }
        mysqli_stmt_close($stmt);
        
        header("Location: sales.php");
        exit();
    }

    if (isset($_POST['add_followup'])) {
        if (!isset($permissions['sales']['add']) || $permissions['sales']['add'] != 1) {
            echo "Permission denied";
            exit();
        }
        $id_sales = intval($_POST['id_sales']);
        $media = mysqli_real_escape_string($conn, $_POST['media']);
        $hasil = mysqli_real_escape_string($conn, $_POST['hasil']);
        $tgl_follow_up = mysqli_real_escape_string($conn, $_POST['tgl_follow_up']);
        $id_user = $_SESSION['user_id'];
        $id_unit = !empty($_POST['id_unit']) ? intval($_POST['id_unit']) : null;

        $query = "INSERT INTO tbl_follow_up (id_sales, id_user, id_unit, media, hasil, tgl_follow_up) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iiisss", $id_sales, $id_user, $id_unit, $media, $hasil, $tgl_follow_up);
        if(mysqli_stmt_execute($stmt)) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'icon' => 'check-circle',
                'title' => 'Berhasil!',
                'message' => 'Follow up berhasil ditambahkan'
            ];
        }
        mysqli_stmt_close($stmt);
        
        header("Location: sales.php");
        exit();
    }

    if (isset($_POST['edit_followup'])) {
        if (!isset($permissions['sales']['edit']) || $permissions['sales']['edit'] != 1) {
            echo "Permission denied";
            exit();
        }
        
        $followup_id = intval($_POST['followup_id']);
        $media = mysqli_real_escape_string($conn, $_POST['media']);
        $hasil = mysqli_real_escape_string($conn, $_POST['hasil']);
        $tgl_follow_up = mysqli_real_escape_string($conn, $_POST['tgl_follow_up']);
        $id_unit = !empty($_POST['id_unit']) ? intval($_POST['id_unit']) : null;

        $query = "UPDATE tbl_follow_up SET media = ?, hasil = ?, tgl_follow_up = ?, id_unit = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssis", $media, $hasil, $tgl_follow_up, $id_unit, $followup_id);
        if(mysqli_stmt_execute($stmt)) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'icon' => 'edit',
                'title' => 'Berhasil!',
                'message' => 'Follow up berhasil diperbarui'
            ];
        }
        mysqli_stmt_close($stmt);
        
        header("Location: sales.php");
        exit();
    }

    if (isset($_POST['edit_sales'])) {
        if (!isset($permissions['sales']['edit']) || $permissions['sales']['edit'] != 1) {
            echo "Permission denied";
            exit();
        }
        $id = intval($_POST['id']);
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $id_sumber = intval($_POST['id_sumber']);
        $pekerjaan = mysqli_real_escape_string($conn, $_POST['pekerjaan']);
        $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        $query = "UPDATE tbl_sales SET nama = ?, id_sumber = ?, pekerjaan = ?, no_hp = ?, email = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sisssi", $nama, $id_sumber, $pekerjaan, $no_hp, $email, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['notification'] = [
            'type' => 'success',
            'icon' => 'user-edit',
            'title' => 'Berhasil!',
            'message' => 'Data calon pembeli berhasil diperbarui'
        ];
        header("Location: sales.php");
        exit();
    }

    if (isset($_POST['delete_sales'])) {
        if (!isset($permissions['sales']['delete']) || $permissions['sales']['delete'] != 1) {
            echo "Permission denied";
            exit();
        }
        $sales_id = intval($_POST['sales_id']);
        
        // Get sales name before deletion
        $query = "SELECT nama FROM tbl_sales WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $sales_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $sales = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $query = "DELETE FROM tbl_sales WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $sales_id);
        if (mysqli_stmt_execute($stmt)) {
            triggerNotification($conn, 'delete_sales', ['nama' => $sales['nama']]);
        }
        mysqli_stmt_close($stmt);

        header("Location: sales.php");
        exit();
    }
}

// Fetch data for display
$query = "SELECT s.*, src.nama as sumber_nama, u.nama as user_nama 
          FROM tbl_sales s 
          LEFT JOIN tbl_sumber src ON s.id_sumber = src.id
          LEFT JOIN tbl_users u ON s.id_user = u.id
          ORDER BY s.tgl_create DESC";
$sales = mysqli_query($conn, $query);

$query = "SELECT * FROM tbl_sumber ORDER BY nama";
$sources = mysqli_query($conn, $query);

$query = "SELECT id, name FROM tbl_unit ORDER BY name";
$units = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (isset($_SESSION['level'])): ?>
        <?php if ($_SESSION['level'] == '0'): ?>
            <title>Dashboard Admin | Bogor Raya Residential</title>
        <?php elseif ($_SESSION['level'] == '1'): ?>
            <title>Dashboard User | Bogor Raya Residential</title>
        <?php elseif ($_SESSION['level'] == '2'): ?>
            <title>Dashboard Sales | Bogor Raya Residential</title>
        <?php endif; ?>
    <?php else: ?>
        <title>Dashboard | Bogor Raya Residential</title>
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <link href="../assets/css/dashboard-produk.css" rel="stylesheet">
    <style>
    /* Enhanced Card & Table Styles */
    .sales-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .sales-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }

    .table-container {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        margin: 20px 0;
        overflow: hidden;
    }

    #salesTable {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    #salesTable thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    #salesTable thead th {
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1.2rem 1rem;
        border: none;
        white-space: nowrap;
    }

    #salesTable tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f1f1f1;
    }

    #salesTable tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .lead-info-cell {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
    }

    .lead-avatar {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.2rem;
    }

    .lead-details {
        flex: 1;
    }

    .lead-name {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.2rem;
        font-size: 1.1rem;
    }

    .lead-contact {
        display: flex;
        gap: 1rem;
        color: #718096;
        font-size: 0.9rem;
    }

    .lead-contact i {
        width: 16px;
    }

    .source-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background: #edf2ff;
        color: #4f46e5;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .status-badge.new {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-badge.followup {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.3s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
    }

    .action-btn.view {
        background-color: #e0f2fe;
        color: #0284c7;
    }

    .action-btn.edit {
        background-color: #fef9c3;
        color: #ca8a04;
    }

    .action-btn.delete {
        background-color: #fee2e2;
        color: #dc2626;
    }

    /* Timeline Styles */
    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline-item {
        padding: 0 20px 20px 50px;
        position: relative;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.3s ease forwards;
    }

    .timeline-badge {
        width: 12px;
        height: 12px;
        background: #6366f1;
        border-radius: 50%;
        position: absolute;
        left: 20px;
        top: 15px;
    }

    .timeline-item::before {
        content: '';
        width: 2px;
        height: 100%;
        background: #e9ecef;
        position: absolute;
        left: 25px;
        top: 0;
    }

    .timeline-item .card {
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .timeline-item .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .timeline-item:last-child::before {
        height: 15px;
    }

    /* Modal Enhancements */
    .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .modal-header {
        border-bottom: none;
        padding: 1.5rem;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 15px 15px 0 0;
    }

    .modal-header .modal-title {
        color: white;
        font-weight: 600;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
    }

    textarea.form-control {
        min-height: 120px;
    }

    .input-group-text {
        border-radius: 10px 0 0 10px;
        border: 2px solid #e9ecef;
        border-right: none;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal.show .modal-dialog {
        opacity: 0;
        animation: modalFadeIn 0.3s ease forwards;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .toast {
        min-width: 300px;
    }

    .toast-container {
        z-index: 1060;
    }

    .source-name-preview {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #dc3545;
        margin: 1rem 0;
    }

    .sales-name-display {
        font-size: 1.2rem;
        font-weight: 600;
        color: #dc3545;
        border-left: 4px solid #dc3545;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    .notification-wrapper {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }

    .notification-toast {
        background: white;
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        min-width: 300px;
        transform: translateX(120%);
        animation: slideIn 0.5s ease forwards;
        border-left: 5px solid;
    }

    @keyframes slideIn {
        from {
            transform: translateX(120%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(120%);
            opacity: 0;
        }
    }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h4>
            <span class="brand-text">Bogor Raya</span>
            <small>Residential Admin</small>
        </h4>
    </div>

    <div class="sidebar-menu">
        <div class="menu-section">
            <div class="menu-section-title">Main Navigation</div>
            <?php if ($_SESSION['level'] == '0'): ?>
            <!-- Dashboard is always visible -->
            <a href="../dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == '../dashboard.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-home"></i>Dashboard
            </a>
            <?php endif; ?>

            <?php
            // Debug: Print permissions
            // echo "<pre>"; print_r($_SESSION['permissions']); echo "</pre>";
            
            // Decode JSON permissions from session
            $permissions = json_decode($_SESSION['permissions'], true);

            // Product Menu
            if (isset($permissions['products']['view']) && $permissions['products']['view'] == 1) {
                echo '<a href="produk.php" '.(basename($_SERVER['PHP_SELF']) == 'produk.php' ? 'class="active"' : '').'>
                    <i class="fas fa-building"></i>Product
                </a>';
            }

            // News Menu
            if (isset($permissions['news']['view']) && $permissions['news']['view'] == 1) {
                echo '<a href="news.php" '.(basename($_SERVER['PHP_SELF']) == 'news.php' ? 'class="active"' : '').'>
                    <i class="fas fa-newspaper"></i>News & Promotion
                </a>';
            }

            // Contacts/Pemesanan Menu
            if (isset($permissions['contacts']['view']) && $permissions['contacts']['view'] == 1) {
                echo '<a href="contact.php" '.(basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'class="active"' : '').'>
                    <i class="fas fa-inbox"></i>Pemesanan
                </a>';
            }

            // Sales Menu
            if (isset($permissions['sales']['view']) && $permissions['sales']['view'] == 1) {
                echo '<a href="sales.php" '.(basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'class="active"' : '').'>
                    <i class="fas fa-user-tie"></i>Sales
                </a>';
            }

            // Testimonials Menu
            if (isset($permissions['testimonials']['view']) && $permissions['testimonials']['view'] == 1) {
                echo '<a href="testimoni.php" '.(basename($_SERVER['PHP_SELF']) == 'testimoni.php' ? 'class="active"' : '').'>
                    <i class="fas fa-quote-right"></i>Testimoni
                </a>';
            }


            // Administration Section - Users Menu
            if (isset($permissions['users']['view']) && $permissions['users']['view'] == 1) {
                echo '<div class="menu-section">
                    <div class="menu-section-title">Administration</div>
                    <a href="tambah-user.php" '.(basename($_SERVER['PHP_SELF']) == 'tambah-user.php' ? 'class="active"' : '').'>
                        <i class="fas fa-users-cog"></i>User
                    </a>
                    <a href="management.php" '.(basename($_SERVER['PHP_SELF']) == 'management.php' ? 'class="active"' : '').'>
                        <i class="fas fa-users-cog"></i>Management
                    </a>
                </div>';
            }
            ?>

            <!-- Profile is always visible -->
            <div class="menu-section">
                <a href="profile.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-user"></i>Profile
                </a>
            </div>
        </div>
    </div>

    <div class="sidebar-footer">

        <div class="user-info">
            <div class="user-avatar">
                <img src="../<?php echo htmlspecialchars($_SESSION['foto']); ?>" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;">
            </div>
            <div class="user-details">
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['nama']); ?></div>
                <?php if (isset($_SESSION['level'])): ?>
                <?php if ($_SESSION['level'] == '0'): ?>
                    <div class="user-role">Admin</div>
                <?php elseif ($_SESSION['level'] == '1'): ?>
                    <div class="user-role">User</div>
                <?php elseif ($_SESSION['level'] == '2'): ?>
                    <div class="user-role">Sales</div>
                <?php endif; ?>
            <?php endif; ?>
            </div>
        </div>


        <form action="../logout.php" method="post">
            <button type="submit" class="btn btn-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
        </form>
    </div>
</div>
        

<button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>

<div class="content">
    <!-- Replace old header with new dashboard-style header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0">Manajemen Calon Pembeli</h1>
                <p class="text-dark mb-0">Kelola data calon pembeli dan tindak lanjut</p>
            </div>
            <div class="col text-end">
                <a href="export_sales.php" class="btn btn-success btn-sm me-2">
                    <i class="fas fa-file-excel me-2"></i>Export ke Excel
                </a>
                <?php if (isset($permissions['sales']['add']) && $permissions['sales']['add'] == 1): ?>
                <button class="btn btn-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#addSalesModal">
                    <i class="fas fa-plus me-2"></i>Tambah Calon Pembeli
                </button>

                    <?php if ($_SESSION['level'] == '0'): ?>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#sourceManagementModal">
                        <i class="fas fa-tag me-2"></i>Kelola Sumber
                    </button>
                    <?php endif;?>
                    
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Filter Section -->
    <div class="dashboard-header mt-2 mb-2">
        <div class="card-body">
            <div class="filter-section">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Cari calon pembeli...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="sourceFilter">
                            <option value="">Semua Sumber</option>
                            <?php mysqli_data_seek($sources, 0); 
                            while($source = mysqli_fetch_assoc($sources)): ?>
                                <option value="<?php echo $source['id']; ?>"><?php echo htmlspecialchars($source['nama']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="new">Baru</option>
                            <option value="followup">Sudah Follow Up</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales List -->
    <div class="sales-card card">
        <div class="card-body table-container">
            <div class="table-responsive">
                <table class="table table-hover" id="salesTable">
                    <thead>
                        <tr>
                            <th>Informasi Calon Pembeli</th>
                            <th>Sumber</th>
                            <th>Dibuat Oleh</th>
                            <th>Tindak Lanjut Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($sale = mysqli_fetch_assoc($sales)): ?>
                        <tr>
                            <td>
                                <div class="lead-info-cell">
                                    <div class="lead-avatar">
                                        <?php echo strtoupper(substr($sale['nama'], 0, 1)); ?>
                                    </div>
                                    <div class="lead-details">
                                        <div class="lead-name"><?php echo htmlspecialchars($sale['nama']); ?></div>
                                        <div class="lead-occupation"><?php echo htmlspecialchars($sale['pekerjaan']); ?></div>
                                        <div class="lead-contact">
                                            <span><i class="fas fa-phone"></i><?php echo htmlspecialchars($sale['no_hp']); ?>
                                            <br>
                                            <?php if($sale['email']): ?>
                                                <span><i class="fas fa-envelope"></i><?php echo htmlspecialchars($sale['email']); ?></span>
                                            <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="source-badge">
                                    <i class="fas fa-tag me-2"></i>
                                    <?php echo htmlspecialchars($sale['sumber_nama']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name"><?php echo htmlspecialchars($sale['user_nama']); ?>
                                    <br>
                                    <small class="text-muted"><?php echo date('d M Y', strtotime($sale['tgl_create'])); ?></small></div>    
                                </div>
                            </td>
                            <td>
                                <?php
                                $query = "SELECT * FROM tbl_follow_up WHERE id_sales = ? ORDER BY tgl_follow_up DESC LIMIT 1";
                                $stmt = mysqli_prepare($conn, $query);
                                mysqli_stmt_bind_param($stmt, "i", $sale['id']);
                                mysqli_stmt_execute($stmt);
                                $last_followup = mysqli_stmt_get_result($stmt)->fetch_assoc();
                                if($last_followup):
                                ?>
                                    <div class="status-badge followup">
                                        <i class="fas fa-check-circle me-1"></i>
                                        <?php echo date('d M Y', strtotime($last_followup['tgl_follow_up'])); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="status-badge new">
                                        <i class="fas fa-clock me-1"></i>
                                        Belum ada follow-up
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn view" onclick="viewHistory(<?php echo $sale['id']; ?>)" title="Lihat Riwayat">
                                        <i class="fas fa-history"></i>
                                    </button>
                                    <button class="action-btn edit" onclick="addFollowUp(<?php echo $sale['id']; ?>)" title="Tambah Follow Up">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <?php if (isset($permissions['sales']['edit']) && $permissions['sales']['edit'] == 1): ?>
                                    <button class="action-btn edit" onclick="editSales(<?php echo $sale['id']; ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php endif; ?>

                                    <?php if (isset($permissions['sales']['delete']) && $permissions['sales']['delete'] == 1): ?>
                                    <button class="action-btn delete" onclick="deleteSales(<?php echo $sale['id']; ?>)" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                    
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Replace old source modal with new source management modal -->
<div class="modal fade" id="sourceManagementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kelola Sumber</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Add Source Form -->
                <?php if (isset($permissions['sales']['add']) && $permissions['sales']['add'] == 1): ?>
                <form method="POST" class="mb-4 border-bottom pb-4">
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control" name="nama" placeholder="Nama Sumber Baru" required>
                        <button type="submit" name="add_source" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tambah
                        </button>
                    </div>
                </form>
                <?php endif; ?>

                <!-- Source List -->
                <div class="table-responsive">
                    <table class="table" id="sourceTable">
                        <thead>
                            <tr>
                                <th>Nama Sumber</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $source_query = "SELECT s.*, u.nama as created_by 
                                           FROM tbl_sumber s 
                                           LEFT JOIN tbl_users u ON s.id_user = u.id 
                                           ORDER BY s.nama";
                            $sources_list = mysqli_query($conn, $source_query);
                            while($source = mysqli_fetch_assoc($sources_list)): 
                            ?>
                            <tr>
                                <td class="source-name"><?php echo htmlspecialchars($source['nama']); ?></td>
                                <td><?php echo htmlspecialchars($source['created_by']); ?></td>
                                <td><?php echo date('d M Y', strtotime($source['tgl_create'])); ?></td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-end">
                                        <?php if (isset($permissions['sales']['edit']) && $permissions['sales']['edit'] == 1): ?>
                                            <button class="action-btn edit" 
                                                    onclick="editSource('<?php echo $source['id']; ?>', '<?php echo htmlspecialchars($source['nama']); ?>')"
                                                    title="Edit Sumber">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($permissions['sales']['delete']) && $permissions['sales']['delete'] == 1): ?>
                                            <button class="action-btn delete"
                                                    onclick="confirmDeleteSource('<?php echo $source['id']; ?>', '<?php echo htmlspecialchars($source['nama']); ?>')"
                                                    title="Hapus Sumber">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add edit source form modal -->
<div class="modal fade" id="editSourceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Sumber</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="source_id" id="editSourceId">
                    <div class="mb-3">
                        <label class="form-label">Nama Sumber</label>
                        <input type="text" class="form-control" name="source_name" id="editSourceName" required>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_source" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Delete Source Confirmation Modal -->
<div class="modal fade" id="deleteSourceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus sumber:</p>
                <h5 class="source-name-preview mt-2 mb-3"></h5>
                <p class="text-danger mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Tindakan ini tidak dapat dibatalkan!
                </p>
            </div>
            <div class="modal-footer">
                <form method="POST" class="w-100 d-flex justify-content-end gap-2">
                    <input type="hidden" name="delete_source" id="deleteSourceId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Hapus Sumber
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Sales Modal -->
<div class="modal fade" id="addSalesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Calon Pembeli Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sumber Informasi</label>
                        <select class="form-select" name="id_sumber" required>
                            <?php mysqli_data_seek($sources, 0); ?>
                            <?php while($source = mysqli_fetch_assoc($sources)): ?>
                                <option value="<?php echo $source['id']; ?>"><?php echo htmlspecialchars($source['nama']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pekerjaan</label>
                        <input type="text" class="form-control" name="pekerjaan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor HP</label>
                        <input type="tel" class="form-control" name="no_hp" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <button type="submit" name="add_sales" class="btn btn-primary">Simpan Data</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add necessary modals -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Follow Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="historyTimeline" class="timeline"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="followUpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah Follow Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="id_sales" id="followup_sales_id">
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Media Komunikasi</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="media" value="WhatsApp" id="mediaWA" required>
                                <label class="form-check-label" for="mediaWA">
                                    <i class="fab fa-whatsapp me-1"></i>WhatsApp
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="media" value="Phone" id="mediaPhone">
                                <label class="form-check-label" for="mediaPhone">
                                    <i class="fas fa-phone me-1"></i>Telepon
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="media" value="Meeting" id="mediaMeet">
                                <label class="form-check-label" for="mediaMeet">
                                    <i class="fas fa-handshake me-1"></i>Meeting
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="media" value="Other" id="mediaOther">
                                <label class="form-check-label" for="mediaOther">
                                    <i class="fas fa-pencil-alt me-1"></i>Lainnya
                                </label>
                            </div>
                            <div id="otherMediaInput" class="w-100 mt-2" style="display:none;">
                                <input type="text" class="form-control" name="media_other" placeholder="Masukkan media lainnya...">
                            </div>
                        </div>
                        <script>
                            document.getElementById('mediaOther').addEventListener('change', function() {
                                document.getElementById('otherMediaInput').style.display = this.checked ? 'block' : 'none';
                                if (this.checked) {
                                    document.querySelector('[name="media_other"]').required = true;
                                } else {
                                    document.querySelector('[name="media_other"]').required = false;
                                }
                            });

                            // Hide other input when different radio is selected
                            document.querySelectorAll('[name="media"]').forEach(radio => {
                                radio.addEventListener('change', function() {
                                    if (this.id !== 'mediaOther') {
                                        document.getElementById('otherMediaInput').style.display = 'none';
                                        document.querySelector('[name="media_other"]').required = false;
                                    }
                                });
                            });
                        </script>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Unit (Opsional)</label>
                        <select class="form-select" name="id_unit">
                            <option value="">Pilih Unit</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Hasil/Catatan</label>
                        <div class="d-flex gap-2 flex-wrap mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="hasil_type" value="Tertarik" id="hasilTertarik" required>
                                <label class="form-check-label" for="hasilTertarik">Tertarik</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="hasil_type" value="Pikir-pikir" id="hasilPikir">
                                <label class="form-check-label" for="hasilPikir">Pikir-pikir</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="hasil_type" value="Tidak tertarik" id="hasilTidak">
                                <label class="form-check-label" for="hasilTidak">Tidak tertarik</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="hasil_type" value="other" id="hasilOther">
                                <label class="form-check-label" for="hasilOther">Lainnya</label>
                            </div>
                        </div>
                        <div id="hasilOtherInput" class="mt-2" style="display:none;">
                            <textarea class="form-control" name="hasil" placeholder="Masukkan hasil follow up..."></textarea>
                        </div>
                        <script>
                            document.getElementById('hasilOther').addEventListener('change', function() {
                                document.getElementById('hasilOtherInput').style.display = this.checked ? 'block' : 'none';
                                document.querySelector('[name="hasil"]').required = this.checked;
                            });
                            
                            document.querySelectorAll('[name="hasil_type"]').forEach(radio => {
                                radio.addEventListener('change', function() {
                                    if (this.id !== 'hasilOther') {
                                        document.getElementById('hasilOtherInput').style.display = 'none';
                                        document.querySelector('[name="hasil"]').required = false;
                                        document.querySelector('[name="hasil"]').value = this.value;
                                    }
                                });
                            });
                        </script>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tanggal Follow Up</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" class="form-control flatpickr" 
                                   name="tgl_follow_up" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" name="add_followup" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Sales Modal -->
<div class="modal fade" id="editSalesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Calon Pembeli</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="editSalesForm">
                    <input type="hidden" name="id" id="editSalesId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" id="editSalesNama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sumber Informasi</label>
                        <select class="form-select" name="id_sumber" id="editSalesSumber" required>
                            <?php mysqli_data_seek($sources, 0); ?>
                            <?php while($source = mysqli_fetch_assoc($sources)): ?>
                                <option value="<?php echo $source['id']; ?>">
                                    <?php echo htmlspecialchars($source['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pekerjaan</label>
                        <input type="text" class="form-control" name="pekerjaan" id="editSalesPekerjaan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor HP</label>
                        <input type="tel" class="form-control" name="no_hp" id="editSalesNoHp" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" name="email" id="editSalesEmail">
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" name="edit_sales" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Delete Sales Confirmation Modal -->
<div class="modal fade" id="deleteSalesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <i class="fas fa-user-times text-danger" style="font-size: 4rem;"></i>
                </div>
                <h5 class="mb-4">Apakah Anda yakin ingin menghapus data calon pembeli:</h5>
                <div class="sales-name-display p-3 mb-3 bg-light rounded"></div>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    Semua data follow up untuk calon pembeli ini juga akan dihapus!
                </div>
            </div>
            <div class="modal-footer">
                <form method="POST" class="w-100 d-flex justify-content-end gap-2">
                    <input type="hidden" name="sales_id" id="deleteSalesId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" name="delete_sales" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/dashboard.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
function addFollowUp(salesId) {
    const modal = document.getElementById('followUpModal');
    const form = modal.querySelector('form');
    form.reset();
    document.getElementById('followup_sales_id').value = salesId;
    
    // Initialize flatpickr for date input
    flatpickr(modal.querySelector('.flatpickr'), {
        dateFormat: "Y-m-d",
        maxDate: "today",
        defaultDate: "today"
    });
    
    // Load units into dropdown
    fetch('get_units.php')
        .then(response => response.json())
        .then(units => {
            const unitSelect = modal.querySelector('select[name="id_unit"]');
            unitSelect.innerHTML = '<option value="">Pilih Unit</option>';
            units.forEach(unit => {
                const option = document.createElement('option');
                option.value = unit.id;
                option.textContent = unit.name;
                unitSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading units:', error));
    
    new bootstrap.Modal(modal).show();
}

function viewHistory(salesId) {
    // Show loading state
    const timeline = document.getElementById('historyTimeline');
    timeline.innerHTML = `
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Memuat riwayat...</p>
        </div>`;
    
    // Show modal immediately with loading state
    const historyModal = new bootstrap.Modal(document.getElementById('historyModal'));
    historyModal.show();

    // Fetch history data
    fetch(`get_sales_history.php?id=${salesId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!Array.isArray(data)) {
                throw new Error('Invalid data format');
            }
            
            timeline.innerHTML = '';
            
            if (data.length === 0) {
                timeline.innerHTML = `
                    <div class="text-center p-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada riwayat follow up</p>
                    </div>`;
                return;
            }
            
            data.forEach((item, index) => {
                const date = new Date(item.tgl_follow_up).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                
                const unitInfo = item.unit_name 
                    ? `<div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-building me-1"></i>
                            Unit: ${item.unit_name}
                        </small>
                       </div>`
                    : '';
                
                // Add edit and delete buttons
                const actionButtons = `
                    <div class="mt-2 text-end">
                        <button class="btn btn-sm btn-light" onclick="editFollowUp(${item.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger" onclick="deleteFollowUp(${item.id}, '${date}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>`;
                
                timeline.innerHTML += `
                    <div class="timeline-item" style="animation-delay: ${index * 0.1}s">
                        <div class="timeline-badge"></div>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-${getMediaIcon(item.media)} me-2"></i>
                                        ${item.media}
                                    </h6>
                                    <small class="text-muted">${date}</small>
                                </div>
                                <p class="card-text">${item.hasil}</p>
                                ${unitInfo}
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        By: ${item.user_nama || 'Unknown'}
                                    </small>
                                </div>
                                ${actionButtons}
                            </div>
                        </div>
                    </div>`;
            });
        })
        .catch(error => {
            console.error('Error:', error);
            timeline.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                    <p class="text-danger">Gagal memuat riwayat follow up</p>
                </div>`;
            showNotification('Gagal memuat riwayat follow up', 'danger');
        });
}

function editSales(salesId) {
    fetch(`get_sales.php?id=${salesId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('editSalesId').value = data.id;
            document.getElementById('editSalesNama').value = data.nama;
            document.getElementById('editSalesSumber').value = data.id_sumber;
            document.getElementById('editSalesPekerjaan').value = data.pekerjaan;
            document.getElementById('editSalesNoHp').value = data.no_hp;
            document.getElementById('editSalesEmail').value = data.email;
            
            new bootstrap.Modal(document.getElementById('editSalesModal')).show();
        });
}

function editSource(id, nama) {
    document.getElementById('editSourceId').value = id;
    document.getElementById('editSourceName').value = nama;
    new bootstrap.Modal(document.getElementById('editSourceModal')).show();
}

function confirmDeleteSource(id, nama) {
    const modal = document.getElementById('deleteSourceModal');
    modal.querySelector('.source-name-preview').textContent = nama;
    modal.querySelector('#deleteSourceId').value = id;
    
    const deleteModal = new bootstrap.Modal(modal);
    deleteModal.show();
}

function deleteSales(salesId) {
    fetch(`get_sales.php?id=${salesId}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('deleteSalesModal');
            modal.querySelector('.sales-name-display').textContent = data.nama;
            modal.querySelector('#deleteSalesId').value = data.id;
            new bootstrap.Modal(modal).show();
        });
}

function editFollowUp(followUpId) {
    const modal = document.getElementById('followUpModal');
    const form = modal.querySelector('form');
    form.reset();
    
    // First load units, then get follow-up data
    fetch('get_units.php')
        .then(response => response.json())
        .then(units => {
            const unitSelect = modal.querySelector('select[name="id_unit"]');
            unitSelect.innerHTML = '<option value="">Pilih Unit</option>';
            units.forEach(unit => {
                const option = document.createElement('option');
                option.value = unit.id;
                option.textContent = unit.name;
                unitSelect.appendChild(option);
            });
            
            // After units are loaded, get follow-up data
            return fetch(`get_followup.php?id=${followUpId}`);
        })
        .then(response => response.json())
        .then(data => {
            // Populate form fields
            document.getElementById('followup_sales_id').value = data.id_sales;
            modal.querySelector(`input[value="${data.media}"]`).checked = true;
            modal.querySelector('select[name="id_unit"]').value = data.id_unit || '';
            
            // Handle hasil field
            if (data.hasil === 'Tertarik' || data.hasil === 'Pikir-pikir' || data.hasil === 'Tidak tertarik') {
                document.querySelector(`input[value="${data.hasil}"]`).checked = true;
            } else {
                document.getElementById('hasilOther').checked = true;
                document.getElementById('hasilOtherInput').style.display = 'block';
                modal.querySelector('textarea[name="hasil"]').value = data.hasil;
            }

            // Initialize flatpickr with the saved date
            const dateInput = modal.querySelector('.flatpickr');
            flatpickr(dateInput, {
                dateFormat: "Y-m-d",
                maxDate: "today",
                defaultDate: data.tgl_follow_up
            });
            
            // Add hidden input for followup ID
            let hiddenInput = modal.querySelector('input[name="followup_id"]');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'followup_id';
                modal.querySelector('form').appendChild(hiddenInput);
            }
            hiddenInput.value = followUpId;
            
            // Change form submission name
            const submitButton = modal.querySelector('button[type="submit"]');
            submitButton.name = 'edit_followup';
            submitButton.innerHTML = '<i class="fas fa-save me-1"></i>Update';
            
            // Change modal title
            modal.querySelector('.modal-title').innerHTML = 
                '<i class="fas fa-edit me-2"></i>Edit Follow Up';
            
            new bootstrap.Modal(modal).show();
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Gagal memuat data follow up!', 'danger');
        });
}

function deleteFollowUp(followUpId, followUpDate) {
    if (confirm(`Apakah Anda yakin ingin menghapus riwayat follow up pada ${followUpDate}?`)) {
        fetch('delete_followup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${followUpId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Follow up berhasil dihapus!', 'warning');
                // Refresh the current view
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification('Gagal menghapus follow up!', 'danger');
            }
        });
    }
}

function getMediaIcon(media) {
    switch(media) {
        case 'WhatsApp': return 'whatsapp';
        case 'Phone': return 'phone';
        case 'Email': return 'envelope';
        case 'Meeting': return 'handshake';
        default: return 'comment';
    }
}

// Initialize Toasts
const toastContainer = document.createElement('div');
toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
document.body.appendChild(toastContainer);

function showNotification(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

// Add success notification after form submission
<?php if (isset($_POST['add_source'])): ?>
    showNotification('Sumber berhasil ditambahkan!');
<?php endif; ?>

<?php if (isset($_POST['edit_source'])): ?>
    showNotification('Sumber berhasil diperbarui!');
<?php endif; ?>

<?php if (isset($_POST['delete_source'])): ?>
    showNotification('Sumber berhasil dihapus!', 'danger');
<?php endif; ?>

<?php if (isset($_POST['delete_sales'])): ?>
    showNotification('Data calon pembeli berhasil dihapus!', 'warning');
<?php endif; ?>

<?php if (isset($_POST['add_followup'])): ?>
    showNotification('Follow up berhasil ditambahkan!', 'success');
<?php endif; ?>

<?php if (isset($_POST['edit_followup'])): ?>
    showNotification('Follow up berhasil diperbarui!', 'success');
<?php endif; ?>

function initializeFilters() {
    const searchInput = document.getElementById('searchInput');
    const sourceFilter = document.getElementById('sourceFilter');
    const statusFilter = document.getElementById('statusFilter');

    let filterTimeout;

    const applyFilters = async () => {
        const searchTerm = searchInput.value;
        const sourceId = sourceFilter.value;
        const status = statusFilter.value;

        try {
            const response = await fetch('filter_sales.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    search: searchTerm,
                    source: sourceId,
                    status: status
                })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            updateSalesTable(data);
        } catch (error) {
            console.error('Error:', error);
            showNotification('Gagal memuat data!', 'danger');
        }
    };

    // Debounced filter function
    const debouncedFilter = () => {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            applyFilters();
        }, 300);
    };

    // Event listeners
    searchInput.addEventListener('input', debouncedFilter);
    sourceFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
}

function updateSalesTable(data) {
    const tbody = document.querySelector('#salesTable tbody');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center p-4">
                    <i class="fas fa-search text-muted mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0">Tidak ada data yang ditemukan</p>
                </td>
            </tr>`;
        return;
    }

    data.forEach(sale => {
        const row = `
            <tr>
                <td>
                    <div class="lead-info-cell">
                        <div class="lead-avatar">
                            ${sale.nama.charAt(0).toUpperCase()}
                        </div>
                        <div class="lead-details">
                            <div class="lead-name">${sale.nama}</div>
                            <div class="lead-occupation">${sale.pekerjaan || ''}</div>
                            <div class="lead-contact">
                                <span><i class="fas fa-phone"></i>${sale.no_hp}
                                ${sale.email ? `<br><span><i class="fas fa-envelope"></i>${sale.email}</span>` : ''}
                                </span>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="source-badge">
                        <i class="fas fa-tag me-2"></i>
                        ${sale.sumber_nama}
                    </span>
                </td>
                <td>
                    <div class="user-info">
                        <div class="user-name">${sale.user_nama}<br>
                        <small class="text-muted">${new Date(sale.tgl_create).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        })}</small></div>
                    </div>
                </td>
                <td>
                    ${sale.last_followup ? `
                        <div class="status-badge followup">
                            <i class="fas fa-check-circle me-1"></i>
                            ${new Date(sale.last_followup).toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            })}
                        </div>
                    ` : `
                        <div class="status-badge new">
                            <i class="fas fa-clock me-1"></i>
                            Belum ada follow-up
                        </div>
                    `}
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" onclick="viewHistory(${sale.id})" title="Lihat Riwayat">
                            <i class="fas fa-history"></i>
                        </button>
                        <button class="action-btn edit" onclick="addFollowUp(${sale.id})" title="Tambah Follow Up">
                            <i class="fas fa-plus"></i>
                        </button>
                        ${sale.can_edit ? `
                            <button class="action-btn edit" onclick="editSales(${sale.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                        ` : ''}
                        <button class="action-btn delete" onclick="deleteSales(${sale.id})" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        tbody.innerHTML += row;
    });
}

// Initialize filters when document is ready
document.addEventListener('DOMContentLoaded', initializeFilters);

document.addEventListener('DOMContentLoaded', function() {
    const notification = document.querySelector('.notification-toast');
    if (notification) {
        // Auto-hide after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.5s ease forwards';
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 5000);
    }
});
</script>

<!-- Modern Notification System -->
<?php if (isset($_SESSION['notification'])): ?>
<div class="notification-wrapper">
    <div class="notification-toast <?php echo $_SESSION['notification']['type']; ?>">
        <div class="notification-content">
            <i class="fas fa-<?php echo $_SESSION['notification']['icon']; ?> notification-icon"></i>
            <div class="notification-text">
                <h6 class="notification-title"><?php echo $_SESSION['notification']['title']; ?></h6>
                <p class="notification-message"><?php echo $_SESSION['notification']['message']; ?></p>
            </div>
        </div>
        <button type="button" class="notification-close" onclick="this.parentElement.remove();">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<style>
.notification-wrapper {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.notification-toast {
    background: white;
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    min-width: 300px;
    transform: translateX(120%);
    animation: slideIn 0.5s ease forwards;
    border-left: 5px solid;
}

.notification-toast.success {
    border-left-color: #10B981;
}

.notification-toast.warning {
    border-left-color: #F59E0B;
}

.notification-toast.error {
    border-left-color: #EF4444;
}

.notification-content {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.notification-icon {
    font-size: 24px;
    padding-top: 3px;
}

.notification-toast.success .notification-icon {
    color: #10B981;
}

.notification-toast.warning .notification-icon {
    color: #F59E0B;
}

.notification-toast.error .notification-icon {
    color: #EF4444;
}

.notification-text {
    flex: 1;
}

.notification-title {
    margin: 0 0 5px;
    font-size: 16px;
    font-weight: 600;
}

.notification-message {
    margin: 0;
    font-size: 14px;
    color: #666;
}

.notification-close {
    background: none;
    border: none;
    padding: 5px;
    margin-left: 10px;
    cursor: pointer;
    color: #999;
    align-self: flex-start;
}

.notification-close:hover {
    color: #666;
}

@keyframes slideIn {
    from {
        transform: translateX(120%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(120%);
        opacity: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.querySelector('.notification-toast');
    if (notification) {
        // Auto-hide after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.5s ease forwards';
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 5000);
    }
});
</script>

<?php unset($_SESSION['notification']); ?>
<?php endif; ?>

</body>
</html>

</body>
</html>
