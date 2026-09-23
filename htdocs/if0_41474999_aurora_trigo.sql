-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql110.infinityfree.com
-- Generation Time: Jun 24, 2026 at 11:09 AM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41474999_aurora_trigo`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `passenger_id` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `pickup_address` text NOT NULL,
  `pickup_lat` decimal(10,8) DEFAULT NULL,
  `pickup_lng` decimal(11,8) DEFAULT NULL,
  `dropoff_address` text NOT NULL,
  `dropoff_lat` decimal(10,8) DEFAULT NULL,
  `dropoff_lng` decimal(11,8) DEFAULT NULL,
  `distance_km` decimal(5,2) DEFAULT NULL,
  `fare` decimal(8,2) DEFAULT NULL,
  `payment_method` enum('cash','gcash') DEFAULT 'cash',
  `status` enum('pending','accepted','ongoing','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `booked_at` timestamp NULL DEFAULT current_timestamp(),
  `accepted_at` timestamp NULL DEFAULT NULL,
  `picked_up_at` timestamp NULL DEFAULT NULL,
  `dropped_off_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` enum('passenger','driver') DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `passenger_id`, `driver_id`, `pickup_address`, `pickup_lat`, `pickup_lng`, `dropoff_address`, `dropoff_lat`, `dropoff_lng`, `distance_km`, `fare`, `payment_method`, `status`, `notes`, `booked_at`, `accepted_at`, `picked_up_at`, `dropped_off_at`, `completed_at`, `cancelled_by`) VALUES
(40, 24, 23, 'Dicaloyungan, Sabang, Baler, Aurora, Central Luzon, 3200, Philippines', '15.74519902', '121.57535567', 'Aurora Provincial Capitol, Baler, Aurora', '15.75593370', '121.55050110', '2.92', '90.00', 'cash', 'completed', '', '2026-05-14 13:03:19', '2026-05-14 13:05:06', '2026-05-14 13:05:21', '2026-05-14 13:13:30', '2026-05-14 13:13:30', NULL),
(41, 24, 25, 'Dicaloyungan, Sabang, Baler, Aurora, Central Luzon, 3200, Philippines', '15.74490450', '121.57531175', 'Baler Public Market, Baler, Aurora', '15.75335030', '121.55884380', '2.00', '60.00', 'cash', 'completed', '', '2026-05-15 03:41:08', '2026-05-15 03:41:43', '2026-05-15 03:42:00', '2026-05-15 03:42:02', '2026-05-15 03:42:02', NULL),
(42, 24, NULL, 'Baler Public Market, Baler, Aurora', '15.75335030', '121.55884380', 'Baler Municipal Hall, Baler, Aurora', '15.75983420', '121.56297030', '0.85', '30.00', 'cash', 'pending', '', '2026-05-24 01:18:49', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `driver_details`
--

CREATE TABLE `driver_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `license_photo` varchar(255) DEFAULT NULL,
  `plate_number` varchar(20) DEFAULT NULL,
  `plate_photo` varchar(255) DEFAULT NULL,
  `tricycle_color` varchar(50) DEFAULT NULL,
  `tricycle_photo` varchar(255) DEFAULT NULL,
  `franchise_number` varchar(50) DEFAULT NULL,
  `is_online` tinyint(1) DEFAULT 0,
  `current_lat` decimal(10,8) DEFAULT NULL,
  `current_lng` decimal(11,8) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `driver_details`
--

