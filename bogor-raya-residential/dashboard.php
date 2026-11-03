<?php
session_start();
require_once 'includes/management.php';

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "db_bogor");

// Get user's role and permissions
$userLevel = isset($_SESSION['level']) ? $_SESSION['level'] : '1';
$permissions = getRolePermissions($conn, $userLevel);

// Update session permissions in case they changed in management
$_SESSION['permissions'] = json_encode($permissions);

// Cek apakah sesi 'nama' sudah ada, jika tidak, redirect ke login.php
if (!isset($_SESSION['nama'])) {
    header("Location: login.php");
    exit();
}

// Redirect sales (level 2) to sales dashboard
if ($userLevel == '2') {
    header("Location: dashboard/sales.php");
    exit();
}

// Add status check
$user_id = $_SESSION['user_id'];
$query = "SELECT status FROM tbl_users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user['status'] == 0) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=account_inactive");
    exit();
}

// Set user level consistently
$userLevel = isset($_SESSION['level']) ? $_SESSION['level'] : '1'; // Default to regular user if not set
$permissions = getRolePermissions($conn, $userLevel);

// Only fetch data if user has permission
$total_news = $total_produk = $total_users = $total_pemesanan = 0;

if (isset($permissions['news']['view']) && $permissions['news']['view'] == 1) {
    $sql_news = "SELECT * FROM view_total_news";
    $result_news = $conn->query($sql_news);
    if ($result_news && $result_news->num_rows > 0) {
        $total_news = $result_news->fetch_assoc()['jumlah_news'];
    }
}

if (isset($permissions['products']['view']) && $permissions['products']['view'] == 1) {
    $sql_produk = "SELECT * FROM view_total_produk";
    $result_produk = $conn->query($sql_produk);
    if ($result_produk && $result_produk->num_rows > 0) {
        $total_produk = $result_produk->fetch_assoc()['jumlah_produk'];
    }
}

// Only admin and users with permission can see users count
if ($userLevel == '0' || (isset($permissions['users']['view']) && $permissions['users']['view'] == 1)) {
    $sql_users = "SELECT * FROM view_total_users";
    $result_users = $conn->query($sql_users);
    if ($result_users && $result_users->num_rows > 0) {
        $total_users = $result_users->fetch_assoc()['jumlah_users'];
    }
}

if (isset($permissions['contacts']['view']) && $permissions['contacts']['view'] == 1) {
    $sql_pemesanan = "SELECT * FROM view_total_pemesanan";
    $result_pemesanan = $conn->query($sql_pemesanan);
    if ($result_pemesanan && $result_pemesanan->num_rows > 0) {
        $total_pemesanan = $result_pemesanan->fetch_assoc()['jumlah_pemesan'];
    }
}

// Get user ID from session or query it if not set
if (!isset($_SESSION['user_id']) && isset($_SESSION['nama'])) {
    $stmt = $conn->prepare("SELECT id FROM tbl_users WHERE nama = ?");
    $stmt->bind_param("s", $_SESSION['nama']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $row['id'];
    }
    $stmt->close();
}

