CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    length INT NOT NULL,
    par INT NOT NULL,
    handicap INT NOT NULL,
    view_image VARCHAR(255) NOT NULL,
    field_image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
