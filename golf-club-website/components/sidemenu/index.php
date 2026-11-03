<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../components/sidemenu/style.css">
</head>
<body>
    <div class="main-header">
        <div class="header-left">
            <button class="hamburger" onclick="toggleMenu()" aria-label="Toggle Menu">
                <i class="fas fa-bars"></i>
            </button>
            <h2 style="margin-left: 15px; color: var(--primary-color);">Golf Club</h2>
        </div>
        <div class="header-right">
            <a href="../dashboard/profile.php" class="header-profile" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                <img src="<?php echo isset($_SESSION['foto']) && $_SESSION['foto'] ? '../uploads/users/' . $_SESSION['foto'] : 'https://cdn-icons-png.flaticon.com/512/219/219969.png'; ?>" 
                     alt="Profile Photo" id="preview-image">
                <span><?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Guest'; ?></span>
            </a>
        </div>
    </div>

    <div class="side-menu">
        
        <div class="user-profile">
            <div class="profile-container" style="border: none;">
            <img src="../assets/photos/Logo Master KGBR.avif" 
                 alt="Golf Club Logo" 
                 style="width: 120px; height: auto; margin: 10px;">
            </div>
        </div>
        
        <ul class="menu-items">
            <li><a href="../dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="../dashboard/course.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'course.php' ? 'active-menu' : ''; ?>"><i class="fas fa-flag"></i> <span>Course Overview</span></a></li>
            <!-- <li>
                <a href="#" class="has-submenu" onclick="toggleSubmenu(event, this)">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Events & Promotions</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="../dashboard/events.php"><i class="fas fa-calendar-day"></i> <span>Events</span></a></li>
                    <li><a href="../dashboard/promotions.php"><i class="fas fa-percent"></i> <span>Promotions</span></a></li>
                </ul>
            </li> -->
            <li><a href="../dashboard/events.php"><i class="fas fa-calendar-day"></i> <span>Event</span></a></li>
            <li><a href="../dashboard/promotions.php"><i class="fas fa-percent"></i> <span>Promotion</span></a></li>
            <li><a href="../dashboard/locker.php"><i class="fas fa-door-closed"></i> <span>Locker Room</span></a></li>
            <li><a href="../dashboard/driving.php"><i class="fas fa-golf-ball"></i> <span>Driving Range</span></a></li>
            <li><a href="../dashboard/food.php"><i class="fas fa-utensils"></i> <span>Food & Beverage</span></a></li>
            <li><a href="../dashboard/vip.php"><i class="fas fa-crown"></i> <span>VIP Room & Matana</span></a></li>
            <!-- <li>
                <a href="#" class="has-submenu" onclick="toggleSubmenu(event, this)">
                    <i class="fas fa-heart"></i>
                    <span>Wedding Services</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="../dashboard/pre-wedding.php"><i class="fas fa-camera"></i> <span>Pre-Wedding Shoot</span></a></li>
                    <li><a href="../dashboard/wedding.php"><i class="fas fa-ring"></i> <span>Wedding Venue</span></a></li>
                </ul>
            </li> -->
            <!-- <li><a href="../dashboard/pre-wedding.php"><i class="fas fa-camera"></i> <span>Pre-Wedding Shoot</span></a></li> -->
            <li><a href="../dashboard/wedding.php"><i class="fas fa-ring"></i> <span>Wedding Venue</span></a></li>
            <li><a href="../dashboard/meet.php"><i class="fas fa-handshake"></i> <span>Meeting & Gathering</span></a></li>
            <li><a href="../dashboard/booking.php"><i class="fas fa-book"></i> <span>Booking</span></a></li>
            <li class="logout-item">
                <a href="#" onclick="showLogoutModal(event)" class="logout-link" 
                   style="display: flex; align-items: center; padding: 12px 15px; 
                          border-radius: 8px; margin: 10px 15px; 
                          background: rgba(220, 53, 69, 0.1); 
                          transition: all 0.3s ease;">
                    <i class="fas fa-sign-out-alt" style="color: #dc3545; margin-right: 10px; font-size: 1.1em;"></i>
                    <span style="color: #dc3545; font-weight: 600;">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal" class="modal-overlay">
        <div class="modal-content">
            <h4>Konfirmasi Logout</h4>
            <p>Apakah Anda yakin ingin keluar?</p>
            <div class="modal-buttons">
                <button onclick="logout()" class="btn-danger">Ya, Keluar</button>
                <button onclick="hideLogoutModal()" class="btn-secondary">Batal</button>
            </div>
        </div>
    </div>

    <style>
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>

    <script>
        // Load menu state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sideMenu = document.querySelector('.side-menu');
            const mainHeader = document.querySelector('.main-header');
            const mainContent = document.querySelector('.main-content');
            
            // Check localStorage for saved state
            if(localStorage.getItem('menuState') === 'collapsed') {
                sideMenu.classList.add('collapsed');
                mainHeader.style.left = '70px';
                mainHeader.style.width = 'calc(100% - 70px)';
                if(mainContent) {
                    mainContent.style.marginLeft = '70px';
                    mainContent.style.width = 'calc(100% - 70px)';
                }
            }

            // Create overlay for mobile
            const overlay = document.createElement('div');
            overlay.className = 'menu-overlay';
            document.body.appendChild(overlay);
            
            overlay.addEventListener('click', function() {
                document.querySelector('.side-menu').classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        });

        function toggleMenu() {
            const sideMenu = document.querySelector('.side-menu');
            const mainHeader = document.querySelector('.main-header');
            const mainContent = document.querySelector('.main-content');
            
            if (window.innerWidth <= 768) {
                // Mobile behavior
                sideMenu.classList.toggle('active');
                document.querySelector('.menu-overlay').classList.toggle('active');
            } else {
                // Desktop behavior
                sideMenu.classList.toggle('collapsed');
                
                // Save menu state to localStorage
                localStorage.setItem('menuState', sideMenu.classList.contains('collapsed') ? 'collapsed' : 'expanded');
                
                // Adjust header and content width
                if (sideMenu.classList.contains('collapsed')) {
                    mainHeader.style.left = '70px';
                    mainHeader.style.width = 'calc(100% - 70px)';
                    mainContent.style.marginLeft = '70px';
                    mainContent.style.width = 'calc(100% - 70px)';
                } else {
                    mainHeader.style.left = '250px';
                    mainHeader.style.width = 'calc(100% - 250px)';
                    mainContent.style.marginLeft = '250px';
                    mainContent.style.width = 'calc(100% - 250px)';
                }
            }
        }

    // Close menu when clicking overlay
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.createElement('div');
        overlay.className = 'menu-overlay';
        document.body.appendChild(overlay);

        overlay.addEventListener('click', function() {
            document.querySelector('.side-menu').classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    });

        function toggleSubmenu(event, element) {
            event.preventDefault();
            if (!document.querySelector('.side-menu').classList.contains('collapsed')) {
                const submenu = element.nextElementSibling;
                const dropdownIcon = element.querySelector('.dropdown-icon');
                submenu.classList.toggle('show');
                dropdownIcon.classList.toggle('rotate');
            }
        }

        function showLogoutModal(event) {
            event.preventDefault();
            document.getElementById('logoutModal').style.display = 'flex';
        }
        
        function hideLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }
        
        function logout() {
            window.location.href = '../dashboard/logout.php';
        }
    </script>
</body>
</html>
