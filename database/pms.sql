-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2024 at 08:20 AM
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
-- Database: `pms`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `plate_number` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `slot_number` int(11) DEFAULT NULL,
  `slot_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `plate_number`, `user_id`, `date`, `time_in`, `time_out`, `status`, `slot_number`, `slot_id`) VALUES
(108, 'RED', 54, '2024-10-09', '10:41:34', '10:42:04', 'out', NULL, 4),
(109, 'GAGOGO', 54, '2024-10-13', '05:54:42', '05:54:59', 'out', NULL, 27);

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `plate_number` varchar(255) NOT NULL,
  `receipt_token` varchar(255) NOT NULL,
  `expiration_date` timestamp NULL DEFAULT NULL,
  `qr_code` blob DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`id`, `plate_number`, `receipt_token`, `expiration_date`, `qr_code`, `created_at`, `user_id`) VALUES
(81, 'W45WIW', '349bb162db9b9a201b6f013a2cc4443c', '2026-08-25 11:48:09', 0x7172636f646573322f5734355749575f7172636f64652e706e67, '2024-08-25 11:48:09', 54),
(82, 'RED', '457932a3a9ee466c8167e3f7b6bc6c2b', '2025-08-25 11:50:48', 0x7172636f646573322f5245445f7172636f64652e706e67, '2024-08-25 11:50:48', 54),
(83, 'president', 'a186b8e96c451182e22506539e8fdc0f', '2025-08-25 12:04:35', 0x7172636f646573322f707265736964656e745f7172636f64652e706e67, '2024-08-25 12:04:35', 58),
(84, 'EGM43A', '61de7149e42e72b0e42424b3812f216a', '2025-08-27 13:13:44', 0x7172636f646573322f45474d3433415f7172636f64652e706e67, '2024-08-27 13:13:44', 54),
(85, 'waoj', '273df840f8eac7fe1146cf89d3c13ec0', '2025-08-27 13:13:52', 0x7172636f646573322f77616f6a5f7172636f64652e706e67, '2024-08-27 13:13:52', 54),
(86, '3KIVFW2', '999c97aba0e52e9877a1c0bbbeff0f25', '2025-08-27 13:14:20', 0x7172636f646573322f334b49564657325f7172636f64652e706e67, '2024-08-27 13:14:20', 56),
(87, 'KEJ3351', '16718e73c753353ce37cc89bbc3980ef', '2025-08-27 13:14:27', 0x7172636f646573322f4b454a333335315f7172636f64652e706e67, '2024-08-27 13:14:27', 54),
(88, '00000000000', 'b23a6e1dde0131bc63f23d5dfaed4186', '2025-09-03 05:18:31', 0x7172636f646573322f30303030303030303030305f7172636f64652e706e67, '2024-09-03 05:18:31', 55),
(89, '1111111111111111111', '7855e26420597bdd202a5b23e6800d7e', '2025-09-03 06:20:58', 0x7172636f646573322f313131313131313131313131313131313131315f7172636f64652e706e67, '2024-09-03 06:20:58', 59),
(90, 'eihq', '32b26afb5caf1711f7e0b9bcd223db49', '2025-10-04 10:28:56', 0x7172636f646573322f656968715f7172636f64652e706e67, '2024-10-04 10:28:56', 61),
(91, '-hw5rmji', '7907a87d87740c2655921c242f82be92', '2025-10-04 11:01:09', 0x7172636f646573322f2d687735726d6a695f7172636f64652e706e67, '2024-10-04 11:01:09', 58),
(92, 'aaa2', '4c6a855e4acab594cc1b89eab90eb53c', '2025-10-08 13:23:44', 0x7172636f646573322f616161325f7172636f64652e706e67, '2024-10-08 13:23:44', 54),
(93, 'wow', '99812ce39e8c7ab713c1fc7c767c9b69', '2025-10-08 13:24:29', 0x7172636f646573322f776f775f7172636f64652e706e67, '2024-10-08 13:24:29', 54),
(94, 'NOOOOOo', '827722918e06b33bde0b092ee26c7626', '2025-10-08 14:22:25', 0x7172636f646573322f4e4f4f4f4f4f6f5f7172636f64652e706e67, '2024-10-08 14:22:25', 54),
(95, 'UWU', '9fbafea2ec181ad77c1a4fe79fa80e91', '2025-10-08 14:22:36', 0x7172636f646573322f5557555f7172636f64652e706e67, '2024-10-08 14:22:36', 54),
(96, 'OYOYOY', '3e4951accff3b84d37925b690a589b77', '2025-10-08 14:22:50', 0x7172636f646573322f4f594f594f595f7172636f64652e706e67, '2024-10-08 14:22:50', 54),
(97, 'GAGOGO', 'b86cac1f0593ae73a9f4b48b1201c0fd', '2025-10-08 14:23:12', 0x7172636f646573322f4741474f474f5f7172636f64652e706e67, '2024-10-08 14:23:12', 54);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `plate_number` varchar(255) DEFAULT NULL,
  `vehicle_type` varchar(255) DEFAULT NULL,
  `slot_number` int(11) DEFAULT NULL,
  `reservation_date` datetime DEFAULT current_timestamp(),
  `status` enum('reserved','available','occupied','expired') DEFAULT 'available',
  `expiry_time` time DEFAULT NULL,
  `slot_id` int(255) DEFAULT NULL,
  `user_id` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `plate_number`, `vehicle_type`, `slot_number`, `reservation_date`, `status`, `expiry_time`, `slot_id`, `user_id`) VALUES
(623, 'RED', '4_wheel', NULL, '2024-10-04 03:27:00', 'expired', '00:00:00', 3, 54),
(633, 'RED', '4_wheel', NULL, '2024-10-06 04:43:39', 'expired', '00:00:00', 4, 54),
(634, 'eihq', '4_wheel', 3, '2024-10-07 05:51:36', 'reserved', '05:56:36', 3, 61),
(636, '00000000000', '4_wheel', 1, '2024-10-07 05:56:28', 'reserved', '23:59:59', 1, 55),
(637, 'RED', '4_wheel', NULL, '2024-10-09 10:40:56', NULL, '00:00:00', 4, 54),
(638, 'W45WIW', '4_wheel', NULL, '2024-10-12 10:32:59', 'expired', '00:00:00', 4, 54),
(639, 'GAGOGO', '3_wheel', NULL, '2024-10-13 05:53:31', NULL, '00:00:00', 27, 54);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `type` enum('Admin','User') NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `lto_registration` varchar(255) DEFAULT NULL,
  `license` int(255) DEFAULT NULL,
  `license_img` blob DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `account_created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `penalty` decimal(10,2) DEFAULT 0.00,
  `restricted` tinyint(1) DEFAULT 0,
  `disabled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `birth_date`, `gender`, `contact_number`, `type`, `role`, `username`, `password`, `email`, `lto_registration`, `license`, `license_img`, `image`, `account_created_at`, `penalty`, `restricted`, `disabled`) VALUES
(5, 'Pabook', 'ka?', '1935-12-25', 'Male', '099999', 'Admin', NULL, 'Melucky', '$2y$10$xSM6HEdvpJhB2nAdNNkLG.1etfaLH2cMUjcRgghaV4sgr/Bt6EDhq', 'Melucky@gmail.com', NULL, NULL, NULL, 'working.png', '2024-09-04 18:52:18', NULL, 0, 0),
(54, 'Yong', 'Flores', '2018-01-01', 'Male', '99564575', 'User', 'security', 'jaspherflores', '$2y$10$nJ1YkIrQhzuH7DMAxFda0uvoYlzBLlNzsgaFmWr4wf23y4V6/Zb1.', 'jaspherflores@gmail.com', 'melv.jpg', 8777777, 0x74756e696e672e706e67, 'cancer.jpg', '2024-09-04 18:52:18', 75.00, 0, 0),
(55, 'sample', 'sample', '2024-10-01', 'Male', '9999', 'User', 'president', 'sample', '$2y$10$i.oeV4xiMh1SLy8gUWJ7Be//AW2nJtpxppms.BCt81maO/OwnT.n.', 'sample.@gmail.com', NULL, 22222, 0x74756e696e672e706e67, 'users.jpg', '2024-09-04 18:52:18', 0.00, 0, 0),
(56, 'sample2', 'sample2', '2030-09-16', 'Male', '099999', 'User', 'staff', 'sample2', '$2y$10$6WH6xLcvP6oCSMFW0TKhFuz7OT5yPASKTTu/RVHoiYDVGY.FbVHfK', 'sample2@gmail.com', NULL, 99999, 0x74756e696e672e706e67, 'users.jpg', '2024-09-04 18:52:18', 0.00, 0, 0),
(57, 'sample0', 'sample0', '2019-06-29', 'Male', '0999999', 'User', 'vice_president', 'sample0', '$2y$10$1iBpIhZnhVUmtoS/7hDo6OTyBEN/xIRtIbCTXLn5yhwGWvPucATNm', 'sample0Q@gmail.com', NULL, 1111111, NULL, 'users.jpg', '2024-09-04 18:52:18', 0.00, 0, 0),
(58, 'president', 'president', '2024-09-02', 'Male', '0999999', 'User', 'president', 'president', '$2y$10$KYrB6fJaR3fDhdFEuOKWK.f7WFp5YhHfwGOpSpes2gm3DIRjgiOAK', 'president@gmail.com', NULL, NULL, NULL, 'users.jpg', '2024-09-04 18:52:18', 0.00, 0, 0),
(59, 'user', 'user', '0000-00-00', 'Male', '099999', 'User', 'faculty', 'user', '$2y$10$D6yoeCzhywBe1GaSn898xuvmeZLEDzMOK5jleCXXC8B/07yzzbeZS', 'user@gmail.com', NULL, NULL, NULL, 'users.jpg', '2024-09-04 18:52:18', 0.00, 0, 0),
(60, 'vicepresident', 'vicepresident', '0000-00-00', 'Male', '9999999', 'User', 'vice_president', 'vicepresident', '$2y$10$jUySJbO1vxKBKTNBC5RoWOA.jhzP5FjioFAHqRLlQPg9fGHBo/tke', 'vicepresident@gmail.com', NULL, NULL, NULL, 'users.jpg', '2024-09-04 18:52:18', 0.00, 0, 0),
(61, 'samplelang', 'samplelang', '0000-00-00', 'Male', '099999', 'User', 'faculty', 'samplelang', '$2y$10$OGVESwcZruxAF/RneuOucu7s5P3LJz0JtZYWnsHjFWk.BQ4kxndzW', 'samplelang@gmail.com', NULL, NULL, NULL, 'users.jpg', '2024-09-04 18:52:18', 0.00, 0, 0),
(68, 'jqawo', 'eaq', '2024-09-15', 'Female', '80', 'User', 'faculty', 'babae', '$2y$10$awb39TFaV71djnyyL2P6T.KZIq1PeGTNstpVpOgFu5qzYlx6ijCpG', 'aw2q@gmail.com', NULL, NULL, NULL, NULL, '2024-09-27 04:58:43', 0.00, 0, 0),
(69, 'testing', 'testing', '2009-02-12', 'Male', '0999999', 'User', 'staff', 'testing', '$2y$10$z8M4evOqNF5Vx/vMtboIJeOiZi5yjQ6KARBV28Y9K6.3xhGdazxtG', 'testing@gmail.com', NULL, 999999999, 0x2e2e2f75706c6f6164732f6d656c762e6a7067, 'tuning.png', '2024-10-06 07:51:56', 0.00, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `plate_number` varchar(50) NOT NULL,
  `license` int(255) DEFAULT NULL,
  `license_img` blob DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_brand` varchar(255) NOT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `color` varchar(50) NOT NULL,
  `vehicle_picture` blob DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid` varchar(255) DEFAULT NULL,
  `amount` int(255) DEFAULT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`plate_number`, `license`, `license_img`, `user_id`, `vehicle_brand`, `vehicle_type`, `color`, `vehicle_picture`, `created_at`, `paid`, `amount`, `paid_at`) VALUES
('-hw5rmji', NULL, NULL, 58, 'equt-9', '3_wheel', 'gwsr', '', '2024-10-04 01:59:51', NULL, NULL, '2024-10-04 01:59:51'),
('00000000000', NULL, NULL, 55, 'LAMBORGINI', '4_wheel', 'RAINBOW', '', '2024-09-02 20:17:57', NULL, NULL, '2024-09-04 19:24:26'),
('1111111111111111111', NULL, NULL, 59, 'MUSTANG', '4_wheel', 'RED', '', '2024-09-02 21:20:44', '1', 1200, '2024-09-04 19:24:26'),
('3KIVFW2', NULL, NULL, 56, 'sample2', '2_wheel', 'red', '', '2024-08-27 04:09:23', NULL, NULL, '2024-09-04 19:24:26'),
('aaa2', NULL, NULL, 54, 'aaa', '4_wheel', 'aaa', 0x2e2e2f75706c6f6164732f6d656c762e6a7067, '2024-09-23 18:45:05', '1', 1200, '2024-09-23 18:45:05'),
('EGM43A', NULL, NULL, 54, 'toyota', '2_wheel', 'red', '', '2024-08-27 04:12:57', '1', 600, '2024-09-04 19:24:26'),
('eihq', NULL, NULL, 61, 'ows', '4_wheel', 'wf', '', '2024-10-04 01:28:05', '1', 1200, '2024-10-04 01:28:05'),
('GAGOGO', NULL, NULL, 54, 'GAGOGO', '3_wheel', 'PENKPENK', '', '2024-10-08 05:01:04', '1', 600, '2024-10-08 05:01:04'),
('GAGOGOGOGO', NULL, NULL, 54, 'GAGOGOGOGO', '3_wheel', 'PENKPENK', '', '2024-10-08 05:02:40', NULL, NULL, '2024-10-08 05:02:40'),
('GAIGGAIGGAIG', NULL, NULL, 54, 'GAIGGAIGGAIG', '3_wheel', 'GAIGGAIGGAIG', '', '2024-10-08 05:08:01', '1', 600, '2024-10-08 05:08:01'),
('gjWKFJ43', 0, 0x2e2e2f75706c6f6164732f74756e696e672e706e67, 58, 'PERARA', '2_wheel', 'PENK', 0x2e2e2f75706c6f6164732f74756e696e672e706e67, '2024-10-04 02:33:05', NULL, NULL, '2024-10-04 02:33:05'),
('GLE35ElF', 0, 0x2e2e2f75706c6f6164732f74756e696e672e706e67, 58, 'ipwjgva', '3_wheel', 'PENK', 0x2e2e2f75706c6f6164732f63616e6365722e6a7067, '2024-10-04 02:36:57', NULL, NULL, '2024-10-04 02:36:57'),
('KEJ3351', NULL, NULL, 54, 'Mercidis', '3_wheel', 'griy', 0x2e2e2f75706c6f6164732f74756e696e672e706e67, '2024-08-25 02:44:30', '1', 600, '2024-09-04 19:24:26'),
('kg[oeg33', 452543, 0x2e2e2f75706c6f6164732f63616e6365722e6a7067, 58, 'PERARARE', '3_wheel', 'RID', 0x2e2e2f75706c6f6164732f63616e6365722e6a7067, '2024-10-04 02:38:37', NULL, NULL, '2024-10-04 02:38:37'),
('NOOOOOo', NULL, NULL, 54, 'NOOOOOo', '3_wheel', 'YALLOW', '', '2024-10-08 05:11:57', '1', 600, '2024-10-08 05:11:57'),
('OYOYOY', NULL, NULL, 54, 'OYOYOY', '3_wheel', 'OYOYOY', '', '2024-10-08 05:09:00', '1', 600, '2024-10-08 05:09:00'),
('president', NULL, NULL, 58, 'president', '4_wheel', 'president', '', '2024-08-25 03:03:44', NULL, NULL, '2024-09-04 19:24:26'),
('RED', NULL, NULL, 54, 'RED', '4_wheel', 'RED', 0x2e2e2f75706c6f6164732f74756e696e672e706e67, '2024-08-25 02:45:02', '1', 1200, '2024-09-04 19:24:26'),
('UWU', NULL, NULL, 54, 'UWU', '3_wheel', 'BLOCK', '', '2024-10-08 05:17:26', '1', 600, '2024-10-08 05:17:26'),
('W45WIW', NULL, NULL, 54, 'Inubishe', '4_wheel', 'rid', 0x2e2e2f75706c6f6164732f636d752e6a7067, '2024-08-25 02:43:20', '1', 1200, '2024-09-04 19:24:26'),
('waoj', NULL, NULL, 54, 'waoj', '4_wheel', 'waoj', '', '2024-08-25 03:01:48', '1', 1200, '2024-09-04 19:24:26'),
('wow', NULL, NULL, 54, 'wow', '4_wheel', 'wow', '', '2024-09-23 18:46:44', '1', 1200, '2024-09-23 18:46:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_plate_number` (`plate_number`),
  ADD UNIQUE KEY `unique_plate_token` (`plate_number`,`receipt_token`),
  ADD UNIQUE KEY `plate_number` (`plate_number`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`plate_number`,`slot_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_type` (`type`);

--
-- Indexes for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`plate_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=640;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `fk_vehicle_plate` FOREIGN KEY (`plate_number`) REFERENCES `vehicle` (`plate_number`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservations_plate_number` FOREIGN KEY (`plate_number`) REFERENCES `vehicle` (`plate_number`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
