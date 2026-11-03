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
if (!isset($permissions['news']['view']) || $permissions['news']['view'] != 1) {
    header("Location: ../dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check add permission
    if (isset($_POST['add_news'])) {
        if (!isset($permissions['news']['add']) || $permissions['news']['add'] != 1) {
            echo "Permission denied";
            exit();
        }
        $nama = $_POST['nama'];
        $lokasi = $_POST['lokasi'];
        $deskripsi = $_POST['deskripsi'];
        $status = $_POST['status']; // Add this line
        
        // Set expiration date to 1 year from now
        $expired_date = date('Y-m-d', strtotime('+1 year'));
        
        $foto = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_dir = "../uploads/news/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $foto = $target_dir . basename($_FILES["foto"]["name"]);
            move_uploaded_file($_FILES["foto"]["tmp_name"], $foto);
        }

        $query = "INSERT INTO tbl_news (nama, lokasi, deskripsi, foto, expired_date, status) 
                  VALUES ('$nama', '$lokasi', '$deskripsi', '$foto', '$expired_date', '$status')";
        mysqli_query($conn, $query);

        // After successful news insertion
        require_once '../includes/notifications.php';
        triggerNotification($conn, 'new_news', [
            'nama' => $nama // Replace with your actual news title variable
        ]);
        header("Location: news.php");
        exit();
    }

    // Check edit permission
    if (isset($_POST['update_news'])) {
        if (!isset($permissions['news']['edit']) || $permissions['news']['edit'] != 1) {
            echo "Permission denied";
            exit();
        }
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $deskripsi = $_POST['deskripsi'];
        $lokasi = $_POST['lokasi'];
        $expired_date = $_POST['expired_date'];
        $status = $_POST['status'];
        
        $query = "UPDATE tbl_news SET 
                  nama='$nama', 
                  deskripsi='$deskripsi',
                  lokasi='$lokasi',
                  expired_date='$expired_date',
                  status='$status'";

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_dir = "../uploads/news/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $foto = $target_dir . basename($_FILES["foto"]["name"]);
            move_uploaded_file($_FILES["foto"]["tmp_name"], $foto);

            // Delete the old photo
            $result = mysqli_query($conn, "SELECT foto FROM tbl_news WHERE id=$id");
            $old_foto = mysqli_fetch_assoc($result)['foto'];
            if (file_exists($old_foto)) {
                unlink($old_foto);
            }

            $query .= ", foto='$foto'";
        }

        $query .= " WHERE id=$id";
        mysqli_query($conn, $query);
        
        require_once '../includes/notifications.php';
        triggerNotification($conn, 'edit_news', [
            'nama' => $nama
        ]);
        
        header("Location: news.php");
        exit();
    }

    // Add toggle status handler
    if (isset($_POST['toggle_status'])) {
        if (!isset($permissions['news']['edit']) || $permissions['news']['edit'] != 1) {
            echo json_encode(['error' => 'Permission denied']);
            exit();
        }
        $id = $_POST['id'];
        $current_status = $_POST['current_status'];
        $new_status = ($current_status == 'show') ? 'hide' : 'show';
        
        $query = "UPDATE tbl_news SET status='$new_status' WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'new_status' => $new_status]);
        } else {
            echo json_encode(['error' => 'Database update failed']);
        }
        exit();
    }
}

