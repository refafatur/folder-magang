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
                    // Handle image upload
                    $foto = '';
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $target_dir = "../uploads/rooms/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $foto = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                        move_uploaded_file($_FILES["image"]["tmp_name"], $foto);
                    }
                    $type = isset($_POST['type']) ? $_POST['type'] : 'wedding';

                    $sql = "INSERT INTO tbl_wedding (foto, type) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("ss", $foto, $type);
                        if (!$stmt->execute()) {
                            throw new Exception("Error executing statement: " . $stmt->error);
                        }
                        $stmt->close();
                    }
                    $_SESSION['success_message'] = 'Wedding photo successfully added!';
                    break;

                case 'delete':
                    $id = $_POST['id'];
                    // Get image path before deleting
                    $stmt = $conn->prepare("SELECT foto FROM tbl_wedding WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $photo = $result->fetch_assoc();
                    
                    // Delete physical image file
                    if (file_exists($photo['foto'])) unlink($photo['foto']);
                    
                    // Delete database record
                    $sql = "DELETE FROM tbl_wedding WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Wedding photo successfully deleted!';
                    break;

                case 'update':
                    $id = $_POST['id'];
                    $type = isset($_POST['type']) ? $_POST['type'] : 'wedding';

                    // Handle image upload
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        // Get old image
                        $stmt = $conn->prepare("SELECT foto FROM tbl_wedding WHERE id = ?");
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $old = $result->fetch_assoc();
                        
                        // Delete old image
                        if (file_exists($old['foto'])) unlink($old['foto']);
                        
                        // Upload new image
                        $target_dir = "../uploads/rooms/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $foto = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                        move_uploaded_file($_FILES["image"]["tmp_name"], $foto);
                        
                        // Update database
                        $sql = "UPDATE tbl_wedding SET foto = ?, type = ? WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssi", $foto, $type, $id);
                        $stmt->execute();
                    } else {
                        // Update only type if image not uploaded
                        $sql = "UPDATE tbl_wedding SET type = ? WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("si", $type, $id);
                        $stmt->execute();
                    }
                    $_SESSION['success_message'] = 'Wedding photo successfully updated!';
                    break;
            }
        }
        // Redirect to avoid form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch all photos
try {
    $sql = "SELECT * FROM tbl_wedding ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error fetching photos: " . $conn->error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $result = false;
}

// Initialize $result if query failed
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
    <title>Golf Club - Wedding Spot Management</title>
    <link rel="icon" href="../assets/photos/Logo Master KGBR.avif" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/pre-wedding.css">
