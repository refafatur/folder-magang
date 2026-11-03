<?php
$conn = mysqli_connect("localhost", "root", "", "db_bogor");
require_once 'includes/notifications.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_contact'])) {
        $nama = trim($_POST['nama']);
        $email = trim($_POST['email']);
        $no_hp = trim($_POST['no_telp']);
        $cluster = trim($_POST['cluster']);
        $pesan = trim($_POST['pesan']);
        $tgl_kunjungan = trim($_POST['tgl_kunjungan']);

        $query = "INSERT INTO tbl_contacts (nama, email, no_telp, cluster, pesan, tgl_kunjungan) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $nama, $email, $no_hp, $cluster, $pesan, $tgl_kunjungan);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Add notification for new order
            triggerNotification($conn, 'new_order', [
                'nama' => $nama
            ]);
            mysqli_stmt_close($stmt);
            header("Location: contact.php?status=success");
        } else {
            header("Location: contact.php?status=error");
        }
        exit();
    }
}
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


        <div class="container py-5">
            <br>
            <br>
            <div class="row justify-content-center">
            <!-- Left side - Housing Image -->
            <div class="col-md-6 mb-4 mb-md-0">
                <div id="housingCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-wrap="true">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="assets/rumah.png" alt="Housing Preview 1" class="img-fluid rounded shadow" style="width: 100%; height: 600px; object-fit: cover;">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/kemang.png" alt="Housing Preview 2" class="img-fluid rounded shadow" style="width: 100%; height: 600px; object-fit: cover;">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/tera.png" alt="Housing Preview 3" class="img-fluid rounded shadow" style="width: 100%; height: 600px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Form -->
            <div class="col-md-6">
                <div class="card-body">
                <h3 class="card-title text-center mb-4">Formulir Pemesanan</h3>
                <div id="alert-container"></div>

                <form action="contact.php" method="POST">
                    <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>

                    <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="tel" class="form-control" id="no_telp" name="no_telp" required>
                    </div>

                    <div class="mb-3">
                    <label for="property" class="form-label">Cluster</label>
                    <select class="form-select" id="cluster" name="cluster" required>
                        <option value="">Pilih Cluster...</option>
                        <?php
                        $query = "SELECT nama FROM tbl_produk";
                        $result = mysqli_query($conn, $query);
                        while($row = mysqli_fetch_assoc($result)) {
                            $cluster = $row['nama'];
                            echo "<option value=\"" . htmlspecialchars($cluster) . "\">" . htmlspecialchars($cluster) . "</option>";
                        }
                        ?>
                    </select>
                    </div>

                    <div class="mb-3">
                    <label for="message" class="form-label">Pesan Tambahan</label>
                    <textarea class="form-control" id="pesan" name="pesan" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                    <label for="date" class="form-label">Tanggal Kunjungan</label>
                    <input type="date" class="form-control" id="tgl_kunjungan" name="tgl_kunjungan" required onclick="this.showPicker()">
                    </div>

                    <div class="d-grid gap-2">
                    <button type="submit" name="add_contact" class="btn btn-success btn-lg">Kirim Pemesanan</button>
                    </div>
                </form>
                </div>
            </div>
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
                    document.addEventListener("DOMContentLoaded", function() {
                        const urlParams = new URLSearchParams(window.location.search);
                        const alertContainer = document.getElementById('alert-container');

                        if (urlParams.has('status')) {
                            const status = urlParams.get('status');
                            let alertHTML = '';

                            if (status === 'success') {
                                alertHTML = `
                                    <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                                        <i class="fas fa-check-circle"></i> Pemesanan Anda telah berhasil dikirim!
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                `;
                            } else if (status === 'error') {
                                alertHTML = `
                                    <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                                        <i class="fas fa-times-circle"></i> Terjadi kesalahan, pemesanan Anda gagal dikirim!
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                `;
                            }

                            alertContainer.innerHTML = alertHTML;
                        }
                    });
        </script>
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
