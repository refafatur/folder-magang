<?php
session_start();
require_once 'db/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM tbl_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['foto'] = $user['foto'];
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['message'] = "Invalid username or password";
            header("Location: login.php");
            exit();
        }
    } catch(PDOException $e) {
        die("Login failed: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Golf Club - Login</title>
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
        .login-container {
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
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(218, 165, 32, 0.25);
            border-color: var(--accent-color);
        }
        .btn-login {
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
        .btn-login:hover {
            background: #daa520;
            transform: translateY(-2px);
        }
        .error-message {
            color: #dc3545;
            text-align: center;
            margin-bottom: 15px;
        }
        .text-center a {
            color: var(--accent-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .text-center a:hover {
            color: #daa520;
        }

        /* Add responsive styles */
        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
            }
            
            .login-logo img {
                width: 60px;
            }
            
            .login-logo h2 {
                font-size: 1.5rem;
            }
            
            .form-control {
                padding: 10px 15px;
            }
            
            .btn-login {
                padding: 10px;
            }
            
            .text-center {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="https://cdn-icons-png.flaticon.com/512/2076/2076787.png" alt="Golf Club Logo">
            <h2>Welcome Back</h2>
        </div>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='error-message'>" . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']);
        }
        ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-login">Sign In</button>
        </form>
        <p class="text-center mt-4 mb-0">Forgot Password? <a href="lupasandi.php">Click here</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