// Modified notification query to fetch required fields
if (isset($_SESSION['user_id'])) {
    $sql_notifikasi = "SELECT 
        n.id,
        n.message,
        n.link,
        n.timestamp,
        CASE 
            WHEN TIMESTAMPDIFF(MINUTE, n.timestamp, NOW()) < 60 
                THEN CONCAT(TIMESTAMPDIFF(MINUTE, n.timestamp, NOW()), ' menit yang lalu')
            WHEN TIMESTAMPDIFF(HOUR, n.timestamp, NOW()) < 24 
                THEN CONCAT(TIMESTAMPDIFF(HOUR, n.timestamp, NOW()), ' jam yang lalu')
            WHEN TIMESTAMPDIFF(DAY, n.timestamp, NOW()) < 7
                THEN CONCAT(TIMESTAMPDIFF(DAY, n.timestamp, NOW()), ' hari yang lalu')
            ELSE DATE_FORMAT(n.timestamp, '%d %M %Y')
        END AS time_ago,
        CASE 
            WHEN n.message LIKE '%pemesanan%' THEN 'primary'
            WHEN n.message LIKE '%sales%' THEN 'success' 
            WHEN n.message LIKE '%news%' THEN 'warning'
            ELSE 'info'
        END AS color,
        CASE
            WHEN n.message LIKE '%pemesanan%' THEN 'envelope'
            WHEN n.message LIKE '%sales%' THEN 'chart-line'
            WHEN n.message LIKE '%news%' THEN 'newspaper'
            ELSE 'bell'
        END AS icon
        FROM tbl_user_notifications n 
        WHERE n.user_id = ? 
        ORDER BY n.timestamp DESC 
        LIMIT 10";

    try {
        $stmt = $conn->prepare($sql_notifikasi);
        if (!$stmt) {
            throw new Exception('Failed to prepare notification query');
        }
        
        $stmt->bind_param("i", $_SESSION['user_id']);
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute notification query');
        }
        
        $result_notifikasi = $stmt->get_result();
    } catch (Exception $e) {
        error_log('Notification Error: ' . $e->getMessage());
        $result_notifikasi = null;
    }
} else {
    $result_notifikasi = null;
}

// Modify sales statistics queries
if (isset($permissions['sales']['view']) && $permissions['sales']['view'] == 1) {
    // Get sales by source with proper counting
    $sql_sales_by_source = "SELECT 
        s.nama as source_name, 
        COUNT(ts.id) as count,
        COALESCE(COUNT(ts.id), 0) as total 
        FROM tbl_sumber s 
        LEFT JOIN tbl_sales ts ON s.id = ts.id_sumber 
        WHERE s.nama IS NOT NULL
        GROUP BY s.id, s.nama 
        ORDER BY count DESC";
    $result_sales_by_source = $conn->query($sql_sales_by_source);

    // Get follow-up results with better categorization
    $sql_followup_results = "SELECT 
        CASE 
            WHEN hasil = 'Tertarik' THEN 'Tertarik'
            WHEN hasil = 'Pikir-pikir' THEN 'Pikir-pikir'
            WHEN hasil = 'Tidak tertarik' THEN 'Tidak Tertarik'
            ELSE 'Lainnya'
        END as kategori,
        COUNT(*) as count 
        FROM tbl_follow_up 
        GROUP BY 
        CASE 
            WHEN hasil = 'Tertarik' THEN 'Tertarik'
            WHEN hasil = 'Pikir-pikir' THEN 'Pikir-pikir'
            WHEN hasil = 'Tidak tertarik' THEN 'Tidak Tertarik'
            ELSE 'Lainnya'
        END
        ORDER BY count DESC";
    $result_followup_results = $conn->query($sql_followup_results);
}
?>

