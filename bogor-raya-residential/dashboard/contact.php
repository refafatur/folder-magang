<?php
session_start();

if (!isset($_SESSION['nama'])) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "db_bogor");

// Load permissions right after session check
$permissions = isset($_SESSION['permissions']) ? json_decode($_SESSION['permissions'], true) : [];

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if permissions array exists before checking specific permissions
    if (!is_array($permissions)) {
        $_SESSION['error_message'] = "Permission configuration error. Please contact administrator.";
        header("Location: contact.php");
        exit();
    }

    if (isset($_POST['add_contact'])) {
        // Modified permission check
        if (!isset($permissions['contacts']['add']) || $permissions['contacts']['add'] != 1) {
            $_SESSION['error_message'] = "You don't have permission to add contacts.";
            header("Location: contact.php");
            exit();
        }
        
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
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Contact added successfully.";
        } else {
            $_SESSION['error_message'] = "Error adding contact: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
        
        header("Location: contact.php");
        exit();
    }

    if (isset($_POST['edit_contact'])) {
        if (!isset($permissions['contacts']['edit']) || $permissions['contacts']['edit'] != 1) {
            $_SESSION['error_message'] = "You don't have permission to edit contacts.";
            header("Location: contact.php");
            exit();
        }
        
        $id = intval($_POST['id']);
        $nama = trim($_POST['nama']);
        $email = trim($_POST['email']);
        $no_hp = trim($_POST['no_telp']);
        $cluster = trim($_POST['cluster']);
        $pesan = trim($_POST['pesan']);
        $tgl_kunjungan = trim($_POST['tgl_kunjungan']);

        $query = "UPDATE tbl_contacts SET nama=?, email=?, no_telp=?, cluster=?, pesan=?, tgl_kunjungan=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssi", $nama, $email, $no_hp, $cluster, $pesan, $tgl_kunjungan, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        require_once '../includes/notifications.php';
        triggerNotification($conn, 'edit_contact', [
            'nama' => $nama
        ]);
        
        header("Location: contact.php");
        exit();
    }

    if (isset($_POST['delete_contact'])) {
        if (!isset($permissions['contacts']['delete']) || $permissions['contacts']['delete'] != 1) {
            $_SESSION['error_message'] = "You don't have permission to delete contacts.";
            header("Location: contact.php");
            exit();
        }
        
        $id = intval($_POST['id']);
        
        // Get contact name before deletion
        $stmt = mysqli_prepare($conn, "SELECT nama FROM tbl_contacts WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $contact = mysqli_fetch_assoc($result);
        
        // Delete contact
        $query = "DELETE FROM tbl_contacts WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        require_once '../includes/notifications.php';
        triggerNotification($conn, 'delete_contact', [
            'nama' => $contact['nama']
        ]);
        
        header("Location: contact.php");
        exit();
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = mysqli_prepare($conn, "SELECT * FROM tbl_contacts WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Fetch users from database again to update the list
$query = "SELECT * FROM tbl_contacts";
$result = mysqli_query($conn, $query);

// Store users in array
$contacts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $contacts[] = $row;
}

// Fetch data for chart
$chartQuery = "SELECT cluster, COUNT(*) as count FROM tbl_contacts GROUP BY cluster";
$chartResult = mysqli_query($conn, $chartQuery);
$chartData = [];
while ($row = mysqli_fetch_assoc($chartResult)) {
    $chartData[] = $row;
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
if (isset($permissions['contacts']['view']) && $permissions['contacts']['view'] == 1): 
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-inbox"></i> Pemesanan</h2>
        <!-- <?php if (isset($permissions['contacts']['add']) && $permissions['contacts']['add'] == 1): ?>
            <a href="#" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addContactModal">Add New Contact</a>
        <?php endif; ?> -->
    </div>

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
    

    <div class="card mb-3">
        <div class="card-body" style="height: 300px;">
            <canvas id="contactsChart"></canvas>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Cluster</th>
                            <th>Pesan</th>
                            <th>Tanggal Kunjungan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contact['nama']); ?></td>
                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                        <td><?php echo htmlspecialchars($contact['no_telp']); ?></td>
                        <td><?php echo htmlspecialchars($contact['cluster']); ?></td>
                        <td><?php echo htmlspecialchars($contact['pesan']); ?></td>
                        <td><?php echo date('d M Y', strtotime($contact['tgl_kunjungan'])); ?></td>
                        <td>
                        <div class="btn-group">
                            <?php if (isset($permissions['contacts']['edit']) && $permissions['contacts']['edit'] == 1): ?>
                                <button class="btn btn-sm btn-info btn-edit-contact" data-id="<?php echo $contact['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                            <?php endif; ?>
                            
                            <?php if (isset($permissions['contacts']['view']) && $permissions['contacts']['view'] == 1): ?>
                                <button class="btn btn-sm btn-success btn-view-user" data-id="<?php echo $contact['id']; ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                            <?php endif; ?>
                            
                            <?php if (isset($permissions['contacts']['delete']) && $permissions['contacts']['delete'] == 1): ?>
                                <button class="btn btn-sm btn-danger" onclick="deleteContact(<?php echo $contact['id']; ?>)">
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



    <!-- Add Contact Modal -->
    <div class="modal fade" id="addContactModal" tabindex="-1" aria-labelledby="addContactModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="contact.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addContactModalLabel">Add New Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No HP</label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp" required>
                        </div>
                        <div class="mb-3">
                    <label class="form-label">Cluster</label>
                    <select class="form-select" name="cluster" required>
                        <option value="">Pilih Cluster...</option>
                        <option value="terracotta3">Terracotta 3</option>
                        <option value="kemangpermataneo">Kemang Permata Neo</option>
                        <option value="terracottainsert">Terracotta Insert</option>
                        <option value="kemangpermatapremiere">Kemang Permata Premiere</option>
                    </select>
                    </div>
                        <div class="mb-3">
                            <label for="pesan" class="form-label">Pesan</label>
                            <textarea class="form-control" id="pesan" name="pesan" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tgl_kunjungan" class="form-label">Tanggal Kunjungan</label>
                            <input type="date" class="form-control" id="tgl_kunjungan" name="tgl_kunjungan" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add_contact">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Contact Modal -->
<div class="modal fade" id="editContactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" id="editContactForm<?php echo $user['id']; ?>">
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama<?php echo $user['id']; ?>" name="nama" required>
                    </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email<?php echo $user['id']; ?>" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No HP</label>
                            <input type="text" class="form-control" id="no_telp<?php echo $user['id']; ?>" name="no_telp" required>
                        </div>
                        <div class="mb-3">
                    <label class="form-label">Cluster</label>
                    <select class="form-select" name="cluster" id="cluster<?php echo $user['id']; ?>" required>
                        <option value="">Pilih Cluster...</option>
                        <option value="terracotta3">Terracotta 3</option>
                        <option value="kemangpermataneo">Kemang Permata Neo</option>
                        <option value="terracottainsert">Terracotta Insert</option>
                        <option value="kemangpermatapremiere">Kemang Permata Premiere</option>
                    </select>
                    </div>
                        <div class="mb-3">
                            <label for="pesan" class="form-label">Pesan</label>
                            <textarea class="form-control" id="pesan<?php echo $user['id']; ?>" name="pesan" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tgl_kunjungan" class="form-label">Tanggal Kunjungan</label>
                            <input type="date" class="form-control" id="tgl_kunjungan<?php echo $user['id']; ?>" name="tgl_kunjungan" required>
                        </div>
                    <button type="submit" name="edit_contact" class="btn btn-primary">Update Contact</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteContactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user?</p>
                <form id="deleteContactForm" method="POST">
                    <input type="hidden" name="id" id="deleteContactId">
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_contact" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete Contact
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
    // Edit contact functionality
    const editButtons = document.querySelectorAll(".btn-edit-contact");
    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            const contactId = this.getAttribute("data-id");
            fetch(`get-contact.php?id=${contactId}`)
                .then(response => response.json())
                .then(data => {
                    // Get the edit modal
                    const modal = document.getElementById('editContactModal');
                    const form = modal.querySelector('form');

                    // Populate the form fields
                    form.querySelector('input[name="id"]').value = data.id;
                    form.querySelector('input[name="nama"]').value = data.nama;
                    form.querySelector('input[name="email"]').value = data.email;
                    form.querySelector('input[name="no_telp"]').value = data.no_telp;
                    form.querySelector('select[name="cluster"]').value = data.cluster;
                    form.querySelector('textarea[name="pesan"]').value = data.pesan;
                    form.querySelector('input[name="tgl_kunjungan"]').value = data.tgl_kunjungan;

                    // Show the modal
                    const modalInstance = new bootstrap.Modal(modal);
                    modalInstance.show();
                })
                .catch(error => console.error('Error:', error));
        });
    });
    });

    // Chart.js setup
    const ctx = document.getElementById('contactsChart').getContext('2d');
    const chartData = <?php echo json_encode($chartData); ?>;
    const labels = chartData.map(data => data.cluster);
    const data = chartData.map(data => data.count);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Contacts per Cluster',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // View contact functionality
    const viewButtons = document.querySelectorAll(".btn-view-user");
    viewButtons.forEach(button => {
        button.addEventListener("click", function() {
            const contactId = this.getAttribute("data-id");
            fetchAndShowContactData(contactId, 'view');
        });
    });

    function fetchAndShowContactData(contactId, mode) {
        fetch(`get-contact.php?id=${contactId}`)
            .then(response => response.json())
            .then(data => {
                if (mode === 'view') {
                    showViewModal(data);
                }
            })
            .catch(error => console.error('Error fetching contact data:', error));
    }

    function showViewModal(data) {
    let viewModal = document.getElementById('viewContactModal');
    if (!viewModal) {
        viewModal = document.createElement('div');
        viewModal.className = 'modal fade';
        viewModal.id = 'viewContactModal';
        viewModal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-3 p-3">
                    <div class="modal-header bg-primary text-white rounded-top">
                        <h5 class="modal-title">Contact Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="card border-0 shadow-sm p-4 rounded-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-1">${data.nama}</h6>
                                <p class="text-muted mb-3">${data.email}</p>
                                <ul class="list-group list-group-flush text-start">
                                    <li class="list-group-item"><strong>No HP:</strong> ${data.no_telp}</li>
                                    <li class="list-group-item"><strong>Cluster:</strong> ${data.cluster}</li>
                                    <li class="list-group-item"><strong>Pesan:</strong> ${data.pesan}</li>
                                    <li class="list-group-item"><strong>Tanggal Kunjungan:</strong> ${new Date(data.tgl_kunjungan).toLocaleDateString()}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(viewModal);
    } else {
        viewModal.querySelector('.fw-bold').innerText = data.nama;
        viewModal.querySelector('.text-muted').innerText = data.email;
        const listItems = viewModal.querySelectorAll('.list-group-item');
        listItems[0].innerHTML = `<strong>No HP:</strong> ${data.no_telp}`;
        listItems[1].innerHTML = `<strong>Cluster:</strong> ${data.cluster}`;
        listItems[2].innerHTML = `<strong>Pesan:</strong> ${data.pesan}`;
        listItems[3].innerHTML = `<strong>Tanggal Kunjungan:</strong> ${new Date(data.tgl_kunjungan).toLocaleDateString()}`;
    }

    const modalInstance = new bootstrap.Modal(viewModal);
    modalInstance.show();
};


    function deleteContact(userId) {
        document.getElementById('deleteContactId').value = userId;
        new bootstrap.Modal(document.getElementById('deleteContactModal')).show();
    }
</script>

</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/dashboard.js"></script>

</body>
</html>