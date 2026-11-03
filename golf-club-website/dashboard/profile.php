<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

// Handle AJAX form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    try {
        $nama = $_POST['nama'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        
        // Validate inputs
        if (empty($nama) || empty($username) || empty($email)) {
            throw new Exception("Name, username, and email are required");
        }
        
        $updates = ["nama = ?", "username = ?", "email = ?"];
        $params = [$nama, $username, $email];
        $types = "sss";
        
        if (!empty($password)) {
            $updates[] = "password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
            $types .= "s";
        }
        
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $foto = time() . '_' . $_FILES['foto']['name'];
            $target_path = "../uploads/users/" . $foto;
            
            if (!is_dir("../uploads/users")) {
                mkdir("../uploads/users", 0777, true);
            }
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
                $updates[] = "foto = ?";
                $params[] = $foto;
                $types .= "s";
            } else {
                throw new Exception("Failed to upload image");
            }
        }
        
        $params[] = $_SESSION['user_id'];
        $types .= "i";
        
        $sql = "UPDATE tbl_users SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
            exit;
        } else {
            throw new Exception("Failed to update profile");
        }
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

// Fetch user data
try {
    $stmt = $conn->prepare("SELECT nama, username, foto, email FROM tbl_users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golf Club - My Profile</title>
    <link rel="icon" href="../assets/photos/Logo Master KGBR.avif" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/profile.css">
    <style>
        .modal-content {
            display: flex;
            flex-direction: column;
            max-height: 90vh;
            height: auto;
        }

        .modal-header {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            border-bottom: 1px solid #eee;
        }

        .modal-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            max-height: calc(90vh - 130px);
        }

        .modal-footer {
            position: sticky;
            bottom: 0;
            background: white;
            z-index: 10;
            border-top: 1px solid #eee;
            padding: 15px;
        }

        @media (max-width: 768px) {
            .modal-content {
                height: 90vh;
            }
            
            .modal-body {
                max-height: calc(90vh - 120px);
            }
        }

        .password-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-input-wrapper input {
            flex: 1;
            padding-right: 40px;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            padding: 5px;
        }

        .toggle-password:hover {
            color: #333;
        }

        /* Notification Styles */
        .notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.95);
            padding: 20px 30px;
            border-radius: 16px;
            color: #fff;
            font-weight: 500;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 320px;
            background: rgba(23, 25, 35, 0.9);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .notification.show {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        .notification.success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(6, 95, 70, 0.95));
        }

        .notification.error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.95), rgba(153, 27, 27, 0.95));
        }

        .notification i {
            font-size: 24px;
            height: 24px;
            width: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification #notification-message {
            font-size: 15px;
            line-height: 1.5;
            font-weight: 400;
            letter-spacing: 0.3px;
        }

        @media (max-width: 768px) {
            .notification {
                min-width: calc(100% - 32px);
                margin: 0 16px;
                padding: 16px 20px;
            }
        }

        @keyframes notification-pulse {
            0% { transform: translate(-50%, -50%) scale(0.95); opacity: 0; }
            50% { transform: translate(-50%, -50%) scale(1.02); opacity: 0.9; }
            100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
        }

        .notification.show {
            animation: notification-pulse 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }
    </style>
