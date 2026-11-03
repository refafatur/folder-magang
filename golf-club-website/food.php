<?php include 'includes/header.php'; ?>

<div class="food-page">
<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/food/1.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
        <span class="section-subtitle" style="font-size: 2em; margin-bottom: 100px; margin: 0 auto; color: white;">Food & Beverage</span>
        <p>Exquisite Dining Experience</p>
    </div>
</header>

    <section class="restaurant-intro">
        <div class="container">
            <div class="intro-content" data-aos="fade-up">
                <h2 class="section-title">Traditional Flavors, Experienced Chef</h2>
                <div class="divider"></div>
                <p class="intro-description">
                    Tondano Restaurant is a fine place to dine after golf. Offering traditional authentic Indonesian, Asian, Javanese and Korean dishes will pamper your tongue. With the good quality of material and cooked by professional Chef, makes all our menu be the favorite choice. Tondano’s Oxtail Soup & Gurame Dabu-dabu is our signature dishes and it is recommended menus. 

Not only food, Tondano Restaurant also provides healthy drink which is very suitable for golfer. Juices are made from fresh fruit and vegetables which is good for health, such as Green Garden Juice is made from Cucumber, vegetables, star fruit and lemon.
                </p>
            </div>
        </div>
    </section>

    <section class="dining-services">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up" style="color:#333">Our Menu Gallery</h2>
            <?php
            require_once 'config/database.php';
            
            // Fetch all images regardless of category
            $stmt = $conn->prepare("SELECT * FROM tbl_food");
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            
            <div class="services-list">
                <?php
                while ($row = $result->fetch_assoc()) {
                    $height = rand(300, 600);
                    $width = rand(1, 3);
                    echo '<div class="service-item" data-aos="fade-up" style="height: ' . $height . 'px; grid-column: span ' . $width . ';">';
                    echo '<img src="../' . htmlspecialchars($row['image_path']) . '" alt="Menu Item" loading="lazy">';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <section class="qr-code-section">
        <div class="container">
            <div class="qr-code-wrapper" data-aos="zoom-in">
                <img src="assets/qr.png" alt="Menu QR Code" class="qr-code-image">
                <!-- <a href="tondano-restaurant/index.php" class="qr-link" target="_blank">Menu Tondano Restaurant (Click Here)</a> -->
            </div>
        </div>
    </section>
</div>

<style>
.food-page {
    background-color: #f9f9f9;
    font-family: Arial, sans-serif;
}

.food-hero {
    height: 70vh;
    background-size: cover;
    background-position: center;
    position: relative;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    text-align: center;
}

.hero-overlay h1 {
    font-size: 3.5rem;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-family: Arial, sans-serif;
}

.hero-overlay p {
    font-size: 1.5rem;
    font-weight: 300;
    font-family: Arial, sans-serif;
}

.restaurant-intro {
    padding: 5rem 0;
}

.intro-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.section-title {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 1.5rem;
    font-family: Arial, sans-serif;
}

.divider {
    width: 80px;
    height: 3px;
    background: #gold;
    margin: 1.5rem auto;
}

.intro-description {
    font-size: 1.2rem;
    line-height: 1.8;
    color: #666;
    font-family: Arial, sans-serif;
}

.dining-services {
    padding: 5rem 0;
    background: white;
}

.category-section {
    margin: 3rem 0;
}

.category-section h3 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 2rem;
    text-align: center;
}

.services-list {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-top: 3rem;
    padding: 0 2rem;
    grid-auto-flow: dense;
}

.service-item {
    text-align: center;
    overflow: hidden;
    transition: all 0.3s ease;
}

.service-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.qr-code-section {
    padding: 3rem 0;
    text-align: center;
    background: white;
}

.qr-code-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.qr-code-image {
    width: 200px;
    height: 200px;
    object-fit: contain;
}

.qr-link {
    display: inline-block;
    padding: 10px 20px;
    background-color: #333;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.qr-link:hover {
    background-color: #555;
}

@media (max-width: 768px) {
    .services-list {
        grid-template-columns: 1fr;
        padding: 0 1rem;
    }
    
    .service-item {
        height: 300px !important;
        grid-column: span 1 !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>