INSERT INTO `driver_details` (`id`, `user_id`, `license_number`, `license_photo`, `plate_number`, `plate_photo`, `tricycle_color`, `tricycle_photo`, `franchise_number`, `is_online`, `current_lat`, `current_lng`) VALUES
(8, 25, NULL, 'licenses/lic_1778815855_6a06936f0ed74.png', NULL, 'plates/plate_1778815855_6a06936f0ef48.png', '', 'tricycles/tri_1778815855_6a06936f0f117.jpg', NULL, 0, NULL, NULL),
(7, 23, NULL, 'licenses/lic_1778760083_6a05b99399002.png', NULL, 'plates/plate_1778760083_6a05b99399236.jpg', '', 'tricycles/tri_1778760083_6a05b993998fa.png', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `booking_id`, `sender_id`, `message`, `sent_at`) VALUES
(10, 15, 5, 'saan kana?', '2026-03-29 08:53:14'),
(11, 15, 5, 'scam ang puta', '2026-03-29 08:53:39'),
(60, 41, 25, 'ðŸ›º SYSTEM: Your driver has arrived and picked you up! Heading to your destination.', '2026-05-15 03:42:00'),
(61, 41, 25, 'ðŸ SYSTEM: You have been dropped off! Thank you for riding with Aurora Tri-Go. Have a safe day! ðŸ˜Š', '2026-05-15 03:42:02'),
(59, 40, 23, 'ðŸ SYSTEM: You have been dropped off! Thank you for riding with Aurora Tri-Go. Have a safe day! ðŸ˜Š', '2026-05-14 13:13:30'),
(56, 40, 24, 'tantadooo', '2026-05-14 13:06:12'),
(57, 40, 24, 'titeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee', '2026-05-14 13:06:29'),
(58, 40, 24, 'titeeeeeeeeeeeeee', '2026-05-14 13:06:36'),
(55, 40, 23, 'ðŸ›º SYSTEM: Your driver has arrived and picked you up! Heading to your destination.', '2026-05-14 13:05:21');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `body`, `is_read`, `created_at`) VALUES
(3, 23, 'âš ï¸ Account Warning (1/3)', 'You have received an official warning: Conduct warning issued by admin\n\nYou have 1 out of 3 allowed warnings. Your account will be automatically banned if you receive 2 more warnings.', 0, '2026-05-14 14:00:12'),
(4, 24, 'âš ï¸ Account Warning (1/3)', 'You have received an official warning: Conduct warning issued by admin\n\nYou have 1 out of 3 allowed warnings. Your account will be automatically banned if you receive 2 more warnings.', 0, '2026-05-14 14:00:17'),
(5, 23, 'âš ï¸ Account Warning (2/3)', 'You have received an official warning: Conduct warning issued by admin\n\nYou have 2 out of 3 allowed warnings. Your account will be automatically banned if you receive 1 more warning.', 0, '2026-05-14 16:39:44'),
(6, 24, 'âš ï¸ Account Warning (2/3)', 'You have received an official warning: Conduct warning issued by admin\n\nYou have 2 out of 3 allowed warnings. Your account will be automatically banned if you receive 1 more warning.', 0, '2026-05-14 17:18:12'),
(7, 23, 'ðŸš« Account Banned â€“ Warning Limit Reached', 'You have received warning #3: Conduct warning issued by admin\n\nYou have been automatically banned for reaching 3 warnings. Please contact support if you believe this is a mistake.', 0, '2026-05-14 17:18:17'),
(8, 23, 'âœ… Account Reinstated', 'Your account ban has been lifted. You may now use the app again. Please follow our community guidelines.', 0, '2026-05-14 17:18:26'),
(9, 23, 'ðŸš« Account Banned â€“ Warning Limit Reached', 'You have received warning #4: Conduct warning issued by admin\n\nYou have been automatically banned for reaching 4 warnings. Please contact support if you believe this is a mistake.', 0, '2026-05-14 17:18:31'),
(10, 25, 'ðŸš« Account Banned', 'Your account has been banned. Reason: Violated Terms of Service', 0, '2026-05-15 06:37:09'),
(11, 25, 'âœ… Account Reinstated', 'Your account ban has been lifted. You may now use the app again. Please follow our community guidelines.', 0, '2026-05-20 00:13:00');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `rater_id` int(11) NOT NULL,
  `rated_id` int(11) NOT NULL,
  `score` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `payer_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `method` enum('cash','gcash') DEFAULT 'cash',
  `status` enum('pending','paid','refunded') DEFAULT 'pending',
  `transaction_ref` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `booking_id`, `payer_id`, `receiver_id`, `amount`, `method`, `status`, `transaction_ref`, `created_at`) VALUES
(1, 11, 3, 1, '45.00', 'cash', 'paid', NULL, '2026-03-29 06:12:59'),
(2, 17, 3, 1, '37.00', 'cash', 'paid', NULL, '2026-03-29 10:34:56'),
(3, 18, 3, 1, '37.00', 'cash', 'paid', NULL, '2026-03-29 10:36:36'),
(4, 19, 3, 1, '37.00', 'cash', 'paid', NULL, '2026-03-29 10:37:32'),
(5, 21, 7, 1, '30.00', 'cash', 'paid', NULL, '2026-03-29 11:01:14'),
(6, 23, 9, 1, '4398.00', 'cash', 'paid', NULL, '2026-03-30 04:08:54'),
(7, 25, 3, 1, '30.00', 'cash', 'paid', NULL, '2026-04-02 03:19:47'),
(8, 32, 3, 1, '90.00', 'cash', 'paid', NULL, '2026-04-24 14:13:23'),
(9, 33, 13, 15, '90.00', 'cash', 'paid', NULL, '2026-05-10 06:31:56'),
(10, 34, 14, 15, '30.00', 'cash', 'paid', NULL, '2026-05-10 06:32:21'),
(11, 38, 21, 22, '60.00', 'cash', 'paid', NULL, '2026-05-12 01:35:13'),
(12, 39, 21, 22, '90.00', 'cash', 'paid', NULL, '2026-05-12 01:42:26'),
(13, 40, 24, 23, '90.00', 'cash', 'paid', NULL, '2026-05-14 13:13:30'),
(14, 41, 24, 25, '60.00', 'cash', 'paid', NULL, '2026-05-15 03:42:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('driver','passenger','admin') NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `id_type` varchar(50) DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `id_photo` varchar(255) DEFAULT NULL,
  `gcash_number` varchar(20) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `warnings` int(11) NOT NULL DEFAULT 0,
  `ban_reason` varchar(255) DEFAULT NULL,
  `banned_at` datetime DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 5.0,
  `total_rides` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `password`, `role`, `profile_photo`, `address`, `birthdate`, `gender`, `id_type`, `id_number`, `id_photo`, `gcash_number`, `is_verified`, `is_active`, `warnings`, `ban_reason`, `banned_at`, `rating`, `total_rides`, `created_at`) VALUES
(24, 'Jairus', 'Leron', NULL, '09123456789', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'passenger', NULL, 'gesrgt', '2006-12-05', 'Male', NULL, NULL, 'ids/id_1778760169_6a05b9e913a76.png', NULL, 0, 1, 2, NULL, NULL, '5.0', 2, '2026-05-14 12:02:49'),
(12, 'Jayvee', 'Admin', 'jayveevelmonte19@gmail.com', '09812847469', 'a36aef5a11c4073fbe60314fc9df530a9d5f986533594d1f5190742ff9e0e408', 'admin', NULL, 'Aurora Tri-Go HQ', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, '5.0', 0, '2026-04-27 08:39:40'),
(25, 'Jayvee', 'Velmonte', NULL, '09876543212', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'driver', NULL, 'd dscxs', '2006-05-19', 'Female', NULL, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, '5.0', 1, '2026-05-15 03:30:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `driver_details`
--
ALTER TABLE `driver_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `driver_details`
--
ALTER TABLE `driver_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
