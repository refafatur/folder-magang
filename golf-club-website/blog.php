<?php include 'includes/header.php'; ?>

<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/photos/course.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
        <span class="section-subtitle" style="font-size: 2em; margin-bottom: 100px; margin: 0 auto; color: white;">Blog</span>
    </div>
</header>

<main class="blog-page">
    <div class="blog-wrapper">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-subtitle">Latest Articles</span>
                <h2 class="section-title">Golf News & Tips</h2>
            </div>

            <div class="blog-grid">
                <!-- Featured Post -->
                <article class="blog-card featured" data-aos="fade-up" data-aos-delay="100">
                    <div class="blog-image">
                        <img src="assets/photos/gunung.avif" alt="Featured Blog Post">
                        <div class="blog-category">Featured</div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="far fa-calendar"></i> June 15, 2023</span>
                            <span><i class="far fa-user"></i> John Doe</span>
                        </div>
                        <h3>Mastering Your Golf Swing: Expert Tips for Beginners</h3>
                        <p>Learn the fundamental techniques to improve your golf swing and take your game to the next level with these expert tips...</p>
                        <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>

                <!-- Regular Posts -->
                <article class="blog-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="blog-image">
                        <img src="assets/photos/gunung.avif" alt="Blog Post">
                        <div class="blog-category">Tips</div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="far fa-calendar"></i> June 10, 2023</span>
                            <span><i class="far fa-user"></i> Jane Smith</span>
                        </div>
                        <h3>Essential Golf Equipment for Beginners</h3>
                        <p>A comprehensive guide to selecting the right golf equipment as a beginner...</p>
                        <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="blog-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="blog-image">
                        <img src="assets/photos/gunung.avif" alt="Blog Post">
                        <div class="blog-category">News</div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="far fa-calendar"></i> June 5, 2023</span>
                            <span><i class="far fa-user"></i> Mike Johnson</span>
                        </div>
                        <h3>Upcoming Golf Tournament Schedule</h3>
                        <p>Stay updated with our upcoming tournament schedule and registration details...</p>
                        <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="blog-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="blog-image">
                        <img src="assets/photos/gunung.avif" alt="Blog Post">
                        <div class="blog-category">Course</div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="far fa-calendar"></i> June 1, 2023</span>
                            <span><i class="far fa-user"></i> Sarah Williams</span>
                        </div>
                        <h3>Course Maintenance Updates</h3>
                        <p>Latest updates on our course maintenance schedule and improvements...</p>
                        <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
            </div>

            <div class="pagination" data-aos="fade-up" data-aos-delay="500">
                <a href="#" class="page-link active">1</a>
                <a href="#" class="page-link">2</a>
                <a href="#" class="page-link">3</a>
                <span class="page-dots">...</span>
                <a href="#" class="page-link">10</a>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>