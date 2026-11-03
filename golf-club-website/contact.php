<?php include 'includes/header.php'; ?>

<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/photos/course.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
        <span class="section-subtitle" style="font-size: 2em; margin-bottom: 100px; margin: 0 auto; color: white;">Contact Us</span>
    </div>
</header>

<main class="about-page">
    <section class="partnerships">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-subtitle">Our Group</span>
                <h2 class="section-title">Company Group</h2>
            </div>

            <div class="partner-logo" data-aos="zoom-in" data-aos-delay="100">
                <img src="assets/photos/Logo Master Boray Residential.avif" alt="Company Group Logo">
            </div>

            <div class="partners-grid">
                <div class="partner-logo" data-aos="zoom-in" data-aos-delay="200">
                    <img src="assets/photos/Logo Master KGBR.avif" alt="Partner 1">
                </div>
                <div class="partner-logo" data-aos="zoom-in" data-aos-delay="300">
                    <img src="assets/photos/Logo_2015.avif" alt="Partner 3">
                </div>
                <div class="partner-logo" data-aos="zoom-in" data-aos-delay="400">
                    <img src="partners/ibis.png" alt="Partner 4">
                </div>
            </div>
        </div>
    </section>

    <style>
        .contact-wrapper {
            padding: 6rem 0;
            background: linear-gradient(to bottom, #f8fafc 0%, #ffffff 100%);
        }

        .contact-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .contact-title {
            text-align: center;
            font-size: 2.5rem;
            color: #1e293b;
            margin-bottom: 3rem;
            font-weight: 700;
            position: relative;
        }

        .contact-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background:goldenrod;
            border-radius: 4px;
        }

        .social-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .social-card {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .social-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(37,99,235,0.1);
        }

        .social-icon {
            width: 70px;
            height: 70px;
            background: #e0e7ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            transition: all 0.3s ease;
        }

        .social-icon i {
            font-size: 1.8rem;
            color: goldenrod;
        }

        .social-card:hover .social-icon {
            background:goldenrod;
        }

        .social-card:hover .social-icon i {
            color: white;
        }

        .social-name {
            font-weight: 600;
            color: #1e293b;
            margin-top: 1rem;
        }

        .map-container {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }

        .map-title {
            font-size: 1.8rem;
            color: #1e293b;
            margin-bottom: 2rem;
            font-weight: 600;
        }

        .map-frame {
            width: 100%;
            height: 500px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        @media (max-width: 992px) {
            .social-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .social-grid {
                grid-template-columns: 1fr;
            }
            .map-frame {
                height: 350px;
            }
        }
    </style>

    <div class="contact-wrapper">
        <div class="contact-section">
            <div class="section-header" data-aos="fade-up">
                <span class="section-subtitle">Stay Connected</span>
                <h2 class="section-title">Connect With Us</h2>
            </div>
            
            <div class="social-grid" style="grid-template-columns: repeat(3, 1fr);">
                <a href="https://www.instagram.com/klubgolfbogorraya" class="social-card" data-aos="fade-up" data-aos-delay="100" target="_blank">
                    <div class="social-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <div class="social-name">Instagram</div>
                </a>
                
                <a href="https://www.youtube.com/channel/UCSiTPaR3GgW_p6i7iGy3YZQ" class="social-card" data-aos="fade-up" data-aos-delay="200" target="_blank">
                    <div class="social-icon">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <div class="social-name">YouTube</div>
                </a>
                
                <a href="https://www.tiktok.com/@klubgolfbogorraya" class="social-card" data-aos="fade-up" data-aos-delay="300" target="_blank">
                    <div class="social-icon">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <div class="social-name">TikTok</div>
                </a>

                <a href="https://www.facebook.com/Klub-Golf-Bogor-Raya-112346780272299/?__tn__=%2Cd%2CP-R&eid=ARAB0zZvDjIL-gl56EhPbMDOz-bP3tZmS2YqOIEQbK_suDpZrStlgrXQoLLJmQ7LfGk-bgjf8aKs5uwA" class="social-card" data-aos="fade-up" data-aos-delay="400" target="_blank">
                    <div class="social-icon">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <div class="social-name">Facebook</div>
                </a>
                
                <a href="https://twitter.com/golfbogorraya" class="social-card" data-aos="fade-up" data-aos-delay="500" target="_blank">
                    <div class="social-icon">
                        <i class="fab fa-twitter"></i>
                    </div>
                    <div class="social-name">Twitter</div>
                </a>

                <a href="https://www.linkedin.com/company/28742982/admin/" class="social-card" data-aos="fade-up" data-aos-delay="600" target="_blank">
                    <div class="social-icon">
                        <i class="fab fa-linkedin-in"></i>
                    </div>
                    <div class="social-name">LinkedIn</div>
                </a>
            </div>

            <div class="map-container" data-aos="fade-up" data-aos-delay="500">
                <div class="section-header">
                    <span class="section-subtitle">Find Us</span>
                    <h2 class="section-title">Our Location</h2>
                </div>
                <iframe class="map-frame"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.389127663056!2d106.83364647453631!3d-6.598466964502125!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c66bf0d0549b%3A0xacf4c6f2da35672f!2sKlub%20Golf%20Bogor%20Raya!5e0!3m2!1sid!2sus!4v1749613183159!5m2!1sid!2sus" 
                width="600" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

