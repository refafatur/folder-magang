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
                    $image = '';
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $target_dir = "../uploads/meet_gathering/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $image = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
                    }

                    $sql = "INSERT INTO tbl_meet_gathering (image) VALUES (?)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("s", $image);
                        $stmt->execute();
                        $stmt->close();
                    }
                    $_SESSION['success_message'] = 'Photo added successfully!';
                    break;

                case 'delete':
                    $id = $_POST['id'];
                    $stmt = $conn->prepare("SELECT image FROM tbl_meet_gathering WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $photo = $result->fetch_assoc();
                    
                    if (file_exists($photo['image'])) unlink($photo['image']);
                    
                    $sql = "DELETE FROM tbl_meet_gathering WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Photo deleted successfully!';
                    break;

                case 'update':
                    // Tidak melakukan apa-apa untuk edit
                    $_SESSION['error_message'] = 'Edit photo is disabled.';
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

// Fetch all photos
$sql = "SELECT * FROM tbl_meet_gathering ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golf Club - Meet & Gathering Photos</title>
    <link rel="icon" href="../assets/photos/Logo Master KGBR.avif" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/meet.css">
</head>
<body>
    <?php include '../components/sidemenu/index.php'; ?>
    <div class="main-content">
        <div class="dashboard-container">
            <div class="header-actions">
                <h1>Meet & Gathering Photos</h1>
                <button onclick="showModal('addPhotoModal')" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Photo
                </button>
            </div>

            <!-- Add Photo Modal -->
            <div id="addPhotoModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('addPhotoModal')">&times;</span>
                    <h2>Add New Photo</h2>
                    <form class="course-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create">
                        <div class="form-grid">
                            <div class="form-group file-input-group">
                                <label>Upload Photo</label>
                                <input type="file" name="image" accept="image/*" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload Photo</button>
                    </form>
                </div>
            </div>

            <!-- Edit Photo Modal -->
            <div id="editPhotoModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('editPhotoModal')">&times;</span>
                    <h2>Edit Photo</h2>
                    <form class="edit-photo-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_photo_id">
                        <input type="hidden" name="current_image" id="edit_current_image">
                        <div class="form-grid">
                            <div class="form-group file-input-group" style="display: flex; align-items: flex-start; gap: 40px; justify-content: center;">
                                <div style="text-align:center;">
                                    <label style="font-weight:bold;">Foto Lama</label>
                                    <div style="margin:10px 0;">
                                        <div style="width:130px;height:130px;display:flex;align-items:center;justify-content:center;border:2px dashed #bbb;border-radius:10px;background:#fafbfc;">
                                            <div id="edit_photo_preview" style="width:120px;height:120px;display:flex;align-items:center;justify-content:center;">
                                                <!-- Preview image will be inserted here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align:center;">
                                    <label style="font-weight:bold;">Foto Baru</label>
                                    <div style="margin:10px 0;">
                                        <div style="width:130px;height:130px;display:flex;align-items:center;justify-content:center;border:2px dashed #bbb;border-radius:10px;background:#fafbfc;">
                                            <div id="edit_new_photo_area" style="width:120px;height:120px;display:flex;align-items:center;justify-content:center;">
                                                <label for="edit_image_input" id="edit_image_label" style="cursor:pointer; display:inline-block; width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                                    <span id="edit_camera_icon" style="display:flex; flex-direction:column; align-items:center; gap:6px;">
                                                        <i class="fas fa-camera" style="font-size: 2em;"></i>
                                                        <span style="font-size:0.95em;">Pilih Foto</span>
                                                    </span>
                                                </label>
                                                <input id="edit_image_input" type="file" name="image" accept="image/*" style="display:none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Photo</button>
                    </form>
                </div>
            </div>

            <!-- Photo Grid -->
            <div class="photos-grid-modern">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="photo-card-modern">
                            <div class="photo-img-wrapper">
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Meet & Gathering Photo" class="photo-img">
                                <div class="photo-overlay">
                                    <form method="POST" class="photo-action-form">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-danger-modern" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <!-- No date/meta shown -->
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-photos-modern">
                        <i class="fas fa-image" style="font-size:2em;color:#bbb;"></i>
                        <p>No photos found</p>
                    </div>
                <?php endif; ?>
            </div>
            <style>
                .photos-grid-modern {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                    gap: 28px;
                    margin-top: 32px;
                }
                .photo-card-modern {
                    background: #fff;
                    border-radius: 18px;
                    box-shadow: 0 4px 18px rgba(0,0,0,0.07), 0 1.5px 4px rgba(0,0,0,0.03);
                    overflow: hidden;
                    transition: box-shadow 0.2s;
                    display: flex;
                    flex-direction: column;
                    position: relative;
                }
                .photo-card-modern:hover {
                    box-shadow: 0 8px 32px rgba(0,0,0,0.13), 0 3px 8px rgba(0,0,0,0.06);
                }
                .photo-img-wrapper {
                    position: relative;
                    width: 100%;
                    aspect-ratio: 1/1;
                    background: #f4f6fa;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .photo-img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    border-radius: 0;
                    transition: transform 0.2s;
                }
                .photo-card-modern:hover .photo-img {
                    transform: scale(1.04);
                }
                .photo-overlay {
                    position: absolute;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background: linear-gradient(0deg, rgba(0,0,0,0.22) 60%, rgba(0,0,0,0.04) 100%);
                    opacity: 0;
                    display: flex;
                    align-items: flex-end;
                    justify-content: flex-end;
                    padding: 14px;
                    transition: opacity 0.2s;
                }
                .photo-card-modern:hover .photo-overlay {
                    opacity: 1;
                }
                .photo-action-form {
                    margin: 0;
                }
                .btn.btn-danger-modern {
                    background: #ff4d4f;
                    border: none;
                    color: #fff;
                    border-radius: 50%;
                    width: 42px;
                    height: 42px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.2em;
                    box-shadow: 0 2px 8px rgba(255,77,79,0.13);
                    transition: background 0.18s;
                    cursor: pointer;
                }
                .btn.btn-danger-modern:hover {
                    background: #d9363e;
                }
                .no-photos-modern {
                    grid-column: 1/-1;
                    text-align: center;
                    color: #bbb;
                    padding: 48px 0 32px 0;
                    font-size: 1.1em;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 10px;
                }
                @media (max-width: 600px) {
                    .photos-grid-modern {
                        grid-template-columns: 1fr;
                        gap: 18px;
                    }
                }
            </style>
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
            if (modalId === 'editPhotoModal') {
                modal.querySelector('form').reset();
                document.getElementById('edit_photo_preview').innerHTML = '';
            }
        }
    }

    // Form submission handling
    document.querySelectorAll('.course-form, .edit-photo-form').forEach(form => {
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
                    message = 'Updating photo...';
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

    // Edit Photo
    function editPhoto(id, image) {
        // Set ID dan current image
        document.getElementById('edit_photo_id').value = id;
        document.getElementById('edit_current_image').value = image;

        // Set preview foto lama
        document.getElementById('edit_photo_preview').innerHTML =
            '<img src="' + image + '" alt="Current Photo" style="max-width:120px;max-height:120px;border-radius:8px;object-fit:cover;width:120px;height:120px;">';

        // Reset area foto baru ke kamera
        const area = document.getElementById('edit_new_photo_area');
        area.innerHTML = `
            <label for="edit_image_input" id="edit_image_label" style="cursor:pointer; display:inline-block; width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                <span id="edit_camera_icon" style="display:flex; flex-direction:column; align-items:center; gap:6px;">
                    <i class="fas fa-camera" style="font-size: 2em;"></i>
                    <span style="font-size:0.95em;">Pilih Foto</span>
                </span>
            </label>
            <input id="edit_image_input" type="file" name="image" accept="image/*" style="display:none;">
        `;

        // Attach ulang event listener untuk input file
        attachEditImageInputListener();

        // Tampilkan modal
        showModal('editPhotoModal');
    }

    function attachEditImageInputListener() {
        const area = document.getElementById('edit_new_photo_area');
        const input = area.querySelector('#edit_image_input');
        if (input) {
            input.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        area.innerHTML = `
                            <div style="position:relative; display:inline-block;">
                                <img src="${ev.target.result}" alt="New Photo" style="max-width:120px;max-height:120px;border-radius:8px;object-fit:cover;width:120px;height:120px;">
                                <label for="edit_image_input" title="Ganti Foto" style="position:absolute;bottom:5px;right:5px;cursor:pointer;background:rgba(255,255,255,0.7);border-radius:50%;padding:6px;">
                                    <i class="fas fa-camera" style="font-size:1.2em;"></i>
                                </label>
                            </div>
                            <input id="edit_image_input" type="file" name="image" accept="image/*" style="display:none;">
                        `;
                        // Attach ulang agar bisa ganti foto lagi
                        attachEditImageInputListener();
                    }
                    reader.readAsDataURL(e.target.files[0]);
                } else {
                    // Jika batal pilih, reset ke kamera
                    area.innerHTML = `
                        <label for="edit_image_input" id="edit_image_label" style="cursor:pointer; display:inline-block; width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                            <span id="edit_camera_icon" style="display:flex; flex-direction:column; align-items:center; gap:6px;">
                                <i class="fas fa-camera" style="font-size: 2em;"></i>
                                <span style="font-size:0.95em;">Pilih Foto</span>
                            </span>
                        </label>
                        <input id="edit_image_input" type="file" name="image" accept="image/*" style="display:none;">
                    `;
                    attachEditImageInputListener();
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        attachEditImageInputListener();
    });

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