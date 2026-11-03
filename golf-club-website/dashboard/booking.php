<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Add these lines after session check
$today = date('Y-m-d');
$first_day_of_month = date('Y-m-01');
$date_from = $_GET['date_from'] ?? $first_day_of_month;
$date_to = $_GET['date_to'] ?? $today;

require_once '../db/config.php';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['new_status'];
    
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $booking_id]);
    
    header("Location: booking.php");
    exit();
}

// Add this after the status update handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->execute([$booking_id]);
    
    header("Location: booking.php");
    exit();
}

// Tambahkan ini setelah POST delete_booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_multiple'])) {
    $booking_ids = $_POST['booking_ids'];
    
    $placeholders = str_repeat('?,', count($booking_ids) - 1) . '?';
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id IN ($placeholders)");
    $stmt->execute($booking_ids);
    
    header("Location: booking.php");
    exit();
}

// Build filter conditions
$where_conditions = [];
$params = [];

if (!empty($_GET['date_from'])) {
    $where_conditions[] = "booking_date >= ?";
    $params[] = $_GET['date_from'];
}

if (!empty($_GET['date_to'])) {
    $where_conditions[] = "booking_date <= ?";
    $params[] = $_GET['date_to'];
}

if (!empty($_GET['status'])) {
    $where_conditions[] = "status = ?";
    $params[] = $_GET['status'];
}

if (!empty($_GET['search'])) {
    $search = "%{$_GET['search']}%";
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR booking_code LIKE ?)";
    $params = array_merge($params, [$search, $search, $search, $search]);
}

// Build the final query
$query = "SELECT * FROM bookings";
if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}
$query .= " ORDER BY booking_date DESC, booking_time DESC";

