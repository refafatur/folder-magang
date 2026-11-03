<?php 
include 'includes/header.php';
require_once 'db/config.php';

// Get wedding gallery images
$type = 'wedding';
$stmt = $pdo->prepare("SELECT * FROM tbl_wedding WHERE type = :type ORDER BY created_at DESC");
$stmt->execute(['type' => $type]);
$images = $stmt->fetchAll();
?>

<style>
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    grid-auto-rows: 300px;
    grid-auto-flow: dense;
    gap: 5px;
    padding: 5px;
}

.gallery-item {
    position: relative;
    overflow: hidden;
    width: 100%;
    height: 100%;
}

.gallery-item.wide {
    grid-column: span 2;
}

.gallery-item.tall {
    grid-row: span 2;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.gallery-item:hover img {
    transform: scale(1.1);
}
</style>

<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/wedding/1a.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
        <img src="assets/wedding/1.avif" alt="Klub Golf Bogor Raya Logo" class="hero-logo" width="auto" height="150">
    </div>
</header>

<!-- Description Section -->
<section class="description-section">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto text-center" data-aos="fade-up" data-aos-delay="100">
                <h2 class="section-title">Weddings at Klub Golf Bogor Raya</h2>
                <div class="section-description">
                    <p data-aos="fade-up" data-aos-delay="200">Klub Golf Bogor Raya has outdoor and indoor wedding venues surrounded by beautiful golf views which will make for a memorable wedding celebration. With a capacity of 300 – 1000 guests, you can choose the venue that suits the lovely couple.</p>
                    
                    <p data-aos="fade-up" data-aos-delay="300">Minimal decorations are needed - our outdoor venue with natural grass as tapestry and beautiful sky as the roof creates a perfect setting for your wedding party. The indoor venue provides a semi-outdoor experience where you can still enjoy the stunning golf course views.</p>
                    
                    <p data-aos="fade-up" data-aos-delay="400">Beyond the magnificent venues, Klub Golf Bogor Raya also provides wedding buffets with diverse menu options including Asian, Western, and Barbeque cuisine.</p>
                    
                    <p data-aos="fade-up" data-aos-delay="500">Delicious food, a spectacular venue, and your beloved family combine to create the perfect once-in-a-lifetime wedding celebration.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Certificates Section -->
<section class="certificates-section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Our Certificates</h2>
        <div class="certificate-wrapper" data-aos="fade-up" data-aos-delay="100">
            <div class="certificate-item">
                <img src="assets/wedding/serti.avif" alt="ISO Certificate">
                <p>ISO Certified Venue</p>
            </div>
        </div>
    </div>
</section>

<!-- Wedding Gallery Section -->
<section class="wedding-section">
    <div class="container-fluid px-2">
        <h2 class="section-title" data-aos="fade-up">Wedding Gallery</h2>
        <div class="gallery-grid">
            <?php foreach($images as $index => $image): 
                $randomSize = rand(1, 3);
                $classes = 'gallery-item';
                switch($randomSize) {
                    case 1: break;
                    case 2: $classes .= ' wide'; break;
                    case 3: $classes .= ' tall'; break;
                }
            ?>
            <div class="<?php echo $classes; ?>" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                <img src="../<?php echo $image['foto']; ?>" alt="Wedding Venue">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>