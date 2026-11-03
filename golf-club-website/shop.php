<?php include 'includes/header.php'; ?>

<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/shop/header.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
        <span class="section-subtitle" style="font-size: 2em; margin-bottom: 100px; margin: 0 auto; color: white;">Golf Shop</span>
    </div>
</header>

<section class="events-intro" style="background-color:rgba(229, 229, 229, 0.95);">
    <div class="container" data-aos="fade-up">
        <h2>Shopping at Klub Golf Bogor Raya</h2>
        <p>Whether you're visiting us in person or online, explore everything Klub Golf Bogor Raya has to offer. From official golf apparel and mementos for all ages, we invite you to explore the many in-store and online shopping experiences awaiting you at Klub Golf Bogor Raya.</p>
    </div>
</section>

<!-- Brands Section -->
<section class="brands-section" style="padding: 4rem 0; background-color: white;">
    <div class="container">
        <span class="section-subtitle" data-aos="fade-up">FEATURED BRANDS</span>
        <div class="brands-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 2rem; margin-top: 2rem; align-items: center;">
            <?php
            $brandsDir = 'assets/brands/';
            $brands = array_merge(
                glob($brandsDir . '*.png'),
                glob($brandsDir . '*.jpg')
            );
            
            foreach ($brands as $key => $brand) {
                $brandName = pathinfo($brand, PATHINFO_FILENAME);
                $brandName = ucfirst($brandName);
                echo '<div class="brand-item" data-aos="zoom-in" data-aos-delay="' . ($key * 100) . '" style="text-align: center; justify-content: center; align-items: center;">';
                echo '<img src="' . $brand . '" alt="' . $brandName . '" style="max-width: 120px; transition: transform 0.3s;">';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<style>
.product-card:hover {
    transform: translateY(-5px);
}
.brand-item img:hover {
    filter: grayscale(0%);
}
.product-card button:hover {
    background: #b8860b;
}
</style>

<?php include 'includes/footer.php'; ?>