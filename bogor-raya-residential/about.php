<?php
$conn = mysqli_connect("localhost", "root", "", "db_bogor");

// Add visitor tracking
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$page = $_SERVER['PHP_SELF'];
$current_time = date('Y-m-d H:i:s');

$query = "INSERT INTO tbl_visitors (ip_address, user_agent, visit_time, page) 
          VALUES ('$ip_address', '$user_agent', '$current_time', '$page')";
mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Bogor Raya Residential</title>
    <meta name="description" content="Official Marketing Gallery Bogor Raya Development">
    <meta property="og:image" content="https://images.unsplash.com/photo-1613977257363-707ba9348227">
    <meta property="og:type" content="website">
    <meta property="og:description" content="Website resmi kantor pemasaran perumahan cluster Bogor Raya Development">
    <link rel="icon" type="image/png" sizes="1181x430" href="assets/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Navbar Styling */
        .navbar {
        background: transparent;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: background 0.3s ease-in-out;
        }

        .navbar.scrolled {
        background: rgba(0, 0, 0, 0.9);
        }

        .navbar-dark .navbar-brand {
        font-size: 1.5rem;
        font-weight: bold;
        color: #ffffff;
        transition: color 0.3s ease-in-out;
        }

        .navbar-dark .navbar-brand:hover {
        color: #f8b400;
        }

        .navbar-dark .navbar-nav .nav-link {
        font-size: 1rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.8);
        transition: color 0.3s ease-in-out;
        padding: 10px 15px;
        }

        .navbar-dark .navbar-nav .nav-link:hover {
        color: #f8b400;
        }

        .navbar-toggler {
        border: none;
        outline: none;
        color: #ffffff;
        }

        .navbar-toggler:focus,
        .navbar-toggler:active {
        box-shadow: none;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
        .navbar-collapse {
        background: rgba(0, 0, 0, 0.9);
        padding: 10px;
        border-radius: 10px;
    }
    .navbar-nav .nav-item {
        text-align: center;
        padding: 5px 0;
    }
}


        .hero-section {
        min-height: 100vh;
        background: linear-gradient(rgba(96, 96, 96, 0.7), rgba(199, 199, 199, 0.7)), 
              url('assets/rumah.png') center/cover no-repeat;
        display: flex;
        align-items: center;
        position: relative;
        }

        .hero-content {
            padding: 200px 0;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .section-title {
            font-weight: 600;
            position: relative;
            padding-bottom: 20px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 3px;
            background: #28a745;
            transition: width 0.3s;
        }

        .section-title:hover::after {
            width: 100px;
        }

        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .product-img {
            height: 250px;
            object-fit: cover;
        }

        .product-content {
            padding: 20px;
            background: white;
        }

        .product-title {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .carousel-item {
            height: 600px;
        }

        .carousel-img {
            height: 100%;
            object-fit: cover;
        }

        .feature-icon {
            font-size: 2rem;
            color: #28a745;
            margin-bottom: 15px;
        }

        .footer {
            background: #1a1a1a;
            color: white;
            padding: 60px 0 30px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #28a745;
            color: white;
            border-radius: 50%;
            margin: 0 5px;
            transition: all 0.3s;
        }

        .social-links a:hover {
            transform: translateY(-3px);
            background: #218838;
        }
        @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.5);
        }
        70% {
            transform: scale(1.1);
            box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
        }
    }

    .btn-success:hover {
        transform: scale(1.1);
        background-color: #20ba5a !important;
        box-shadow: 2px 2px 11px rgba(0,0,0,0.7);
    }
    </style>
</head>

<body>
    <div class="page-wrapper" data-bs-spy="scroll" data-bs-target="#mainNav" data-bs-offset="100">
        <nav class="navbar navbar-dark navbar-expand-lg fixed-top bg-dark" id="mainNav">
            <div class="container">
                <a class="navbar-brand" href="index.php" style="font-family: 'Alegreya Sans SC', sans-serif;">
                    <img src="assets/logo.png" alt="Logo" style="height: 40px; margin-right: 10px;">
                    Bogor Raya Residential
                </a>
                <button data-bs-toggle="collapse" data-bs-target="#navbarResponsive" class="navbar-toggler navbar-toggler-right" type="button" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto text-uppercase">
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                        <li class="nav-item"><a href="about.php" class="nav-link">About</a></li>
                        <li class="nav-item"><a href="np.php"class="nav-link">News &amp; Promotion</a></li>
                        <li class="nav-item"><a href="produk.php"class="nav-link">Products</a></li>
                        <li class="nav-item"><a href="contact.php"class="nav-link">Order</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <section class="hero-section">
            <div class="container hero-content text-center text-white">
                <img src="assets/logo-2.png" alt="Logo" class="animate__animated animate__fadeIn" style="height: 120px; margin-bottom: 30px;">
            </div>
        </section>

