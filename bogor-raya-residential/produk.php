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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

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

        #unitDetailCarousel img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        #unitDetailCarousel img:hover {
            transform: scale(1.02);
        }

        /* Fancybox customization */
        .fancybox-bg {
            background: rgba(0, 0, 0, 0.9);
        }

        .fancybox-button {
            background: rgba(40, 167, 69, 0.8);
        }

        .fancybox-button:hover {
            background: rgba(40, 167, 69, 1);
        }

        .unit-image-link {
            display: block;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }

        .unit-image-link::after {
            content: '\f00e';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 1.5rem;
            opacity: 0;
            transition: all 0.3s ease;
            background: rgba(40, 167, 69, 0.8);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .unit-image-link:hover::after {
            opacity: 1;
        }

        .unit-image-link:hover img {
            transform: scale(1.1);
        }

        .unit-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .fancybox-caption {
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
        }

        .unit-image-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            cursor: pointer;
        }

        .unit-image-link {
            display: block;
            position: relative;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(40, 167, 69, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .image-overlay i {
            color: white;
            font-size: 1.5rem;
            transform: scale(0.5);
            transition: transform 0.3s ease;
        }

        .unit-image-link:hover .image-overlay {
            opacity: 1;
        }

        .unit-image-link:hover .image-overlay i {
            transform: scale(1);
        }

        .unit-thumbnail {
            width: 120px;
            height: 80px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .unit-image-link:hover .unit-thumbnail {
            transform: scale(1.1);
        }

        .fancybox-bg {
            background: rgba(0, 0, 0, 0.95);
        }

        .fancybox-is-open .fancybox-bg {
            opacity: 1;
        }

        .fancybox-slide--image {
            padding: 30px;
        }

        .fancybox-toolbar {
            opacity: 1;
            visibility: visible;
        }

        .fancybox-button {
            background: rgba(40, 167, 69, 0.8) !important;
            color: #fff !important;
        }

        .fancybox-button:hover {
            background: rgba(40, 167, 69, 1) !important;
        }

        .fancybox-caption {
            background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0) 100%);
            padding: 30px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
        }

        .fancybox-progress {
            background: #28a745;
        }

        .fancybox-thumbs {
            background: #111;
        }

        .fancybox-thumbs__list a:before {
            border-color: #28a745;
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
                    <span class="text-warning">Our</span> <span class="text-success">Products</span>
                    </h1>
                    <p class="lead mt-3 animate__animated animate__fadeInUp" style="font-size: 1.5rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                    Discover Premium Living at Bogor Raya Residential
                    </p>
                    <div class="mt-5 animate__animated animate__fadeInUp animate__delay-2s">
                    <p class="text-white">
                        <i class="fas fa-home fa-bounce me-2"></i>
                        Find your dream home with our exclusive properties!
                    </p>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </section>

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center mb-5">
                    <h2 class="section-title fw-bold">
                        <span class="text-success">Premium Living</span> Spaces
                    </h2>
                    <p class="lead text-muted mt-4">Experience luxury and comfort in our carefully designed residential clusters</p>
                    <div class="divider mx-auto my-4"></div>
                </div>
            </div>
            
            <div class="row g-4">
                <?php
                $query = "SELECT * FROM tbl_produk WHERE status = 1";
                $result = mysqli_query($conn, $query);

                while($row = mysqli_fetch_assoc($result)) {
                ?>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card product-card h-100 border-0 shadow-hover animate__animated animate__fadeIn">
                            <div class="position-relative overflow-hidden">
                                <img src="bogor-raya-residential/<?php echo $row['foto']; ?>" 
                                     class="card-img-top product-img" 
                                     alt="<?php echo $row['nama']; ?>">
                                <div class="overlay">
                                    <span class="badge bg-gradient position-absolute top-0 end-0 m-3">
                                        <i class="fas fa-star-of-life me-1"></i> Premium
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <h4 class="card-title fw-bold mb-3"><?php echo $row['nama']; ?></h4>
                                <p class="card-text text-muted mb-4"><?php echo substr($row['deskripsi'], 0, 120); ?>...</p>
                                
                                <div class="specs-container mb-4">
                                    <div class="spec-item">
                                        <div class="spec-icon">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="spec-info">
                                            <span class="spec-label">Starting from</span>
                                            <span class="spec-value">Rp <?php echo number_format((float)str_replace('.', '', $row['harga']), 0, ',', '.'); ?></span>
                                        </div>
                                    </div>
                                    <div class="spec-item">
                                        <div class="spec-icon">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div class="spec-info">
                                            <span class="spec-label">Floors</span>
                                            <span class="spec-value"><?php echo $row['lantai']; ?></span>
                                        </div>
                                    </div>
                                    <div class="spec-item">
                                        <div class="spec-icon">
                                            <i class="fas fa-home"></i>
                                        </div>
                                        <div class="spec-info">
                                            <span class="spec-label">Style</span>
                                            <span class="spec-value"><?php echo $row['tampilan']; ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="action-buttons">
                                    <button class="btn btn-primary-gradient btn-lg w-100 mb-2" 
                                            onclick="showUnits(<?php echo $row['id']; ?>)">
                                        <i class="fas fa-th-list me-2"></i>View Units
                                    </button>
                                    <a class="btn btn-outline-success btn-lg w-100" 
                                       href="bogor-raya-residential/<?php echo $row['file']; ?>" 
                                       target="_blank">
                                        <i class="fas fa-file-download me-2"></i>Download Brochure
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>

            <style>
            .divider {
                width: 60px;
                height: 3px;
                background: linear-gradient(90deg, #28a745, #20c997);
                border-radius: 3px;
            }

            .product-card {
                border-radius: 20px;
                overflow: hidden;
                transition: all 0.4s ease;
            }

            .shadow-hover:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            }

            .product-img {
                height: 300px;
                object-fit: cover;
                transition: transform 0.6s ease;
            }

            .product-card:hover .product-img {
                transform: scale(1.1);
            }

            .overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0) 100%);
            }

            .bg-gradient {
                background: linear-gradient(45deg, #28a745, #20c997);
                border: none;
                padding: 0.6rem 1rem;
            }

            .specs-container {
                display: grid;
                gap: 1rem;
            }

            .spec-item {
                display: flex;
                align-items: center;
                padding: 0.8rem;
                background: #f8f9fa;
                border-radius: 12px;
                transition: all 0.3s ease;
            }

            .spec-item:hover {
                background: #e9ecef;
                transform: translateX(5px);
            }

            .spec-icon {
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #28a745;
                color: white;
                border-radius: 10px;
                margin-right: 1rem;
            }

            .spec-info {
                display: flex;
                flex-direction: column;
            }

            .spec-label {
                font-size: 0.8rem;
                color: #6c757d;
            }

            .spec-value {
                font-weight: 600;
                color: #212529;
            }

            .btn-primary-gradient {
                background: linear-gradient(45deg, #28a745, #20c997);
                border: none;
                color: white;
                transition: all 0.3s ease;
            }

            .btn-primary-gradient:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(40,167,69,0.3);
            }

            .action-buttons {
                display: grid;
                gap: 0.5rem;
            }
            </style>

        </div>

        <br>
        <br>
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
                            <a href="https://bogorraya.co.id/residential/trpdf.html" class="text-light text-decoration-none mb-2 hover-success" target="_blank" rel="noopener">
                                <i class="fas fa-chevron-right text-success me-2"></i>Terracotta 3
                            </a>
                            <a href="https://bogorraya.co.id/residential/trpdf.html" class="text-light text-decoration-none mb-2 hover-success" target="_blank">
                                <i class="fas fa-chevron-right text-success me-2"></i>Terracotta Insert
                            </a>
                            <a href="https://bogorraya.co.id/residential/kppdf.html" class="text-light text-decoration-none mb-2 hover-success" target="_blank">
                                <i class="fas fa-chevron-right text-success me-2"></i>Kemang Permata Neo
                            </a>
                            <a href="https://bogorraya.co.id/residential/kppdf.html" class="text-light text-decoration-none mb-2 hover-success" target="_blank">
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

    <!-- Units Modal -->
    <div class="modal fade" id="unitsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 bg-gradient-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-building me-2"></i>Available Units
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle custom-table">
                            <thead>
                                <tr>
                                    <th>Unit Type</th>
                                    <th>Price</th>
                                    <th>Specifications</th>
                                    <th>Stock</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody id="unitsTableBody">
                                <!-- Units will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showUnits(productId) {
        fetch(`get_units.php?product_id=${productId}`)
            .then(response => response.json())
            .then(units => {
                const tbody = document.getElementById('unitsTableBody');
                tbody.innerHTML = '';
                
                units.forEach((unit, index) => {
                    // Parse the JSON photo data
                    let photoUrl = '';
                    try {
                        const photos = JSON.parse(unit.foto);
                        photoUrl = photos && photos.length > 0 ? photos[0] : '';
                    } catch (e) {
                        console.warn('Error parsing photo JSON:', e);
                    }

                    tbody.innerHTML += `
                        <tr class="unit-row animate__animated animate__fadeIn" style="animation-delay: ${index * 0.1}s">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="unit-image-wrapper">
                                        ${photoUrl ? 
                                            `<a href="bogor-raya-residential/${photoUrl}" 
                                                data-fancybox="gallery-${unit.id}" 
                                                data-caption="${unit.name}"
                                                class="unit-image-link">
                                                <img src="bogor-raya-residential/${photoUrl}" 
                                                     class="unit-thumbnail" 
                                                     alt="${unit.name}">
                                                <div class="image-overlay">
                                                    <i class="fas fa-search-plus"></i>
                                                </div>
                                            </a>
                                            ${(() => {
                                                try {
                                                    const allPhotos = JSON.parse(unit.foto);
                                                    return allPhotos.slice(1).map(photo => `
                                                        <a href="bogor-raya-residential/${photo}" 
                                                           data-fancybox="gallery-${unit.id}"
                                                           data-caption="${unit.name}"
                                                           style="display: none;">
                                                        </a>
                                                    `).join('');
                                                } catch (e) {
                                                    console.warn('Error parsing additional photos:', e);
                                                    return '';
                                                }
                                            })()}` :
                                            `<div class="unit-thumbnail-placeholder">
                                                <i class="fas fa-home"></i>
                                             </div>`
                                        }
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">${unit.name}</h6>
                                        <span class="cluster-badge">${unit.cluster}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="price-tag">
                                    <i class="fas fa-tag me-1"></i>
                                    Rp${formatNumber(unit.harga)}
                                </div>
                            </td>
                            <td>
                                <div class="specs-grid">
                                    <div class="spec-item">
                                        <i class="fas fa-layer-group"></i>
                                        <span>${unit.lantai} Floor(s)</span>
                                    </div>
                                    <div class="spec-item">
                                        <i class="fas fa-vector-square"></i>
                                        <span>Land: ${unit.luas_tanah}m²</span>
                                    </div>
                                    <div class="spec-item">
                                        <i class="fas fa-home"></i>
                                        <span>Building: ${unit.luas_bangunan}m²</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-pill ${unit.stok > 0 ? 'available' : 'sold-out'}">
                                    <i class="fas ${unit.stok > 0 ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                                    ${unit.stok > 0 ? `${unit.stok} Available` : 'Sold Out'}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-detail" onclick='showUnitDetails(${JSON.stringify(unit).replace(/'/g, "&apos;")})'>
                                    <i class="fas fa-info-circle me-2"></i>View Details
                                </button>
                            </td>
                        </tr>
                    `;
                });
                new bootstrap.Modal(document.getElementById('unitsModal')).show();
            });
    }

    function formatNumber(num) {
        try {
            if (!num) return '0';
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        } catch (error) {
            console.warn('Error formatting number:', error);
            return '0';
        }
    }

    function showUnitDetails(unit) {
        try {
            if (typeof unit === 'string') {
                unit = JSON.parse(unit);
            }

            const carouselInner = document.querySelector('#unitDetailCarousel .carousel-inner');
            const carouselIndicators = document.querySelector('#unitDetailCarousel .carousel-indicators');
            carouselInner.innerHTML = '';
            carouselIndicators.innerHTML = '';

            let photos;
            try {
                photos = Array.isArray(unit.foto) ? unit.foto : JSON.parse(unit.foto);
            } catch (e) {
                photos = [];
                console.warn('Error parsing photos:', e);
            }

            if (photos && photos.length > 0) {
                photos.forEach((photo, index) => {
                    const photoUrl = `bogor-raya-residential/${photo}`;
                    carouselInner.innerHTML += `
                        <div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <a href="${photoUrl}" data-fancybox="unit-gallery">
                                <img src="${photoUrl}" class="d-block w-100" alt="Unit photo ${index + 1}">
                            </a>
                        </div>
                    `;
                    carouselIndicators.innerHTML += `
                        <button type="button" 
                                data-bs-target="#unitDetailCarousel" 
                                data-bs-slide-to="${index}" 
                                ${index === 0 ? 'class="active"' : ''}
                                aria-label="Slide ${index + 1}">
                        </button>
                    `;
                });
            } else {
                carouselInner.innerHTML = `
                    <div class="carousel-item active">
                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                            <i class="fas fa-home fa-4x text-muted"></i>
                        </div>
                    </div>
                `;
            }

            const price = unit.harga ? formatNumber(unit.harga) : 'Price on request';

            document.getElementById('unitDetailName').textContent = unit.name || 'Unnamed Unit';
            document.getElementById('unitDetailPrice').textContent = `Rp ${price}`;
            document.getElementById('unitDetailLand').textContent = `${unit.luas_tanah || 'N/A'} m²`;
            document.getElementById('unitDetailBuilding').textContent = `${unit.luas_bangunan || 'N/A'} m²`;
            document.getElementById('unitDetailBedrooms').textContent = unit.kamar_tidur || 'N/A';
            document.getElementById('unitDetailBathrooms').textContent = unit.kamar_mandi || 'N/A';
            document.getElementById('unitDetailDesc').textContent = unit.deskripsi || 'No description available';

            const featuresContainer = document.getElementById('unitDetailFeatures');
            featuresContainer.innerHTML = '';
            const features = [
                'Smart Home System',
                'Garden Area',
                'Car Port',
                'Security 24/7',
                'Modern Kitchen',
                'High Ceiling'
            ];
            features.forEach(feature => {
                featuresContainer.innerHTML += `
                    <div class="feature-tag">
                        <i class="fas fa-check-circle"></i>
                        ${feature}
                    </div>
                `;
            });

            new bootstrap.Modal(document.getElementById('unitDetailModal')).show();
        } catch (error) {
            console.error('Error showing unit details:', error);
            alert('Sorry, there was an error displaying the unit details. Please try again.');
        }
    }

    $(document).ready(function() {
        $('[data-fancybox]').fancybox({
            buttons: [
                "slideShow",
                "thumbs",
                "zoom",
                "fullScreen",
                "close"
            ],
            animationEffect: "zoom-in-out",
            transitionEffect: "slide",
            transitionDuration: 366,
            loop: true,
            thumbs: {
                autoStart: true,
                axis: 'x'
            },
            slideShow: {
                autoStart: false,
                speed: 3000
            },
            protect: true,
            modal: false,
            wheel: false,
            clickContent: false,
            clickSlide: false,
            mobile: {
                clickContent: function(current, event) {
                    return current.type === "image" ? "toggleControls" : false;
                },
                clickSlide: function(current, event) {
                    return current.type === "image" ? "toggleControls" : false;
                },
            },
            caption: function(instance, item) {
                return $(this).data('caption');
            }
        });
    });

    function shareUnit() {
        if (navigator.share) {
            navigator.share({
                title: document.getElementById('unitDetailName').textContent,
                text: document.getElementById('unitDetailDesc').textContent,
                url: window.location.href
            });
        }
    }
    </script>

    <style>
    .modal-content {
        border-radius: 15px;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #28a745, #20c997);
    }

    .custom-table thead th {
        background: #f8f9fa;
        padding: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .unit-image-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }

    .unit-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .unit-image-wrapper:hover .unit-thumbnail {
        transform: scale(1.1);
    }

    .cluster-badge {
        background: #e8f5e9;
        color: #28a745;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
    }

    .price-tag {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 0.5rem 1.25rem;
        border-radius: 25px;
        font-weight: 600;
        display: inline-block;
        box-shadow: 0 3px 6px rgba(40, 167, 69, 0.2);
    }

    .specs-grid {
        display: grid;
        gap: 0.5rem;
    }

    .spec-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem;
        background: #f8f9fa;
        border-radius: 8px;
        transition: transform 0.2s;
    }

    .spec-item:hover {
        transform: translateX(5px);
        background: #e9ecef;
    }

    .spec-item i {
        color: #28a745;
    }

    .status-pill {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-pill.available {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-pill.sold-out {
        background: #ffebee;
        color: #c62828;
    }

    .btn-detail {
        background: transparent;
        border: 2px solid #28a745;
        color: #28a745;
        border-radius: 25px;
        padding: 0.5rem 1.25rem;
        transition: all 0.3s;
    }

    .btn-detail:hover {
        background: #28a745;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2);
    }

    .unit-row {
        transition: transform 0.2s;
    }

    .unit-row:hover {
        transform: translateY(-2px);
        background: #f8f9fa;
    }
    </style>

    <!-- Unit Detail Modal -->
    <div class="modal fade" id="unitDetailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <button type="button" class="btn-close position-absolute end-0 top-0 m-3 z-3" data-bs-dismiss="modal"></button>
                <div class="row g-0">
                    <div class="col-lg-7">
                        <div id="unitDetailCarousel" class="carousel slide h-100" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <!-- Will be populated dynamically -->
                            </div>
                            <div class="carousel-inner h-100">
                                <!-- Will be populated dynamically -->
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#unitDetailCarousel" data-bs-slide="prev" style="background: rgba(255,255,255,0.5); width: 40px; height: 40px; border-radius: 50%; margin: auto 10px;">
                                <span class="carousel-control-prev-icon" style="filter: invert(1);"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#unitDetailCarousel" data-bs-slide="next" style="background: rgba(255,255,255,0.5); width: 40px; height: 40px; border-radius: 50%; margin: auto 10px;">
                                <span class="carousel-control-next-icon" style="filter: invert(1);"></span>
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="p-4 p-lg-5">
                            <div class="mb-4">
                                <span class="badge bg-success mb-2">Available</span>
                                <h3 class="fw-bold" id="unitDetailName"></h3>
                                <p class="text-success h4 mb-3" id="unitDetailPrice"></p>
                            </div>
                            
                            <div class="specs-grid-detail mb-4">
                                <div class="spec-detail-item">
                                    <i class="fas fa-ruler-combined"></i>
                                    <div>
                                        <small>Land Area</small>
                                        <strong id="unitDetailLand"></strong>
                                    </div>
                                </div>
                                <div class="spec-detail-item">
                                    <i class="fas fa-home"></i>
                                    <div>
                                        <small>Building Area</small>
                                        <strong id="unitDetailBuilding"></strong>
                                    </div>
                                </div>
                                <div class="spec-detail-item">
                                    <i class="fas fa-bed"></i>
                                    <div>
                                        <small>Bedrooms</small>
                                        <strong id="unitDetailBedrooms"></strong>
                                    </div>
                                </div>
                                <div class="spec-detail-item">
                                    <i class="fas fa-bath"></i>
                                    <div>
                                        <small>Bathrooms</small>
                                        <strong id="unitDetailBathrooms"></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="fw-bold mb-3">Description</h5>
                                <p class="text-muted" id="unitDetailDesc"></p>
                            </div>

                            <div class="mb-4">
                                <h5 class="fw-bold mb-3">Features</h5>
                                <div class="features-grid" id="unitDetailFeatures">
                                    <!-- Will be populated dynamically -->
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="contact.php" class="btn btn-success btn-lg">
                                    <i class="fas fa-phone-alt me-2"></i>Contact Sales
                                </a>
                                <button class="btn btn-outline-success btn-lg" onclick="shareUnit()">
                                    <i class="fas fa-share-alt me-2"></i>Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Add these styles */
    .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    #unitDetailCarousel {
        border-radius: 20px 0 0 20px;
        overflow: hidden;
    }

    #unitDetailCarousel .carousel-inner {
        border-radius: 20px 0 0 20px;
    }

    #unitDetailCarousel .carousel-item {
        height: 600px;
    }

    #unitDetailCarousel img {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }

    .specs-grid-detail {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .spec-detail-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .spec-detail-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .spec-detail-item i {
        font-size: 1.5rem;
        color: #28a745;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e8f5e9;
        border-radius: 10px;
    }

    .spec-detail-item div {
        display: flex;
        flex-direction: column;
    }

    .spec-detail-item small {
        color: #6c757d;
        font-size: 0.8rem;
    }

    .spec-detail-item strong {
        color: #212529;
        font-size: 1.1rem;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .feature-tag {
        background: #e8f5e9;
        color: #28a745;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    @media (max-width: 991.98px) {
        #unitDetailCarousel {
            border-radius: 20px 20px 0 0;
        }
        #unitDetailCarousel .carousel-inner {
            border-radius: 20px 20px 0 0;
        }
        #unitDetailCarousel .carousel-item {
            height: 400px;
        }
    }
    </style>

    <script>
    // Update the showUnitDetails function
    function showUnitDetails(unit) {
        try {
            if (typeof unit === 'string') {
                unit = JSON.parse(unit);
            }

            const carouselInner = document.querySelector('#unitDetailCarousel .carousel-inner');
            const carouselIndicators = document.querySelector('#unitDetailCarousel .carousel-indicators');
            carouselInner.innerHTML = '';
            carouselIndicators.innerHTML = '';

            let photos;
            try {
                photos = Array.isArray(unit.foto) ? unit.foto : JSON.parse(unit.foto);
            } catch (e) {
                photos = [];
                console.warn('Error parsing photos:', e);
            }

            if (photos && photos.length > 0) {
                photos.forEach((photo, index) => {
                    const photoUrl = `bogor-raya-residential/${photo}`;
                    carouselInner.innerHTML += `
                        <div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <a href="${photoUrl}" data-fancybox="unit-gallery">
                                <img src="${photoUrl}" class="d-block w-100" alt="Unit photo ${index + 1}">
                            </a>
                        </div>
                    `;
                    carouselIndicators.innerHTML += `
                        <button type="button" 
                                data-bs-target="#unitDetailCarousel" 
                                data-bs-slide-to="${index}" 
                                ${index === 0 ? 'class="active"' : ''}
                                aria-label="Slide ${index + 1}">
                        </button>
                    `;
                });
            } else {
                carouselInner.innerHTML = `
                    <div class="carousel-item active">
                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                            <i class="fas fa-home fa-4x text-muted"></i>
                        </div>
                    </div>
                `;
            }

            const price = unit.harga ? formatNumber(unit.harga) : 'Price on request';

            document.getElementById('unitDetailName').textContent = unit.name || 'Unnamed Unit';
            document.getElementById('unitDetailPrice').textContent = `Rp ${price}`;
            document.getElementById('unitDetailLand').textContent = `${unit.luas_tanah || 'N/A'} m²`;
            document.getElementById('unitDetailBuilding').textContent = `${unit.luas_bangunan || 'N/A'} m²`;
            document.getElementById('unitDetailBedrooms').textContent = unit.kamar_tidur || 'N/A';
            document.getElementById('unitDetailBathrooms').textContent = unit.kamar_mandi || 'N/A';
            document.getElementById('unitDetailDesc').textContent = unit.deskripsi || 'No description available';

            const featuresContainer = document.getElementById('unitDetailFeatures');
            featuresContainer.innerHTML = '';
            const features = [
                'Smart Home System',
                'Garden Area',
                'Car Port',
                'Security 24/7',
                'Modern Kitchen',
                'High Ceiling'
            ];
            features.forEach(feature => {
                featuresContainer.innerHTML += `
                    <div class="feature-tag">
                        <i class="fas fa-check-circle"></i>
                        ${feature}
                    </div>
                `;
            });

            new bootstrap.Modal(document.getElementById('unitDetailModal')).show();
        } catch (error) {
            console.error('Error showing unit details:', error);
            alert('Sorry, there was an error displaying the unit details. Please try again.');
        }
    }

    function shareUnit() {
        if (navigator.share) {
            navigator.share({
                title: document.getElementById('unitDetailName').textContent,
                text: document.getElementById('unitDetailDesc').textContent,
                url: window.location.href
            });
        }
    }
    </script>
</body>

</html>
