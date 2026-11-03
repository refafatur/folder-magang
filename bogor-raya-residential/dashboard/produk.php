<?php
session_start();
require_once '../includes/auth_helper.php';
checkUserStatus();

if (!isset($_SESSION['nama'])) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "db_bogor");
$permissions = json_decode($_SESSION['permissions'], true);

// Check if user has view permission
if (!isset($permissions['products']['view']) || $permissions['products']['view'] != 1) {
    header("Location: ../dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add product permission check
    if (isset($_POST['add_product'])) {
        if (!isset($permissions['products']['add']) || $permissions['products']['add'] != 1) {
            echo "Permission denied";
            exit();
        }
        $nama = $_POST['nama'];
        $deskripsi = $_POST['deskripsi'];
        $harga = $_POST['harga'];
        $lantai = $_POST['lantai'];
        $tampilan = $_POST['tampilan'];
        
        $foto = '';
        $file = '';

        // Handle foto upload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $fileType = mime_content_type($_FILES['foto']['tmp_name']);
            if (strpos($fileType, 'image') === 0) {
                $target_dir = "../uploads/produk/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $foto = $target_dir . basename($_FILES["foto"]["name"]);
                move_uploaded_file($_FILES["foto"]["tmp_name"], $foto);
            }
        }

        // Handle file upload
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $target_dir = "../uploads/produk/files/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file = $target_dir . basename($_FILES["file"]["name"]);
            move_uploaded_file($_FILES["file"]["tmp_name"], $file);
        }

        $query = "INSERT INTO tbl_produk (nama, deskripsi, harga, lantai, tampilan, foto, file, status) 
                  VALUES ('$nama', '$deskripsi', '$harga', '$lantai', '$tampilan', '$foto', '$file', 1)";
        if(mysqli_query($conn, $query)) {
            $_SESSION['sweet_alert'] = [
                'type' => 'success',
                'title' => 'Product Added Successfully',
                'text' => 'New product has been added to the database',
                'icon' => 'success',
                'product_name' => $nama
            ];
            
            // Trigger notification
            require_once '../includes/notifications.php';
            triggerNotification($conn, 'new_product', [
                'nama' => $nama,
                'id' => mysqli_insert_id($conn)
            ]);
            
            header("Location: produk.php");
            exit();
        } else {
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Failed to add product. Please try again.',
                'icon' => 'error'
            ];
            header("Location: produk.php");
            exit();
        }
    }

    // Update product permission check
    if (isset($_POST['update_product'])) {
        if (!isset($permissions['products']['edit']) || $permissions['products']['edit'] != 1) {
            echo "Permission denied";
            exit();
        }
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $deskripsi = $_POST['deskripsi'];
        $harga = $_POST['harga'];
        $lantai = $_POST['lantai'];
        $tampilan = $_POST['tampilan'];
        
        $query = "UPDATE tbl_produk SET 
                  nama='$nama', 
                  deskripsi='$deskripsi', 
                  harga='$harga', 
                  lantai='$lantai', 
                  tampilan='$tampilan'";

        // Handle foto upload for update
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_dir = "../uploads/produk/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $foto = $target_dir . basename($_FILES["foto"]["name"]);
            move_uploaded_file($_FILES["foto"]["tmp_name"], $foto);

            // Delete old photo
            $result = mysqli_query($conn, "SELECT foto FROM tbl_produk WHERE id=$id");
            $old_foto = mysqli_fetch_assoc($result)['foto'];
            if (file_exists($old_foto)) {
                unlink($old_foto);
            }

            $query .= ", foto='$foto'";
        }

        // Handle file upload for update
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $target_dir = "../uploads/produk/files/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file = $target_dir . basename($_FILES["file"]["name"]);
            move_uploaded_file($_FILES["file"]["tmp_name"], $file);

            // Delete old file
            $result = mysqli_query($conn, "SELECT file FROM tbl_produk WHERE id=$id");
            $old_file = mysqli_fetch_assoc($result)['file'];
            if (file_exists($old_file)) {
                unlink($old_file);
            }

            $query .= ", file='$file'";
        }

        $query .= " WHERE id=$id";
        if(mysqli_query($conn, $query)) {
            $_SESSION['sweet_alert'] = [
                'type' => 'success',
                'title' => 'Product Updated',
                'text' => 'The product has been successfully updated',
                'icon' => 'success',
                'product_name' => $nama
            ];
        } else {
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Failed to update product. Please try again.',
                'icon' => 'error'
            ];
        }
        require_once '../includes/notifications.php';
        triggerNotification($conn, 'edit_product', [
            'nama' => $nama
        ]);
        header("Location: produk.php");
        exit();
    }

    // Toggle status permission check
    if (isset($_POST['toggle_status'])) {
        if (!isset($permissions['products']['edit']) || $permissions['products']['edit'] != 1) {
            echo "Permission denied";
            exit();
        }
        $id = $_POST['id'];
        $status = $_POST['status'];
        $new_status = ($status == 1) ? 0 : 1;
        
        $query = "UPDATE tbl_produk SET status = $new_status WHERE id = $id";
        mysqli_query($conn, $query);
        header("Location: produk.php");
        exit();
    }

    if (isset($_POST['add_unit'])) {
        if (!isset($permissions['products']['add']) || $permissions['products']['add'] != 1) {
            echo "Permission denied";
            exit();
        }
        
        // Get product name for cluster
        $product_id = (int)$_POST['product_id'];
        $product_query = "SELECT nama FROM tbl_produk WHERE id = $product_id";
        $product_result = mysqli_query($conn, $product_query);
        $product_data = mysqli_fetch_assoc($product_result);
        $cluster = mysqli_real_escape_string($conn, $product_data['nama']); // Use product name as cluster
        
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $harga = mysqli_real_escape_string($conn, $_POST['harga']);
        
        $photos = [];
        $main_photo_index = isset($_POST['main_photo']) ? (int)$_POST['main_photo'] : 0;
        
        if (isset($_FILES['foto']) && is_array($_FILES['foto']['tmp_name'])) {
            $uploaded_files = $_FILES['foto'];
            
            foreach ($uploaded_files['tmp_name'] as $index => $tmp_name) {
                if ($uploaded_files['error'][$index] === 0) {
                    $target_dir = "../uploads/unit/";
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    
                    $file_ext = pathinfo($uploaded_files["name"][$index], PATHINFO_EXTENSION);
                    $unique_filename = uniqid() . '_' . time() . '_' . $index . '.' . $file_ext;
                    $target_file = $target_dir . $unique_filename;
                    
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $photos[] = $target_file;
                    }
                }
            }
            
            // Reorder photos based on main photo selection
            if (!empty($photos) && isset($photos[$main_photo_index])) {
                $main_photo = $photos[$main_photo_index];
                unset($photos[$main_photo_index]);
                array_unshift($photos, $main_photo);
            }
            
            $foto_json = json_encode($photos);
        }
        
        $lantai = mysqli_real_escape_string($conn, $_POST['lantai']);
        $ukuran = mysqli_real_escape_string($conn, $_POST['ukuran']);
        $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
        $luas_tanah = mysqli_real_escape_string($conn, $_POST['luas_tanah']);
        $luas_bangunan = mysqli_real_escape_string($conn, $_POST['luas_bangunan']);
        $stok = (int)$_POST['stok'];
        
        $query = "INSERT INTO tbl_unit (cluster, name, product_id, harga, foto, lantai, luas_tanah, luas_bangunan, stok, deskripsi) 
                  VALUES ('$cluster', '$name', $product_id, '$harga', '$foto_json', '$lantai', '$luas_tanah', '$luas_bangunan', $stok, '$deskripsi')";
        if(mysqli_query($conn, $query)) {
            $_SESSION['sweet_alert'] = [
                'type' => 'success',
                'title' => 'Unit Added',
                'text' => 'New unit has been successfully added',
                'icon' => 'success',
                'unit_name' => $name
            ];
        } else {
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Failed to add unit. Please try again.',
                'icon' => 'error'
            ];
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?product=" . $product_id . "#unitsModal" . $product_id);
        exit();
    }

    if (isset($_POST['update_unit'])) {
        if (!isset($permissions['products']['edit']) || $permissions['products']['edit'] != 1) {
            echo "Permission denied";
            exit();
        }
        
        // Get product name for cluster
        $product_id = (int)$_POST['product_id'];
        $product_query = "SELECT nama FROM tbl_produk WHERE id = $product_id";
        $product_result = mysqli_query($conn, $product_query);
        $product_data = mysqli_fetch_assoc($product_result);
        $cluster = mysqli_real_escape_string($conn, $product_data['nama']); // Use product name as cluster
        
        $unit_id = (int)$_POST['unit_id'];
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $harga = mysqli_real_escape_string($conn, $_POST['harga']);
        
        $lantai = mysqli_real_escape_string($conn, $_POST['lantai']);
        $ukuran = mysqli_real_escape_string($conn, $_POST['ukuran']);
        $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
        $luas_tanah = mysqli_real_escape_string($conn, $_POST['luas_tanah']);
        $luas_bangunan = mysqli_real_escape_string($conn, $_POST['luas_bangunan']);
        $stok = (int)$_POST['stok'];
        
        $existing_photos = json_decode($_POST['existing_photos'], true) ?: [];
        $removed_photos = json_decode($_POST['removed_photos'], true) ?: [];
        
        // Delete removed photos from server
        foreach ($removed_photos as $photo) {
            if (file_exists($photo)) {
                unlink($photo);
            }
        }
        
        // Handle new photos
        $new_photos = [];
        if (isset($_FILES['foto']) && is_array($_FILES['foto']['tmp_name'])) {
            foreach ($_FILES['foto']['tmp_name'] as $index => $tmp_name) {
                if ($_FILES['foto']['error'][$index] === 0) {
                    $file_ext = pathinfo($_FILES['foto']['name'][$index], PATHINFO_EXTENSION);
                    $unique_filename = uniqid() . '_' . time() . '_' . $index . '.' . $file_ext;
                    $target_file = "../uploads/unit/" . $unique_filename;
                    
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $new_photos[] = $target_file;
                    }
                }
            }
        }
        
        // Combine existing and new photos
        $final_photos = array_merge($existing_photos, $new_photos);
        
        // Handle main photo selection
        $main_photo_index = isset($_POST['main_photo_existing']) ? (int)$_POST['main_photo_existing'] : 0;
        if (!empty($final_photos) && isset($final_photos[$main_photo_index])) {
            $main_photo = $final_photos[$main_photo_index];
            $final_photos = array_diff($final_photos, [$main_photo]);
            array_unshift($final_photos, $main_photo);
        }
        
        $foto_json = json_encode(array_values($final_photos));
        
        // Update the rest of the unit data
        $query = "UPDATE tbl_unit SET 
                  cluster='$cluster', 
                  name='$name', 
                  harga='$harga', 
                  lantai='$lantai', 
                  luas_tanah='$luas_tanah', 
                  luas_bangunan='$luas_bangunan', 
                  stok=$stok, 
                  deskripsi='$deskripsi',
                  foto='$foto_json'
                  WHERE id=$unit_id";
                  
        if(mysqli_query($conn, $query)) {
            $_SESSION['sweet_alert'] = [
                'type' => 'success',
                'title' => 'Unit Updated',
                'text' => 'The unit has been successfully updated',
                'icon' => 'success',
                'unit_name' => $name
            ];
        } else {
            $_SESSION['sweet_alert'] = [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Failed to update unit. Please try again.',
                'icon' => 'error'
            ];
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?product=" . $product_id . "#unitsModal" . $product_id);
        exit();
    }
}

