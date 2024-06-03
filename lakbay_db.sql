-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2024 at 02:28 AM
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
-- Database: `lakbay_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `driver_id` int(11) NOT NULL,
  `dr_emp_id` int(255) NOT NULL,
  `dr_name` varchar(255) NOT NULL,
  `dr_office` varchar(255) DEFAULT NULL,
  `dr_status` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`driver_id`, `dr_emp_id`, `dr_name`, `dr_office`, `dr_status`) VALUES
(2, 67381340, 'Waccha McColediss', 'Provincial Assessor\'s Office', 'Idle'),
(3, 22819405, 'Sim Mansters', 'Provincial Governor\'s Office', 'Idle'),
(4, 48291839, 'Harvy Toralba', 'PGO', 'Idle'),
(5, 48291840, 'Terry Dactyl', 'PASSO', 'Idle'),
(6, 48291841, 'Pool Tact', 'PTO', 'Idle'),
(7, 48291842, 'Mang Rooves', 'PTO', 'Idle');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `ev_name` varchar(100) NOT NULL,
  `ev_venue` varchar(100) NOT NULL,
  `ev_date_start` date DEFAULT NULL,
  `ev_time_start` time DEFAULT NULL,
  `ev_date_end` date DEFAULT NULL,
  `ev_time_end` time DEFAULT NULL,
  `ev_date_added` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `ev_name`, `ev_venue`, `ev_date_start`, `ev_time_start`, `ev_date_end`, `ev_time_end`, `ev_date_added`) VALUES
(2, 'Dev4Dev', 'DICT, Baguio City', '2023-10-18', '08:00:00', '2023-10-18', '08:00:00', '2023-10-27');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_02_05_005420_create_offices_table', 1),
(2, '2024_02_05_010649_add_vh_office_to_vehicles_table', 2),
(4, '2024_02_05_011733_remove_off_id_column_from_offices_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `off_acr` varchar(255) NOT NULL,
  `off_name` varchar(255) NOT NULL,
  `off_head` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requestors`
--

CREATE TABLE `requestors` (
  `requestor_id` int(11) NOT NULL,
  `rq_full_name` varchar(100) NOT NULL,
  `rq_office` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requestors`
--

INSERT INTO `requestors` (`requestor_id`, `rq_full_name`, `rq_office`) VALUES
(1, 'Harvy Toralba', 'PGO');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `rs_voucher` int(11) NOT NULL,
  `rs_daily_transport` tinyint(1) DEFAULT NULL,
  `rs_outside_province` tinyint(1) DEFAULT NULL,
  `rs_date_filed` date DEFAULT NULL,
  `rs_approval_status` varchar(20) DEFAULT NULL,
  `rs_status` varchar(20) DEFAULT NULL,
  `event_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `requestor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `rs_voucher`, `rs_daily_transport`, `rs_outside_province`, `rs_date_filed`, `rs_approval_status`, `rs_status`, `event_id`, `driver_id`, `vehicle_id`, `requestor_id`) VALUES
(2, 402125, 1, 1, '2023-10-02', 'approved', 'queued', 2, 6, 1, 1),
(3, 2414124, 1, 1, '2023-10-24', 'approved', 'queued', 2, 7, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`) VALUES
(1, 'system.admin', '$2a$12$Y8nFsS3r2BqCXU.fNh.mTeMnxlmk9Y4hw8HvnA8t4RljDgEfQ9Atq');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `vh_plate_number` varchar(10) NOT NULL,
  `vh_type` varchar(150) NOT NULL,
  `vh_brand` varchar(30) DEFAULT NULL,
  `vh_year` tinyint(4) DEFAULT NULL,
  `vh_fuel_type` varchar(50) DEFAULT NULL,
  `vh_condition` varchar(30) NOT NULL,
  `vh_status` varchar(10) NOT NULL,
  `vh_office` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `vh_plate_number`, `vh_type`, `vh_brand`, `vh_year`, `vh_fuel_type`, `vh_condition`, `vh_status`, `vh_office`) VALUES
(1, 'OWE-2492', 'SUV', 'Toyota', 127, 'Diesel', 'Good', 'Available', ''),
(4, 'UWU-6969', 'Pickup', 'Cherry Mobile', 127, 'Ethanol', 'Good', 'Available', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `dr_emp_id` (`dr_emp_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requestors`
--
ALTER TABLE `requestors`
  ADD PRIMARY KEY (`requestor_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `reservations_ibfk_2` (`event_id`),
  ADD KEY `reservations_ibfk_3` (`requestor_id`),
  ADD KEY `driver_id` (`driver_id`,`vehicle_id`),
  ADD KEY `reservations_ibfk_4` (`vehicle_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD UNIQUE KEY `vh_plate_number` (`vh_plate_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requestors`
--
ALTER TABLE `requestors`
  MODIFY `requestor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`),
  ADD CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`requestor_id`) REFERENCES `requestors` (`requestor_id`),
  ADD CONSTRAINT `reservations_ibfk_4` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
