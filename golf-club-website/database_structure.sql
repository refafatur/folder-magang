CREATE TABLE `tbl_wedding` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `foto` VARCHAR(255) NOT NULL,
    `type` ENUM('wedding', 'pre-wedding') NOT NULL DEFAULT 'wedding',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
