CREATE TABLE IF NOT EXISTS `tbl_food_images` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `image_path` varchar(255) NOT NULL,
    `category` enum('food', 'beverage') NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
