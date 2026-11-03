<?php
session_start();
// Create directories if they don't exist
if (!file_exists('includes')) {
    mkdir('includes', 0777, true);
}
if (!file_exists('db')) {
    mkdir('db', 0777, true);
}

// Include database configuration - make sure this is at the top
if (!file_exists('db/config.php')) {
    die('Database configuration file is missing. Please create db/config.php');
}
require_once 'db/config.php';
$conn = $pdo; // Use the PDO connection from config.php

// Add at the top after other requires
require_once 'includes/email_config.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Process booking form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate inputs
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone']) || 
            empty($_POST['date']) || empty($_POST['times']) || empty($_POST['players'])) {
            throw new Exception("All fields are required");
        }

        // Start transaction
        $conn->beginTransaction();

        try {
            // Validate players count
            $players = intval($_POST['players']);
            if ($players < 1) {
                throw new Exception("Number of players must be at least 1");
            }

            // Validate selected times
            $selectedTimes = $_POST['times'];
            if (!is_array($selectedTimes) || empty($selectedTimes)) {
                throw new Exception("Please select at least one time slot");
            }

            // Check if booking already exists
            $check_query = "SELECT COUNT(*) FROM bookings WHERE 
                          booking_date = ? AND booking_time = ? AND status != 'cancelled'";
            $check_stmt = $conn->prepare($check_query);
            
            foreach ($selectedTimes as $time) {
                $check_stmt->execute([$_POST['date'], $time]);
                if ($check_stmt->fetchColumn() > 0) {
                    throw new Exception("Selected time slot is no longer available");
                }
            }

            // Generate unique booking code
            $booking_code = generateUniqueBookingCode($conn);
            $times_string = implode(',', $selectedTimes);
            
            $stmt = $conn->prepare("INSERT INTO bookings (booking_code, name, email, phone, booking_date, booking_time, players, status) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            
            $stmt->execute([
                $booking_code,
                $_POST['name'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['date'],
                $times_string,
                $players
            ]);

            // Commit transaction
            $conn->commit();

            // Create HTML email template
            $emailHTML = '
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { 
                        font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif;
                        line-height: 1.6;
                        margin: 0;
                        padding: 0;
                        background-color: #f5f5f5;
                        color: #333;
                    }
                    .container {
                        max-width: 600px;
                        margin: auto;
                        padding: 40px 20px;
                    }
                    .inner-container {
                        background: #ffffff;
                        border-radius: 16px;
                        padding: 30px;
                        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
                        position: relative;
                    }
                    .header, .content, .footer {
                        position: relative;
                        z-index: 1;
                    }
                    .content {
                        background-repeat: no-repeat;
                        background-position: center center;
                        background-size: contain;
                        border-radius: 12px;
                        padding: 40px 30px;
                        margin: 20px 0;
                        position: relative;
                    }
                    .content::before {
                        content: "";
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: rgba(255, 255, 255, 0.97);
                        border-radius: 12px;
                        z-index: -1;
                    }
                    .booking-details {
                        background: rgba(255, 255, 255, 0.95);
                        backdrop-filter: blur(5px);
                        margin: 30px 0;
                        padding: 30px;
                        border-radius: 12px;
                        border: 1px solid rgba(222, 226, 230, 0.8);
                    }
                    .booking-code {
                        font-size: 36px;
                        color: #1b5e20;
                        text-align: center;
                        margin: 20px 0;
                        font-weight: 800;
                        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
                        letter-spacing: 2px;
                    }
                    .detail-row {
                        display: flex;
                        margin: 20px 0;
                        padding: 15px;
                        background: white;
                        border-radius: 12px;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
                        border: 1px solid #e9ecef;
                    }
                    .detail-label {
                        font-weight: 600;
                        width: 140px;
                        color: #2e7d32;
                    }
                    .detail-value {
                        color: #212529;
                        flex: 1;
                        font-weight: 500;
                    }
                    .important-notes {
                        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
                        padding: 25px;
                        border-radius: 12px;
                        margin: 30px 0;
                        border: 1px solid #a5d6a7;
                    }
                    .important-notes h3 {
                        color: #1b5e20;
                        margin-top: 0;
                        font-size: 20px;
                        font-weight: 700;
                        margin-bottom: 15px;
                    }
                    .important-notes ul {
                        padding-left: 20px;
                        margin: 15px 0 0;
                    }
                    .important-notes li {
                        margin-bottom: 12px;
                        color: #2e7d32;
                        font-weight: 500;
                        position: relative;
                        padding-left: 10px;
                    }
                    .important-notes li::before {
                        content: "•";
                        color: #1b5e20;
                        font-weight: bold;
                        position: absolute;
                        left: -12px;
                    }
                    .footer {
                        text-align: center;
                        padding: 40px 20px;
                        color: #495057;
                        font-size: 14px;
                        line-height: 1.8;
                        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                        border-radius: 16px;
                        margin-top: 30px;
                    }
                    .contact-info {
                        display: flex;
                        justify-content: center;
                        gap: 30px;
                        margin: 20px 0;
                    }
                    .contact-item {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        padding: 10px 20px;
                        background: white;
                        border-radius: 25px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                    }
                    .logo {
                        margin-bottom: 25px;
                        font-size: 28px;
                        font-weight: bold;
                        background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        letter-spacing: 1px;
                    }
                    .social-links {
                        margin-top: 20px;
                    }
                    .social-links a {
                        display: inline-block;
                        margin: 0 10px;
                        color: #1b5e20;
                        text-decoration: none;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="inner-container">
                        <div class="header">
                            <h1>Booking Confirmation</h1>
                        </div>
                        <div class="content" style="background-image: url(\'assets/photos/Logo Master KGBR.avif\');">
                            <p style="font-size: 18px; color: #333; font-weight: 500;">Dear ' . htmlspecialchars($_POST['name']) . ',</p>
                            <p style="color: #666;">Thank you for choosing Klub Golf Bogor Raya. Your booking has been confirmed with the following details:</p>
                            
                            <div class="booking-details">
                                <div class="booking-code">' . $booking_code . '</div>
                                <div style="display: flex; align-items: center; gap: 2rem; margin: 2rem 0;">
                                    <div style="flex: 1;">
                                        <div class="detail-row">
                                            <span class="detail-label">📅 Date:</span>
                                            <span class="detail-value">' . date('l, d F Y', strtotime($_POST['date'])) . '</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">⏰ Time:</span>
                                            <span class="detail-value">' . implode(", ", array_map(function($t) { 
                                                return substr($t, 0, 5); 
                                            }, $selectedTimes)) . '</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">👥 Players:</span>
                                            <span class="detail-value">' . $_POST['players'] . '</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="important-notes">
                                <h3>🔍 Important Information</h3>
                                <ul>
                                    <li>Please arrive at least 15 minutes before your tee time</li>
                                    <li>Present your booking code at the reception desk</li>
                                    <li>Golf cart rental is available at the clubhouse</li>
                                    <li>Proper golf attire is required</li>
                                    <li>Cancelation must be made at least 24 hours before the scheduled time</li>
                                </ul>
                            </div>
                        </div>
                        <div class="footer">
                            <div class="logo">⛳ Klub Golf Bogor Raya</div>
                            <div class="contact-info">
                                <div class="contact-item">
                                    <span>📞</span>
                                    <span>+62 811-9718-844</span>
                                </div>
                                <div class="contact-item">
                                    <span>✉️</span>
                                    <span>kgbr@bogorraya.co.id</span>
                                </div>
                            </div>
                            <p>Perumahan Klub Golf, Jl. Golf Estate Bogor Raya, Sukaraja, Kec. Sukaraja, Kabupaten Bogor, Jawa Barat 16710</p>
                            <div class="social-links">
                                <a href="https://www.instagram.com/klubgolfbogorraya">Instagram</a> • 
                                <a href="https://www.youtube.com/channel/UCSiTPaR3GgW_p6i7iGy3YZQ">YouTube</a> • 
                                <a href="https://www.tiktok.com/@klubgolfbogorraya">TikTok</a> • 
                                <a href="https://www.instagram.com/klubgolfbogorraya">Facebook</a> • 
                                <a href="https://twitter.com/golfbogorraya">Twitter</a> • 
                                <a href="https://www.linkedin.com/company/28742982/admin/">LinkedIn</a>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
            </html>';

            // Replace existing email sending code
            try {
                $mail = new PHPMailer(true);
                
                // Enable SMTP debugging (0 = off, 1 = client messages, 2 = client and server messages)
                $mail->SMTPDebug = 0;
                
                // Server settings
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USER;
                $mail->Password = SMTP_PASS;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = SMTP_PORT;
                
                // Recipients
                $mail->setFrom(SMTP_FROM, SMTP_NAME);
                $mail->addAddress($_POST['email'], $_POST['name']);

                // Add embedded image for logo
                $logoPath = 'assets/photos/Logo Master KGBR.avif';
                if(file_exists($logoPath)) {
                    $mail->addEmbeddedImage($logoPath, 'logo', 'Logo Master KGBR.avif');
                }
                
                // Content
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = "Golf Booking Confirmation - " . $booking_code;
                // Add logo to email body
                $emailHTML = str_replace(
                    'url(\'assets/photos/Logo Master KGBR.avif\')',
                    'url(cid:logo)',
                    $emailHTML
                );
                $mail->Body = $emailHTML;
                $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $emailHTML));
                
                // Attempt to send email
                if(!$mail->send()) {
                    error_log("Email sending failed: " . $mail->ErrorInfo);
                    // Add notification that email failed but booking was successful
                    $_SESSION['email_status'] = 'failed';
                } else {
                    $_SESSION['email_status'] = 'success';
                }
                
            } catch (Exception $e) {
                error_log("Email sending failed: " . $e->getMessage());
                $_SESSION['email_status'] = 'failed';
            }

            // After successful email sending, redirect instead of JSON response
            if($_SESSION['email_status'] === 'success') {
                $_SESSION['booking_code'] = $booking_code;
                header("Location: booking.php");
                exit;
            }

        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['error_message'] = $e->getMessage();
            header("Location: booking.php");
            exit;
        }

    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: booking.php");
        exit;
    }
}

