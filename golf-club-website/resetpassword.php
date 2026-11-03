<?php
session_start();

// Tambahkan koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db = "golf";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if(!isset($_SESSION['reset_email'])) {
    header("Location: lupasandi.php");
    exit();
}

if(isset($_POST['reset_password'])) {
    $email = $_SESSION['reset_email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if(strlen($new_password) < 8) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Password harus minimal 8 karakter!'
        ];
    } else if($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE tbl_users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $email);
        
        if($stmt->execute()) {
            unset($_SESSION['reset_email']);
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Password berhasil diubah!'
            ];
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Gagal mengubah password!'
            ];
        }
    } else {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Password tidak cocok!'
        ];
    }
}

$notification = isset($_SESSION['notification']) ? $_SESSION['notification'] : null;
unset($_SESSION['notification']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Golf Club - Reset Password</title>
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
        .reset-container {
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
        .form-text {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        /* Overlay Notification Styles */
        .overlay-notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
        }

        .overlay-notification.success {
            background-color: #4CAF50;
        }

        .overlay-notification.error {
            background-color: #f44336;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <?php if ($notification): ?>
    <div class="overlay-notification <?php echo $notification['type']; ?>">
        <?php echo $notification['message']; ?>
    </div>
    <?php endif; ?>

    <div class="reset-container">
        <div class="login-logo">
            <img src="https://cdn-icons-png.flaticon.com/512/2076/2076787.png" alt="Golf Club Logo">
            <h2>New Password</h2>
        </div>
        
        <form method="post">
            <div class="mb-3">
                <input type="password" name="new_password" class="form-control" placeholder="Password Baru" required>
                <div class="form-text">Password minimal 8 karakter</div>
            </div>
            <div class="mb-3">
                <input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi Password" required>
            </div>
            <button type="submit" name="reset_password" class="btn btn-submit">Reset Password</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Notification handling
        document.addEventListener('DOMContentLoaded', function() {
            const notification = document.querySelector('.overlay-notification');
            if (notification) {
                notification.style.display = 'block';
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transition = 'opacity 0.5s ease-out';
                    setTimeout(() => {
                        notification.remove();
                    }, 500);
                }, 3000);
            }
        });
    </script>
</body>
</html>
