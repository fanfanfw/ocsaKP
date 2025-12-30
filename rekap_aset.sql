-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 30, 2025 at 08:34 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rekap_aset`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `nama_aset` varchar(255) NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Tersedia',
  `tahun` int(11) DEFAULT NULL,
  `harga` bigint(20) DEFAULT NULL,
  `jumlah` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `nama_aset`, `kategori`, `status`, `tahun`, `harga`, `jumlah`) VALUES
(1, 'airplane', 'fischer', 'Tersedia', 0, 0, 5),
(3, 'dino T-Rex', 'Huna', 'Tersedia', 0, 0, 3),
(11, 'mini soccer', 'Huna', 'Terjadwal', 0, 0, 2),
(13, 'truck', 'fischer', 'Tersedia', 0, 0, 1),
(14, 'Duplo Creative', 'Lego', 'Tersedia', 0, 0, 1),
(15, 'F1 Supercar', 'Lego', 'Tersedia', 0, 0, 5);

-- --------------------------------------------------------

--
-- Table structure for table `asset_parts`
--

CREATE TABLE `asset_parts` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `nama_part` varchar(100) NOT NULL,
  `kondisi` varchar(50) DEFAULT NULL,
  `jumlah` int(11) DEFAULT 1,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_parts`
--

INSERT INTO `asset_parts` (`id`, `asset_id`, `nama_part`, `kondisi`, `jumlah`, `keterangan`) VALUES
(1, 11, 'ban', 'baik', 4, '');

-- --------------------------------------------------------

--
-- Table structure for table `asset_status_log`
--

CREATE TABLE `asset_status_log` (
  `asset_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`id`, `asset_id`, `hari`, `jam_mulai`, `jam_selesai`, `keterangan`) VALUES
(3, 1, 'Senin', '13:00:00', '14:00:00', 'SD BPK'),
(4, 3, 'Senin', '13:00:00', '14:00:00', 'SD BPK'),
(5, 11, 'Senin', '12:30:00', '13:30:00', 'SD Taruna Bhakti'),
(6, 13, 'Senin', '12:30:00', '13:30:00', 'SD Taruna Bhakti'),
(7, 15, 'Selasa', '08:00:00', '11:00:00', 'TK BPK'),
(8, 3, 'Selasa', '08:00:00', '11:00:00', 'TK BPK'),
(9, 11, 'Selasa', '13:30:00', '14:30:00', 'SD BPK');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_otomatis`
--

CREATE TABLE `jadwal_otomatis` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `status` enum('Terjadwal','Berjalan','Selesai') DEFAULT 'Terjadwal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `tanggal_pinjam` datetime NOT NULL,
  `tanggal_kembali` datetime DEFAULT NULL,
  `status` enum('Dipinjam','Dikembalikan') DEFAULT 'Dipinjam',
  `bukti_kembali` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `user_id`, `asset_id`, `tanggal_pinjam`, `tanggal_kembali`, `status`, `bukti_kembali`) VALUES
(3, 5, 11, '2025-12-22 12:02:12', '2025-12-22 12:03:35', 'Dikembalikan', '1766379815_mobl_2.png'),
(4, 5, 11, '2025-12-22 12:08:20', NULL, 'Dipinjam', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `tanggal` date NOT NULL,
  `part` varchar(100) DEFAULT NULL,
  `jenis_kerusakan` varchar(100) DEFAULT NULL,
  `tingkat` enum('Ringan','Sedang','Berat') DEFAULT NULL,
  `tindakan` text DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`id`, `asset_id`, `deskripsi`, `tanggal`, `part`, `jenis_kerusakan`, `tingkat`, `tindakan`, `tanggal_selesai`) VALUES
(6, 11, 'partnya banyak yang hilang (Selesai)', '2025-12-19', NULL, NULL, NULL, NULL, NULL),
(7, 11, '', '2025-12-19', 'ban', 'robek', 'Berat', 'diganti', '2025-12-19');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) DEFAULT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `asset_id`, `hari`, `jam_mulai`, `jam_selesai`) VALUES
(22, 1, 'Minggu', '13:30:00', '14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tentor`
--

CREATE TABLE `tentor` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','tentor') NOT NULL DEFAULT 'tentor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'Administrator', '173847d61d01f5e9', 'admin', '2025-10-09 23:29:31'),
(2, 'tentor', 'Tentor User', '314f8d5a31919520', 'tentor', '2025-10-09 23:29:31'),
(3, 'ocsa', 'ocsa', '$2y$10$NZCZ8Fu/CSq8MAgEOB2SLeKd4MHq2yEOXBRKrxs.P7A2OL2rgUMVK', 'admin', '2025-10-09 23:30:02'),
(4, 'rizky', 'rizky', '$2y$10$6j1mHIfqo/ypq9oljUXNkOgkpdUZz8yDQ0urh.d1dABMVJ5IdBRG2', 'tentor', '2025-10-09 23:30:46'),
(5, 'farhan', 'farhan', '$2y$10$iVQRcLQIFQyA0/xeObxO/OEk4w3W3sFC54pro1WzjmDIXfDFrTKgK', 'tentor', '2025-12-03 16:40:00'),
(6, 'dede', 'dede', '$2y$10$Vt3GOFu.rWUIWB7SfDmNVeNzGl9bPAllvM81x40M9qOnUTxMN.xxy', 'tentor', '2025-12-28 05:40:07'),
(7, 'koko', 'koko', '$2y$10$riwODrFiB2Fs.HNCTFaxN.KxDmw6HB/3fl7Y.47cA6m4TMx8XtieO', 'tentor', '2025-12-28 05:46:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `asset_parts`
--
ALTER TABLE `asset_parts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `jadwal_otomatis`
--
ALTER TABLE `jadwal_otomatis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tentor`
--
ALTER TABLE `tentor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `asset_parts`
--
ALTER TABLE `asset_parts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `jadwal_otomatis`
--
ALTER TABLE `jadwal_otomatis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tentor`
--
ALTER TABLE `tentor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `asset_parts`
--
ALTER TABLE `asset_parts`
  ADD CONSTRAINT `asset_parts_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jadwal_otomatis`
--
ALTER TABLE `jadwal_otomatis`
  ADD CONSTRAINT `jadwal_otomatis_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`);

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD CONSTRAINT `maintenance_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
