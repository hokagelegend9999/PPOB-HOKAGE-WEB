-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 10, 2025 at 11:38 AM
-- Server version: 10.11.14-MariaDB-cll-lve
-- PHP Version: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hoky4323_hokage_ppob`
--

-- --------------------------------------------------------

--
-- Table structure for table `bonuses`
--

CREATE TABLE `bonuses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bonus_name` varchar(255) NOT NULL,
  `status` enum('active','used','expired') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reff_id` varchar(255) DEFAULT NULL,
  `gateway_reference` varchar(255) DEFAULT NULL,
  `package_name` varchar(255) NOT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `transaction_status` varchar(50) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status_transaksi` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `user_id`, `reff_id`, `gateway_reference`, `package_name`, `service_name`, `amount`, `transaction_status`, `payment_method`, `status`, `type`, `price`, `status_transaksi`, `description`, `created_at`) VALUES
(1, 40, NULL, NULL, 'Top Up Saldo', NULL, NULL, 'pending', 'QRIS', NULL, '', 10000.00, NULL, NULL, '2025-09-05 13:29:21'),
(2, 40, NULL, NULL, 'Top Up Saldo', NULL, NULL, 'pending', 'QRIS', NULL, '', 12000.00, NULL, NULL, '2025-09-05 13:29:35'),
(3, 40, NULL, 'T4504526726032ISNMN', 'Top Up Saldo', NULL, NULL, 'pending', 'QRIS', NULL, '', 12000.00, NULL, NULL, '2025-09-05 13:30:37'),
(4, 40, NULL, NULL, '', NULL, 12000.00, '', '', 'pending', 'topup', 0.00, NULL, 'Top Up Saldo via QRIS', '2025-09-09 17:40:12'),
(5, 40, NULL, NULL, '', NULL, 12000.00, '', '', 'pending', 'topup', 0.00, NULL, 'Top Up Saldo via QRIS', '2025-09-09 17:40:19'),
(6, 40, 'TOPUP-40-1757439754', NULL, 'Top Up Saldo', NULL, NULL, 'pending', 'QRIS', NULL, '', 12000.00, NULL, NULL, '2025-09-09 17:42:34'),
(7, 40, 'TOPUP-40-1757467292', 'T4504526832902LDSP2', 'Top Up Saldo', NULL, NULL, 'pending', 'QRIS', NULL, '', 12000.00, NULL, NULL, '2025-09-10 01:21:32'),
(8, 43, NULL, NULL, '', NULL, 20000.00, '', '', 'completed', 'topup', 0.00, NULL, 'Top up manual oleh admin (hokage_ppob)', '2025-09-10 01:55:06'),
(9, 43, NULL, NULL, '', NULL, -2000.00, '', '', 'completed', 'purchase', 0.00, NULL, 'Biaya Jasa Pembelian: Standard', '2025-09-10 01:59:20'),
(10, 43, NULL, '6815493', '', NULL, 20000.00, '', '', 'pending', 'purchase', 0.00, NULL, 'Standard', '2025-09-10 01:59:20'),
(11, 44, NULL, NULL, '', NULL, 20000.00, '', '', 'completed', 'topup', 0.00, NULL, 'Top up manual oleh admin (hokage_ppob)', '2025-09-10 02:35:30'),
(12, 44, NULL, NULL, '', NULL, -2000.00, '', '', 'completed', 'purchase', 0.00, NULL, 'Biaya Jasa Pembelian: Vidio', '2025-09-10 02:41:06'),
(13, 44, NULL, '7750152', '', NULL, 25000.00, '', '', 'pending', 'purchase', 0.00, NULL, 'Vidio', '2025-09-10 02:41:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `auth_provider` varchar(50) NOT NULL DEFAULT 'local',
  `phone_number` varchar(20) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `subscriber_id` varchar(255) DEFAULT NULL,
  `api_access_token` text DEFAULT NULL,
  `api_id_token` text DEFAULT NULL,
  `api_refresh_token` text DEFAULT NULL,
  `api_token_expires_at` timestamp NULL DEFAULT NULL,
  `otp` varchar(10) DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `saldo` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `google_id`, `auth_provider`, `phone_number`, `role`, `profile_picture`, `status`, `subscriber_id`, `api_access_token`, `api_id_token`, `api_refresh_token`, `api_token_expires_at`, `otp`, `otp_expires_at`, `created_at`, `saldo`) VALUES
(40, 'hokage_ppob', 'faridaumiabi@gmail.com', '$2y$10$vnof.kYjLFFonp13O9gGFuHIzU.SmMBDcIKie5yQpxEIT6B8mJzRS', NULL, 'local', '6287726917005', 'admin', 'user_40_1757438146.jfif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 07:10:26', 46000.00),
(41, 'eliot12', 'sweetbonitta@coffeepancakewafflebacon.com', '$2y$10$k3.puoVU74VkBrU5fSf3cu6YvVuS.aP8IM8ZNG.b98bdNT4i66s86', NULL, 'local', '6282288419778', 'user', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 07:57:00', 0.00),
(42, 'Tripay', 'tripasy@gmail.com', '$2y$10$poWagR0Qmvz2wRO2rWR2i.0LisY8.JVOLzALVcVEs5ymoCt8/i84u', NULL, 'local', '081234567', 'user', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 13:58:14', 0.00),
(43, 'kegiatan', 'khd.community87@gmail.com', '$2y$10$jtpz1W5jVLGwhdD1NgRoPuaPko1uJvTgqHvArh6QSzto8An8gtVSO', NULL, 'local', '6285966444051', 'user', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-05 02:08:01', 18000.00),
(44, 'amifi', 'storeamifi@gmail.com', '$2y$10$W59eAUhLhSoQEMrR.2nalebcqouzOL1O6ujlACobuPv53W8xB8zYK', NULL, 'local', '0859106609838', 'user', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-10 02:31:49', 18000.00),
(45, 'Zann', 'znzy728@gmail.com', '$2y$10$BUVMhKz5uMPg9X28YZl1ZOvr1cxkcKawz8mgTlG.Xmb5wknzYYoyK', NULL, 'local', '081299594861', 'user', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-10 02:32:37', 0.00),
(46, 'petedunham', 'ontaarab081@gmail.com', '$2y$10$cmj6JFakbqWVckP2wzLrNePbF4PladU.SyPDGTXMN3FPuAyHWoYGm', NULL, 'local', '085931408790', 'user', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-10 03:55:56', 0.00),
(47, 'Rizky', 'riskydwidata@gmail.com', '$2y$10$DKFwk/7nGZfeEmQq29FxDe6jn9XBJLq6vm3mL6F.zFtsv42GVQX7K', NULL, 'local', '082257539604', 'user', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-10 04:01:46', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `user_balances`
--

CREATE TABLE `user_balances` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bonuses`
--
ALTER TABLE `bonuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD UNIQUE KEY `reff_id` (`reff_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_balances`
--
ALTER TABLE `user_balances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bonuses`
--
ALTER TABLE `bonuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `user_balances`
--
ALTER TABLE `user_balances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bonuses`
--
ALTER TABLE `bonuses`
  ADD CONSTRAINT `bonuses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_balances`
--
ALTER TABLE `user_balances`
  ADD CONSTRAINT `user_balances_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