</head>
<body>
    <?php include '../components/sidemenu/index.php'; ?>
    
    <div class="main-content">
        <div class="content-card">
            <div class="profile-header">
                <h1><i class="fas fa-user-circle"></i> My Profile</h1>
                <div class="profile-photo">
                    <img src="<?php echo $user['foto'] ? '../uploads/users/' . $user['foto'] : 'https://cdn-icons-png.flaticon.com/512/219/219969.png'; ?>" 
                         alt="Profile Photo" id="preview-image">
                </div>
                <div class="profile-actions">
                    <button class="btn-edit" onclick="openEditModal()">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                </div>
            </div>

            <div class="profile-info">
                <div class="info-grid">
                    <div class="info-group">
                        <label><i class="fas fa-user"></i> Full Name</label>
                        <p><?php echo htmlspecialchars($user['nama'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="info-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <p><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="info-group">
                        <label><i class="fas fa-at"></i> Username</label>
                        <p><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="info-group">
                        <label><i class="fas fa-shield-alt"></i> Password</label>
                        <p>••••••••</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal" id="editProfileModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Profile</h2>
                <button type="button" class="close-modal" onclick="closeEditModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editProfileForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body" style="background: #f7f8fa; border-radius: 0 0 18px 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                    <div class="form-group photo-upload" style="margin-bottom: 28px;">
                        <label style="font-weight: 600; color: #222; margin-bottom: 10px; display: block;">
                            <i class="fas fa-image"></i> Profile Photo
                        </label>
                        <div class="preview-container" style="display: flex; justify-content: center; align-items: center; margin-bottom: 12px;">
                            <img id="modalPreview" src="<?php echo $user['foto'] ? '../uploads/users/' . $user['foto'] : 'https://cdn-icons-png.flaticon.com/512/219/219969.png'; ?>" alt="Preview" style="width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 3px solid #e0e7ef; box-shadow: 0 2px 8px rgba(0,0,0,0.07);">
                        </div>
                        <div class="file-input-container" style="display: flex; justify-content: center;">
                            <input type="file" name="foto" accept="image/*" id="fotoInput" onchange="previewModalImage(this)" style="display: none;">
                            <label for="fotoInput" class="file-label" style="background: #e0e7ef; color: #222; padding: 8px 18px; border-radius: 22px; cursor: pointer; font-size: 15px; font-weight: 500; transition: background 0.2s;">
                                <i class="fas fa-upload"></i> Choose Photo
                            </label>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="font-weight: 600; color: #222; margin-bottom: 6px; display: block;">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <input type="text" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #e0e7ef; background: #fff; font-size: 15px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="font-weight: 600; color: #222; margin-bottom: 6px; display: block;">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="text" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #e0e7ef; background: #fff; font-size: 15px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="font-weight: 600; color: #222; margin-bottom: 6px; display: block;">
                            <i class="fas fa-at"></i> Username
                        </label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #e0e7ef; background: #fff; font-size: 15px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 28px;">
                        <label style="font-weight: 600; color: #222; margin-bottom: 6px; display: block;">
                            <i class="fas fa-key"></i> New Password
                        </label>
                        <div class="password-input-wrapper" style="background: #fff; border-radius: 10px; border: 1px solid #e0e7ef; padding-right: 0;">
                            <input type="password" name="password" id="passwordInput" placeholder="Leave blank to keep current password" style="width: 100%; padding: 10px 14px; border: none; background: transparent; font-size: 15px; border-radius: 10px 0 0 10px;">
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility()" style="right: 8px;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group" style="display: flex; justify-content: flex-end; gap: 12px;">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()" style="background: #e0e7ef; color: #222; border: none; border-radius: 8px; padding: 9px 22px; font-size: 15px; font-weight: 500; cursor: pointer; transition: background 0.2s;">
                            Cancel
                        </button>
                        <button type="submit" class="btn-save" style="background: linear-gradient(90deg, #16a085 0%, #2980b9 100%); color: #fff; border: none; border-radius: 8px; padding: 9px 22px; font-size: 15px; font-weight: 500; cursor: pointer; box-shadow: 0 2px 8px rgba(22,160,133,0.07); transition: background 0.2s;">
                            Save Changes
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Notification Component -->
    <div id="notification" class="notification">
        <i class="fas"></i>
        <span id="notification-message"></span>
    </div>

    <script>
        function openEditModal() {
            document.getElementById('editProfileModal').classList.add('show');
        }

        function closeEditModal() {
            document.getElementById('editProfileModal').classList.remove('show');
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewModalImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('modalPreview').src = e.target.result;
                    document.getElementById('preview-image').src = e.target.result; // Update main profile image
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                closeEditModal();
            }
        });

        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const icon = notification.querySelector('i');
            const messageEl = document.getElementById('notification-message');
            
            // Remove existing show class and reset animation
            notification.classList.remove('show');
            void notification.offsetWidth;
            
            // Set icon and class based on type
            if (type === 'success') {
                notification.className = 'notification success';
                icon.className = 'fas fa-check-circle fa-bounce';
            } else {
                notification.className = 'notification error';
                icon.className = 'fas fa-exclamation-circle fa-shake';
            }
            
            messageEl.textContent = message;
            notification.classList.add('show');
            
            // Hide notification after 3 seconds with fade out
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.classList.remove('show');
                    notification.style.opacity = '';
                }, 400);
            }, 3000);
        }

        // Handle form submission
        document.getElementById('editProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Invalid JSON response: ' + text);
                    }
                });
            })
            .then(data => {
                if (data.status === 'success') {
                    showNotification(data.message, 'success');
                    closeEditModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating profile: ' + error.message, 'error');
            });
        });

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const toggleBtn = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.classList.remove('fa-eye');
                toggleBtn.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleBtn.classList.remove('fa-eye-slash');
                toggleBtn.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
