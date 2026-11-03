<?php
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

        // Validate players count
        $players = intval($_POST['players']);
        if ($players < 1 || $players > 16) {
            throw new Exception("Number of players must be between 1 and 16");
        }

        // Validate selected times
        $selectedTimes = $_POST['times'];
        if (!is_array($selectedTimes) || count($selectedTimes) < 1 || count($selectedTimes) > 4) {
            throw new Exception("Please select between 1 and 4 time slots");
        }

        // Remove the old availability check and replace with this simpler version
        foreach ($selectedTimes as $time) {
            $query = "SELECT COUNT(*) as booked FROM bookings 
                      WHERE booking_date = ? AND booking_time = ? 
                      AND status != 'cancelled'";
            $stmt = $conn->prepare($query);
            $stmt->execute([$_POST['date'], $time]);
            // Remove the check for maximum bookings
        }

        // Insert bookings for each time slot
        $booking_code = 'BK' . date('ymd') . rand(1000, 9999);
        $times_string = implode(',', $selectedTimes); // Convert array to comma-separated string
        
        $stmt = $conn->prepare("INSERT INTO bookings (booking_code, name, email, phone, booking_date, booking_time, players, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        
        $stmt->execute([
            $booking_code,
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['date'],
            $times_string, // Store all times as a comma-separated string
            $players
        ]);

        // Create HTML email template
        $emailHTML = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: auto; padding: 20px; }
                .header { background: #1b5e20; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
                .booking-details { margin: 20px 0; background: white; padding: 15px; border-radius: 8px; }
                .booking-code { font-size: 24px; color: #1b5e20; text-align: center; margin: 20px 0; }
                .detail-row { display: flex; margin: 10px 0; }
                .detail-label { font-weight: bold; width: 100px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Booking Confirmation</h1>
                </div>
                <div class="content">
                    <p>Dear ' . htmlspecialchars($_POST['name']) . ',</p>
                    <p>Thank you for booking with Klub Golf Bogor Raya. Below are your booking details:</p>
                    
                    <div class="booking-details">
                        <div class="booking-code">Booking Code: ' . $booking_code . '</div>
                        <div class="detail-row">
                            <span class="detail-label">Date:</span>
                            <span>' . $_POST['date'] . '</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Time:</span>
                            <span>' . implode(", ", array_map(function($t) { return substr($t, 0, 5); }, $selectedTimes)) . '</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Players:</span>
                            <span>' . $_POST['players'] . '</span>
                        </div>
                    </div>

                    <p><strong>Important Notes:</strong></p>
                    <ul>
                        <li>Please arrive 15 minutes before your tee time</li>
                        <li>Bring your booking code for verification</li>
                        <li>Golf cart rental is available at the clubhouse</li>
                    </ul>
                </div>
                <div class="footer">
                    <p>Need assistance? Contact us:</p>
                    <p>Phone: +1234567890 | Email: support@golfbogor.com</p>
                    <p>Klub Golf Bogor Raya</p>
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
            
            // Content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = "Golf Booking Confirmation - " . $booking_code;
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

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode([
                'success' => true, 
                'code' => $booking_code,
                'emailStatus' => $_SESSION['email_status'] ?? 'unknown'
            ]);
            exit;
        }

        header("Location: booking.php?code=" . $booking_code);
        exit;
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
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
    }
    exit;
}
?>
<?php include 'includes/header.php'; ?>
<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/photos/course.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
    <div class="hero-content">
        <span class="section-subtitle" style="font-size: 2em; color: white;">Book Your Tee Time</span>
        <p style="color: rgba(255,255,255,0.8);">Experience world-class golfing at Klub Golf Bogor Raya</p>
    </div>
</header>