// Add this near the top of the file after session_start()
if(isset($_GET['success']) && isset($_SESSION['booking_code'])) {
    $booking_code = $_SESSION['booking_code'];
    unset($_SESSION['booking_code']); // Clear the session
}

// Validate date range
$minDate = date('Y-m-d');
$maxDate = date('Y-m-d', strtotime('+30 days'));

if(isset($_GET['check_availability'])) {
    try {
        $date = $_GET['date'];
        $time = $_GET['time'];
        
        // Validate date and time format
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date) || !preg_match("/^\d{2}:\d{2}:\d{2}$/", $time)) {
            throw new Exception("Invalid date or time format");
        }

        $query = "SELECT COUNT(*) as booked FROM bookings 
                  WHERE booking_date = ? AND booking_time LIKE ? 
                  AND status != 'cancelled'";
        $stmt = $conn->prepare($query);
        $stmt->execute([$date, "%$time%"]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Always return available = true since we removed the limit
        echo json_encode([
            'success' => true,
            'available' => true,
            'booked' => $row['booked']
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        header("Location: booking.php");
        exit();
    }
    exit;
}

function generateUniqueBookingCode($conn) {
    $attempts = 0;
    $maxAttempts = 10;
    
    do {
        $booking_code = 'BK' . date('ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Check if code exists
        $check = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE booking_code = ?");
        $check->execute([$booking_code]);
        $exists = $check->fetchColumn();
        
        $attempts++;
        
        if (!$exists) {
            return $booking_code;
        }
    } while ($attempts < $maxAttempts);
    
    // If we couldn't generate a unique code after max attempts
    throw new Exception("Unable to generate unique booking code. Please try again.");
}
?>
<?php include 'includes/header.php'; ?>
<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/photos/course.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
        <span class="section-subtitle" style="font-size: 2em; color: white;">Book Your Tee Time</span>
        <p style="color: rgba(255,255,255,0.8);">Experience world-class golfing at Klub Golf Bogor Raya</p>
    </div>
</header>

<section style="background: white; padding: 4rem 0; margin-top: -50px;">
    <div class="container" style="max-width: 1000px;">
        <div class="booking-card" style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 3rem;">
            
            <!-- Add this before the form -->
            <div class="booking-steps" style="margin-bottom: 3rem;" data-aos="fade-down">
                <div class="steps-container" style="display: flex; justify-content: center; align-items: center; max-width: 600px; margin: 0 auto;">
                    <div class="step active">
                        <div class="step-text" style="color: black; font-family: 'Montserrat', sans-serif; font-size: 1.5rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                            Booking
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" class="modern-form" action="booking.php">
                <?php if (isset($error_message)): ?>
                    <div data-aos="fade-up" style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 1rem; margin-bottom: 1rem; border-radius: 8px;">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <div style="margin-bottom: 2rem;" data-aos="fade-up" data-aos-delay="100">

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin-bottom: 2rem;">
                    <div class="form-group">
                        <label style="display: block; color: #333; margin-bottom: 0.5rem; font-weight: 500;">Full Name</label>
                        <input type="text" name="name" required style="width: 100%; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                    </div>
                    <div class="form-group">
                        <label style="display: block; color: #333; margin-bottom: 0.5rem; font-weight: 500;">Phone Number</label>
                        <input type="tel" name="phone" required style="width: 100%; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 2rem;">
                    <label style="display: block; color: #333; margin-bottom: 0.5rem; font-weight: 500;">Email Address</label>
                    <input type="email" name="email" required style="width: 100%; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                </div>
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; color: #333; margin-bottom: 0.5rem; font-weight: 500;">Number of Players</label>
                    <input type="number" name="players" min="1" value="1" required 
                           style="width: 100%; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                    <small style="color: #666;">Enter the number of players</small>
                </div>
                    <div class="form-group" style="max-width: 300px; margin-bottom: 2rem;">
                        <label style="display: block; color: #333; margin-bottom: 0.5rem; font-weight: 500;">Select Date</label>
                        <input type="date" class="form-control" id="date" name="date" 
                              min="<?php echo $minDate; ?>" max="<?php echo $maxDate; ?>" required 
                               style="width: 100%; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                    </div>

                    <div class="form-group">
                        <label style="display: block; color: #333; margin-bottom: 0.5rem; font-weight: 500;">Preferred Time</label>
                        <div id="timeSlotStatus" style="margin-bottom: 1rem; text-align: center; display: none;">
                            <div class="loading-spinner"></div>
                            <p>Checking availability...</p>
                        </div>
                        <div class="time-slots-container" style="height: 400px; overflow-y: auto;">
                            <div class="time-slots-grid" style="display: grid; grid-template-columns: repeat(8, 1fr); gap: 0.5rem; padding: 0.5rem; width: 100%;">
                                <?php
                                $times = [
                                    "05:36:00", "05:44:00", "05:52:00", "06:00:00", "06:08:00", "06:16:00",
                                    "06:24:00", "06:32:00", "06:40:00", "06:48:00", "06:56:00", "07:04:00",
                                    "07:12:00", "07:20:00", "07:28:00", "07:36:00", "07:44:00", "07:52:00",
                                    "08:00:00", "08:08:00", "08:16:00", "08:24:00", "08:32:00", "08:40:00",
                                    "08:48:00", "08:56:00", "09:04:00", "09:12:00", "09:20:00", "09:28:00",
                                    "09:36:00", "09:44:00", "09:52:00", "10:00:00", "10:08:00", "10:16:00",
                                    "10:24:00", "10:32:00", "10:40:00", "10:48:00", "10:56:00", "11:04:00",
                                    "11:12:00", "11:20:00", "11:28:00", "11:36:00", "11:44:00", "11:52:00",
                                    "12:00:00", "12:08:00", "12:16:00", "12:24:00", "12:32:00", "12:40:00",
                                    "12:48:00", "12:56:00", "13:04:00", "13:12:00", "13:20:00", "13:28:00",
                                    "13:36:00", "13:44:00", "13:52:00", "14:00:00", "14:08:00", "14:16:00",
                                    "14:24:00", "14:32:00", "14:40:00", "14:48:00", "14:56:00", "15:04:00"
                                ];

                                $today = date('Y-m-d');
                                $tomorrow = date('Y-m-d', strtotime('+1 day'));
                                $currentTime = date('H:i:s');
                                $selected_date = isset($_GET['date']) ? $_GET['date'] : $tomorrow;

                                foreach($times as $time) {
                                    // Only check past time if it's today, otherwise everything is available
                                    $isPastTime = ($selected_date === $today && $time <= $currentTime);
                                    $buttonStyle = $isPastTime ? 
                                        'background: #f5f5f5; color: #999; cursor: not-allowed; text-decoration: line-through;' : 
                                        'background: #4CAF50; color: white; cursor: pointer;';
                                
                                    echo '<div class="time-slot-option'.($isPastTime ? ' disabled past-time' : '').'" data-time="'.$time.'">
                                        <input type="checkbox" name="times[]" id="time_'.$time.'" value="'.$time.'" 
                                               '.($isPastTime ? 'disabled' : '').'
                                               style="position: absolute; opacity: 0;">
                                        <label for="time_'.$time.'" style="display: flex; align-items: center; justify-content: center;
                                               padding: 1rem; border: 1px solid #eee; border-radius: 12px; 
                                               transition: all 0.3s ease; font-size: 0.95rem;
                                               font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
                                               '.$buttonStyle.'">
                                        '.substr($time, 0, 5).'
                                    </label>
                                </div>';
                                }
                                ?>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Add this at the end before closing form -->
                <div class="summary-section" style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px; display: none;" data-aos="fade-up" data-aos-delay="300">
                    <h3>Booking Summary</h3>
                    <div class="summary-details"></div>
                </div>

                <button type="submit" id="submitBtn" data-aos="fade-up" data-aos-delay="400" style="width: 100%; padding: 1.2rem; background: goldenrod; color: white; border: none; 
                        border-radius: 8px; font-size: 1.1rem; font-weight: 500; cursor: pointer; transition: all 0.3s ease;">
                    Confirm Booking
                </button>
            </form>
        </div>

        <!-- Add loading overlay -->
        <div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
                <div class="spinner" style="width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid goldenrod; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto;"></div>
                <p style="margin-top: 20px; font-size: 1.2rem;">Processing your booking...</p>
            </div>
        </div>

        <!-- Add success notification -->
        <div id="successNotification" style="display: none; position: fixed; top: 20px; right: 20px; background: #4CAF50; color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 9999; max-width: 350px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                <div>
                    <h4 style="margin: 0 0 5px 0;">Booking Successful!</h4>
                    <p style="margin: 0; font-size: 0.9rem;">Check your email for booking details.</p>
                </div>
            </div>
        </div>

        <!-- Add success overlay -->
        <div id="bookingSuccessOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0,0,0,0.9); z-index: 9999;">
            <div class="success-content" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 3rem; border-radius: 20px; text-align: center; max-width: 90%; width: 450px;">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52" style="width: 80px; height: 80px; margin: 0 auto 20px;">
                    <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" style="stroke: #4CAF50; stroke-width: 2;"/>
                    <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" style="stroke: #4CAF50; stroke-width: 3;"/>
                </svg>
                <h2 style="color: #2e7d32; margin: 0 0 1rem;">Booking Successful!</h2>
                <div style="margin: 1.5rem 0;">
                    <p style="color: #666; margin: 0 0 0.5rem;">Your booking code:</p>
                    <p id="bookingCodeDisplay" style="font-size: 2rem; font-weight: bold; color: #1b5e20; margin: 0;"></p>
                </div>
                <p style="color: #666; margin: 0 0 2rem;">Confirmation email has been sent to your inbox.</p>
                <div class="action-buttons" style="display: flex; gap: 1rem; justify-content: center;">
                    <button onclick="window.location.href='index.php'" style="padding: 1rem 2rem; border: 2px solid #4CAF50; background: white; color: #4CAF50; border-radius: 8px; font-weight: 500; cursor: pointer;">Go Home</button>
                    <button onclick="window.location.reload()" style="padding: 1rem 2rem; background: #4CAF50; color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">New Booking</button>
                </div>
            </div>
        </div>

        <style>
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            #loadingOverlay.fade-out {
                animation: fadeOut 0.5s ease forwards;
            }

            #successNotification.slide-in {
                animation: slideIn 0.5s ease forwards;
            }

            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }

            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }

            @keyframes slideIn {
                from { transform: translate(-50%, -40%); opacity: 0; }
                to { transform: translate(-50%, -50%); opacity: 1; }
            }

            @keyframes checkmarkCircle {
                0% { stroke-dasharray: 157; stroke-dashoffset: 157; }
                100% { stroke-dasharray: 157; stroke-dashoffset: 0; }
            }

            @keyframes checkmarkCheck {
                0% { stroke-dasharray: 50; stroke-dashoffset: 50; }
                100% { stroke-dasharray: 50; stroke-dashoffset: 0; }
            }

            #bookingSuccessOverlay .success-content {
                animation: slideIn 0.6s ease forwards;
            }

            #bookingSuccessOverlay .checkmark-circle {
                animation: checkmarkCircle 1s ease-in-out forwards;
                stroke-dasharray: 157;
                stroke-dashoffset: 157;
            }

            #bookingSuccessOverlay .checkmark-check {
                animation: checkmarkCheck 0.6s 0.4s ease-out forwards;
                stroke-dasharray: 50;
                stroke-dashoffset: 50;
            }
        </style>

        <!-- Update form submission script -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.modern-form');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const submitBtn = document.getElementById('submitBtn');
            let hasSubmitted = false;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Cek jika sudah pernah submit
                if (hasSubmitted) {
                    console.log('Form sudah disubmit');
                    return false;
                }

                // Validasi time slot
                const selectedTimes = document.querySelectorAll('input[name="times[]"]:checked');
                if (selectedTimes.length === 0) {
                    alert('Silakan pilih minimal satu waktu bermain');
                    return false;
                }

                // Set flag submitted
                hasSubmitted = true;
                submitBtn.disabled = true;
                
                // Tampilkan loading
                loadingOverlay.style.display = 'block';

                // Submit form setelah delay singkat
                setTimeout(() => {
                    form.removeEventListener('submit', arguments.callee);
                    form._submit(); // Gunakan native submit
                }, 500);
            });

            // Backup native submit
            form._submit = form.submit;
            form.submit = function() {
                form.dispatchEvent(new Event('submit'));
            };

            // Reset flag saat page unload
            window.addEventListener('beforeunload', () => {
                hasSubmitted = false;
                submitBtn.disabled = false;
            });
        });
        </script>

        <!-- ...existing code... -->
    </div>
