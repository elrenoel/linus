-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: May 18, 2026 at 01:44 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `linus`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin', '12345');

-- --------------------------------------------------------

--
-- Table structure for table `bus`
--

CREATE TABLE `bus` (
  `id_bus` int NOT NULL,
  `nama_bus` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus`
--

INSERT INTO `bus` (`id_bus`, `nama_bus`) VALUES
(5, 'Linus 1'),
(6, 'Linus 2'),
(7, 'Linus 3'),
(8, 'Linus 4');

-- --------------------------------------------------------

--
-- Table structure for table `bus_location`
--

CREATE TABLE `bus_location` (
  `id_bus` int NOT NULL,
  `lat` decimal(10,7) NOT NULL,
  `lng` decimal(10,7) NOT NULL,
  `speed_kmh` decimal(6,2) DEFAULT NULL,
  `heading_deg` smallint DEFAULT NULL,
  `accuracy_m` decimal(6,2) DEFAULT NULL,
  `recorded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_location`
--

INSERT INTO `bus_location` (`id_bus`, `lat`, `lng`, `speed_kmh`, `heading_deg`, `accuracy_m`, `recorded_at`) VALUES
(5, 3.5595739, 98.6572942, NULL, NULL, 86.00, '2026-05-13 15:05:48');

-- --------------------------------------------------------

--
-- Table structure for table `bus_location_log`
--

CREATE TABLE `bus_location_log` (
  `id_log` bigint NOT NULL,
  `id_bus` int NOT NULL,
  `lat` decimal(10,7) NOT NULL,
  `lng` decimal(10,7) NOT NULL,
  `speed_kmh` decimal(6,2) DEFAULT NULL,
  `heading_deg` smallint DEFAULT NULL,
  `accuracy_m` decimal(6,2) DEFAULT NULL,
  `recorded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `source` enum('gps','manual','sim') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'gps'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_location_log`
--

INSERT INTO `bus_location_log` (`id_log`, `id_bus`, `lat`, `lng`, `speed_kmh`, `heading_deg`, `accuracy_m`, `recorded_at`, `source`) VALUES
(333, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:49:30', 'gps'),
(334, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:49:34', 'gps'),
(335, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:49:37', 'gps'),
(336, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:49:40', 'gps'),
(337, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:49:43', 'gps'),
(338, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:49:46', 'gps'),
(339, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:49:49', 'gps'),
(340, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:49:52', 'gps'),
(341, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:49:55', 'gps'),
(342, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:49:58', 'gps'),
(343, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:01', 'gps'),
(344, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:04', 'gps'),
(345, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:07', 'gps'),
(346, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:10', 'gps'),
(347, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:13', 'gps'),
(348, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:15', 'gps'),
(349, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:19', 'gps'),
(350, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:22', 'gps'),
(351, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:25', 'gps'),
(352, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:28', 'gps'),
(353, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:31', 'gps'),
(354, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:34', 'gps'),
(355, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:50:37', 'gps'),
(356, 5, 3.5595816, 98.6573290, NULL, NULL, 71.00, '2026-05-13 14:50:39', 'gps'),
(357, 5, 3.5595816, 98.6573290, NULL, NULL, 71.00, '2026-05-13 14:50:43', 'gps'),
(358, 5, 3.5595816, 98.6573290, NULL, NULL, 71.00, '2026-05-13 14:50:46', 'gps'),
(359, 5, 3.5595816, 98.6573290, NULL, NULL, 71.00, '2026-05-13 14:50:49', 'gps'),
(360, 5, 3.5595816, 98.6573290, NULL, NULL, 71.00, '2026-05-13 14:50:51', 'gps'),
(361, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:50:54', 'gps'),
(362, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:50:57', 'gps'),
(363, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:00', 'gps'),
(364, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:03', 'gps'),
(365, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:07', 'gps'),
(366, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:10', 'gps'),
(367, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:13', 'gps'),
(368, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:16', 'gps'),
(369, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:19', 'gps'),
(370, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:22', 'gps'),
(371, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:25', 'gps'),
(372, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:28', 'gps'),
(373, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:31', 'gps'),
(374, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:34', 'gps'),
(375, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:37', 'gps'),
(376, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:40', 'gps'),
(377, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:43', 'gps'),
(378, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:46', 'gps'),
(379, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:49', 'gps'),
(380, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:52', 'gps'),
(381, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:55', 'gps'),
(382, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:51:58', 'gps'),
(383, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:52:01', 'gps'),
(384, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:52:04', 'gps'),
(385, 5, 3.5596270, 98.6573684, NULL, NULL, 75.00, '2026-05-13 14:52:18', 'gps'),
(386, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:18', 'gps'),
(387, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:21', 'gps'),
(388, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:25', 'gps'),
(389, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:28', 'gps'),
(390, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:31', 'gps'),
(391, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:34', 'gps'),
(392, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:37', 'gps'),
(393, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:40', 'gps'),
(394, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:43', 'gps'),
(395, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:46', 'gps'),
(396, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:49', 'gps'),
(397, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:52', 'gps'),
(398, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:55', 'gps'),
(399, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:52:58', 'gps'),
(400, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:53:01', 'gps'),
(401, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:53:04', 'gps'),
(402, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:53:07', 'gps'),
(403, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:53:10', 'gps'),
(404, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:53:13', 'gps'),
(405, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:53:16', 'gps'),
(406, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:53:19', 'gps'),
(407, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:53:22', 'gps'),
(408, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:53:46', 'gps'),
(409, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:54:46', 'gps'),
(410, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:55:46', 'gps'),
(411, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:56:46', 'gps'),
(412, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:57:46', 'gps'),
(413, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:58:46', 'gps'),
(414, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 14:59:46', 'gps'),
(415, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 15:00:46', 'gps'),
(416, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 15:01:46', 'gps'),
(417, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 15:02:46', 'gps'),
(418, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 15:03:46', 'gps'),
(419, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 15:04:46', 'gps'),
(420, 5, 3.5596538, 98.6572761, NULL, NULL, 108.00, '2026-05-13 15:05:25', 'gps'),
(421, 5, 3.5595945, 98.6573023, NULL, NULL, 84.00, '2026-05-13 15:05:27', 'gps'),
(422, 5, 3.5595945, 98.6573023, NULL, NULL, 84.00, '2026-05-13 15:05:31', 'gps'),
(423, 5, 3.5595945, 98.6573023, NULL, NULL, 84.00, '2026-05-13 15:05:34', 'gps'),
(424, 5, 3.5595945, 98.6573023, NULL, NULL, 84.00, '2026-05-13 15:05:36', 'gps'),
(425, 5, 3.5595739, 98.6572942, NULL, NULL, 86.00, '2026-05-13 15:05:39', 'gps'),
(426, 5, 3.5595739, 98.6572942, NULL, NULL, 86.00, '2026-05-13 15:05:42', 'gps'),
(427, 5, 3.5595739, 98.6572942, NULL, NULL, 86.00, '2026-05-13 15:05:45', 'gps'),
(428, 5, 3.5595739, 98.6572942, NULL, NULL, 86.00, '2026-05-13 15:05:48', 'gps');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id_feedback` int NOT NULL,
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `rating` int DEFAULT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `bus_label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `route_label` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id_feedback`, `username`, `comment`, `rating`, `date`, `bus_label`, `route_label`) VALUES
(11, 'dummy', 'tes', 5, '2025-06-08 05:58:51', '', ''),
(12, 'aqil', 'aplikasinya bagus', 5, '2025-06-08 05:58:59', '', ''),
(14, 'batman', 'inovasi yang bagus', 5, '2025-06-08 06:23:44', '', ''),
(15, 'joker', 'gg', 5, '2025-06-08 06:35:21', '', ''),
(16, 'Albert', 'Masih butuh beberapa perbaikan, seperti bangku linus yang sudah goyang.', 3, '2025-06-08 16:20:20', '', ''),
(17, 'Chyntia ', 'Inovasi yang bagus, saya jadi lebih bisa memanajemen waktu karena tau informasi lokasi linus', 5, '2025-06-08 16:20:51', '', ''),
(19, 'dumb ass', 'bagus mantap anj', 5, '2026-04-29 00:34:27', 'Bus Linus 3', 'Pintu 4 -> Pintu 1');