if (isset($_GET['delete_unit'])) {
    if (!isset($permissions['products']['delete']) || $permissions['products']['delete'] != 1) {
        echo "Permission denied";
        exit();
    }
    $unit_id = (int)$_GET['delete_unit'];
    $product_id = (int)$_GET['product'];
    $query = "DELETE FROM tbl_unit WHERE id=$unit_id";
    if(mysqli_query($conn, $query)) {
        $_SESSION['sweet_alert'] = [
            'type' => 'success',
            'title' => 'Unit Deleted',
            'text' => 'The unit has been permanently removed',
            'icon' => 'success'
        ];
    } else {
        $_SESSION['sweet_alert'] = [
            'type' => 'error',
            'title' => 'Error',
            'text' => 'Failed to delete unit. Please try again.',
            'icon' => 'error'
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?product=" . $product_id . "#unitsModal" . $product_id);
    exit();
}

// Delete permission check
if (isset($_GET['delete'])) {
    if (!isset($permissions['products']['delete']) || $permissions['products']['delete'] != 1) {
        echo "Permission denied";
        exit();
    }
    $id = $_GET['delete'];
    $result = mysqli_query($conn, "SELECT foto FROM tbl_produk WHERE id=$id");
    $foto = mysqli_fetch_assoc($result)['foto'];
    if (file_exists($foto)) {
        unlink($foto);
    }
    $stmt = mysqli_prepare($conn, "SELECT nama FROM tbl_produk WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    
    $query = "DELETE FROM tbl_produk WHERE id=$id";
    if(mysqli_query($conn, $query)) {
        $_SESSION['sweet_alert'] = [
            'type' => 'success',
            'title' => 'Product Deleted',
            'text' => 'The product has been permanently removed',
            'icon' => 'success'
        ];
    } else {
        $_SESSION['sweet_alert'] = [
            'type' => 'error',
            'title' => 'Error',
            'text' => 'Failed to delete product. Please try again.',
            'icon' => 'error'
        ];
    }
    require_once '../includes/notifications.php';
    triggerNotification($conn, 'delete_product', [
        'nama' => $product['nama']
    ]);
    header("Location: produk.php");
    exit();
}

// Filter and Search Logic
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filterPrice = isset($_GET['filterPrice']) ? $_GET['filterPrice'] : '';
$filterFloor = isset($_GET['filterFloor']) ? $_GET['filterFloor'] : '';

$query = "SELECT * FROM tbl_produk WHERE 1=1";

if ($search) {
    $query .= " AND nama LIKE '%$search%'";
}

if ($filterPrice) {
    if ($filterPrice == '0-500000000') {
        $query .= " AND CAST(REPLACE(harga, '.', '') AS DECIMAL) <= 500000000";
    } elseif ($filterPrice == '500000000-1000000000') {
        $query .= " AND CAST(REPLACE(harga, '.', '') AS DECIMAL) BETWEEN 500000000 AND 1000000000";
    } elseif ($filterPrice == '1000000000+') {
        $query .= " AND CAST(REPLACE(harga, '.', '') AS DECIMAL) > 1000000000";
    }
}

if ($filterFloor) {
    $query .= " AND lantai = $filterFloor";
}

$products = mysqli_query($conn, $query);
if (!$products) {
    echo "Error: " . mysqli_error($conn);
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
    <link href="../assets/css/produk.css" rel="stylesheet">
    <style>
.horizontal-photo-grid {
    display: flex;
    flex-direction: row;
    overflow-x: auto;
    gap: 15px;
    padding: 10px 0;
    scrollbar-width: thin;
    -webkit-overflow-scrolling: touch;
}

.horizontal-photo-grid .photo-box {
    flex: 0 0 200px;
    height: 200px;
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #eee;
}

.horizontal-photo-grid .photo-content {
    width: 100%;
    height: 100%;
}

.horizontal-photo-grid img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.horizontal-photo-grid .photo-controls {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 8px;
    background: rgba(255,255,255,0.9);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Styling untuk scrollbar */
.horizontal-photo-grid::-webkit-scrollbar {
    height: 6px;
}

.horizontal-photo-grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.horizontal-photo-grid::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.horizontal-photo-grid::-webkit-scrollbar-thumb:hover {
    background: #555;
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
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-building me-2"></i>Products Management</h2>
            <?php if (isset($permissions['products']['add']) && $permissions['products']['add'] == 1): ?>
            <button class="btn btn-primary add-product-btn" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fas fa-plus-circle me-2"></i>Add New Product
            </button>
            <?php endif; ?>
        </div>
        
        <!-- Modal Add Product -->
        <div class="modal fade" id="addProductModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Product</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form method="POST" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Property Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                            <input type="text" class="form-control" name="nama" required>
                                            <div class="invalid-feedback">Please enter property name</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Starting Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control price-input" name="harga" required>
                                            <div class="invalid-feedback">Please enter valid price</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Floor</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                            <input type="number" class="form-control" name="lantai" min="1" required>
                                            <div class="invalid-feedback">Please enter floor number</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">View Type</label>
                                        <select class="form-select" name="tampilan" required>
                                            <option value="">Select view</option>
                                            <option value="City View">City View</option>
                                            <option value="Garden View">Garden View</option>
                                            <option value="Mountain View">Mountain View</option>
                                            <option value="Pool View">Pool View</option>
                                        </select>
                                        <div class="invalid-feedback">Please select view type</div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Description</label>
                                        <textarea class="form-control" name="deskripsi" rows="4" required></textarea>
                                        <div class="invalid-feedback">Please enter description</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Property Photo</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-camera"></i></span>
                                            <input type="file" class="form-control" name="foto" accept="image/*" required onchange="previewProductPhoto(this)">
                                        </div>
                                        <div class="invalid-feedback">Please select a photo</div>
                                        <div id="productPhotoPreview" class="mt-3" style="display:none;">
                                            <div class="position-relative" style="max-width: 300px;">
                                                <img src="" class="img-fluid rounded" style="width: 100%; height: auto;">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                                        onclick="clearProductPhoto()">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Additional Documents</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                                            <input type="file" class="form-control" name="file">
                                        </div>
                                        <small class="text-muted">Optional. Supported: PDF, DOC, DOCX</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                                <button type="submit" name="add_product" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Save Property
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Add Product -->
        
        <div class="filter-section mb-4">
            <form class="row g-3" method="GET">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search properties..." 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="filterFloor" class="form-select">
                        <option value="">Select Floor</option>
                        <?php
                        $floors = range(1, 10);
                        foreach ($floors as $floor) {
                            $selected = (isset($_GET['filterFloor']) && $_GET['filterFloor'] == $floor) ? 'selected' : '';
                            echo "<option value='$floor' $selected>Floor $floor</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="filterView" class="form-select">
                        <option value="">Select View</option>
                        <?php
                        $views = ['City View', 'Garden View', 'Mountain View', 'Pool View'];
                        foreach ($views as $view) {
                            $selected = (isset($_GET['filterView']) && $_GET['filterView'] == $view) ? 'selected' : '';
                            echo "<option value='$view' $selected>$view</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="produk.php" class="btn btn-light">
                            <i class="fas fa-sync-alt me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card h-100">
                    <div class="product-image-container">
                        <img src="<?php echo $product['foto']; ?>" class="product-image" alt="<?php echo htmlspecialchars($product['nama']); ?>">
                        <div class="product-overlay">
                        <div class="product-actions">
                                <?php if (isset($permissions['products']['edit']) && $permissions['products']['edit'] == 1): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="status" value="<?php echo $product['status']; ?>">
                                        <button type="submit" name="toggle_status" 
                                                class="action-btn <?php echo $product['status'] == 1 ? 'active' : ''; ?>"
                                                title="<?php echo $product['status'] == 1 ? 'Hide Property' : 'Show Property'; ?>">
                                            <i class="fas <?php echo $product['status'] == 1 ? 'fa-eye' : 'fa-eye-slash'; ?>"></i>
                                        </button>
                                    </form>
                                    <button class="action-btn edit" data-bs-toggle="modal" 
                                            data-bs-target="#editModal<?php echo $product['id']; ?>"
                                            title="Edit Property">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                <?php endif; ?>
                                <?php if (isset($permissions['products']['delete']) && $permissions['products']['delete'] == 1): ?>
                                    <button class="action-btn delete" 
                                            onclick="confirmDelete(<?php echo $product['id']; ?>)"
                                            title="Delete Property">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                                <button class="action-btn view" data-bs-toggle="modal" 
                                        data-bs-target="#viewModal<?php echo $product['id']; ?>"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="product-content">
                        <h5 class="product-title"><?php echo htmlspecialchars($product['nama']); ?></h5>
                        <div class="product-price">
                            <i class="fas fa-tag"></i>
                            <span>Rp<?php echo number_format(str_replace('.', '', $product['harga']), 0, ',', '.'); ?></span>
                        </div>
                        <div class="product-specs">
                            <div class="spec-item">
                                <i class="fas fa-building"></i>
                                <span><?php echo htmlspecialchars($product['lantai']); ?> Floor</span>
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-eye"></i>
                                <span><?php echo htmlspecialchars($product['tampilan']); ?></span>
                            </div>
                        </div>
                        <p class="product-description"><?php echo mb_strimwidth(htmlspecialchars($product['deskripsi']), 0, 100, "..."); ?></p>
                        <button class="btn-units-modern" data-bs-toggle="modal" data-bs-target="#unitsModal<?php echo $product['id']; ?>">
                            <i class="fas fa-building me-2"></i>
                            <span>Manage Units</span>
                            <?php 
                            $unit_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM tbl_unit WHERE product_id = " . $product['id']));
                            if($unit_count > 0): 
                            ?>
                            <span class="unit-badge"><?php echo $unit_count; ?></span>
                            <?php endif; ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Units Modal -->
            <div class="modal fade unit-modal" id="unitsModal<?php echo $product['id']; ?>" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-building me-2"></i>
                                <span><?php echo htmlspecialchars($product['nama']); ?></span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="unit-dashboard">
                                <div class="unit-stats-bar">
                                    <?php
                                    $total_units = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM tbl_unit WHERE product_id = " . $product['id']));
                                    $available_units = mysqli_fetch_assoc(mysqli_query($conn, 
                                        "SELECT SUM(stok) as total FROM tbl_unit WHERE product_id = " . $product['id']
                                    ))['total'] ?? 0;
                                    ?>
                                    <div class="stat-card">
                                        <i class="fas fa-home"></i>
                                        <div class="stat-info">
                                            <span class="stat-label">Total Units</span>
                                            <span class="stat-value"><?php echo $total_units; ?></span>
                                        </div>
                                    </div>
                                    <div class="stat-card">
                                        <i class="fas fa-check-circle"></i>
                                        <div class="stat-info">
                                            <span class="stat-label">Available</span>
                                            <span class="stat-value"><?php echo $available_units; ?></span>
                                        </div>
                                    </div>
                                    <?php if (isset($permissions['products']['add']) && $permissions['products']['add'] == 1): ?>
                                    <button class="btn-add-unit" data-bs-toggle="modal" 
                                            data-bs-target="#addUnitModal<?php echo $product['id']; ?>">
                                        <i class="fas fa-plus-circle me-2"></i>Add New Unit
                                    </button>
                                    <?php endif; ?>
                                </div>

                                <div class="units-grid">
                                    <?php
                                    $units_query = "SELECT * FROM tbl_unit WHERE product_id = " . $product['id'];
                                    $units_result = mysqli_query($conn, $units_query);
                                    
                                    // Store all units in array first
                                    $units = [];
                                    while ($unit = mysqli_fetch_assoc($units_result)) {
                                        $units[] = $unit;
                                    }
                                    
                                    // Display unit cards
                                    foreach ($units as $unit):
                                        $photos = json_decode($unit['foto'], true) ?: [];
                                        $main_photo = !empty($photos) ? $photos[0] : '';
                                    ?>
                                    <div class="unit-card">
                                        <div class="unit-card-header">
                                            <div class="unit-image">
                                                <?php 
                                                $photos = json_decode($unit['foto'], true) ?: [];
                                                $main_photo = !empty($photos) ? $photos[0] : '';
                                                if ($main_photo): 
                                                ?>
                                                <img src="<?php echo htmlspecialchars($main_photo); ?>" alt="Unit Photo">
                                                <?php if(count($photos) > 1): ?>
                                                    <div class="photo-count">
                                                        <i class="fas fa-images"></i> +<?php echo count($photos) - 1; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php else: ?>
                                                <div class="no-image">
                                                    <i class="fas fa-home"></i>
                                                </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="unit-status <?php echo $unit['stok'] > 0 ? 'available' : 'sold'; ?>">
                                                <?php echo $unit['stok'] > 0 ? 'Available' : 'Sold Out'; ?>
                                            </div>
                                        </div>
                                        <div class="unit-card-body">
                                            <h6 class="unit-name"><?php echo htmlspecialchars($unit['name']); ?></h6>
                                            <div class="unit-price">
                                                <i class="fas fa-tag"></i>
                                                <span>Rp<?php echo number_format(str_replace('.', '', $unit['harga']), 0, ',', '.'); ?></span>
                                            </div>
                                            <div class="unit-specs">
                                                <div class="spec">
                                                    <i class="fas fa-ruler-combined"></i>
                                                    <span><?php echo $unit['luas_tanah']; ?> m²</span>
                                                </div>
                                                <div class="spec">
                                                    <i class="fas fa-building"></i>
                                                    <span><?php echo $unit['luas_bangunan']; ?> m²</span>
                                                </div>
                                            </div>
                                            <div class="unit-actions">
                                                <button class="btn-unit-action view" data-bs-toggle="modal" 
                                                        data-bs-target="#viewUnitModal<?php echo $unit['id']; ?>"
                                                        title="View Unit Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if (isset($permissions['products']['edit']) && $permissions['products']['edit'] == 1): ?>
                                                <button class="btn-unit-action edit" data-bs-toggle="modal"
                                                        data-bs-target="#editUnitModal<?php echo $unit['id']; ?>"
                                                        title="Edit Unit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php endif; ?>
                                                <?php if (isset($permissions['products']['delete']) && $permissions['products']['delete'] == 1): ?>
                                                <button class="btn-unit-action delete" 
                                                        onclick="deleteUnit(<?php echo $unit['id']; ?>, <?php echo $product['id']; ?>)"
                                                        title="Delete Unit">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Unit Modal -->
            <div class="modal fade" id="addUnitModal<?php echo $product['id']; ?>" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Unit</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal" data-bs-target="#unitsModal<?php echo $product['id']; ?>"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form method="POST" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Unit Name</label>
                                            <input type="text" class="form-control" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Price</label>
                                            <input type="text" class="form-control price-input" name="harga" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Stock</label>
                                            <input type="number" class="form-control" name="stok" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Photos</label>
                                            <input type="file" class="form-control unit-photos" name="foto[]" multiple accept="image/*" required>
                                            <div class="photo-preview-container mt-3">
                                                <div class="horizontal-photo-grid" id="photoGrid<?php echo $product['id']; ?>"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Floor</label>
                                            <input type="number" class="form-control" name="lantai" min="1" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Land Area (m²)</label>
                                            <input type="number" class="form-control" name="luas_tanah" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Building Area (m²)</label>
                                            <input type="number" class="form-control" name="luas_bangunan" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Description</label>
                                            <textarea class="form-control" name="deskripsi" rows="4" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#unitsModal<?php echo $product['id']; ?>">Cancel</button>
                                    <button type="submit" name="add_unit" class="btn btn-primary">Add Unit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Generate modals for each unit
            foreach ($units as $unit):
            ?>
            <!-- Edit Unit Modal -->
            <div class="modal fade" id="editUnitModal<?php echo $unit['id']; ?>" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Unit</h5>
                            <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#unitsModal<?php echo $product['id']; ?>"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="unit_id" value="<?php echo $unit['id']; ?>">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="existing_photos" value='<?php echo htmlspecialchars($unit['foto']); ?>'>
                                <input type="hidden" name="removed_photos" value="[]">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Unit Name</label>
                                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($unit['name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Price</label>
                                            <input type="text" class="form-control price-input" name="harga" value="<?php echo htmlspecialchars($unit['harga']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Stock</label>
                                            <input type="number" class="form-control" name="stok" value="<?php echo htmlspecialchars($unit['stok']); ?>" min="0" required>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Photo Management</label>
                                            <div class="photo-grid mb-3">
                                                <?php 
                                                $photos = json_decode($unit['foto'], true) ?: [];
                                                foreach($photos as $index => $photo): 
                                                ?>
                                                <div class="photo-box">
                                                    <div class="photo-content">
                                                        <img src="<?php echo htmlspecialchars($photo); ?>" alt="Unit photo">
                                                        <div class="photo-controls">
                                                            <div class="form-check">
                                                                <input type="radio" class="form-check-input" name="main_photo_existing" 
                                                                       value="<?php echo $index; ?>" <?php echo $index === 0 ? 'checked' : ''; ?>>
                                                                <label class="form-check-label">Tampilkan</label>
                                                            </div>
                                                            <button type="button" class="btn btn-danger btn-sm remove-photo" 
                                                                    data-photo="<?php echo htmlspecialchars($photo); ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                            
                                            <div class="new-photos mt-3">
                                                <label class="form-label">Add New Photos</label>
                                                <input type="file" class="form-control" name="foto[]" multiple accept="image/*">
                                                <div class="new-photos-preview mt-2"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Floor</label>
                                            <input type="number" class="form-control" name="lantai" value="<?php echo htmlspecialchars($unit['lantai']); ?>" min="1" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Land Area (m²)</label>
                                            <input type="number" class="form-control" name="luas_tanah" value="<?php echo htmlspecialchars($unit['luas_tanah']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Building Area (m²)</label>
                                            <input type="number" class="form-control" name="luas_bangunan" value="<?php echo htmlspecialchars($unit['luas_bangunan']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Description</label>
                                            <textarea class="form-control" name="deskripsi" rows="4" required><?php echo htmlspecialchars($unit['deskripsi']); ?></textarea>
                                        </div>
                                    </div>
                                </div>

<style>
.photo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
}

.photo-box {
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #eee;
}

.photo-content {
    position: relative;
    width: 100%;
    height: 100%;
}

.photo-content img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-controls {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 8px;
    background: rgba(255,255,255,0.9);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: opacity 0.2s;
    opacity: 0;
}

.photo-box:hover .photo-controls {
    opacity: 1;
}

.new-photos-preview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 10px;
}
</style>
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#unitsModal<?php echo $product['id']; ?>">Cancel</button>
                                    <button type="submit" name="update_unit" class="btn btn-warning">Update Unit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Unit Modal -->
            <div class="modal fade" id="viewUnitModal<?php echo $unit['id']; ?>" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Unit Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal" data-bs-target="#unitsModal<?php echo $product['id']; ?>"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div id="unitPhotosCarousel<?php echo $unit['id']; ?>" class="carousel slide carousel-dark" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            <?php 
                                            $photos = json_decode($unit['foto'], true) ?: [];
                                            foreach($photos as $index => $photo): 
                                            ?>
                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <img src="<?php echo htmlspecialchars($photo); ?>" class="d-block w-100" style="height: 400px; object-fit: cover;">
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php if(count($photos) > 1): ?>
                                        <button class="carousel-control-prev custom-carousel-control" type="button" 
                                            data-bs-target="#unitPhotosCarousel<?php echo $unit['id']; ?>" 
                                            data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                        </button>
                                        <button class="carousel-control-next custom-carousel-control" type="button" 
                                            data-bs-target="#unitPhotosCarousel<?php echo $unit['id']; ?>" 
                                            data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                        </button>

                                        <style>
                                        .custom-carousel-control {
                                            background: rgba(255, 255, 255, 0.8);
                                            border-radius: 50%;
                                            width: 40px;
                                            height: 40px;
                                            position: absolute;
                                            top: 50%;
                                            transform: translateY(-50%);
                                            border: none;
                                            box-shadow: black;
                                            transition: all 0.3s ease;
                                        }

                                        .custom-carousel-control:hover {
                                            background: rgba(255, 255, 255, 0.9);
                                            box-shadow: black;
                                        }

                                        .carousel-control-prev {
                                            left: 10px;
                                        }

                                        .carousel-control-next {
                                            right: 10px;
                                        }

                                        .carousel-control-prev-icon,
                                        .carousel-control-next-icon {
                                            filter: invert(1) grayscale(100);
                                        }
                                        </style>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h4><?php echo htmlspecialchars($unit['name']); ?></h4>
                                    <p class="text-muted mb-2">Cluster: <?php echo htmlspecialchars($unit['cluster']); ?></p>
                                    <div class="unit-details mt-3">
                                        <p><strong>Price:</strong> Rp<?php echo number_format(str_replace('.', '', $unit['harga']), 0, ',', '.'); ?></p>
                                        <p><strong>Stock:</strong> <?php echo htmlspecialchars($unit['stok']); ?> units</p>
                                        <p><strong>Floor:</strong> <?php echo htmlspecialchars($unit['lantai']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">Specifications</h5>
                                            <div class="specs-grid">
                                                <div class="spec-item">
                                                    <i class="fas fa-ruler-combined"></i>
                                                    <div>
                                                        <small>Land Area</small>
                                                        <p><?php echo htmlspecialchars($unit['luas_tanah']); ?> m²</p>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <i class="fas fa-building"></i>
                                                    <div>
                                                        <small>Building Area</small>
                                                        <p><?php echo htmlspecialchars($unit['luas_bangunan']); ?> m²</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <h5>Description</h5>
                                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($unit['deskripsi'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?php echo $product['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Property</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form method="POST" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Property Name</label>
                                            <input type="text" class="form-control" name="nama" value="<?php echo htmlspecialchars($product['nama']); ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Price</label>
                                            <input type="text" class="form-control price-input" name="harga" value="<?php echo htmlspecialchars($product['harga']); ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Floor</label>
                                            <input type="number" class="form-control" name="lantai" value="<?php echo htmlspecialchars($product['lantai']); ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">View Type</label>
                                            <select class="form-select" name="tampilan" required>
                                                <?php 
                                                $views = ['City View', 'Garden View', 'Mountain View', 'Pool View'];
                                                foreach ($views as $view) {
                                                    $selected = ($product['tampilan'] == $view) ? 'selected' : '';
                                                    echo "<option value='$view' $selected>$view</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Description</label>
                                            <textarea class="form-control" name="deskripsi" rows="4" required><?php echo htmlspecialchars($product['deskripsi']); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Update Photo</label>
                                            <input type="file" class="form-control" name="foto" accept="image/*">
                                            <?php if ($product['foto']): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo $product['foto']; ?>" class="img-thumbnail" style="height: 100px">
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Update Documents</label>
                                            <input type="file" class="form-control" name="file">
                                            <?php if ($product['file']): ?>
                                            <small class="text-muted">Current file: <?php echo basename($product['file']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="update_product" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i>Update Property
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div class="modal fade" id="viewModal<?php echo $product['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title"><i class="fas fa-eye me-2"></i>View Property Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <img src="<?php echo $product['foto']; ?>" class="img-fluid rounded" alt="Property Image">
                                </div>
                                <div class="col-md-6">
                                    <h4 class="mb-3"><?php echo htmlspecialchars($product['nama']); ?></h4>
                                    <div class="property-info">
                                        <p><strong>Price:</strong> Rp<?php echo number_format(str_replace('.', '', $product['harga']), 0, ',', '.'); ?></p>
                                        <p><strong>Floor:</strong> <?php echo htmlspecialchars($product['lantai']); ?></p>
                                        <p><strong>View:</strong> <?php echo htmlspecialchars($product['tampilan']); ?></p>
                                        <p><strong>Status:</strong> 
                                            <span class="badge <?php echo $product['status'] == 1 ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $product['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </p>
                                    </div>
                                    <?php if ($product['file']): ?>
                                    <div class="mt-3">
                                        <a href="<?php echo $product['file']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-file-download me-1"></i>Download Documents
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <h5 class="mb-3">Description</h5>
                                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($product['deskripsi'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/dashboard-produk.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/dashboard.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Define confirmDelete in global scope
    window.confirmDelete = function(productId) {
        Swal.fire({
            title: 'Delete Property?',
            text: "This action cannot be undone and will remove all associated units!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary me-3'
            },
            buttonsStyling: false,
            padding: '2em'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `?delete=${productId}`;
            }
        });
    };
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add deleteUnit function to global scope
    window.deleteUnit = function(unitId, productId) {
        Swal.fire({
            title: 'Delete Unit?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary me-3'
            },
            buttonsStyling: false,
            padding: '2em'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `?delete_unit=${unitId}&product=${productId}`;
            }
        });
    };
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for product parameter in URL and open corresponding unit management modal
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('product');
    const hash = window.location.hash;
    
    if (productId && hash.startsWith('#unitsModal')) {
        const modalElement = document.querySelector(`#unitsModal${productId}`);
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            
            // Clean the URL without reloading the page
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }
});
</script>
<script>
// Image preview handler for product photo
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.classList.remove('d-none');
            preview.querySelector('img').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Enhanced photo preview handler for unit photos
function previewUnitPhotos(input, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        const photoGrid = document.createElement('div');
        photoGrid.className = 'horizontal-photo-grid';
        
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const photoBox = document.createElement('div');
                photoBox.className = 'photo-box';
                photoBox.innerHTML = `
                    <div class="photo-content">
                        <img src="${e.target.result}" alt="Preview">
                        <div class="photo-controls">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" 
                                    name="main_photo" value="${index}" 
                                    ${index === 0 ? 'checked' : ''}>
                                <label class="form-check-label">Set as Main</label>
                            </div>
                        </div>
                    </div>
                `;
                photoGrid.appendChild(photoBox);
            };
            reader.readAsDataURL(file);
        });
        container.appendChild(photoGrid);
    }
}

// Initialize all file inputs when document is ready
document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = document.querySelectorAll('.unit-photos');
    fileInputs.forEach(input => {
        const productId = input.closest('form').querySelector('[name="product_id"]').value;
        const containerId = 'photoGrid' + productId;
        input.addEventListener('change', (e) => previewUnitPhotos(e.target, containerId));
    });

    // Handle modal switches
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function(event) {
            // Find and show the units modal if it exists
            const productId = modal.querySelector('[name="product_id"]')?.value;
            if (productId) {
                const unitsModal = document.querySelector(`#unitsModal${productId}`);
                if (unitsModal) {
                    const bsUnitsModal = new bootstrap.Modal(unitsModal);
                    bsUnitsModal.show();
                }
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const ModalManager = {
        init() {
            this.setupModals();
            this.setupModalNavigation();
            this.handleBodyScroll();
        },

        setupModals() {
            // Handle all modals
            document.querySelectorAll('.modal').forEach(modal => {
                const closeBtn = modal.querySelector('.btn-close');
                const cancelBtn = modal.querySelector('button[type="button"].btn-light');
                
                // Handle close and cancel buttons
                [closeBtn, cancelBtn].forEach(btn => {
                    if (btn) {
                        btn.addEventListener('click', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            const currentModal = bootstrap.Modal.getInstance(modal);
                            if (currentModal) {
                                currentModal.hide();
                                
                                // If this is a unit modal (add/edit/view), show the parent units modal
                                if (modal.id.match(/^(addUnitModal|editUnitModal|viewUnitModal)/)) {
                                    const productId = modal.querySelector('[name="product_id"]')?.value;
                                    if (productId) {
                                        setTimeout(() => {
                                            const unitsModal = document.querySelector(`#unitsModal${productId}`);
                                            if (unitsModal) {
                                                const unitsModalInstance = new bootstrap.Modal(unitsModal);
                                                unitsModalInstance.show();
                                            }
                                        }, 150);
                                    }
                                }
                            }
                        });
                    }
                });
            });
        },

        setupModalNavigation() {
            // Clean up modal effects when any modal is hidden
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    // Only clean up if no other modals are visible
                    const visibleModals = document.querySelectorAll('.modal.show');
                    if (visibleModals.length === 0) {
                        document.body.classList.remove('modal-open');
                        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                    }
                });
            });

            // Handle ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const visibleModals = Array.from(document.querySelectorAll('.modal.show'));
                    if (visibleModals.length > 0) {
                        const topModal = visibleModals[visibleModals.length - 1];
                        const modalInstance = bootstrap.Modal.getInstance(topModal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }
                }
            });
        },

        handleBodyScroll() {
            document.querySelectorAll('.modal').forEach(modal => {
                // Ketika modal dibuka
                modal.addEventListener('show.bs.modal', () => {
                    setTimeout(() => {
                        const isModalOpen = document.querySelector('.modal.show');
                        if (isModalOpen) {
                            document.body.style.overflow = 'auto';
                            document.body.style.paddingRight = '0';
                        }
                    }, 10);
                });

                // Ketika modal ditutup
                modal.addEventListener('hidden.bs.modal', () => {
                    setTimeout(() => {
                        const isAnyModalOpen = document.querySelector('.modal.show');
                        if (!isAnyModalOpen) {
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        }
                    }, 10);
                });
            });

            // Override Bootstrap's modal backdrop click handler
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('modal')) {
                    const modalInstance = bootstrap.Modal.getInstance(e.target);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
            });
        }
    };

    // Initialize ModalManager
    ModalManager.init();
});

