-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2026 at 06:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dtr_practice`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('On-time','Late','Grace Period') DEFAULT 'On-time',
  `am_in` time DEFAULT NULL,
  `am_out` time DEFAULT NULL,
  `pm_in` time DEFAULT NULL,
  `pm_out` time DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `log_date`, `time_in`, `time_out`, `status`, `am_in`, `am_out`, `pm_in`, `pm_out`, `note`) VALUES
(1, 1, '2026-03-01', '10:49:58', '10:49:59', '', '00:00:00', '00:00:00', '12:50:00', '18:20:00', NULL),
(2, 1, '2026-03-13', NULL, NULL, '', '07:01:00', '12:01:00', '13:01:00', '17:43:00', 'sads'),
(3, 1, '2026-03-02', NULL, NULL, '', '06:23:00', '11:01:00', '12:01:00', '17:01:00', ''),
(4, 1, '2026-02-27', NULL, NULL, '', '07:20:00', '00:00:00', '00:00:00', '00:00:00', NULL),
(5, 1, '2026-02-20', NULL, NULL, '', '07:00:00', '12:00:00', '13:00:00', '17:00:00', NULL),
(6, 2, '2026-03-01', NULL, NULL, '', '07:32:00', '11:33:00', '00:00:00', '00:00:00', ''),
(7, 2, '2026-02-20', NULL, NULL, '', '07:37:00', '23:40:00', '00:00:00', '00:00:00', ''),
(8, 2, '2026-03-20', NULL, NULL, '', '07:40:00', '22:45:00', '00:00:00', '00:00:00', ''),
(9, 2, '2026-03-02', NULL, NULL, '', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL,
  `target_hours` int(11) DEFAULT 500,
  `login_attempts` int(11) DEFAULT 0,
  `lockout_time` datetime DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `school` varchar(255) DEFAULT NULL,
  `agency` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `created_at`, `profile_pic`, `target_hours`, `login_attempts`, `lockout_time`, `course`, `school`, `agency`) VALUES
(1, 'Angel guzman', 'Angel Mallillin', '$2y$10$ae/TiAijsQfGTfSUe.FzwevY3LvYxbZ2aYNvKDu2bNihoOSXIxH72', '2026-03-01 09:45:57', 'profile_1_1772367421.jpg', 300, 0, NULL, NULL, NULL, NULL),
(2, 'John ', 'John123', '$2y$10$/U/UWSOPwkk0e2haG4koNujyJjfVhyEA5iZgblm0.DCz.f987O8Gq', '2026-03-01 13:25:03', 'profile_2_1772469395.jpg', 500, 0, NULL, 'BSIT', 'BESTLINK', 'HIS STORY STUDIO');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