-- --------------------------------------------------------

--
-- Table structure for table `operasional`
--

CREATE TABLE `operasional` (
  `id_operasional` int NOT NULL,
  `id_bus` int NOT NULL,
  `id_supir` int NOT NULL,
  `lokasi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mulai` time NOT NULL,
  `selesai` time NOT NULL,
  `status` enum('aktif','selesai','batal') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aktif',
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supir`
--

CREATE TABLE `supir` (
  `id_supir` int NOT NULL,
  `nama_supir` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supir`
--

INSERT INTO `supir` (`id_supir`, `nama_supir`, `username`, `password`) VALUES
(1, 'Sucipto', 'sucipto', '12345'),
(2, 'Sultansyah', 'sultansyah', '12345'),
(3, 'Hadi', 'hadi', '12345'),
(4, 'Yazid', 'yazid', '12345');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `email`, `password`) VALUES
(2, 'dummy', 'dummy@gmail.com', '12345'),
(4, 'aqil', 'aqil@gmail.com', '12345'),
(5, 'elreno', 'reno@mail.com', '12345'),
(6, 'jule', 'jule@mail.com', '12345');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `bus`
--
ALTER TABLE `bus`
  ADD PRIMARY KEY (`id_bus`);

--
-- Indexes for table `bus_location`
--
ALTER TABLE `bus_location`
  ADD PRIMARY KEY (`id_bus`);

--
-- Indexes for table `bus_location_log`
--
ALTER TABLE `bus_location_log`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_bus_time` (`id_bus`,`recorded_at`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id_feedback`);

--
-- Indexes for table `operasional`
--
ALTER TABLE `operasional`
  ADD PRIMARY KEY (`id_operasional`);

--
-- Indexes for table `supir`
--
ALTER TABLE `supir`
  ADD PRIMARY KEY (`id_supir`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bus`
--
ALTER TABLE `bus`
  MODIFY `id_bus` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bus_location_log`
--
ALTER TABLE `bus_location_log`
  MODIFY `id_log` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=429;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id_feedback` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `operasional`
--
ALTER TABLE `operasional`
  MODIFY `id_operasional` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `supir`
--
ALTER TABLE `supir`
  MODIFY `id_supir` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