</section>

<style>
.player-option input[type="radio"]:checked + label {
    border-color: goldenrod;
    background: rgba(218, 165, 32, 0.1);
    color: goldenrod;
}

.form-control:focus {
    outline: none;
    border-color: goldenrod;
    box-shadow: 0 0 0 2px rgba(218, 165, 32, 0.1);
}

.time-slot-option input[type="checkbox"]:checked + label {
    background: #1b5e20 !important; /* Darker green when selected */
    color: white !important;
    border-color: #1b5e20 !important;
    transform: scale(1.05);
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(27, 94, 32, 0.2);
}

.time-slot-option.disabled {
    pointer-events: none;
}

.time-slot-option:not(.disabled) label:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.08);
    opacity: 0.9;
}

.time-slot-option input[type="checkbox"]:checked + label::before {
    content: '×';  /* Changed from '✓' to '×' */
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;  /* Changed to red background */
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;  /* Increased font size for 'X' */
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: transform 0.2s ease;
}

.time-slot-option input[type="checkbox"]:checked + label::before:hover {
    transform: scale(1.1);
    background: #c82333;  /* Darker red on hover */
}

.time-slot-option input[type="checkbox"]:checked + label::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 10px;
    height: 10px;
    background: white;
    border-radius: 50%;
    transform: translate(-50%, -50%) scale(0);
    transition: transform 0.2s ease;
}