</head>
<body>
    <?php include '../components/sidemenu/index.php'; ?>
    <div class="main-content">
        <div class="dashboard-container">
            <div class="header-actions">
                <h1>Wedding & Pre-Wedding Spot Management</h1>
                <button onclick="showModal('addCourseModal')" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Spot
                </button>
            </div>

            <!-- Add Photo Modal -->
            <div id="addCourseModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('addCourseModal')">&times;</span>
                    <h2>Add New Wedding / Pre-Wedding Photo</h2>
                    <form class="course-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create">
                        <div class="form-group">
                            <label>Wedding Photo</label>
                            <input type="file" name="image" accept="image/*" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <div class="type-radio-group">
                                <label class="type-radio wedding">
                                    <input type="radio" name="type" value="wedding" id="add_type_wedding">
                                    <span><i class="fa-solid fa-heart"></i> Wedding</span>
                                </label>
                                <label class="type-radio prewedding">
                                    <input type="radio" name="type" value="pre-wedding" id="add_type_prewedding">
                                    <span><i class="fa-solid fa-camera-retro"></i> Pre-Wedding</span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Photo</button>
                    </form>
                </div>
            </div>

            <!-- Edit Photo Modal -->
            <div id="editCourseModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('editCourseModal')">&times;</span>
                    <h2>Edit Wedding / Pre-Wedding Photo</h2>
                    <form class="course-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_photo_id">
                        <div class="form-group">
                            <label>Update Photo</label>
                            <input type="file" name="image" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <div class="type-radio-group">
                                <label class="type-radio wedding">
                                    <input type="radio" name="type" value="wedding" id="edit_type_wedding">
                                    <span><i class="fa-solid fa-heart"></i> Wedding</span>
                                </label>
                                <label class="type-radio prewedding">
                                    <input type="radio" name="type" value="pre-wedding" id="edit_type_prewedding">
                                    <span><i class="fa-solid fa-camera-retro"></i> Pre-Wedding</span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Photo</button>
                    </form>
                </div>
            </div>

            <!-- Grid layout -->
            <div class="courses-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="course-card">
                            <div class="event-image">
                                <img src="<?php echo htmlspecialchars($row['foto']); ?>" alt="Wedding Photo">
                            </div>
                            <div class="course-details">
                                <div class="course-type">
                                    <?php
                                        // Gunakan strtolower untuk memastikan perbandingan konsisten
                                        $type = strtolower(trim($row['type']));
                                        if ($type === 'wedding') {
                                            $badgeClass = 'badge-wedding';
                                            $badgeLabel = 'Wedding';
                                            $badgeIcon = 'fa-heart';
                                        } elseif ($type === 'pre-wedding' || $type === 'prewedding') {
                                            $badgeClass = 'badge-prewedding';
                                            $badgeLabel = 'Pre-Wedding';
                                            $badgeIcon = 'fa-camera-retro';
                                        } else {
                                            $badgeClass = 'badge-wedding';
                                            $badgeLabel = ucfirst($type);
                                            $badgeIcon = 'fa-image';
                                        }
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <i class="fa-solid <?php echo $badgeIcon; ?>"></i>
                                        <?php echo $badgeLabel; ?>
                                    </span>
                                </div>
                                <div class="course-actions">
                                    <button onclick="viewPhoto('<?php echo htmlspecialchars($row['foto']); ?>')" class="btn btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button onclick="editPhoto(<?php echo $row['id']; ?>, '<?php echo $row['type']; ?>')" class="btn btn-primary">
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
                        message = 'Adding new spot...';
                        break;
                    case 'update':
                        message = 'Updating spot...';
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

        function editEvent(event) {
            if (!event || !event.id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid spot data'
                });
                return;
            }
            
            const form = document.querySelector('#editCourseModal form');
            document.getElementById('edit_course_id').value = event.id;
            form.querySelector('input[name="name"]').value = event.name || '';
            form.querySelector('textarea[name="description"]').value = event.description || '';
            
            showModal('editCourseModal');
        }

        // Add view function after existing modal functions
        function viewEvent(event) {
            if (!event || !event.id) {
                return;
            }
            
            Swal.fire({
                title: event.name,
                html: `
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start; margin-top: 20px;">
                        <div>
                            <img src="${event.image}" alt="Spot Image" style="width: 100%; border-radius: 8px; height: 300px; object-fit: cover;">
                        </div>
                        <div>
                            <p style="text-align: left; margin: 0;">${event.description}</p>
                        </div>
                    </div>
                `,
                width: '800px',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    container: 'view-event-modal'
                }
            });
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

        function editPhoto(id, type) {
            document.getElementById('edit_photo_id').value = id;
            // Normalisasi type agar perbandingan selalu benar
            var t = (type || '').toLowerCase().replace(/\s+/g, '').replace('-', '');
            document.getElementById('edit_type_wedding').checked = false;
            document.getElementById('edit_type_prewedding').checked = false;
            if (t === 'wedding') {
                document.getElementById('edit_type_wedding').checked = true;
            } else if (t === 'prewedding' || t === 'pre-wedding') {
                document.getElementById('edit_type_prewedding').checked = true;
            }
            showModal('editCourseModal');
        }

        function viewPhoto(imageUrl) {
            Swal.fire({
                imageUrl: imageUrl,
                imageAlt: 'Wedding Photo',
                width: 800,
                imageWidth: '100%',
                imageHeight: 'auto',
                showCloseButton: true,
                showConfirmButton: false
            });
        }
    </script>

    <style>
        /* Tambahkan style badge yang lebih menarik */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.95em;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .badge-wedding {
            background: #ffe0ec;
            color: #d72660;
            border: 1px solid #d72660;
        }
        .badge-prewedding {
            background: #e0f7fa;
            color: #00796b;
            border: 1px solid #00796b;
        }
        .badge .fa-solid {
            font-size: 1em;
        }

        .type-radio-group {
            display: flex;
            gap: 18px;
            margin-top: 4px;
        }
        .type-radio {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 16px;
            border: 1.5px solid transparent;
            transition: border 0.2s, background 0.2s;
            user-select: none;
        }
        .type-radio.wedding input[type="radio"]:checked + span,
        .type-radio.wedding:hover span {
            color: #d72660;
        }
        .type-radio.wedding input[type="radio"]:checked,
        .type-radio.wedding:hover {
            border-color: #d72660;
            background: #ffe0ec;
        }
        .type-radio.prewedding input[type="radio"]:checked + span,
        .type-radio.prewedding:hover span {
            color: #00796b;
        }
        .type-radio.prewedding input[type="radio"]:checked,
        .type-radio.prewedding:hover {
            border-color: #00796b;
            background: #e0f7fa;
        }
        .type-radio input[type="radio"] {
            accent-color: #d72660;
            margin-right: 4px;
        }
        .type-radio.prewedding input[type="radio"] {
            accent-color: #00796b;
        }
        .type-radio span {
            display: flex;
            align-items: center;
            gap: 4px;
        }
    </style>
</body>
</html>