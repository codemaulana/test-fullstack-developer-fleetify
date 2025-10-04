-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Okt 2025 pada 12.41
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fleetify_test`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `attendance_id` varchar(100) NOT NULL,
  `clock_in` timestamp NULL DEFAULT NULL,
  `clock_out` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `attendance_id`, `clock_in`, `clock_out`, `created_at`, `updated_at`) VALUES
(14, 'KRYWN03', 'ATT-KRYWN03-20251004', '2025-10-04 04:43:42', '2025-10-04 04:44:02', '2025-10-04 04:43:42', '2025-10-04 04:44:02'),
(15, 'FIN-004', 'ATT-FIN-004-20251004', '2025-10-04 05:30:47', '2025-10-04 05:31:47', '2025-10-04 05:30:47', '2025-10-04 05:31:47'),
(16, 'HR-003', 'ATT-HR-003-20251004', '2025-10-04 05:31:21', '2025-10-04 05:31:28', '2025-10-04 05:31:21', '2025-10-04 05:31:28'),
(17, 'HR-002', 'ATT-HR-002-20251004', '2025-10-04 05:35:25', '2025-10-04 06:11:10', '2025-10-04 05:35:25', '2025-10-04 06:11:10'),
(18, 'TI-001', 'ATT-TI-001-20251004', '2025-10-04 05:38:46', '2025-10-04 05:39:13', '2025-10-04 05:38:46', '2025-10-04 05:39:13'),
(19, 'OPS-003', 'ATT-OPS-003-20251004', '2025-10-04 06:16:32', '2025-10-04 06:17:24', '2025-10-04 06:16:32', '2025-10-04 06:17:24'),
(20, 'RND-002', 'ATT-RND-002-20251004', '2025-10-04 06:26:19', '2025-10-04 06:26:45', '2025-10-04 06:26:19', '2025-10-04 06:26:45'),
(21, 'SALES-004', 'ATT-SALES-004-20251004', '2025-10-03 07:25:58', '2025-10-04 07:26:07', '2025-10-04 07:25:58', '2025-10-04 09:54:21'),
(22, 'SALES-003', 'ATT-SALES-003-20251004', '2025-10-04 09:52:55', '2025-10-04 09:53:03', '2025-10-04 09:52:55', '2025-10-04 09:53:03'),
(23, 'FIN-003', 'ATT-FIN-003-20251004', '2025-10-04 10:30:37', '2025-10-04 10:30:45', '2025-10-04 10:30:37', '2025-10-04 10:30:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `attendance_history`
--

CREATE TABLE `attendance_history` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `attendance_id` varchar(100) NOT NULL,
  `date_attendance` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attendance_type` tinyint(1) DEFAULT NULL COMMENT '1: In, 2: Out',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `attendance_history`
--