.time-slot-option input[type="checkbox"]:checked + label:hover::after {
    transform: translate(-50%, -50%) scale(1);
}

.time-slot-option label:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.08);
}

.time-slots-grid {
    scrollbar-width: thin;
    scrollbar-color: goldenrod #f0f0f0;
}

.time-slots-grid::-webkit-scrollbar {
    width: 8px;
}

.time-slots-grid::-webkit-scrollbar-track {
    background: #f0f0f0;
    border-radius: 4px;
}

.time-slots-grid::-webkit-scrollbar-thumb {
    background-color: goldenrod;
    border-radius: 4px;
}

.time-slots-container {
    scrollbar-width: thin;
    scrollbar-color: #4CAF50 #f0f0f0;
}

.time-slots-container::-webkit-scrollbar {
    width: 6px;
}

.time-slots-container::-webkit-scrollbar-track {
    background: #f0f0f0;
    border-radius: 3px;
}

.time-slots-container::-webkit-scrollbar-thumb {
    background-color: #4CAF50;
    border-radius: 3px;
}

@media (max-width: 1400px) {
    .time-slots-container {
        height: 350px;
    }
    .time-slots-grid {
        grid-template-columns: repeat(6, 1fr) !important;
    }
}

@media (max-width: 1200px) {
    .time-slots-container {
        height: 300px;
    }
    .time-slots-grid {
        grid-template-columns: repeat(5, 1fr) !important;
    }
}

