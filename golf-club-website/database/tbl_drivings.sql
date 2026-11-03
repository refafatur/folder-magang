CREATE TABLE tbl_drivings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO tbl_drivings (name, image) VALUES
('Main Driving Range', '/uploads/drivings/driving1.jpg'),
('Practice Range', '/uploads/drivings/driving2.jpg'),
('Night Driving Range', '/uploads/drivings/driving3.jpg');
