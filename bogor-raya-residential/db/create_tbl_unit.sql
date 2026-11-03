CREATE TABLE IF NOT EXISTS tbl_unit (
    id int(11) NOT NULL AUTO_INCREMENT,
    cluster varchar(255) NOT NULL,
    name varchar(255) NOT NULL,
    product_id int(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (product_id) REFERENCES tbl_produk(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
