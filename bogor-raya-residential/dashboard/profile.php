<?php
session_start();
require_once '../includes/auth_helper.php';
checkUserStatus();
require '../vendor/autoload.php'; // Add this line to include autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['nama'])) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "db_bogor");

if (isset($_GET['password_change']) && $_GET['password_change'] == 'success') {
    $success_message = "Password has been successfully updated!";
}

if (isset($_GET['profile_update']) && $_GET['profile_update'] == 'success') {
    $success_message = "Profile has been successfully updated!";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['send_otp'])) {
        $email = trim($_POST['email']);

        $otp = rand(100000, 999999);
        $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $stmt = mysqli_prepare($conn, "UPDATE tbl_users SET otp=?, otp_expiry=? WHERE email=?");
        mysqli_stmt_bind_param($stmt, "sss", $otp, $expiry, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Send OTP via Email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'refafatur97@gmail.com'; // Replace with your Gmail address
            $mail->Password = 'prix eidm rjip sinh'; // Replace with your Gmail password or app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // atau ENCRYPTION_SMTPS
            $mail->Port = 587; // atau 465 jika pakai SSL

            //Recipients
            $mail->setFrom('refafatur97@gmail.com', 'Muhammad Refa'); // Replace with your Gmail address and name
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Kode OTP Anda';
            $mail->Body = '<div style="font-family: Arial, sans-serif; text-align: center; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); max-width: 400px; margin: auto; border: 1px solid #ddd;">
    <h2 style="color: #2c3e50; margin-bottom: 10px;">🔐 Kode OTP Anda</h2>
    <p style="font-size: 16px; color: #555; margin-bottom: 20px;">Gunakan kode OTP berikut untuk mereset kata sandi Anda:</p>
    <div style="font-size: 26px; font-weight: bold; letter-spacing: 3px; color: #ffffff; background-color: #3498db; padding: 15px; border-radius: 5px; display: inline-block;">
        <strong>' . $otp . '</strong>
    </div>
    <p style="font-size: 14px; color: #777; margin-top: 20px;">Kode ini akan kedaluwarsa dalam <strong>10 menit</strong>.</p>
    <p style="font-size: 14px; color: #777;">Jika Anda tidak meminta kode ini, abaikan email ini.</p>
    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #ddd;">
    <p style="font-size: 12px; color: #aaa;">© 2025 Nama Perusahaan Anda. Semua Hak Dilindungi.</p>
</div>';

            $mail->send();
            $_SESSION['reset_email'] = $email;
            $_SESSION['otp_sent'] = true;
        } catch (Exception $e) {
            $error_message = "Gagal mengirim OTP. Silakan coba lagi.";
        }
    } elseif (isset($_POST['verify_otp'])) {
        $otp = trim($_POST['otp']);
        $email = $_SESSION['reset_email'];

        $stmt = mysqli_prepare($conn, "SELECT otp, otp_expiry FROM tbl_users WHERE email=?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $stored_otp, $otp_expiry);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($otp == $stored_otp && strtotime($otp_expiry) > time()) {
            $_SESSION['otp_verified'] = true;
        } else {
            $error_message = "OTP tidak valid atau telah kedaluwarsa.";
        }
    } elseif (isset($_POST['reset_password'])) {
        if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
            header("Location: profile.php");
            exit();
        }

        $new_password = password_hash(trim($_POST['new_password']), PASSWORD_BCRYPT);
        $email = $_SESSION['reset_email'];

        $stmt = mysqli_prepare($conn, "UPDATE tbl_users SET password=?, otp=NULL, otp_expiry=NULL WHERE email=?");
        mysqli_stmt_bind_param($stmt, "ss", $new_password, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        unset($_SESSION['otp_verified']);
        unset($_SESSION['reset_email']);
        unset($_SESSION['otp_sent']);

        header("Location: ../login.php");
        exit();
    }

    if (isset($_POST['edit_profile'])) {
        $id = intval($_SESSION['id']);
        $username = trim($_POST['username']);
        $nama = trim($_POST['nama']);
        $email = trim($_POST['email']);
        $no_hp = trim($_POST['no_hp']);
        $jenis_kelamin = trim($_POST['jenis_kelamin']);
        $expired = trim($_POST['expired']);

        $foto = null;
        if (!empty($_FILES['foto']['name'])) {
            $folder = match ($_SESSION['level']) {
                '0' => 'admin/',
                '1' => 'user/',
                '2' => 'sales/',
                default => ''
            };

            $upload_dir = '../uploads/' . $folder;
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = uniqid() . '_' . $_FILES['foto']['name'];
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $file_path)) {
                $foto = 'uploads/' . $folder . $file_name;
            }
        }

        $query = "UPDATE tbl_users SET username=?, nama=?, email=?, jenis_kelamin=?, no_hp=?, expired=?";
        $params = [$username, $nama, $email, $jenis_kelamin, $no_hp, $expired];
        $types = "ssssss";

        if ($foto) {
            $query .= ", foto=?";
            $params[] = $foto;
            $types .= "s";
        }
        $query .= " WHERE id=?";
        $params[] = $id;
        $types .= "i";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['username'] = $username;
        $_SESSION['nama'] = $nama;
        $_SESSION['email'] = $email;
        $_SESSION['no_hp'] = $no_hp;
        $_SESSION['jenis_kelamin'] = $jenis_kelamin;
        $_SESSION['expired'] = $expired;
        if ($foto) {
            $_SESSION['foto'] = $foto;
        }

        header("Location: profile.php?profile_update=success");
        exit();
    }

    if (isset($_POST['edit_password'])) {
        $id = intval($_SESSION['id']);
        $current_password = trim($_POST['current_password']);
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);

        // Get the stored password using ID
        $stmt = mysqli_prepare($conn, "SELECT password FROM tbl_users WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $stored_password = $row['password'];
            
            // Verify current password
            if (password_verify($current_password, $stored_password)) {
                // Verify new passwords match
                if ($new_password === $confirm_password) {
                    // Hash new password
                    $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    
                    // Update password
                    $update_stmt = mysqli_prepare($conn, "UPDATE tbl_users SET password=? WHERE id=?");
                    mysqli_stmt_bind_param($update_stmt, "si", $new_hashed_password, $id);
                    
                    if (mysqli_stmt_execute($update_stmt)) {
                        mysqli_stmt_close($update_stmt);
                        mysqli_stmt_close($stmt);
                        
                        // Set success message
                        header("Location: profile.php?password_change=success");
                        exit();
                    } else {
                        $error_message = "Failed to update password. Please try again.";
                    }
                } else {
                    $error_message = "New passwords do not match";
                }
            } else {
                $error_message = "Current password is incorrect";
            }
        } else {
            $error_message = "User not found";
        }
        mysqli_stmt_close($stmt);

        if (isset($error_message)) {
            // Stay on same page and show error
            header("Location: profile.php?error=" . urlencode($error_message));
            exit();
        }
    }
}

