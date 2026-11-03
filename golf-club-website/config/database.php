<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'golf';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get action from POST if it exists
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    // ...existing food/beverage cases...

    case 'create_menu':
        $name = $_POST['name'];
        $description = $_POST['description'];
        $category = $_POST['category']; // appetizer, main, beverage, dessert
        $subcategory = $_POST['subcategory']; // Specific to each category
        $image_path = null;

        // Validate category and subcategory combinations
        $valid_categories = ['appetizer', 'main', 'beverage', 'dessert'];
        $valid_subcategories = [
            'appetizer' => ['small-bites', 'breads', 'soup-salad', 'noodles'],
            'main' => ['rice', 'poultry', 'steak', 'fish-seafood'],
            'beverage' => ['juice', 'coffee-tea'],
            'dessert' => ['all'] // Dessert doesn't have subcategories
        ];

        if (!in_array($category, $valid_categories) || 
            !in_array($subcategory, $valid_subcategories[$category])) {
            die("Invalid category or subcategory");
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/menu/" . $category . "/";
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            $image_path = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
        }

        $sql = "INSERT INTO tbl_menu (name, description, category, subcategory, image_path) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $description, $category, $subcategory, $image_path);
        $stmt->execute();
        break;

    case 'update_menu':
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        $subcategory = $_POST['subcategory'];

        // Validate category and subcategory combinations
        $valid_categories = ['appetizer', 'main', 'beverage', 'dessert'];
        $valid_subcategories = [
            'appetizer' => ['small-bites', 'breads', 'soup-salad', 'noodles'],
            'main' => ['rice', 'poultry', 'steak', 'fish-seafood'],
            'beverage' => ['juice', 'coffee-tea'],
            'dessert' => ['all']
        ];

        if (!in_array($category, $valid_categories) || 
            !in_array($subcategory, $valid_subcategories[$category])) {
            die("Invalid category or subcategory");
        }

        // Get old image information
        $stmt = $conn->prepare("SELECT image_path, category FROM tbl_menu WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $old_data = $result->fetch_assoc();

        $image_path = $old_data['image_path'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/menu/" . $category . "/";
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            $image_path = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
            
            // Delete old image if category changed or new image uploaded
            if ($old_data['image_path'] && file_exists($old_data['image_path']) &&
                ($category !== $old_data['category'] || $image_path !== $old_data['image_path'])) {
                unlink($old_data['image_path']);
            }
        }

        $sql = "UPDATE tbl_menu SET name=?, description=?, category=?, subcategory=?, image_path=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $description, $category, $subcategory, $image_path, $id);
        $stmt->execute();
        break;

    case 'delete_menu':
        $id = $_POST['id'];
        // Get image path before deleting
        $stmt = $conn->prepare("SELECT image_path FROM tbl_menu WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $menu = $result->fetch_assoc();
        
        // Delete physical image file if exists
        if ($menu['image_path'] && file_exists($menu['image_path'])) {
            unlink($menu['image_path']);
        }
        
        // Delete database record
        $sql = "DELETE FROM tbl_menu WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        break;

    default:
        // No action or invalid action
        break;
}
