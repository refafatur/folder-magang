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
                    $category = $_POST['category'];

                    // Handle image upload
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $target_dir = "../uploads/food/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $image_path = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
                        
                        $sql = "INSERT INTO tbl_food (image_path, category) VALUES (?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ss", $image_path, $category);
                        $stmt->execute();
                        $stmt->close();
                    }
                    $_SESSION['success_message'] = 'Food/Beverage item successfully created!';
                    break;

                case 'update':
                    if (!isset($_POST['id'])) {
                        throw new Exception("Item ID is required for update");
                    }
                    
                    $id = $_POST['id'];
                    $category = $_POST['category'];

                    // Get old image path
                    $checkStmt = $conn->prepare("SELECT image_path FROM tbl_food WHERE id = ?");
                    $checkStmt->bind_param("i", $id);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();
                    $old_image = $result->fetch_assoc()['image_path'];
                    $checkStmt->close();

                    // Handle new image upload
                    $image_path = $old_image;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $target_dir = "../uploads/food/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $image_path = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
                        if (file_exists($old_image)) unlink($old_image);
                    }

                    $sql = "UPDATE tbl_food SET image_path=?, category=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssi", $image_path, $category, $id);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Food/Beverage item successfully updated!';
                    break;

                case 'delete':
                    $id = $_POST['id'];
                    // Get image path before deleting
                    $stmt = $conn->prepare("SELECT image_path FROM tbl_food WHERE id = ?");  // Changed table
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $event = $result->fetch_assoc();
                    
                    // Delete physical image file
                    if (file_exists($event['image_path'])) unlink($event['image_path']);
                    
                    // Delete database record
                    $sql = "DELETE FROM tbl_food WHERE id = ?";  // Changed table
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Food/Beverage item successfully deleted!';
                    break;

                case 'create_menu':
                    $name = $_POST['name'];
                    $description = $_POST['description'];
                    $category = $_POST['category'];
                    $subcategory = $_POST['subcategory'];
                    $is_bestseller = isset($_POST['is_bestseller']) ? 1 : 0;
                    $is_popular = isset($_POST['is_popular']) ? 1 : 0;

                    $image_path = null;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $target_dir = "../uploads/menu/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $image_path = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
                    }

                    $sql = "INSERT INTO tbl_menu (name, description, category, subcategory, image_path, is_bestseller, is_popular) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssii", $name, $description, $category, $subcategory, $image_path, $is_bestseller, $is_popular);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Menu item successfully created!';
                    break;

                case 'update_menu':
                    if (!isset($_POST['id'])) {
                        throw new Exception("Menu ID is required for update");
                    }
                    
                    $id = $_POST['id'];
                    $name = $_POST['name'];
                    $description = $_POST['description'];
                    $category = $_POST['category'];
                    $subcategory = $_POST['subcategory'];
                    $is_bestseller = isset($_POST['is_bestseller']) ? 1 : 0;
                    $is_popular = isset($_POST['is_popular']) ? 1 : 0;

                    // Get old image path
                    $checkStmt = $conn->prepare("SELECT image_path FROM tbl_menu WHERE id = ?");
                    $checkStmt->bind_param("i", $id);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();
                    $old_image = $result->fetch_assoc()['image_path'];
                    $checkStmt->close();

                    // Handle new image upload
                    $image_path = $old_image;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $target_dir = "../uploads/menu/";
                        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                        $image_path = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
                        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
                        if ($old_image && file_exists($old_image)) unlink($old_image);
                    }

                    $sql = "UPDATE tbl_menu SET name=?, description=?, category=?, subcategory=?, 
                            image_path=?, is_bestseller=?, is_popular=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssiii", $name, $description, $category, $subcategory, 
                                      $image_path, $is_bestseller, $is_popular, $id);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Menu item successfully updated!';
                    break;

                case 'delete_menu':
                    $id = $_POST['id'];
                    // Get image path before deleting
                    $stmt = $conn->prepare("SELECT image_path FROM tbl_menu WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $menu = $result->fetch_assoc();
                    
                    // Delete physical image file
                    if ($menu['image_path'] && file_exists($menu['image_path'])) unlink($menu['image_path']);
                    
                    // Delete database record
                    $sql = "DELETE FROM tbl_menu WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $_SESSION['success_message'] = 'Menu item successfully deleted!';
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

// Fetch all food items with error handling
try {
    $sql = "SELECT * FROM tbl_food ORDER BY created_at DESC";  // Changed table and ordering
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error fetching food items: " . $conn->error);
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
    <title>Golf Club - Food & Beverage Management</title>
        <link rel="icon" href="../assets/photos/Logo Master KGBR.avif" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/food.css">
    <style>
        .view-toggle {
            margin-bottom: 20px;
            text-align: right;
        }
        
        .toggle-btn {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .toggle-btn:hover {
            background: #45a049;
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        /* Menu Section Styles */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            padding: 20px;
        }

        .menu-card {
            background: #1E1E1E;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .menu-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .menu-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .menu-card:hover .menu-image img {
            transform: scale(1.05);
        }

        .category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(218, 165, 32, 0.9);
            color: #1E1E1E;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
            z-index: 1;
            backdrop-filter: blur(5px);
        }

        .menu-details {
            padding: 20px;
        }

        .menu-details h3 {
            color: #DAA520;
            font-size: 1.4em;
            margin: 0 0 10px;
            font-weight: 500;
        }

        .menu-details p {
            color: #888;
            font-size: 0.95em;
            line-height: 1.5;
            margin: 0 0 15px;
        }

        .menu-badges {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: 500;
        }

        .badge-bestseller {
            background-color: #FFD700;
            color: #000;
        }

        .badge-popular {
            background-color: #FF4500;
            color: #fff;
        }

        .menu-actions {
            padding: 0 20px 20px;
            display: flex;
            gap: 10px;
        }

        .menu-actions button {
            flex: 1;
            padding: 8px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        /* Filter Buttons Style */
        .filter-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 0 0 30px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: transparent;
            color: #DAA520;
            border: 2px solid #DAA520;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0;
        }

        .filter-btn:hover, .filter-btn.active {
            background: #DAA520;
            color: #1E1E1E;
        }

        /* Sub-filters Container */
        .sub-filter-container {
            margin-bottom: 30px;
        }

        .sub-filters {
            display: none;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            padding: 15px 0;
        }

        .sub-btn {
            background: transparent;
            color: #888;
            border: 1px solid #888;
            padding: 6px 15px;
            border-radius: 15px;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }

        .sub-btn:hover, .sub-btn.active {
            background: #888;
            color: #1E1E1E;
        }

        .checkbox-group {
            display: flex;
            gap: 20px;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php include '../components/sidemenu/index.php'; ?>
    <div class="main-content">
        <div class="dashboard-container">
            <!-- <div class="view-toggle">
                <button class="toggle-btn" onclick="toggleView()">
                    <span id="toggleText">Switch to Menu View</span>
                </button>
            </div> -->

            <!-- Food & Beverage Section -->
            <div id="foodSection" class="section active">
                <div class="header-actions">
                    <h1>Food & Beverage Management</h1>
                    <button onclick="showModal('addFoodModal')" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Item
                    </button>
                </div>
                <div class="courses-grid">
                    <?php 
                    $sql = "SELECT * FROM tbl_food ORDER BY created_at DESC";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0): 
                        while($row = $result->fetch_assoc()): 
                    ?>
                        <div class="course-card">
                            <div class="category-badge <?php echo $row['category']; ?>">
                                <?php echo ucfirst($row['category']); ?>
                            </div>
                            <div class="event-image">
                                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Food Image">
                            </div>
                            <div class="course-actions">
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
                    <?php endwhile; endif; ?>
                </div>
            </div>

            <!-- Add Food Modal -->
            <div id="addFoodModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('addFoodModal')">&times;</span>
                    <h2>Add New Food/Beverage Item</h2>
                    <form class="food-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="food">Food</option>
                                    <option value="beverage">Beverage</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" name="image" accept="image/*" required>
                            </div>
                            <!-- <div class="form-group checkbox-group">
                                <label>
                                    <input type="checkbox" name="is_bestseller" value="1">
                                    Mark as Best Seller
                                </label>
                                <label>
                                    <input type="checkbox" name="is_popular" value="1">
                                    Mark as Popular
                                </label>
                            </div> -->
                        </div>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </form>
                </div>
            </div>

            <!-- Edit Food Modal -->
            <div id="editFoodModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('editFoodModal')">&times;</span>
                    <h2>Edit Food/Beverage Item</h2>
                    <form class="food-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_food_id">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category" id="edit_food_category" required>
                                    <option value="">Select Category</option>
                                    <option value="food">Food</option>
                                    <option value="beverage">Beverage</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" name="image" accept="image/*">
                                <small>Leave empty to keep current image</small>
                            </div>
                            <!-- <div class="form-group checkbox-group">
                                <label>
                                    <input type="checkbox" name="is_bestseller" value="1" id="edit_menu_bestseller">
                                    Mark as Best Seller
                                </label>
                                <label>
                                    <input type="checkbox" name="is_popular" value="1" id="edit_menu_popular">
                                    Mark as Popular
                                </label>
                            </div> -->
                        </div>
                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </form>
                </div>
            </div>

            <!-- Menu Section -->
            <div id="menuSection" class="section">
                <div class="header-actions">
                    <h1>Restaurant Menu Management</h1>
                    <button onclick="showModal('addMenuModal')" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Menu Item
                    </button>
                </div>

                <div class="filter-buttons">
                    <button class="filter-btn active" data-category="all">All Items</button>
                    <button class="filter-btn" data-category="appetizer">Appetizer</button>
                    <button class="filter-btn" data-category="main">Main Course</button>
                    <button class="filter-btn" data-category="beverage">Beverage</button>
                    <button class="filter-btn" data-category="dessert">Dessert</button>
                </div>

                <div class="sub-filter-container">
                    <div class="sub-filters" id="appetizer-filters">
                        <button class="sub-btn active" data-subcategory="all">All Appetizers</button>
                        <button class="sub-btn" data-subcategory="small-bites">Small Bites</button>
                        <button class="sub-btn" data-subcategory="breads">Breads</button>
                        <button class="sub-btn" data-subcategory="soup-salad">Soup & Salad</button>
                        <button class="sub-btn" data-subcategory="noodles">Noodles</button>
                    </div>
                    <div class="sub-filters" id="main-filters">
                        <button class="sub-btn active" data-subcategory="all">All Mains</button>
                        <button class="sub-btn" data-subcategory="rice">Rice</button>
                        <button class="sub-btn" data-subcategory="poultry">Poultry</button>
                        <button class="sub-btn" data-subcategory="steak">Steak</button>
                        <button class="sub-btn" data-subcategory="fish-seafood">Fish & Seafood</button>
                    </div>
                    <div class="sub-filters" id="beverage-filters">
                        <button class="sub-btn active" data-subcategory="all">All Beverages</button>
                        <button class="sub-btn" data-subcategory="juice">Juice</button>
                        <button class="sub-btn" data-subcategory="coffee-tea">Coffee & Tea</button>
                    </div>
                    <div class="sub-filters" id="dessert-filters">
                        <button class="sub-btn active" data-subcategory="all">All Desserts</button>
                    </div>
                </div>

                <div class="menu-grid">
                    <?php 
                    $sql = "SELECT * FROM tbl_menu ORDER BY category, subcategory";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0): 
                        while($row = $result->fetch_assoc()): 
                    ?>
                        <div class="menu-card" data-category="<?php echo $row['category']; ?>" data-subcategory="<?php echo $row['subcategory']; ?>">
                            <div class="category-badge">
                                <?php echo ucfirst($row['category']); ?>
                                <?php if($row['subcategory']): ?>
                                    - <?php echo ucfirst($row['subcategory']); ?>
                                <?php endif; ?>
                            </div>
                            <?php if($row['image_path']): ?>
                            <div class="menu-image">
                                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Menu Image">
                            </div>
                            <?php endif; ?>
                            <div class="menu-details">
                                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="menu-badges">
                                    <?php if($row['is_bestseller']): ?>
                                        <span class="badge badge-bestseller">Best Seller</span>
                                    <?php endif; ?>
                                    <?php if($row['is_popular']): ?>
                                        <span class="badge badge-popular">Popular</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="menu-actions">
                                <button onclick="editMenuItem(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="delete_menu">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>

            <!-- Add Menu Modal -->
            <div id="addMenuModal" class="modal">
                <div class="modal-content">
                    <span class="close-modal" onclick="closeModal('addMenuModal')">&times;</span>
                    <h2>Add New Menu Item</h2>
                    <form class="menu-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create_menu">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category" id="add_menu_category" onchange="updateSubcategories(this)" required>
                                    <option value="">Select Category</option>
                                    <option value="appetizer">Appetizer</option>
                                    <option value="main">Main Course</option>
                                    <option value="beverage">Beverage</option>
                                    <option value="dessert">Dessert</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Subcategory</label>
                                <select name="subcategory" id="add_menu_subcategory" required>
                                    <option value="">Select Category First</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" name="image" accept="image/*">
                            </div>
                            <div class="form-group checkbox-group">
                                <label>
                                    <input type="checkbox" name="is_bestseller" value="1">
                                    Mark as Best Seller
                                </label>
                                <label>
                                    <input type="checkbox" name="is_popular" value="1">
                                    Mark as Popular
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Menu Item</button>
                    </form>
                </div>
            </div>

            <!-- Edit Menu Modal -->
    <div id="editMenuModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('editMenuModal')">&times;</span>
            <h2>Edit Menu Item</h2>
            <form class="menu-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_menu">
                <input type="hidden" name="id" id="edit_menu_id">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="edit_menu_name" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="edit_menu_description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" id="edit_menu_category" onchange="updateSubcategories(this)" required>
                            <option value="">Select Category</option>
                            <option value="appetizer">Appetizer</option>
                            <option value="main">Main Course</option>
                            <option value="beverage">Beverage</option>
                            <option value="dessert">Dessert</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subcategory</label>
                        <select name="subcategory" id="edit_menu_subcategory" required>
                            <option value="">Select Category First</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="is_bestseller" value="1" id="edit_menu_bestseller">
                            Mark as Best Seller
                        </label>
                        <label>
                            <input type="checkbox" name="is_popular" value="1" id="edit_menu_popular">
                            Mark as Popular
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Menu Item</button>
            </form>
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
                if (modalId === 'editCourseModal' || modalId === 'editMenuModal') {
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
                        message = 'Adding new item...';
                        break;
                    case 'update':
                        message = 'Updating item...';
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
                    text: 'Invalid item data'
                });
                return;
            }
            
            const form = document.querySelector('#editFoodModal form');
            document.getElementById('edit_food_id').value = event.id;
            document.getElementById('edit_food_category').value = event.category || 'food';
            
            showModal('editFoodModal');
        }

        function editMenuItem(menuItem) {
            if (!menuItem || !menuItem.id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid menu item data'
                });
                return;
            }
            
            const form = document.querySelector('#editMenuModal form');
            document.getElementById('edit_menu_id').value = menuItem.id;
            document.getElementById('edit_menu_name').value = menuItem.name;
            document.getElementById('edit_menu_description').value = menuItem.description;
            document.getElementById('edit_menu_category').value = menuItem.category;
            document.getElementById('edit_menu_bestseller').checked = menuItem.is_bestseller == 1;
            document.getElementById('edit_menu_popular').checked = menuItem.is_popular == 1;
            updateSubcategories(document.getElementById('edit_menu_category'));
            document.getElementById('edit_menu_subcategory').value = menuItem.subcategory;
            
            showModal('editMenuModal');
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
                                <strong>Price:</strong><br>
                                RM${parseFloat(event.price).toFixed(2)}
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
                }
            }
        });

        // Add this new function for view toggling
        function toggleView() {
            const foodSection = document.getElementById('foodSection');
            const menuSection = document.getElementById('menuSection');
            const toggleText = document.getElementById('toggleText');

            if (foodSection.classList.contains('active')) {
                foodSection.classList.remove('active');
                menuSection.classList.add('active');
                toggleText.textContent = 'Switch to Food & Beverage View';
            } else {
                menuSection.classList.remove('active');
                foodSection.classList.add('active');
                toggleText.textContent = 'Switch to Menu View';
            }
        }

        const categorySubcategories = {
            appetizer: ['small-bites', 'breads', 'soup-salad', 'noodles'],
            main: ['rice', 'poultry', 'steak', 'fish-seafood'],
            beverage: ['juice', 'coffee-tea'],
            dessert: ['all']
        };

        function updateSubcategories(categorySelect) {
            const subcategorySelect = categorySelect.form.querySelector('[name="subcategory"]');
            const category = categorySelect.value;
            
            // Clear existing options
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
            
            if (category && categorySubcategories[category]) {
                categorySubcategories[category].forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub;
                    option.textContent = sub.split('-').map(word => 
                        word.charAt(0).toUpperCase() + word.slice(1)
                    ).join(' ');
                    subcategorySelect.appendChild(option);
                });
            }
        }

        // Add menu filtering functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const menuItems = document.querySelectorAll('.menu-card');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const category = btn.dataset.category;
                    
                    // Update active button
                    filterBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    // Filter menu items
                    menuItems.forEach(item => {
                        if (category === 'all' || item.dataset.category === category) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
        });

        // Add this to your existing JavaScript
        function handleCategoryFilter() {
            const category = this.dataset.category;
            const subFilters = document.querySelectorAll('.sub-filters');
            
            // Update active states
            document.querySelectorAll('.filter-btn').forEach(btn => 
                btn.classList.toggle('active', btn === this));
            
            // Show/hide relevant sub-filters
            subFilters.forEach(sf => sf.style.display = 'none');
            if (category !== 'all') {
                const targetSubFilter = document.getElementById(`${category}-filters`);
                if (targetSubFilter) targetSubFilter.style.display = 'flex';
            }
            
            // Filter menu items
            document.querySelectorAll('.menu-card').forEach(card => {
                card.style.display = 
                    category === 'all' || card.dataset.category === category 
                        ? 'block' 
                        : 'none';
            });
        }

        // Add this function after handleCategoryFilter
        function handleSubcategoryFilter() {
            const subcategory = this.dataset.subcategory;
            const category = document.querySelector('.filter-btn.active').dataset.category;
            
            // Update active states for sub-buttons
            const parentFilter = this.closest('.sub-filters');
            parentFilter.querySelectorAll('.sub-btn').forEach(btn => 
                btn.classList.toggle('active', btn === this));
            
            // Filter menu items
            document.querySelectorAll('.menu-card').forEach(card => {
                if (card.dataset.category === category) {
                    card.style.display = 
                        subcategory === 'all' || card.dataset.subcategory === subcategory 
                            ? 'block' 
                            : 'none';
                }
            });
        }

        // Update the DOMContentLoaded event listener
        document.addEventListener('DOMContentLoaded', function() {
            // Filter button click handlers
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const category = this.dataset.category;
                    
                    // Update active states
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show/hide sub-filters
                    document.querySelectorAll('.sub-filters').forEach(sf => sf.style.display = 'none');
                    if (category !== 'all') {
                        const targetSubFilter = document.getElementById(`${category}-filters`);
                        if (targetSubFilter) {
                            targetSubFilter.style.display = 'flex';
                            // Reset sub-filter active states
                            targetSubFilter.querySelectorAll('.sub-btn').forEach((sb, index) => {
                                sb.classList.toggle('active', index === 0);
                            });
                        }
                    }
                    
                    // Filter menu items
                    document.querySelectorAll('.menu-card').forEach(card => {
                        card.style.display = 
                            category === 'all' || card.dataset.category === category 
                                ? 'block' 
                                : 'none';
                    });
                });
            });

            // Sub-category button click handlers
            document.querySelectorAll('.sub-btn').forEach(btn => {
                btn.addEventListener('click', handleSubcategoryFilter);
            });
        });
    </script>
</body>
</html>
