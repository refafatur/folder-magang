<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db/config.php';

// Get statistics
$today = date('Y-m-d');


// This month's statistics
$firstDayOfMonth = date('Y-m-01');
$stmt = $pdo->prepare("SELECT 
    COUNT(*) as total_bookings,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
FROM bookings WHERE booking_date >= ?");
$stmt->execute([$firstDayOfMonth]);
$monthStats = $stmt->fetch();

// Get recent bookings with more details
$stmt = $pdo->prepare("SELECT * FROM bookings ORDER BY booking_date DESC, booking_time DESC LIMIT 6");
$stmt->execute();
$recentBookings = $stmt->fetchAll();

// Get additional statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total_courses FROM tbl_courses");
$stmt->execute();
$courseStats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) as total_events FROM tbl_events");
$stmt->execute();
$eventStats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) as total_promotions FROM tbl_promotions");
$stmt->execute();
$promotionStats = $stmt->fetch();

// Get today's schedules
$stmt = $pdo->prepare("SELECT TIME_FORMAT(booking_time, '%H:%i') as formatted_time, status 
                       FROM bookings 
                       WHERE booking_date = ? 
                       ORDER BY booking_time ASC");
$stmt->execute([$today]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golf Club - Dashboard</title>
    <link rel="icon" href="assets/photos/Logo Master KGBR.avif" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="components/sidemenu/style.css">
    <style>
        /* Keep only dashboard specific styles */
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            transition: all 0.3s ease;
        }
        .dashboard-container {
            padding: 2rem;
            background: #f8fafc;
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 10px;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 1rem;
            }

            .quick-actions {
                width: 100%;
                justify-content: space-between;
            }

            .action-btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .booking-cards {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }

            .stat-info .value {
                font-size: 1.5rem;
            }

            .weather-item {
                padding: 0.8rem;
            }

            .weather-item i {
                width: 45px;
                height: 45px;
                font-size: 1.5rem;
            }

            .weather-info h4 {
                font-size: 1rem;
            }

            .weather-temp {
                font-size: 1.2rem;
            }

            .booking-card {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .quick-actions {
                flex-direction: column;
                gap: 0.5rem;
            }

            .action-btn {
                width: 100%;
                justify-content: center;
            }

            .weather-info {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .weather-desc {
                text-align: center;
            }
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .welcome-section h1 {
            font-size: 1.8rem;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }
        .current-date {
            color: #666;
            font-size: 1rem;
        }
        .quick-actions {
            display: flex;
            gap: 1rem;
        }
        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            background: white;
            color: #1a1a1a;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.highlight {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .highlight .stat-icon {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .stat-info {
            flex: 1;
        }
        .stat-info h3 {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: inherit;
        }
        .stat-info .value {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .sub-value {
            font-size: 0.85rem;
            color: #666;
        }
        .highlight .sub-value {
            color: rgba(255,255,255,0.8);
        }
        .recent-bookings-section {
            margin-top: 2rem;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .section-header h2 {
            font-size: 1.2rem;
            color: #333;
        }
        .view-all {
            text-decoration: none;
            color: #007bff;
            font-size: 0.9rem;
        }
        .view-all:hover {
            text-decoration: underline;
        }
        .booking-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.2rem;
        }
        
        .booking-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .booking-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .booking-date {
            text-align: center;
            padding: 0.8rem;
            background: #f8f9fa;
            border-radius: 12px;
            min-width: 80px;
        }
        
        .booking-date .day {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1;
        }
        
        .booking-date .month {
            font-size: 1rem;
            color: #4a5568;
            text-transform: uppercase;
            margin-top: 4px;
        }
        
        .booking-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .booking-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .customer-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .customer-info .name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
        }
        
        .customer-info .players {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a5568;
        }
        
        .customer-info .players i {
            color: #3182ce;
        }
        
        .view-details {
            background: #ebf8ff;
            border: none;
            padding: 0.8rem;
            border-radius: 12px;
            cursor: pointer;
            color: #3182ce;
            transition: all 0.3s ease;
        }
        
        .view-details:hover {
            background: #bee3f8;
            transform: scale(1.1);
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .dashboard-section {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .weather-forecast {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .weather-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.2rem;
            background: linear-gradient(to right, #f0f9ff, #e6f3ff);
            border-radius: 12px;
            transition: transform 0.3s ease;
        }
        
        .weather-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .weather-item i {
            font-size: 2rem;
            color: #f59e0b;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.8);
            border-radius: 50%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .weather-info {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            align-items: center;
        }
        
        .weather-info h4 {
            grid-column: 1 / -1;
            margin: 0;
            font-size: 1.1rem;
            color: #1e3a8a;
            font-weight: 600;
        }
        
        .weather-temp {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e40af;
        }
        
        .weather-desc {
            text-align: right;
            color: #3b82f6;
            font-weight: 500;
        }
        
        .wind-speed {
            grid-column: 1 / -1;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>

</head>
<body>
    <div class="main-header">
        <div class="header-left">
            <button class="hamburger" onclick="toggleMenu()" aria-label="Toggle Menu">
                <i class="fas fa-bars"></i>
            </button>
            <h2 style="margin-left: 15px; color: var(--primary-color);">Golf Club</h2>
        </div>
        <div class="header-right">
            <a href="dashboard/profile.php" class="header-profile" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                <img src="<?php echo isset($_SESSION['foto']) && $_SESSION['foto'] ? 'uploads/users/' . $_SESSION['foto'] : 'https://cdn-icons-png.flaticon.com/512/219/219969.png'; ?>" 
                     alt="Profile" id="preview-image">
                <span><?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Guest'; ?></span>
            </a>
        </div>
    </div>
    
    <div class="side-menu">
        <div class="user-profile">
            <div class="profile-container" style="border: none;">
            <img src="../assets/photos/Logo Master KGBR.avif" 
                 alt="Golf Club Logo" 
                 style="width: 120px; height: auto; margin: 10px;">
            </div>
        </div>
        <ul class="menu-items">
            <li><a href="#"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="dashboard/course.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'course.php' ? 'active-menu' : ''; ?>"><i class="fas fa-flag"></i> <span>Course Overview</span></a></li>
            <li><a href="dashboard/events.php"><i class="fas fa-calendar-day"></i> <span>Event</span></a></li>
            <li><a href="dashboard/promotions.php"><i class="fas fa-percent"></i> <span>Promotion</span></a></li>
            <li><a href="dashboard/locker.php"><i class="fas fa-door-closed"></i> <span>Locker Room</span></a></li>
            <li><a href="dashboard/driving.php"><i class="fas fa-golf-ball"></i> <span>Driving Range</span></a></li>
            <li><a href="dashboard/food.php"><i class="fas fa-utensils"></i> <span>Food & Beverage</span></a></li>
            <li><a href="dashboard/vip.php"><i class="fas fa-crown"></i> <span>VIP Room & Matana</span></a></li>
            <!-- <li>
                <a href="#" class="has-submenu" onclick="toggleSubmenu(event, this)">
                    <i class="fas fa-heart"></i>
                    <span>Wedding Services</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="dashboard/pre-wedding.php"><i class="fas fa-camera"></i> <span>Pre-Wedding Shoot</span></a></li>
                    <li><a href="dashboard/wedding.php"><i class="fas fa-ring"></i> <span>Wedding Venue</span></a></li>
                </ul>
            </li> -->
            <li><a href="../dashboard/wedding.php"><i class="fas fa-ring"></i> <span>Wedding Venue</span></a></li>
            <li><a href="dashboard/meet.php"><i class="fas fa-handshake"></i> <span>Meeting & Gathering</span></a></li>
            <li><a href="dashboard/booking.php"><i class="fas fa-book"></i> <span>Booking</span></a></li>
            <li class="logout-item">
                <a href="#" onclick="showLogoutModal(event)" class="logout-link" 
                   style="display: flex; align-items: center; padding: 12px 15px; 
                          border-radius: 8px; margin: 10px 15px; 
                          background: rgba(220, 53, 69, 0.1); 
                          transition: all 0.3s ease;">
                    <i class="fas fa-sign-out-alt" style="color: #dc3545; margin-right: 10px; font-size: 1.1em;"></i>
                    <span style="color: #dc3545; font-weight: 600;">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal" class="modal-overlay">
        <div class="modal-content">
            <h4>Konfirmasi Logout</h4>
            <p>Apakah Anda yakin ingin keluar?</p>
            <div class="modal-buttons">
                <button onclick="logout()" class="btn-danger">Ya, Keluar</button>
                <button onclick="hideLogoutModal()" class="btn-secondary">Batal</button>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <div class="welcome-section">
                    <?php
                    // Set timezone to Indonesia/Jakarta
                    date_default_timezone_set('Asia/Jakarta');
                    
                    $hour = date('H');
                    $greeting = '';
                    if ($hour < 12) {
                        $greeting = 'Good Morning';
                    } elseif ($hour < 17) {
                        $greeting = 'Good Afternoon'; 
                    } else {
                        $greeting = 'Good Evening';
                    }
                    ?>
                    <h1><?= $greeting ?>!</h1>
                    <p class="current-date"><?= date('l, d F Y') ?></p>
                </div>
                <div class="quick-actions">
                    <button class="action-btn" style="position:relative;" onclick="location.href='dashboard/booking.php'">
                        <i class="fas fa-book"></i>
                        <span>Total Booking</span>
                        <?php if (!empty($monthStats['total_bookings'])): ?>
                        <span style="
                            position: absolute;
                            top: 2px;
                            right: 10px;
                            background: #dc3545;
                            color: #fff;
                            font-size: 0.7rem;
                            font-weight: bold;
                            border-radius: 50%;
                            padding: 2px 6px;
                            min-width: 18px;
                            text-align: center;
                            box-shadow: 0 2px 6px rgba(220,53,69,0.15);
                        ">
                            <?= $monthStats['total_bookings'] ?>
                        </span>
                        <?php endif; ?>
                    </button>
                    <!-- <button class="action-btn" onclick="location.href='reports.php'">
                        <i class="fas fa-chart-line"></i>
                        <span>View Reports</span>
                    </button> -->
                </div>
            </div>

            <div class="stats-grid">
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-golf-ball"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Available Courses</h3>
                        <div class="value"><?= $courseStats['total_courses'] ?></div>
                        <div class="sub-value">Active golf courses</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Active Events</h3>
                        <div class="value"><?= $eventStats['total_events'] ?></div>
                        <div class="sub-value">Scheduled events</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Active Promotions</h3>
                        <div class="value"><?= $promotionStats['total_promotions'] ?></div>
                        <div class="sub-value">Current offers</div>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Bookings</h2>
                        <a href="dashboard/booking.php" class="view-all">View All</a>
                    </div>
                    <div class="booking-cards">
                        <?php
                        // Ambil data booking hari ini dari recentBookings, waktu dari schedules (JSON-friendly)
                        $todayBookings = array_filter($recentBookings, function($booking) use ($today) {
                            return $booking['booking_date'] === $today;
                        });

                        // Ambil semua waktu booking hari ini dalam format array JSON
                        $jsonTimes = array_map(function($schedule) {
                            return $schedule['formatted_time'] . ':00';
                        }, $schedules);

                        // Gabungkan ke string JSON-like
                        $jsonTimesString = implode(',', $jsonTimes);

                        if (count($todayBookings) > 0):
                            foreach ($todayBookings as $bookingDetail):
                                // Ambil waktu booking asli (format 05:44:00)
                                $bookingTime = date('H:i:s', strtotime($bookingDetail['booking_time']));
                        ?>
                                <div class="booking-card" style="border-left: 6px solid 
                                    <?php
                                        switch(strtolower($bookingDetail['status'])) {
                                            case 'confirmed': echo '#4caf50'; break;
                                            case 'pending': echo '#ff9800'; break;
                                            case 'cancelled': echo '#dc3545'; break;
                                            case 'completed': echo '#2196f3'; break;
                                            default: echo '#ccc';
                                        }
                                    ?>;">
                                    <div class="booking-card-header">
                                        <div class="booking-date" style="background: #e3f6fc;">
                                            <div class="day" style="color:#2196f3;"><?= date('d', strtotime($bookingDetail['booking_date'])) ?></div>
                                            <div class="month" style="color:#555;"><?= date('M', strtotime($bookingDetail['booking_date'])) ?></div>
                                        </div>
                                        <div class="booking-status status-<?= strtolower($bookingDetail['status']) ?>" style="
                                            background:
                                            <?php
                                                switch(strtolower($bookingDetail['status'])) {
                                                    case 'confirmed': echo 'rgba(76,175,80,0.15); color:#388e3c;'; break;
                                                    case 'pending': echo 'rgba(255,152,0,0.15); color:#f57c00;'; break;
                                                    case 'cancelled': echo 'rgba(220,53,69,0.15); color:#c82333;'; break;
                                                    case 'completed': echo 'rgba(33,150,243,0.15); color:#1565c0;'; break;
                                                    default: echo 'rgba(204,204,204,0.15); color:#888;';
                                                }
                                            ?>
                                            font-weight:600; min-width:90px; text-align:center;">
                                            <?= ucfirst($bookingDetail['status']) ?>
                                        </div>
                                    </div>
                                    <div class="booking-details" style="margin-top:1rem;">
                                        <div class="customer-info">
                                            <span class="name" style="font-size:1.15rem; color:#222;"><?= htmlspecialchars($bookingDetail['name']) ?></span>
                                            <span class="players" style="color:#666; font-size:0.95rem;">
                                                <?= $bookingDetail['players'] ?> Players
                                            </span>
                                            <span style="color:#888; font-size:0.9rem;">
                                                <?= $jsonTimesString ?> WIB
                                            </span>
                                            <?php if (!empty($bookingDetail['course_name'])): ?>
                                                <span style="color:#2196f3; font-size:0.95rem;">
                                                    <?= htmlspecialchars($bookingDetail['course_name']) ?>
                                                </span>
                                            <?php endif; ?>

                                        </div>
                                        <div style="text-align:right;">
                                            <span style="background:#f8fafc; color:#555; padding:6px 14px; border-radius:8px; font-size:0.95rem;">
                                                Kode: <?= htmlspecialchars($bookingDetail['booking_code']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            endforeach;
                        else: ?>
                            <div style="padding: 2rem; text-align: center; color: #888;">
                                Tidak ada booking hari ini.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Weather Forecast</h2>
                        <a href="#" class="view-all">3 Day Forecast</a>
                    </div>
                    <div class="weather-forecast">
                        <div class="weather-item">
                            <i class="fas fa-sun"></i>
                            <div class="weather-info">
                                <h4>Today</h4>
                                <span class="weather-temp">28°C</span>
                                <span class="weather-desc">Sunny</span>
                                <span class="wind-speed">Wind: 12 km/h</span>
                            </div>
                        </div>
                        <div class="weather-item">
                            <i class="fas fa-cloud-sun"></i>
                            <div class="weather-info">
                                <h4>Tomorrow</h4>
                                <span class="weather-temp">26°C</span>
                                <span class="weather-desc">Partly Cloudy</span>
                                <span class="wind-speed">Wind: 15 km/h</span>
                            </div>
                        </div>
                        <div class="weather-item">
                            <i class="fas fa-cloud"></i>
                            <div class="weather-info">
                                <h4>Day After</h4>
                                <span class="weather-temp">25°C</span>
                                <span class="weather-desc">Cloudy</span>
                                <span class="wind-speed">Wind: 18 km/h</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <script>
        // Load menu state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sideMenu = document.querySelector('.side-menu');
            const mainHeader = document.querySelector('.main-header');
            const mainContent = document.querySelector('.main-content');
            
            // Check localStorage for saved state
            if(localStorage.getItem('menuState') === 'collapsed') {
                sideMenu.classList.add('collapsed');
                mainHeader.style.left = '70px';
                mainHeader.style.width = 'calc(100% - 70px)';
                if(mainContent) {
                    mainContent.style.marginLeft = '70px';
                    mainContent.style.width = 'calc(100% - 70px)';
                }
            }

            // Create overlay for mobile
            const overlay = document.createElement('div');
            overlay.className = 'menu-overlay';
            document.body.appendChild(overlay);
            
            overlay.addEventListener('click', function() {
                document.querySelector('.side-menu').classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        });

        function toggleMenu() {
            const sideMenu = document.querySelector('.side-menu');
            const mainHeader = document.querySelector('.main-header');
            const mainContent = document.querySelector('.main-content');
            
            if (window.innerWidth <= 768) {
                // Mobile behavior
                sideMenu.classList.toggle('active');
                document.querySelector('.menu-overlay').classList.toggle('active');
            } else {
                // Desktop behavior
                sideMenu.classList.toggle('collapsed');
                
                // Save menu state to localStorage
                localStorage.setItem('menuState', sideMenu.classList.contains('collapsed') ? 'collapsed' : 'expanded');
                
                // Adjust header and content width
                if (sideMenu.classList.contains('collapsed')) {
                    mainHeader.style.left = '70px';
                    mainHeader.style.width = 'calc(100% - 70px)';
                    mainContent.style.marginLeft = '70px';
                    mainContent.style.width = 'calc(100% - 70px)';
                } else {
                    mainHeader.style.left = '250px';
                    mainHeader.style.width = 'calc(100% - 250px)';
                    mainContent.style.marginLeft = '250px';
                    mainContent.style.width = 'calc(100% - 250px)';
                }
            }
        }

    // Close menu when clicking overlay
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.createElement('div');
        overlay.className = 'menu-overlay';
        document.body.appendChild(overlay);

        overlay.addEventListener('click', function() {
            document.querySelector('.side-menu').classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    });

        function toggleSubmenu(event, element) {
            event.preventDefault();
            if (!document.querySelector('.side-menu').classList.contains('collapsed')) {
                const submenu = element.nextElementSibling;
                const dropdownIcon = element.querySelector('.dropdown-icon');
                submenu.classList.toggle('show');
                dropdownIcon.classList.toggle('rotate');
            }
        }

        function showLogoutModal(event) {
            event.preventDefault();
            document.getElementById('logoutModal').style.display = 'flex';
        }
        
        function hideLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }
        
        function logout() {
            window.location.href = 'dashboard/logout.php';
        }
    </script>
</body>
</html>