<?php if ($_SESSION['level'] == '0'): ?>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">

    <script>
    let currentPeriod = 'daily';
    let startDate = '';
    let endDate = '';

    function applyDateFilter() {
        startDate = document.getElementById('startDate').value;
        endDate = document.getElementById('endDate').value;
        
        if (!startDate || !endDate) {
            alert('Please select both start and end dates');
            return;
        }
        
        updateChart(currentPeriod);
    }

    window.updateChart = function(period) {
        currentPeriod = period;
        
        // Update button states
        const buttons = ['dailyBtn', 'monthlyBtn', 'yearlyBtn'];
        buttons.forEach(btn => {
            document.getElementById(btn).classList.remove('active');
        });
        document.getElementById(period + 'Btn').classList.add('active');

        // Fetch filtered data
        fetch('ajax/get_traffic_data.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                period: period,
                startDate: startDate,
                endDate: endDate
            })
        })
        .then(response => response.json())
        .then(data => {
            trafficChart.data.labels = data.labels;
            trafficChart.data.datasets[0].data = data.values;
            trafficChart.data.datasets[0].label = data.label;
            trafficChart.update();
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    </script>

</head>
<style>
/* Card Styles */
.stat-card {
    border: none;
    border-radius: 15px;
    transition: transform 0.3s ease;
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.stat-card:hover {
    transform: translateY(-5px);
}

/* Icon Styles */
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.stat-icon i {
    font-size: 24px;
    color: #fff;
}

/* Card Details */
.stat-details h3 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 5px;
    color: #2c3e50;
}

.stat-details p {
    color: #7f8c8d;
    margin-bottom: 10px;
}

/* Trend Indicators */
.stat-trend {
    font-size: 14px;
    padding: 4px 8px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.stat-trend.positive {
    background-color: rgba(46, 213, 115, 0.1);
    color: #2ed573;
}

/* Dashboard Header */
.dashboard-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 30px;
    color: white;
}

/* Filter Buttons */
.date-filter .btn-group .btn {
    border-color: rgba(255, 255, 255, 0.3);
    color: white;
}

.date-filter .btn-group .btn.active {
    background: rgba(255, 255, 255, 0.2);
}

/* Chart Container */
.chart-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    padding: 20px;
    transition: all 0.3s ease;
}

.chart-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.card-header h5 {
    font-size: 1rem;
    font-weight: 600;
}

.card-header i {
    opacity: 0.8;
}

canvas {
    min-height: 250px;
}

/* Stat Card Colors */
.visits-card .stat-icon { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
.news-card .stat-icon { background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); }
.products-card .stat-icon { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.users-card .stat-icon { background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%); }

/* Recent Activities */
.activities-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.activity-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 15px;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

/* Notification Dropdown */
.notification-dropdown {
    display: flex;
    justify-content: flex-end;
}

#notificationButton {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

#notificationButton:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.notification-menu {
    width: 350px;
    padding: 0;
    border: none;
    box-shadow: 0 5px 25px rgba(0,0,0,0.15);
    border-radius: 15px;
    margin-top: 15px;
}

.notification-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    background: #f8f9fa;
    border-radius: 15px 15px 0 0;
}

.notification-body {
    max-height: 400px;
    overflow-y: auto;
}