// Prevent URL updates for modals
const modals = document.querySelectorAll('.modal');
modals.forEach(modal => {
    modal.addEventListener('show.bs.modal', function (e) {
        history.pushState(null, '', window.location.pathname);
    });
});
</script>
<script>
// Photo gallery modal handler
document.addEventListener('DOMContentLoaded', function() {
    // Handle photo modal opening
    document.querySelectorAll('.photo-gallery').forEach(gallery => {
        gallery.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get target modal ID from data attribute
            const modalId = this.dataset.bsTarget;
            const modal = document.querySelector(modalId);
            
            // Store the parent unit modal instance
            const unitModal = this.closest('.unit-modal');
            const unitModalInstance = bootstrap.Modal.getInstance(unitModal);
            if (unitModalInstance) {
                unitModalInstance.hide();
            }
            
            // Show photo modal
            const photoModal = new bootstrap.Modal(modal);
            photoModal.show();
            
            // Initialize carousel
            const carousel = modal.querySelector('.carousel');
            const bsCarousel = new bootstrap.Carousel(carousel, {
                interval: false,
                keyboard: true
            });
        });
    });

    // Handle photo modal closing
    document.querySelectorAll('.photo-modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function(e) {
            // Get product ID and show unit modal
            const productId = this.querySelector('[data-bs-target]')?.dataset.bsTarget;
            if (productId) {
                const unitModal = document.querySelector(productId);
                if (unitModal) {
                    const bsModal = new bootstrap.Modal(unitModal);
                    bsModal.show();
                }
            }
        });
    });

    // Thumbnail navigation
    function selectPhoto(unitId, index) {
        const carousel = document.querySelector(`#photoCarousel${unitId}`);
        if (!carousel) return;

        const bsCarousel = bootstrap.Carousel.getInstance(carousel);
        if (!bsCarousel) return;

        // Update carousel position
        bsCarousel.to(index);

        // Update thumbnail active states
        const thumbnails = carousel.closest('.gallery-container').querySelectorAll('.thumbnail');
        thumbnails.forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
    }

    // Make selectPhoto available globally
    window.selectPhoto = selectPhoto;

    // Handle carousel slide events
    document.querySelectorAll('[id^="photoCarousel"]').forEach(carousel => {
        carousel.addEventListener('slid.bs.carousel', event => {
            const thumbnails = carousel.closest('.gallery-container').querySelectorAll('.thumbnail');
            thumbnails.forEach((thumb, i) => {
                thumb.classList.toggle('active', i === event.to);
            });
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle photo deletion in edit unit modal
    document.querySelectorAll('[id^="editUnitModal"]').forEach(modal => {
        const form = modal.querySelector('form');
        const removedPhotosInput = form.querySelector('[name="removed_photos"]');
        const existingPhotosInput = form.querySelector('[name="existing_photos"]');

        // Handle remove photo button clicks
        modal.querySelectorAll('.remove-photo').forEach(button => {
            button.addEventListener('click', function() {
                const photoBox = this.closest('.photo-box');
                const photoUrl = this.dataset.photo;
                
                // Update removed_photos hidden input
                const removedPhotos = JSON.parse(removedPhotosInput.value || '[]');
                removedPhotos.push(photoUrl);
                removedPhotosInput.value = JSON.stringify(removedPhotos);
                
                // Update existing_photos hidden input
                const existingPhotos = JSON.parse(existingPhotosInput.value || '[]');
                const updatedPhotos = existingPhotos.filter(photo => photo !== photoUrl);
                existingPhotosInput.value = JSON.stringify(updatedPhotos);
                
                // Remove the photo box from display
                photoBox.remove();
                
                // Reorder radio buttons
                const remainingRadios = modal.querySelectorAll('[name="main_photo_existing"]');
                remainingRadios.forEach((radio, index) => {
                    radio.value = index;
                    if (index === 0) radio.checked = true;
                });
            });
        });

        // Reset form on modal hidden
        modal.addEventListener('hidden.bs.modal', function() {
            form.reset();
            removedPhotosInput.value = '[]';
            
            // Reset existing photos to original state
            const originalPhotos = JSON.parse(existingPhotosInput.getAttribute('value') || '[]');
            existingPhotosInput.value = JSON.stringify(originalPhotos);
            
            // Reload the photos grid (optional - depends on your needs)
            // You might want to add code here to restore the photo grid to its original state
        });
    });
});
</script>
<script>
function previewProductPhoto(input) {
    const preview = document.getElementById('productPhotoPreview');
    const img = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
            
            // Optional: Scroll to the preview
            preview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function clearProductPhoto() {
    const preview = document.getElementById('productPhotoPreview');
    const input = document.querySelector('input[name="foto"]');
    
    preview.style.display = 'none';
    preview.querySelector('img').src = '';
    input.value = '';
}

// Clear preview when modal is closed
document.getElementById('addProductModal').addEventListener('hidden.bs.modal', function () {
    clearProductPhoto();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['sweet_alert'])): ?>
    Swal.fire({
        title: <?php echo json_encode($_SESSION['sweet_alert']['title']); ?>,
        text: <?php echo json_encode($_SESSION['sweet_alert']['text']); ?>,
        icon: <?php echo json_encode($_SESSION['sweet_alert']['icon']); ?>,
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        showClass: {
            popup: 'animate__animated animate__fadeInRight'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutRight'
        }
    });
    <?php unset($_SESSION['sweet_alert']); ?>
    <?php endif; ?>
});
</script>
<style>
.swal2-popup.swal2-toast {
    padding: 0.625em;
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 0.5rem;
}

.swal2-title {
    font-size: 1.1rem !important;
    margin-bottom: 0.25rem !important;
}

.swal2-html-container {
    font-size: 0.9rem !important;
}

/* Animation classes */
.animate__animated {
    animation-duration: 0.5s;
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translate3d(100%, 0, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

@keyframes fadeOutRight {
    from {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
    to {
        opacity: 0;
        transform: translate3d(100%, 0, 0);
    }
}

.animate__fadeInRight {
    animation-name: fadeInRight;
}

.animate__fadeOutRight {
    animation-name: fadeOutRight;
}
</style>
<script>
// Format harga dengan titik sebagai pemisah ribuan
document.addEventListener('DOMContentLoaded', function() {
    function formatPrice(input) {
        // Hapus semua karakter non-digit
        let value = input.value.replace(/\D/g, '');
        
        // Format dengan titik sebagai pemisah ribuan
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        
        // Update nilai input
        input.value = value;
    }

    // Terapkan ke semua input dengan class price-input
    document.querySelectorAll('.price-input').forEach(input => {
        // Format saat halaman dimuat
        formatPrice(input);
        
        // Format saat input berubah
        input.addEventListener('input', function() {
            formatPrice(this);
        });
        
        // Format saat fokus hilang
        input.addEventListener('blur', function() {
            formatPrice(this);
        });
        
        // Saat fokus, letakkan kursor di akhir
        input.addEventListener('focus', function() {
            const length = this.value.length;
            setTimeout(() => this.setSelectionRange(length, length), 0);
        });
    });
});
</script>
</body>
</html>
