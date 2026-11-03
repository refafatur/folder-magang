-- Tabel sumber informasi
CREATE TABLE IF NOT EXISTS tbl_sumber (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(50) NOT NULL,
    id_user INT,
    tgl_create DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES tbl_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabel calon pembeli
CREATE TABLE IF NOT EXISTS tbl_sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT,
    id_sumber INT,
    nama VARCHAR(100) NOT NULL,
    pekerjaan VARCHAR(100),
    no_hp VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    tgl_create DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES tbl_users(id) ON DELETE SET NULL,
    FOREIGN KEY (id_sumber) REFERENCES tbl_sumber(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabel tindak lanjut
CREATE TABLE IF NOT EXISTS tbl_follow_up (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_sales INT,
    id_user INT,
    id_unit INT NULL,
    media VARCHAR(100) NOT NULL,
    hasil VARCHAR(100),
    tgl_follow_up DATE NOT NULL,
    tgl_create DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sales) REFERENCES tbl_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES tbl_users(id) ON DELETE SET NULL,
    FOREIGN KEY (id_unit) REFERENCES tbl_unit(id) ON DELETE SET NULL
) ENGINE=InnoDB;