.notification-item {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s ease;
    position: relative;
    background-color: transparent !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

.notification-item:hover {
    background-color: rgba(0, 0, 0, 0.03) !important;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: white;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-text {
    margin: 0;
    font-size: 0.9rem;
    color: #2d3748;
    font-weight: 500;
}

.notification-time {
    color: #718096;
    font-size: 0.8rem;
}

.delete-notification {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.notification-item:hover .delete-notification {
    opacity: 0.7;
}

.delete-notification:hover {
    opacity: 1 !important;
}

.badge {
    border: 2px solid white;
    transition: all 0.3s ease;
}

/* Scrollbar Styling */
.notification-body::-webkit-scrollbar {
    width: 6px;
}

.notification-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notification-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.notification-body::-webkit-scrollbar-thumb:hover {
    background: #999;
}

/* Floating Notification Button */
.floating-notification {
    display: none;
}

.notification-btn {
    position: relative;
    width: 60px;
    height: 60px;
    border-radius: 30px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    color: white;
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    outline: none;
}

.notification-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(99,102,241,0.3);
}

.notification-btn:active {
    transform: translateY(-2px);
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 12px;
    font-weight: 600;
    border: 2px solid white;
    min-width: 20px;
    text-align: center;
}

.notification-panel {
    width: 350px !important;
    border-radius: 15px 0 0 15px;
    border: none;
    background-color: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.offcanvas-header {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.95) 0%, rgba(139, 92, 246, 0.95) 100%);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 1rem 1.5rem;
}

.offcanvas-header h5 {
    color: white;
    margin: 0;
    font-weight: 600;
}

.offcanvas-header .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

/* Smooth animation for notifications */
.notification-item {
    animation: none;
    opacity: 1 !important;
    transform: none !important;
    border-bottom: 1px solid #eee;
}

.activity-item.notification-item {
    animation: none;
    opacity: 1;
    transform: none;
    transition: background-color 0.3s ease;
}

.activity-item.notification-item:hover {
    background-color: rgba(0, 0, 0, 0.03);
}

.activity-item.notification-item a {
    display: flex;
    width: 100%;
    padding: 15px;
    color: inherit;
    text-decoration: none;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.new-notification {
    animation: fadeIn 0.3s ease forwards;
}

/* Offcanvas Animation */
.offcanvas.offcanvas-end {
    transform: translateX(100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.offcanvas.offcanvas-end.show {
    transform: translateX(0);
}

/* Button hover effect */
.btn-light:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

/* Update notification link colors */
.notification-link {
    color: inherit !important;
    text-decoration: none !important;
}

.notification-link:hover {
    color: inherit !important;
}
</style>
</body>
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
            
            <!-- Dashboard is always visible -->
            <a href="dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-home"></i>Dashboard
            </a>

            <?php
            // Debug: Print permissions
            // echo "<pre>"; print_r($_SESSION['permissions']); echo "</pre>";
            
            // Decode JSON permissions from session
            $permissions = json_decode($_SESSION['permissions'], true);

            // Product Menu
            if (isset($permissions['products']['view']) && $permissions['products']['view'] == 1) {
                echo '<a href="dashboard/produk.php" '.(basename($_SERVER['PHP_SELF']) == 'dashboard/produk.php' ? 'class="active"' : '').'>
                    <i class="fas fa-building"></i>Product
                </a>';
            }

            // News Menu
            if (isset($permissions['news']['view']) && $permissions['news']['view'] == 1) {
                echo '<a href="dashboard/news.php" '.(basename($_SERVER['PHP_SELF']) == 'dashboard/news.php' ? 'class="active"' : '').'>
                    <i class="fas fa-newspaper"></i>News & Promotion
                </a>';
            }

            // Contacts/Pemesanan Menu
            if (isset($permissions['contacts']['view']) && $permissions['contacts']['view'] == 1) {
                echo '<a href="dashboard/contact.php" '.(basename($_SERVER['PHP_SELF']) == 'dashboard/contact.php' ? 'class="active"' : '').'>
                    <i class="fas fa-inbox"></i>Pemesanan
                </a>';
            }

            // Sales Menu
            if (isset($permissions['sales']['view']) && $permissions['sales']['view'] == 1) {
                echo '<a href="dashboard/sales.php" '.(basename($_SERVER['PHP_SELF']) == 'dashboard/sales.php' ? 'class="active"' : '').'>
                    <i class="fas fa-user-tie"></i>Sales
                </a>';
            }


            // Testimonials Menu
            if (isset($permissions['testimonials']['view']) && $permissions['testimonials']['view'] == 1) {
                echo '<a href="dashboard/testimoni.php" '.(basename($_SERVER['PHP_SELF']) == 'dashboard/testimoni.php' ? 'class="active"' : '').'>
                    <i class="fas fa-quote-right"></i>Testimoni
                </a>';
            }


            // Administration Section - Users Menu
            if (isset($permissions['users']['view']) && $permissions['users']['view'] == 1) {
                echo '<div class="menu-section">
                    <div class="menu-section-title">Administration</div>
                    <a href="dashboard/tambah-user.php" '.(basename($_SERVER['PHP_SELF']) == 'dashboard/tambah-user.php' ? 'class="active"' : '').'>
                        <i class="fas fa-users-cog"></i>User
                    </a>
                    <a href="dashboard/management.php" '.(basename($_SERVER['PHP_SELF']) == 'dashboard/management.php' ? 'class="active"' : '').'>
                        <i class="fas fa-users-cog"></i>Management
                    </a>
                </div>';
            }
            ?>

            <!-- Profile is always visible -->
            <div class="menu-section">
                <a href="dashboard/profile.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard/profile.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-user"></i>Profile
                </a>
            </div>
        </div>
    </div>

    <div class="sidebar-footer">

        <div class="user-info">
            <div class="user-avatar">
                <img src="<?php echo htmlspecialchars($_SESSION['foto']); ?>" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;">
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


        <form action="logout.php" method="post">
            <button type="submit" class="btn btn-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
        </form>
    </div>
</div>


<button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>

<div class="content">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <h1 class="h3 mb-0">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
                <p class="text-light mb-0">
                    <?php 
                    $roleName = getNamaRole($conn, $_SESSION['level']);
                    echo "Panel " . htmlspecialchars($roleName);
                    ?>
                </p>
            </div>
            <?php if ($userLevel == '0' || (isset($permissions['reports']['export']) && $permissions['reports']['export'] == 1)): ?>
            <div class="col">
                <div class="date-filter d-flex justify-content-end align-items-center gap-2">
                    <form action="generate_report.php" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="startDate" id="reportStartDate">
                        <input type="hidden" name="endDate" id="reportEndDate">
                        <input type="hidden" name="period" id="reportPeriod" value="daily">
                        <button type="submit" class="btn btn-light btn-sm">
                            <i class="fas fa-download me-2"></i>Unduh Laporan
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <?php if (isset($permissions['contacts']['view']) && $permissions['contacts']['view'] == 1): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card visits-card">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo $total_pemesanan; ?></h3>
                        <p>Total Pemesanan</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($permissions['news']['view']) && $permissions['news']['view'] == 1): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card news-card h-100">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo $total_news; ?></h3>
                        <p>Total News</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (isset($permissions['products']['view']) && $permissions['products']['view'] == 1): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card products-card h-100">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo $total_produk; ?></h3>
                        <p>Total Products</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($userLevel == '0' || (isset($permissions['users']['view']) && $permissions['users']['view'] == 1)): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card users-card h-100">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo $total_users; ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Role-specific content -->
    <?php if ($userLevel == '2'): // Sales role specific content ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Performa Penjualan</h5>
                    <!-- Add sales-specific content here -->
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content Area -->
    <div class="row g-3">
        <!-- Traffic and Sales Stats -->
        <div class="col-lg-7">
            <!-- Traffic Chart -->
            <div class="card chart-card mb-4">
                <div class="card-header border-0 bg-transparent">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0">Website Traffic</h5>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm active" id="dailyBtn" onclick="switchView('daily')">Daily</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="monthlyBtn" onclick="switchView('monthly')">Monthly</button>
                            </div>
                            <div class="date-filters d-flex gap-2">
                                <input type="date" id="startDate" class="form-control form-control-sm" onchange="autoUpdateChart()">
                                <input type="date" id="endDate" class="form-control form-control-sm" onchange="autoUpdateChart()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="trafficChart"></canvas>
                    <?php
                    // Set default date range (today)
                    $today = date('Y-m-d');
                    $sql_daily_visitors = "SELECT 
                        visit_date as date,
                        total_visitors 
                        FROM view_daily_visitors 
                        WHERE visit_date = '$today'
                        ORDER BY visit_date";
                    $result_daily_visitors = $conn->query($sql_daily_visitors);
                    
                    $daily_dates = [];
                    $daily_visitors = [];
                    
                    if ($result_daily_visitors) {
                        while($row = $result_daily_visitors->fetch_assoc()) {
                            $daily_dates[] = date('d M Y', strtotime($row['date']));
                            $daily_visitors[] = intval($row['total_visitors']);
                        }
                    }
                    ?>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Set default dates
                        const today = new Date();
                        document.getElementById('startDate').value = today.toISOString().split('T')[0];
                        document.getElementById('endDate').value = today.toISOString().split('T')[0];
                        
                        const ctx = document.getElementById('trafficChart').getContext('2d');
                        window.trafficChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: <?php echo json_encode($daily_dates); ?>,
                                datasets: [{
                                    label: 'Visitors',
                                    data: <?php echo json_encode($daily_visitors); ?>,
                                    borderColor: '#6366f1',
                                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                    borderWidth: 2,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#6366f1'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });

                        // Initial data load
                        autoUpdateChart();
                    });
                    </script>
                </div>
            </div>

            <?php if (isset($permissions['sales']['view']) && $permissions['sales']['view'] == 1): ?>
            <!-- Sales Analytics -->
            <div class="row g-3">
                <!-- Sales Source Distribution -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header border-0 bg-transparent">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-pie text-primary me-2"></i>
                                Distribusi Sumber
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="salesSourceChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Follow-up Results -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header border-0 bg-transparent">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-bar text-success me-2"></i>
                                Status Follow Up
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="followupResultsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Recent Activities - Keep only this one -->
        <div class="col-lg-5">
            <div class="card activities-card h-100">
                <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Activities</h5>
                    <span class="badge bg-primary rounded-pill"><?php echo $result_notifikasi ? $result_notifikasi->num_rows : '0'; ?></span>
                </div>
                <div class="card-body p-0" id="notificationsList">
                    <?php if ($result_notifikasi && $result_notifikasi->num_rows > 0): ?>
                        <?php while($row = $result_notifikasi->fetch_assoc()): ?>
                            <div class="activity-item notification-item" data-id="<?php echo $row['id']; ?>">
                                <a href="<?php echo htmlspecialchars($row['link']); ?>" class="d-flex align-items-center text-decoration-none text-dark">
                                    <div class="activity-icon bg-<?php echo htmlspecialchars($row['color']); ?> me-3">
                                        <i class="fas fa-<?php echo htmlspecialchars($row['icon']); ?>"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-semibold"><?php echo htmlspecialchars($row['message']); ?></p>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['time_ago']); ?></small>
                                    </div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-bell-slash fa-2x mb-3"></i>
                            <p class="mb-0">Tidak ada notifikasi</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($permissions['sales']['view']) && $permissions['sales']['view'] == 1): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Source Chart
        const salesSourceCtx = document.getElementById('salesSourceChart').getContext('2d');
        new Chart(salesSourceCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php 
                    if($result_sales_by_source && $result_sales_by_source->num_rows > 0) {
                        $labels = [];
                        $data = [];
                        while($row = $result_sales_by_source->fetch_assoc()) {
                            $labels[] = '"' . $row['source_name'] . '"';
                            $data[] = $row['count'];
                        }
                        echo implode(',', $labels);
                    } else {
                        echo '"Tidak ada data"';
                    }
                ?>],
                datasets: [{
                    data: [<?php 
                        if(isset($data) && !empty($data)) {
                            echo implode(',', $data);
                        } else {
                            echo "0";
                        }
                    ?>],
                    backgroundColor: [
                        '#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                        '#EC4899', '#06B6D4', '#84CC16', '#6366F1'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: {
                                size: 12
                            },
                            padding: 20
                        }
                    },
                    title: {
                        display: false
                    }
                }
            }
        });

        // Follow-up Results Chart
        const followupCtx = document.getElementById('followupResultsChart').getContext('2d');
        new Chart(followupCtx, {
            type: 'bar',
            data: {
                labels: [<?php 
                    if($result_followup_results && $result_followup_results->num_rows > 0) {
                        $labels = [];
                        $counts = [];
                        while($row = $result_followup_results->fetch_assoc()) {
                            $labels[] = '"' . $row['kategori'] . '"';
                            $counts[] = $row['count'];
                        }
                        echo implode(',', $labels);
                    } else {
                        echo '"Tidak ada data"';
                    }
                ?>],
                datasets: [{
                    label: 'Jumlah Follow Up',
                    data: [<?php 
                        if(isset($counts) && !empty($counts)) {
                            echo implode(',', $counts);
                        } else {
                            echo "0";
                        }
                    ?>],
                    backgroundColor: [
                        '#4F46E5',
                        '#10B981',
                        '#EF4444',
                        '#F59E0B'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
    </script>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/dashboard.js"></script>

<script>
// Tambahkan ini di bagian awal script
document.addEventListener('DOMContentLoaded', function() {
    // Load notifications when page loads
    loadNotifications();
    
    // Update notification badge
    updateNotificationBadge();
    
    // Refresh notifications every 30 seconds
    setInterval(function() {
        loadNotifications();
        updateNotificationBadge();
    }, 30000);
});

// Update loadNotifications function
function loadNotifications() {
    fetch('ajax/get_notifications.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const notificationsList = document.getElementById('notificationsList');
            
            if (!data || data.length === 0) {
                notificationsList.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-2x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada notifikasi</p>
                    </div>`;
                return;
            }

            notificationsList.innerHTML = data.map((notification, index) => `
                <div class="activity-item notification-item new-notification" data-id="${notification.id}">
                    <a href="${notification.link || '#'}" class="d-flex align-items-center">
                        <div class="activity-icon bg-${notification.color || 'primary'} me-3">
                            <i class="fas fa-${notification.icon || 'bell'}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 fw-semibold text-wrap">${notification.message}</p>
                            <small class="text-muted">${notification.time_ago}</small>
                        </div>
                    </a>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            document.getElementById('notificationsList').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-circle fa-2x text-danger mb-3"></i>
                    <p class="text-danger">Gagal memuat notifikasi</p>
                </div>`;
        });
}

// Remove conflicting event listeners
document.removeEventListener('DOMContentLoaded', loadNotifications);
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    setInterval(loadNotifications, 30000);
});

let currentView = 'daily';

function autoUpdateChart() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate || !endDate) return;
    
    if (new Date(endDate) < new Date(startDate)) {
        alert('End date cannot be earlier than start date');
        return;
    }

    updateChartWithDates(startDate, endDate);
}

function updateChartWithDates(startDate, endDate) {
    // Format dates based on view type
    let formattedStartDate, formattedEndDate;
    
    if (currentView === 'monthly') {
        // For monthly view, ensure we get full month data
        formattedStartDate = startDate + '-01';
        const endMonth = new Date(endDate.split('-')[0], endDate.split('-')[1], 0);
        formattedEndDate = endDate + '-' + endMonth.getDate();
    } else {
        formattedStartDate = startDate;
        formattedEndDate = endDate;
    }
    
    fetch('ajax/get_traffic_data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            period: currentView,
            startDate: formattedStartDate,
            endDate: formattedEndDate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.message);
        }
        
        trafficChart.data.labels = data.labels;
        trafficChart.data.datasets[0].data = data.values;
        trafficChart.data.datasets[0].label = data.label;
        trafficChart.update();
        
        // Update hidden form fields for report generation
        document.getElementById('reportStartDate').value = formattedStartDate;
        document.getElementById('reportEndDate').value = formattedEndDate;
        document.getElementById('reportPeriod').value = currentView;
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function switchView(view) {
    currentView = view;
    
    // Update button states
    document.getElementById('dailyBtn').classList.toggle('active', view === 'daily');
    document.getElementById('monthlyBtn').classList.toggle('active', view === 'monthly');
    
    // Update report period
    document.getElementById('reportPeriod').value = view;
    
    // Get current date
    const today = new Date();
    let startDate, endDate;
    
    if (view === 'monthly') {
        // Switch input types
        document.getElementById('startDate').type = 'month';
        document.getElementById('endDate').type = 'month';
        
        // Set default to current month
        startDate = today.toISOString().slice(0, 7); // YYYY-MM
        endDate = startDate;
    } else {
        // Switch back to date inputs
        document.getElementById('startDate').type = 'date';
        document.getElementById('endDate').type = 'date';
        
        // Set default to current day
        startDate = today.toISOString().slice(0, 10); // YYYY-MM-DD
        endDate = startDate;
    }
    
    // Update input values
    document.getElementById('startDate').value = startDate;
    document.getElementById('endDate').value = endDate;
    
    // Update chart with new dates
    updateChartWithDates(startDate, endDate);
}

// Initialize with current date/month
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const startDate = today.toISOString().slice(0, 10);
    const endDate = startDate;
    
    document.getElementById('startDate').value = startDate;
    document.getElementById('endDate').value = endDate;
    
    // Initial load with daily view
    switchView('daily');
});

function deleteNotification(notificationId, button) {
    fetch('ajax/delete_notification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            notification_id: notificationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationItem = button.closest('.notification-item');
            notificationItem.style.animation = 'fadeOut 0.3s ease forwards';
            setTimeout(() => {
                notificationItem.remove();
                // Update badge count
                const badge = document.querySelector('.badge');
                const currentCount = parseInt(badge.textContent) - 1;
                if (currentCount > 0) {
                    badge.textContent = currentCount;
                } else {
                    badge.style.display = 'none';
                }
            }, 300);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Add event listener for offcanvas show
document.getElementById('notificationOffcanvas').addEventListener('show.bs.offcanvas', function () {
    // Refresh notifications when offcanvas is opened
    fetch('ajax/get_notifications.php')
        .then(response => response.json())
        .then(data => {
            const notificationsList = document.getElementById('notificationsList');
            if (data.length > 0) {
                notificationsList.innerHTML = data.map(notification => `
                    <div class="notification-item" data-id="${notification.id}">
                        <a href="${notification.link}" class="notification-link">
                            <div class="d-flex align-items-center">
                                <div class="notification-icon bg-${notification.color}">
                                    <i class="fas fa-${notification.icon}"></i>
                                </div>
                                <div class="notification-content">
                                    <p class="notification-text">${notification.message}</p>
                                    <small class="notification-time">${notification.time_ago}</small>
                                </div>
                            </div>
                        </a>
                        <button class="btn-close delete-notification" 
                                onclick="event.preventDefault(); event.stopPropagation(); deleteNotification(${notification.id}, this)"
                                title="Delete notification">
                        </button>
                    </div>
                `).join('');
            } else {
                notificationsList.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No notifications</p>
                    </div>
                `;
            }
        })
        .catch(error => console.error('Error:', error));
});

// Update notification loading function
function loadNotifications() {
    fetch('ajax/get_notifications.php')
        .then(response => response.json())
        .then(data => {
            const notificationsList = document.getElementById('notificationsList');
            
            if (data.length === 0) {
                notificationsList.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No notifications available</p>
                    </div>`;
                return;
            }

            notificationsList.innerHTML = data.map((notification, index) => `
                <div class="notification-item" style="animation-delay: ${index * 0.1}s">
                    <a href="${notification.link}" class="d-flex p-3 text-dark text-decoration-none">
                        <div class="notification-icon bg-${notification.color} me-3">
                            <i class="fas fa-${notification.icon}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 fw-semibold">${notification.message}</p>
                            <small class="text-muted">${notification.time_ago}</small>
                        </div>
                        <button class="btn-close delete-notification" 
                                onclick="event.preventDefault(); event.stopPropagation(); deleteNotification(${notification.id}, this)"
                                aria-label="Delete"></button>
                    </a>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            document.getElementById('notificationsList').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-circle fa-2x text-danger mb-3"></i>
                    <p class="text-danger">Failed to load notifications</p>
                </div>`;
        });
}

// Refresh notification badge
function updateNotificationBadge() {
    fetch('ajax/get_notification_count.php')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.notification-badge');
            if (data.count > 0) {
                if (!badge) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'notification-badge';
                    newBadge.textContent = data.count;
                    document.querySelector('.notification-btn').appendChild(newBadge);
                } else {
                    badge.textContent = data.count;
                }
            } else if (badge) {
                badge.remove();
            }
        });
}
</script>

</body>
</html>
<?php endif;?>