INSERT INTO `attendance_history` (`id`, `employee_id`, `attendance_id`, `date_attendance`, `attendance_type`, `description`, `created_at`, `updated_at`) VALUES
(12, 'KRYWN03', 'ATT-KRYWN03-20251004', '2025-10-04 04:43:42', 1, '', '2025-10-04 04:43:42', '2025-10-04 04:43:42'),
(13, 'KRYWN03', 'ATT-KRYWN03-20251004', '2025-10-04 04:44:02', 2, '', '2025-10-04 04:44:02', '2025-10-04 04:44:02'),
(14, 'FIN-004', 'ATT-FIN-004-20251004', '2025-10-04 05:30:47', 1, '', '2025-10-04 05:30:47', '2025-10-04 05:30:47'),
(15, 'HR-003', 'ATT-HR-003-20251004', '2025-10-04 05:31:21', 1, '', '2025-10-04 05:31:21', '2025-10-04 05:31:21'),
(16, 'HR-003', 'ATT-HR-003-20251004', '2025-10-04 05:31:28', 2, '', '2025-10-04 05:31:28', '2025-10-04 05:31:28'),
(17, 'FIN-004', 'ATT-FIN-004-20251004', '2025-10-04 05:31:47', 2, '', '2025-10-04 05:31:47', '2025-10-04 05:31:47'),
(18, 'HR-002', 'ATT-HR-002-20251004', '2025-10-04 05:35:25', 1, '', '2025-10-04 05:35:25', '2025-10-04 05:35:25'),
(19, 'TI-001', 'ATT-TI-001-20251004', '2025-10-04 05:38:46', 1, '', '2025-10-04 05:38:46', '2025-10-04 05:38:46'),
(20, 'TI-001', 'ATT-TI-001-20251004', '2025-10-04 05:39:13', 2, '', '2025-10-04 05:39:13', '2025-10-04 05:39:13'),
(21, 'HR-002', 'ATT-HR-002-20251004', '2025-10-04 06:11:10', 2, '', '2025-10-04 06:11:10', '2025-10-04 06:11:10'),
(22, 'OPS-003', 'ATT-OPS-003-20251004', '2025-10-04 06:16:32', 1, '', '2025-10-04 06:16:32', '2025-10-04 06:16:32'),
(23, 'OPS-003', 'ATT-OPS-003-20251004', '2025-10-04 06:17:24', 2, '', '2025-10-04 06:17:24', '2025-10-04 06:17:24'),
(24, 'RND-002', 'ATT-RND-002-20251004', '2025-10-04 06:26:19', 1, '', '2025-10-04 06:26:19', '2025-10-04 06:26:19'),
(25, 'RND-002', 'ATT-RND-002-20251004', '2025-10-04 06:26:45', 2, '', '2025-10-04 06:26:45', '2025-10-04 06:26:45'),
(26, 'SALES-004', 'ATT-SALES-004-20251004', '2025-10-04 07:25:58', 1, '', '2025-10-04 07:25:58', '2025-10-04 07:25:58'),
(27, 'SALES-004', 'ATT-SALES-004-20251004', '2025-10-04 07:26:07', 2, '', '2025-10-04 07:26:07', '2025-10-04 07:26:07'),
(28, 'SALES-003', 'ATT-SALES-003-20251004', '2025-10-04 09:52:55', 1, '', '2025-10-04 09:52:55', '2025-10-04 09:52:55'),
(29, 'SALES-003', 'ATT-SALES-003-20251004', '2025-10-04 09:53:03', 2, '', '2025-10-04 09:53:03', '2025-10-04 09:53:03'),
(30, 'FIN-003', 'ATT-FIN-003-20251004', '2025-10-04 10:30:37', 1, '', '2025-10-04 10:30:37', '2025-10-04 10:30:37'),
(31, 'FIN-003', 'ATT-FIN-003-20251004', '2025-10-04 10:30:45', 2, '', '2025-10-04 10:30:45', '2025-10-04 10:30:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `departement`
--

CREATE TABLE `departement` (
  `id` int(11) NOT NULL,
  `departement_name` varchar(255) NOT NULL,
  `max_clock_in_time` time NOT NULL,
  `max_clock_out_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `departement`
--

INSERT INTO `departement` (`id`, `departement_name`, `max_clock_in_time`, `max_clock_out_time`) VALUES
(1, 'Teknologi Informasi ( TI )', '09:30:00', '17:00:00'),
(3, 'SDM', '08:00:00', '17:00:00'),
(5, 'Riset dan Pengembangan (R&D)', '08:00:00', '17:00:00'),
(6, 'Keuangan', '07:00:00', '16:30:00'),
(7, 'Pemasaran dan Penjualan', '07:45:00', '18:00:00'),
(8, 'Operasional/Produk', '08:00:00', '17:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `departement_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `employee`
--

INSERT INTO `employee` (`id`, `employee_id`, `departement_id`, `name`, `address`, `created_at`, `updated_at`) VALUES
(5, 'KRYWN03', 3, 'Maul', 'Depok', '2025-10-04 04:41:48', '2025-10-04 04:41:48'),
(6, 'TI-001', 1, 'Muhammad Nur Maulana', 'Jalan Rancho Indah, Jakarta', '2025-10-04 05:02:07', '2025-10-04 05:02:07'),
(7, 'TI-002', 1, 'Bambang Susilo', 'Jalan Server No. 22', '2025-10-04 05:02:23', '2025-10-04 05:02:23'),
(8, 'HR-002', 3, 'Dewi Anggraini', 'Jalan Personalia No. 3', '2025-10-04 05:02:36', '2025-10-04 05:02:36'),
(9, 'HR-003', 3, 'Eko Prasetyo', 'Jalan Rekrutmen No. 7', '2025-10-04 05:02:51', '2025-10-04 05:02:51'),
(11, 'RND-002', 5, 'Gatot Subroto', 'Jalan Penelitian No. 9', '2025-10-04 05:03:10', '2025-10-04 05:03:10'),
(12, 'RND-003', 5, 'Hesti Purwadinata', 'Jalan Eksperimen No. 4', '2025-10-04 05:03:19', '2025-10-04 05:03:19'),
(13, 'FIN-002', 6, 'Indra Hermawan', 'Jalan Neraca No. 11', '2025-10-04 05:03:26', '2025-10-04 05:03:26'),
(14, 'FIN-003', 6, 'Joko Anwar', 'Jalan Laba No. 21', '2025-10-04 05:03:35', '2025-10-04 05:03:35'),
(15, 'FIN-004', 6, 'Lina Marpaung', 'Jalan Aset No. 30', '2025-10-04 05:03:43', '2025-10-04 05:03:43'),
(16, 'SALES-002', 7, 'Mira Santika', 'Jalan Pasar No. 77', '2025-10-04 05:03:50', '2025-10-04 05:03:50'),
(17, 'SALES-003', 7, 'Nadia Wijaya', 'Jalan Pelanggan No. 101', '2025-10-04 05:03:57', '2025-10-04 05:03:57'),
(18, 'SALES-004', 7, 'Oscar Mahendra', 'Jalan Iklan No. 20', '2025-10-04 05:04:04', '2025-10-04 05:04:04'),
(20, 'OPS-003', 8, 'Rian Ardianto', 'Jalan Logistik No. 12B', '2025-10-04 05:04:23', '2025-10-04 05:04:23'),
(23, 'OPS-006', 8, 'Umar Bakri', 'Jalan Efisiensi No. 8', '2025-10-04 05:04:45', '2025-10-04 05:04:45');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_id` (`attendance_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indeks untuk tabel `attendance_history`
--
ALTER TABLE `attendance_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `attendance_id` (`attendance_id`);

--
-- Indeks untuk tabel `departement`
--
ALTER TABLE `departement`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `departement_id` (`departement_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `attendance_history`
--
ALTER TABLE `attendance_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `departement`
--
ALTER TABLE `departement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`);

--
-- Ketidakleluasaan untuk tabel `attendance_history`
--
ALTER TABLE `attendance_history`
  ADD CONSTRAINT `attendance_history_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`),
  ADD CONSTRAINT `attendance_history_ibfk_2` FOREIGN KEY (`attendance_id`) REFERENCES `attendance` (`attendance_id`);

--
-- Ketidakleluasaan untuk tabel `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`departement_id`) REFERENCES `departement` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
