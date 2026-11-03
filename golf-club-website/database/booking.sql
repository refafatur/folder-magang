CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_code VARCHAR(10) UNIQUE,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    booking_date DATE,
    booking_time TEXT,
    players INT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