// Execute the filtered query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golf Club - Booking Management</title>
    <link rel="icon" href="../assets/photos/Logo Master KGBR.avif" type="image/png">
    <link rel="icon" type="image/x-icon" href="">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/booking.css">
    <style>
        .filter-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filter-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 15px;
        }

        .filter-item {
            flex: 1;
            min-width: 200px;
        }

        .filter-item label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        .filter-item input[type="date"],
        .filter-item input[type="text"],
        .filter-item select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .filter-item input[type="date"]:focus,
        .filter-item input[type="text"]:focus,
        .filter-item select:focus {
            border-color: goldenrod;
            outline: none;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
        }

        /* Enhance existing table styles */
        .booking-table {
            margin-top: 20px;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .booking-table th {
            background-color: goldenrod;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .booking-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .booking-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        /* New styles for card view */
        .view-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .view-btn {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
            color: #333;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .view-btn.active {
            background: goldenrod;
            color: white;
        }

        .view-btn i {
            margin-right: 5px;
        }

        .view-container {
            display: block;
        }

        .booking-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .booking-card {
            position: relative;
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.8)), var(--default-bg);
            background-size: cover;
            background-position: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .card-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 75%;
            height: 75%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.15;
            transition: opacity 0.3s ease;
        }

        .card-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            color: white;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
        }

        .customer-info {
            color: white;
        }

        .customer-name {
            color: white;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .customer-info p {
            margin: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9em;
            opacity: 0.9;
        }

        .booking-code {
            color: white;
            font-size: 1.1em;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            color: white;
        }

        .status-pending {
            background: orange;
        }

        .status-confirmed {
            background: green;
        }

        .status-cancelled {
            background: red;
        }

        .status-completed {
            background: blue;
        }

        .booking-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .booking-modal.active {
            display: flex;
            opacity: 1;
        }

        .booking-modal-content {
            position: relative;
            width: 95%;
            max-width: 1200px;
            margin: auto;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            transform: scale(0.8);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            padding: 20px;
            background: goldenrod;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .modal-body {
            display: flex;
            min-height: 500px;
        }

        .left-section {
            width: 300px;
            background:rgb(255, 255, 255);
            background-size: cover;
            background-position: center;
            color: white;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .booking-details {
            flex: 1;
            padding: 2rem;
        }

        .booking-code-section {
            text-align: center;
            margin: 20px 0;
        }

        .booking-code-section .label {
            color: rgba(255,255,255,0.7);
        }

        .booking-code-section h3 {
            font-size: 2em;
            color: white;
            margin: 10px 0;
        }

        .status-indicator {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
            margin: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .status-indicator.status-pending {
            background: linear-gradient(45deg, #ffa726, #fb8c00);
        }

        .status-indicator.status-confirmed {
            background: linear-gradient(45deg, #66bb6a, #43a047);
        }

        .status-indicator.status-cancelled {
            background: linear-gradient(45deg, #ef5350, #e53935);
        }

        .status-indicator.status-completed {
            background: linear-gradient(45deg, #42a5f5, #1e88e5);
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            padding: 1rem;
        }

        .detail-group {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .detail-group i {
            color: goldenrod;
            font-size: 1.5em;
            width: 24px;
        }

        .detail-content {
            flex: 1;
        }

        .detail-content label {
            display: block;
            color: #666;
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        .detail-value {
            color: #333;
            font-size: 1.1em;
            line-height: 1.4;
        }

        .modal-footer {
            padding: 20px;
            text-align: right;
            border-top: 1px solid #eee;
        }

        .btn-secondary {
            background: #666;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-secondary:hover {
            background: #555;
        }

        /* Add these new status styles in the <style> section */
        .status-form select {
            padding: 8px 15px;
            border-radius: 20px;
            border: none;
            font-weight: 600;
            color: white;
            cursor: pointer;
            width: 120px;
            text-align: center;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="white" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 8px center;
        }

        .status-form select.status-pending {
            background-color: #ffa726;
        }

        .status-form select.status-confirmed {
            background-color: #66bb6a;
        }

        .status-form select.status-cancelled {
            background-color: #ef5350;
        }

        .status-form select.status-completed {
            background-color: #42a5f5;
        }

        .status-form select option {
            background: white;
            color: #333;
            padding: 5px;
        }

        /* Enhanced Notification Styles */
        .notification {
            position: fixed;
            top: 30px;
            right: 30px;
            width: min(90vw, 400px);
            background: rgba(255, 255, 255, 0.95);
            border-left: 5px solid goldenrod;
            color: #333;
            border-radius: 10px;
            padding: 0;
            box-shadow: 0 5px 30px rgba(0,0,0,0.15);
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            backdrop-filter: blur(10px);
            z-index: 9999;
        }

        @media (max-width: 768px) {
            .notification {
                top: auto;
                bottom: 20px;
                right: 20px;
                left: 20px;
                width: auto;
                transform: translateY(120%);
            }
            
            .notification.show {
                transform: translateY(0);
            }
        }

        .notification.show {
            transform: translateX(0);
            opacity: 1;
        }

        .notification-header {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .notification-icon {
            background: linear-gradient(135deg, goldenrod, #ffd700);
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
            color: white;
            box-shadow: 0 3px 10px rgba(218,165,32,0.3);
        }

        .notification-content {
            padding: 15px 20px;
            line-height: 1.6;
        }

        .notification-content p {
            margin: 5px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #555;
        }

        .notification-content p i {
            color: goldenrod;
            width: 20px;
        }

        .notification-content strong {
            color: #222;
        }

        .notification-actions {
            padding: 15px 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            border-top: 1px solid rgba(0,0,0,0.1);
        }

        .notification-btn {
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .notification-btn.primary {
            background: goldenrod;
            color: white;
        }

        .notification-btn.primary:hover {
            background: #daa520;
            transform: translateY(-2px);
        }

        .notification-btn.secondary {
            background: #f0f0f0;
            color: #666;
        }

        .notification-btn.secondary:hover {
            background: #e0e0e0;
        }

        .notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(218,165,32,0.2);
        }

        .progress-bar {
            height: 100%;
            background: goldenrod;
            width: 100%;
            transition: width 5s linear;
        }

        /* Add responsive styles for the booking table */
        @media (max-width: 1024px) {
            .booking-table th:nth-child(4),
            .booking-table td:nth-child(4) {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .booking-table th:nth-child(2),
            .booking-table td:nth-child(2) {
                display: none;
            }
            
            .filter-group {
                flex-direction: column;
            }
            
            .filter-item {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .booking-cards {
                grid-template-columns: 1fr;
            }
            
            .view-toggle {
                flex-direction: column;
            }
        }

        .action-btn.btn-delete {
            background-color: #dc3545;
            color: white;
            margin-left: 5px;
        }

        .action-btn.btn-delete:hover {
            background-color: #c82333;
        }

        .delete-notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            background: white;
            padding: 0;
            border-radius: 15px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.3);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            width: 90%;
            max-width: 400px;
        }

        .delete-notification.show {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
            visibility: visible;
        }

        .delete-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
        }

        .delete-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .delete-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .delete-icon {
            background: #fee2e2;
            color: #dc2626;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
        }

        .delete-content {
            padding: 20px;
            text-align: center;
        }

        .delete-content h3 {
            color: #111;
            margin-bottom: 10px;
        }

        .delete-content p {
            color: #666;
            line-height: 1.5;
        }

        .delete-actions {
            padding: 20px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .delete-btn {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .delete-btn.cancel {
            background: #e5e7eb;
            color: #374151;
            border: none;
        }

        .delete-btn.cancel:hover {
            background: #d1d5db;
        }

        .delete-btn.confirm {
            background: #dc2626;
            color: white;
            border: none;
        }

        .delete-btn.confirm:hover {
            background: #b91c1c;
        }

        .bulk-action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
            transition: all 0.3s;
        }

        .bulk-action-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .bulk-action-btn:first-child {
            background: #dc3545;
            color: white;
        }

        .bulk-action-btn:last-child {
            background: #343a40;
            color: white;
        }

        .bulk-action-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .quick-delete {
            opacity: 0;
            transition: opacity 0.3s;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        tr:hover .quick-delete {
            opacity: 1;
        }

        .card-actions {
            position: absolute;
            top: 10px;
            left: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .booking-card:hover .card-actions {
            opacity: 1;
        }

        .card-actions .action-btn {
            padding: 6px 12px;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .card-actions .btn-delete {
            background-color: rgba(220, 53, 69, 0.9);
            color: white;
        }

        .card-actions .btn-delete:hover {
            background-color: #dc3545;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Add notification element after body tag -->
    <div id="bookingNotification" class="notification">
        <div class="notification-header">
            <div class="notification-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3 class="notification-title">New Booking!</h3>
        </div>
        <div class="notification-body">
            <div class="notification-content" id="notificationMessage"></div>
            <div class="notification-actions">
                <button class="notification-btn primary" onclick="viewLatestBooking()">View Details</button>
                <button class="notification-btn secondary" onclick="dismissNotification()">Dismiss</button>
            </div>
        </div>
        <div class="notification-progress">
            <div class="progress-bar"></div>
        </div>
    </div>

    <?php include '../components/sidemenu/index.php'; ?>
    <div class="main-content">
        <div class="dashboard-container">
            <h1>Booking Management</h1>
            
            <div class="filter-container">
                <form action="" method="GET" class="filter-group" id="filterForm">
                    <div class="filter-item">
                        <label>From Date</label>
                        <input type="date" name="date_from" value="<?= $date_from ?>" onchange="this.form.submit()">
                    </div>
                    <div class="filter-item">
                        <label>To Date</label>
                        <input type="date" name="date_to" value="<?= $date_to ?>" onchange="this.form.submit()">
                    </div>
                    <div class="filter-item">
                        <label>Status</label>
                        <select name="status" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= ($_GET['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>Sort By</label>
                        <select name="sort" id="sortSelect" onchange="applySorting(this.value)">
                            <option value="">Default</option>
                            <option value="name_asc">Name (A-Z)</option>
                            <option value="name_desc">Name (Z-A)</option>
                            <option value="date_newest">Newest First</option>
                            <option value="date_oldest">Oldest First</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>Search</label>
                        <input type="text" name="search" id="searchInput" 
                               placeholder="Search by name, email, phone..." 
                               value="<?= $_GET['search'] ?? '' ?>"
                               oninput="filterTable(this.value)">
                    </div>
                </form>
                <div class="view-toggle">
                    <button type="button" class="view-btn active" onclick="toggleView('table')" id="tableViewBtn">
                        <i class="fas fa-list"></i> Table View
                    </button>
                    <button type="button" class="view-btn" onclick="toggleView('card')" id="cardViewBtn">
                        <i class="fas fa-th"></i> Card View
                    </button>
                </div>
            </div>

            <div id="tableView" class="view-container">
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                            </th>
                            <th>Booking Code</th>
                            <th>Customer</th>
                            <th>Date & Time</th>
                            <th>Players</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        <tr>
                            <th colspan="7">
                                <button type="button" class="bulk-action-btn" onclick="deleteSelected()" disabled id="deleteSelectedBtn">
                                    <i class="fas fa-trash-alt"></i> Delete Selected
                                </button>
                                <button type="button" class="bulk-action-btn" onclick="confirmDeleteAll()">
                                    <i class="fas fa-trash"></i> Delete All
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center; color:#888; padding:40px 0;">
                                    <i class="fas fa-calendar-times" style="font-size:2em; color:#ccc;"></i><br>
                                    <strong>No bookings found.</strong><br>
                                    There are no bookings to display for the selected filter.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="booking-checkbox" value="<?= $booking['id'] ?>" onchange="updateDeleteButton()">
                                    </td>
                                    <td><?= htmlspecialchars($booking['booking_code']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($booking['name']) ?><br>
                                        <small><?= htmlspecialchars($booking['email']) ?></small><br>
                                        <small><?= htmlspecialchars($booking['phone']) ?></small>
                                    </td>
                                    <td>
                                        <?= date('d M Y', strtotime($booking['booking_date'])) ?><br>
                                        <small><?= date('H:i', strtotime($booking['booking_time'])) ?></small>
                                    </td>
                                    <td><?= $booking['players'] ?></td>
                                    <td>
                                        <form method="POST" class="status-form">
                                            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                            <input type="hidden" name="update_status" value="1">
                                            <select name="new_status" onchange="this.form.submit()" class="status-<?= strtolower($booking['status']) ?>">
                                                <option value="pending" <?= $booking['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="confirmed" <?= $booking['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                <option value="cancelled" <?= $booking['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                <option value="completed" <?= $booking['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <button class="action-btn btn-edit" onclick="viewDetails('<?= $booking['booking_code'] ?>')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="action-btn btn-delete" onclick="deleteBooking(<?= $booking['id'] ?>, '<?= $booking['booking_code'] ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="cardView" class="view-container" style="display: none;">
                <div class="booking-cards">
                    <?php if (empty($bookings)): ?>
                        <div style="grid-column: 1/-1; text-align: center; color: #888; padding: 40px 0;">
                            <i class="fas fa-calendar-times" style="font-size:2em; color:#ccc;"></i><br>
                            <strong>No bookings found.</strong><br>
                            There are no bookings to display for the selected filter.
                        </div>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <div class="booking-card" onclick="viewDetails('<?= $booking['booking_code'] ?>')">
                                <div class="card-logo">
                                    <img src="./../assets/photos/Logo Master KGBR.avif" alt="KGBR Logo">
                                </div>
                                <div class="card-content">
                                    <span class="status-badge status-<?= strtolower($booking['status']) ?>">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                    <h3 class="booking-code"><?= htmlspecialchars($booking['booking_code']) ?></h3>
                                    <div class="customer-info">
                                        <h4 class="customer-name"><?= htmlspecialchars($booking['name']) ?></h4>
                                        <p><i class="far fa-calendar"></i> <?= date('d M Y', strtotime($booking['booking_date'])) ?></p>
                                        <p><i class="far fa-clock"></i> <?= date('H:i', strtotime($booking['booking_time'])) ?></p>
                                        <p><i class="fas fa-users"></i> <?= $booking['players'] ?> Players</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal HTML -->
    <div class="booking-modal" id="bookingModal">
        <div class="booking-modal-content">
            <div class="modal-header">
                <h2>Booking Details</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="left-section">
                    <div class="card-logo">
                        <img src="./../assets/photos/Logo Master KGBR.avif" alt="KGBR Logo" width="auto" height="100%">
                    </div>
                    <div class="booking-status">
                        <span class="status-indicator" id="modalStatus"></span>
                    </div>
                    <div class="booking-code-section">
                        <span class="label" style="color:black;">Booking Code</span>
                        <h3 style="color:black;" id="modalBookingCode"></h3>
                    </div>
                </div>
                <div class="booking-details">
                    <div class="details-grid">
                        <div class="detail-group">
                            <i class="fas fa-user"></i>
                            <div class="detail-content">
                                <label>Customer Name</label>
                                <div class="detail-value" id="modalCustomerName"></div>
                            </div>
                        </div>
                        <div class="detail-group">
                            <i class="fas fa-envelope"></i>
                            <div class="detail-content">
                                <label>Contact Info</label>
                                <div class="detail-value" id="modalContact"></div>
                            </div>
                        </div>
                        <div class="detail-group">
                            <i class="far fa-calendar-alt"></i>
                            <div class="detail-content">
                                <label>Booking Date & Time</label>
                                <div class="detail-value" id="modalDate"></div>
                            </div>
                        </div>
                        <div class="detail-group">
                            <i class="fas fa-users"></i>
                            <div class="detail-content">
                                <label>Number of Players</label>
                                <div class="detail-value" id="modalPlayers"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <div class="delete-overlay" id="deleteOverlay"></div>
    <div class="delete-notification" id="deleteNotification">
        <div class="delete-header">
            <div class="delete-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3>Delete Booking</h3>
        </div>
        <div class="delete-content">
            <h3>Are you sure?</h3>
            <p>You are about to delete <strong id="deleteBookingCode"></strong>.<br>
            This action cannot be undone and all related data will be permanently removed.</p>
        </div>
        <div class="delete-actions">
            <button class="delete-btn cancel" onclick="cancelDelete()">Cancel</button>
            <button class="delete-btn confirm" onclick="confirmDelete()">Delete</button>
        </div>
    </div>

    <script>
        let ws;
        
        function initializeWebSocket() {
            const host = window.location.hostname;
            const port = window.location.port;
            ws = new WebSocket(`ws://${host}:${port}`);

            ws.onopen = function() {
                console.log('Connected to WebSocket server');
            };

            ws.onmessage = function(event) {
                const data = JSON.parse(event.data);
                console.log('Received:', data);
                
                if (data.type === 'new_booking') {
                    handleNewBooking(data.data);
                }
            };

            ws.onclose = function() {
                console.log('Connection closed, attempting to reconnect...');
                setTimeout(initializeWebSocket, 3000);
            };
        }

        function handleNewBooking(booking) {
            // Tampilkan notifikasi
            showNotification(booking);
            
            // Update tampilan tabel
            const tbody = document.querySelector('.booking-table tbody');
            const row = createBookingRow(booking);
            tbody.insertBefore(row, tbody.firstChild);
            
            // Update tampilan card
            const cardContainer = document.querySelector('.booking-cards');
            const card = createBookingCard(booking);
            cardContainer.insertBefore(card, cardContainer.firstChild);
        }

        function createBookingRow(booking) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${booking.booking_code}</td>
                <td>
                    ${booking.name}<br>
                    <small>${booking.email}</small><br>
                    <small>${booking.phone}</small>
                </td>
                <td>
                    ${formatDate(booking.booking_date)}<br>
                    <small>${formatTime(booking.booking_time)}</small>
                </td>
                <td>${booking.players}</td>
                <td>
                    <form method="POST" class="status-form">
                        <input type="hidden" name="booking_id" value="${booking.id}">
                        <input type="hidden" name="update_status" value="1">
                        <select name="new_status" onchange="this.form.submit()" class="status-pending">
                            <option value="pending" selected>Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="completed">Completed</option>
                        </select>
                    </form>
                </td>
                <td>
                    <button class="action-btn btn-edit" onclick="viewDetails('${booking.booking_code}')">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="action-btn btn-delete" onclick="deleteBooking(${booking.id}, '${booking.booking_code}')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            `;
            tr.classList.add('new-booking');
            return tr;
        }

        function createBookingCard(booking) {
            const div = document.createElement('div');
            div.className = 'booking-card new-booking';
            div.onclick = () => viewDetails(booking.booking_code);
            div.innerHTML = `
                <div class="card-logo">
                    <img src="./../assets/photos/Logo Master KGBR.avif" alt="KGBR Logo">
                </div>
                <div class="card-content">
                    <span class="status-badge status-pending">
                        Pending
                    </span>
                    <h3 class="booking-code">${booking.booking_code}</h3>
                    <div class="customer-info">
                        <h4 class="customer-name">${booking.name}</h4>
                        <p><i class="far fa-calendar"></i> ${formatDate(booking.booking_date)}</p>
                        <p><i class="far fa-clock"></i> ${formatTime(booking.booking_time)}</p>
                        <p><i class="fas fa-users"></i> ${booking.players} Players</p>
                    </div>
                </div>
            `;
            return div;
        }

        function showNotification(booking) {
            const notification = document.getElementById('bookingNotification');
            const message = document.getElementById('notificationMessage');
            const progressBar = notification.querySelector('.progress-bar');
            
            const bookingDate = new Date(booking.booking_date);
            const formattedDate = bookingDate.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            
            message.innerHTML = `
                <p><strong>${booking.name}</strong> just booked for:</p>
                <p><i class="far fa-calendar"></i> ${formattedDate}</p>
                <p><i class="far fa-clock"></i> ${booking.booking_time}</p>
                <p><i class="fas fa-users"></i> ${booking.players} Players</p>
            `;
            
            notification.classList.add('show');
            progressBar.style.width = '100%';
            
            // Play notification sound
            const audio = new Audio('../assets/ding.mp3');
            audio.play().catch(e => console.log('Sound error:', e));

            // Reset progress bar animation
            setTimeout(() => {
                progressBar.style.width = '0%';
            }, 100);

            // Auto dismiss after 5 seconds
            setTimeout(dismissNotification, 5000);
        }

        function dismissNotification() {
            const notification = document.getElementById('bookingNotification');
            notification.classList.remove('show');
        }

        function viewLatestBooking() {
            if (latestBookingData) {
                // Format date and time
                const date = new Date(latestBookingData.booking_date);
                const time = latestBookingData.booking_time;
                const formattedDate = date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
                
                // Isi data ke dalam modal
                document.getElementById('modalBookingCode').textContent = latestBookingData.booking_code;
                
                const statusElement = document.getElementById('modalStatus');
                statusElement.textContent = latestBookingData.status.toUpperCase();
                statusElement.className = 'status-indicator status-' + latestBookingData.status.toLowerCase();
                
                document.getElementById('modalCustomerName').textContent = latestBookingData.name;
                document.getElementById('modalContact').innerHTML = `${latestBookingData.email}<br>${latestBookingData.phone}`;
                document.getElementById('modalDate').textContent = `${formattedDate} at ${time}`;
                document.getElementById('modalPlayers').textContent = `${latestBookingData.players} Players`;

                // Tampilkan modal
                const modal = document.getElementById('bookingModal');
                modal.classList.add('active');
                
                // Tutup notifikasi
                dismissNotification();
            }
        }

        function showNotification(booking) {
            const notification = document.getElementById('bookingNotification');
            const message = document.getElementById('notificationMessage');
            const progressBar = notification.querySelector('.progress-bar');
            
            const bookingDate = new Date(booking.booking_date);
            const formattedDate = bookingDate.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            
            message.innerHTML = `
                <p><strong>${booking.name}</strong> just booked for:</p>
                <p><i class="far fa-calendar"></i> ${formattedDate}</p>
                <p><i class="far fa-clock"></i> ${booking.booking_time}</p>
                <p><i class="fas fa-users"></i> ${booking.players} Players</p>
            `;
            
            notification.classList.add('show');
            progressBar.style.width = '100%';
            
            // Play notification sound
            const audio = new Audio('../assets/ding.mp3');
            audio.play().catch(e => console.log('Sound error:', e));

            // Reset progress bar animation
            setTimeout(() => {
                progressBar.style.width = '0%';
            }, 100);

            // Simpan data booking terbaru dalam variable global
            latestBookingData = booking;

            // Auto dismiss after 5 seconds
            setTimeout(dismissNotification, 5000);
        }

        function dismissNotification() {
            const notification = document.getElementById('bookingNotification');
            notification.classList.remove('show');
        }

        function viewLatestBooking() {
            if (latestBookingData) {
                // Format date and time
                const date = new Date(latestBookingData.booking_date);
                const time = latestBookingData.booking_time;
                const formattedDate = date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
                
                // Isi data ke dalam modal
                document.getElementById('modalBookingCode').textContent = latestBookingData.booking_code;
                
                const statusElement = document.getElementById('modalStatus');
                statusElement.textContent = latestBookingData.status.toUpperCase();
                statusElement.className = 'status-indicator status-' + latestBookingData.status.toLowerCase();
                
                document.getElementById('modalCustomerName').textContent = latestBookingData.name;
                document.getElementById('modalContact').innerHTML = `${latestBookingData.email}<br>${latestBookingData.phone}`;
                document.getElementById('modalDate').textContent = `${formattedDate} at ${time}`;
                document.getElementById('modalPlayers').textContent = `${latestBookingData.players} Players`;

                // Tampilkan modal
                const modal = document.getElementById('bookingModal');
                modal.classList.add('active');
                
                // Tutup notifikasi
                dismissNotification();
            }
        }

        let lastCheck = new Date().toISOString();
        let latestBooking = null;

        function checkNewBookings() {
            fetch(`../api/check-bookings.php?lastCheck=${lastCheck}`)
                .then(response => response.json())
                .then(data => {
                    if (data.hasNew && data.data && !document.querySelector('.notification.show')) {
                        lastCheck = data.timestamp;
                        latestBooking = data.data;
                        showNotification(data.data);
                    }
                });
        }

        // Check every 10 seconds instead of 5
        setInterval(checkNewBookings, 10000);
        
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        function formatTime(timeStr) {
            return timeStr.substring(0, 5);
        }

        // Initialize WebSocket when page loads
        document.addEventListener('DOMContentLoaded', initializeWebSocket);
        
        function viewDetails(bookingCode) {
            // Cari data booking berdasarkan booking code
            <?php
            echo "const bookings = " . json_encode($bookings) . ";";
            ?>
            const booking = bookings.find(b => b.booking_code === bookingCode);
            
            if (booking) {
                // Format date and time
                const date = new Date(booking.booking_date);
                const time = booking.booking_time;
                const formattedDate = date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
                
                // Isi data ke dalam modal
                document.getElementById('modalBookingCode').textContent = booking.booking_code;
                
                const statusElement = document.getElementById('modalStatus');
                statusElement.textContent = booking.status.toUpperCase();
                statusElement.className = 'status-indicator status-' + booking.status.toLowerCase();
                
                document.getElementById('modalCustomerName').textContent = booking.name;
                document.getElementById('modalContact').innerHTML = `${booking.email}<br>${booking.phone}`;
                document.getElementById('modalDate').textContent = `${formattedDate} at ${time}`;
                document.getElementById('modalPlayers').textContent = `${booking.players} Players`;

                // Tampilkan modal dengan animasi
                const modal = document.getElementById('bookingModal');
                modal.classList.add('active');
            }
        }

        function closeModal() {
            const modal = document.getElementById('bookingModal');
            modal.classList.remove('active');
        }

        // Tutup modal jika user klik di luar modal
        document.getElementById('bookingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        function filterTable(searchText) {
            // Filter table rows
            const rows = document.querySelectorAll('.booking-table tbody tr');
            const cards = document.querySelectorAll('.booking-card');
            searchText = searchText.toLowerCase();
            
            // Filter table view
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });

            // Filter card view
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchText) ? '' : 'none';
            });
        }

        // Remove existing event listener and add new one
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function(e) {
            filterTable(e.target.value);
        });

        // Modify form submission to handle both views
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams(formData).toString();
            window.location.href = `?${params}`;
        });

        // Add filter reset function
        function resetFilters() {
            document.getElementById('filterForm').reset();
            filterTable('');
        }

        // Initialize filters on page load
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchValue = urlParams.get('search') || '';
            const sortValue = urlParams.get('sort') || '';
            document.getElementById('sortSelect').value = sortValue;
            if (searchValue) {
                filterTable(searchValue);
            }
        });
        
        // Add this function for view toggling
        function toggleView(view) {
            const tableView = document.getElementById('tableView');
            const cardView = document.getElementById('cardView');
            const tableBtn = document.getElementById('tableViewBtn');
            const cardBtn = document.getElementById('cardViewBtn');

            if (view === 'table') {
                tableView.style.display = 'block';
                cardView.style.display = 'none';
                tableBtn.classList.add('active');
                cardBtn.classList.remove('active');
            } else {
                tableView.style.display = 'none';
                cardView.style.display = 'block';
                tableBtn.classList.remove('active');
                cardBtn.classList.add('active');
            }
        }

        let checkingNewBookings = false;

        async function checkNewBookings() {
            if (checkingNewBookings) return;
            
            try {
                checkingNewBookings = true;
                const response = await fetch('../api/check-bookings.php');
                const data = await response.json();
                
                if (data.hasNew) {
                    showNotification(data.data);
                    fetchBookings(document.getElementById('sortSelect').value);
                }
            } catch (error) {
                console.error('Error checking new bookings:', error);
            } finally {
                checkingNewBookings = false;
            }
        }

        // Check setiap 10 detik
        setInterval(checkNewBookings, 10000);

        function deleteBooking(bookingId, bookingCode) {
            document.getElementById('deleteBookingCode').textContent = bookingCode;
            document.getElementById('deleteOverlay').classList.add('show');
            document.getElementById('deleteNotification').classList.add('show');
            
            // Store booking info for confirmation
            window.deleteBookingData = {
                id: bookingId,
                code: bookingCode
            };
        }

        function cancelDelete() {
            document.getElementById('deleteOverlay').classList.remove('show');
            document.getElementById('deleteNotification').classList.remove('show');
            window.deleteBookingData = null;
        }

        function confirmDelete() {
            if (window.deleteBookingData) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'booking.php';
                
                if (window.deleteBookingData.type === 'multiple') {
                    window.deleteBookingData.ids.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'booking_ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_multiple';
                    deleteInput.value = '1';
                    form.appendChild(deleteInput);
                } else if (window.deleteBookingData.type === 'all') {
                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_all';
                    deleteInput.value = '1';
                    form.appendChild(deleteInput);
                } else {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'booking_id';
                    input.value = window.deleteBookingData.id;
                    
                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_booking';
                    deleteInput.value = '1';
                    
                    form.appendChild(input);
                    form.appendChild(deleteInput);
                }
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        let selectedBookings = [];

        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.booking-checkbox');
            checkboxes.forEach(box => {
                box.checked = checkbox.checked;
            });
            updateDeleteButton();
        }

        function updateDeleteButton() {
            const checkboxes = document.querySelectorAll('.booking-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');
            selectedBookings = Array.from(checkboxes).map(cb => cb.value);
            deleteBtn.disabled = selectedBookings.length === 0;
        }

        function deleteSelected() {
            if (selectedBookings.length === 0) return;
            
            document.getElementById('deleteBookingCode').textContent = `${selectedBookings.length} bookings`;
            document.getElementById('deleteOverlay').classList.add('show');
            document.getElementById('deleteNotification').classList.add('show');
            
            window.deleteBookingData = {
                type: 'multiple',
                ids: selectedBookings
            };
        }

        function confirmDeleteAll() {
            document.getElementById('deleteBookingCode').textContent = 'ALL BOOKINGS';
            document.getElementById('deleteOverlay').classList.add('show');
            document.getElementById('deleteNotification').classList.add('show');
            
            window.deleteBookingData = {
                type: 'all'
            };
        }

        // Add this to close modal when clicking overlay
        document.getElementById('deleteOverlay').addEventListener('click', cancelDelete);

        async function fetchBookings(sort = '') {
            try {
                const response = await fetch(`../api/get-bookings.php${sort ? `?sort=${sort}` : ''}`);
                const result = await response.json();
                if (result.success) {
                    updateBookingList(result.data);
                }
            } catch (error) {
                console.error('Error fetching bookings:', error);
            }
        }

        function updateBookingList(bookings) {
            const tbody = document.querySelector('.booking-table tbody');
            const cardContainer = document.querySelector('.booking-cards');
            
            // Update table view
            tbody.innerHTML = bookings.map(booking => `
                <tr>
                    <td>
                        <input type="checkbox" class="booking-checkbox" value="${booking.id}" onchange="updateDeleteButton()">
                    </td>
                    <td>${booking.booking_code}</td>
                    <td>
                        ${booking.name}<br>
                        <small>${booking.email}</small><br>
                        <small>${booking.phone}</small>
                    </td>
                    <td>
                        ${formatDate(booking.booking_date)}<br>
                        <small>${formatTime(booking.booking_time)}</small>
                    </td>
                    <td>${booking.players}</td>
                    <td>
                        <form method="POST" class="status-form">
                            <input type="hidden" name="booking_id" value="${booking.id}">
                            <input type="hidden" name="update_status" value="1">
                            <select name="new_status" onchange="this.form.submit()" class="status-${booking.status.toLowerCase()}">
                                <option value="pending" ${booking.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="confirmed" ${booking.status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                                <option value="cancelled" ${booking.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                                <option value="completed" ${booking.status === 'completed' ? 'selected' : ''}>Completed</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <button class="action-btn btn-edit" onclick="viewDetails('${booking.booking_code}')">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="action-btn btn-delete" onclick="deleteBooking(${booking.id}, '${booking.booking_code}')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `).join('');
            
            // Update card view with proper status
            cardContainer.innerHTML = bookings.map(booking => `
                <div class="booking-card" onclick="viewDetails('${booking.booking_code}')">
                    <div class="card-logo">
                        <img src="./../assets/photos/Logo Master KGBR.avif" alt="KGBR Logo">
                    </div>
                    <div class="card-content">
                        <span class="status-badge status-${booking.status.toLowerCase()}">
                            ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                        </span>
                        <h3 class="booking-code">${booking.booking_code}</h3>
                        <div class="customer-info">
                            <h4 class="customer-name">${booking.name}</h4>
                            <p><i class="far fa-calendar"></i> ${formatDate(booking.booking_date)}</p>
                            <p><i class="far fa-clock"></i> ${formatTime(booking.booking_time)}</p>
                            <p><i class="fas fa-users"></i> ${booking.players} Players</p>
                        </div>
                        <div class="card-actions">
                            <button class="action-btn btn-delete" onclick="event.stopPropagation(); deleteBooking(${booking.id}, '${booking.booking_code}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function applySorting(sortValue) {
            fetchBookings(sortValue);
        }

        // Modify existing checkNewBookings function
        async function checkNewBookings() {
            if (checkingNewBookings) return;
            
            try {
                checkingNewBookings = true;
                const response = await fetch('../api/check-bookings.php');
                const data = await response.json();
                
                if (data.hasNew) {
                    showNotification(data.data);
                    fetchBookings(document.getElementById('sortSelect').value);
                }
            } catch (error) {
                console.error('Error checking new bookings:', error);
            } finally {
                checkingNewBookings = false;
            }
        }

        // Add to DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            // ...existing code...
            
            // Refresh data every 30 seconds
            setInterval(() => fetchBookings(document.getElementById('sortSelect').value), 30000);
        });
    </script>
</body>
</html>