<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$successMessage = '';
$errorMessage = '';

if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create':
                    $name = $_POST['name'];
                    
                    // Handle image upload
                    $image = '';
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $target_dir = "../uploads/drivings/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $image = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
                    }

                    $sql = "INSERT INTO tbl_drivings (name, image) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("ss", $name, $image);
                        if (!$stmt->execute()) {
                            throw new Exception("Error executing statement: " . $stmt->error);
                        }
                        $stmt->close();
                    }
                    $_SESSION['success_message'] = 'Driving range successfully created!';
                    break;

                case 'update':
                    if (!isset($_POST['id'])) {
                        throw new Exception("Driving Range ID is required for update");
                    }
                    
                    $id = $_POST['id'];
                    $name = $_POST['name'];

                    // Verify driving range exists before update
                    $checkStmt = $conn->prepare("SELECT image FROM tbl_drivings WHERE id = ?");
                    $checkStmt->bind_param("i", $id);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();
                    $drivingRange = $result->fetch_assoc();
                    $old_image = $drivingRange['image'];
                    $checkStmt->close();

                    // Handle image upload
                    $image = $old_image;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $target_dir = "../uploads/drivings/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $image = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
                        if (file_exists($old_image)) unlink($old_image);
                    }

                    $sql = "UPDATE tbl_drivings SET name=?, image=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssi", $name, $image, $id);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Driving range successfully updated!';
                    break;

                case 'delete':
                    $id = $_POST['id'];
                    $stmt = $conn->prepare("SELECT image FROM tbl_drivings WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $drivingRange = $result->fetch_assoc();
                    
                    if (file_exists($drivingRange['image'])) unlink($drivingRange['image']);
                    
                    $sql = "DELETE FROM tbl_drivings WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Driving range successfully deleted!';
                    break;
            }
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch all driving ranges
try {
    $sql = "SELECT * FROM tbl_drivings ORDER BY name ASC";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error fetching driving ranges: " . $conn->error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $result = false;
}

if (!$result) {
    $result = new stdClass();
    $result->num_rows = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golf Club - Driving Range Management</title>
        <link rel="icon" href="../assets/photos/Logo Master KGBR.avif" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/driving.css">
</head>
<body>
    <?php include '../components/sidemenu/index.php'; ?>
    <div class="main-content">
        <div class="dashboard-container">
            <div class="header-actions">
                <h1>Driving Range Management</h1>
                <button onclick="showModal('addDrivingModal')" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Driving Range
                </button>
            </div>

            <!-- Add Driving Range Modal -->
            <div id="addDrivingModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('addDrivingModal')">&times;</span>
                    <h2>Add New Driving Range</h2>
                    <form class="course-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Driving Range Name</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group file-input-group">
                                <label>Driving Range Image</label>
                                <input type="file" name="image" accept="image/*" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Driving Range</button>
                    </form>
                </div>
            </div>

            <!-- Edit Driving Range Modal -->
            <div id="editDrivingModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('editDrivingModal')">&times;</span>
                    <h2>Edit Driving Range</h2>
                    <form class="course-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_driving_id">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Driving Range Name</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group file-input-group">
                                <label>Driving Range Image</label>
                                <input type="file" name="image" accept="image/*">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Driving Range</button>
                    </form>
                </div>
            </div>

            <!-- Driving Ranges Grid -->
            <div class="courses-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="course-card">
                            <div class="event-image">
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Driving Range Image">
                            </div>
                            <div class="course-details">
                                <h3 class="course-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <div class="course-actions">
                                    <button onclick="editDrivingRange(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-courses">
                        <p>No driving ranges found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($successMessage): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?php echo $successMessage; ?>',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?php echo $errorMessage; ?>',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            <?php endif; ?>
        });

        // Modal functions
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
                if (modalId === 'editCourseModal') {
                    modal.querySelector('form').reset();
                }
            }
        }

        // Form submission handling
        document.querySelectorAll('.course-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const action = formData.get('action');
                let message = '';

                switch(action) {
                    case 'create':
                        message = 'Adding new event...';
                        break;
                    case 'update':
                        message = 'Updating event...';
                        break;
                }

                if (message) {
                    Swal.fire({
                        title: 'Processing...',
                        text: message,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    });
                }

                this.submit();
            });
        });

        function editDrivingRange(drivingRange) {
            if (!drivingRange || !drivingRange.id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid driving range data'
                });
                return;
            }
            
            const form = document.querySelector('#editDrivingModal form');
            document.getElementById('edit_driving_id').value = drivingRange.id;
            form.querySelector('input[name="name"]').value = drivingRange.name || '';
            
            showModal('editDrivingModal');
        }

        // Add delete confirmation
        document.querySelectorAll('form[method="POST"]').forEach(form => {
            if (form.querySelector('input[name="action"][value="delete"]')) {
                form.onsubmit = function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                };
            }
        });
    </script>
</body>
</html>