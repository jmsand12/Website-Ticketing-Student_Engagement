-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2025 at 07:30 AM
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
-- Database: `student_engagement`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `laporan_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `old_status` enum('pending','assign','resolved','done') DEFAULT NULL,
  `new_status` enum('pending','assign','resolved','done') DEFAULT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `laporan_id`, `action`, `old_status`, `new_status`, `changed_by`, `created_at`) VALUES
(1, 1, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 04:24:29'),
(2, 2, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 04:24:37'),
(3, 3, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 04:24:45'),
(4, 3, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-29 04:34:54'),
(5, 3, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-29 04:35:02'),
(6, 3, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-29 04:43:30'),
(7, 3, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-29 04:43:36'),
(8, 3, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-29 04:45:22'),
(9, 3, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-29 04:45:54'),
(10, 3, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-29 04:47:51'),
(11, 3, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-29 04:51:07'),
(12, 2, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-29 04:51:29'),
(13, 1, 'Status Changed', 'pending', 'assign', NULL, '2025-10-29 04:54:55'),
(14, 1, 'Forwarded/Updated', 'pending', 'assign', 3, '2025-10-29 04:54:55'),
(15, 1, 'Forwarded/Updated', 'assign', 'assign', 3, '2025-10-29 04:55:10'),
(16, 1, 'Forwarded/Updated', 'assign', 'assign', 3, '2025-10-29 04:55:28'),
(17, 4, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 04:55:41'),
(18, 5, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 04:55:50'),
(19, 5, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-29 04:56:25'),
(20, 2, 'Forwarded/Updated', 'pending', 'pending', 4, '2025-10-29 04:57:44'),
(21, 2, 'Status Changed', 'pending', 'assign', NULL, '2025-10-29 04:57:58'),
(22, 2, 'Forwarded/Updated', 'pending', 'assign', 4, '2025-10-29 04:57:58'),
(23, 2, 'Status Changed', 'assign', 'resolved', NULL, '2025-10-29 04:58:50'),
(24, 2, 'Forwarded/Updated', 'assign', 'resolved', 4, '2025-10-29 04:58:50'),
(25, 2, 'Forwarded/Updated', 'resolved', 'resolved', 4, '2025-10-29 04:59:00'),
(26, 2, 'Forwarded/Updated', 'resolved', 'resolved', 4, '2025-10-29 04:59:07'),
(27, 2, 'Status Changed', 'resolved', 'done', NULL, '2025-10-29 04:59:13'),
(28, 2, 'Forwarded/Updated', 'resolved', 'done', 4, '2025-10-29 04:59:13'),
(29, 2, 'Status Changed', 'done', 'resolved', NULL, '2025-10-29 04:59:21'),
(30, 2, 'Forwarded/Updated', 'done', 'resolved', 4, '2025-10-29 04:59:21'),
(31, 4, 'Status Changed', 'pending', 'assign', NULL, '2025-10-29 04:59:39'),
(32, 4, 'Forwarded/Updated', 'pending', 'assign', 4, '2025-10-29 04:59:39'),
(33, 4, 'Forwarded/Updated', 'assign', 'assign', 4, '2025-10-29 04:59:52'),
(34, 4, 'Forwarded/Updated', 'assign', 'assign', 4, '2025-10-29 05:00:01'),
(35, 4, 'Status Changed', 'assign', 'pending', NULL, '2025-10-29 05:01:22'),
(36, 4, 'Forwarded/Updated', 'assign', 'pending', 4, '2025-10-29 05:01:22'),
(37, 4, 'Status Changed', 'pending', 'assign', NULL, '2025-10-29 05:01:34'),
(38, 4, 'Forwarded/Updated', 'pending', 'assign', 4, '2025-10-29 05:01:34'),
(39, 4, 'Forwarded/Updated', 'assign', 'assign', 4, '2025-10-29 05:01:41'),
(40, 4, 'Status Changed', 'assign', 'resolved', NULL, '2025-10-29 05:01:52'),
(41, 4, 'Forwarded/Updated', 'assign', 'resolved', 4, '2025-10-29 05:01:52'),
(42, 4, 'Status Changed', 'resolved', 'pending', NULL, '2025-10-29 05:02:09'),
(43, 4, 'Forwarded/Updated', 'resolved', 'pending', 4, '2025-10-29 05:02:09'),
(44, 2, 'Status Changed', 'resolved', 'pending', NULL, '2025-10-29 05:03:28'),
(45, 2, 'Forwarded/Updated', 'resolved', 'pending', 4, '2025-10-29 05:03:28'),
(46, 2, 'Status Changed', 'pending', 'assign', NULL, '2025-10-29 05:03:35'),
(47, 2, 'Forwarded/Updated', 'pending', 'assign', 4, '2025-10-29 05:03:35'),
(48, 4, 'Forwarded/Updated', 'pending', 'pending', 4, '2025-10-29 05:06:48'),
(49, 4, 'Forwarded/Updated', 'pending', 'pending', 4, '2025-10-29 05:06:57'),
(50, 4, 'Status Changed', 'pending', 'resolved', NULL, '2025-10-29 05:07:13'),
(51, 4, 'Forwarded/Updated', 'pending', 'resolved', 4, '2025-10-29 05:07:13'),
(52, 4, 'Forwarded/Updated', 'resolved', 'resolved', 4, '2025-10-29 07:08:29'),
(55, 8, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 07:09:09'),
(56, 8, 'Status Changed', 'pending', 'assign', NULL, '2025-10-29 07:09:32'),
(57, 8, 'Forwarded/Updated', 'pending', 'assign', 3, '2025-10-29 07:09:32'),
(58, 9, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 07:18:38'),
(59, 10, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 07:18:49'),
(60, 11, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 07:18:58'),
(61, 12, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 07:19:09'),
(64, 15, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 07:21:30'),
(65, 16, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 07:22:26'),
(66, 16, 'Status Changed', 'pending', 'assign', NULL, '2025-10-29 07:22:47'),
(67, 16, 'Forwarded/Updated', 'pending', 'assign', 3, '2025-10-29 07:22:47'),
(68, 16, 'Status Changed', 'assign', 'resolved', NULL, '2025-10-29 07:22:58'),
(69, 16, 'Forwarded/Updated', 'assign', 'resolved', 3, '2025-10-29 07:22:58'),
(70, 16, 'Status Changed', 'resolved', 'done', NULL, '2025-10-29 07:23:08'),
(71, 16, 'Forwarded/Updated', 'resolved', 'done', 3, '2025-10-29 07:23:08'),
(72, 17, 'Report Submitted', NULL, 'pending', NULL, '2025-10-29 07:25:06'),
(73, 3, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-31 02:42:50'),
(74, 3, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-31 02:42:54'),
(75, 17, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-31 02:43:07'),
(76, 15, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-31 02:43:20'),
(79, 18, 'Report Submitted', NULL, 'pending', NULL, '2025-10-31 03:13:55'),
(80, 19, 'Report Submitted', NULL, 'pending', NULL, '2025-10-31 03:17:25'),
(81, 19, 'Status Changed', 'pending', 'assign', NULL, '2025-10-31 03:17:45'),
(82, 19, 'Forwarded/Updated', 'pending', 'assign', 3, '2025-10-31 03:17:45'),
(83, 19, 'Forwarded/Updated', 'assign', 'assign', 3, '2025-10-31 03:18:05'),
(84, 19, 'Status Changed', 'assign', 'resolved', NULL, '2025-10-31 03:18:17'),
(85, 19, 'Forwarded/Updated', 'assign', 'resolved', 3, '2025-10-31 03:18:17'),
(86, 20, 'Report Submitted', NULL, 'pending', NULL, '2025-10-31 03:18:46'),
(87, 21, 'Report Submitted', NULL, 'pending', NULL, '2025-10-31 03:19:01'),
(88, 22, 'Report Submitted', NULL, 'pending', NULL, '2025-10-31 03:19:19'),
(89, 19, 'Forwarded/Updated', 'resolved', 'resolved', 3, '2025-10-31 03:19:48'),
(90, 19, 'Forwarded/Updated', 'resolved', 'resolved', 3, '2025-10-31 03:19:56'),
(91, 22, 'Forwarded/Updated', 'pending', 'pending', 3, '2025-10-31 03:22:45');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id` int(11) NOT NULL,
  `ticket_code` varchar(20) DEFAULT NULL,
  `nim` varchar(20) NOT NULL,
  `log_type` enum('harian','khusus') NOT NULL,
  `report_type` varchar(100) NOT NULL,
  `sub_type` varchar(50) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `division` enum('studentservice','studentsupport','studentdevelopment') DEFAULT NULL,
  `lampiran` varchar(255) DEFAULT NULL,
  `status` enum('pending','assign','resolved','done') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id`, `ticket_code`, `nim`, `log_type`, `report_type`, `sub_type`, `deskripsi`, `division`, `lampiran`, `status`, `created_at`, `updated_at`) VALUES
(1, 'TKT-UNK-00001', '00000069612', 'harian', 'status_mahasiswa', 'cuti', 't', 'studentdevelopment', NULL, 'assign', '2025-10-29 04:24:29', '2025-10-29 04:55:28'),
(2, 'TKT-UNK-00002', '00000012345', 'khusus', 'saran_dan_masukan', '', 'r', 'studentsupport', NULL, 'assign', '2025-10-29 04:24:37', '2025-10-29 05:03:35'),
(3, 'TKT-UNK-00003', '00000054321', 'harian', 'pengambilan_toeic', '', 'a', 'studentsupport', NULL, 'pending', '2025-10-29 04:24:45', '2025-10-31 02:42:54'),
(4, 'TKT-UNK-00004', '00000069612', 'harian', 'pengambilan_legalisir', '', 'a', 'studentsupport', NULL, 'resolved', '2025-10-29 04:55:41', '2025-10-29 07:08:29'),
(5, 'TKT-UNK-00005', '00000069612', 'harian', 'pemutihan_absensi_sakit_dan_lomba', '', 'a', 'studentservice', NULL, 'pending', '2025-10-29 04:55:50', '2025-10-29 04:56:25'),
(8, 'TKT-UNK-00008', '00000012345', 'khusus', 'konseling_rujukan', '', 'z', 'studentservice', NULL, 'assign', '2025-10-29 07:09:09', '2025-10-29 07:09:32'),
(9, 'TKT-UNK-00009', '00000012345', 'khusus', 'kendala_krs', '', 'v', NULL, NULL, 'pending', '2025-10-29 07:18:38', NULL),
(10, 'TKT-UNK-00010', '00000054321', 'harian', 'cicilan_jangka_panjang', '', 'a', '', NULL, 'pending', '2025-10-29 07:18:49', NULL),
(11, 'TKT-UNK-00011', '00000012345', 'harian', 'pengambilan_legalisir', '', 'a', '', NULL, 'pending', '2025-10-29 07:18:58', NULL),
(12, 'TKT-UNK-00012', '00000054321', 'khusus', 'pemutihan/ujian_susulan_kondisi_khusus', '', 'q', '', NULL, 'pending', '2025-10-29 07:19:09', NULL),
(15, 'TKT-UNK-00015', '00000069612', 'harian', 'status_mahasiswa', 'pindah_prodi', 'a', 'studentservice', NULL, 'pending', '2025-10-29 07:21:30', '2025-10-31 02:43:20'),
(16, 'TKT-UNK-00016', '00000069612', 'harian', 'status_mahasiswa', 'masa_studi_habis', 'a', 'studentsupport', '1761722546_62b7f2360609.png', 'done', '2025-10-29 07:22:26', '2025-10-29 07:23:08'),
(17, 'TKT-UNK-00017', '00000069612', 'harian', 'status_mahasiswa', 'aktif_nim', 'balik', 'studentsupport', NULL, 'pending', '2025-10-29 07:25:06', '2025-10-31 02:43:07'),
(18, 'TKT-UNK-00018', '00000012345', 'harian', 'status_mahasiswa', 'cuti', 'a', NULL, NULL, 'pending', '2025-10-31 03:13:55', NULL),
(19, 'TKT-UNK-00019', '00000012345', 'khusus', 'saran_dan_masukan', '', 'a', 'studentsupport', NULL, 'resolved', '2025-10-31 03:17:25', '2025-10-31 03:19:56'),
(20, 'TKT-UNK-00020', '00000054321', 'harian', 'ktm', '', 'a', NULL, NULL, 'pending', '2025-10-31 03:18:46', NULL),
(21, 'TKT-UNK-00021', '00000054321', 'harian', 'status_mahasiswa', 'pindah_prodi', 'a', '', NULL, 'pending', '2025-10-31 03:19:01', NULL),
(22, 'TKT-UNK-00022', '00000069612', 'harian', 'pengambilan_legalisir', '', 'a', 'studentservice', NULL, 'pending', '2025-10-31 03:19:19', '2025-10-31 03:22:45');

--
-- Triggers `laporan`
--
DELIMITER $$
CREATE TRIGGER `after_update_laporan` AFTER UPDATE ON `laporan` FOR EACH ROW BEGIN
  IF OLD.status <> NEW.status THEN
    INSERT INTO activity_log (laporan_id, action, old_status, new_status, created_at)
    VALUES (NEW.id, 'Status Changed', OLD.status, NEW.status, NOW());
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_generate_ticket_code` BEFORE INSERT ON `laporan` FOR EACH ROW BEGIN
  DECLARE prefix VARCHAR(10);
  DECLARE new_id INT;

  SET new_id = (SELECT IFNULL(MAX(id), 0) + 1 FROM laporan);

  -- Tentukan prefix berdasarkan division
  SET prefix = CASE
    WHEN NEW.division LIKE '%service%' THEN 'TKT-SER'
    WHEN NEW.division LIKE '%support%' THEN 'TKT-SUP'
    WHEN NEW.division LIKE '%development%' THEN 'TKT-DEV'
    ELSE 'TKT-UNK'
  END;

  SET NEW.ticket_code = CONCAT(prefix, '-', LPAD(new_id, 5, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `nim` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `angkatan` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`nim`, `nama`, `jurusan`, `angkatan`) VALUES
('00000012345', 'Dummy', 'Teknik Informatika', '2021'),
('00000054321', 'Anderson', 'Teknik Komputer', '2025'),
('00000069612', 'James Andersen', 'Sistem Informasi', '2022');

-- --------------------------------------------------------

--
-- Table structure for table `report_notes`
--

CREATE TABLE `report_notes` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report_notes`
--

INSERT INTO `report_notes` (`id`, `report_id`, `note`, `created_by`, `created_at`) VALUES
(1, 3, 't', 3, '2025-10-29 04:35:02'),
(2, 3, 'a', 3, '2025-10-29 04:43:30'),
(3, 1, 'a', 3, '2025-10-29 04:54:55'),
(4, 2, 'test', 4, '2025-10-29 04:57:44'),
(5, 4, 'g', 4, '2025-10-29 05:00:01'),
(6, 4, 'a', 4, '2025-10-29 05:06:57'),
(7, 4, 'v', 4, '2025-10-29 05:07:13'),
(8, 4, 'a', 4, '2025-10-29 07:08:29'),
(9, 8, 'v', 3, '2025-10-29 07:09:32'),
(10, 16, 'a', 3, '2025-10-29 07:22:47'),
(11, 16, 'v', 3, '2025-10-29 07:22:58'),
(12, 16, 'z', 3, '2025-10-29 07:23:08'),
(13, 17, 'c', 3, '2025-10-31 02:43:07'),
(14, 15, 'z', 3, '2025-10-31 02:43:20'),
(15, 19, 'test', 3, '2025-10-31 03:17:45'),
(16, 19, 'yea', 3, '2025-10-31 03:18:05'),
(17, 19, 'aman', 3, '2025-10-31 03:18:17'),
(18, 22, 'c', 3, '2025-10-31 03:22:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('superadmin','admin','studentservice','studentsupport','studentdevelopment') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `full_name`, `role`, `created_at`, `updated_at`) VALUES
(1, 'superadmin@umn.ac.id', '$2y$10$.yETaLHbS4.FuNMHEJMx5OYy6prASIN9DnsrqmX/R0QcCm/AR./Ge', 'Lala', 'superadmin', '2025-09-10 09:34:35', '2025-09-10 09:39:03'),
(2, 'admin@umn.ac.id', '$2y$10$MdgKPzF0fIBfp8jjp1EFWexmhtO2WVdntITqyGMiPoUvjY4EToXni', 'Carlos', 'admin', '2025-09-10 09:34:35', '2025-09-10 09:39:25'),
(3, 'studentservice@umn.ac.id', '$2y$10$fHT93tQE3RIG7CPDm5ZxG.Wkf6dDfR3WkoWLbkVWqB8lqPmw10aja', 'James', 'studentservice', '2025-09-10 09:35:59', '2025-09-10 09:39:41'),
(4, 'studentsupport@umn.ac.id', '$2y$10$DbP9KOqzCSzlQF5GZLMzMu3cew6Lkl5SCtB3df6etiOy.MuA2GSKW', 'Elsa', 'studentsupport', '2025-09-10 09:35:59', '2025-09-10 09:39:53'),
(5, 'studentdevelopment@umn.ac.id', '$2y$10$zp3Ib45mTvD9YqUlvAGhn.RHYALuSoZcHrroZzYZIn/0Fgeuf/drG', 'Loki', 'studentdevelopment', '2025-09-10 09:36:32', '2025-09-10 09:40:07');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_laporan_detail`
-- (See below for the actual view)
--
CREATE TABLE `vw_laporan_detail` (
`id` int(11)
,`ticket_code` varchar(20)
,`nim` varchar(20)
,`nama_mahasiswa` varchar(100)
,`jurusan` varchar(100)
,`angkatan` year(4)
,`log_type` enum('harian','khusus')
,`report_type` varchar(100)
,`sub_type` varchar(50)
,`deskripsi` text
,`division` enum('studentservice','studentsupport','studentdevelopment')
,`lampiran` varchar(255)
,`status` enum('pending','assign','resolved','done')
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `vw_laporan_detail`
--
DROP TABLE IF EXISTS `vw_laporan_detail`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_laporan_detail`  AS SELECT `l`.`id` AS `id`, `l`.`ticket_code` AS `ticket_code`, `l`.`nim` AS `nim`, `m`.`nama` AS `nama_mahasiswa`, `m`.`jurusan` AS `jurusan`, `m`.`angkatan` AS `angkatan`, `l`.`log_type` AS `log_type`, `l`.`report_type` AS `report_type`, `l`.`sub_type` AS `sub_type`, `l`.`deskripsi` AS `deskripsi`, `l`.`division` AS `division`, `l`.`lampiran` AS `lampiran`, `l`.`status` AS `status`, `l`.`created_at` AS `created_at`, `l`.`updated_at` AS `updated_at` FROM (`laporan` `l` left join `mahasiswa` `m` on(`l`.`nim` = `m`.`nim`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `changed_by` (`changed_by`),
  ADD KEY `idx_log_laporan` (`laporan_id`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_code` (`ticket_code`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_division_status` (`division`,`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_laporan_nim` (`nim`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`nim`);

--
-- Indexes for table `report_notes`
--
ALTER TABLE `report_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_report` (`report_id`),
  ADD KEY `fk_report_notes_created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `report_notes`
--
ALTER TABLE `report_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_activity_laporan` FOREIGN KEY (`laporan_id`) REFERENCES `laporan` (`id`),
  ADD CONSTRAINT `fk_activity_log_laporan` FOREIGN KEY (`laporan_id`) REFERENCES `laporan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `fk_laporan_mahasiswa` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`),
  ADD CONSTRAINT `laporan_ibfk_2` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`);

--
-- Constraints for table `report_notes`
--
ALTER TABLE `report_notes`
  ADD CONSTRAINT `fk_notes_laporan` FOREIGN KEY (`report_id`) REFERENCES `laporan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_report` FOREIGN KEY (`report_id`) REFERENCES `laporan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_report_notes_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `report_notes_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `laporan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_notes_ibfk_2` FOREIGN KEY (`report_id`) REFERENCES `laporan` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
