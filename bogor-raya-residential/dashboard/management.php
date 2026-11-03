<?php
session_start();
require_once('../includes/management.php');

// Only allow admin access
if (!isset($_SESSION['nama']) || $_SESSION['level'] != '0') {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "db_bogor");


// Get role permissions and name
$rolePermissions = getRolePermissions($conn, $_SESSION['level']);
$roleName = getNamaRole($conn, $_SESSION['level']);

// Update session permissions if role permissions found
if (!empty($rolePermissions)) {
    $_SESSION['permissions'] = json_encode($rolePermissions);
}

// Handle CRUD operations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_management'])) {
        $role = trim($_POST['role']);
        $created_at = date('Y-m-d H:i:s');

        $permissions = array(
            'contacts' => array(
                'view' => isset($_POST['contacts_view']) ? 1 : 0,
                'add' => isset($_POST['contacts_add']) ? 1 : 0,
                'edit' => isset($_POST['contacts_edit']) ? 1 : 0,
                'delete' => isset($_POST['contacts_delete']) ? 1 : 0
            ),
            'news' => array(
                'view' => isset($_POST['news_view']) ? 1 : 0,
                'add' => isset($_POST['news_add']) ? 1 : 0,
                'edit' => isset($_POST['news_edit']) ? 1 : 0,
                'delete' => isset($_POST['news_delete']) ? 1 : 0
            ),
            'products' => array(
                'view' => isset($_POST['products_view']) ? 1 : 0,
                'add' => isset($_POST['products_add']) ? 1 : 0,
                'edit' => isset($_POST['products_edit']) ? 1 : 0,
                'delete' => isset($_POST['products_delete']) ? 1 : 0
            ),
            'testimonials' => array(
                'view' => isset($_POST['testimonials_view']) ? 1 : 0,
                'add' => isset($_POST['testimonials_add']) ? 1 : 0,
                'edit' => isset($_POST['testimonials_edit']) ? 1 : 0,
                'delete' => isset($_POST['testimonials_delete']) ? 1 : 0
            ),
            'users' => array(
                'view' => isset($_POST['users_view']) ? 1 : 0,
                'add' => isset($_POST['users_add']) ? 1 : 0,
                'edit' => isset($_POST['users_edit']) ? 1 : 0,
                'delete' => isset($_POST['users_delete']) ? 1 : 0
            ),
            'sales' => array(
                'view' => isset($_POST['sales_view']) ? 1 : 0,
                'add' => isset($_POST['sales_add']) ? 1 : 0,
                'edit' => isset($_POST['sales_edit']) ? 1 : 0,
                'delete' => isset($_POST['sales_delete']) ? 1 : 0
            )
        );

        $permissions_json = json_encode($permissions);

        $query = "INSERT INTO tbl_management (role, permissions, created_at) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $role, $permissions_json, $created_at);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        header("Location: management.php");
        exit();
    }

    if (isset($_POST['edit_management'])) {
        $id = intval($_POST['id']);
        $role = trim($_POST['role']);

        $permissions = array(
            'contacts' => array(
                'view' => isset($_POST['contacts_view']) ? 1 : 0,
                'add' => isset($_POST['contacts_add']) ? 1 : 0,
                'edit' => isset($_POST['contacts_edit']) ? 1 : 0,
                'delete' => isset($_POST['contacts_delete']) ? 1 : 0
            ),
            'news' => array(
                'view' => isset($_POST['news_view']) ? 1 : 0,
                'add' => isset($_POST['news_add']) ? 1 : 0,
                'edit' => isset($_POST['news_edit']) ? 1 : 0,
                'delete' => isset($_POST['news_delete']) ? 1 : 0
            ),
            'products' => array(
                'view' => isset($_POST['products_view']) ? 1 : 0,
                'add' => isset($_POST['products_add']) ? 1 : 0,
                'edit' => isset($_POST['products_edit']) ? 1 : 0,
                'delete' => isset($_POST['products_delete']) ? 1 : 0
            ),
            'testimonials' => array(
                'view' => isset($_POST['testimonials_view']) ? 1 : 0,
                'add' => isset($_POST['testimonials_add']) ? 1 : 0,
                'edit' => isset($_POST['testimonials_edit']) ? 1 : 0,
                'delete' => isset($_POST['testimonials_delete']) ? 1 : 0
            ),
            'users' => array(
                'view' => isset($_POST['users_view']) ? 1 : 0,
                'add' => isset($_POST['users_add']) ? 1 : 0,
                'edit' => isset($_POST['users_edit']) ? 1 : 0,
                'delete' => isset($_POST['users_delete']) ? 1 : 0
            ),
            'sales' => array(
                'view' => isset($_POST['sales_view']) ? 1 : 0,
                'add' => isset($_POST['sales_add']) ? 1 : 0,
                'edit' => isset($_POST['sales_edit']) ? 1 : 0,
                'delete' => isset($_POST['sales_delete']) ? 1 : 0
            )
        );

        $permissions_json = json_encode($permissions);

        $query = "UPDATE tbl_management SET role=?, permissions=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $role, $permissions_json, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        header("Location: management.php");
        exit();
    }

    if (isset($_POST['delete_management'])) {
        $id = intval($_POST['id']);

        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_management WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        header("Location: management.php");
        exit();
    }
}

