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
                    $length = $_POST['length'];
                    $par = $_POST['par'];

                    // Handle view image upload
                    $view_image = '';
                    if (isset($_FILES['view_image']) && $_FILES['view_image']['error'] == 0) {
                        $target_dir = "../uploads/courses/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $view_image = $target_dir . time() . "_view_" . basename($_FILES["view_image"]["name"]);
                        move_uploaded_file($_FILES["view_image"]["tmp_name"], $view_image);
                    }

                    // Handle field image upload
                    $field_image = '';
                    if (isset($_FILES['field_image']) && $_FILES['field_image']['error'] == 0) {
                        $target_dir = "../uploads/courses/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $field_image = $target_dir . time() . "_field_" . basename($_FILES["field_image"]["name"]);
                        move_uploaded_file($_FILES["field_image"]["tmp_name"], $field_image);
                    }

                    $sql = "INSERT INTO tbl_courses (name, description, length, par, view_image, field_image) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("ssiiss", $name, $description, $length, $par, $view_image, $field_image);
                        if (!$stmt->execute()) {
                            throw new Exception("Error executing statement: " . $stmt->error);
                        }
                        $stmt->close();
                    }
                    $_SESSION['success_message'] = 'Course successfully created!';
                    break;

                case 'update':
                    if (!isset($_POST['id'])) {
                        throw new Exception("Course ID is required for update");
                    }
                    
                    $id = $_POST['id'];
                    $name = $_POST['name'];
                    $description = $_POST['description'];
                    $length = $_POST['length'];
                    $par = $_POST['par'];

                    // Verify course exists before update
                    $checkStmt = $conn->prepare("SELECT view_image, field_image FROM tbl_courses WHERE id = ?");
                    if (!$checkStmt) {
                        throw new Exception("Failed to prepare check statement");
                    }
                    
                    $checkStmt->bind_param("i", $id);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();
                    
                    if ($result->num_rows === 0) {
                        throw new Exception("Course not found");
                    }
                    
                    $course = $result->fetch_assoc();
                    $old_view_image = $course['view_image'];
                    $old_field_image = $course['field_image'];
                    $checkStmt->close();

                    // Handle view image upload
                    $view_image = $old_view_image;
                    if (isset($_FILES['view_image']) && $_FILES['view_image']['error'] == 0) {
                        $target_dir = "../uploads/courses/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $view_image = $target_dir . time() . "_view_" . basename($_FILES["view_image"]["name"]);
                        move_uploaded_file($_FILES["view_image"]["tmp_name"], $view_image);
                        // Delete old view image
                        if (file_exists($old_view_image)) unlink($old_view_image);
                    }

                    // Handle field image upload
                    $field_image = $old_field_image;
                    if (isset($_FILES['field_image']) && $_FILES['field_image']['error'] == 0) {
                        $target_dir = "../uploads/courses/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $field_image = $target_dir . time() . "_field_" . basename($_FILES["field_image"]["name"]);
                        move_uploaded_file($_FILES["field_image"]["tmp_name"], $field_image);
                        // Delete old field image
                        if (file_exists($old_field_image)) unlink($old_field_image);
                    }

                    $sql = "UPDATE tbl_courses SET name=?, description=?, length=?, par=?, view_image=?, field_image=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssiissi", $name, $description, $length, $par, $view_image, $field_image, $id);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Course successfully updated!';
                    break;

                case 'delete':
                    $id = $_POST['id'];
                    // Get image paths before deleting
                    $stmt = $conn->prepare("SELECT view_image, field_image FROM tbl_courses WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $course = $result->fetch_assoc();
                    
                    // Delete physical image files
                    if (file_exists($course['view_image'])) unlink($course['view_image']);
                    if (file_exists($course['field_image'])) unlink($course['field_image']);
                    
                    // Delete database record
                    $sql = "DELETE FROM tbl_courses WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Course successfully deleted!';
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

// Fetch all courses with error handling
try {
    $sql = "SELECT * FROM tbl_courses";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error fetching courses: " . $conn->error);
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
    <title>Golf Club - Course Overview</title>
    <link rel="icon" href="../assets/photos/Logo Master KGBR.avif" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/course.css">
</head>
<body>
    <?php include '../components/sidemenu/index.php'; ?>
    <div class="main-content">
        <div class="dashboard-container">
            <div class="header-actions">
                <div class="filters">
                    <input type="text" id="searchInput" placeholder="Search courses..." class="filter-input">
                    <select id="filterLength" class="filter-input">
                        <option value="">All Lengths</option>
                        <option value="5000-">&lt; 5000 yards</option>
                        <option value="5000-6000">5000-6000 yards</option>
                        <option value="6000+">6000+ yards</option>
                    </select>
                    <select id="filterPar" class="filter-input">
                        <option value="">All Pars</option>
                        <option value="70-">&lt; 70</option>
                        <option value="70-72">70-72</option>
                        <option value="72+">72+</option>
                    </select>
                </div>
                <button onclick="showModal('addCourseModal')" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Course
                </button>
            </div>

            <!-- Add Course Modal -->
            <div id="addCourseModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('addCourseModal')">&times;</span>
                    <h2>Add New Course</h2>
                    <form class="course-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Course Name</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Length (yards)</label>
                                <input type="number" name="length" required>
                            </div>
                            <div class="form-group">
                                <label>Par</label>
                                <input type="number" name="par" required>
                            </div>
                            <div class="form-group full-width">
                                <label>Description</label>
                                <textarea name="description" required></textarea>
                            </div>
                            <div class="form-group file-input-group">
                                <label>View Image</label>
                                <input type="file" name="view_image" accept="image/*" required>
                            </div>
                            <div class="form-group file-input-group">
                                <label>Field Image</label>
                                <input type="file" name="field_image" accept="image/*" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Course</button>
                    </form>
                </div>
            </div>

            <!-- Edit Course Modal -->
            <div id="editCourseModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('editCourseModal')">&times;</span>
                    <h2>Edit Course</h2>
                    <form class="course-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_course_id">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Course Name</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Length (yards)</label>
                                <input type="number" name="length" required>
                            </div>
                            <div class="form-group">
                                <label>Par</label>
                                <input type="number" name="par" required>
                            </div>
                            <div class="form-group full-width">
                                <label>Description</label>
                                <textarea name="description" required></textarea>
                            </div>
                            <div class="form-group file-input-group">
                                <label>View Image</label>
                                <input type="file" name="view_image" accept="image/*">
                            </div>
                            <div class="form-group file-input-group">
                                <label>Field Image</label>
                                <input type="file" name="field_image" accept="image/*">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Course</button>
                    </form>
                </div>
            </div>

            <!-- Replace table with grid layout -->
            <div class="courses-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="course-card">
                            <div class="course-images">
                                <img src="<?php echo htmlspecialchars($row['view_image']); ?>" alt="View">
                                <img src="<?php echo htmlspecialchars($row['field_image']); ?>" alt="Field">
                            </div>
                            <div class="course-details">
                                <h3 class="course-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p class="course-description"><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="course-stats">
                                    <div class="stat-item">
                                        <div class="stat-label">Length</div>
                                        <div class="stat-value"><?php echo htmlspecialchars($row['length']); ?>y</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-label">Par</div>
                                        <div class="stat-value"><?php echo htmlspecialchars($row['par']); ?></div>
                                    </div>
                                </div>
                                <div class="course-actions">
                                    <button onclick="viewCourse(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button onclick="editCourse(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn btn-primary">
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
                        <p>Belum ada course yang tersedia.</p>
                        <p>Silakan tambahkan course baru dengan menekan tombol <b>Add New Course</b>.</p>
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
                        message = 'Adding new course...';
                        break;
                    case 'update':
                        message = 'Updating course...';
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

        function editCourse(course) {
            if (!course || !course.id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid course data'
                });
                return;
            }
            
            const form = document.querySelector('#editCourseModal form');
            document.getElementById('edit_course_id').value = course.id;
            form.querySelector('input[name="name"]').value = course.name || '';
            form.querySelector('textarea[name="description"]').value = course.description || '';
            form.querySelector('input[name="length"]').value = course.length || '';
            form.querySelector('input[name="par"]').value = course.par || '';
            
            showModal('editCourseModal');
        }

        // Add view function after existing modal functions
        function viewCourse(course) {
            if (!course || !course.id) {
                return;
            }
            
            Swal.fire({
                title: course.name,
                html: `
                    <div style="text-align: left; margin-bottom: 20px;">
                        <p style="margin-bottom: 15px;">${course.description}</p>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 20px 0;">
                            <div>
                                <strong>Length:</strong><br>
                                ${course.length} yards
                            </div>
                            <div>
                                <strong>Par:</strong><br>
                                ${course.par}
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px;">
                            <img src="${course.view_image}" alt="View" style="width: 100%; border-radius: 8px;">
                            <img src="${course.field_image}" alt="Field" style="width: 100%; border-radius: 8px;">
                        </div>
                    </div>
                `,
                width: '600px',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    container: 'view-course-modal'
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

        // Add filter functionality
        let filterTimeout;
        
        function debounce(func, wait) {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(func, wait);
        }

        function filterCourses() {
            const searchQuery = document.getElementById('searchInput').value.toLowerCase();
            const lengthFilter = document.getElementById('filterLength').value;
            const parFilter = document.getElementById('filterPar').value;

            fetch(`filter_courses.php?search=${encodeURIComponent(searchQuery)}&length=${lengthFilter}&par=${parFilter}`)
                .then(response => response.json())
                .then(data => {
                    const coursesGrid = document.querySelector('.courses-grid');
                    coursesGrid.innerHTML = '';

                    if (data.length === 0) {
                        coursesGrid.innerHTML = '<div class="no-courses"><p>No courses found</p></div>';
                        return;
                    }

                    data.forEach(course => {
                        coursesGrid.innerHTML += `
                            <div class="course-card">
                                <div class="course-images">
                                    <img src="${course.view_image}" alt="View">
                                    <img src="${course.field_image}" alt="Field">
                                </div>
                                <div class="course-details">
                                    <h3 class="course-name">${course.name}</h3>
                                    <p class="course-description">${course.description}</p>
                                    <div class="course-stats">
                                        <div class="stat-item">
                                            <div class="stat-label">Length</div>
                                            <div class="stat-value">${course.length}y</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-label">Par</div>
                                            <div class="stat-value">${course.par}</div>
                                        </div>
                                    </div>
                                    <div class="course-actions">
                                        <button onclick="viewCourse(${JSON.stringify(course)})" class="btn btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button onclick="editCourse(${JSON.stringify(course)})" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" style="display:inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="${course.id}">
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        // Add event listeners for filters
        document.getElementById('searchInput').addEventListener('input', () => debounce(filterCourses, 300));
        document.getElementById('filterLength').addEventListener('change', filterCourses);
        document.getElementById('filterPar').addEventListener('change', filterCourses);
    </script>
</body>
</html>