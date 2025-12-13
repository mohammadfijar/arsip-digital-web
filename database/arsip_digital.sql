-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 01:46 PM
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
-- Database: `arsip_digital`
--

-- --------------------------------------------------------

--
-- Table structure for table `arsip`
--

CREATE TABLE `arsip` (
  `arsip_id` int(11) NOT NULL,
  `nomor_arsip` varchar(50) DEFAULT NULL,
  `judul` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `nama_file` varchar(255) NOT NULL,
  `ukuran_file` bigint(20) DEFAULT NULL,
  `ekstensi` varchar(10) DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_arsip` enum('baru','diperiksa','disetujui','ditolak') DEFAULT 'baru',
  `uploaded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `arsip`
--

INSERT INTO `arsip` (`arsip_id`, `nomor_arsip`, `judul`, `deskripsi`, `kategori_id`, `nama_file`, `ukuran_file`, `ekstensi`, `tanggal_upload`, `status_arsip`, `uploaded_by`) VALUES
(37, 'SK-2025-001', 'Arsip Perkantoran', 'File Meeting Client', 3, '68ddf00958c22_1314-Article Text-3497-1-10-20210225.pdf', 576837, 'pdf', '2025-10-01 22:22:49', 'disetujui', 2),
(38, '986687', 'keuangan', 'guoras', 1, '68e5c2d86c499_Penmahaman Materi ppt KP.docx', 27489, 'docx', '2025-10-07 20:48:08', 'ditolak', 2),
(39, '6787', 'Sepak Bola', 'get', 3, '68e5f6fc10b61_Makalah Blibli.docx', 18771, 'docx', '2025-10-08 00:30:36', 'diperiksa', 2),
(40, '9866823', 'Teslmvda', 'ds', 3, '68e653d1e57c2_Keunikan Riau.docx', 42000, 'docx', '2025-10-08 07:06:41', 'disetujui', 2),
(42, '9866872121232', 'Presentasi', 'efwdsc', 3, '68ec60e1c2109_Makalah Blibli.docx', 18771, 'docx', '2025-10-12 21:16:01', 'baru', 2),
(44, '678786', 'KP Tes', 'Udah Oke', 3, '68ec617f0d841_Basis Data pertemuan6 MFijar.docx', 651885, 'docx', '2025-10-12 21:18:39', 'disetujui', 2);

-- --------------------------------------------------------

--
-- Table structure for table `backup_log`
--

CREATE TABLE `backup_log` (
  `backup_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `jenis` enum('backup','restore') DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `backup_log`
--

INSERT INTO `backup_log` (`backup_id`, `admin_id`, `jenis`, `file_path`, `keterangan`, `waktu`) VALUES
(4, 1, 'backup', '../backup/backup_20250925_114744.sql', 'Backup database berhasil dibuat', '2025-09-25 09:47:45'),
(5, 1, 'backup', '../backup/backup_20250925_114808.sql', 'Backup database berhasil dibuat', '2025-09-25 09:48:08');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `kategori_id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`kategori_id`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'Surat Keuangan', 'Dokumen laporan dan surat terkait keuangan', '2025-09-25 03:17:56'),
(2, 'Surat Kepegawaian', 'Dokumen administrasi pegawai', '2025-09-25 03:17:56'),
(3, 'Notulen Rapat', 'Catatan hasil rapat', '2025-09-25 03:17:56'),
(4, 'Lain-lain', 'Arsip umum lainnya', '2025-09-25 03:17:56'),
(5, 'Surat Lamaran', '', '2025-10-08 10:34:31'),
(6, 'Surat Lamaran', '', '2025-10-08 10:35:05');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `laporan_id` int(11) NOT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `isi_laporan` text DEFAULT NULL,
  `dibuat_oleh` int(11) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `kategori_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_akses`
--

CREATE TABLE `log_akses` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `arsip_id` int(11) DEFAULT NULL,
  `aksi` enum('upload','edit','unduh','lihat','hapus','login','logout') DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_akses`
--

INSERT INTO `log_akses` (`log_id`, `user_id`, `arsip_id`, `aksi`, `keterangan`, `waktu`) VALUES
(1, 2, 40, 'upload', 'Staff mengupload arsip berjudul: Tes', '2025-10-08 12:06:42'),
(2, 2, 40, 'edit', 'Mengedit arsip dan mengganti file: Tes', '2025-10-08 07:24:42'),
(3, 1, 39, 'edit', 'Admin mengubah status arsip \'Sepak Bola\' dari \'\' menjadi \'baru\'', '2025-10-08 07:30:56'),
(4, 1, 39, 'edit', 'Admin mengubah status arsip \'Sepak Bola\' dari \'baru\' menjadi \'diperiksa\'', '2025-10-08 07:31:34'),
(5, 1, 40, 'lihat', 'Melihat detail arsip: Tes', '2025-10-13 02:11:53'),
(6, 2, 42, 'upload', 'Staff mengupload arsip berjudul: Presentasi', '2025-10-13 02:16:01'),
(7, 2, 44, 'upload', 'Staff mengupload arsip berjudul: KP Andre', '2025-10-13 02:18:39'),
(8, 2, 44, 'edit', 'Mengedit arsip dan mengganti file: Skriptis Andre', '2025-10-12 21:20:55'),
(9, 2, 44, 'edit', 'Mengedit arsip dan mengganti file: xc', '2025-10-12 21:22:07'),
(10, 2, 44, 'edit', 'Mengedit arsip dan mengganti file: KP Andre', '2025-11-17 06:13:37'),
(11, 2, 44, 'edit', 'Mengedit arsip dan mengganti file: Kp sda', '2025-11-17 06:13:49'),
(12, 2, 44, 'edit', 'Mengedit arsip dan mengganti file: KP Andre', '2025-11-17 06:13:50'),
(13, 2, 44, 'edit', 'Mengedit arsip dan mengganti file: KP Andre', '2025-11-17 06:16:21'),
(14, 2, 44, 'edit', 'Mengedit arsip dan mengganti file: KP Tes', '2025-11-17 06:21:02'),
(15, 2, 40, 'edit', 'Mengedit arsip dan mengganti file: Teslmvda', '2025-11-17 06:26:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','staff','pimpinan') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `created_at`) VALUES
(1, 'admin', 'admin123', 'Administrator', NULL, 'admin', '2025-09-25 03:59:42'),
(2, 'staff1', 'staff123', 'Staff Arsip', '', 'staff', '2025-09-25 03:59:42'),
(3, 'pimpinan1', 'pimpinan123', 'Kepala Kantor', NULL, 'pimpinan', '2025-09-25 03:59:42'),
(4, 'jilan', '12345', 'jilanos', '', 'admin', '2025-10-08 04:09:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arsip`
--
ALTER TABLE `arsip`
  ADD PRIMARY KEY (`arsip_id`),
  ADD UNIQUE KEY `nomor_arsip` (`nomor_arsip`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `backup_log`
--
ALTER TABLE `backup_log`
  ADD PRIMARY KEY (`backup_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`laporan_id`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`),
  ADD KEY `fk_laporan_kategori` (`kategori_id`);

--
-- Indexes for table `log_akses`
--
ALTER TABLE `log_akses`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `arsip_id` (`arsip_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arsip`
--
ALTER TABLE `arsip`
  MODIFY `arsip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `backup_log`
--
ALTER TABLE `backup_log`
  MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `kategori_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `laporan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_akses`
--
ALTER TABLE `log_akses`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `arsip`
--
ALTER TABLE `arsip`
  ADD CONSTRAINT `arsip_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`kategori_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `arsip_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `backup_log`
--
ALTER TABLE `backup_log`
  ADD CONSTRAINT `backup_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `fk_laporan_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`kategori_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `log_akses`
--
ALTER TABLE `log_akses`
  ADD CONSTRAINT `log_akses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `log_akses_ibfk_2` FOREIGN KEY (`arsip_id`) REFERENCES `arsip` (`arsip_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