// Fetch all management members
$query = "SELECT * FROM tbl_management ORDER BY id DESC";
$result = mysqli_query($conn, $query);
$management = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management | Bogor Raya Residential</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <link href="../assets/css/dashboard-produk.css" rel="stylesheet">
    <style>
        :root {
            --accent-color: #3498db;
        }

        .management-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .management-card:hover {
            transform: translateY(-5px);
        }

        .management-card .card-body {
            padding: 1.5rem;
        }

        .management-card .role-badge {
            background: var(--accent-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .management-card .created-date {
            color: #6c757d;
            font-size: 0.85rem;
        }

        .action-buttons {
            margin-top: 1rem;
            gap: 0.5rem;
        }

        .action-buttons .btn {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        .page-header {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .modal-content {
            border-radius: 15px;
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .btn-close {
            filter: brightness(0) invert(1);
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
    <div class="page-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0"><i class="fas fa-users-cog me-2"></i>Management</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addManagementModal">
            <i class="fas fa-plus me-2"></i>Add New Member
        </button>
    </div>

    <div class="row">
        <?php foreach ($management as $member): ?>
        <div class="col-md-4 mb-4">
            <div class="management-card card">
                <div class="card-body">
                    <span class="role-badge">
                        <i class="fas fa-user-tie me-2"></i>
                        <?php echo htmlspecialchars($member['role']); ?>
                    </span>
                    <div class="created-date">
                        <i class="far fa-calendar-alt me-2"></i>
                        Created: <?php echo date('M d, Y', strtotime($member['created_at'])); ?>
                    </div>
                    <div class="action-buttons d-flex">
                        <button class="btn btn-info flex-grow-1" onclick="editMember(<?php echo $member['id']; ?>)">
                            <i class="fas fa-edit me-2"></i>Edit
                        </button>
                        <button class="btn btn-danger flex-grow-1" onclick="deleteMember(<?php echo $member['id']; ?>)">
                            <i class="fas fa-trash me-2"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Management Modal -->
<div class="modal fade" id="addManagementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Management Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" class="form-control" name="role" required>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Permissions</h6>
                        <div class="row">
                            <?php
                            $modules = ['contacts', 'news', 'products', 'testimonials', 'users', 'sales'];
                            $actions = ['view', 'add', 'edit', 'delete'];
                            
                            foreach ($modules as $module): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0 text-capitalize"><?php echo $module; ?></h6>
                                        </div>
                                        <div class="card-body">
                                            <?php foreach ($actions as $action): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="<?php echo $module . '_' . $action; ?>" 
                                                           id="add_<?php echo $module . '_' . $action; ?>">
                                                    <label class="form-check-label text-capitalize" 
                                                           for="add_<?php echo $module . '_' . $action; ?>">
                                                        <?php echo $action; ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="submit" name="add_management" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Management Modal -->
<div class="modal fade" id="editManagementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Management Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" class="form-control" name="role" id="edit_role" required>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Permissions</h6>
                        <div class="row">
                            <?php foreach ($modules as $module): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0 text-capitalize"><?php echo $module; ?></h6>
                                        </div>
                                        <div class="card-body">
                                            <?php foreach ($actions as $action): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="<?php echo $module . '_' . $action; ?>" 
                                                           id="edit_<?php echo $module . '_' . $action; ?>">
                                                    <label class="form-check-label text-capitalize" 
                                                           for="edit_<?php echo $module . '_' . $action; ?>">
                                                        <?php echo $action; ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="submit" name="edit_management" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Management Modal -->
<div class="modal fade" id="deleteManagementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Management Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this management member?</p>
                <form method="POST" action="">
                    <input type="hidden" name="id" id="delete_id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_management" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editMember(id) {
    fetch(`get-management.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_role').value = data.role;
            
            // Parse and populate permissions
            try {
                const permissions = JSON.parse(data.permissions);
                Object.keys(permissions).forEach(module => {
                    Object.keys(permissions[module]).forEach(action => {
                        const checkboxId = `edit_${module}_${action}`;
                        const checkbox = document.getElementById(checkboxId);
                        if (checkbox) {
                            checkbox.checked = permissions[module][action] === 1;
                        }
                    });
                });
            } catch (e) {
                console.error('Error parsing permissions:', e);
            }
            
            new bootstrap.Modal(document.getElementById('editManagementModal')).show();
        });
}

function deleteMember(id) {
    document.getElementById('delete_id').value = id;
    new bootstrap.Modal(document.getElementById('deleteManagementModal')).show();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/dashboard-produk.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>