// Fetch current user's data from database
$id = intval($_SESSION['id']);
$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_users WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($user = mysqli_fetch_assoc($result)) {
    // Update session with latest user data
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['no_hp'] = $user['no_hp'];
    $_SESSION['jenis_kelamin'] = $user['jenis_kelamin'];
    $_SESSION['expired'] = $user['expired'];
    $_SESSION['foto'] = $user['foto'];
}
mysqli_stmt_close($stmt);

// Fetch all users if needed
$query = "SELECT * FROM tbl_users";
$result = mysqli_query($conn, $query);

// Store users in array
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }
        
        .profile-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            background: #fff;
            overflow: hidden;
        }
        
        .profile-sidebar {
            background: linear-gradient(150deg, #2e4fd7 0%, #6d42d8 100%);
            padding: 3rem 2rem;
            position: relative;
        }
        
        .profile-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,...') center/cover;
            opacity: 0.1;
        }
        
        .profile-image {
            width: 180px;
            height: 180px;
            border: 5px solid rgba(255,255,255,0.2);
            padding: 5px;
            transition: all 0.4s ease;
            position: relative;
            object-fit: cover;
        }
        
        .profile-image:hover {
            transform: scale(1.05);
            border-color: rgba(255,255,255,0.4);
        }
        
        .btn-profile {
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(5px);
        }
        
        .btn-profile:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            background: rgba(255,255,255,0.95);
            color: #2e4fd7;
        }
        
        .profile-content {
            padding: 2.5rem;
        }
        
        .info-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .info-label {
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 1.125rem;
            line-height: 1.4;
        }
        
        .form-control {
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            padding: 0.875rem 1.25rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #2e4fd7;
            box-shadow: 0 0 0 4px rgba(46,79,215,0.1);
        }
        
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
        }
        
        .nav-tabs {
            border: none;
            margin-bottom: 1.5rem;
        }
        
        .nav-tabs .nav-link {
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link.active {
            background: #2e4fd7;
            color: #fff;
        }
        
        .form-floating label {
            padding: 1rem;
        }
        
        .form-floating>.form-control {
            padding: 1.25rem 1rem;
        }
        
        .input-group-text {
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            background: #f9fafb;
        }

        .profile-forms {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            margin-top: 2rem;
        }

        .profile-form-section {
            padding: 2rem;
        }

        .profile-form-section:first-child {
            border-right: 1px solid #e5e7eb;
        }

        .form-title {
            color: #2e4fd7;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        
        .notification {
            background: #fff;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 10px;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transform: translateX(120%);
            transition: all 0.3s ease;
            border-left: 4px solid #2e4fd7;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.success {
            border-left-color: #10B981;
        }
        
        .notification.error {
            border-left-color: #EF4444;
        }
        
        .notification-content {
            flex-grow: 1;
        }
        
        .notification-title {
            font-weight: 600;
            margin-bottom: 4px;
            color: #1f2937;
        }
        
        .notification-message {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .notification-close {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
        }
        
        .notification-close:hover {
            color: #4b5563;
        }

        /* Modern Form Styles */
        .modern-form {
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
        }

        .modern-form .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .modern-form .form-control {
            height: 52px;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            background-color: #f9fafb;
            transition: all 0.3s ease;
        }

        .modern-form .form-control:focus {
            border-color: #2e4fd7;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(46,79,215,0.1);
        }

        .modern-form .form-label {
            position: absolute;
            left: 1rem;
            top: 1rem;
            padding: 0 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            pointer-events: none;
            background: transparent;
        }

        .modern-form .form-control:focus ~ .form-label,
        .modern-form .form-control:not(:placeholder-shown) ~ .form-label {
            top: -0.5rem;
            left: 0.8rem;
            font-size: 0.75rem;
            color: #2e4fd7;
            background: #fff;
        }

        .modern-form .btn-submit {
            height: 52px;
            font-weight: 600;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #2e4fd7 0%, #6d42d8 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(46,79,215,0.2);
            transition: all 0.3s ease;
        }

        .modern-form .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(46,79,215,0.3);
        }

        .form-divider {
            position: relative;
            height: 1px;
            background: #e5e7eb;
            margin: 2rem 0;
        }

        .form-divider::before {
            content: 'or';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 0 1rem;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            cursor: pointer;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #2e4fd7;
        }
    </style>
</head>
<body>
    <div class="notification-container"></div>
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
        <div class="container py-5">
            <!-- Profile Card -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="profile-card">
                        <div class="row g-0">
                            <!-- Profile Sidebar -->
                            <div class="col-lg-4 profile-sidebar text-center text-white">
                                <div class="position-relative mb-4">
                                    <img src="../<?php echo htmlspecialchars($_SESSION['foto']); ?>" 
                                         alt="Profile Picture" 
                                         class="profile-image rounded-circle">
                                    <div class="mt-3">
                                        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($_SESSION['nama']); ?></h4>
                                        <p class="mb-3 opacity-75">
                                            <i class="fas fa-id-badge me-2"></i>
                                            <?php echo ($_SESSION['level'] == '0') ? 'Administrator' : 
                                                  (($_SESSION['level'] == '1') ? 'Regular User' : 'Sales Representative'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Profile Content -->
                            <div class="col-lg-8 profile-content">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#personal">
                                            <i class="fas fa-user me-2"></i>Personal Info
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#account">
                                            <i class="fas fa-shield-alt me-2"></i>Account
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="personal">
                                        <!-- Personal Information -->
                                        <div class="info-card">
                                            <div class="row g-4">
                                                <div class="col-sm-6">
                                                    <div class="info-label">Full Name</div>
                                                    <div class="info-value"><?php echo htmlspecialchars($_SESSION['nama']); ?></div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="info-label">Email Address</div>
                                                    <div class="info-value"><?php echo htmlspecialchars($_SESSION['email'] ?? 'Not set'); ?></div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="info-label">Phone Number</div>
                                                    <div class="info-value"><?php echo htmlspecialchars($_SESSION['no_hp'] ?? 'Not set'); ?></div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="info-label">Gender</div>
                                                    <div class="info-value"><?php echo htmlspecialchars($_SESSION['jenis_kelamin'] ?? 'Not set'); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="account">
                                        <!-- Account Status -->
                                        <div class="info-card">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="info-label">Account Expiry</div>
                                                    <div class="info-value">
                                                        <?php 
                                                        $expiry = $_SESSION['expired'] ?? null;
                                                        if ($expiry) {
                                                            $today = new DateTime();
                                                            $expiry_date = new DateTime($expiry);
                                                            $interval = $today->diff($expiry_date);
                                                            if ($expiry_date < $today) {
                                                                echo '<span class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>Account expired</span>';
                                                            } else {
                                                                echo '<span class="text-success"><i class="fas fa-check-circle me-2"></i>' . 
                                                                     htmlspecialchars($expiry) . ' (' . $interval->days . ' days remaining)</span>';
                                                            }
                                                        } else {
                                                            echo '<span class="text-warning"><i class="fas fa-question-circle me-2"></i>Not set</span>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Profile Forms -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="profile-forms">
                        <div class="row g-0">
                            <!-- Edit Profile Form -->
                            <div class="col-md-6 profile-form-section">
                                <div class="form-title">
                                    <i class="fas fa-user-edit"></i>
                                    Edit Profile
                                </div>
                                <form action="profile.php" method="post" enctype="multipart/form-data" class="modern-form needs-validation" novalidate>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>" required placeholder=" ">
                                        <label for="username" class="form-label">Username</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="nama" name="nama" 
                                               value="<?php echo htmlspecialchars($_SESSION['nama'] ?? ''); ?>" required placeholder=" ">
                                        <label for="nama" class="form-label">Full Name</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required placeholder=" ">
                                        <label for="email" class="form-label">Email</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="no_hp" name="no_hp" 
                                               value="<?php echo htmlspecialchars($_SESSION['no_hp'] ?? ''); ?>" required placeholder=" ">
                                        <label for="no_hp" class="form-label">Phone</label>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="">Select</option>
                                            <option value="Laki-Laki" <?php echo (isset($_SESSION['jenis_kelamin']) && $_SESSION['jenis_kelamin'] == 'Laki-Laki') ? 'selected' : ''; ?>>Male</option>
                                            <option value="Perempuan" <?php echo (isset($_SESSION['jenis_kelamin']) && $_SESSION['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Female</option>
                                        </select>
                                        <label for="jenis_kelamin" class="form-label">Gender</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="file" class="form-control" id="foto" name="foto">
                                        <label for="foto" class="form-label">Profile Photo</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="date" class="form-control" id="expired" name="expired" 
                                               value="<?php echo htmlspecialchars($_SESSION['expired'] ?? ''); ?>" required placeholder=" ">
                                        <label for="expired" class="form-label">Account Expiry</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-submit w-100" name="edit_profile">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                    </button>
                                </form>
                            </div>
                            <!-- Change Password Form -->
                            <div class="col-md-6 profile-form-section">
                                <div class="form-title">
                                    <i class="fas fa-key"></i>
                                    Change Password
                                </div>
                                <form action="profile.php" method="post" class="modern-form needs-validation" novalidate>
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="current_password" name="current_password" required placeholder=" ">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <span class="password-toggle" onclick="togglePassword('current_password')">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    <div class="form-divider"></div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="new_password" name="new_password" required placeholder=" ">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <span class="password-toggle" onclick="togglePassword('new_password')">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder=" ">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <span class="password-toggle" onclick="togglePassword('confirm_password')">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-submit w-100" name="edit_password">
                                        <i class="fas fa-key me-2"></i>Update Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/dashboard.js"></script>
    <script>
    // Form validation
    (function () {
        'use strict'
        
        // Fetch all forms we want to apply validation styles to
        var forms = document.querySelectorAll('.needs-validation')
        
        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
    })()

    // Password match validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        if (this.value !== document.getElementById('new_password').value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });

    // Auto-hide alerts after 5 seconds
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 5000);

    // Add this function for notifications
    function showNotification(title, message, type) {
        const container = document.querySelector('.notification-container');
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        container.appendChild(notification);
        
        // Force reflow
        notification.offsetHeight;
        
        // Add show class
        notification.classList.add('show');
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Replace alert code with notification calls
    <?php if (isset($success_message)): ?>
        showNotification('Success', '<?php echo addslashes($success_message); ?>', 'success');
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        showNotification('Error', '<?php echo addslashes($error_message); ?>', 'error');
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        showNotification('Error', '<?php echo addslashes($_GET['error']); ?>', 'error');
    <?php endif; ?>

    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = event.currentTarget.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>