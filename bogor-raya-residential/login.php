<?php
session_start();

if (isset($_SESSION['nama'])) {
    header("Location: dashboard.php");
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$database = "db_bogor";

$conn = mysqli_connect($host, $username, $password, $database);

if (isset($_GET['error']) && $_GET['error'] == 'account_inactive') {
    $error = "Your account has been deactivated. Please contact administrator.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = $_POST["password"];

    $query = "SELECT * FROM tbl_users WHERE username=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Check if user is already active
        if ($row["status"] == 1) {
            $error = "This account is already logged in elsewhere!";
        } else if (password_verify($password, $row["password"])) {
            // Update user status to active
            $update_query = "UPDATE tbl_users SET status = 1 WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "i", $row["id"]);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);

            // Set session variables
            $_SESSION["user_id"] = $row["id"]; // Add this line first
            $_SESSION["id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["nama"] = $row["nama"];
            $_SESSION["email"] = $row["email"];
            $_SESSION["level"] = $row["level"];
            $_SESSION["foto"] = $row["foto"];
            $_SESSION["permissions"] = $row["permissions"]; // Add this line to store permissions

            // If no permissions are set, set default permissions
            if (empty($_SESSION["permissions"])) {
                $_SESSION["permissions"] = json_encode([
                    'contacts' => ['view' => 0, 'add' => 0, 'edit' => 0, 'delete' => 0],
                    'news' => ['view' => 0, 'add' => 0, 'edit' => 0, 'delete' => 0],
                    'products' => ['view' => 0, 'add' => 0, 'edit' => 0, 'delete' => 0],
                    'testimonials' => ['view' => 0, 'add' => 0, 'edit' => 0, 'delete' => 0],
                    'users' => ['view' => 0, 'add' => 0, 'edit' => 0, 'delete' => 0],
                    'sales' => ['view' => 0, 'add' => 0, 'edit' => 0, 'delete' => 0]
                ]);
            }

            // Log login activity
            require_once 'includes/notifications.php';
            addNotification(
                $conn, 
                "User " . $row["nama"] . " has logged in", 
                "user", 
                "info", 
                "dashboard/profile.php"
            );

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width,</div> initial-scale=1.0, shrink-to-fit=no">
    <title>Bogor Raya Residential</title>
    <meta name="description" content="Official Marketing Gallery Bogor Raya Development">
    <meta property="og:image" content="https://images.unsplash.com/photo-1613977257363-707ba9348227">
    <meta property="og:type" content="website">
    <meta property="og:description" content="Website resmi kantor pemasaran perumahan cluster Bogor Raya Development">
    <link rel="icon" type="image/png" sizes="1181x430" href="assets/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/login.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
    <div class="container">
        <input type="checkbox" id="flip">
        <div class="cover">
            <div class="front">
                <img src="https://bogorraya.co.id/residential/assets/img/tr3/BIRD%20EYE%20VIEW_TERRACOTTA%20III%20-%2003_R1.jpg" alt="">
                <div class="text">
                    <span class="text-1">Every new friend is a <br> new adventure</span>
                    <span class="text-2">Let's get connected</span>
                </div>
            </div>
            <div class="back">
                <div class="text">
                    <span class="text-1">Complete miles of journey <br> with one step</span>
                    <span class="text-2">Let's get started</span>
                </div>
            </div>
        </div>
        <div class="forms">
            <div class="form-content">
                <div class="login-form">
                <?php if(isset($error)) { ?>
                <div class="alert alert-danger alert-dismissible fade show text-center mb-3" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

                    <div class="title">Login</div>
                    <form action="login.php" method="post">
                        <div class="input-boxes">
                            <div class="input-box">
                                <i class="fas fa-envelope"></i>
                                <input type="text" id="username" name="username" placeholder="Enter your email" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            <!-- <div class="text"><a href="forgot-password.php">Forgot password?</a></div> -->
                            <div class="button input-box">
                                <input type="submit" value="Submit">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>