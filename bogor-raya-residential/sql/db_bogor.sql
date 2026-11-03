-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Bulan Mei 2025 pada 10.57
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_bogor`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_contacts`
--

CREATE TABLE `tbl_contacts` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telp` varchar(100) NOT NULL,
  `cluster` varchar(100) NOT NULL,
  `pesan` longtext NOT NULL,
  `tgl_kunjungan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_contacts`
--

INSERT INTO `tbl_contacts` (`id`, `nama`, `email`, `no_telp`, `cluster`, `pesan`, `tgl_kunjungan`) VALUES
(11, 'Egi Apriliansyah', 'egiapriliansyah5@gmail.com', '081388603049', 'Terracotta Insert', '', '2025-03-07'),
(12, 'Cikal', 'cikal@gmail.com', '0813216546', 'Terracotta Insert', '', '2025-04-05'),
(13, 'Ziqi Raehan', 'ziqiraehan@gmail.com', '0813216546', 'Kemang Permata Neo', '', '2025-04-04'),
(14, 'Muhammad Refa Fatur Rahman', 'refafatur97@gmail.com', '0813216546', 'Terracotta Insert', '', '2025-03-30'),
(15, 'Apoy', 'refafatur97@gmail.com', '0813216546', 'Terracotta 3', '', '2025-04-04'),
(17, 'Muhammad Refa Fatur Rahman', 'cikal@gmail.com', '0813216546', 'Kemang Permata Premiere', '', '2025-03-29'),
(18, 'Muhammad Refa Fatur Rahman', 'refafatur97@gmail.com', '0813216546', 'Kemang Permata Neo', '', '2025-03-28');

--
-- Trigger `tbl_contacts`
--
DELIMITER $$
CREATE TRIGGER `after_order_insert` AFTER INSERT ON `tbl_contacts` FOR EACH ROW BEGIN
  INSERT INTO `tbl_notifications` (`message`, `timestamp`, `status`)
  VALUES ('New Order Received', NOW(), 'unread');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_follow_up`
--

