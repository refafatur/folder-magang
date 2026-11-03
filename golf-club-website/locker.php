<?php 
include 'includes/header.php';
include 'config/database.php';

// Fetch locker data from database
$sql = "SELECT * FROM tbl_lockers";
$result = $conn->query($sql);
$lockers = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $lockers[] = $row;
    }
}
?>

    <header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/locker/1.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
        <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                <img src="assets/locker/2.avif" alt="Locker" width="auto" height="110">
        </div>
    </header>

    <!-- Upcoming Events -->
    <section class="events-section">
        <div class="container">
            <span class="section-subtitle" data-aos="fade-up">LOCKER</span>
            <div class="events-grid" style="display: flex; justify-content: center;">
                <!-- Single centered event -->
                <div class="event-card" data-aos="zoom-in" data-aos-delay="100" style="margin: 0 auto;">
                    <img src="assets/locker/serti.avif" alt="Summer Golf Championship" style="max-width: 100%; height: auto; display: block;">
                </div>
            </div>
        </div>
    </section>

    <!-- Locker Section -->
    <section class="promotions-section">
        <div class="container">
            <div class="intro-text" data-aos="fade-up" style="text-align: center; margin-bottom: 50px;">
                <h2 style="font-size: 2.5em; margin-bottom: 20px;">Your Home Away From Home</h2>
                <p style="font-size: 1.2em; line-height: 1.6; max-width: 800px; margin: 0 auto; color: #666;">
                    Proud to present No. 4 Top Locker Room in The World on 2015 by Golf Vacations Magazine, the Men's locker room has 242 lockers with Javanese artifacts and colonial furniture add charm and character to include huge spacious locker room. Each shower room has 2 optional, indoor and outdoor shower, makes the different sensation for relaxing bath. Other facilities at locker room are sauna, massage and lazy chair. 
                </p>
            </div>
            <span class="section-subtitle" data-aos="fade-up">FACILITIES</span>
            <h2 data-aos="fade-up" data-aos-delay="100">LOCKER ROOM</h2>
            <div class="promotions-grid">
                <?php foreach ($lockers as $key => $locker): ?>
                <?php 
                    // Randomly decide if the item should be wide
                    $isWide = rand(0, 1);
                    $gridColumn = $isWide ? "span 2" : "span 1";
                    $height = rand(300, 500); // Random height between 300px and 500px
                ?>
                <div class="promo-card" 
                     data-aos="fade-up" 
                     data-aos-delay="<?php echo $key * 100; ?>" 
                     style="grid-column: <?php echo $gridColumn; ?>;">
                    <div class="promo-image" style="height: <?php echo $height; ?>px;">
                        <img src="../<?php echo $locker['image']; ?>" alt="<?php echo $locker['name']; ?>">
                        <div class="content-preview">
                            <h3><?php echo $locker['name']; ?></h3>
                            <?php if (!empty($locker['description'])): ?>
                            <p><?php echo $locker['description']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <style>
    .hidden {
        display: none;
    }
    .promotions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }
    .promo-card {
        cursor: pointer;
        position: relative;
        width: 100%;
    }
    .promo-image {
        position: relative;
        overflow: hidden;
        height: 400px;
    }
    .promo-image img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Ensure images cover the area nicely */
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
    .content-preview p {
        margin: 5px 0;
        font-size: 0.9em;
    }
    </style>

    <!-- <script>
    function toggleContent(element) {
        const content = element.querySelector('.promo-content');
        content.classList.toggle('hidden');
    }
    </script> -->


<?php include 'includes/footer.php'; ?>