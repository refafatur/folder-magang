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
    <link href="assets/css/style.css" rel="stylesheet">
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

    .hover-success:hover {
            color: #28a745 !important;
            transition: color 0.3s ease;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .social-links a:hover {
            transform: translateY(-3px);
        }

        .footer-contact p {
            transition: transform 0.3s ease;
        }

        .footer-contact p:hover {
            transform: translateX(5px);
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

        <section class="hero-section mb-5">
            <div class="container">
                <div class="hero-content text-center text-white">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <h1 class="hero-title mb-4 animate__animated animate__fadeInDown">
                                <span class="text-warning">News</span> & <span class="text-success">Promotion</span>
                            </h1>
                            <p class="lead mt-3 animate__animated animate__fadeInUp" style="font-size: 1.5rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                Discover the Latest Updates and Exclusive Offers from Bogor Raya Residential
                            </p>
                            <div class="mt-5 animate__animated animate__fadeInUp animate__delay-2s">
                                <p class="text-white">
                                    <i class="fas fa-bell fa-bounce me-2"></i>
                                    Don't miss our exclusive updates and special offers!
                                </p>
                            </div>
                        </div>
                    </div>
                </div></p>
            </div>
        </section>

        <div class="container">
        <section class="news-section py-5">
                <div class="container"></div>
                    <!-- News Item 1 -->
                    <?php
                $query = "SELECT * FROM tbl_news WHERE status = 'show'";
                $result = mysqli_query($conn, $query);

                $counter = 0;
                while($row = mysqli_fetch_assoc($result)) {
                    $counter++;
                ?>
                    <div class="news-item mb-5">
                        <div class="row align-items-center">
                            <?php if ($counter % 2 == 1) { ?>
                            <div class="col-lg-6">
                                <img src="bogor-raya-residential/<?php echo $row['foto']; ?>" class="img-fluid rounded shadow" alt="News 1">
                            </div>
                            <div class="col-lg-6">
                                <div class="news-content p-4">
                                    <h3 class="text-success mb-3"><?php echo $row['nama']; ?></h3>
                                    <p class="text-muted mb-2"><i class="fas fa-map-marker-alt"></i> <?php echo $row['lokasi']; ?></p>
                                    <p class="lead text-justify">
                                    <?php echo nl2br($row['deskripsi']); ?>
                                    <br>📞 Hubungi kami sekarang:
                                            <br>📲 0811 999 1660
                                            <br>🌐 www.bogorraya.co.id/residential
                                            <br>Jangan lewatkan kesempatan emas ini! 🏆✨
                                    </p>
                                    <a href="bogor-raya-residential/<?php echo $row['foto']; ?>" target="_blank" class="btn btn-outline-success rounded-pill">Baca Selengkapnya</a>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="col-lg-6 order-lg-2">
                                <img src="bogor-raya-residential/<?php echo $row['foto']; ?>" class="img-fluid rounded shadow" alt="News 1">
                            </div>
                            <div class="col-lg-6 order-lg-1">
                                <div class="news-content p-4">
                                    <h3 class="text-success mb-3"><?php echo $row['nama']; ?></h3>
                                    <p class="text-muted mb-2"><i class="fas fa-map-marker-alt"></i> <?php echo $row['lokasi']; ?></p>
                                    <p class="lead text-justify">
                                    <?php echo nl2br($row['deskripsi']); ?>
                                    <br>📞 Hubungi kami sekarang:
                                            <br>📲 0811 999 1660
                                            <br>🌐 www.bogorraya.co.id/residential
                                            <br>Jangan lewatkan kesempatan emas ini! 🏆✨
                                    </p>
                                    <a href="bogor-raya-residential/<?php echo $row['foto']; ?>" target="_blank" class="btn btn-outline-success rounded-pill">Baca Selengkapnya</a>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <hr class="my-5 border-success">

                    <?php
                }
                ?>

                </div>
            </section>

        </div>

        <br>
        <br>

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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="assets/js/scrollspy.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.js"></script>
        <script src="assets/js/agency.js"></script>
        <script src="assets/js/Lightbox-Gallery.js"></script>
        <script src='https://cdn.firebase.com/js/client/2.3.2/firebase.js' type='text/javascript'></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    </div>
</body>

</html>