@media (max-width: 992px) {
    .time-slots-container {
        height: 400px;
    }
    .time-slots-grid {
        grid-template-columns: repeat(4, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .time-slots-container {
        height: 350px;
    }
    .booking-card {
        padding: 1.5rem;
    }
    
    .container {
        padding: 0 1rem;
    }
    
    .time-slots-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 576px) {
    .time-slots-container {
        height: 300px;
    }
    .time-slots-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

/* Hapus style past-time yang lama dan ganti dengan yang baru */
.time-slot-unavailable {
    opacity: 0.7;
    position: relative;
    text-decoration: line-through;
}

.time-slot-unavailable label {
    cursor: not-allowed !important;
    transform: none !important;
}
</style>

<script>
async function checkAllTimeSlots(date) {
    const statusDiv = document.getElementById('timeSlotStatus');
    const timeGrid = document.querySelector('.time-slots-grid');
    
    statusDiv.style.display = 'block';
    timeGrid.style.opacity = '0.5';

    try {
        const today = new Date().toISOString().split('T')[0];
        
        // Reset all slots first
        document.querySelectorAll('.time-slot-option').forEach(slot => {
            slot.classList.remove('time-slot-unavailable', 'disabled', 'past-time');
            const label = slot.querySelector('label');
            const input = slot.querySelector('input');
            input.disabled = false;
            label.style.background = '#4CAF50';
            label.style.color = 'white';
            label.style.textDecoration = 'none';
            label.style.cursor = 'pointer';
        });

        // Only check past times if date is today
        if(date === today) {
            updatePastTimeSlots();
        }

        // Check availability for all appropriate slots
        const availableSlots = document.querySelectorAll('.time-slot-option:not(.past-time)');
        const promises = Array.from(availableSlots).map(async (slot) => {
            const time = slot.querySelector('input').value;
            slot.classList.add('loading');
            
            try {
                const response = await fetch(`booking.php?check_availability=1&date=${date}&time=${time}`);
                const data = await response.json();
                slot.classList.remove('loading');
                
                if (!data.available) {
                    slot.classList.add('time-slot-unavailable');
                    slot.querySelector('input').disabled = true;
                }
            } catch (error) {
                console.error('Error checking availability:', error);
            }
        });

        // Wait for all checks to complete
        await Promise.all(promises);
    } finally {
        statusDiv.style.display = 'none';
        timeGrid.style.opacity = '1';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    // Set tomorrow as default date
    if (dateInput.value === '') {
        dateInput.value = tomorrow.toISOString().split('T')[0];
        checkAllTimeSlots(dateInput.value);
    }
    
    dateInput.addEventListener('change', function() {
        const selectedDate = this.value;
        const today = new Date().toISOString().split('T')[0];
        
        // Reset semua time slots
        document.querySelectorAll('.time-slot-option').forEach(slot => {
            slot.classList.remove('time-slot-unavailable');
            slot.querySelector('input').disabled = false;
        });

        // Cek waktu yang sudah lewat hanya untuk hari ini
        if(selectedDate === today) {
            const currentTime = new Date().toTimeString().split(' ')[0];
            document.querySelectorAll('.time-slot-option').forEach(slot => {
                const slotTime = slot.querySelector('input').value;
                if(slotTime <= currentTime) {
                    slot.classList.add('time-slot-unavailable');
                    slot.querySelector('input').disabled = true;
                }
            });
        }
        
        checkAllTimeSlots(selectedDate);
    });

    // Add time slot selection handling
    const timeSlots = document.querySelectorAll('input[name="times[]"]');

    timeSlots.forEach(slot => {
        slot.addEventListener('change', function() {
            // Update styles for selected slots
            const label = this.nextElementSibling;
            if (this.checked) {
                label.style.background = '#1b5e20';
                label.style.color = 'white';
            } else if (!this.disabled) {
                label.style.background = '#4CAF50';
                label.style.color = 'white';
            }
        });
    });
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const date = document.getElementById('date').value;
        const selectedTimes = document.querySelectorAll('input[name="times[]"]:checked');
        
        // Custom validation for time slots
        if (selectedTimes.length === 0) {
            alert('Please select at least one time slot');
            return;
        }

        // Rest of validation and submission
        const checkPromises = Array.from(selectedTimes).map(slot => {
            return fetch(`booking.php?check_availability=1&date=${date}&time=${slot.value}`)
                .then(res => res.json());
        });

        Promise.all(checkPromises)
            .then(results => {
                const allAvailable = results.every(data => data.success && data.available);
                if (allAvailable) {
                    form.submit();
                } else {
                    alert('Sorry, one or more time slots are no longer available. Please select different times.');
                    checkAllTimeSlots(date);
                }
            });
    });
});

function updatePastTimeSlots() {
    const dateInput = document.getElementById('date');
    const selectedDate = dateInput.value;
    const today = new Date().toISOString().split('T')[0];
    
    if(selectedDate === today) {
        const currentTime = new Date().toLocaleTimeString('en-US', { hour12: false });
        document.querySelectorAll('.time-slot-option:not(.past-time)').forEach(slot => {
            const time = slot.dataset.time;
            if(time <= currentTime) {
                slot.classList.add('disabled', 'past-time');
                const input = slot.querySelector('input');
                const label = slot.querySelector('label');
                input.disabled = true;
                label.style.background = '#f5f5f5';
                label.style.color = '#999';
                label.style.textDecoration = 'line-through';
                label.style.cursor = 'not-allowed';
            }
        });
    }
}

// Update setiap menit
setInterval(updatePastTimeSlots, 60000);

// Panggil saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    updatePastTimeSlots();
    // ...existing DOMContentLoaded code...
});

// Update saat tanggal berubah
document.getElementById('date').addEventListener('change', function() {
    updatePastTimeSlots();
    // ...existing change event code...
});
</script>

<?php 
// Include footer with absolute path
require_once 'includes/footer.php';
?>