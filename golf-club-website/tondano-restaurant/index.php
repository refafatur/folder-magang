<?php 
include 'includes/header.php';
require '../db/config.php';

// Fetch menu items from database
$stmt = $pdo->query("SELECT * FROM tbl_menu");
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<body>
    <!-- Hero Section -->
    <header class="hero">
        <div class="hero-background" data-aos="fade">
            <div class="hero-overlay"></div>
            <img src="../tondano-restaurant/assets/2.avif" alt="Restaurant Background View">
        </div>
        <div class="hero-content" data-aos="fade-up" data-aos-delay="300">
            <div class="hero-logo">
                <img src="../tondano-restaurant/assets/1.avif" alt="Restaurant Logo">
            </div>
        </div>
    </header>

    <!-- About Section -->
    <section id="about" class="section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">About Us</h2>
            <div class="about-content">
                <div class="about-text" data-aos="fade-up" data-aos-delay="100">
                    <p>Located in the prestigious real estate in Bogor and as a part of the best golf course of Indonesia "Klub Golf Bogor Raya", Golf Tondano Terrace is a must visit restaurant for people who want to have different sensation of dining. Surrounded by the golf course makes Golf Tondano Terrace have a beautiful scenery while you have dinner with your family, friends, or colleagues.</p>
                    <p>With the unique building concepts of Javanese style and, we offer you a cozy, relaxing and romantic place. We provide variety menu of Indonesian, Asian, Western, Japanese and Korean dishes in high standard quality.</p>
                    <p>The restaurant also could be functioned as a wedding venue for capacity 1000 pax. Achieving as the Best Wedding Destination in Indonesia on 2017 by Asian Lifestyle Tourism Awards, makes Golf Tondano Terrace is the best choice to have a memorable wedding.</p>
                </div>
            </div>

            <!-- Awards Section -->
            <div class="awards-section">
                <h3 class="award-title" data-aos="fade-up">Our Achievement</h3>
                <div class="award-container" data-aos="fade-up" data-aos-delay="100">
                    <div class="award-item">
                        <img src="../tondano-restaurant/assets/serti.avif" alt="Best Wedding Destination Award">
                        <p class="award-caption">Best Wedding Destination in Indonesia 2017</p>
                        <p class="award-subtitle">Asian Lifestyle Tourism Awards</p>
                    </div>
                </div>
            </div>

            <!-- Find Us Section -->
            <div class="awards-section">
                <h3 class="award-title" data-aos="fade-up">Find Us</h3>
                <div class="award-container" data-aos="fade-up" data-aos-delay="100">
                <div class="award-item">
                    <img src="../tondano-restaurant/assets/trip.avif" style="width: 200px;" alt="Restaurant Location">
                </div>
                <div class="award-item">
                    <img src="../tondano-restaurant/assets/zomato.avif" alt="Restaurant Location">
                </div>
                <div class="award-item">
                    <img src="../tondano-restaurant/assets/yu.avif" alt="Restaurant Location">
                </div>
            </div>

        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Our Menu</h2>
            
            <div class="filter-buttons" data-aos="fade-up" data-aos-delay="100">
                <button class="filter-btn active" data-category="all">All</button>
                <button class="filter-btn" data-category="bestseller">Best Seller</button>
                <button class="filter-btn" data-category="popular">Popular</button>
                <button class="filter-btn" data-category="appetizer">Appetizer</button>
                <button class="filter-btn" data-category="main">Main Course</button>
                <button class="filter-btn" data-category="beverage">Beverage</button>
                <button class="filter-btn" data-category="dessert">Dessert</button>
            </div>

            <div class="menu-grid" data-aos="fade-up" data-aos-delay="200">
                <?php foreach($menuItems as $item): ?>
                <div class="menu-item" 
                     data-category="<?php echo htmlspecialchars($item['category']); ?>" 
                     data-bestseller="<?php echo $item['is_bestseller'] ? 'true' : 'false'; ?>"
                     data-popular="<?php echo $item['is_popular'] ? 'true' : 'false'; ?>"
                     data-aos="fade-up"
                     data-aos-duration="800"
                     data-aos-delay="<?php echo $index * 100; ?>">
                    <?php if($item['is_bestseller'] || $item['is_popular']): ?>
                        <div class="menu-item-badges">
                            <?php if($item['is_bestseller']): ?>
                                <span class="badge bestseller"><i class="fas fa-star"></i> Best Seller</span>
                            <?php endif; ?>
                            <?php if($item['is_popular']): ?>
                                <span class="badge popular"><i class="fas fa-fire"></i> Popular</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="menu-item-image">
                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="menu-item-overlay"></div>
                    </div>
                    <div class="menu-item-content">
                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                        <p class="description"><?php echo htmlspecialchars($item['description']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <style>
                .filter-buttons {
                    display: flex;
                    justify-content: center;
                    gap: 15px;
                    margin-bottom: 30px;
                    flex-wrap: wrap;
                }

                .filter-btn {
                    padding: 10px 20px;
                    border: 2px solid #DAA520;
                    background: transparent;
                    color: white;
                    cursor: pointer;
                    border-radius: 25px;
                    transition: all 0.3s ease;
                }

                .filter-btn.active,
                .filter-btn:hover {
                    background: #DAA520;
                    color: #1a1a1a;
                }

                .menu-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                    gap: 30px;
                    padding: 20px;
                }

                .menu-item {
                    background: #1a1a1a;
                    border-radius: 15px;
                    overflow: hidden;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                    transition: all 0.4s ease;
                    position: relative;
                }

                .menu-item:hover {
                    transform: translateY(-10px);
                    box-shadow: 0 20px 40px rgba(218, 165, 32, 0.2);
                }

                .menu-item-image {
                    position: relative;
                    overflow: hidden;
                    height: 280px;
                }

                .menu-item-image img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.6s ease;
                }

                .menu-item:hover .menu-item-image img {
                    transform: scale(1.1);
                }

                .menu-item-overlay {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    height: 50%;
                    background: linear-gradient(to top, rgba(26, 26, 26, 1), rgba(26, 26, 26, 0));
                }

                .menu-item-content {
                    padding: 20px;
                    position: relative;
                }

                .menu-item-content h4 {
                    color: #DAA520;
                    font-size: 1.4em;
                    font-weight: 500;
                    margin-bottom: 10px;
                    letter-spacing: 1px;
                }

                .menu-item-content .description {
                    color: #999;
                    font-size: 0.95em;
                    line-height: 1.6;
                    margin: 0;
                }

                .menu-item-badges {
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                    z-index: 2;
                }

                .badge {
                    padding: 8px 12px;
                    border-radius: 20px;
                    font-size: 0.85em;
                    font-weight: 500;
                    display: flex;
                    align-items: center;
                    gap: 5px;
                    backdrop-filter: blur(5px);
                    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                }

                .badge.bestseller {
                    background: rgba(255, 215, 0, 0.9);
                    color: #000;
                }

                .badge.popular {
                    background: rgba(255, 69, 0, 0.9);
                    color: #fff;
                }

                .badge i {
                    font-size: 0.9em;
                }

                .hero {
                    width: 100%;
                    height: 100vh;
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    overflow: hidden;
                }

                .hero-background {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                }

                .hero-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(
                        rgba(0,0,0,0.5),
                        rgba(0,0,0,0.3) 40%,
                        rgba(0,0,0,0.7)
                    );
                    z-index: 1;
                }

                .hero-background img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                .hero-content {
                    position: relative;
                    z-index: 2;
                    text-align: center;
                    padding: 20px;
                }

                .hero-logo {
                    max-width: 400px;
                    margin: 0 auto;
                }

                .hero-logo img {
                    width: 100%;
                    height: auto;
                }

                @media (max-width: 768px) {
                    .menu-grid {
                        grid-template-columns: repeat(2, 1fr);
                        gap: 15px;
                        padding: 10px;
                    }

                    .hero {
                        height: 70vh;
                    }

                    .about-text p {
                        font-size: 0.95em;
                        line-height: 1.6;
                        padding: 0 15px;
                    }

                    .section {
                        padding: 60px 0;
                    }

                    .section-title {
                        font-size: 1.6em;
                        margin-bottom: 35px;
                    }

                    .filter-buttons {
                        padding: 0 10px;
                        margin-bottom: 25px;
                        overflow-x: auto;
                        -webkit-overflow-scrolling: touch;
                        scrollbar-width: none;
                        white-space: nowrap;
                        display: flex;
                        justify-content: flex-start;
                    }

                    .filter-buttons::-webkit-scrollbar {
                        display: none;
                    }

                    .filter-btn {
                        padding: 8px 16px;
                        font-size: 0.85em;
                        flex: 0 0 auto;
                    }

                    .menu-item-image {
                        height: 200px;
                    }

                    .menu-item-content {
                        padding: 15px;
                    }

                    .menu-item-content h4 {
                        font-size: 1.1em;
                    }

                    .award-container {
                        padding: 0 15px;
                    }

                    .award-item {
                        max-width: 45%;
                    }

                    .award-caption {
                        font-size: 1em;
                    }

                    .location-info h3 {
                        font-size: 1.3em;
                    }

                    .location-info p {
                        font-size: 0.95em;
                        margin: 10px 0;
                    }

                    .map iframe {
                        height: 300px;
                    }
                }

                @media (max-width: 480px) {
                    .menu-grid {
                        grid-template-columns: 1fr;
                        gap: 20px;
                    }

                    .hero {
                        height: 60vh;
                    }

                    .hero-logo {
                        max-width: 180px;
                    }

                    .section {
                        padding: 40px 0;
                    }

                    .filter-btn {
                        padding: 6px 14px;
                        font-size: 0.8em;
                    }

                    .menu-item-image {
                        height: 180px;
                    }

                    .menu-item-content h4 {
                        font-size: 1em;
                    }

                    .menu-item-content .description {
                        font-size: 0.85em;
                    }

                    .award-item {
                        max-width: 100%;
                        margin-bottom: 20px;
                    }

                    .award-item img {
                        width: 80%;
                        margin: 0 auto;
                    }

                    .badge {
                        padding: 4px 8px;
                        font-size: 0.7em;
                    }

                    .container {
                        padding: 0 10px;
                    }
                }

                .section {
                    width: 100%;
                    padding: 100px 0;
                    background-color: #121212;
                }

                .section-title {
                    font-weight: 300;
                    letter-spacing: 3px;
                    margin-bottom: 60px;
                    text-align: center;
                    color: #DAA520;
                }

                .about-content {
                    display: grid;
                    grid-template-columns: 1fr;
                    text-align: center;
                    max-width: 800px;
                    margin: 0 auto;
                }

                .about-text {
                    width: 100%;
                    text-align: center;
                }

                .about-text h3 {
                    font-weight: 300;
                    letter-spacing: 2px;
                    margin-bottom: 20px;
                    color: #DAA520;
                }

                .menu-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                    gap: 40px;
                    padding: 40px 20px;
                }

                .menu-item {
                    background: #1a1a1a;
                    border-radius: 15px;
                    overflow: hidden;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                    transition: transform 0.3s ease;
                }

                .menu-item:hover {
                    transform: translateY(-10px);
                }

                .menu-item img {
                    height: 250px;
                    transition: transform 0.3s ease;
                }

                .menu-item:hover img {
                    transform: scale(1.05);
                }

                .menu-item h4 {
                    font-size: 1.2em;
                    font-weight: 400;
                    letter-spacing: 1px;
                }

                .filter-buttons {
                    margin-bottom: 40px;
                }

                .filter-btn {
                    padding: 12px 25px;
                    border-radius: 25px;
                    font-size: 0.9em;
                    letter-spacing: 1px;
                    transition: all 0.3s ease;
                    text-transform: uppercase;
                }

                .location-content {
                    display: grid;
                    grid-template-columns: 2fr 1fr;
                    gap: 40px;
                    align-items: start;
                }

                .location-info {
                    padding: 30px;
                    border-radius: 15px;
                }

                .location-info p {
                    margin: 15px 0;
                    font-size: 1.1em;
                }

                .location-info i {
                    margin-right: 10px;
                    color: #DAA520;
                }

                .awards-section {
                    margin-top: 60px;
                    text-align: center;
                }

                .award-title {
                    color: #DAA520;
                    font-size: 1.8em;
                    margin-bottom: 30px;
                    font-weight: 300;
                    letter-spacing: 2px;
                }

                .award-container {
                    display: flex;
                    justify-content: center;
                    gap: 30px;
                    flex-wrap: wrap;
                }

                .award-item {
                    max-width: 300px;
                }

                .award-item img {
                    width: 100%;
                    height: auto;
                    border-radius: 10px;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
                    transition: transform 0.3s ease;
                }

                .award-item img:hover {
                    transform: scale(1.05);
                }

                .award-caption {
                    color: #DAA520;
                    font-size: 1.2em;
                    margin: 15px 0 5px;
                }

                .award-subtitle {
                    color: #888;
                    font-size: 0.9em;
                }

                .find-us-section {
                    margin-top: 60px;
                    text-align: center;
                    padding: 40px 20px;
                    background-color: rgba(218, 165, 32, 0.1);
                    border-radius: 15px;
                }

                .find-us-title {
                    color: #DAA520;
                    font-size: 1.8em;
                    margin-bottom: 30px;
                    font-weight: 300;
                    letter-spacing: 2px;
                }

                .find-us-content {
                    display: flex;
                    justify-content: center;
                    gap: 50px;
                    flex-wrap: wrap;
                }

                .find-us-text {
                    color: #fff;
                    font-size: 1.1em;
                    line-height: 1.8;
                }

                .find-us-text i {
                    color: #DAA520;
                    margin-right: 10px;
                    font-size: 1.2em;
                }

                @media (max-width: 768px) {
                    .about-content,
                    .location-content {
                        grid-template-columns: 1fr;
                    }
                    
                    .hero-content h1 {
                        font-size: 2.5em;
                    }
                    
                    .menu-grid {
                        grid-template-columns: 1fr;
                        padding: 20px 10px;
                    }

                    .filter-buttons {
                        gap: 10px;
                        padding: 0 10px;
                    }

                    .filter-btn {
                        padding: 8px 15px;
                        font-size: 0.8em;
                    }

                    .award-container {
                        gap: 15px;
                        padding: 0 10px;
                    }

                    .award-item {
                        max-width: 100%;
                    }

                    .award-item img {
                        width: 80%;
                    }

                    .location-content {
                        grid-template-columns: 1fr;
                    }

                    .map iframe {
                        width: 100%;
                        height: 300px;
                    }

                    .location-info {
                        padding: 20px 10px;
                    }

                    .about-text {
                        padding: 0 15px;
                    }

                    .section {
                        padding: 50px 0;
                    }

                    .section-title {
                        font-size: 1.8em;
                        margin-bottom: 30px;
                    }

                    .container {
                        padding: 0 15px;
                    }

                    .menu-item-image {
                        height: 200px;
                    }

                    .menu-item-content h4 {
                        font-size: 1.2em;
                    }

                    .hero {
                        height: 60vh; /* Kurangi tinggi pada mobile */
                    }

                    .hero-background img {
                        object-fit: cover; /* Ubah ke cover untuk tampilan lebih baik di mobile */
                    }

                    .hero-left {
                        width: 100%;
                        margin: 0;
                        padding: 20px;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
                    }

                    .hero-left img {
                        width: 200px; /* Tetapkan ukuran spesifik untuk mobile */
                        height: auto;
                        object-fit: contain;
                    }
                }

                @media (max-width: 480px) {
                    .hero {
                        height: 50vh;
                    }

                    .hero-left {
                        padding: 15px;
                    }

                    .hero-left img {
                        width: 150px; /* Lebih kecil untuk device yang sangat kecil */
                    }

                    .badge {
                        padding: 6px 10px;
                        font-size: 0.75em;
                    }
                }

                /* Fix untuk menu grid responsive */
                .menu-grid {
                    width: 100%;
                    max-width: 100%;
                    margin: 0 auto;
                    overflow: hidden;
                }

                /* Fix untuk filter buttons */
                .filter-buttons {
                    width: 100%;
                    max-width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    scrollbar-width: none;
                    margin: 0 auto;
                    padding: 0 15px;
                }

                .filter-buttons::-webkit-scrollbar {
                    display: none;
                }

                /* Fix untuk award container */
                .award-container {
                    width: 100%;
                    max-width: 100%;
                    overflow: hidden;
                    padding: 0 15px;
                }

                @media (max-width: 768px) {
                    .menu-item {
                        width: 100%;
                    }

                    .menu-item-image img {
                        width: 100%;
                        object-fit: cover;
                    }

                    .award-item img {
                        max-width: 100%;
                    }
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const filterBtns = document.querySelectorAll('.filter-btn');
                    const menuItems = document.querySelectorAll('.menu-item');
                    let animationTimeout;

                    // Helper function untuk menampilkan item dengan animasi
                    const showItemWithAnimation = (item, index) => {
                        item.style.transition = 'none';
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(20px)';
                        item.style.display = 'block';

                        // Menambah delay berdasarkan index untuk efek stagger
                        setTimeout(() => {
                            item.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                            item.style.opacity = '1';
                            item.style.transform = 'translateY(0)';
                        }, index * 50); // Stagger 50ms per item
                    };

                    // Helper function untuk menyembunyikan item
                    const hideItem = (item) => {
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(10px)';
                        
                        setTimeout(() => {
                            item.style.display = 'none';
                        }, 300);
                    };

                    // Function untuk filter items
                    const filterItems = (category) => {
                        // Clear previous animation timeout
                        if (animationTimeout) {
                            clearTimeout(animationTimeout);
                        }

                        const itemsToShow = [];
                        menuItems.forEach(item => {
                            if (
                                category === 'all' || 
                                (category === 'bestseller' && item.dataset.bestseller === 'true') ||
                                (category === 'popular' && item.dataset.popular === 'true') ||
                                item.dataset.category === category
                            ) {
                                itemsToShow.push(item);
                            } else {
                                hideItem(item);
                            }
                        });

                        // Animate visible items with stagger
                        itemsToShow.forEach((item, index) => {
                            showItemWithAnimation(item, index);
                        });
                    };

                    // Event listener untuk filter buttons
                    filterBtns.forEach(btn => {
                        btn.addEventListener('click', () => {
                            const category = btn.dataset.category;
                            
                            filterBtns.forEach(b => b.classList.remove('active'));
                            btn.classList.add('active');
                            
                            filterItems(category);
                        });
                    });

                    // Initialize with 'all' category
                    filterItems('all');
                });
            </script>
    </section>

    <!-- Location Section -->
    <section id="location" class="section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Location</h2>
            <div class="location-content">
                <div class="map" data-aos="fade-right">
                    <!-- Add your Google Maps iframe here -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.391602577918!2d106.8337526745363!3d-6.598157664499091!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c7e563ec1ad9%3A0x18c5524b87f2ac3d!2sTondano%20Restaurant!5e0!3m2!1sid!2sid!4v1749614459941!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>      
                </div>
                <div class="location-info" data-aos="fade-left">
                    <h3>Find Us</h3>
                    <p><i class="fas fa-map-marker-alt"></i>Tondano Restaurant</p>
                    <p><i class="fas fa-phone"></i> +62 123 456 789</p>
                    <p><i class="fas fa-envelope"></i> info@tondano.com</p>
                </div>
            </div>
        </div>
    </section>

    <style>
    .hero {
        width: 100%;
        height: 100vh;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            rgba(0,0,0,0.5),
            rgba(0,0,0,0.3) 40%,
            rgba(0,0,0,0.7)
        );
        z-index: 1;
    }

    .hero-background img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        padding: 20px;
    }

    .hero-logo {
        max-width: 400px;
        margin: 0 auto;
    }

    .hero-logo img {
        width: 100%;
        height: auto;
    }

    @media (max-width: 768px) {
        .menu-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            padding: 10px;
        }

        .hero {
            height: 70vh;
        }

        .about-text p {
            font-size: 0.95em;
            line-height: 1.6;
            padding: 0 15px;
        }

        .section {
            padding: 60px 0;
        }

        .section-title {
            font-size: 1.6em;
            margin-bottom: 35px;
        }

        .filter-buttons {
            padding: 0 10px;
            margin-bottom: 25px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            white-space: nowrap;
            display: flex;
            justify-content: flex-start;
        }

        .filter-buttons::-webkit-scrollbar {
            display: none;
        }

        .filter-btn {
            padding: 8px 16px;
            font-size: 0.85em;
            flex: 0 0 auto;
        }

        .menu-item-image {
            height: 200px;
        }

        .menu-item-content {
            padding: 15px;
        }

        .menu-item-content h4 {
            font-size: 1.1em;
        }

        .award-container {
            padding: 0 15px;
        }

        .award-item {
            max-width: 45%;
        }

        .award-caption {
            font-size: 1em;
        }

        .location-info h3 {
            font-size: 1.3em;
        }

        .location-info p {
            font-size: 0.95em;
            margin: 10px 0;
        }

        .map iframe {
            height: 300px;
        }
    }

    @media (max-width: 480px) {
        .menu-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .hero {
            height: 60vh;
        }

        .hero-logo {
            max-width: 180px;
        }

        .section {
            padding: 40px 0;
        }

        .filter-btn {
            padding: 6px 14px;
            font-size: 0.8em;
        }

        .menu-item-image {
            height: 180px;
        }

        .menu-item-content h4 {
            font-size: 1em;
        }

        .menu-item-content .description {
            font-size: 0.85em;
        }

        .award-item {
            max-width: 100%;
            margin-bottom: 20px;
        }

        .award-item img {
            width: 80%;
            margin: 0 auto;
        }

        .badge {
            padding: 4px 8px;
            font-size: 0.7em;
        }

        .container {
            padding: 0 10px;
        }
    }

    .section {
        width: 100%;
        padding: 100px 0;
        background-color: #121212;
    }

    /* Hapus style duplikat lainnya dan biarkan hanya yang di atas */
    </style>

    <!-- Inisialisasi AOS di sini, sebelum closing body tag -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                easing: 'ease-out',
                once: false,
                offset: 50,
                delay: 200,
                mirror: true,
                anchorPlacement: 'top-bottom'
            });

            // Refresh AOS ketika filtering
            const refreshAOS = () => {
                setTimeout(() => {
                    AOS.refresh();
                }, 300);
            };

            // Tambahkan refreshAOS di setiap fungsi filtering
            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const category = btn.dataset.category;
                    
                    filterBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    menuItems.forEach(item => {
                        if (category === 'all' || 
                            (category === 'bestseller' && item.dataset.bestseller === 'true') ||
                            (category === 'popular' && item.dataset.popular === 'true') ||
                            item.dataset.category === category) {
                            showItemWithAnimation(item);
                        } else {
                            hideItem(item);
                        }
                    });
                    refreshAOS();
                });
            });
        });
    </script>
</body>
<?php include 'includes/footer.php'; ?>