<section style="background: white; padding: 4rem 0; margin-top: -50px;">
    <div class="container" style="max-width: 1000px;">
        <div class="booking-card" style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 3rem;">
            
            <!-- Add this before the form -->
            <div class="booking-steps" style="margin-bottom: 3rem;">
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
                    <div style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 1rem; margin-bottom: 1rem; border-radius: 8px;">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <div style="margin-bottom: 2rem;">

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
                    <input type="number" name="players" min="1" max="16" value="1" required 
                           style="width: 100%; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                    <small style="color: #666;">You can book for up to 16 players</small>
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
                        <div style="display: flex; gap: 1.5rem; margin-top: 1.5rem; justify-content: center; font-size: 0.85rem;">
                            <span style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="width: 10px; height: 10px; background: #f5f5f5; border-radius: 50%;"></span>
                                <span style="color: #666;">Available</span>
                            </span>
                            <span style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="width: 10px; height: 10px; background: #4CAF50; border-radius: 50%;"></span>
                                <span style="color: #666;">Selected</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Add this at the end before closing form -->
                <div class="summary-section" style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px; display: none;">
                    <h3>Booking Summary</h3>
                    <div class="summary-details"></div>
                </div>

                <button type="submit" style="width: 100%; padding: 1.2rem; background: goldenrod; color: white; border: none; 
                        border-radius: 8px; font-size: 1.1rem; font-weight: 500; cursor: pointer; transition: all 0.3s ease;">
                    Confirm Booking
                </button>
            </form>
        </div>

        <div style="margin-top: 3rem; text-align: center; color: #666;">
            <p>Need assistance? Contact our golf concierge</p>
            <div style="margin-top: 1rem;">
                <a href="tel:+1234567890" style="color: goldenrod; text-decoration: none; margin: 0 1rem;">
                    <i class="fas fa-phone"></i> Call Us
                </a>
                <a href="https://wa.me/1234567890" style="color: goldenrod; text-decoration: none; margin: 0 1rem;">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
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
    content: '✓';
    position: absolute;
    top: -8px;
    right: -8px;
    background: currentColor;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.time-slot-option input[type="checkbox"]:checked + label[style*="C49132"]::before {
    background: #C49132;
}

.time-slot-option input[type="checkbox"]:checked + label[style*="7A9555"]::before {
    background: #7A9555;
}

.time-slot-option input[type="checkbox"]:checked + label[style*="D17B45"]::before {
    background: #D17B45;
}

.time-slot-option input[type="checkbox"]:checked + label[style*="5B7086"]::before {
    background: #5B7086;
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

.booking-steps {
    margin-bottom: 3rem;
}

.step {
    text-align: center;
    position: relative;
}

.step-number {
    width: 30px;
    height: 30px;
    background: #eee;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
}

.step.active .step-number {
    background: goldenrod;
    color: white;
}

.disabled {
    opacity: 0.7;
    position: relative;
}

.disabled label {
    background: #f5f5f5 !important;
    color: #999 !important;
    border-color: #ddd !important;
    cursor: not-allowed !important;
    transform: none !important;
}

.disabled label:hover {
    transform: none !important;
    box-shadow: none !important;
}

.time-slot-option.loading label {
    background: #f5f5f5 !important;
    color: #999 !important;
    position: relative;
    overflow: hidden;
}

.time-slot-option.loading label::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 200%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255,255,255,0.3),
        transparent
    );
    animation: loading 1.5s infinite;
}

.loading-spinner {
    width: 30px;
    height: 30px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid goldenrod;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

.disabled:hover::after {
    content: attr(data-reason);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
}

@keyframes loading {
    from {
        left: -100%;
    }
    to {
        left: 100%;
    }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 1400px) {
    .time-slots-grid {
        grid-template-columns: repeat(6, 1fr) !important;
    }
}

@media (max-width: 1200px) {
    .time-slots-grid {
        grid-template-columns: repeat(5, 1fr) !important;
    }
}

@media (max-width: 992px) {
    .time-slots-grid {
        grid-template-columns: repeat(4, 1fr) !important;
    }
}

@media (max-width: 768px) {
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
        timeGrid.style.opacity = '1';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
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
    const maxSlots = 4;

    timeSlots.forEach(slot => {
        slot.addEventListener('change', function() {
            const checkedSlots = Array.from(document.querySelectorAll('input[name="times[]"]:checked'))
                .sort((a, b) => a.value.localeCompare(b.value));
            
            // Only check for maximum slots
            if (checkedSlots.length > maxSlots) {
                this.checked = false;
                alert(`You can only select up to ${maxSlots} time slots`);
                return;
            }

            // Update styles for selected slots
            timeSlots.forEach(input => {
                const label = input.nextElementSibling;
                if (input.checked) {
                    label.style.background = '#1b5e20';
                    label.style.color = 'white';
                } else if (!input.disabled) {
                    label.style.background = '#4CAF50';
                    label.style.color = 'white';
                }
            });
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