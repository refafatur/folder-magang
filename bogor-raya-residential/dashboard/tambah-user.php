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

// Check if user has permission to view users
if (!isset($permissions['users']['view']) || $permissions['users']['view'] != 1) {
    header("Location: ../dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check create permission
    if (isset($_POST['create_user'])) {
        if (!isset($permissions['users']['add']) || $permissions['users']['add'] != 1) {
            $_SESSION['error'] = "You don't have permission to add users";
            header("Location: tambah-user.php");
            exit();
        }
        $username = trim($_POST['username']);
        $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
        $nama = trim($_POST['nama']);
        $email = trim($_POST['email']);
        $no_hp = trim($_POST['no_hp']);
        $jenis_kelamin = trim($_POST['jenis_kelamin']);
        $role = trim($_POST['level']);
        $created_at = date('Y-m-d H:i:s');
        $expired = date('Y-m-d', strtotime('+1 year', strtotime($created_at)));
        $status = 0; // Set default status to 0 (inactive)

        $foto = '../assets/boy.png';
        if (!empty($_FILES['foto']['name'])) {
            $folder = match ($role) {
                '0' => 'admin/',
                '1' => 'user/',
                '2' => 'sales/',
                default => ''
            };

            $upload_dir = '../uploads/' . $folder;
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = uniqid() . '_' . $_FILES['foto']['name'];
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $file_path)) {
                $foto = 'uploads/' . $folder . $file_name;
            }
        }

        // Get default permissions from tbl_management based on role
        $stmt = mysqli_prepare($conn, "SELECT permissions FROM tbl_management WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $role);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $query = "INSERT INTO tbl_users (username, password, nama, email, jenis_kelamin, foto, tanggal_pembuatan, no_hp, expired, level, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssssssi", $username, $password, $nama, $email, $jenis_kelamin, $foto, $created_at, $no_hp, $expired, $role, $status);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // After successful user insertion
        require_once '../includes/notifications.php';
        triggerNotification($conn, 'new_user', [
            'nama' => $nama // Replace with your actual user name variable
        ]);
        
        header("Location: tambah-user.php");
        exit();
    }

    // Check delete permission
    if (isset($_POST['delete_user'])) {
        if (!isset($permissions['users']['delete']) || $permissions['users']['delete'] != 1) {
            $_SESSION['error'] = "You don't have permission to delete users";
            header("Location: tambah-user.php");
            exit();
        }
        $id = intval($_POST['id']);
        $stmt = mysqli_prepare($conn, "SELECT foto FROM tbl_users WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $foto);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($foto && file_exists($foto)) {
            unlink($foto);
        }

        // Get user info before deletion
        $stmt = mysqli_prepare($conn, "SELECT nama FROM tbl_users WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_users WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        require_once '../includes/notifications.php';
        triggerNotification($conn, 'delete_user', [
            'nama' => $user['nama']
        ]);
        
        header("Location: tambah-user.php");
        exit();
    }

    // Check edit permission
    if (isset($_POST['edit_user'])) {
        if (!isset($permissions['users']['edit']) || $permissions['users']['edit'] != 1) {
            $_SESSION['error'] = "You don't have permission to edit users";
            header("Location: tambah-user.php");
            exit();
        }
        $id = intval($_POST['id']);
        $username = trim($_POST['username']);
        $password = !empty($_POST['password']) ? password_hash(trim($_POST['password']), PASSWORD_BCRYPT) : null;
        $nama = trim($_POST['nama']);
        $email = trim($_POST['email']);
        $no_hp = trim($_POST['no_hp']);
        $jenis_kelamin = trim($_POST['jenis_kelamin']);
        $role = trim($_POST['level']);
        $expired = trim($_POST['expired']);
        $status = intval($_POST['status']);

        $foto = null;
        if (!empty($_FILES['foto']['name'])) {
            $folder = match ($role) {
                '0' => 'admin/',
                '1' => 'user/',
                '2' => 'sales/',
                default => ''
            };

            $upload_dir = '../uploads/' . $folder;
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = uniqid() . '_' . $_FILES['foto']['name'];
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $file_path)) {
                $foto = 'uploads/' . $folder . $file_name;
            }
        }

        // Get updated permissions from tbl_management based on role
        $stmt = mysqli_prepare($conn, "SELECT permissions FROM tbl_management WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $role);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $query = "UPDATE tbl_users SET username=?, nama=?, email=?, jenis_kelamin=?, no_hp=?, expired=?, level=?, status=?";
        $params = [$username, $nama, $email, $jenis_kelamin, $no_hp, $expired, $role, $status];
        $types = "sssssssi"; // 8 parameters

        if ($password) {
            $query .= ", password=?";
            $params[] = $password;
            $types .= "s";
        }
        if ($foto) {
            $query .= ", foto=?";
            $params[] = $foto;
            $types .= "s";
        }
        $query .= " WHERE id=?";
        $params[] = $id;
        $types .= "i";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        require_once '../includes/notifications.php';
        triggerNotification($conn, 'edit_user', [
            'nama' => $nama
        ]);
        
        header("Location: tambah-user.php");
        exit();
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = mysqli_prepare($conn, "SELECT * FROM tbl_users WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Fetch users from database again to update the list
$query = "SELECT * FROM tbl_users";
$result = mysqli_query($conn, $query);

// Store users in array
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Bogor Raya Residential</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
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


<?php 
$permissions = json_decode($_SESSION['permissions'], true);
if (isset($permissions['users']['view']) && $permissions['users']['view'] == 1): 
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users-cog me-2"></i>User Management</h2>
        <?php if (isset($permissions['users']['add']) && $permissions['users']['add'] == 1): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal" type="button">
            <i class="fas fa-user-plus me-2"></i>Add New User
        </button>
        <?php endif; ?>
    </div>

    <!-- Filter and Search Form -->
    <div class="row mb-4">
        <div class="col-md-4">
            <input type="text" id="searchName" class="form-control" placeholder="Search by name">
        </div>
        <div class="col-md-4">
            <select id="filterStatus" class="form-select">
                <option value="">Filter by status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="col-md-4">
            <select id="filterRole" class="form-select">
                <option value="">Filter by role</option>
                <?php
                $role_query = "SELECT id, role FROM tbl_management ORDER BY id";
                $role_result = mysqli_query($conn, $role_query);
                while ($role = mysqli_fetch_assoc($role_result)) {
                    echo '<option value="' . htmlspecialchars($role['id']) . '">' . htmlspecialchars($role['role']) . '</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Users Table -->

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="usersTable">
                    <thead>
                        <tr>
                            <th>User Info</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                    <?php foreach ($users as $user): ?>
<tr data-role="<?php echo $user['level']; ?>">
    <td>
        <div class="d-flex align-items-center">
            <img src="../<?php echo $user['foto'] ?: 'assets/img/default-avatar.png'; ?>" 
                 class="rounded-circle me-2" width="32" height="32">
            <div>
                <?php echo htmlspecialchars($user['nama']); ?><br>
                <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
            </div>
        </div>
    </td>
    <td><?php echo htmlspecialchars($user['username']); ?></td>
    <td>
        <span class="badge bg-info">
            <?php 
                $role_id = $user['level'];
                $role_query = "SELECT role FROM tbl_management WHERE id=?";
                $role_stmt = mysqli_prepare($conn, $role_query);
                mysqli_stmt_bind_param($role_stmt, "i", $role_id);
                mysqli_stmt_execute($role_stmt);
                mysqli_stmt_bind_result($role_stmt, $role_name);
                mysqli_stmt_fetch($role_stmt);
                mysqli_stmt_close($role_stmt);
                echo htmlspecialchars($role_name);
            ?>
        </span>
    </td>
    <td>
        <?php 
            if($user['status'] == 1) {
                echo '<span class="badge bg-success">Active</span>';
            } else {
                echo '<span class="badge bg-danger">Inactive</span>';
            }
        ?>
    </td>
    <td><?php echo date('d M Y', strtotime($user['tanggal_pembuatan'])); ?></td>
    <td><?php echo date('d M Y', strtotime($user['expired'])); ?></td>
    <td>
        <div class="btn-group">
            <?php if (isset($permissions['users']['edit']) && $permissions['users']['edit'] == 1): ?>
            <button class="btn btn-sm btn-info btn-edit-user" data-id="<?php echo $user['id']; ?>">
                <i class="fas fa-edit"></i>
            </button>
            <?php endif; ?>
            
            <?php if (isset($permissions['users']['view']) && $permissions['users']['view'] == 1): ?>
            <button class="btn btn-sm btn-success btn-view-user" data-id="<?php echo $user['id']; ?>">
                <i class="fas fa-eye"></i>
            </button>
            <?php endif; ?>
            
            <?php if (isset($permissions['users']['delete']) && $permissions['users']['delete'] == 1): ?>
            <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                <i class="fas fa-trash"></i>
            </button>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" id="createUserForm">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="addPassword" required minlength="6">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('addPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No Handphone</label>
                        <input type="text" class="form-control" name="no_hp" pattern="\d{10,13}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select class="form-select" name="jenis_kelamin" required>
                            <option value="Laki-Laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select class="form-select" name="level" required>
                            <?php
                            $query = "SELECT id, role FROM tbl_management";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['role']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="0">Inactive</option>
                            <option value="1">Active</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
                    </div>
                    <button type="submit" name="create_user" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- Edit User Modal template - will be populated dynamically -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" id="editUserForm">
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="username<?php echo $user['id']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="editPassword" minlength="6">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('editPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" id="nama<?php echo $user['id']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email<?php echo $user['id']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No Handphone</label>
                        <input type="text" class="form-control" name="no_hp" id="no_hp<?php echo $user['id']; ?>" pattern="\d{10,13}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select class="form-select" name="jenis_kelamin" id="jenis_kelamin<?php echo $user['id']; ?>" required>
                            <option value="Laki-Laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select class="form-select" name="level" required id="level<?php echo $user['id']; ?>">
                            <?php
                            $query = "SELECT id, role FROM tbl_management";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['role']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="status<?php echo $user['id']; ?>" required>
                            <option value="0" <?php echo ($user['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                            <option value="1" <?php echo ($user['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiry</label>
                        <input type="date" class="form-control" name="expired" id="expired<?php echo $user['id']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <input type="file" class="form-control" name="foto" id="foto<?php echo $user['id']; ?>" accept="image/*">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_user" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user?</p>
                <form id="deleteUserForm" method="POST">
                    <input type="hidden" name="id" id="deleteUserId">
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_user" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Reset form when Add New User button is clicked
    document.querySelector('[data-bs-target="#addUserModal"]').addEventListener('click', function() {
        document.getElementById('createUserForm').reset();
    });

    // Get permissions from PHP
    const permissions = <?php echo $_SESSION['permissions']; ?>;

    // Edit user functionality
    if (permissions.users.edit === 1) {
        const editButtons = document.querySelectorAll(".btn-edit-user");
        editButtons.forEach(button => {
            button.addEventListener("click", function () {
                const userId = this.getAttribute("data-id");
                fetch(`get-user.php?id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Get the edit modal
                        const modal = document.getElementById('editUserModal');
                        const form = modal.querySelector('form');

                        // Populate the form fields
                        form.querySelector('input[name="id"]').value = data.id;
                        form.querySelector('input[name="username"]').value = data.username;
                        form.querySelector('input[name="password"]').value = '';
                        form.querySelector('input[name="nama"]').value = data.nama;
                        form.querySelector('input[name="email"]').value = data.email;
                        form.querySelector('input[name="no_hp"]').value = data.no_hp;
                        form.querySelector('select[name="jenis_kelamin"]').value = data.jenis_kelamin;
                        form.querySelector('select[name="level"]').value = data.level;
                        form.querySelector('input[name="expired"]').value = data.expired;

                        // Parse and populate permissions
                        try {
                            const permissions = JSON.parse(data.permissions);
                            Object.keys(permissions).forEach(group => {
                                Object.keys(permissions[group]).forEach(action => {
                                    const checkboxId = `edit_${group}_${action}`;
                                    const checkbox = document.getElementById(checkboxId);
                                    if (checkbox) {
                                        checkbox.checked = permissions[group][action] === 1;
                                    }
                                });
                            });
                        } catch (e) {
                            console.error('Error parsing permissions:', e);
                        }

                        // Show the modal
                        const modalInstance = new bootstrap.Modal(modal);
                        modalInstance.show();
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    }

    // View user functionality
    if (permissions.users.view === 1) {
        const viewButtons = document.querySelectorAll(".btn-view-user");
        viewButtons.forEach(button => {
            button.addEventListener("click", function() {
                const userId = this.getAttribute("data-id");
                fetch(`get-user.php?id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Calculate days until expiration
                        const today = new Date();
                        const expiryDate = new Date(data.expired);
                        const daysUntilExpiry = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));
                        
                        let expiryMessage;
                        if (daysUntilExpiry < 0) {
                            expiryMessage = `<span class="text-danger">Expired ${Math.abs(daysUntilExpiry)} days ago</span>`;
                        } else if (daysUntilExpiry === 0) {
                            expiryMessage = `<span class="text-warning">Expires today</span>`;
                        } else {
                            expiryMessage = `<span class="text-success">Expires in ${daysUntilExpiry} days</span>`;
                        }

                        // Create modal content
                        let viewModal = document.getElementById('viewUserModal');
                        const modalHTML = `
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content shadow-lg border-0 rounded-3">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">User Details</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row">
                                            <!-- User Info Column -->
                                            <div class="col-md-6">
                                                <div class="text-center mb-4">
                                                    <img src="../${data.foto}" class="rounded-circle border border-3 border-primary mb-3" 
                                                         style="width: 120px; height: 120px; object-fit: cover;">
                                                    <h5 class="fw-bold mb-1">${data.nama}</h5>
                                                    <p class="text-muted mb-0">${data.username}</p>
                                                    <span class="badge bg-info">${data.role_name}</span>
                                                </div>
                                                <div class="mt-3">
                                                    <p><strong>Email:</strong> ${data.email}</p>
                                                    <p><strong>Phone:</strong> ${data.no_hp}</p>
                                                    <p><strong>Gender:</strong> ${data.jenis_kelamin}</p>
                                                    <p><strong>Created:</strong> ${new Date(data.tanggal_pembuatan).toLocaleDateString('id-ID', {
                                                        day: 'numeric',
                                                        month: 'long',
                                                        year: 'numeric'
                                                    })}</p>
                                                    <p><strong>Expiration:</strong> ${expiryMessage}</p>
                                                    <p><strong>Status:</strong> ${data.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Permissions Column -->
                                            <div class="col-md-6">
                                                <h6 class="fw-bold mb-3">Role Permissions</h6>
                                                ${Object.entries(data.role_info.permissions).map(([module, actions]) => `
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-light">
                                                            <h6 class="mb-0 text-capitalize">${module}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row g-2">
                                                                ${Object.entries(actions).map(([action, value]) => `
                                                                    <div class="col-6">
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="fas ${value === 1 ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} me-2"></i>
                                                                            <span class="text-capitalize">${action}</span>
                                                                        </div>
                                                                    </div>
                                                                `).join('')}
                                                            </div>
                                                        </div>
                                                    </div>
                                                `).join('')}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        `;

                        if (!viewModal) {
                            viewModal = document.createElement('div');
                            viewModal.className = 'modal fade';
                            viewModal.id = 'viewUserModal';
                            document.body.appendChild(viewModal);
                        }

                        viewModal.innerHTML = modalHTML;
                        const modal = new bootstrap.Modal(viewModal);
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error loading user details');
                    });
            });
        });
    }

    // Delete user functionality
    if (permissions.users.delete === 1) {
        window.deleteUser = function(userId) {
            document.getElementById('deleteUserId').value = userId;
            new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
        }
    }
});

// Error message handling
<?php if (isset($_SESSION['error'])): ?>
    alert('<?php echo $_SESSION['error']; ?>');
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const icon = passwordInput.nextElementSibling.querySelector('i');
    
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        passwordInput.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

window.onload = function () {
    document.getElementById('createUserForm').onsubmit = function(e) {
        const submitBtn = document.getElementById('submitUserBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Creating...';
    };
};

document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 150);
    }, 5000);
});

// Filter and Search functionality
document.getElementById('searchName').addEventListener('input', filterUsers);
document.getElementById('filterStatus').addEventListener('change', filterUsers);
document.getElementById('filterRole').addEventListener('change', filterUsers);

function filterUsers() {
    const name = document.getElementById('searchName').value.toLowerCase();
    const status = document.getElementById('filterStatus').value.toLowerCase();
    const roleId = document.getElementById('filterRole').value;

    const rows = document.querySelectorAll('#usersTableBody tr');
    rows.forEach(row => {
        const userName = row.querySelector('td:nth-child(1) div div').innerText.toLowerCase();
        const userStatus = row.querySelector('td:nth-child(4) span').innerText.toLowerCase();
        const userRole = row.getAttribute('data-role');

        const matchesName = userName.includes(name);
        const matchesStatus = !status || userStatus === status;
        const matchesRole = !roleId || userRole === roleId;

        row.style.display = (matchesName && matchesStatus && matchesRole) ? '' : 'none';
    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/dashboard.js"></script>

</body>
</html>