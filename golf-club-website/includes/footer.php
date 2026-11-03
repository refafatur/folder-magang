<footer>
        <div class="footer-grid">
            <div class="footer-logo">
                <img src="../assets/KGBR logo white.avif" alt="Golf Club Logo">
                <img src="../assets/HOME FOR GOLFERS WHITE.avif" alt="Home For Golfers Logo">
                <p>18 holes golf course, designed by renowned international golf course architect, Graham V. Marsh, is a 72-hectares masterpiece of stunning natural beauty.</p>
            </div>

            <div class="footer-menus">
                <div class="footer-menu-row">
                    <div class="footer-menu">
                        <ul>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="course.php">Courses Review</a></li>
                            <li><a href="event.php">Promotions & Events</a></li>
                            <li><a href="locker.php">Locker Room</a></li>
                            <li><a href="driver.php">Driving</a></li>
                            <li><a href="food.php">Food & Beverage</a></li>
                            <li><a href="vip.php">VIP Room & Matana</a></li>
                            <li><a href="prewedding.php">Pre-wedding Photoshoot</a></li>
                            <li><a href="https://www.google.com/maps/dir/-6.5982181,106.836336/Golf+estate+Bogor+raya,+9RWP%2BCCQ,+Sukaraja,+Kec.+Sukaraja,+Kabupaten+Bogor,+Jawa+Barat+16710/@-6.6010902,106.8324325,1068m/data=!3m2!1e3!4b1!4m9!4m8!1m1!4e1!1m5!1m1!1s0x2e69c76a0809d85b:0xa0b7d0f0991f02d5!2m2!1d106.8361046!2d-6.6039077?entry=ttu&g_ep=EgoyMDI0MTAxNi4wIKXMDSoASAFQAw%3D%3D">Rates</a></li>
                        </ul>
                    </div>

                    <div class="footer-menu">
                        <ul>
                            <li><a href="meet.php">Meeting & Gathering</a></li>
                            <li><a href="wedding.php">Wedding</a></li>
                            <li><a href="shop.php">Golf Shop</a></li>
                            <li><a href="food.php">Restaurant</a></li>
                        </ul>
                    </div>
                    
                </div>
            </div>
            
            <div class="footer-contact">
                <h3>Contact Us</h3>
                <div class="contact-buttons">
                    <a href="mailto:kgbr@bogorraya.co.id"><button>Email</button></a>
                    <a href="https://api.whatsapp.com/send?phone=628119718844"><button>WhatsApp</button></a>
                    <a href="https://www.google.com/maps/place/Klub+Golf+Bogor+Raya/@-6.598467,106.8336465,17z/data=!4m6!3m5!1s0x2e69c66bf0d0549b:0xacf4c6f2da35672f!8m2!3d-6.5984723!4d106.8362214!16s%2Fg%2F11gzpcyfh!5m1!1e1?entry=ttu&g_ep=EgoyMDI1MDYxMS4wIKXMDSoASAFQAw%3D%3D" target="_blank"><button>Directory</button></a>
                </div>
                <div class="hours">
                    <p><b>Monday - Sunday / Public Holiday</b></p>
                    <p>5:30 AM - 6 PM</p>
                </div>
                <p class="more-info">More Info</p>
                <div class="social-media">
                    <a href="#" style="color: #E1306C;"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="color: #FF0000;"><i class="fab fa-youtube"></i></a>
                    <a href="#" style="color: #000000;"><i class="fab fa-tiktok" style="color:white;"></i></a>
                    <a href="#" style="color: #1877F2;"><i class="fab fa-facebook"></i></a>
                    <a href="#" style="color: #1DA1F2;"><i class="fab fa-twitter"></i></a>
                    <a href="#" style="color: #0A66C2;"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>

        <div class="tripadvisor-section">
            <a href="https://www.tripadvisor.com/ShowUserReviews-g297706-d6457516-r241042517-Klub_Golf_Bogor_Raya-Bogor_West_Java_Java.html">
                <img src="assets/gif/1.gif" alt="TripAdvisor" style="width: 400px;">
            </a>
        </div>

        <div class="certification">
            <div class="qr-code">
                <img src="assets/photos/Barcode.avif" alt="QR Code">
            </div>
            <div class="chse-badge">
                <a href="https://chse.kemenparekraf.go.id/detail-tersertifikasi/klub-golf-bogor-raya">
                    <img src="assets/photos/Certificate.avif" alt="CHSE Certification">
                </a>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="policy-links">
                <a href="privacypolicy.php">Privacy Policy</a>
                <span>|</span>
                <a href="terms&service.php">Terms of Service</a>
            </div>
            <p>Copyright 2025 Klub Golf Bogor Raya</p>
        </div>
    </footer>
    
    <div class="weather-fab" id="weatherFab">
        <i class="fas fa-sun"></i>
        <div class="weather-details">
            <span class="temperature">--°C</span>
            <span class="condition">Loading...</span>
        </div>
    </div>

    <script src="script.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                easing: 'ease',
                once: true,
                offset: 100
            });
        });
    </script>
</body>
</html>
