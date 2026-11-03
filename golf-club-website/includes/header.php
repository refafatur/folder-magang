<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Golf Bogor Raya</title>
    <link rel="icon" href="assets/photos/Logo Master KGBR.avif" type="image/png">
    <link rel="stylesheet" href="styles.css">
    <link href="bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/brands.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/solid.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Single AOS initialization with optimal settings
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                offset: 100,
                delay: 100,
                once: false,
                mirror: true,
                anchorPlacement: 'top-bottom'
            });

            // Efficient update mechanism
            let updateTimeout;
            const refreshAOS = () => {
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(() => {
                    AOS.refresh();
                }, 60);
            };

            window.addEventListener('scroll', refreshAOS);
            document.addEventListener('scrollend', refreshAOS);
            setInterval(AOS.refresh, 2000);
        });
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="menu-toggle">
            <i class="fas fa-bars" onclick="openMenu()"></i>
        </div>
        <div class="logo">
            <a href="index.php">
                <img src="../assets/KGBR logo white.avif" alt="Golf Club Logo">
            </a>
            <img src="../assets/HOME FOR GOLFERS WHITE.avif" alt="Home For Golfers Logo">
        </div>
        <div class="nav-buttons">
            <a href="booking.php" style="text-decoration: none;"><button class="lets-golfing">Let's Golfing</button></a>
        </div>
    </nav>

    <div class="menu-overlay" id="menuOverlay">
        <div class="menu-header">
            <span>CLOSE TO MENU</span>
            <button class="close-menu" onclick="closeMenu()">×</button>
        </div>
        
        <div class="menu-content">
            <div class="menu-section">
            <h3><a href="index.php">GOLF</a></h3>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="course.php">Course Review</a></li>
                    <li><a href="event.php">Promotions & Events</a></li>
                    <li><a href="locker.php">Locker Room</a></li>
                    <li><a href="driving.php">Driving</a></li>
                </ul>
            </div>

            <div class="menu-section">
                <h3>SERVICES</h3>
                <ul>
                    <li><a href="food.php">Food & Beverage</a></li>
                    <li><a href="vip.php">VIP Room & Matana</a></li>
                    <li><a href="prewedding.php">Pre-Wedding Photoshoot</a></li>
                </ul>
            </div>

            <div class="menu-section">
                <h3><a href="meet.php">MEETING & GATHERING</a></h3>
                <h3><a href="wedding.php">WEDDING</a></h3>
                <h3><a href="shop.php">GOLF SHOP</a></h3>
                <h3><a href="contact.php">CONTACT US</a></h3>
            </div>

            <div class="menu-section">
                <h3>Interested in playing golf with us? Let us help</h3>
                <div class="booking-buttons">
                    <button><a href="booking.php">Book A Tee Time</a></button>
                    <!-- <button><a href="https://wa.me/+6281234567890" target="_blank">Live Chat</a></button> -->
                    <button><a href="../assets/rates/FALYER PRICE LIST A4 FIX.pdf" target="_blank">Rates</a></button>
                </div>
                <p><b>Monday - Friday - Weekends & Public Holidays</b></p>
                <p>6 AM to 5 PM</p>
            </div>

        </div>
    </div>