CREATE TABLE `tbl_follow_up` (
  `id` int(11) NOT NULL,
  `id_sales` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_unit` int(11) DEFAULT NULL,
  `media` varchar(100) NOT NULL,
  `hasil` varchar(100) DEFAULT NULL,
  `tgl_follow_up` date NOT NULL,
  `tgl_create` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_follow_up`
--

INSERT INTO `tbl_follow_up` (`id`, `id_sales`, `id_user`, `id_unit`, `media`, `hasil`, `tgl_follow_up`, `tgl_create`) VALUES
(37, 13, 58, 12, 'Phone', 'Tidak tertarik', '2025-04-22', '2025-04-29 09:50:45'),
(39, 13, 58, 27, 'Phone', 'Pikir-pikir', '2025-04-16', '2025-04-29 09:55:30'),
(47, 19, 58, 12, 'Phone', 'Pikir-pikir', '2025-04-22', '2025-04-29 13:51:10'),
(48, 20, 58, 25, 'WhatsApp', 'Tertarik', '2025-05-05', '2025-05-05 10:29:43'),
(49, 21, 58, 10, 'WhatsApp', 'Deal', '2025-05-05', '2025-05-05 14:41:07'),
(50, 21, 58, 20, 'WhatsApp', 'Pikir-pikir', '2025-05-05', '2025-05-05 14:42:00'),
(51, 21, 58, 21, 'WhatsApp', 'Pikir-pikir', '2025-05-05', '2025-05-05 14:42:34'),
(52, 21, 58, 10, 'WhatsApp', 'Pikir-pikir', '2025-05-05', '2025-05-05 14:42:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_management`
--

CREATE TABLE `tbl_management` (
  `id` int(11) NOT NULL,
  `role` varchar(100) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_management`
--

INSERT INTO `tbl_management` (`id`, `role`, `created_at`, `permissions`) VALUES
(0, 'Admin', '2025-03-05', '{\"contacts\":{\"view\":1,\"add\":1,\"edit\":1,\"delete\":1},\"news\":{\"view\":1,\"add\":1,\"edit\":1,\"delete\":1},\"products\":{\"view\":1,\"add\":1,\"edit\":1,\"delete\":1},\"testimonials\":{\"view\":1,\"add\":1,\"edit\":1,\"delete\":1},\"users\":{\"view\":1,\"add\":1,\"edit\":1,\"delete\":1},\"sales\":{\"view\":1,\"add\":1,\"edit\":1,\"delete\":1}}'),
(1, 'User', '2025-03-05', '{\"contacts\":{\"view\":1,\"add\":1,\"edit\":1,\"delete\":1},\"news\":{\"view\":1,\"add\":1,\"edit\":1,\"delete\":1},\"products\":{\"view\":1,\"add\":1,\"edit\":1,\"delete\":1},\"testimonials\":{\"view\":1,\"add\":1,\"edit\":1,\"delete\":1},\"users\":{\"view\":0,\"add\":0,\"edit\":0,\"delete\":0},\"sales\":{\"view\":0,\"add\":0,\"edit\":0,\"delete\":0}}'),
(2, 'Sales', '2025-03-05', '{\"contacts\":{\"view\":0,\"add\":0,\"edit\":0,\"delete\":0},\"news\":{\"view\":0,\"add\":0,\"edit\":0,\"delete\":0},\"products\":{\"view\":0,\"add\":0,\"edit\":0,\"delete\":0},\"testimonials\":{\"view\":1,\"add\":1,\"edit\":1,\"delete\":1},\"users\":{\"view\":0,\"add\":0,\"edit\":0,\"delete\":0},\"sales\":{\"view\":1,\"add\":1,\"edit\":1,\"delete\":1}}');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_news`
--

CREATE TABLE `tbl_news` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `deskripsi` longtext NOT NULL,
  `foto` varchar(100) NOT NULL,
  `expired_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('show','hide') DEFAULT 'show'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_news`
--

INSERT INTO `tbl_news` (`id`, `nama`, `lokasi`, `deskripsi`, `foto`, `expired_date`, `created_at`, `status`) VALUES
(3, '🏡 HUJAN EMAS DI BOGOR RAYA RESIDENTIAL! ✨', 'Terracotta 3', 'Miliki rumah impian di Kemang Permata, hanya 0 KM dari Pintu Tol Bogor Selatan! 🚗💨\r\n🎁 Dapatkan Angpao Logam Mulia 10gr\r\n💰 FREE PPN 100%\r\n💲 Harga mulai 2M-an\r\n🏡 Hunian eksklusif dengan desain modern, lingkungan nyaman, dan akses mudah ke berbagai fasilitas!', '../uploads/lokasi/Cuplikan layar 2025-02-24 143751.png', '2026-04-11', '2025-03-05 13:46:54', 'hide'),
(4, '🏡 HUJAN EMAS DI BOGOR RAYA RESIDENTIAL! ✨', 'Kemang', 'Miliki rumah impian di Kemang Permata, hanya 0 KM dari Pintu Tol Bogor Selatan! 🚗💨\r\n🎁 Dapatkan Angpao Logam Mulia 10gr\r\n💰 FREE PPN 100%\r\n💲 Harga mulai 2M-an\r\n🏡 Hunian eksklusif dengan desain modern, lingkungan nyaman, dan akses mudah ke berbagai fasilitas!', '../uploads/lokasi/Cuplikan layar 2025-02-28 132646.png', '2025-03-29', '2025-03-05 13:46:54', 'hide'),
(5, '🏡 HUJAN EMAS DI BOGOR RAYA RESIDENTIAL! ✨', 'Kemang Permata', 'Miliki rumah impian di Kemang Permata, hanya 0 KM dari Pintu Tol Bogor Selatan! 🚗💨\r\n🎁 Dapatkan Angpao Logam Mulia 10gr\r\n💰 FREE PPN 100%\r\n💲 Harga mulai 2M-an\r\n🏡 Hunian eksklusif dengan desain modern, lingkungan nyaman, dan akses mudah ke berbagai fasilitas!', '../uploads/lokasi/Cuplikan layar 2025-02-28 134022.png', '2025-03-31', '2025-03-05 13:46:54', 'hide'),
(6, '🏡 HUJAN EMAS DI BOGOR RAYA RESIDENTIAL! ✨', 'Kemang Permata', 'Miliki rumah impian di Kemang Permata, hanya 0 KM dari Pintu Tol Bogor Selatan! 🚗💨\r\n🎁 Dapatkan Angpao Logam Mulia 10gr\r\n💰 FREE PPN 100%\r\n💲 Harga mulai 2M-an\r\n🏡 Hunian eksklusif dengan desain modern, lingkungan nyaman, dan akses mudah ke berbagai fasilitas!', '../uploads/lokasi/Cuplikan layar 2025-02-28 134123.png', '2025-04-05', '2025-03-05 13:46:54', 'hide'),
(7, '🏡 HUJAN THR DI BOGOR RAYA RESIDENTIAL! ✨', 'Kemang', 'Miliki rumah impian di Kemang Permata NEO, hanya 0 KM dari Pintu Tol Bogor Selatan! 🚗💨\r\n🎁 Dapatkan Samsung Galaxy S25 & Voucher Belanja 10 Juta\r\n💰 FREE PPN 100%, Legalitas & dan IPL 12 Bulan\r\n💲 Harga mulai 2M-an\r\n🏡 Hunian eksklusif dengan desain modern, lingkungan nyaman, dan akses mudah ke berbagai fasilitas!', '../uploads/news/Cuplikan layar 2025-03-06 113949.png', '2026-03-06', '2025-03-06 11:45:53', 'show'),
(8, '🏡 HUJAN THR DI BOGOR RAYA RESIDENTIAL! ✨', 'Kemang Permata', 'Miliki rumah impian di Kemang Permata NEO, hanya 0 KM dari Pintu Tol Bogor Selatan! 🚗💨\r\n🎁 Dapatkan Samsung Galaxy S25 & Voucher Belanja 10 Juta\r\n💰 FREE PPN 100%, Legalitas & dan IPL 12 Bulan\r\n💲 Harga mulai 2M-an\r\n🏡 Hunian eksklusif dengan desain modern, lingkungan nyaman, dan akses mudah ke berbagai fasilitas!', '../uploads/news/Cuplikan layar 2025-03-06 114814.png', '2026-03-06', '2025-03-06 11:49:26', 'show'),
(9, '🏡 HUJAN THR DI BOGOR RAYA RESIDENTIAL! ✨', 'Avani Bogor Raya', 'Miliki rumah impian di Avani Bogor Raya, hanya 0 KM dari Pintu Tol Bogor Selatan! 🚗💨\r\n🎁 Dapatkan Samsung Galaxy S25 & Voucher Belanja 10 Juta\r\n💰 FREE PPN 100%, Legalitas & dan IPL 12 Bulan\r\n💲 Harga mulai 2M-an\r\n🏡 Hunian eksklusif dengan desain modern, lingkungan nyaman, dan akses mudah ke berbagai fasilitas!', '../uploads/news/Cuplikan layar 2025-03-06 115019.png', '2026-03-06', '2025-03-06 11:50:30', 'show'),
(10, '🏡 HUJAN THR DI BOGOR RAYA RESIDENTIAL! ✨', 'Terracotta', 'Miliki rumah impian di Terracotta, hanya 1KM dari Pintu Tol Bogor Selatan! 🚗💨\r\n🎁 Dapatkan Voucher UMROH & Belanja 7,5 Jt\r\n💰 FREE PPN 100%, Membership Sport Club , Free IPL dan Booking FEE 2,5 Jt All-In\r\n💲 Harga mulai 2M-an\r\n🏡 Hunian eksklusif dengan desain modern, lingkungan nyaman, dan akses mudah ke berbagai fasilitas!', '../uploads/news/Cuplikan layar 2025-03-06 115122.png', '2026-03-06', '2025-03-06 11:55:59', 'show'),
(11, '🏡 HUJAN THR DI BOGOR RAYA RESIDENTIAL! ✨', 'Terracotta', 'Miliki rumah impian di Terracotta, hanya 1KM dari Pintu Tol Bogor Selatan! 🚗💨\r\n🎁 Dapatkan Voucher UMROH & Belanja 7,5 Jt\r\n💰 FREE PPN 100%, Membership Sport Club , Free IPL dan Booking FEE 2,5 Jt All-In\r\n💲 Harga mulai 2M-an\r\n🏡 Hunian eksklusif dengan desain modern, lingkungan nyaman, dan akses mudah ke berbagai fasilitas!', '../uploads/news/Cuplikan layar 2025-03-06 115707.png', '2026-03-06', '2025-03-06 11:57:23', 'show');

--
-- Trigger `tbl_news`
--
DELIMITER $$
CREATE TRIGGER `after_news_insert` AFTER INSERT ON `tbl_news` FOR EACH ROW BEGIN
  INSERT INTO `tbl_notifications` (`message`, `timestamp`, `status`)
  VALUES ('New News Posted', NOW(), 'unread');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_notifications`
--

CREATE TABLE `tbl_notifications` (
  `id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('unread','read') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_notifications`
--

INSERT INTO `tbl_notifications` (`id`, `message`, `timestamp`, `status`) VALUES
(1, 'New User Registration', '2025-03-06 03:55:09', 'unread'),
(2, 'New News Posted', '2025-03-06 04:45:53', 'unread'),
(3, 'New News Posted', '2025-03-06 04:49:26', 'unread'),
(4, 'New News Posted', '2025-03-06 04:50:30', 'unread'),
(5, 'New News Posted', '2025-03-06 04:55:59', 'unread'),
(6, 'New News Posted', '2025-03-06 04:57:23', 'unread'),
(7, 'New Order Received', '2025-03-06 07:40:03', 'unread'),
(8, 'New Order Received', '2025-03-06 07:55:32', 'unread'),
(9, 'New User Registration', '2025-03-07 03:13:12', 'unread'),
(10, 'New User Registration', '2025-03-07 03:15:59', 'unread'),
(11, 'New User Registration', '2025-03-07 03:54:59', 'unread'),
(12, 'New Order Received', '2025-03-07 07:56:58', 'unread'),
(13, 'New User Registration', '2025-03-07 08:27:25', 'unread'),
(14, 'New Order Received', '2025-03-10 06:47:30', 'unread'),
(15, 'New News Posted', '2025-03-10 07:46:58', 'unread'),
(16, 'New Order Received', '2025-03-11 02:37:47', 'unread'),
(17, 'New Order Received', '2025-03-11 02:42:16', 'unread'),
(18, 'New Order Received', '2025-03-11 03:05:44', 'unread'),
(19, 'New Order Received', '2025-03-11 06:12:22', 'unread'),
(20, 'New Order Received', '2025-03-20 07:43:30', 'unread'),
(21, 'New News Posted', '2025-03-21 07:25:33', 'unread'),
(22, 'New Order Received', '2025-03-21 08:59:20', 'unread'),
(23, 'New User Registration', '2025-04-28 07:07:27', 'unread'),
(24, 'New User Registration', '2025-04-29 06:15:54', 'unread'),
(25, 'New User Registration', '2025-04-30 04:52:04', 'unread'),
(26, 'New User Registration', '2025-04-30 05:02:06', 'unread'),
(27, 'New User Registration', '2025-05-14 16:17:14', 'unread');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_permissions`
--

CREATE TABLE `tbl_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_produk`
--

CREATE TABLE `tbl_produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `deskripsi` varchar(100) DEFAULT NULL,
  `harga` varchar(100) DEFAULT NULL,
  `lantai` varchar(100) DEFAULT NULL,
  `tampilan` varchar(100) DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_produk`
--

INSERT INTO `tbl_produk` (`id`, `nama`, `deskripsi`, `harga`, `lantai`, `tampilan`, `foto`, `status`, `file`) VALUES
(17, 'Kemang Permata Neo', 'Modern design', '1.200.000.000', '2', 'City View', '../uploads/produk/Cuplikan layar 2025-03-20 091900.png', 1, '../uploads/produk/files/KemangPermata.pdf'),
(25, 'Kemang Permata Premiere', 'Modern Design', '1.200.000.000', '2', 'City View', '../uploads/produk/Cuplikan layar 2025-03-20 092352.png', 1, '../uploads/produk/files/KemangPermata.pdf'),
(26, 'Terracotta 3', '-', '1.000.000.000', '2', 'City View', '../uploads/produk/Cuplikan layar 2025-03-20 113403.png', 1, '../uploads/produk/files/Terracotta.pdf'),
(27, 'Terracotta Insert', '-', '1.000.000.000', '2', 'City View', '../uploads/produk/tera.png', 1, '../uploads/produk/files/Terracotta.pdf');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_sales`
--

CREATE TABLE `tbl_sales` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_sumber` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `no_hp` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tgl_create` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_sales`
--

INSERT INTO `tbl_sales` (`id`, `id_user`, `id_sumber`, `nama`, `pekerjaan`, `no_hp`, `email`, `tgl_create`) VALUES
(13, 58, 1, 'tes2', 'usaha', '081317490001', 'refafatur97@gmail.com', '2025-04-29 09:50:36'),
(19, 58, 4, 'Muhammad Refa', 'nongkrong', '081317490001', 'refafatur97@gmail.com', '2025-04-29 13:39:33'),
(20, 59, 4, 'Leon Leonheart', 'King', '6666666666', 'kingleon123@gmail.com', '2025-04-30 12:07:17'),
(21, 58, 2, 'Muhammad Refa Fatur Rahman', 'sssddsds', '081317490001', 'egi@gmail.com', '2025-05-05 14:40:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_sumber`
--

CREATE TABLE `tbl_sumber` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `tgl_create` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_sumber`
--

INSERT INTO `tbl_sumber` (`id`, `nama`, `id_user`, `tgl_create`) VALUES
(1, 'Instagram', 58, '2025-04-24 10:46:16'),
(2, 'Berita', 58, '2025-04-24 10:46:31'),
(3, 'Linked In', 58, '2025-04-29 09:56:33'),
(4, 'Facebook', 58, '2025-04-29 09:58:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_testimoni`
--

CREATE TABLE `tbl_testimoni` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `pesan` longtext NOT NULL,
  `foto` varchar(100) NOT NULL,
  `rate` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_testimoni`
--

INSERT INTO `tbl_testimoni` (`id`, `nama`, `pesan`, `foto`, `rate`) VALUES
(2, 'Ziqi Raehan', 'Waawww bagus sekali tempat ini', '../uploads/testimoni/BIRD EYE VIEW_TERRACOTTA III - 03_R1.jpg', 5),
(3, 'Apoyy', 'Sangat Dekat sekali dengan TOL', '../uploads/testimoni/Jobcom.jpg', 5),
(4, 'Egi', 'Dekat sekali dengan Lapangan Golf', '../uploads/testimoni/boy.png', 5),
(5, 'Refa', 'Bagus sekali pemandangannya!!', '../uploads/testimoni/Gambar1.jpg', 5),
(6, 'Muhammad Refa Fatur Rahman', 'dbhevdevu', '../uploads/testimoni/Wp.png', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_unit`
--

CREATE TABLE `tbl_unit` (
  `id` int(11) NOT NULL,
  `cluster` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `harga` varchar(255) DEFAULT NULL,
  `foto` text DEFAULT NULL,
  `lantai` int(11) DEFAULT 1,
  `deskripsi` text DEFAULT NULL,
  `luas_tanah` decimal(10,2) DEFAULT NULL,
  `luas_bangunan` decimal(10,2) DEFAULT NULL,
  `stok` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_unit`
--

INSERT INTO `tbl_unit` (`id`, `cluster`, `name`, `product_id`, `created_at`, `harga`, `foto`, `lantai`, `deskripsi`, `luas_tanah`, `luas_bangunan`, `stok`) VALUES
(10, 'Kemang Permata Neo', 'KP Neo Tipe', 17, '2025-03-19 03:37:58', '1.000.000.000', '[\"../uploads/unit/67dd08d6cd33b_1742538966_0.png\",\"../uploads/unit/67dd08d6cef26_1742538966_1.png\"]', 2, '-', '150.00', '150.00', 20),
(12, 'Kemang Permata Neo', 'KP Neo Tipe', 17, '2025-03-19 07:58:17', '1.000.000.000', '[\"../uploads/unit/67dd08e7c528c_1742538983_1.png\",\"../uploads/unit/67dd08e7c436c_1742538983_0.png\"]', 2, '-', '132.00', '120.00', 20),
(19, 'Kemang Permata Premiere', 'KP Premier Tipe', 25, '2025-03-20 04:13:59', '1.000.000.000', '[\"../uploads/unit/67dd06dd10f34_1742538461_0.png\",\"../uploads/unit/67dd06dd116fe_1742538461_1.png\"]', 2, '-', '404.00', '294.00', 20),
(20, 'Kemang Permata Premiere', 'KP Premier Tipe', 25, '2025-03-20 04:22:15', '1.000.000.000', '[\"../uploads/unit/67dd092f502b5_1742539055_0.png\",\"../uploads/unit/67dd092f50b2c_1742539055_1.png\"]', 2, '-', '202.00', '200.00', 20),
(21, 'Kemang Permata Premiere', 'KP Premier Tipe', 25, '2025-03-20 04:23:16', '1.000.000.000', '[\"../uploads/unit/67dd0966d3c0c_1742539110_2.png\",\"../uploads/unit/67dd0966d2d13_1742539110_0.png\",\"../uploads/unit/67dd0966d355c_1742539110_1.png\"]', 2, '-', '132.00', '120.00', 20),
(22, 'Terracotta 3', 'Archilla ', 26, '2025-03-20 04:36:28', '1.000.000.000', '[\"../uploads/unit/67dd0a08595fe_1742539272_0.png\",\"../uploads/unit/67dd0a0859dfc_1742539272_1.png\"]', 2, 'Unit standard untuk cluster Terracotta 3. Dilengkapi dengan fasilitas standar dan layout optimal.', '200.00', '102.00', 20),
(23, 'Terracotta 3', 'Archilla ', 26, '2025-03-20 04:41:57', '1.000.000.000', '[\"../uploads/unit/67dd0a2f0949c_1742539311_0.png\",\"../uploads/unit/67dd0a2f09bc2_1742539311_1.png\"]', 2, 'Unit standard untuk cluster Terracotta 3. Dilengkapi dengan fasilitas standar dan layout optimal.', '200.00', '93.00', 20),
(24, 'Terracotta 3', 'Archilla ', 26, '2025-03-20 04:42:49', '1.000.000.000', '[\"../uploads/unit/67dd0a447e8de_1742539332_0.png\",\"../uploads/unit/67dd0a447f14d_1742539332_1.png\"]', 2, 'Unit standard untuk cluster Terracotta 3. Dilengkapi dengan fasilitas standar dan layout optimal.', '200.00', '87.00', 20),
(25, 'Terracotta 3', 'Klei', 26, '2025-03-20 04:43:44', '1.000.000.000', '[\"../uploads/unit/67dd0a57c4860_1742539351_0.png\"]', 1, 'Unit standard untuk cluster Terracotta 3. Dilengkapi dengan fasilitas standar dan layout optimal.', '200.00', '45.00', 20),
(26, 'Terracotta Insert', 'Argilla', 27, '2025-03-20 06:22:06', '1.000.000.000', '[\"../uploads/unit/67dd0aadd8734_1742539437_0.png\",\"../uploads/unit/67dd0aadd93de_1742539437_1.png\"]', 3, 'Unit standard untuk cluster Terracotta Insert. Dilengkapi dengan fasilitas standar dan layout optimal.', '120.00', '82.00', 20),
(27, 'Terracotta Insert', 'Argilla', 27, '2025-03-20 06:31:45', '1.000.000.000', '[\"../uploads/unit/67dd0abf71c84_1742539455_0.png\",\"../uploads/unit/67dd0abf727ee_1742539455_1.png\"]', 3, 'Unit standard untuk cluster Terracotta Insert. Dilengkapi dengan fasilitas standar dan layout optimal.', '100.00', '78.00', 20),
(28, 'Terracotta Insert', 'Argilla', 27, '2025-03-20 06:32:30', '1.000.000.000', '[\"../uploads/unit/67dd0ad2285ea_1742539474_0.png\",\"../uploads/unit/67dd0ad228c50_1742539474_1.png\"]', 3, 'Unit standard untuk cluster Terracotta Insert. Dilengkapi dengan fasilitas standar dan layout optimal.', '100.00', '72.00', 20),
(41, 'Kemang Permata Neo', 'tes1', 17, '2025-03-24 08:35:05', '123.456', '[\"../uploads/unit/67e119397b9a8_1742805305_0.jpg\",\"../uploads/unit/67e11990439f1_1742805392_0.PNG\",\"../uploads/unit/67e1199044571_1742805392_1.PNG\",\"../uploads/unit/67e1199045082_1742805392_2.PNG\",\"../uploads/unit/67e119cdaf498_1742805453_0.jpg\"]', 2, 'test', '100.00', '70.00', 10),
(59, 'Kemang Permata Premiere', 'test', 25, '2025-04-11 09:02:08', '123456789123', '[\"../uploads/unit/67f8da906feda_1744362128_0.jpg\",\"../uploads/unit/67f8da90718e6_1744362128_1.jpg\"]', 2, 'testtt', '100.00', '80.00', 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `jenis_kelamin` varchar(100) DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `tanggal_pembuatan` date DEFAULT NULL,
  `no_hp` varchar(100) DEFAULT NULL,
  `expired` date DEFAULT NULL,
  `level` varchar(10) DEFAULT NULL,
  `otp` int(11) NOT NULL,
  `otp_expiry` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `username`, `password`, `nama`, `email`, `jenis_kelamin`, `foto`, `tanggal_pembuatan`, `no_hp`, `expired`, `level`, `otp`, `otp_expiry`, `status`) VALUES
(58, 'admin', '$2y$10$p1W1.c00RlwGKp1fzIy0hOjoc2NOERUwD7sKdSxKStLOwliLdwmHa', 'Muhammad Refa Fatur Rahman', 'refafatur97@gmail.com', 'Laki-Laki', 'uploads/admin/67ca7b156eada_boy.png', '2025-03-05', '081317490001', '2029-12-10', '0', 0, 0, 1),
(59, 'sales', '$2y$10$7lBW3dKFfUgMWkRmJi26jOwqIFk.6M1kOWM/VTJCUeI3hGPIHUXJy', 'Cikal', 'cikal@gmail.com', 'Laki-Laki', 'uploads/sales/67c86fa03e940_boy.png', '2025-03-05', '081317490001', '2025-05-31', '2', 0, 0, 0);

--
-- Trigger `tbl_users`
--
DELIMITER $$
CREATE TRIGGER `after_user_insert` AFTER INSERT ON `tbl_users` FOR EACH ROW BEGIN
  INSERT INTO `tbl_notifications` (`message`, `timestamp`, `status`)
  VALUES ('New User Registration', NOW(), 'unread');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_user_notifications`
--

CREATE TABLE `tbl_user_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `icon` varchar(50) DEFAULT 'bell',
  `color` varchar(50) DEFAULT 'primary',
  `link` varchar(255) DEFAULT '#',
  `timestamp` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_user_notifications`
--

INSERT INTO `tbl_user_notifications` (`id`, `user_id`, `message`, `icon`, `color`, `link`, `timestamp`, `is_read`) VALUES
(2, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 09:41:11', 0),
(8, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 09:43:02', 0),
(14, 59, 'User Egi Apriliansyah has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 09:43:43', 0),
(20, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 09:44:35', 0),
(26, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-11 09:48:10', 0),
(32, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 09:48:35', 0),
(38, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 09:49:24', 0),
(44, 59, 'User Egi Apriliansyah has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 09:51:18', 0),
(50, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 09:54:54', 0),
(56, 59, 'Product updated: Kemang Permata Neo', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-11 09:59:24', 0),
(62, 59, 'News article updated: 🏡 HUJAN EMAS DI BOGOR RAYA RESIDENTIAL! ✨', 'pencil', 'info', 'dashboard/news.php', '2025-03-11 10:00:04', 0),
(68, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-11 10:02:38', 0),
(74, 59, 'New order received from: Apoy', 'shopping-cart', 'success', 'dashboard/contact.php', '2025-03-11 10:05:44', 0),
(80, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 10:06:21', 0),
(86, 59, 'New testimonial added from: Muhammad Refa Fatur Rahman', 'comment', 'success', 'dashboard/testimoni.php', '2025-03-11 10:10:06', 0),
(92, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 10:12:51', 0),
(98, 59, 'User Egi Apriliansyah has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 10:13:02', 0),
(104, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 10:14:57', 0),
(110, 59, 'Contact updated: Fatur Rahman', 'address-book', 'info', 'dashboard/contact.php', '2025-03-11 10:20:05', 0),
(115, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 11:53:48', 1),
(116, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 11:53:48', 0),
(121, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 11:53:57', 1),
(122, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 11:53:57', 0),
(127, 58, 'New order received from: hxhdheddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddbbbbcccccccccccccccccccccccccccccc', 'shopping-cart', 'success', 'dashboard/contact.php', '2025-03-11 13:12:22', 1),
(128, 59, 'New order received from: hxhdheddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddbbbbcccccccccccccccccccccccccccccc', 'shopping-cart', 'success', 'dashboard/contact.php', '2025-03-11 13:12:22', 0),
(133, 58, 'Contact deleted: hxhdhedddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', 'trash', 'danger', 'dashboard/contact.php', '2025-03-11 13:13:39', 1),
(134, 59, 'Contact deleted: hxhdhedddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd', 'trash', 'danger', 'dashboard/contact.php', '2025-03-11 13:13:39', 0),
(139, 58, 'Contact deleted: Fatur Rahman', 'trash', 'danger', 'dashboard/contact.php', '2025-03-11 13:16:10', 1),
(140, 59, 'Contact deleted: Fatur Rahman', 'trash', 'danger', 'dashboard/contact.php', '2025-03-11 13:16:10', 0),
(145, 58, 'Contact deleted: Egi Apriliansyah', 'trash', 'danger', 'dashboard/contact.php', '2025-03-11 13:16:15', 1),
(146, 59, 'Contact deleted: Egi Apriliansyah', 'trash', 'danger', 'dashboard/contact.php', '2025-03-11 13:16:15', 0),
(151, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:20:23', 1),
(152, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:20:23', 0),
(157, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:23:37', 1),
(158, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:23:37', 0),
(163, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:26:05', 1),
(164, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:26:05', 0),
(169, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:27:33', 1),
(170, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:27:33', 0),
(175, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:32:47', 1),
(176, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:32:47', 0),
(181, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:35:02', 1),
(182, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:35:02', 0),
(187, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:38:49', 1),
(188, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 13:38:49', 0),
(193, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 14:14:19', 1),
(194, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-11 14:14:19', 0),
(199, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-12 09:15:15', 1),
(200, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-12 09:15:15', 0),
(205, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-12 09:25:27', 1),
(206, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-12 09:25:27', 0),
(211, 58, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-12 09:36:07', 1),
(212, 59, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-12 09:36:07', 0),
(217, 58, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-12 09:36:49', 1),
(218, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-12 09:36:49', 0),
(223, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-12 09:36:59', 1),
(224, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-12 09:36:59', 0),
(229, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-13 09:25:00', 1),
(230, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-13 09:25:00', 0),
(235, 58, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-13 09:36:25', 1),
(236, 59, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-13 09:36:25', 0),
(241, 58, 'Product updated: Kemang Permata Neo', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-13 09:36:28', 1),
(242, 59, 'Product updated: Kemang Permata Neo', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-13 09:36:28', 0),
(247, 58, 'Product updated: Terracotta Insert', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-13 09:36:32', 1),
(248, 59, 'Product updated: Terracotta Insert', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-13 09:36:32', 0),
(253, 58, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-13 09:36:36', 1),
(254, 59, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-13 09:36:36', 0),
(259, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-13 09:51:55', 1),
(260, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-13 09:51:55', 0),
(265, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-13 09:52:30', 1),
(266, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-13 09:52:31', 0),
(271, 58, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-13 10:11:11', 1),
(272, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-13 10:11:11', 0),
(277, 58, 'Product updated: Muhammad Refa Fatur Rahman', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-13 10:11:46', 1),
(278, 59, 'Product updated: Muhammad Refa Fatur Rahman', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-13 10:11:46', 0),
(283, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-13 10:11:55', 1),
(284, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-13 10:11:55', 0),
(289, 58, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-13 10:27:25', 1),
(290, 59, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-13 10:27:25', 0),
(295, 58, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-03-13 10:27:59', 1),
(296, 59, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-03-13 10:27:59', 0),
(301, 58, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-03-13 10:28:16', 1),
(302, 59, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-03-13 10:28:16', 0),
(307, 58, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-03-13 10:28:29', 1),
(308, 59, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-03-13 10:28:29', 0),
(313, 58, 'Product deleted: Cikal', 'trash', 'danger', 'dashboard/produk.php', '2025-03-13 10:28:35', 1),
(314, 59, 'Product deleted: Cikal', 'trash', 'danger', 'dashboard/produk.php', '2025-03-13 10:28:35', 0),
(319, 58, 'Product deleted: Cikal', 'trash', 'danger', 'dashboard/produk.php', '2025-03-13 10:28:41', 1),
(320, 59, 'Product deleted: Cikal', 'trash', 'danger', 'dashboard/produk.php', '2025-03-13 10:28:41', 0),
(325, 58, 'Product deleted: Cikal', 'trash', 'danger', 'dashboard/produk.php', '2025-03-13 10:28:46', 1),
(326, 59, 'Product deleted: Cikal', 'trash', 'danger', 'dashboard/produk.php', '2025-03-13 10:28:46', 0),
(331, 58, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-13 10:38:27', 1),
(332, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-13 10:38:27', 0),
(337, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-13 10:39:22', 1),
(338, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-13 10:39:22', 0),
(343, 58, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-13 10:40:42', 1),
(344, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-13 10:40:42', 0),
(349, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-13 10:40:54', 1),
(350, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-13 10:40:54', 0),
(355, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-13 11:12:17', 1),
(356, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-13 11:12:17', 0),
(361, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 09:33:44', 1),
(362, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 09:33:44', 0),
(367, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 09:43:06', 1),
(368, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 09:43:06', 0),
(373, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 09:43:41', 1),
(374, 59, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 09:43:41', 0),
(379, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 09:52:37', 1),
(380, 59, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 09:52:37', 0),
(385, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 09:53:02', 1),
(391, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 09:54:38', 1),
(397, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 09:54:45', 1),
(403, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 09:55:10', 1),
(409, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:00:16', 1),
(415, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 10:00:25', 1),
(421, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:02:41', 1),
(427, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:15:44', 1),
(433, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:15:55', 1),
(439, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:16:54', 1),
(445, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:19:18', 1),
(451, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:19:27', 1),
(457, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:21:03', 1),
(463, 58, 'User updated: Egi Apriliansyah', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:21:11', 1),
(469, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:32:52', 1),
(475, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 10:37:32', 1),
(481, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:37:42', 1),
(487, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 10:37:48', 1),
(493, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:38:08', 1),
(499, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 10:38:29', 1),
(505, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:38:53', 1),
(511, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 10:39:12', 1),
(517, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 10:39:19', 1),
(523, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 10:39:28', 1),
(529, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-17 11:49:11', 1),
(535, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 13:24:53', 1),
(541, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 13:32:48', 1),
(547, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 13:51:27', 1),
(553, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 14:06:37', 1),
(559, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 14:07:59', 1),
(565, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 14:12:30', 1),
(566, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 14:12:30', 0),
(571, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 14:14:06', 1),
(572, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 14:14:06', 0),
(577, 58, 'User Anonymous has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 14:19:33', 1),
(578, 59, 'User Anonymous has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 14:19:33', 0),
(583, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 15:18:36', 1),
(584, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-17 15:18:36', 0),
(589, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-18 09:18:51', 1),
(590, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-18 09:18:51', 0),
(595, 58, 'Product deleted: Terracotta 3', 'trash', 'danger', 'dashboard/produk.php', '2025-03-18 11:30:52', 1),
(596, 59, 'Product deleted: Terracotta 3', 'trash', 'danger', 'dashboard/produk.php', '2025-03-18 11:30:52', 0),
(601, 58, 'Product deleted: Kemang Permata Neo', 'trash', 'danger', 'dashboard/produk.php', '2025-03-18 11:31:41', 1),
(602, 59, 'Product deleted: Kemang Permata Neo', 'trash', 'danger', 'dashboard/produk.php', '2025-03-18 11:31:41', 0),
(607, 58, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-18 11:57:43', 1),
(608, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-18 11:57:43', 0),
(613, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-18 11:57:50', 1),
(614, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-18 11:57:50', 0),
(619, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-18 11:57:55', 1),
(620, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-18 11:57:55', 0),
(625, 58, 'Product deleted: ', 'trash', 'danger', 'dashboard/produk.php', '2025-03-18 11:57:57', 1),
(626, 59, 'Product deleted: ', 'trash', 'danger', 'dashboard/produk.php', '2025-03-18 11:57:57', 0),
(631, 58, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-18 11:58:10', 1),
(632, 59, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-18 11:58:10', 0),
(637, 58, 'New product added: Ziqi Raehan', 'building', 'warning', 'dashboard/produk.php', '2025-03-18 13:26:28', 1),
(638, 59, 'New product added: Ziqi Raehan', 'building', 'warning', 'dashboard/produk.php', '2025-03-18 13:26:28', 0),
(643, 58, 'Product deleted: Ziqi Raehan', 'trash', 'danger', 'dashboard/produk.php', '2025-03-18 15:01:36', 1),
(644, 59, 'Product deleted: Ziqi Raehan', 'trash', 'danger', 'dashboard/produk.php', '2025-03-18 15:01:36', 0),
(649, 58, 'New product added: 🏡 HUJAN EMAS DI BOGOR RAYA RESIDENTIAL! ✨', 'building', 'warning', 'dashboard/produk.php', '2025-03-18 15:08:35', 1),
(650, 59, 'New product added: 🏡 HUJAN EMAS DI BOGOR RAYA RESIDENTIAL! ✨', 'building', 'warning', 'dashboard/produk.php', '2025-03-18 15:08:35', 0),
(655, 58, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-19 09:20:06', 1),
(656, 59, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-19 09:20:06', 0),
(661, 58, 'Product deleted: Terracotta 3', 'trash', 'danger', 'dashboard/produk.php', '2025-03-19 09:20:37', 1),
(662, 59, 'Product deleted: Terracotta 3', 'trash', 'danger', 'dashboard/produk.php', '2025-03-19 09:20:37', 0),
(667, 58, 'Product deleted: Kemang Permata Neo', 'trash', 'danger', 'dashboard/produk.php', '2025-03-19 09:20:40', 1),
(668, 59, 'Product deleted: Kemang Permata Neo', 'trash', 'danger', 'dashboard/produk.php', '2025-03-19 09:20:40', 0),
(673, 58, 'Product deleted: Terracotta Insert', 'trash', 'danger', 'dashboard/produk.php', '2025-03-19 09:20:43', 1),
(674, 59, 'Product deleted: Terracotta Insert', 'trash', 'danger', 'dashboard/produk.php', '2025-03-19 09:20:43', 0),
(679, 58, 'Product deleted: Kemang Permata Premiere', 'trash', 'danger', 'dashboard/produk.php', '2025-03-19 09:20:46', 1),
(680, 59, 'Product deleted: Kemang Permata Premiere', 'trash', 'danger', 'dashboard/produk.php', '2025-03-19 09:20:46', 0),
(685, 58, 'New product added: Terracotta 3', 'building', 'warning', 'dashboard/produk.php', '2025-03-19 09:21:24', 1),
(686, 59, 'New product added: Terracotta 3', 'building', 'warning', 'dashboard/produk.php', '2025-03-19 09:21:24', 0),
(691, 58, 'Product deleted: 🏡 HUJAN EMAS DI BOGOR RAYA RESIDENTIAL! ✨', 'trash', 'danger', 'dashboard/produk.php', '2025-03-19 09:21:34', 1),
(692, 59, 'Product deleted: 🏡 HUJAN EMAS DI BOGOR RAYA RESIDENTIAL! ✨', 'trash', 'danger', 'dashboard/produk.php', '2025-03-19 09:21:34', 0),
(697, 58, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-19 09:21:48', 1),
(698, 59, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-19 09:21:48', 0),
(703, 58, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-19 09:22:03', 1),
(704, 59, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-19 09:22:03', 0),
(709, 58, 'New product added: Kemang Permata Neo', 'building', 'warning', 'dashboard/produk.php', '2025-03-19 09:28:58', 1),
(710, 59, 'New product added: Kemang Permata Neo', 'building', 'warning', 'dashboard/produk.php', '2025-03-19 09:28:58', 0),
(715, 58, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-19 10:23:48', 1),
(716, 59, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-19 10:23:48', 0),
(721, 58, 'Product updated: Kemang Permata Neo', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-19 10:23:58', 1),
(722, 59, 'Product updated: Kemang Permata Neo', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-19 10:23:58', 0),
(727, 58, 'Product updated: Kemang Permata Neo', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 09:19:24', 1),
(728, 59, 'Product updated: Kemang Permata Neo', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 09:19:24', 0),
(733, 58, 'New product added: Kemang Permata Premiere', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 09:24:11', 1),
(734, 59, 'New product added: Kemang Permata Premiere', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 09:24:11', 0),
(739, 58, 'Product deleted: Terracotta 3', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 09:29:01', 1),
(740, 59, 'Product deleted: Terracotta 3', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 09:29:01', 0),
(745, 58, 'Product deleted: Kemang Permata Premiere', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 09:33:58', 1),
(746, 59, 'Product deleted: Kemang Permata Premiere', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 09:33:58', 0),
(751, 58, 'New product added: Kemang Permata Premiere', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 09:34:53', 1),
(752, 59, 'New product added: Kemang Permata Premiere', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 09:34:53', 0),
(757, 58, 'Product deleted: Kemang Permata Premiere', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 09:39:34', 1),
(758, 59, 'Product deleted: Kemang Permata Premiere', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 09:39:34', 0),
(763, 58, 'New product added: Kemang Permata Premiere', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 09:43:14', 1),
(764, 59, 'New product added: Kemang Permata Premiere', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 09:43:14', 0),
(769, 58, 'Product deleted: Kemang Permata Premiere', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 09:54:25', 1),
(770, 59, 'Product deleted: Kemang Permata Premiere', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 09:54:25', 0),
(775, 58, 'New product added: Ziqi Raehan', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 09:55:00', 1),
(776, 59, 'New product added: Ziqi Raehan', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 09:55:00', 0),
(781, 58, 'Product deleted: Ziqi Raehan', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 09:56:53', 1),
(782, 59, 'Product deleted: Ziqi Raehan', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 09:56:53', 0),
(787, 58, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 10:00:37', 1),
(788, 59, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 10:00:37', 0),
(793, 58, 'Product deleted: Cikal', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 11:02:52', 1),
(794, 59, 'Product deleted: Cikal', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 11:02:52', 0),
(799, 58, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 11:10:06', 1),
(800, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 11:10:06', 0),
(805, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 11:10:17', 1),
(806, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 11:10:17', 0),
(811, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 11:10:27', 1),
(812, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-20 11:10:27', 0),
(817, 58, 'New product added: Muhammad Refa', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 11:13:59', 1),
(818, 59, 'New product added: Muhammad Refa', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 11:13:59', 0),
(823, 58, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 11:15:47', 1),
(824, 59, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 11:15:47', 0),
(829, 58, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 11:15:47', 1),
(830, 59, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 11:15:47', 0),
(835, 58, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 11:16:06', 1),
(836, 59, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 11:16:06', 0),
(841, 58, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 11:16:45', 1),
(842, 59, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 11:16:45', 0),
(847, 58, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 11:17:20', 1),
(848, 59, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 11:17:20', 0),
(853, 58, 'New product added: Terracotta 3', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 11:36:28', 1),
(854, 59, 'New product added: Terracotta 3', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 11:36:28', 0),
(859, 58, 'New product added: Terracotta Insert', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 13:22:07', 1),
(860, 59, 'New product added: Terracotta Insert', 'building', 'warning', 'dashboard/produk.php', '2025-03-20 13:22:07', 0),
(865, 58, 'Product updated: Terracotta Insert', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 13:29:35', 1),
(866, 59, 'Product updated: Terracotta Insert', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 13:29:35', 0),
(871, 58, 'Product updated: Terracotta Insert', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 13:29:52', 1),
(872, 59, 'Product updated: Terracotta Insert', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 13:29:52', 0),
(877, 58, 'Product updated: Terracotta Insert', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 13:30:05', 1),
(878, 59, 'Product updated: Terracotta Insert', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-20 13:30:05', 0),
(883, 58, 'Contact deleted: Egi Apriliansyah', 'trash', 'danger', 'dashboard/contact.php', '2025-03-20 14:18:21', 1),
(884, 59, 'Contact deleted: Egi Apriliansyah', 'trash', 'danger', 'dashboard/contact.php', '2025-03-20 14:18:21', 0),
(889, 58, 'New order received from: Muhammad Refa Fatur Rahman', 'shopping-cart', 'success', 'dashboard/contact.php', '2025-03-20 14:43:30', 1),
(890, 59, 'New order received from: Muhammad Refa Fatur Rahman', 'shopping-cart', 'success', 'dashboard/contact.php', '2025-03-20 14:43:30', 0),
(895, 58, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-21 10:10:35', 1),
(896, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-21 10:10:35', 0),
(901, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-21 10:15:16', 1),
(902, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-21 10:15:16', 0),
(907, 58, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-03-21 11:15:10', 1),
(908, 59, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-03-21 11:15:10', 0),
(913, 58, 'Product deleted: Cikal', 'trash', 'danger', 'dashboard/produk.php', '2025-03-21 13:10:47', 1),
(914, 59, 'Product deleted: Cikal', 'trash', 'danger', 'dashboard/produk.php', '2025-03-21 13:10:47', 0),
(919, 58, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-21 13:11:26', 1),
(920, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-03-21 13:11:26', 0),
(925, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-21 13:25:25', 1),
(926, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-03-21 13:25:25', 0),
(931, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-21 14:17:25', 1),
(932, 59, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-03-21 14:17:25', 0),
(937, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-21 14:18:06', 1),
(938, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-21 14:18:06', 0),
(943, 58, 'New news article: Muhammad Refa Fatur Rahman', 'newspaper', 'info', 'dashboard/news.php', '2025-03-21 14:25:33', 1),
(944, 59, 'New news article: Muhammad Refa Fatur Rahman', 'newspaper', 'info', 'dashboard/news.php', '2025-03-21 14:25:33', 0),
(949, 58, 'News article updated: Muhammad Refa Fatur Rahman', 'pencil', 'info', 'dashboard/news.php', '2025-03-21 14:25:54', 1),
(950, 59, 'News article updated: Muhammad Refa Fatur Rahman', 'pencil', 'info', 'dashboard/news.php', '2025-03-21 14:25:54', 0),
(955, 58, 'News article deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/news.php', '2025-03-21 14:26:05', 1),
(956, 59, 'News article deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/news.php', '2025-03-21 14:26:05', 0),
(961, 58, 'Product updated: Kemang Permata Neo', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-21 14:44:30', 1),
(962, 59, 'Product updated: Kemang Permata Neo', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-21 14:44:30', 0),
(967, 58, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-21 14:44:48', 1),
(968, 59, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-21 14:44:48', 0),
(973, 58, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-21 14:45:17', 1),
(974, 59, 'Product updated: Terracotta 3', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-21 14:45:17', 0),
(979, 58, 'Product updated: Terracotta Insert', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-21 14:45:34', 1),
(980, 59, 'Product updated: Terracotta Insert', 'pencil', 'warning', 'dashboard/produk.php', '2025-03-21 14:45:34', 0),
(985, 58, 'New order received from: Muhammad Refa Fatur Rahman', 'shopping-cart', 'success', 'dashboard/contact.php', '2025-03-21 15:59:21', 1),
(986, 59, 'New order received from: Muhammad Refa Fatur Rahman', 'shopping-cart', 'success', 'dashboard/contact.php', '2025-03-21 15:59:21', 0),
(991, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-24 09:17:00', 1),
(992, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-24 09:17:00', 0),
(997, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-24 09:19:59', 1),
(998, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-24 09:19:59', 0),
(1004, 59, 'New product added: Apoyy', 'building', 'warning', 'dashboard/produk.php', '2025-03-24 09:24:16', 0),
(1010, 59, 'New product added: Apoyy', 'building', 'warning', 'dashboard/produk.php', '2025-03-24 09:38:41', 0),
(1016, 59, 'Product deleted: Apoyy', 'trash', 'danger', 'dashboard/produk.php', '2025-03-24 09:38:55', 0),
(1021, 58, 'Product deleted: Apoyy', 'trash', 'danger', 'dashboard/produk.php', '2025-03-24 10:09:29', 1),
(1022, 59, 'Product deleted: Apoyy', 'trash', 'danger', 'dashboard/produk.php', '2025-03-24 10:09:29', 0),
(1027, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-24 15:28:16', 1),
(1028, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-24 15:28:16', 0),
(1033, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-24 15:33:42', 1),
(1034, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-24 15:33:42', 0),
(1039, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-24 15:41:01', 1),
(1040, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-24 15:41:01', 0),
(1046, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-25 09:18:06', 0),
(1051, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-26 15:37:53', 1),
(1052, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-03-26 15:37:53', 0),
(1058, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-08 09:15:00', 0),
(1063, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-08 14:05:21', 1),
(1064, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-08 14:05:21', 0),
(1069, 58, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-04-08 14:06:02', 1),
(1070, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-04-08 14:06:02', 0),
(1075, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-09 09:16:36', 1),
(1076, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-09 09:16:36', 0),
(1081, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-09 09:22:04', 1),
(1082, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-09 09:22:04', 0),
(1087, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-09 09:31:16', 1),
(1088, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-09 09:31:16', 0),
(1093, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-09 09:41:13', 1),
(1094, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-09 09:41:13', 0),
(1099, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-04-09 09:41:46', 1),
(1100, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-04-09 09:41:46', 0),
(1105, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-09 13:13:14', 1),
(1106, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-09 13:13:14', 0),
(1111, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-09 20:02:52', 1),
(1112, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-09 20:02:52', 0),
(1117, 58, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-04-09 21:06:34', 1),
(1118, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-04-09 21:06:34', 0),
(1123, 58, 'Product updated: Muhammad Refa Fatur Rahman', 'pencil', 'warning', 'dashboard/produk.php', '2025-04-09 21:08:34', 1),
(1124, 59, 'Product updated: Muhammad Refa Fatur Rahman', 'pencil', 'warning', 'dashboard/produk.php', '2025-04-09 21:08:34', 0),
(1129, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-04-09 22:18:27', 1),
(1130, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-04-09 22:18:27', 0),
(1136, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-04-09 22:19:17', 0),
(1141, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-04-09 23:57:36', 1),
(1142, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-04-09 23:57:36', 0),
(1148, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-04-09 23:58:03', 0),
(1154, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-10 09:14:08', 0),
(1159, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-04-10 13:16:12', 1),
(1160, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-04-10 13:16:12', 0),
(1165, 58, 'New product added: Ziqi Raehan', 'building', 'warning', 'dashboard/produk.php', '2025-04-10 13:16:36', 1),
(1166, 59, 'New product added: Ziqi Raehan', 'building', 'warning', 'dashboard/produk.php', '2025-04-10 13:16:36', 0),
(1172, 59, 'Product updated: Ziqi Raehan', 'pencil', 'warning', 'dashboard/produk.php', '2025-04-10 13:17:14', 0),
(1177, 58, 'Product updated: Ziqi Raehan', 'pencil', 'warning', 'dashboard/produk.php', '2025-04-10 13:30:22', 1),
(1178, 59, 'Product updated: Ziqi Raehan', 'pencil', 'warning', 'dashboard/produk.php', '2025-04-10 13:30:22', 0),
(1183, 58, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-04-10 14:05:47', 1),
(1184, 59, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-04-10 14:05:47', 0),
(1190, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-10 15:16:24', 0),
(1196, 59, 'Product deleted: Cikal', 'trash', 'danger', 'dashboard/produk.php', '2025-04-10 15:38:52', 0),
(1202, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-10 15:41:18', 0),
(1208, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-10 15:52:55', 0),
(1214, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-11 09:17:26', 0),
(1220, 59, 'New product added: Cikal', 'building', 'warning', 'dashboard/produk.php', '2025-04-11 13:46:20', 0),
(1226, 59, 'Product updated: Kemang Permata Premiere', 'pencil', 'warning', 'dashboard/produk.php', '2025-04-11 14:41:46', 0),
(1232, 59, 'Product deleted: Cikal', 'trash', 'danger', 'dashboard/produk.php', '2025-04-11 14:44:34', 0),
(1238, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-11 15:29:44', 0),
(1244, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-11 16:00:39', 0),
(1250, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-14 09:19:00', 0),
(1256, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-14 09:52:30', 0),
(1262, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-15 09:17:25', 0),
(1268, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-15 15:39:11', 0),
(1274, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-15 15:57:39', 0),
(1280, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-16 09:14:02', 0),
(1285, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-16 15:45:23', 1),
(1286, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-16 15:45:23', 0),
(1291, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-16 15:51:06', 1),
(1292, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-16 15:51:06', 0),
(1297, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-17 09:13:31', 1),
(1298, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-17 09:13:32', 0),
(1303, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-17 15:53:05', 1),
(1304, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-17 15:53:06', 0),
(1309, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-17 15:56:29', 1),
(1310, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-17 15:56:29', 0),
(1315, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-21 09:24:11', 1),
(1316, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-21 09:24:11', 0),
(1322, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-22 09:19:01', 0),
(1327, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-22 15:47:52', 1),
(1328, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-22 15:47:52', 0),
(1333, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-22 15:52:01', 1),
(1334, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-22 15:52:01', 0),
(1339, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-23 09:20:42', 1),
(1340, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-23 09:20:42', 0),
(1345, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-24 09:39:34', 1),
(1346, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-24 09:39:34', 0),
(1351, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-24 11:27:59', 1),
(1352, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-24 11:27:59', 0),
(1357, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-24 15:53:33', 1),
(1358, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-24 15:53:33', 0),
(1363, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-24 15:54:00', 1),
(1364, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-24 15:54:00', 0),
(1369, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-24 16:04:45', 1),
(1370, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-24 16:04:45', 0),
(1375, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-25 09:26:17', 1),
(1376, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-25 09:26:17', 0),
(1381, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-25 13:52:39', 1),
(1382, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-25 13:52:39', 0),
(1387, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-25 13:52:59', 1),
(1388, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-25 13:52:59', 0),
(1393, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-25 13:53:42', 1),
(1394, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-25 13:53:42', 0),
(1399, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-25 13:57:41', 1),
(1400, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-25 13:57:41', 0),
(1405, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 09:12:42', 1),
(1406, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 09:12:42', 0),
(1411, 58, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-04-28 09:13:12', 1),
(1412, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-04-28 09:13:12', 0),
(1417, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-04-28 09:13:29', 1),
(1418, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-04-28 09:13:29', 0),
(1423, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 14:00:31', 1),
(1424, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 14:00:31', 0),
(1429, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 14:06:27', 1),
(1430, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 14:06:27', 0),
(1435, 58, 'New user registered: sales1', 'user', 'primary', 'dashboard/tambah-user.php', '2025-04-28 14:07:27', 1),
(1436, 59, 'New user registered: sales1', 'user', 'primary', 'dashboard/tambah-user.php', '2025-04-28 14:07:27', 0),
(1442, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 14:07:52', 1),
(1443, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 14:07:52', 0),
(1449, 58, 'User updated: sales1', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-04-28 14:08:05', 1),
(1450, 59, 'User updated: sales1', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-04-28 14:08:05', 0);
INSERT INTO `tbl_user_notifications` (`id`, `user_id`, `message`, `icon`, `color`, `link`, `timestamp`, `is_read`) VALUES
(1456, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 14:08:13', 1),
(1457, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 14:08:13', 0),
(1463, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 14:14:16', 1),
(1464, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 14:14:16', 0),
(1470, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 14:15:20', 1),
(1471, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-28 14:15:20', 0),
(1477, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 09:14:13', 1),
(1478, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 09:14:13', 0),
(1484, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 10:17:22', 1),
(1485, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 10:17:22', 0),
(1491, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 10:17:49', 1),
(1492, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 10:17:49', 0),
(1498, 58, 'New lead added: Muhammad Refa', 'user-plus', 'success', 'dashboard/sales.php', '2025-04-29 13:13:26', 1),
(1499, 59, 'New lead added: Muhammad Refa', 'user-plus', 'success', 'dashboard/sales.php', '2025-04-29 13:13:26', 0),
(1505, 58, 'Lead deleted: Muhammad Refa', 'user-minus', 'danger', 'dashboard/sales.php', '2025-04-29 13:14:25', 1),
(1506, 59, 'Lead deleted: Muhammad Refa', 'user-minus', 'danger', 'dashboard/sales.php', '2025-04-29 13:14:25', 0),
(1512, 58, 'New user registered: Refa', 'user', 'primary', 'dashboard/tambah-user.php', '2025-04-29 13:15:54', 1),
(1513, 59, 'New user registered: Refa', 'user', 'primary', 'dashboard/tambah-user.php', '2025-04-29 13:15:54', 0),
(1520, 58, 'User deleted: Refa', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-29 13:16:03', 1),
(1521, 59, 'User deleted: Refa', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-29 13:16:03', 0),
(1527, 58, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-04-29 13:16:24', 1),
(1528, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-04-29 13:16:24', 0),
(1534, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-04-29 13:16:32', 1),
(1535, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-04-29 13:16:32', 0),
(1541, 58, 'Product updated: Terracotta Insert', 'pencil', 'warning', 'dashboard/produk.php', '2025-04-29 13:17:39', 1),
(1542, 59, 'Product updated: Terracotta Insert', 'pencil', 'warning', 'dashboard/produk.php', '2025-04-29 13:17:39', 0),
(1548, 58, 'New lead added: Egi Apriliansyah', 'user-plus', 'success', 'dashboard/sales.php', '2025-04-29 13:26:44', 1),
(1549, 59, 'New lead added: Egi Apriliansyah', 'user-plus', 'success', 'dashboard/sales.php', '2025-04-29 13:26:44', 0),
(1555, 58, 'Lead deleted: Egi Apriliansyah', 'user-minus', 'danger', 'dashboard/sales.php', '2025-04-29 13:27:20', 1),
(1556, 59, 'Lead deleted: Egi Apriliansyah', 'user-minus', 'danger', 'dashboard/sales.php', '2025-04-29 13:27:20', 0),
(1562, 58, 'New lead added: Muhammad Refa Fatur Rahman', 'user-plus', 'success', 'dashboard/sales.php', '2025-04-29 13:27:50', 1),
(1563, 59, 'New lead added: Muhammad Refa Fatur Rahman', 'user-plus', 'success', 'dashboard/sales.php', '2025-04-29 13:27:50', 0),
(1569, 58, 'Lead deleted: Muhammad Refa Fatur Rahman', 'user-minus', 'danger', 'dashboard/sales.php', '2025-04-29 13:28:02', 1),
(1570, 59, 'Lead deleted: Muhammad Refa Fatur Rahman', 'user-minus', 'danger', 'dashboard/sales.php', '2025-04-29 13:28:02', 0),
(1576, 58, 'Lead deleted: Muhammad Refa Fatur Rahman', 'user-minus', 'danger', 'dashboard/sales.php', '2025-04-29 13:34:46', 1),
(1577, 59, 'Lead deleted: Muhammad Refa Fatur Rahman', 'user-minus', 'danger', 'dashboard/sales.php', '2025-04-29 13:34:46', 0),
(1583, 58, 'New lead added: Muhammad Refa', 'user-plus', 'success', 'dashboard/sales.php', '2025-04-29 13:39:33', 1),
(1584, 59, 'New lead added: Muhammad Refa', 'user-plus', 'success', 'dashboard/sales.php', '2025-04-29 13:39:33', 0),
(1590, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 15:42:07', 1),
(1591, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 15:42:07', 0),
(1597, 58, 'User deleted: sales1', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-29 15:42:22', 1),
(1598, 59, 'User deleted: sales1', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-29 15:42:22', 0),
(1603, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 16:08:39', 1),
(1604, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 16:08:39', 0),
(1609, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 16:10:59', 1),
(1610, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 16:10:59', 0),
(1615, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 16:11:55', 1),
(1616, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-29 16:11:55', 0),
(1621, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:16:58', 1),
(1622, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:16:58', 0),
(1627, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:20:35', 1),
(1628, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:20:35', 0),
(1633, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:20:45', 1),
(1634, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:20:45', 0),
(1639, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:29:42', 1),
(1640, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:29:42', 0),
(1645, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:33:28', 1),
(1646, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:33:28', 0),
(1651, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:35:25', 1),
(1652, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:35:25', 0),
(1657, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:39:09', 1),
(1658, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:39:09', 0),
(1663, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:39:47', 1),
(1664, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:39:47', 0),
(1669, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:41:00', 1),
(1670, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:41:00', 0),
(1675, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:43:32', 1),
(1676, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:43:32', 0),
(1681, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:46:46', 1),
(1682, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:46:46', 0),
(1687, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:47:32', 1),
(1688, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:47:32', 0),
(1693, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:47:38', 1),
(1694, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:47:38', 0),
(1699, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:49:35', 1),
(1700, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:49:35', 0),
(1705, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:50:04', 1),
(1706, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:50:04', 0),
(1711, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:51:44', 1),
(1712, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:51:44', 0),
(1717, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:52:54', 1),
(1718, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:52:54', 0),
(1723, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:53:33', 1),
(1724, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:53:33', 0),
(1729, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:56:18', 1),
(1730, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:56:18', 0),
(1735, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:56:26', 1),
(1736, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:56:26', 0),
(1741, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:59:48', 1),
(1742, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 09:59:48', 0),
(1747, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:01:17', 1),
(1748, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:01:17', 0),
(1753, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:02:11', 1),
(1754, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:02:11', 0),
(1759, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:13:41', 1),
(1760, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:13:41', 0),
(1765, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:15:52', 1),
(1766, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:15:52', 0),
(1771, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:19:39', 1),
(1772, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:19:39', 0),
(1777, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:44:51', 1),
(1778, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:44:51', 0),
(1783, 58, 'User Egi Apriliansyah has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:46:15', 1),
(1784, 59, 'User Egi Apriliansyah has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 10:46:15', 0),
(1789, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 11:31:52', 1),
(1790, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 11:31:52', 0),
(1795, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 11:40:09', 1),
(1796, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 11:40:09', 0),
(1801, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 11:49:36', 1),
(1802, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 11:49:36', 0),
(1807, 58, 'New user registered: Muhammad Refa Fatur Rahman', 'user', 'primary', 'dashboard/tambah-user.php', '2025-04-30 11:52:04', 1),
(1808, 59, 'New user registered: Muhammad Refa Fatur Rahman', 'user', 'primary', 'dashboard/tambah-user.php', '2025-04-30 11:52:04', 0),
(1814, 58, 'User updated: Muhammad Refa Fatur Rahman', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-04-30 11:53:00', 1),
(1815, 59, 'User updated: Muhammad Refa Fatur Rahman', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-04-30 11:53:00', 0),
(1821, 58, 'User deleted: Muhammad Refa Fatur Rahman', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-30 11:53:19', 1),
(1822, 59, 'User deleted: Muhammad Refa Fatur Rahman', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-30 11:53:19', 0),
(1827, 58, 'User deleted: Egi Apriliansyah', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-30 11:53:51', 1),
(1828, 59, 'User deleted: Egi Apriliansyah', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-30 11:53:51', 0),
(1832, 58, 'User deleted: Badru', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-30 11:53:55', 1),
(1833, 59, 'User deleted: Badru', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-30 11:53:55', 0),
(1836, 58, 'User deleted: Muhammad Refa Fatur Rahman', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-30 11:53:58', 1),
(1837, 59, 'User deleted: Muhammad Refa Fatur Rahman', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-30 11:53:58', 0),
(1839, 58, 'User deleted: zio', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-30 11:54:01', 1),
(1840, 59, 'User deleted: zio', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-30 11:54:01', 0),
(1841, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 12:01:38', 1),
(1842, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 12:01:39', 0),
(1843, 58, 'New user registered: Muhammad Refa Fatur Rahman', 'user', 'primary', 'dashboard/tambah-user.php', '2025-04-30 12:02:06', 1),
(1844, 59, 'New user registered: Muhammad Refa Fatur Rahman', 'user', 'primary', 'dashboard/tambah-user.php', '2025-04-30 12:02:06', 0),
(1846, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 12:03:02', 1),
(1847, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 12:03:02', 0),
(1849, 58, 'User deleted: Muhammad Refa Fatur Rahman', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-30 12:03:08', 1),
(1850, 59, 'User deleted: Muhammad Refa Fatur Rahman', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-04-30 12:03:08', 0),
(1851, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 12:03:56', 1),
(1852, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 12:03:56', 0),
(1853, 58, 'New lead added: Leon Leonheart', 'user-plus', 'success', 'dashboard/sales.php', '2025-04-30 12:07:17', 1),
(1854, 59, 'New lead added: Leon Leonheart', 'user-plus', 'success', 'dashboard/sales.php', '2025-04-30 12:07:17', 0),
(1855, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 12:08:39', 1),
(1856, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 12:08:39', 0),
(1857, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 13:27:51', 1),
(1858, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 13:27:51', 0),
(1859, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 13:50:40', 1),
(1860, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 13:50:40', 0),
(1861, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:50:22', 1),
(1862, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:50:22', 0),
(1864, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:50:39', 0),
(1866, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:50:53', 0),
(1868, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:51:28', 0),
(1870, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:51:57', 0),
(1872, 59, 'Lead deleted: tes1', 'user-minus', 'danger', 'dashboard/sales.php', '2025-04-30 15:53:01', 0),
(1874, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:55:53', 0),
(1875, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:56:22', 1),
(1876, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:56:22', 0),
(1877, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:56:35', 1),
(1878, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:56:35', 0),
(1879, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:56:58', 1),
(1880, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 15:56:58', 0),
(1881, 58, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 16:04:41', 1),
(1882, 59, 'User Cikal has logged in', 'user', 'info', 'dashboard/profile.php', '2025-04-30 16:04:41', 0),
(1883, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-02 09:15:11', 1),
(1884, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-02 09:15:11', 0),
(1885, 58, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-05-02 09:32:01', 1),
(1886, 59, 'User updated: Cikal', 'user-edit', 'primary', 'dashboard/tambah-user.php', '2025-05-02 09:32:01', 0),
(1888, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-05 09:15:54', 0),
(1890, 59, 'New product added: Muhammad Refa Fatur Rahman', 'building', 'warning', 'dashboard/produk.php', '2025-05-05 13:28:15', 0),
(1891, 58, 'New lead added: Muhammad Refa Fatur Rahman', 'user-plus', 'success', 'dashboard/sales.php', '2025-05-05 14:40:48', 0),
(1892, 59, 'New lead added: Muhammad Refa Fatur Rahman', 'user-plus', 'success', 'dashboard/sales.php', '2025-05-05 14:40:48', 0),
(1893, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-05 16:02:53', 0),
(1894, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-05 16:02:53', 0),
(1895, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-12 21:57:47', 0),
(1896, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-12 21:57:47', 0),
(1897, 58, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-05-12 22:01:48', 0),
(1898, 59, 'Product deleted: Muhammad Refa Fatur Rahman', 'trash', 'danger', 'dashboard/produk.php', '2025-05-12 22:01:48', 0),
(1900, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-14 09:46:09', 0),
(1901, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-14 09:46:09', 0),
(1902, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-14 22:54:58', 0),
(1903, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-14 22:54:58', 0),
(1904, 58, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-14 23:16:30', 0),
(1905, 59, 'User Muhammad Refa Fatur Rahman has logged in', 'user', 'info', 'dashboard/profile.php', '2025-05-14 23:16:30', 0),
(1906, 58, 'New user registered: xfhgvjh', 'user', 'primary', 'dashboard/tambah-user.php', '2025-05-14 23:17:14', 0),
(1907, 59, 'New user registered: xfhgvjh', 'user', 'primary', 'dashboard/tambah-user.php', '2025-05-14 23:17:14', 0),
(1909, 58, 'User deleted: xfhgvjh', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-05-14 23:17:52', 0),
(1910, 59, 'User deleted: xfhgvjh', 'user-minus', 'danger', 'dashboard/tambah-user.php', '2025-05-14 23:17:52', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_visitors`
--

CREATE TABLE `tbl_visitors` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `visit_time` datetime DEFAULT NULL,
  `page` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_visitors`
--

INSERT INTO `tbl_visitors` (`id`, `ip_address`, `user_agent`, `visit_time`, `page`) VALUES
(1, '192.168.137.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 15:47:23', '/index.php'),
(2, '192.168.137.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 15:48:12', '/index.php'),
(3, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 15:57:28', '/index.php'),
(4, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:02:38', '/index.php'),
(5, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:02:42', '/index.php'),
(6, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:02:44', '/index.php'),
(7, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:02:46', '/index.php'),
(8, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:03:07', '/index.php'),
(9, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:03:09', '/index.php'),
(10, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:03:10', '/index.php'),
(11, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:03:11', '/index.php'),
(12, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:14:37', '/index.php'),
(13, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:14:41', '/about.php'),
(14, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:14:44', '/np.php'),
(15, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:14:50', '/produk.php'),
(16, '192.168.54.78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-08 16:14:54', '/contact.php'),
(17, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 03:15:10', '/index.php'),
(18, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 03:46:59', '/index.php'),
(19, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:22:16', '/index.php'),
(20, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:44:36', '/index.php'),
(21, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:44:38', '/about.php'),
(22, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:45:01', '/np.php'),
(23, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:49:22', '/np.php'),
(24, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:49:26', '/produk.php'),
(25, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:50:15', '/produk.php'),
(26, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:50:25', '/index.php'),
(27, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:57:52', '/index.php'),
(28, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:57:57', '/index.php'),
(29, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:57:59', '/about.php'),
(30, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:58:00', '/np.php'),
(31, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:58:02', '/produk.php'),
(32, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 04:58:04', '/index.php'),
(33, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:00:02', '/np.php'),
(34, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:00:03', '/about.php'),
(35, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:00:25', '/np.php'),
(36, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:01:28', '/np.php'),
(37, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:02:11', '/np.php'),
(38, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:02:58', '/np.php'),
(39, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:03:15', '/np.php'),
(40, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:03:44', '/about.php'),
(41, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:03:46', '/np.php'),
(42, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:03:48', '/produk.php'),
(43, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:03:50', '/np.php'),
(44, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:03:56', '/index.php'),
(45, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:04:02', '/np.php'),
(46, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:04:28', '/np.php'),
(47, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:05:05', '/np.php'),
(48, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:06:10', '/produk.php'),
(49, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:06:17', '/contact.php'),
(50, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:06:25', '/produk.php'),
(51, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:11:59', '/contact.php'),
(52, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:16:59', '/contact.php'),
(53, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:17:56', '/contact.php'),
(54, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:18:06', '/index.php'),
(55, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:19:21', '/about.php'),
(56, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:19:24', '/np.php'),
(57, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 05:56:52', '/index.php'),
(58, '10.1.2.249', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-10 05:58:38', '/index.php'),
(59, '10.1.2.249', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-10 05:58:42', '/index.php'),
(60, '10.1.2.249', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-10 05:58:46', '/index.php'),
(61, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 07:18:30', '/index.php'),
(62, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 07:44:57', '/index.php'),
(63, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 07:46:12', '/about.php'),
(64, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 07:47:10', '/produk.php'),
(65, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 07:47:12', '/contact.php'),
(66, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 07:47:30', '/contact.php'),
(67, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 07:55:24', '/index.php'),
(68, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 08:27:01', '/index.php'),
(69, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 08:27:49', '/index.php'),
(70, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 08:28:16', '/index.php'),
(71, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 08:50:35', '/index.php'),
(72, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 08:50:40', '/produk.php'),
(73, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 09:10:19', '/contact.php'),
(74, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 09:10:29', '/produk.php'),
(75, '10.1.2.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-10 09:15:20', '/index.php'),
(76, '10.1.2.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-10 09:15:26', '/np.php'),
(77, '10.1.2.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-10 09:15:58', '/np.php'),
(78, '10.1.2.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-10 09:16:00', '/produk.php'),
(79, '10.1.2.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-10 09:16:06', '/index.php'),
(80, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 09:36:21', '/np.php'),
(81, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 09:37:27', '/np.php'),
(82, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 09:37:46', '/produk.php'),
(83, '10.1.2.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-10 09:38:13', '/about.php'),
(84, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:05:39', '/index.php'),
(85, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:05:58', '/about.php'),
(86, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:06:53', '/about.php'),
(87, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:07:52', '/about.php'),
(88, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:08:18', '/about.php'),
(89, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:09:56', '/about.php'),
(90, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:11:17', '/about.php'),
(91, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:12:01', '/about.php'),
(92, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:12:44', '/about.php'),
(93, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:13:04', '/about.php'),
(94, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:13:50', '/index.php'),
(95, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:13:56', '/np.php'),
(96, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:14:03', '/about.php'),
(97, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:15:19', '/about.php'),
(98, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:17:26', '/about.php'),
(99, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:18:50', '/about.php'),
(100, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:19:58', '/about.php'),
(101, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:21:41', '/about.php'),
(102, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:23:11', '/about.php'),
(103, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:24:15', '/about.php'),
(104, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:24:49', '/about.php'),
(105, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:24:50', '/index.php'),
(106, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:24:54', '/about.php'),
(107, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:24:57', '/np.php'),
(108, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:24:59', '/produk.php'),
(109, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:25:04', '/contact.php'),
(110, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:37:27', '/index.php'),
(111, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:37:32', '/contact.php'),
(112, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:37:47', '/contact.php'),
(113, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:42:16', '/contact.php'),
(114, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:44:17', '/produk.php'),
(115, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:46:55', '/produk.php'),
(116, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 03:52:35', '/produk.php'),
(117, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 04:05:15', '/contact.php'),
(118, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 04:05:44', '/contact.php'),
(119, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:31:43', '/index.php'),
(120, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:12', '/index.php'),
(121, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:12', '/index.php'),
(122, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:12', '/index.php'),
(123, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:13', '/index.php'),
(124, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:14', '/index.php'),
(125, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:14', '/index.php'),
(126, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:14', '/index.php'),
(127, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:14', '/index.php'),
(128, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:14', '/index.php'),
(129, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:14', '/index.php'),
(130, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:15', '/index.php'),
(131, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:15', '/index.php'),
(132, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:15', '/index.php'),
(133, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:15', '/index.php'),
(134, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:15', '/index.php'),
(135, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:15', '/index.php'),
(136, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:16', '/index.php'),
(137, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:16', '/index.php'),
(138, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:16', '/index.php'),
(139, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:16', '/index.php'),
(140, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:16', '/index.php'),
(141, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:17', '/index.php'),
(142, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:17', '/index.php'),
(143, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:17', '/index.php'),
(144, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:17', '/index.php'),
(145, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:17', '/index.php'),
(146, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:17', '/index.php'),
(147, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:18', '/index.php'),
(148, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:18', '/index.php'),
(149, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:18', '/index.php'),
(150, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:18', '/index.php'),
(151, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:18', '/index.php'),
(152, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:19', '/index.php'),
(153, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:19', '/index.php'),
(154, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:19', '/index.php'),
(155, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:19', '/index.php'),
(156, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:19', '/index.php'),
(157, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:20', '/index.php'),
(158, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:20', '/index.php'),
(159, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:20', '/index.php'),
(160, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:20', '/index.php'),
(161, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:20', '/index.php'),
(162, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:32:21', '/index.php'),
(163, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:34:02', '/index.php'),
(164, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:34:05', '/about.php'),
(165, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 04:34:08', '/np.php'),
(166, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 04:34:25', '/index.php'),
(167, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 04:34:25', '/index.php'),
(168, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 04:34:28', '/index.php'),
(169, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 04:34:30', '/about.php'),
(170, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 04:34:31', '/np.php'),
(171, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 04:34:32', '/index.php'),
(172, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 06:00:19', '/index.php'),
(173, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 06:00:21', '/about.php'),
(174, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 06:01:07', '/about.php'),
(175, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 07:09:55', '/about.php'),
(176, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 07:10:43', '/about.php'),
(177, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 07:11:01', '/np.php'),
(178, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 07:11:11', '/index.php'),
(179, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 07:11:16', '/index.php'),
(180, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 07:11:38', '/contact.php'),
(181, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 07:12:22', '/contact.php'),
(182, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 07:13:56', '/contact.php'),
(183, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 07:13:58', '/contact.php'),
(184, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 07:16:54', '/about.php'),
(185, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-11 07:19:13', '/np.php'),
(186, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 08:21:51', '/index.php'),
(187, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 08:36:31', '/index.php'),
(188, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 08:36:33', '/index.php'),
(189, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 08:36:35', '/index.php'),
(190, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 08:36:37', '/index.php'),
(191, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 08:37:01', '/contact.php'),
(192, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 08:44:03', '/index.php'),
(193, '10.1.2.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '2025-03-11 08:50:44', '/np.php'),
(194, '10.1.2.254', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-12 03:14:57', '/index.php'),
(195, '10.1.2.152', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-03-12 03:17:36', '/index.php'),
(196, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:24:14', '/bogor-raya-residential/index.php'),
(197, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:24:29', '/bogor-raya-residential/index.php'),
(198, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:24:44', '/bogor-raya-residential/index.php'),
(199, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:35:11', '/bogor-raya-residential/index.php'),
(200, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:35:19', '/bogor-raya-residential/about.php'),
(201, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:36:14', '/bogor-raya-residential/index.php'),
(202, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:36:39', '/bogor-raya-residential/index.php'),
(203, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:36:42', '/bogor-raya-residential/index.php'),
(204, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:39:26', '/bogor-raya-residential/index.php'),
(205, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:40:09', '/bogor-raya-residential/index.php'),
(206, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:40:43', '/bogor-raya-residential/index.php'),
(207, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:40:52', '/bogor-raya-residential/about.php'),
(208, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:41:30', '/bogor-raya-residential/about.php'),
(209, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:42:03', '/bogor-raya-residential/about.php'),
(210, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:42:08', '/bogor-raya-residential/np.php'),
(211, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:42:42', '/bogor-raya-residential/np.php'),
(212, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:43:05', '/bogor-raya-residential/produk.php'),
(213, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:43:44', '/bogor-raya-residential/index.php'),
(214, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:43:47', '/bogor-raya-residential/produk.php'),
(215, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:44:11', '/bogor-raya-residential/produk.php'),
(216, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:44:25', '/bogor-raya-residential/produk.php'),
(217, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:44:53', '/bogor-raya-residential/contact.php'),
(218, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:44:58', '/bogor-raya-residential/index.php'),
(219, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:45:02', '/bogor-raya-residential/about.php'),
(220, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:45:07', '/bogor-raya-residential/np.php'),
(221, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:45:08', '/bogor-raya-residential/produk.php'),
(222, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:45:09', '/bogor-raya-residential/contact.php'),
(223, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:49:05', '/bogor-raya-residential/index.php'),
(224, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:49:22', '/bogor-raya-residential/index.php'),
(225, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:49:23', '/bogor-raya-residential/index.php'),
(226, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:49:41', '/bogor-raya-residential/index.php'),
(227, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:50:14', '/bogor-raya-residential/index.php'),
(228, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:50:19', '/bogor-raya-residential/index.php'),
(229, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:50:36', '/bogor-raya-residential/index.php'),
(230, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:50:38', '/bogor-raya-residential/about.php'),
(231, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:50:39', '/bogor-raya-residential/np.php'),
(232, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:50:40', '/bogor-raya-residential/produk.php'),
(233, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:50:41', '/bogor-raya-residential/contact.php'),
(234, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:50:42', '/bogor-raya-residential/produk.php'),
(235, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:50:45', '/bogor-raya-residential/np.php'),
(236, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:50:50', '/bogor-raya-residential/about.php'),
(237, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 03:50:55', '/bogor-raya-residential/index.php'),
(238, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 04:05:35', '/bogor-raya-residential/index.php'),
(239, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 04:05:43', '/bogor-raya-residential/index.php'),
(240, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 04:05:50', '/bogor-raya-residential/about.php'),
(241, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 04:11:19', '/bogor-raya-residential/produk.php'),
(242, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 04:26:40', '/bogor-raya-residential/produk.php'),
(243, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-13 05:25:41', '/bogor-raya-residential/about.php'),
(244, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-17 03:23:56', '/bogor-raya-residential/index.php'),
(245, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-19 03:13:02', '/bogor-raya-residential/index.php'),
(246, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-19 03:27:16', '/bogor-raya-residential/index.php'),
(247, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-19 03:27:19', '/bogor-raya-residential/produk.php'),
(248, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 03:11:41', '/bogor-raya-residential/index.php'),
(249, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 03:11:51', '/bogor-raya-residential/produk.php'),
(250, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:34:07', '/bogor-raya-residential/index.php'),
(251, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:34:10', '/bogor-raya-residential/produk.php'),
(252, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:36:25', '/bogor-raya-residential/produk.php'),
(253, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:38:45', '/bogor-raya-residential/produk.php'),
(254, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:42:54', '/bogor-raya-residential/produk.php'),
(255, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:43:56', '/bogor-raya-residential/produk.php'),
(256, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:46:06', '/bogor-raya-residential/produk.php'),
(257, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:48:04', '/bogor-raya-residential/produk.php'),
(258, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:49:04', '/bogor-raya-residential/produk.php'),
(259, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:51:21', '/bogor-raya-residential/produk.php'),
(260, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:53:37', '/bogor-raya-residential/produk.php'),
(261, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:56:00', '/bogor-raya-residential/about.php'),
(262, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:56:26', '/bogor-raya-residential/produk.php'),
(263, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 07:56:46', '/bogor-raya-residential/produk.php'),
(264, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:00:54', '/bogor-raya-residential/produk.php'),
(265, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:01:21', '/bogor-raya-residential/produk.php'),
(266, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:06:10', '/bogor-raya-residential/produk.php'),
(267, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:06:25', '/bogor-raya-residential/produk.php'),
(268, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:08:21', '/bogor-raya-residential/produk.php'),
(269, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:08:43', '/bogor-raya-residential/np.php'),
(270, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:08:50', '/bogor-raya-residential/index.php'),
(271, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:09:47', '/bogor-raya-residential/index.php'),
(272, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:12:03', '/bogor-raya-residential/index.php'),
(273, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:12:15', '/bogor-raya-residential/index.php'),
(274, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:12:36', '/bogor-raya-residential/index.php'),
(275, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:12:51', '/bogor-raya-residential/index.php'),
(276, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:13:15', '/bogor-raya-residential/contact.php'),
(277, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:13:20', '/bogor-raya-residential/produk.php'),
(278, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:13:25', '/bogor-raya-residential/np.php'),
(279, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:13:26', '/bogor-raya-residential/produk.php'),
(280, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:14:04', '/bogor-raya-residential/np.php'),
(281, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:14:06', '/bogor-raya-residential/contact.php'),
(282, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-20 08:43:30', '/bogor-raya-residential/contact.php'),
(283, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 07:46:55', '/bogor-raya-residential/index.php');
INSERT INTO `tbl_visitors` (`id`, `ip_address`, `user_agent`, `visit_time`, `page`) VALUES
(284, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 07:46:58', '/bogor-raya-residential/produk.php'),
(285, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 07:48:29', '/bogor-raya-residential/produk.php'),
(286, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 07:49:50', '/bogor-raya-residential/produk.php'),
(287, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 07:54:36', '/bogor-raya-residential/produk.php'),
(288, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 07:57:00', '/bogor-raya-residential/produk.php'),
(289, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 07:57:42', '/bogor-raya-residential/produk.php'),
(290, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 07:58:04', '/bogor-raya-residential/produk.php'),
(291, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 08:00:25', '/bogor-raya-residential/produk.php'),
(292, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 08:04:55', '/bogor-raya-residential/produk.php'),
(293, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 08:06:48', '/bogor-raya-residential/produk.php'),
(294, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 08:45:43', '/bogor-raya-residential/produk.php'),
(295, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 08:46:42', '/bogor-raya-residential/about.php'),
(296, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 08:46:43', '/bogor-raya-residential/index.php'),
(297, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 08:47:00', '/bogor-raya-residential/contact.php'),
(298, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 08:53:14', '/bogor-raya-residential/contact.php'),
(299, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 08:53:21', '/bogor-raya-residential/contact.php'),
(300, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 08:53:34', '/bogor-raya-residential/contact.php'),
(301, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 08:54:08', '/bogor-raya-residential/contact.php'),
(302, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 09:51:38', '/bogor-raya-residential/index.php'),
(303, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 09:52:03', '/bogor-raya-residential/produk.php'),
(304, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 09:52:06', '/bogor-raya-residential/index.php'),
(305, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 09:52:41', '/bogor-raya-residential/contact.php'),
(306, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-21 09:59:21', '/bogor-raya-residential/contact.php'),
(307, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 03:19:08', '/index.php'),
(308, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 03:19:22', '/about.php'),
(309, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 03:19:24', '/np.php'),
(310, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 03:19:25', '/contact.php'),
(311, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:08:40', '/index.php'),
(312, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:08:49', '/produk.php'),
(313, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:09:09', '/produk.php'),
(314, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:09:14', '/np.php'),
(315, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:09:36', '/index.php'),
(316, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:14:10', '/np.php'),
(317, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:14:13', '/produk.php'),
(318, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:14:22', '/index.php'),
(319, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:14:55', '/about.php'),
(320, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:21:04', '/about.php'),
(321, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:21:34', '/about.php'),
(322, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:37:58', '/index.php'),
(323, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:38:05', '/about.php'),
(324, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:38:13', '/index.php'),
(325, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 04:39:14', '/produk.php'),
(326, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 05:45:45', '/index.php'),
(327, '10.1.2.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-24 05:46:47', '/index.php'),
(328, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-24 09:33:28', '/index.php'),
(329, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-24 09:33:33', '/index.php/login'),
(330, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-25 03:17:38', '/bogor-raya-residential/index.php'),
(331, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-25 03:17:52', '/bogor-raya-residential/index.php'),
(332, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 03:29:27', '/bogor-raya-residential/index.php'),
(333, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 03:29:34', '/bogor-raya-residential/produk.php'),
(334, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 03:32:34', '/bogor-raya-residential/produk.php'),
(335, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 03:33:12', '/bogor-raya-residential/produk.php'),
(336, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 03:33:31', '/bogor-raya-residential/produk.php'),
(337, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 03:36:53', '/bogor-raya-residential/produk.php'),
(338, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 03:40:16', '/bogor-raya-residential/produk.php'),
(339, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 03:42:33', '/bogor-raya-residential/contact.php'),
(340, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 03:42:38', '/bogor-raya-residential/produk.php'),
(341, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 04:14:44', '/bogor-raya-residential/contact.php'),
(342, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 04:40:02', '/bogor-raya-residential/index.php'),
(343, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 04:40:04', '/bogor-raya-residential/produk.php'),
(344, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 09:39:04', '/index.php'),
(345, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 09:39:07', '/produk.php'),
(346, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-03-26 09:40:49', '/produk.php'),
(347, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 04:13:29', '/bogor-raya-residential/index.php'),
(348, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 04:13:51', '/bogor-raya-residential/about.php'),
(349, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 04:14:05', '/bogor-raya-residential/np.php'),
(350, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 04:14:55', '/bogor-raya-residential/index.php'),
(351, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 08:57:44', '/bogor-raya-residential/np.php'),
(352, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 08:57:57', '/bogor-raya-residential/index.php'),
(353, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 09:04:33', '/bogor-raya-residential/produk.php'),
(354, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 09:06:09', '/bogor-raya-residential/produk.php'),
(355, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 09:11:35', '/bogor-raya-residential/produk.php'),
(356, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 09:14:32', '/bogor-raya-residential/produk.php'),
(357, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 09:41:35', '/bogor-raya-residential/produk.php'),
(358, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 09:41:59', '/bogor-raya-residential/contact.php'),
(359, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 09:47:57', '/bogor-raya-residential/about.php'),
(360, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 09:47:58', '/bogor-raya-residential/index.php'),
(361, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 09:48:06', '/bogor-raya-residential/produk.php'),
(362, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:33:32', '/bogor-raya-residential/index.php'),
(363, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:34:38', '/bogor-raya-residential/index.php'),
(364, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:34:48', '/bogor-raya-residential/about.php'),
(365, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:37:06', '/bogor-raya-residential/index.php'),
(366, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:37:13', '/bogor-raya-residential/index.php'),
(367, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:37:32', '/bogor-raya-residential/index.php'),
(368, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:37:35', '/bogor-raya-residential/index.php'),
(369, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:39:18', '/bogor-raya-residential/index.php'),
(370, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:42:02', '/bogor-raya-residential/index.php'),
(371, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:42:14', '/bogor-raya-residential/about.php'),
(372, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:55:08', '/bogor-raya-residential/index.php'),
(373, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:55:23', '/bogor-raya-residential/index.php'),
(374, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:57:45', '/bogor-raya-residential/index.php'),
(375, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 04:59:03', '/bogor-raya-residential/index.php'),
(376, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 05:01:15', '/bogor-raya-residential/index.php'),
(377, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 05:06:40', '/bogor-raya-residential/index.php'),
(378, '10.1.2.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 05:09:10', '/index.php'),
(379, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 05:13:52', '/bogor-raya-residential/index.php'),
(380, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:09:50', '/bogor-raya-residential/index.php'),
(381, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:09:57', '/bogor-raya-residential/index.php'),
(382, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:26:01', '/bogor-raya-residential/produk.php'),
(383, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:26:15', '/bogor-raya-residential/contact.php'),
(384, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:26:19', '/bogor-raya-residential/index.php'),
(385, '10.1.2.232', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-04-09 06:31:18', '/index.php'),
(386, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:33:49', '/bogor-raya-residential/index.php'),
(387, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:35:57', '/bogor-raya-residential/index.php'),
(388, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:38:19', '/bogor-raya-residential/index.php'),
(389, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:40:24', '/bogor-raya-residential/index.php'),
(390, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:40:40', '/bogor-raya-residential/index.php'),
(391, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:41:16', '/bogor-raya-residential/index.php'),
(392, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:41:25', '/bogor-raya-residential/index.php'),
(393, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:42:53', '/bogor-raya-residential/index.php'),
(394, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:43:26', '/bogor-raya-residential/index.php'),
(395, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:43:33', '/bogor-raya-residential/index.php'),
(396, '10.1.2.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:43:46', '/index.php'),
(397, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:44:00', '/bogor-raya-residential/index.php'),
(398, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:44:19', '/bogor-raya-residential/index.php'),
(399, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:44:54', '/bogor-raya-residential/index.php'),
(400, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:44:56', '/bogor-raya-residential/index.php'),
(401, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 06:47:22', '/bogor-raya-residential/index.php'),
(402, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 06:38:42', '/bogor-raya-residential/index.php'),
(403, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 06:38:46', '/bogor-raya-residential/produk.php'),
(404, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 06:41:56', '/bogor-raya-residential/produk.php'),
(405, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 06:42:19', '/bogor-raya-residential/produk.php'),
(406, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 06:44:25', '/bogor-raya-residential/produk.php'),
(407, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 06:44:49', '/bogor-raya-residential/produk.php'),
(408, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 08:58:53', '/bogor-raya-residential/produk.php'),
(409, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:05:14', '/bogor-raya-residential/produk.php'),
(410, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:06:45', '/bogor-raya-residential/produk.php'),
(411, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:06:52', '/bogor-raya-residential/produk.php'),
(412, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:09:46', '/bogor-raya-residential/produk.php'),
(413, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:44:02', '/bogor-raya-residential/produk.php'),
(414, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:46:39', '/bogor-raya-residential/about.php'),
(415, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:46:39', '/bogor-raya-residential/index.php'),
(416, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:46:51', '/bogor-raya-residential/index.php'),
(417, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:46:57', '/bogor-raya-residential/np.php'),
(418, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:47:28', '/bogor-raya-residential/np.php'),
(419, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:47:30', '/bogor-raya-residential/produk.php'),
(420, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:51:41', '/index.php'),
(421, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 10:51:48', '/produk.php'),
(422, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 05:35:34', '/bogor-raya-residential/index.php'),
(423, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 05:35:38', '/bogor-raya-residential/np.php'),
(424, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 05:35:42', '/bogor-raya-residential/produk.php'),
(425, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 05:38:40', '/bogor-raya-residential/produk.php'),
(426, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 05:39:01', '/bogor-raya-residential/produk.php'),
(427, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 05:40:34', '/bogor-raya-residential/produk.php'),
(428, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 05:41:38', '/bogor-raya-residential/produk.php'),
(429, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 05:45:37', '/bogor-raya-residential/produk.php'),
(430, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:17:41', '/bogor-raya-residential/produk.php'),
(431, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:18:22', '/bogor-raya-residential/produk.php'),
(432, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:19:24', '/bogor-raya-residential/produk.php'),
(433, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:19:37', '/bogor-raya-residential/produk.php'),
(434, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:20:50', '/bogor-raya-residential/produk.php'),
(435, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:21:16', '/bogor-raya-residential/produk.php'),
(436, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:21:56', '/bogor-raya-residential/produk.php'),
(437, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:23:04', '/bogor-raya-residential/produk.php'),
(438, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:24:07', '/bogor-raya-residential/produk.php'),
(439, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:26:07', '/bogor-raya-residential/produk.php'),
(440, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:27:01', '/bogor-raya-residential/produk.php'),
(441, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:27:48', '/bogor-raya-residential/produk.php'),
(442, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:29:33', '/bogor-raya-residential/produk.php'),
(443, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 08:34:15', '/bogor-raya-residential/produk.php'),
(444, '10.1.2.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 10:29:32', '/index.php'),
(445, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-14 08:24:40', '/bogor-raya-residential/index.php'),
(446, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-14 08:26:47', '/bogor-raya-residential/index.php'),
(447, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-14 08:26:49', '/bogor-raya-residential/about.php'),
(448, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-14 08:27:02', '/bogor-raya-residential/index.php'),
(449, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-14 08:27:05', '/bogor-raya-residential/about.php'),
(450, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-14 08:27:18', '/bogor-raya-residential/np.php'),
(451, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-14 08:27:30', '/bogor-raya-residential/produk.php'),
(452, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-15 04:16:56', '/bogor-raya-residential/index.php'),
(453, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-15 05:31:53', '/bogor-raya-residential/index.php'),
(454, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-15 08:40:59', '/bogor-raya-residential/index.php'),
(455, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-15 08:41:07', '/bogor-raya-residential/np.php'),
(456, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-15 08:41:11', '/bogor-raya-residential/produk.php'),
(457, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-15 10:14:56', '/bogor-raya-residential/contact.php'),
(458, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-15 10:38:15', '/bogor-raya-residential/index.php'),
(459, '10.1.2.244', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-15 10:38:53', '/index.php'),
(460, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-15 10:57:25', '/index.php'),
(461, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-15 11:00:25', '/index.php'),
(462, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-15 11:00:28', '/produk.php'),
(463, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-15 11:00:35', '/contact.php'),
(464, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-16 06:23:53', '/bogor-raya-residential/index.php'),
(465, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-16 10:50:56', '/index.php'),
(466, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-17 04:13:23', '/bogor-raya-residential/index.php'),
(467, '10.1.2.234', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-17 10:52:51', '/index.php'),
(468, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-17 10:56:18', '/index.php'),
(469, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-21 10:31:50', '/bogor-raya-residential/index.php'),
(470, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-22 04:17:33', '/bogor-raya-residential/index.php'),
(471, '10.1.2.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-22 10:46:16', '/index.php'),
(472, '10.1.2.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-22 10:46:30', '/produk.php'),
(473, '10.1.2.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-22 10:46:35', '/index.php'),
(474, '10.1.2.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-22 10:46:36', '/about.php'),
(475, '10.1.2.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-22 10:46:48', '/about.php'),
(476, '10.1.2.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-22 10:47:11', '/index.php'),
(477, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-22 10:51:48', '/index.php'),
(478, '10.1.2.246', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-04-22 10:52:40', '/index.php'),
(479, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-23 10:22:27', '/bogor-raya-residential/index.php'),
(480, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-23 10:23:35', '/bogor-raya-residential/produk.php'),
(481, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-24 04:14:22', '/bogor-raya-residential/index.php'),
(482, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-24 04:14:40', '/bogor-raya-residential/contact.php'),
(483, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-24 06:26:59', '/bogor-raya-residential/index.php'),
(484, '10.1.2.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-24 10:53:51', '/index.php'),
(485, '10.1.2.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-28 09:06:14', '/index.php'),
(486, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-29 04:14:03', '/bogor-raya-residential/index.php'),
(487, '10.1.2.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-29 10:41:49', '/index.php'),
(488, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-29 10:48:46', '/bogor-raya-residential/index.php'),
(489, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-29 10:49:21', '/bogor-raya-residential/index.php'),
(490, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-30 04:16:52', '/bogor-raya-residential/index.php'),
(491, '10.1.2.165', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-30 07:01:27', '/index.php'),
(492, '10.1.2.166', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', '2025-04-30 07:07:46', '/index.php/phpmyadmin'),
(493, '10.1.2.165', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-30 10:50:07', '/index.php'),
(494, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-05 04:21:47', '/bogor-raya-residential/index.php'),
(495, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-05 04:21:54', '/bogor-raya-residential/about.php'),
(496, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-05 04:21:57', '/bogor-raya-residential/np.php'),
(497, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-05 04:21:58', '/bogor-raya-residential/produk.php'),
(498, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-05 04:23:12', '/bogor-raya-residential/contact.php'),
(499, '10.1.2.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-05 11:02:38', '/index.php'),
(500, '10.1.2.248', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-05 11:02:39', '/index.php/bogor-raya-residential/'),
(501, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 16:55:54', '/bogor-raya-residential/index.php'),
(502, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 16:57:05', '/bogor-raya-residential/index.php'),
(503, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 16:57:09', '/bogor-raya-residential/about.php'),
(504, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 16:57:17', '/bogor-raya-residential/np.php'),
(505, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 16:57:22', '/bogor-raya-residential/produk.php'),
(506, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 17:12:34', '/bogor-raya-residential/index.php'),
(507, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 17:12:44', '/bogor-raya-residential/np.php'),
(508, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 17:13:23', '/bogor-raya-residential/about.php'),
(509, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 17:13:30', '/bogor-raya-residential/np.php'),
(510, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 17:13:36', '/bogor-raya-residential/produk.php'),
(511, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 17:15:01', '/bogor-raya-residential/about.php'),
(512, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 17:15:02', '/bogor-raya-residential/np.php'),
(513, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 17:15:30', '/bogor-raya-residential/contact.php'),
(514, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 18:45:13', '/bogor-raya-residential/index.php'),
(515, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 18:45:25', '/bogor-raya-residential/index.php'),
(516, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-12 18:45:27', '/bogor-raya-residential/index.php'),
(517, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 04:17:35', '/bogor-raya-residential/index.php'),
(518, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 04:17:35', '/bogor-raya-residential/index.php'),
(519, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 04:25:49', '/bogor-raya-residential/about.php'),
(520, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 04:27:34', '/bogor-raya-residential/np.php'),
(521, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 04:27:37', '/bogor-raya-residential/produk.php'),
(522, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 04:29:46', '/bogor-raya-residential/about.php'),
(523, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 04:32:38', '/bogor-raya-residential/np.php'),
(524, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 04:34:11', '/bogor-raya-residential/produk.php'),
(525, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 04:35:48', '/bogor-raya-residential/contact.php'),
(526, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 17:50:24', '/bogor-raya-residential/index.php'),
(527, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 17:50:28', '/bogor-raya-residential/about.php'),
(528, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 17:50:33', '/bogor-raya-residential/np.php'),
(529, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 17:51:02', '/bogor-raya-residential/np.php'),
(530, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 17:51:08', '/bogor-raya-residential/produk.php'),
(531, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 17:51:14', '/bogor-raya-residential/produk.php'),
(532, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 17:51:33', '/bogor-raya-residential/produk.php'),
(533, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 17:52:21', '/bogor-raya-residential/produk.php'),
(534, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 17:52:31', '/bogor-raya-residential/produk.php'),
(535, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 17:53:36', '/bogor-raya-residential/produk.php');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_daily_visitors`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_daily_visitors` (
`visit_date` date
,`total_visitors` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_total_news`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_total_news` (
`jumlah_news` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_total_pemesanan`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_total_pemesanan` (
`jumlah_pemesan` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_total_produk`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_total_produk` (
`jumlah_produk` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_total_users`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_total_users` (
`jumlah_users` bigint(21)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `view_daily_visitors`
--
DROP TABLE IF EXISTS `view_daily_visitors`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_daily_visitors`  AS SELECT cast(`tbl_visitors`.`visit_time` as date) AS `visit_date`, count(distinct `tbl_visitors`.`ip_address`) AS `total_visitors` FROM `tbl_visitors` GROUP BY cast(`tbl_visitors`.`visit_time` as date)  ;

-- --------------------------------------------------------

--
-- Struktur untuk view `view_total_news`
--
DROP TABLE IF EXISTS `view_total_news`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_total_news`  AS SELECT count(`tbl_news`.`id`) AS `jumlah_news` FROM `tbl_news``tbl_news`  ;

-- --------------------------------------------------------

--
-- Struktur untuk view `view_total_pemesanan`
--
DROP TABLE IF EXISTS `view_total_pemesanan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_total_pemesanan`  AS SELECT count(`tbl_contacts`.`id`) AS `jumlah_pemesan` FROM `tbl_contacts``tbl_contacts`  ;

-- --------------------------------------------------------

--
-- Struktur untuk view `view_total_produk`
--
DROP TABLE IF EXISTS `view_total_produk`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_total_produk`  AS SELECT count(`tbl_produk`.`id`) AS `jumlah_produk` FROM `tbl_produk``tbl_produk`  ;

-- --------------------------------------------------------

--
-- Struktur untuk view `view_total_users`
--
DROP TABLE IF EXISTS `view_total_users`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_total_users`  AS SELECT count(`tbl_users`.`id`) AS `jumlah_users` FROM `tbl_users``tbl_users`  ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tbl_contacts`
--
ALTER TABLE `tbl_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tbl_follow_up`
--
ALTER TABLE `tbl_follow_up`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_sales` (`id_sales`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_unit` (`id_unit`);

--
-- Indeks untuk tabel `tbl_management`
--
ALTER TABLE `tbl_management`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tbl_news`
--
ALTER TABLE `tbl_news`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tbl_permissions`
--
ALTER TABLE `tbl_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `tbl_produk`
--
ALTER TABLE `tbl_produk`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tbl_sales`
--
ALTER TABLE `tbl_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_sumber` (`id_sumber`);

--
-- Indeks untuk tabel `tbl_sumber`
--
ALTER TABLE `tbl_sumber`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `tbl_testimoni`
--
ALTER TABLE `tbl_testimoni`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tbl_unit`
--
ALTER TABLE `tbl_unit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tbl_user_notifications`
--
ALTER TABLE `tbl_user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_notifications` (`user_id`);

--
-- Indeks untuk tabel `tbl_visitors`
--
ALTER TABLE `tbl_visitors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tbl_contacts`
--
ALTER TABLE `tbl_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `tbl_follow_up`
--
ALTER TABLE `tbl_follow_up`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT untuk tabel `tbl_management`
--
ALTER TABLE `tbl_management`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tbl_news`
--
ALTER TABLE `tbl_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `tbl_permissions`
--
ALTER TABLE `tbl_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tbl_produk`
--
ALTER TABLE `tbl_produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT untuk tabel `tbl_sales`
--
ALTER TABLE `tbl_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `tbl_sumber`
--
ALTER TABLE `tbl_sumber`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tbl_testimoni`
--
ALTER TABLE `tbl_testimoni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tbl_unit`
--
ALTER TABLE `tbl_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT untuk tabel `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT untuk tabel `tbl_user_notifications`
--
ALTER TABLE `tbl_user_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1911;

--
-- AUTO_INCREMENT untuk tabel `tbl_visitors`
--
ALTER TABLE `tbl_visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=536;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tbl_follow_up`
--
ALTER TABLE `tbl_follow_up`
  ADD CONSTRAINT `tbl_follow_up_ibfk_1` FOREIGN KEY (`id_sales`) REFERENCES `tbl_sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_follow_up_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tbl_follow_up_ibfk_3` FOREIGN KEY (`id_unit`) REFERENCES `tbl_unit` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `tbl_permissions`
--
ALTER TABLE `tbl_permissions`
  ADD CONSTRAINT `tbl_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_sales`
--
ALTER TABLE `tbl_sales`
  ADD CONSTRAINT `tbl_sales_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tbl_sales_ibfk_2` FOREIGN KEY (`id_sumber`) REFERENCES `tbl_sumber` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `tbl_sumber`
--
ALTER TABLE `tbl_sumber`
  ADD CONSTRAINT `tbl_sumber_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tbl_users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `tbl_unit`
--
ALTER TABLE `tbl_unit`
  ADD CONSTRAINT `tbl_unit_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `tbl_produk` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tbl_user_notifications`
--
ALTER TABLE `tbl_user_notifications`
  ADD CONSTRAINT `fk_user_notifications` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