<div class="about-section py-5">
    <div class="container">
        <!-- Company Story Section -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-10">
                <h2 class="text-center mb-4 section-title animate__animated animate__fadeInDown">Our Story</h2>
                <div class="card shadow-lg border-0 p-4" style="border-radius: 15px; background: rgba(255, 255, 255, 0.95);">
                    <p class="lead" style="text-align: justify; line-height: 1.8;"></p>
                        PT Bogor Raya Residential pertama kali didirikan pada tanggal 12 Juli, 1990. Bogor Raya Residential adalah perusahaan properti dengan bidang usaha property keseluruhan terdiri lebih dari 500 hektar.
                    </p>
                    <p style="text-align: justify; line-height: 1.8;"></p></p>
                        Proyek prestisius pembangunan diawali dengan Perumahan Danau Bogor Raya, sebuah kawa</p>san pemukiman ekslusif bernuansa hijau dengan pemandangan Gunung Salak dengan luas area pemukiman seluas 80 hektar. Area yang mendukung di sekitar Bogor Raya terdiri dari:
                    </p>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><i class="fas fa-hotel text-success me-2"></i> Resort & Convention Hotel (Novotel Coralia Bogor)</li>
                                <li class="list-group-item"><i class="fas fa-golf-ball text-success me-2"></i> Golf Course & Club House (Klub Golf Bogor Raya)</li>
                                <li class="list-group-item"><i class="fas fa-home text-success me-2"></i> Golf Estate & Town House</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><i class="fas fa-running text-success me-2"></i> Sport & Recreation Centre</li>
                                <li class="list-group-item"><i class="fas fa-school text-success me-2"></i> Sekolah Bogor Raya (Nasional Plus)</li>
                                <li class="list-group-item"><i class="fas fa-clock text-success me-2"></i> Event Venues</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Section -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-10">
                <h2 class="text-center mb-4 section-title animate__animated animate__fadeInDown">Our Location</h2>
                <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 15px;">
                    <a href="https://goo.gl/maps/Ft98EBCaaFQXSXFA9" target="_blank" class="d-block position-relative">
                        <div class="map-container" style="height: 400px;">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.4731407921754!2d106.80388661455781!3d-6.591670766277565!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c5c1a7ed7c29%3A0x6b7d6cd73147be4b!2sBogor%20Raya%20Golf%20Estate!5e0!3m2!1sen!2sid!4v1645837074772!5m2!1sen!2sid"
                                width="100%" 
                                height="100%" 
                                style="border: 0;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
                        </div>
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <div class="btn btn-success btn-lg shadow-lg">
                                <i class="fas fa-map-marker-alt me-2"></i>View on Google Maps
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Testimonials Section -->
    <div class="container py-5">
    <h2 class="text-center mb-5 section-title animate__animated animate__fadeInDown">What Our Clients Say</h2>
    <div class="testimonial-wrapper overflow-hidden">
        <div id="testimonialCarousel" class="d-flex">
            <?php
            $result = mysqli_query($conn, "SELECT * FROM tbl_testimoni");
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="testimonial-item text-center p-4 mx-3" style="min-width: 300px; max-width: 300px; background: #ffffff; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <img src="bogor-raya-residential/<?php echo $row['foto']; ?>" class="rounded-circle mb-3 shadow-lg" alt="<?php echo $row['nama']; ?>" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #ddd;">
                    <h5 class="text-dark fw-bold mb-2"><?php echo $row['nama']; ?></h5>
                    <p class="text-muted fst-italic">"<?php echo $row['pesan']; ?>"</p>
                    <div class="star-rating">
                        <?php for ($i = 0; $i < 5; $i++) {
                            echo $i < $row['rate'] ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>';
                        } ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<style>
    .testimonial-wrapper {
        position: relative;
        width: 100%;
        white-space: nowrap;
    }
    #testimonialCarousel {
        display: flex;
        gap: 10px;
        animation: scroll 20s linear infinite;
    }
    @keyframes scroll {
        from { transform: translateX(0); }
        to { transform: translateX(-100%); }
    }
</style>

   </div>
