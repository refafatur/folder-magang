CREATE TABLE tbl_unit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deskripsi TEXT,
    harga VARCHAR(255),
    lantai INT,
    tampilan VARCHAR(255),
    file VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE tbl_produk DROP COLUMN deskripsi;
ALTER TABLE tbl_produk DROP COLUMN harga;
ALTER TABLE tbl_produk DROP COLUMN lantai;
ALTER TABLE tbl_produk DROP COLUMN tampilan;
ALTER TABLE tbl_produk DROP COLUMN file;
ALTER TABLE tbl_produk ADD COLUMN unit_id INT;
ALTER TABLE tbl_produk ADD FOREIGN KEY (unit_id) REFERENCES tbl_unit(id);
