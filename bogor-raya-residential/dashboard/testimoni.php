<?php
session_start();
require_once "../includes/notifications.php";
require_once '../includes/auth_helper.php';
checkUserStatus();

if (!isset($_SESSION['nama'])) {
    header("Location: ../login.php");
    exit();
}

// Decode permissions
$permissions = json_decode($_SESSION['permissions'], true);
$canView = isset($permissions['testimonials']['view']) && $permissions['testimonials']['view'] == 1;
$canAdd = isset($permissions['testimonials']['add']) && $permissions['testimonials']['add'] == 1;
$canEdit = isset($permissions['testimonials']['edit']) && $permissions['testimonials']['edit'] == 1;
$canDelete = isset($permissions['testimonials']['delete']) && $permissions['testimonials']['delete'] == 1;

// Check view permission
if (!$canView) {
    header("Location: ../dashboard.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "db_bogor");

// Handle DELETE request - Check delete permission
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete'])) {
    if (!$canDelete) {
        header("Location: testimoni.php");
        exit();
    }
    $id = $_GET['delete'];
    $result = mysqli_query($conn, "SELECT foto FROM tbl_testimoni WHERE id=$id");
    $foto = mysqli_fetch_assoc($result)['foto'];
    if (file_exists($foto)) {
        unlink($foto);
    }
    mysqli_query($conn, "DELETE FROM tbl_testimoni WHERE id=$id");
    header("Location: testimoni.php");
    exit();
}

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add testimonial - Check add permission
    if (isset($_POST['add_testimoni'])) {
        if (!$canAdd) {
            header("Location: testimoni.php");
            exit();
        }
        $nama = $_POST['nama'];
        $pesan = $_POST['pesan'];
        $rate = $_POST['rate'];
        
        $foto = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_dir = "../uploads/testimoni/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $foto = $target_dir . basename($_FILES["foto"]["name"]);
            move_uploaded_file($_FILES["foto"]["tmp_name"], $foto);
        }

        $query = "INSERT INTO tbl_testimoni (nama, pesan, foto, rate) 
                  VALUES ('$nama', '$pesan', '$foto', '$rate')";
        if (mysqli_query($conn, $query)) {
            triggerNotification($conn, 'new_testimonial', ['nama' => $nama]);
        }
        header("Location: testimoni.php");
        exit();
    }

    // Update testimonial - Check edit permission
    if (isset($_POST['update_testimoni'])) {
        if (!$canEdit) {
            header("Location: testimoni.php");
            exit();
        }
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $pesan = $_POST['pesan'];
        $rate = $_POST['rate'];
        
        $query = "UPDATE tbl_testimoni SET 
                  nama='$nama', 
                  pesan='$pesan', 
                  rate='$rate'";

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_dir = "../uploads/testimoni/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            // Delete old photo first
            $result = mysqli_query($conn, "SELECT foto FROM tbl_testimoni WHERE id=$id");
            if ($result && $row = mysqli_fetch_assoc($result)) {
                $old_foto = $row['foto'];
                if (file_exists($old_foto)) {
                    unlink($old_foto);
                }
            }
            
            // Upload new photo
            $foto = $target_dir . basename($_FILES["foto"]["name"]);
            move_uploaded_file($_FILES["foto"]["tmp_name"], $foto);
            $query .= ", foto='$foto'";
        }

        $query .= " WHERE id=$id";
        mysqli_query($conn, $query);
        header("Location: testimoni.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (isset($_SESSION['level'])): ?>
        <?php if ($_SESSION['level'] == '0'): ?>
            <title>Dashboard Admin | Bogor Raya Residential</title>
        <?php elseif ($_SESSION['level'] == '1'): ?>
            <title>Dashboard User | Bogor Raya Residential</title>
        <?php elseif ($_SESSION['level'] == '2'): ?>
            <title>Dashboard Sales | Bogor Raya Residential</title>
        <?php endif; ?>
    <?php else: ?>
        <title>Dashboard | Bogor Raya Residential</title>
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <link href="../assets/css/dashboard-produk.css" rel="stylesheet">
    <style>
        .star-rating {
            direction: rtl;
            display: inline-block;
            padding: 20px;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            color: #bbb;
            cursor: pointer;
            font-size: 24px;
            padding: 0 2px;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input[type="radio"]:checked ~ label {
            color: #f2b600;
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .testimonial-card {
            height: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            border-radius: 10px;
            overflow: hidden;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .testimonial-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .testimonial-content {
            padding: 1.25rem;
        }

        .testimonial-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .testimonial-rating {
            margin-bottom: 1rem;
        }

        .testimonial-text {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .testimonial-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            padding: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h4>
            <span class="brand-text">Bogor Raya</span>
            <small>Residential Admin</small>
        </h4>
    </div>

    <div class="sidebar-menu">
        <div class="menu-section">
            <div class="menu-section-title">Main Navigation</div>
            <?php if ($_SESSION['level'] == '0'): ?>
            <!-- Dashboard is always visible -->
            <a href="../dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == '../dashboard.php') ? 'class="active"' : ''; ?>>
                <i class="fas fa-home"></i>Dashboard
            </a>
            <?php endif; ?>

            <?php
            // Debug: Print permissions
            // echo "<pre>"; print_r($_SESSION['permissions']); echo "</pre>";
            
            // Decode JSON permissions from session
            $permissions = json_decode($_SESSION['permissions'], true);

            // Product Menu
            if (isset($permissions['products']['view']) && $permissions['products']['view'] == 1) {
                echo '<a href="produk.php" '.(basename($_SERVER['PHP_SELF']) == 'produk.php' ? 'class="active"' : '').'>
                    <i class="fas fa-building"></i>Product
                </a>';
            }

            // News Menu
            if (isset($permissions['news']['view']) && $permissions['news']['view'] == 1) {
                echo '<a href="news.php" '.(basename($_SERVER['PHP_SELF']) == 'news.php' ? 'class="active"' : '').'>
                    <i class="fas fa-newspaper"></i>News & Promotion
                </a>';
            }

            // Contacts/Pemesanan Menu
            if (isset($permissions['contacts']['view']) && $permissions['contacts']['view'] == 1) {
                echo '<a href="contact.php" '.(basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'class="active"' : '').'>
                    <i class="fas fa-inbox"></i>Pemesanan
                </a>';
            }

            // Sales Menu
            if (isset($permissions['sales']['view']) && $permissions['sales']['view'] == 1) {
                echo '<a href="sales.php" '.(basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'class="active"' : '').'>
                    <i class="fas fa-user-tie"></i>Sales
                </a>';
            }

            // Testimonials Menu
            if (isset($permissions['testimonials']['view']) && $permissions['testimonials']['view'] == 1) {
                echo '<a href="testimoni.php" '.(basename($_SERVER['PHP_SELF']) == 'testimoni.php' ? 'class="active"' : '').'>
                    <i class="fas fa-quote-right"></i>Testimoni
                </a>';
            }


            // Administration Section - Users Menu
            if (isset($permissions['users']['view']) && $permissions['users']['view'] == 1) {
                echo '<div class="menu-section">
                    <div class="menu-section-title">Administration</div>
                    <a href="tambah-user.php" '.(basename($_SERVER['PHP_SELF']) == 'tambah-user.php' ? 'class="active"' : '').'>
                        <i class="fas fa-users-cog"></i>User
                    </a>
                    <a href="management.php" '.(basename($_SERVER['PHP_SELF']) == 'management.php' ? 'class="active"' : '').'>
                        <i class="fas fa-users-cog"></i>Management
                    </a>
                </div>';
            }
            ?>

            <!-- Profile is always visible -->
            <div class="menu-section">
                <a href="profile.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-user"></i>Profile
                </a>
            </div>
        </div>
    </div>

    <div class="sidebar-footer">

        <div class="user-info">
            <div class="user-avatar">
                <img src="../<?php echo htmlspecialchars($_SESSION['foto']); ?>" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;">
            </div>
            <div class="user-details">
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['nama']); ?></div>
                <?php if (isset($_SESSION['level'])): ?>
                <?php if ($_SESSION['level'] == '0'): ?>
                    <div class="user-role">Admin</div>
                <?php elseif ($_SESSION['level'] == '1'): ?>
                    <div class="user-role">User</div>
                <?php elseif ($_SESSION['level'] == '2'): ?>
                    <div class="user-role">Sales</div>
                <?php endif; ?>
            <?php endif; ?>
            </div>
        </div>


        <form action="../logout.php" method="post">
            <button type="submit" class="btn btn-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
        </form>
    </div>
</div>

<button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-quote-right me-2"></i>Testimoni Management</h2>
        <?php if ($canAdd): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTestimoniModal">
            <i class="fas fa-plus me-2"></i>Add New Testimoni
        </button>
        <?php endif; ?>
    </div>

    <!-- Filter and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search testimoni...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="ratingFilter">
                        <option value="">Filter by Rating</option>
                        <option value="5">★★★★★</option>
                        <option value="4">★★★★☆</option>
                        <option value="3">★★★☆☆</option>
                        <option value="2">★★☆☆☆</option>
                        <option value="1">★☆☆☆☆</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimoni Grid -->
    <div class="testimonial-grid">
        <?php 
        // Execute query and check for errors
        $testimoni = mysqli_query($conn, "SELECT * FROM tbl_testimoni");
        if (!$testimoni) {
            echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        } else {
            while ($testi = mysqli_fetch_assoc($testimoni)): 
        ?>
            <div class="testimonial-card">
                <div class="position-relative">
                    <img src="<?php echo htmlspecialchars($testi['foto']); ?>" 
                         class="testimonial-image" 
                         alt="<?php echo htmlspecialchars($testi['nama']); ?>">
                    <div class="testimonial-actions">
                        <?php if ($canEdit): ?>
                        <button class="btn btn-light btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal<?php echo $testi['id']; ?>">
                            <i class="fas fa-edit text-primary"></i>
                        </button>
                        <?php endif; ?>
                        <?php if ($canDelete): ?>
                        <a href="#" class="btn btn-light btn-sm" 
                           data-bs-toggle="modal" 
                           data-bs-target="#deleteModal<?php echo $testi['id']; ?>">
                            <i class="fas fa-trash text-danger"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="testimonial-content">
                    <h5 class="testimonial-name"><?php echo htmlspecialchars($testi['nama']); ?></h5>
                    <div class="testimonial-rating">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i <= $testi['rate'] ? 'text-warning' : 'text-secondary'; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="testimonial-text"><?php echo htmlspecialchars($testi['pesan']); ?></p>
                </div>
            </div>

            <?php if ($canEdit): ?>
            <!-- Edit Modal for each testimoni -->
            <div class="modal fade" id="editModal<?php echo $testi['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Testimoni</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="" enctype="multipart/form-data" class="row g-3">
                                <input type="hidden" name="id" value="<?php echo $testi['id']; ?>">
                                
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" 
                                               id="editNama<?php echo $testi['id']; ?>" 
                                               name="nama" 
                                               value="<?php echo htmlspecialchars($testi['nama']); ?>" 
                                               required>
                                        <label for="editNama<?php echo $testi['id']; ?>">Nama</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="editPesan<?php echo $testi['id']; ?>" 
                                                  name="pesan" style="height: 150px" required><?php echo htmlspecialchars($testi['pesan']); ?></textarea>
                                        <label for="editPesan<?php echo $testi['id']; ?>">Pesan</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Rating</label>
                                    <div class="star-rating">
                                        <?php for($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" id="edit-star<?php echo $testi['id']; ?>-<?php echo $i; ?>" 
                                                   name="rate" value="<?php echo $i; ?>" 
                                                   <?php echo $testi['rate'] == $i ? 'checked' : ''; ?>>
                                            <label for="edit-star<?php echo $testi['id']; ?>-<?php echo $i; ?>" 
                                                   class="fas fa-star"></label>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <label class="form-label">
                                                        <i class="fas fa-image me-2"></i>Current Image
                                                    </label>
                                                    <img src="<?php echo htmlspecialchars($testi['foto']); ?>" 
                                                         class="img-thumbnail d-block mb-2" 
                                                         alt="Current Image"
                                                         style="max-width: 200px;">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">
                                                        <i class="fas fa-upload me-2"></i>Upload New Image
                                                    </label>
                                                    <input type="file" class="form-control" name="foto" accept="image/*"
                                                           onchange="previewImage(this, 'imagePreviewEdit<?php echo $testi['id']; ?>')">
                                                    <div class="mt-2">
                                                        <img id="imagePreviewEdit<?php echo $testi['id']; ?>" 
                                                             src="#" 
                                                             alt="Preview" 
                                                             style="max-width: 200px; display: none;" 
                                                             class="img-thumbnail">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </button>
                                    <button type="submit" name="update_testimoni" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Testimoni
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($canDelete): ?>
            <!-- Add Delete Modal for each testimonial -->
            <div class="modal fade" id="deleteModal<?php echo $testi['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Confirmation</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this testimonial?</p>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                This action cannot be undone.
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-quote-right text-primary me-2"></i>
                                <strong><?php echo htmlspecialchars($testi['nama']); ?></strong>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <a href="?delete=<?php echo $testi['id']; ?>" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Delete Testimonial
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php 
            endwhile;
        }
        ?>
    </div>

    <?php if ($canAdd): ?>
    <!-- Add Testimoni Form Modal -->
    <div class="modal fade" id="addTestimoniModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Testimoni</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" id="addTestimoniForm" class="row g-3">
                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="addNama" name="nama" required>
                                <label for="addNama">Nama</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="addPesan" name="pesan" style="height: 150px" required></textarea>
                                <label for="addPesan">Pesan</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Rating</label>
                            <div class="star-rating">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="add-star-<?php echo $i; ?>" 
                                       name="rate" value="<?php echo $i; ?>" 
                                       <?php echo $i == 5 ? 'checked' : ''; ?>>
                                <label for="add-star-<?php echo $i; ?>" class="fas fa-star"></label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <label class="form-label"><i class="fas fa-image me-2"></i>Upload Foto</label>
                                    <input type="file" class="form-control" name="foto" accept="image/*" required
                                           onchange="previewImage(this, 'imagePreviewAdd')">
                                    <div class="mt-2">
                                        <img id="imagePreviewAdd" src="#" alt="Preview" style="max-width: 200px; display: none;" class="img-thumbnail">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_testimoni" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Testimoni
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/dashboard-produk.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/dashboard.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all star ratings
    document.querySelectorAll('.star-rating input[type="radio"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            let stars = this.parentElement.querySelectorAll('label');
            stars.forEach(star => star.style.color = '');
        });
    });

    // Get all filter elements
    const searchInput = document.getElementById('searchInput');
    const ratingFilter = document.getElementById('ratingFilter');
    const testimonials = document.querySelectorAll('.testimonial-card');

    function filterTestimonials() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedRating = ratingFilter.value;

        testimonials.forEach(card => {
            const name = card.querySelector('.testimonial-name').textContent.toLowerCase();
            const message = card.querySelector('.testimonial-text').textContent.toLowerCase();
            const stars = card.querySelectorAll('.testimonial-rating .text-warning').length;

            const matchesSearch = name.includes(searchTerm) || message.includes(searchTerm);
            const matchesRating = selectedRating === '' || stars === parseInt(selectedRating);

            if (matchesSearch && matchesRating) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Add event listeners
    searchInput.addEventListener('input', filterTestimonials);
    ratingFilter.addEventListener('change', filterTestimonials);
});

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>
