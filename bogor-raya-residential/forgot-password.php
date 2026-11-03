<?php
session_start();

require 'vendor/autoload.php'; // Add this line to include autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = mysqli_connect("localhost", "root", "", "db_bogor");

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

        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Bogor Raya Residential</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-5">
                        <h5 class="text-primary mb-4 text-center"><i class="fas fa-key me-2"></i>Forgot Password</h5>
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <form action="forgot-password.php" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary me-2" name="send_otp">Send OTP</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verify OTP Form -->
        <div class="row justify-content-center mt-4" id="verifyOtpForm" style="display: <?php echo isset($_SESSION['otp_sent']) && !isset($_SESSION['otp_verified']) ? 'block' : 'none'; ?>;">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-5">
                        <h5 class="text-primary mb-4 text-center"><i class="fas fa-key me-2"></i>Verify OTP</h5>
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <form action="forgot-password.php" method="post">
                            <div class="mb-3">
                                <label for="otp" class="form-label">OTP</label>
                                <input type="text" class="form-control" id="otp" name="otp" required>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary me-2" name="verify_otp">Verify OTP</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reset Password Form -->
        <div class="row justify-content-center mt-4" id="resetPasswordForm" style="display: <?php echo isset($_SESSION['otp_verified']) ? 'block' : 'none'; ?>;">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-5">
                        <h5 class="text-primary mb-4 text-center"><i class="fas fa-key me-2"></i>Reset Password</h5>
                        <form action="forgot-password.php" method="post">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary me-2" name="reset_password">Reset Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>