</div>

    <!-- Floating Bubbles Container -->
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <!-- Social Media Bubbles (Initially Hidden) -->
        <div id="socialBubbles" style="display: none;">
            <!-- WhatsApp -->
            <a href="https://wa.me/628119991660?text=Hai%20cluster%20apa%20saja%20yang%20ready?" target="_blank" 
               class="btn btn-success rounded-circle p-3 mb-2" 
               style="width: 60px; 
                      height: 60px;
                      background-color: #25d366;
                      box-shadow: 2px 2px 6px rgba(0,0,0,0.4);
                      transition: all 0.3s ease;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      animation: pulse 2s infinite;">
                <i class="fab fa-whatsapp fa-2x"></i>
            </a>

            <!-- Instagram -->
            <a href="https://www.instagram.com/bogorrayaresidential/?hl=en" target="_blank" 
               class="btn btn-success rounded-circle p-3 mb-2" 
               style="width: 60px; 
                      height: 60px;
                      background-color: #E1306C;
                      box-shadow: 2px 2px 6px rgba(0,0,0,0.4);
                      transition: all 0.3s ease;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      animation: pulse 2s infinite;">
                <i class="fab fa-instagram fa-2x"></i>
            </a>

            <!-- YouTube -->
            <a href="https://www.youtube.com/channel/UCY3Hye3uRgLBpGFsv65Bogw" target="_blank" 
               class="btn btn-success rounded-circle p-3" 
               style="width: 60px; 
                      height: 60px;
                      background-color: #FF0000;
                      box-shadow: 2px 2px 6px rgba(0,0,0,0.4);
                      transition: all 0.3s ease;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      animation: pulse 2s infinite;">
                <i class="fab fa-youtube fa-2x"></i>
            </a>
        </div>

        <!-- Toggle Button -->
        <button onclick="toggleSocialBubbles()" 
                class="btn btn-success rounded-circle p-3" 
                style="width: 60px; 
                       height: 60px;
                       background-color: #28a745;
                       box-shadow: 2px 2px 6px rgba(0,0,0,0.4);
                       transition: all 0.3s ease;
                       display: flex;
                       align-items: center;
                       justify-content: center;
                       animation: pulse 2s infinite;">
            <i class="fas fa-home fa-2x"></i>
        </button>
    </div>

    <script>
        function toggleSocialBubbles() {
            const bubbles = document.getElementById('socialBubbles');
            bubbles.style.display = bubbles.style.display === 'none' ? 'block' : 'none';
        }
    </script>


<footer class="footer bg-dark py-5">
            <div class="container">
                <!-- Main Footer Content -->
                <div class="row mb-4">
                    <!-- Company Info -->
                    <div class="col-lg-4 mb-4">
                        <h4 class="text-white mb-4">Bogor Raya Residential</h4>
                        <div class="footer-contact">
                            <p class="text-light mb-3">
                                <i class="fas fa-map-marker-alt text-success me-2"></i>
                                Jl. Raya Pajajaran No.33 C-D, RT.04/RW.11, Baranangsiang, Kec. Bogor Tim., Kota Bogor, Jawa Barat 16143
                            </p>
                            <p class="text-light mb-3">
                                <i class="fas fa-phone text-success me-2"></i>
                                (+62)251 8361000
                            </p>
                            <p class="text-light mb-3">
                                <i class="far fa-clock text-success me-2"></i>
                                Open Daily: 08:00 - 17:00 WIB
                            </p>
                        </div>
                    </div>

                    <!-- Clusters -->
                    <div class="col-lg-4 mb-4">
                        <h4 class="text-white mb-4">Our Clusters</h4>
                        <div class="d-flex flex-column">
                            <a href="https://bogorraya.co.id/residential/trpdf.html" class="text-light text-decoration-none mb-2 hover-success" target="_blank">
                                <i class="fas fa-chevron-right text-success me-2"></i>Terracotta 3
                            </a>
                            <a href="https://bogorraya.co.id/residential/trpdf.html" class="text-light text-decoration-none mb-2 hover-success">
                                <i class="fas fa-chevron-right text-success me-2"></i>Terracotta Insert
                            </a>
                            <a href="https://bogorraya.co.id/residential/kppdf.html" class="text-light text-decoration-none mb-2 hover-success">
                                <i class="fas fa-chevron-right text-success me-2"></i>Kemang Permata Neo
                            </a>
                            <a href="https://bogorraya.co.id/residential/kppdf.html" class="text-light text-decoration-none mb-2 hover-success">
                                <i class="fas fa-chevron-right text-success me-2"></i>Kemang Permata Premiere
                            </a>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="col-lg-4 mb-4">
                        <h4 class="text-white mb-4">Connect With Us</h4>
                        <div class="social-links">
                            <a href="https://www.instagram.com/bogorrayaresidential/?hl=en" class="btn btn-success btn-sm rounded-circle me-2 mb-2">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.facebook.com/bogorrayaproperty/" class="btn btn-success btn-sm rounded-circle me-2 mb-2">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://www.youtube.com/channel/UCY3Hye3uRgLBpGFsv65Bogw" class="btn btn-success btn-sm rounded-circle me-2 mb-2">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="border-top border-secondary pt-4">
                    <div class="row">
                        <div class="col-12 text-center">
                            <p class="text-light mb-0">
                                &copy; 2024 Bogor Raya Development. All rights reserved.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.querySelector('#testimoniCarousel');
        const interval = 3000;

        setInterval(() => {
            const activeItem = carousel.querySelector('.carousel-item.active');
            const nextItem = activeItem.nextElementSibling || carousel.querySelector('.carousel-item:first-child');
            activeItem.classList.remove('active');
            nextItem.classList.add('active');
        }, interval);
        });
        document.addEventListener("DOMContentLoaded", function() {
        let carousel = document.querySelector("#testimonialCarousel");
        let clone = carousel.innerHTML;
        carousel.innerHTML += clone; // Duplicate items to create seamless loop
    });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.js"></script>
        <script src='https://cdn.firebase.com/js/client/2.3.2/firebase.js' type='text/javascript'></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    </div>
</body>

</html>