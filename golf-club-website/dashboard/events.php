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
                    $description = $_POST['description'];
                    $event_date = $_POST['event_date'];

                    // Handle image upload
                    $image = '';
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $target_dir = "../uploads/events/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $image = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
                    }

                    $sql = "INSERT INTO tbl_events (name, description, event_date, image) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("ssss", $name, $description, $event_date, $image);
                        if (!$stmt->execute()) {
                            throw new Exception("Error executing statement: " . $stmt->error);
                        }
                        $stmt->close();
                    }
                    $_SESSION['success_message'] = 'Event successfully created!';
                    break;

                case 'update':
                    if (!isset($_POST['id'])) {
                        throw new Exception("Event ID is required for update");
                    }
                    
                    $id = $_POST['id'];
                    $name = $_POST['name'];
                    $description = $_POST['description'];
                    $event_date = $_POST['event_date'];

                    // Verify event exists before update
                    $checkStmt = $conn->prepare("SELECT image FROM tbl_events WHERE id = ?");
                    if (!$checkStmt) {
                        throw new Exception("Failed to prepare check statement");
                    }
                    
                    $checkStmt->bind_param("i", $id);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();
                    
                    if ($result->num_rows === 0) {
                        throw new Exception("Event not found");
                    }
                    
                    $event = $result->fetch_assoc();
                    $old_image = $event['image'];
                    $checkStmt->close();

                    // Handle image upload
                    $image = $old_image;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $target_dir = "../uploads/events/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $image = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
                        // Delete old image
                        if (file_exists($old_image)) unlink($old_image);
                    }

                    $sql = "UPDATE tbl_events SET name=?, description=?, event_date=?, image=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssi", $name, $description, $event_date, $image, $id);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Event successfully updated!';
                    break;

                case 'delete':
                    $id = $_POST['id'];
                    // Get image path before deleting
                    $stmt = $conn->prepare("SELECT image FROM tbl_events WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $event = $result->fetch_assoc();
                    
                    // Delete physical image file
                    if (file_exists($event['image'])) unlink($event['image']);
                    
                    // Delete database record
                    $sql = "DELETE FROM tbl_events WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Event successfully deleted!';
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

// Fetch all events with error handling
try {
    $sql = "SELECT * FROM tbl_events ORDER BY event_date DESC";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error fetching events: " . $conn->error);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Golf Club - Event Overview</title>
        <link rel="icon" href="../assets/photos/Logo Master KGBR.avif" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/events.css">
</head>
<body>
    <?php include '../components/sidemenu/index.php'; ?>
    <div class="main-content">
        <div class="dashboard-container">
            <div class="header-actions">
                <h1>Event Management</h1>
                <button onclick="showModal('addCourseModal')" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Event
                </button>
            </div>

            <!-- Add Event Modal -->
            <div id="addCourseModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('addCourseModal')">&times;</span>
                    <h2>Add New Event</h2>
                    <form class="course-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Event Name</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Event Date</label>
                                <input type="date" name="event_date" required>
                            </div>
                            <div class="form-group full-width">
                                <label>Description</label>
                                <textarea name="description" required></textarea>
                            </div>
                            <div class="form-group file-input-group">
                                <label>Event Image</label>
                                <input type="file" name="image" accept="image/*" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Event</button>
                    </form>
                </div>
            </div>

            <!-- Edit Event Modal -->
            <div id="editCourseModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('editCourseModal')">&times;</span>
                    <h2>Edit Event</h2>
                    <form class="course-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_course_id">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Event Name</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Event Date</label>
                                <input type="date" name="event_date" required>
                            </div>
                            <div class="form-group full-width">
                                <label>Description</label>
                                <textarea name="description" required></textarea>
                            </div>
                            <div class="form-group file-input-group">
                                <label>Event Image</label>
                                <input type="file" name="image" accept="image/*">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Event</button>
                    </form>
                </div>
            </div>

            <!-- Replace table with grid layout -->
            <div class="courses-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="course-card">
                            <div class="event-image">
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Event Image">
                            </div>
                            <div class="course-details">
                                <h3 class="course-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <div class="event-date">
                                    <i class="fas fa-calendar"></i> 
                                    <?php echo date('F d, Y', strtotime($row['event_date'])); ?>
                                </div>
                                <p class="course-description"><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="course-actions">
                                    <button onclick="viewEvent(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button onclick="editEvent(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn btn-primary">
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
                        <p>No events found</p>
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

        function editEvent(event) {
            if (!event || !event.id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid event data'
                });
                return;
            }
            
            const form = document.querySelector('#editCourseModal form');
            document.getElementById('edit_course_id').value = event.id;
            form.querySelector('input[name="name"]').value = event.name || '';
            form.querySelector('textarea[name="description"]').value = event.description || '';
            form.querySelector('input[name="event_date"]').value = event.event_date.split('T')[0] || '';
            
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
                    <div style="text-align: left; margin-bottom: 20px;">
                        <p style="margin-bottom: 15px;">${event.description}</p>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 20px 0;">
                            <div>
                                <strong>Event Date:</strong><br>
                                ${new Date(event.event_date).toLocaleDateString()}
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr; gap: 10px; margin-top: 15px;">
                            <img src="${event.image}" alt="Event Image" style="width: 100%; border-radius: 8px;">
                        </div>
                    </div>
                `,
                width: '600px',
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
    </script>
</body>
</html>