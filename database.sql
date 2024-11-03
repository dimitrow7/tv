-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 03, 2024 at 04:40 PM
-- Server version: 10.6.20-MariaDB-log
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `resinwoo_media_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `location_name`, `user_id`) VALUES
(1, 'TEST', 2);

-- --------------------------------------------------------

--
-- Table structure for table `media_files`
--

CREATE TABLE `media_files` (
  `id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('image','video') NOT NULL,
  `uploaded_by` varchar(50) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `thumbnail` varchar(255) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `days_of_week` varchar(7) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `media_files`
--

INSERT INTO `media_files` (`id`, `file_path`, `file_type`, `uploaded_by`, `uploaded_at`, `thumbnail`, `location_id`, `start_date`, `end_date`, `days_of_week`, `start_time`, `end_time`) VALUES
(1, 'uploads/Event TV 5.jpg', 'image', NULL, '2024-10-20 10:04:18', NULL, NULL, NULL, NULL, NULL, '12:00:00', '16:00:00'),
(2, 'uploads/Event TV 3.jpg', 'image', NULL, '2024-10-20 10:04:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'uploads/Event TV 4.jpg', 'image', NULL, '2024-10-20 10:04:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'uploads/TV screens video.mp4', 'video', 'admin', '2024-10-20 12:35:15', 'uploads/thumbnails/TV screens video.jpg', NULL, NULL, NULL, NULL, '12:10:00', '14:00:00'),
(21, 'uploads/IQOS_ILUMA_ACCESSORIES_PRIME_45S_v09.mp4', 'video', 'admin', '2024-10-20 12:30:19', 'uploads/thumbnails/IQOS_ILUMA_ACCESSORIES_PRIME_45S_v09.jpg', NULL, NULL, NULL, NULL, NULL, NULL),
(38, 'uploads/bar-hey-video.mp4', 'video', 'admin', '2024-10-20 13:53:36', 'uploads/thumbnails/bar-hey-video.jpg', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `media_schedule`
--

CREATE TABLE `media_schedule` (
  `id` int(11) NOT NULL,
  `media_file_id` int(11) DEFAULT NULL,
  `day_of_week` int(11) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `media_schedule`
--

INSERT INTO `media_schedule` (`id`, `media_file_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(103, 22, 7, '00:00:00', '23:59:00'),
(88, 1, 6, '00:00:00', '00:00:00'),
(87, 1, 5, '00:00:00', '00:00:00'),
(86, 1, 4, '00:00:00', '00:00:00'),
(85, 1, 3, '00:00:00', '00:00:00'),
(84, 1, 2, '00:00:00', '00:00:00'),
(83, 1, 1, '00:00:00', '00:00:00'),
(89, 1, 7, '00:00:00', '23:59:00'),
(95, 2, 6, '00:00:00', '00:00:00'),
(94, 2, 5, '00:00:00', '00:00:00'),
(93, 2, 4, '00:00:00', '00:00:00'),
(92, 2, 3, '00:00:00', '00:00:00'),
(91, 2, 2, '00:00:00', '00:00:00'),
(90, 2, 1, '00:00:00', '00:00:00'),
(104, 21, 7, '00:00:00', '23:59:00'),
(102, 22, 6, '12:11:00', '15:15:00'),
(101, 22, 5, '00:00:00', '00:00:00'),
(100, 22, 4, '00:00:00', '00:00:00'),
(99, 22, 3, '00:00:00', '00:00:00'),
(98, 22, 2, '00:00:00', '00:00:00'),
(97, 22, 1, '00:00:00', '00:00:00'),
(96, 2, 7, '00:00:00', '23:59:00'),
(105, 38, 7, '00:00:00', '23:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$d7.3.ndb7USP8nY25ZNgI.pRWYYW/lO/v3wlHY5nPCo3B3I.LkTiS'),
(2, 'barhey', '$2y$10$4SirzmHohkhS3is1v5nP2On//uM2zZ00SkUwte3QGjcReodlleYKq'),
(3, 'sasho', '$2y$10$cqUzEH/3OfDkR.U4TU.0mOc7fBrGLuLH/ArC/8PUHu5FfkdLh9XMa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `media_files`
--
ALTER TABLE `media_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_location_id` (`location_id`);

--
-- Indexes for table `media_schedule`
--
ALTER TABLE `media_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_file_id` (`media_file_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `media_files`
--
ALTER TABLE `media_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `media_schedule`
--
ALTER TABLE `media_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