// Check delete permission
if (isset($_GET['delete'])) {
    if (!isset($permissions['news']['delete']) || $permissions['news']['delete'] != 1) {
        echo "Permission denied";
        exit();
    }
    // Get news info before deletion
    $id = $_GET['delete'];
    $stmt = mysqli_prepare($conn, "SELECT nama FROM tbl_news WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $news = mysqli_fetch_assoc($result);
    
    $result = mysqli_query($conn, "SELECT foto FROM tbl_news WHERE id=$id");
    $foto = mysqli_fetch_assoc($result)['foto'];
    if (file_exists($foto)) {
        unlink($foto);
    }
    $query = "DELETE FROM tbl_news WHERE id=$id";
    mysqli_query($conn, $query);
    
    require_once '../includes/notifications.php';
    triggerNotification($conn, 'delete_news', [
        'nama' => $news['nama']
    ]);
    
    header("Location: news.php");
    exit();
}

// Modify the query to only show non-expired news
$current_date = date('Y-m-d');
$news = mysqli_query($conn, "SELECT * FROM tbl_news");
// $news = mysqli_query($conn, "SELECT * FROM tbl_news WHERE expired_date >= '$current_date' ORDER BY id DESC");
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
        <h2><i class="fas fa-newspaper me-2"></i>News Management</h2>
        <?php if (isset($permissions['news']['add']) && $permissions['news']['add'] == 1): ?>
            <button class="btn btn-primary" onclick="toggleAddForm()">
                <i class="fas fa-plus me-2"></i>Add New Promotion
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
                        <input type="text" class="form-control" id="searchInput" placeholder="Search news....">
                    </div>
                </div>
                <!-- Add this new filter dropdown -->
                <div class="col-md-3">
    <select class="form-select" id="filterStatus">
        <option value="all">All Status</option>
        <option value="show">Show</option>
        <option value="hide">Hide</option>
    </select>
</div>
            </div>
        </div>
    </div>

    <!-- Properties Grid -->
    <div class="row" id="propertiesGrid">
    <?php 
            
    // Execute query and check for errors
    if (!$news) {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    } else {
        while ($new = mysqli_fetch_assoc($news)): 
    ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 product-card">
                <div class="position-relative">
                    <img src="<?php echo htmlspecialchars($new['foto']); ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($new['nama']); ?>" 
                         style="height: 200px; object-fit: cover;">
                    <div class="position-absolute top-0 end-0 p-2">
                        <div class="btn-group">
                            <?php if (isset($permissions['news']['edit']) && $permissions['news']['edit'] == 1): ?>
                                <button class="btn btn-light btn-sm action-btn" 
                                        onclick="toggleStatus(<?php echo $new['id']; ?>, '<?php echo $new['status']; ?>')"
                                        title="<?php echo ($new['status'] == 'show') ? 'Click to hide' : 'Click to show'; ?>">
                                    <i class="fas <?php echo ($new['status'] == 'show') ? 'fa-eye' : 'fa-eye-slash'; ?>"></i>
                                </button>
                                <script>
                                    function toggleStatus(id, currentStatus) {
                                        const formData = new FormData();
                                        formData.append('toggle_status', true);
                                        formData.append('id', id);
                                        formData.append('current_status', currentStatus);

                                        fetch('news.php', {
                                            method: 'POST',
                                            body: formData
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                location.reload();
                                            } else {
                                                alert(data.error || 'Failed to toggle status');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('Failed to toggle status');
                                        });
                                    }
                                </script>

                                <button class="btn btn-light btn-sm action-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal<?php echo $new['id']; ?>">
                                    <i class="fas fa-edit text-primary"></i>
                                </button>
                            <?php endif; ?>
                            
                            <?php if (isset($permissions['news']['delete']) && $permissions['news']['delete'] == 1): ?>
                                <button class="btn btn-light btn-sm action-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal<?php echo $new['id']; ?>">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            <?php endif; ?>
                            
                            <?php if (isset($permissions['news']['view']) && $permissions['news']['view'] == 1): ?>
                                <button class="btn btn-light btn-sm action-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewModal<?php echo $new['id']; ?>">
                                    <i class="fas fa-eye text-info"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-primary"><?php echo htmlspecialchars($new['nama']); ?></h5>
                    <p class="card-text text-muted"><?php echo nl2br($new['deskripsi']); ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="badge bg-success fs-6"><?php echo htmlspecialchars($new['lokasi']); ?></span>
                        <div class="d-flex gap-2">
                            <span class="badge <?php echo ($new['status'] == 'show') ? 'bg-success' : 'bg-secondary'; ?> fs-6" 
                                  data-status="<?php echo $new['status']; ?>">
                                <?php echo ucfirst($new['status']); ?>
                            </span>
                            <?php 
                            $expired = new DateTime($new['expired_date']);
                            $today = new DateTime();
                            $interval = $today->diff($expired);
                            $daysRemaining = $interval->days;
                            $isExpired = $today > $expired;
                            
                            if ($isExpired) {
                                echo '<span class="badge bg-danger fs-6">Sudah Kadaluarsa</span>';
                            } else {
                                if ($daysRemaining == 0) {
                                    echo '<span class="badge bg-warning fs-6">Hari Terakhir</span>';
                                } else {
                                    echo '<span class="badge bg-warning fs-6">' . $daysRemaining . ' hari lagi</span>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal for each news -->
        <?php if (isset($permissions['news']['edit']) && $permissions['news']['edit'] == 1): ?>
            <div class="modal fade" id="editModal<?php echo $new['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit News</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="" enctype="multipart/form-data" class="row g-3">
                                <input type="hidden" name="id" value="<?php echo $new['id']; ?>">
                                <input type="hidden" name="status" value="<?php echo $new['status']; ?>">

                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="editNama<?php echo $new['id']; ?>" 
                                               name="nama" value="<?php echo htmlspecialchars($new['nama']); ?>" required>
                                        <label for="editNama<?php echo $new['id']; ?>">Nama News</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="editLokasi<?php echo $new['id']; ?>" name="lokasi" required>
                                            <?php
                                            $query = "SELECT nama FROM tbl_produk";
                                            $result = mysqli_query($conn, $query);
                                            while($row = mysqli_fetch_assoc($result)) {
                                                $selected = ($row['nama'] == $new['lokasi']) ? 'selected' : '';
                                                echo "<option value='".$row['nama']."' ".$selected.">".$row['nama']."</option>";
                                            }
                                            ?>
                                        </select>
                                        <label for="editLokasi<?php echo $new['id']; ?>">Pilih Cluster</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" id="editExpiredDate<?php echo $new['id']; ?>" 
                                               name="expired_date" value="<?php echo htmlspecialchars($new['expired_date']); ?>" required>
                                        <label for="editExpiredDate<?php echo $new['id']; ?>">Tanggal Kadaluarsa</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="editDeskripsi<?php echo $new['id']; ?>" 
                                                  name="deskripsi" style="height: 150px" required><?php echo htmlspecialchars($new['deskripsi']); ?></textarea>
                                        <label for="editDeskripsi<?php echo $new['id']; ?>">Deskripsi</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <label class="form-label"><i class="fas fa-image me-2"></i>Current Image</label>
                                                    <img src="<?php echo htmlspecialchars($new['foto']); ?>" 
                                                         class="img-thumbnail" alt="Current Image"
                                                         style="max-width: 200px;">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Upload New Image</label>
                                                    <input type="file" class="form-control" name="foto" accept="image/*"
                                                           onchange="previewImage(this, 'imagePreviewEdit<?php echo $new['id']; ?>')">
                                                    <div class="mt-2">
                                                        <img id="imagePreviewEdit<?php echo $new['id']; ?>" src="#" 
                                                             alt="Preview" style="max-width: 200px; display: none;" 
                                                             class="img-thumbnail">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="update_news" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update News
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- View Modal for each news -->
        <?php if (isset($permissions['news']['view']) && $permissions['news']['view'] == 1): ?>
            <div class="modal fade" id="viewModal<?php echo $new['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">View News</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <img src="<?php echo htmlspecialchars($new['foto']); ?>" 
                                 class="img-fluid mb-3" 
                                 alt="<?php echo htmlspecialchars($new['nama']); ?>">
                            <h5><?php echo htmlspecialchars($new['nama']); ?></h5>
                            <p><?php echo nl2br($new['deskripsi']); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="badge bg-success fs-6"><?php echo htmlspecialchars($new['lokasi']); ?></span>
                                <div>
                                    <?php 
                                    $expired = new DateTime($new['expired_date']);
                                    $today = new DateTime();
                                    $interval = $today->diff($expired);
                                    $daysRemaining = $interval->days;
                                    $isExpired = $today > $expired;
                                    
                                    if ($isExpired) {
                                        echo '<span class="badge bg-danger fs-6">Sudah Kadaluarsa</span>';
                                    } else {
                                        if ($daysRemaining == 0) {
                                            echo '<span class="badge bg-warning fs-6">Hari Terakhir</span>';
                                        } else {
                                            echo '<span class="badge bg-warning fs-6">' . $daysRemaining . ' hari lagi</span>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Delete Modal for each news -->
        <?php if (isset($permissions['news']['delete']) && $permissions['news']['delete'] == 1): ?>
            <div class="modal fade" id="deleteModal<?php echo $new['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Confirmation</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this news?</p>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                This action cannot be undone.
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-newspaper text-primary me-2"></i>
                                <strong><?php echo htmlspecialchars($new['nama']); ?></strong>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <a href="?delete=<?php echo $new['id']; ?>" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Delete News
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

    <!-- Add Property Form Modal -->
    <?php if (isset($permissions['news']['add']) && $permissions['news']['add'] == 1): ?>
        <div class="modal fade" id="addPropertyModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New News</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" id="addPropertyForm" class="row g-3">
                        <input type="hidden" name="status" value="show">
                        
                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="addNama" name="nama" required>
                                <label for="addNama">Nama News</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="addLokasi" name="lokasi" required>
                                    <?php
                                    $query = "SELECT nama FROM tbl_produk";
                                    $result = mysqli_query($conn, $query);
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value='".$row['nama']."'>".$row['nama']."</option>";
                                    }
                                    ?>
                                </select>
                                <label for="addLokasi">Pilih Cluster</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="addExpiredDate" name="expired_date" 
                                       value="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" required>
                                <label for="addExpiredDate">Tanggal Kadaluarsa</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="addDeskripsi" name="deskripsi" style="height: 150px" required></textarea>
                                <label for="addDeskripsi">Deskripsi</label>
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
                            <button type="submit" name="add_news" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save News
                            </button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>


<script>
    function toggleStatus(id, currentStatus) {
    const formData = new FormData();
    formData.append('toggle_status', true);
    formData.append('id', id);
    formData.append('current_status', currentStatus);
    fetch('news.php', {
        method: 'POST',
        body: formData
        })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Failed to toggle status');
        }
    })
    .catch(error => {
    console.error('Error:', error);
    alert('Failed to toggle status');
    });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/dashboard-produk.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/dashboard.js"></script>

<script>
// Update the filterProperties function
function filterProperties() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const cards = document.querySelectorAll('.product-card');

    cards.forEach(card => {
        const cardContainer = card.closest('.col-md-4');
        const title = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
        const description = card.querySelector('.card-text')?.textContent.toLowerCase() || '';
        const location = card.querySelector('.badge.bg-success')?.textContent.toLowerCase() || '';
        const status = card.querySelector('[data-status]')?.getAttribute('data-status') || '';

        // Check if the search term matches any of the card content
        const searchMatch = searchInput === '' || 
            title.includes(searchInput) || 
            description.includes(searchInput) || 
            location.includes(searchInput);

        // Check if the status filter matches
        const statusMatch = statusFilter === 'all' || status === statusFilter;

        // Show/hide the card based on both search and filter criteria
        if (searchMatch && statusMatch) {
            cardContainer.style.display = '';
        } else {
            cardContainer.style.display = 'none';
        }
    });
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('filterStatus');

    if (searchInput) {
        searchInput.addEventListener('input', filterProperties);
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', filterProperties);
    }
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
