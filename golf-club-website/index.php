<?php 
require_once 'config/database.php';

// Fetch events
$sql = "SELECT * FROM tbl_events ORDER BY event_date DESC LIMIT 3";
$result = $conn->query($sql);
$events = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Fetch promotions
$sql = "SELECT * FROM tbl_promotions ORDER BY id DESC LIMIT 3";
$result = $conn->query($sql);
$promotions = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $promotions[] = $row;
    }
}

// Fetch locker rooms
$sql = "SELECT * FROM tbl_lockers ORDER BY id ASC LIMIT 1";
$result = $conn->query($sql);
$locker = $result->fetch_assoc();

// Fetch food items
$sql = "SELECT * FROM tbl_food ORDER BY id ASC LIMIT 1";
$result = $conn->query($sql);
$food = $result->fetch_assoc();

// Fetch wedding images
$sql = "SELECT * FROM tbl_wedding ORDER BY id ASC LIMIT 1";
$result = $conn->query($sql);
$wedding = $result->fetch_assoc();

// Fetch courses
$sql = "SELECT * FROM tbl_courses ORDER BY id ASC LIMIT 3";
$result = $conn->query($sql);
$courses = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

include 'includes/header.php'; 
?>
    <!-- Hero Section -->
    <header class="hero" style="background-position: center; background-size: cover; height: 100vh; padding: 0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
        <video autoplay muted loop playsinline style="position: absolute; width: 100%; height: 100%; object-fit: cover;">
            <source src="assets/vid/golf.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="hero-content" style="position: relative; z-index: 2;">
            <img src="assets/KGBR Logo White.avif" alt="Golf Club Logo" alt="Golf Club Logo" style="width: auto; height: 25%;">
            <img src="assets/HOME FOR GOLFERS WHITE.avif" alt="Home For Golfers Logo">
        </div>
    </header>

    <!-- About Brief -->
    <section style="background: white; padding: 6rem 0;" data-aos="fade-up">
        <div class="container">
            <div style="text-align: center; max-width: 800px; margin: 0 auto;">
                <span class="section-subtitle">WELCOME TO</span>
                <h2 style="color: var(--primary-color); margin-bottom: 2rem;">KLUB GOLF BOGOR RAYA</h2>
                <p style="color: #666; line-height: 1.8; margin-bottom: 2rem;">
                    An 18 holes golf course designed by renowned international golf course architect, Graham V. Marsh. Located in the heart of Bogor, our 72-hectares masterpiece offers stunning natural beauty and challenging play for golfers of all levels.
                </p>
                <a href="about.php" class="btn-register" style="width:50%; justify-content:center; text-decoration: none; display: block; text-align: center; margin: 0 auto;">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Awards Showcase -->
    <section class="awards-showcase" style="background: #f9f9f9; padding: 4rem 0;">
        <div class="container" data-aos="fade-up">
            <span class="section-subtitle">OUR ACHIEVEMENTS</span>
            <h2 style="color: var(--primary-color);">Award-Winning Excellence</h2>
            <div class="awards-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div class="award-item" style="text-align: center;" data-aos="zoom-in" data-aos-delay="100">
                    <img src="assets/certi/2.png" alt="Award" style="height: 100px; margin-bottom: 1rem;">
                    <h3 style="color: var(--primary-color);">NO.4th TOP LOCKER ROOM</h3>
                    <p style="color: #666;">Golf Vacation Magazine 2015</p>
                </div>
                <div class="award-item" style="text-align: center;" data-aos="zoom-in" data-aos-delay="200">
                    <img src="assets/certi/1.png" alt="Award" style="height: 100px; margin-bottom: 1rem;">
                    <h3 style="color: var(--primary-color);">BEST COURSE INDONESIAN</h3>
                    <p style="color: #666;">Asian Golf Awards 2015</p>
                </div>
                <div class="award-item" style="text-align: center;" data-aos="zoom-in" data-aos-delay="300">
                    <img src="assets/certi/3.png" alt="Award" style="height: 100px; margin-bottom: 1rem;">
                    <h3 style="color: var(--primary-color);">BEST WEDDING DESTINATION</h3>
                    <p style="color: #666;">Asian Lifestyle Tourism Awards 2017</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Course Preview -->
    <section class="course-highlights" style="padding: 6rem 0;">
        <div class="container" data-aos="fade-up">
            <div data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                <span class="section-subtitle">DISCOVER</span>
                <h2 style="color: var(--primary-color);">Our Signature Holes</h2>
            </div>
            <div class="highlights-grid">
                <?php foreach($courses as $index => $course): ?>
                    <div class="highlight-item <?php echo $index === 0 ? 'large' : ''; ?>" 
                         data-aos="fade-up" 
                         data-aos-delay="<?php echo ($index + 1) * 100; ?>">
                        <img src="../<?php echo htmlspecialchars($course['view_image']); ?>" alt="Course View">
                        <div class="highlight-content">
                            <div class="highlight-header">
                                <h3><?php echo htmlspecialchars($course['name']); ?></h3>
                                <span class="hole-info">HOLE <?php echo $index + 1; ?> • PAR <?php echo htmlspecialchars($course['par']); ?></span>
                            </div>
                            <?php if($index === 0): ?>
                                <p ><?php echo htmlspecialchars($course['description']); ?></p>
                                <div class="stats-row">
                                    <div class="stat">
                                        <span class="value"><?php echo htmlspecialchars($course['length']); ?></span>
                                        <span class="label">YARDS</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 3rem;">
                <a href="course.php" class="btn-membership" style="color:grey; text-decoration: none;">Explore Full Course</a>
            </div>
        </div>
    </section>
    
    <!-- Facilities Showcase -->
    <section style="background: #f9f9f9; padding: 6rem 0;">
        <div class="container" data-aos="fade-up">
            <span class="section-subtitle">FACILITIES</span>
            <h2 style="color: var(--primary-color); margin-bottom: 3rem;">World-Class Amenities</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                <div class="facility-card" style="text-align: center;" data-aos="zoom-in" data-aos-delay="100">
                    <div style="height: 200px; margin-bottom: 1.5rem;">
                        <img src="../<?php echo htmlspecialchars($driver['image'] ?? '/assets/driver/1.avif'); ?>" alt="Driving Range" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                    </div>
                    <h3 style="color: var(--primary-color);"><?php echo htmlspecialchars($driver['name'] ?? 'Driving Range'); ?></h3>
                    <p style="color: #666; margin-top: 0.5rem;"><?php echo htmlspecialchars($driver['description'] ?? '34 practice bays with advanced technology'); ?></p>
                    <a href="driving.php" style="color: goldenrod; text-decoration: none; margin-top: 1rem; display: inline-block;">Learn More →</a>
                </div>

                <div class="facility-card" style="text-align: center;" data-aos="zoom-in" data-aos-delay="200">
                    <div style="height: 200px; margin-bottom: 1.5rem;">
                        <img src="../<?php echo htmlspecialchars($locker['image']); ?>" alt="Locker Rooms" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                    </div>
                    <h3 style="color: var(--primary-color);"><?php echo htmlspecialchars($locker['name'] ?? 'Premium Lockers'); ?></h3>
                    <p style="color: #666; margin-top: 0.5rem;"><?php echo htmlspecialchars($locker['description'] ?? 'World\'s Top 4 Ranked Locker Facilities'); ?></p>
                    <a href="locker.php" style="color: goldenrod; text-decoration: none; margin-top: 1rem; display: inline-block;">Learn More →</a>
                </div>

                <div class="facility-card" style="text-align: center;" data-aos="zoom-in" data-aos-delay="300">
                    <div style="height: 200px; margin-bottom: 1.5rem;">
                        <img src="../<?php echo htmlspecialchars($food['image_path']); ?>" alt="Restaurant" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                    </div>
                    <h3 style="color: var(--primary-color);"><?php echo htmlspecialchars($food['name'] ?? 'Fine Dining'); ?></h3>
                    <p style="color: #666; margin-top: 0.5rem;"><?php echo htmlspecialchars($food['description'] ?? 'Exquisite culinary experiences'); ?></p>
                    <a href="food.php" style="color: goldenrod; text-decoration: none; margin-top: 1rem; display: inline-block;">Learn More →</a>
                </div>

                <div class="facility-card" style="text-align: center;" data-aos="zoom-in" data-aos-delay="400">
                    <div style="height: 200px; margin-bottom: 1.5rem;">
                        <img src="../<?php echo htmlspecialchars($wedding['foto']); ?>" alt="Event Spaces" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                    </div>
                    <h3 style="color: var(--primary-color);"><?php echo htmlspecialchars($wedding['name'] ?? 'Event Spaces'); ?></h3>
                    <p style="color: #666; margin-top: 0.5rem;"><?php echo htmlspecialchars($wedding['description'] ?? 'Perfect venues for special occasions'); ?></p>
                    <a href="wedding.php" style="color: goldenrod; text-decoration: none; margin-top: 1rem; display: inline-block;">Learn More →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Club Stats -->
    <!-- <section style="background: var(--primary-color); padding: 8rem 0;">
        <div class="container" data-aos="fade-up">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 3rem; text-align: center;">
                <div class="stat-item">
                    <span style="font-size: 3rem; color: goldenrod; display: block;">25+</span>
                    <span style="color: goldenrod; margin-top: 1rem; display: block;">Years Experience</span>
                </div>
                <div class="stat-item">
                    <?php
                    $sql = "SELECT SUM(par) as total_par FROM tbl_courses";
                    $result = $conn->query($sql);
                    $total_par = $result->fetch_assoc()['total_par'];
                    ?>
                    <span style="font-size: 3rem; color: goldenrod; display: block;"><?php echo $total_par; ?></span>
                    <span style="color: goldenrod; margin-top: 1rem; display: block;">Course Par</span>
                </div>
                <div class="stat-item">
                    <span style="font-size: 3rem; color: goldenrod; display: block;">6</span>
                    <span style="color: goldenrod; margin-top: 1rem; display: block;">Major Awards</span>
                </div>
                <div class="stat-item">
                    <span style="font-size: 3rem; color: goldenrod; display: block;">500+</span>
                    <span style="color: goldenrod; margin-top: 1rem; display: block;">Events Hosted</span>
                </div>
            </div>
        </div>
    </section> -->



    <!-- Current Events Preview -->
    <section style="background: var(--primary-color); padding: 6rem 0;">
        <div class="container" data-aos="fade-up">
            <span class="section-subtitle" style="color: white;">CURRENT EVENTS</span>
            <h2 style="color: white; margin-bottom: 3rem;">Latest Promotions</h2>
            <div class="events-preview" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                <?php foreach($events as $event): ?>
                    <div class="event-preview" style="background: rgba(255,255,255,0.1); border-radius: 15px; overflow: hidden;">
                        <img src="../<?php echo htmlspecialchars($event['image']); ?>" 
                             alt="<?php echo htmlspecialchars($event['name']); ?>" style="width: 100%; height: 200px; object-fit: cover;">
                        <div style="padding: 1.5rem;">
                            <h3 style="color: white; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($event['name']); ?></h3>
                            <p style="color: rgba(255,255,255,0.7);"><?php echo date('F d', strtotime($event['event_date'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 3rem;">
                <a href="event.php" class="btn-membership" style="text-decoration: none;">View All Events</a>
            </div>
        </div>
    </section>

    <!-- Booking Preview -->
    <section class="booking-preview">
        <div class="container" data-aos="fade-up">
            <div class="membership-grid">
                <div class="membership-content">
                    <span class="section-subtitle" style="color:white;">BOOK NOW</span>
                    <h2 class="luxury-title">Reserve Your Tee Time</h2>
                    <p class="luxury-description">Experience world-class golfing at Bogor Raya Golf Club</p>
                    <div class="membership-features">
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-golf-ball"></i>
                            </div>
                            <span>18-Hole Course</span>
                        </div>
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span>Flexible Hours</span>
                        </div>
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <span>Premium Equipment</span>
                        </div>
                    </div>
                    <a href="booking.php" class="btn-membership" style="text-decoration: none">Book Your Round</a>
                </div>
                <div class="membership-image">
                    <img src="assets/photos/golf-ball.png" alt="Golf Ball Decoration" class="floating">
                </div>
            </div>
        </div>
    </section>

<style>
@media (max-width: 768px) {
    .hero-content img {
        width: 80%;
        max-width: 300px;
    }
    
    .container {
        padding: 0 20px;
    }
    
    .highlight-item img {
        height: 200px !important;
    }
    
    .facility-card {
        margin-bottom: 2rem;
    }
    
    .stat-item {
        margin-bottom: 2rem;
    }
    
    .membership-grid {
        grid-template-columns: 1fr !important;
    }
    
    .membership-image {
        display: none;
    }
    
    h2 {
        font-size: 24px !important;
    }
    
    .btn-membership, .btn-register {
        width: 100% !important;
        margin: 10px 0;
    }
    
    .events-preview {
        margin: 0 15px;
    }
}

@media (max-width: 480px) {
    section {
        padding: 3rem 0 !important;
    }
    
    .awards-grid {
        gap: 1rem !important;
    }
    
    .stat-item span {
        font-size: 2rem !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>