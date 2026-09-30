-- =====================================================
-- DATABASE POS RETAIL
-- =====================================================

CREATE DATABASE IF NOT EXISTS db_pos;

USE db_pos;


-- =====================================================
-- HAPUS TABEL LAMA
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS detail_penjualan;
DROP TABLE IF EXISTS penjualan;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;


-- =====================================================
-- TABLE USERS
-- =====================================================

CREATE TABLE users (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nama VARCHAR(100) NOT NULL,

    username VARCHAR(50) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    role ENUM('admin','kasir')
        NOT NULL DEFAULT 'kasir',

    status ENUM('aktif','nonaktif')
        NOT NULL DEFAULT 'aktif',

    created_at TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- =====================================================
-- TABLE KATEGORI
-- =====================================================

CREATE TABLE kategori (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nama_kategori VARCHAR(100) NOT NULL,

    created_at TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- =====================================================
-- TABLE PRODUK
-- =====================================================

CREATE TABLE produk (

    id INT AUTO_INCREMENT PRIMARY KEY,

    barcode VARCHAR(50) NOT NULL UNIQUE,

    kode_produk VARCHAR(50) NOT NULL UNIQUE,

    nama_produk VARCHAR(150) NOT NULL,

    kategori_id INT NOT NULL,

    harga_beli DECIMAL(15,2)
        NOT NULL DEFAULT 0,

    harga_jual DECIMAL(15,2)
        NOT NULL DEFAULT 0,

    stok INT NOT NULL DEFAULT 0,

    stok_minimal INT NOT NULL DEFAULT 5,

    satuan VARCHAR(30)
        NOT NULL DEFAULT 'pcs',

    status ENUM('aktif','nonaktif')
        NOT NULL DEFAULT 'aktif',

    created_at TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_produk_kategori

        FOREIGN KEY (kategori_id)

        REFERENCES kategori(id)

        ON DELETE RESTRICT

        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- =====================================================
-- TABLE PENJUALAN
-- =====================================================

CREATE TABLE penjualan (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nomor_transaksi VARCHAR(50)
        NOT NULL UNIQUE,

    user_id INT NOT NULL,

    tanggal DATETIME
        DEFAULT CURRENT_TIMESTAMP,

    subtotal DECIMAL(15,2)
        NOT NULL DEFAULT 0,

    diskon DECIMAL(15,2)
        NOT NULL DEFAULT 0,

    total DECIMAL(15,2)
        NOT NULL DEFAULT 0,

    bayar DECIMAL(15,2)
        NOT NULL DEFAULT 0,

    kembalian DECIMAL(15,2)
        NOT NULL DEFAULT 0,

    metode_pembayaran
        ENUM('cash','qris','debit','transfer')
        NOT NULL DEFAULT 'cash',

    status
        ENUM('selesai','batal')
        NOT NULL DEFAULT 'selesai',

    CONSTRAINT fk_penjualan_user

        FOREIGN KEY (user_id)

        REFERENCES users(id)

        ON DELETE RESTRICT

        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- =====================================================
-- TABLE DETAIL PENJUALAN
-- =====================================================

CREATE TABLE detail_penjualan (

    id INT AUTO_INCREMENT PRIMARY KEY,

    penjualan_id INT NOT NULL,

    produk_id INT NOT NULL,

    barcode VARCHAR(50) NOT NULL,

    nama_produk VARCHAR(150) NOT NULL,

    harga DECIMAL(15,2)
        NOT NULL DEFAULT 0,

    jumlah INT NOT NULL DEFAULT 1,

    diskon DECIMAL(15,2)
        NOT NULL DEFAULT 0,

    subtotal DECIMAL(15,2)
        NOT NULL DEFAULT 0,

    CONSTRAINT fk_detail_penjualan

        FOREIGN KEY (penjualan_id)

        REFERENCES penjualan(id)

        ON DELETE CASCADE

        ON UPDATE CASCADE,

    CONSTRAINT fk_detail_produk

        FOREIGN KEY (produk_id)

        REFERENCES produk(id)

        ON DELETE RESTRICT

        ON UPDATE CASCADE

) ENGINE=InnoDB;


-- =====================================================
-- DATA USER
-- =====================================================

INSERT INTO users
(
    nama,
    username,
    password,
    role,
    status
)
VALUES

(
    'Administrator',
    'admin',
    MD5('admin123'),
    'admin',
    'aktif'
),

(
    'Kasir Utama',
    'kasir',
    MD5('kasir123'),
    'kasir',
    'aktif'
);


-- =====================================================
-- DATA KATEGORI
-- =====================================================

INSERT INTO kategori
(
    nama_kategori
)
VALUES

('Makanan'),

('Minuman'),

('Alat Tulis'),

('Elektronik'),

('Sembako'),

('Perawatan'),

('Kebutuhan Rumah'),

('Lain-lain');


-- =====================================================
-- DATA PRODUK
-- =====================================================

INSERT INTO produk
(
    barcode,
    kode_produk,
    nama_produk,
    kategori_id,
    harga_beli,
    harga_jual,
    stok,
    stok_minimal,
    satuan,
    status
)
VALUES

(
    '899100210001',
    'PRD001',
    'Indomie Goreng',
    1,
    2500,
    3500,
    50,
    10,
    'pcs',
    'aktif'
),

(
    '899100210002',
    'PRD002',
    'Indomie Kuah',
    1,
    2500,
    3500,
    50,
    10,
    'pcs',
    'aktif'
),

(
    '899100210003',
    'PRD003',
    'Teh Botol Sosro',
    2,
    3000,
    5000,
    30,
    10,
    'botol',
    'aktif'
),

(
    '899100210004',
    'PRD004',
    'Air Mineral',
    2,
    2000,
    4000,
    50,
    10,
    'botol',
    'aktif'
),

(
    '899100210005',
    'PRD005',
    'Pulpen',
    3,
    2000,
    3500,
    25,
    5,
    'pcs',
    'aktif'
),

(
    '899100210006',
    'PRD006',
    'Buku Tulis',
    3,
    4000,
    6000,
    30,
    5,
    'pcs',
    'aktif'
),

(
    '899100210007',
    'PRD007',
    'Mouse Wireless',
    4,
    50000,
    75000,
    10,
    2,
    'pcs',
    'aktif'
),

(
    '899100210008',
    'PRD008',
    'Keyboard',
    4,
    75000,
    100000,
    10,
    2,
    'pcs',
    'aktif'
),

(
    '899100210009',
    'PRD009',
    'Beras 5 Kg',
    5,
    65000,
    75000,
    20,
    5,
    'pcs',
    'aktif'
),

(
    '899100210010',
    'PRD010',
    'Gula 1 Kg',
    5,
    14000,
    17000,
    30,
    5,
    'pcs',
    'aktif'
);