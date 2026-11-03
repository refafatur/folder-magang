<?php
session_start();
require_once 'db/config.php';

require_once 'includes/email_config.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Tambahkan koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db = "golf";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

require 'vendor/autoload.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['action']) && $_POST['action'] == 'send_otp') {
        $email = $_POST['email'];
        
        $query = "SELECT * FROM tbl_users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate OTP dengan waktu kadaluarsa 30 menit
            $otp = sprintf("%06d", mt_rand(1, 999999));
            $expiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            
            // Update user dengan OTP baru
            $updateQuery = "UPDATE tbl_users SET otp = ?, otp_expiry = ? WHERE email = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("sss", $otp, $expiry, $email);
            
            if($stmt->execute()) {
                // Kirim email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'refafatur97@gmail.com';
                    $mail->Password = 'ohfh zdti rfiw pcyz';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->setFrom('klubgolf@golfbogor.com', 'Klub Golf Bogor Raya');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Kode OTP Reset Password - Golf Club Bogor Raya';
                    $mail->Body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                        <div style='text-align: center; margin-bottom: 20px;'>
                            <img src='https://cdn-icons-png.flaticon.com/512/2076/2076787.png' alt='Logo' style='width: 80px;'>
                            <h2 style='color: #333;'>Kode OTP Reset Password</h2>
                        </div>
                        <div style='background-color: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px;'>
                            <p style='margin-bottom: 10px;'>Yth. Member Golf Club Bogor Raya,</p>
                            <p>Berikut adalah kode OTP Anda untuk reset password:</p>
                            <div style='text-align: center; padding: 15px;'>
                                <span style='font-size: 24px; font-weight: bold; color: #DAA520; letter-spacing: 5px;'>$otp</span>
                            </div>
                            <p style='margin-top: 20px; color: #666; font-size: 12px;'>Kode OTP ini akan kadaluarsa dalam 30 menit.</p>
                        </div>
                        <div style='text-align: center; color: #666; font-size: 12px;'>
                            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
                            <p>&copy; " . date('Y') . " Golf Club Bogor Raya. All rights reserved.</p>
                        </div>
                    </div>";
                    $mail->isHTML(true);

                    $mail->send();
                    $_SESSION['reset_email'] = $email;
                    echo json_encode(['status' => 'success', 'message' => 'Kode OTP telah dikirim ke email Anda!']);
                    exit;
                } catch (Exception $e) {
                    echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim email: ' . $mail->ErrorInfo]);
                    exit;
                }
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Email tidak ditemukan!']);
            exit;
        }
    }

    if(isset($_POST['action']) && $_POST['action'] == 'verify_otp') {
        $otp = trim($_POST['otp']);
        $email = $_SESSION['reset_email'] ?? '';

        if(!$email) {
            echo json_encode(['status' => 'error', 'message' => 'Sesi telah berakhir, silakan kirim ulang OTP']);
            exit;
        }

        // Sederhanakan query verifikasi OTP
        $query = "SELECT * FROM tbl_users WHERE email = ? AND otp = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $email, $otp);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if($user) {
            // Hapus OTP setelah berhasil diverifikasi
            $updateQuery = "UPDATE tbl_users SET otp = NULL, otp_expiry = NULL WHERE email = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("s", $email);
            
            if($stmt->execute()) {
                $_SESSION['reset_email'] = $email;
                echo json_encode([
                    'status' => 'success',
                    'redirect' => "resetpassword.php" // Perbaiki nama file
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal memproses permintaan']);
            }
        } else {
            // Pesan error sederhana
            echo json_encode(['status' => 'error', 'message' => 'Kode OTP tidak valid. Silakan cek kembali']);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Golf Club - Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-color: #1a1a1a;
            --secondary-color: #ffffff;
            --accent-color: goldenrod;
        }
        
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/login.avif');
            background-size: cover;
            background-position: center;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
        .forgot-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
            margin: 15px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo img {
            width: 80px;
            margin-bottom: 15px;
        }
        .login-logo h2 {
            color: var(--primary-color);
            margin-bottom: 0;
        }
        .form-control {
            border-radius: 20px;
            padding: 12px 20px;
            margin-bottom: 15px;
            border: 1px solid rgba(26, 26, 26, 0.1);
        }
        .btn-submit {
            background: var(--accent-color);
            border: none;
            border-radius: 20px;
            padding: 12px;
            width: 100%;
            color: var(--secondary-color);
            font-weight: bold;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            background: #daa520;
            transform: translateY(-2px);
        }
        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }
        .back-to-login a {
            color: var(--accent-color);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="login-logo">
            <img src="https://cdn-icons-png.flaticon.com/512/2076/2076787.png" alt="Golf Club Logo">
            <h2>Reset Password</h2>
        </div>
        
        <form method="post" class="mb-3" id="otpForm">
            <div class="input-group mb-3">
                <input type="email" name="email" class="form-control" placeholder="Masukkan email anda" required>
                <button type="submit" name="send_otp" class="btn btn-submit" id="sendOtpBtn">Kirim OTP</button>
                <div class="spinner-border text-light" role="status" id="otpSpinner" style="display:none;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </form>

        <div id="otpVerificationSection" style="display:none;">
            <form method="post" id="verifyForm">
                <input type="text" name="otp" class="form-control" placeholder="Masukkan kode OTP" required>
                <button type="submit" name="verify_otp" class="btn btn-submit" id="verifyOtpBtn">Verifikasi OTP</button>
                <div class="spinner-border text-light" role="status" id="verifySpinner" style="display:none;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </form>
        </div>

        <div class="back-to-login">
            <a href="login.php">Kembali ke Login</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#otpForm').on('submit', function(e) {
            e.preventDefault();
            const button = $('#sendOtpBtn');
            const spinner = $('#otpSpinner');
            
            button.prop('disabled', true);
            spinner.show();
            
            $.ajax({
                url: 'lupasandi.php',
                type: 'POST',
                data: {
                    email: $('input[name="email"]').val(),
                    action: 'send_otp'
                },
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        $('#otpVerificationSection').show();
                        showAlert('success', response.message);
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function() {
                    showAlert('danger', 'Terjadi kesalahan. Silakan coba lagi.');
                },
                complete: function() {
                    button.prop('disabled', false);
                    spinner.hide();
                }
            });
        });

        $('#verifyForm').on('submit', function(e) {
            e.preventDefault();
            const button = $('#verifyOtpBtn');
            const spinner = $('#verifySpinner');
            
            button.prop('disabled', true);
            spinner.show();
            
            $.ajax({
                url: 'lupasandi.php',
                type: 'POST',
                data: {
                    otp: $('input[name="otp"]').val(),
                    action: 'verify_otp'
                },
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        window.location.href = response.redirect;
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function() {
                    showAlert('danger', 'Terjadi kesalahan. Silakan coba lagi.');
                },
                complete: function() {
                    button.prop('disabled', false);
                    spinner.hide();
                }
            });
        });

        function showAlert(type, message) {
            $('.alert').remove();
            const alert = `<div class="alert alert-${type} mb-3">${message}</div>`;
            $('.login-logo').after(alert);
        }
    });
    </script>
</body>
</html>



