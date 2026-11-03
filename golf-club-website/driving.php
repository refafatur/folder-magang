<?php 
include 'includes/header.php';
include 'config/database.php';

// Fetch driving range data
$sql = "SELECT * FROM tbl_drivings";
$result = $conn->query($sql);
$drivings = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $drivings[] = $row;
    }
}
?>

<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/driver/1.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
        <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
            <span class="section-subtitle" style="font-size: 2em; margin-bottom: 100px; margin: 0 auto; color: white;">Driving Range</span>
        </div>
    </header>


    <!-- Driver Section -->
    <section class="promotions-section">
        <div class="container">
            <div class="intro-text" data-aos="fade-up" style="text-align: center; margin-bottom: 50px;">
                <span class="section-subtitle" style="font-size: 2em; margin-bottom: 100px; margin: 0 auto; color: #666;">DRIVING AND PUTTING GREEN</span>
                <p style="font-size: 1.2em; line-height: 1.6; max-width: 800px; margin: 0 auto; color: #666;">
                    18 bays of real grass and 16 bays of artificial grass, hitting and practicing your shots on real grass is much better than mat. We open at 06:00 am – 05:00pm.
                </p>
            </div>
            <span class="section-subtitle" data-aos="fade-up">OUR SERVICES</span>
            <h2 data-aos="fade-up">DRIVING RANGE EXPERIENCE</h2>
            <div class="promotions-grid">
                <?php foreach ($drivings as $key => $driving): 
                    // Randomly decide if card should be wide (full-width)
                    $isWide = rand(0, 2) == 0; // 1/3 chance to be wide
                    $randomHeight = rand(400, 500);
                ?>
                <div class="promo-card <?php echo $isWide ? 'wide' : ''; ?>" 
                     data-aos="fade-up" 
                     data-aos-delay="<?php echo $key * 100; ?>">
                    <div class="promo-image" style="height: <?php echo $randomHeight; ?>px;">
                        <img src="../<?php echo $driving['image']; ?>" alt="<?php echo htmlspecialchars($driving['name']); ?>">
                        <div class="content-preview">
                            <h3><?php echo htmlspecialchars($driving['name']); ?></h3>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <style>
    .promotions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }
    .promo-card {
        cursor: pointer;
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        width: 100%;
    }
    .promo-card.wide {
        grid-column: 1 / -1; /* Span all columns */
    }
    .promo-image {
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .promo-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .content-preview {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 15px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }
    .promo-image:hover .content-preview {
        transform: translateY(0);
    }
    .content-preview h3 {
        margin: 0 0 5px 0;
        font-size: 1.2em;
    }
    </style>

<?php include 'includes/footer.php'; ?>