-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2023 at 04:10 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `avians`
--

-- --------------------------------------------------------

--
-- Table structure for table `auto_values`
--

CREATE TABLE `auto_values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inv_no` int(11) DEFAULT NULL,
  `cur_yr` int(11) DEFAULT NULL,
  `nxt_yr` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `auto_values`
--

INSERT INTO `auto_values` (`id`, `inv_no`, `cur_yr`, `nxt_yr`, `created_at`, `updated_at`) VALUES
(1, 14, 23, 24, '2023-04-05 03:00:17', '2023-05-06 03:37:32');

-- --------------------------------------------------------

--
-- Table structure for table `company_profile`
--

CREATE TABLE `company_profile` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `company_email` varchar(255) NOT NULL,
  `account_email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `pincode` int(11) NOT NULL,
  `pan_number` varchar(255) NOT NULL,
  `pan_file` varchar(255) DEFAULT NULL,
  `gst_number` varchar(255) NOT NULL,
  `gst_file` varchar(255) DEFAULT NULL,
  `iec_code` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `iso_certificate_number` varchar(255) NOT NULL,
  `iso_file` varchar(255) DEFAULT NULL,
  `primary_mobile` varchar(255) NOT NULL,
  `alternate_mobile` varchar(255) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `ifsc_code` varchar(255) NOT NULL,
  `bike_pkm_rate` decimal(10,2) DEFAULT NULL,
  `car_pkm_rate` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_profile`
--

INSERT INTO `company_profile` (`id`, `company_name`, `website`, `company_email`, `account_email`, `address`, `city`, `state`, `pincode`, `pan_number`, `pan_file`, `gst_number`, `gst_file`, `iec_code`, `logo`, `iso_certificate_number`, `iso_file`, `primary_mobile`, `alternate_mobile`, `account_name`, `account_number`, `branch`, `bank_name`, `ifsc_code`, `bike_pkm_rate`, `car_pkm_rate`, `created_at`, `updated_at`) VALUES
(1, 'AVIANS INNOVATIONS TECHNOLOGY PVT.LTD', ' www.avians.co.in', 'projects@avians.co.in', 'projects@avians.co.in', 'AVIANS INNOVATIONS TECHNOLOGY PVT.LTD: PLOT NO. C-22/6 PHASE 2, CHAKAN INDUSTRIAL AREA MIDC A/P BHAMBOLI, TAL - KHED, PUNE - 410501', 'PUNE', 'MAHARASHTRA', 410501, 'NA', '88.jpg', '27AAGCA2229N1ZI', '840.jpg', '4521685', 'logo.png', 'NA', NULL, '9552509475', '9552509477', 'NA', 'NA', 'NA', 'NA', 'NA', '4.00', '6.00', NULL, '2023-05-03 09:31:08');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `labour_payments`
--

CREATE TABLE `labour_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `u_id` varchar(255) NOT NULL,
  `so_id` varchar(255) NOT NULL,
  `p_desc` varchar(255) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_amnt` decimal(10,2) NOT NULL,
  `created_by` int(11) NOT NULL,
  `delete` tinyint(4) NOT NULL DEFAULT 0,
  `a_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_04_01_104853_create_users_table', 2),
(6, '2023_04_01_120504_create_company_profile_models_table', 3),
(7, '2023_04_02_164327_add_pan_adhar_to_users_table', 4),
(8, '2023_04_03_104056_create_s_o_models_table', 5),
(9, '2023_04_04_061121_create_labour_payment_models_table', 6),
(10, '2023_04_05_080542_create_auto_values_models_table', 7),
(11, '2023_04_05_083241_emp_number_to_users_table', 8),
(12, '2023_04_05_084352_photo_file_to_users_table', 9),
(13, '2023_04_11_110720_create_transfer_payment_models_table', 10),
(14, '2023_04_11_115431_rename_rcvd_amnt_in_transfer_payments_table', 11),
(15, '2023_04_12_065847_create_technician_expense_models_table', 12),
(16, '2023_04_12_110125_add_attachment_to_technician_expenses_table', 13),
(17, '2023_04_14_070746_add_acc_remark_to_technician_expenses_table', 14),
(18, '2023_04_17_165805_create_punch_in_out_models_table', 15),
(19, '2023_04_26_145331_add_lead_technician_to_sales_orders_table', 16),
(20, '2023_05_01_144603_add_regular_remark_to_punch_in_out_table', 17),
(21, '2023_05_01_160402_add_reg_admin_id_to_punch_in_out_table', 18),
(22, '2023_05_02_170308_add_sa_remark_to_technician_expenses_table', 19),
(23, '2023_05_03_145057_add_bike_pkm_to_company_profile_table', 20),
(24, '2023_05_05_054028_create_travel_expense_models_table', 21);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `punch_in_out`
--

CREATE TABLE `punch_in_out` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pin_u_id` varchar(255) DEFAULT NULL,
  `pin_so_id` varchar(255) DEFAULT NULL,
  `pin_date` date DEFAULT NULL,
  `pin_remark` varchar(255) DEFAULT NULL,
  `pin_latitude` varchar(255) DEFAULT NULL,
  `pin_longitude` varchar(255) DEFAULT NULL,
  `pin_img` varchar(255) DEFAULT NULL,
  `pout_u_id` varchar(255) DEFAULT NULL,
  `pout_so_id` varchar(255) DEFAULT NULL,
  `pout_date` date DEFAULT NULL,
  `pout_remark` varchar(255) DEFAULT NULL,
  `pout_work_desc` varchar(255) DEFAULT NULL,
  `pout_latitude` varchar(255) DEFAULT NULL,
  `pout_longitude` varchar(255) DEFAULT NULL,
  `pout_img` varchar(255) DEFAULT NULL,
  `regular_remark` varchar(255) DEFAULT NULL,
  `reg_status` varchar(255) DEFAULT NULL,
  `reg_admin_id` varchar(255) DEFAULT NULL,
  `a_id` int(11) DEFAULT NULL,
  `delete` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `punch_in_out`
--

INSERT INTO `punch_in_out` (`id`, `pin_u_id`, `pin_so_id`, `pin_date`, `pin_remark`, `pin_latitude`, `pin_longitude`, `pin_img`, `pout_u_id`, `pout_so_id`, `pout_date`, `pout_remark`, `pout_work_desc`, `pout_latitude`, `pout_longitude`, `pout_img`, `regular_remark`, `reg_status`, `reg_admin_id`, `a_id`, `delete`, `created_at`, `updated_at`) VALUES
(1, '16', '3', '2023-05-08', 'DDD', '18.511898', '73.7830746', '6458fdf7b7dcd.png', '16', '3', '2023-05-08', 'DDD', 'DDD', '18.511898', '73.7830746', '6458fe03226fa.png', NULL, NULL, NULL, 3, 0, '2023-05-08 08:19:43', '2023-05-08 08:19:55'),
(2, '3', '3', '2023-05-08', 'DDD', '18.511898', '73.7830746', '6458fdf7d7840.png', '3', '3', '2023-05-08', 'DDD', 'DDD', '18.511898', '73.7830746', '6458fe034d4c8.png', NULL, NULL, NULL, 3, 0, '2023-05-08 08:19:43', '2023-05-08 08:19:55'),
(3, '5', '3', '2023-05-08', 'DDD', '18.511898', '73.7830746', '6458fdf7d7840.png', '5', '3', '2023-05-08', 'DDD', 'DDD', '18.511898', '73.7830746', '6458fe033d1c5.png', 'DDD', 'Confirm', '2', 3, 0, '2023-05-08 08:19:43', '2023-05-08 08:21:27');

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `so_number` varchar(255) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `cp_name` varchar(255) NOT NULL,
  `cp_ph_no` varchar(255) NOT NULL,
  `labour` varchar(255) NOT NULL,
  `lead_technician` varchar(255) NOT NULL,
  `delete` tinyint(4) NOT NULL DEFAULT 0,
  `a_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_orders`
--

INSERT INTO `sales_orders` (`id`, `so_number`, `client_name`, `project_name`, `address`, `cp_name`, `cp_ph_no`, `labour`, `lead_technician`, `delete`, `a_id`, `created_at`, `updated_at`) VALUES
(1, 'OA-101', 'TATA', 'TATA GROUP', 'PUNE', 'ASHOK PAWAR', '8888888888', '15,5,3', '16', 0, 2, '2023-05-08 06:13:17', '2023-05-08 06:13:17'),
(2, 'OA-102', 'BIRLAS', 'BIRLA GROUP', 'NASHIK', 'RAJU KALE', '9856987458', '16,15', '3', 0, 2, '2023-05-08 06:14:19', '2023-05-08 06:14:19'),
(3, 'OA-103', 'ADANI', 'LOGITECH GROUP', 'MUMBAI', 'KUNAL TARU', '7854968596', '16,5,3', '15', 0, 2, '2023-05-08 06:15:18', '2023-05-08 06:15:18');

-- --------------------------------------------------------

--
-- Table structure for table `technician_expenses`
--

CREATE TABLE `technician_expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `so_id` varchar(255) NOT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `sa_id` varchar(255) DEFAULT NULL,
  `exp_type` varchar(255) NOT NULL,
  `exp_date` date DEFAULT NULL,
  `exp_desc` varchar(255) DEFAULT NULL,
  `acc_remark` varchar(255) DEFAULT NULL,
  `sa_remark` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `a_id` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Uncleared',
  `delete` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transfer_payments`
--

CREATE TABLE `transfer_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `u_id` int(11) NOT NULL,
  `so_id` varchar(255) NOT NULL,
  `p_date` date DEFAULT NULL,
  `p_desc` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `a_id` int(11) DEFAULT NULL,
  `delete` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `travel_expenses`
--

CREATE TABLE `travel_expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ad_id` int(11) DEFAULT NULL,
  `sa_id` int(11) DEFAULT NULL,
  `mode_travel` varchar(255) NOT NULL,
  `from_location` varchar(255) NOT NULL,
  `to_location` varchar(255) NOT NULL,
  `total_km` decimal(10,2) NOT NULL,
  `travel_date` date NOT NULL,
  `travel_desc` varchar(255) DEFAULT NULL,
  `ad_remark` varchar(255) DEFAULT NULL,
  `sa_remark` varchar(255) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `travel_amount` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Uncleared',
  `a_id` int(11) DEFAULT NULL,
  `delete` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `emp_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `pan_number` varchar(255) NOT NULL,
  `pan_file` varchar(255) DEFAULT NULL,
  `aadhar_number` varchar(255) NOT NULL,
  `aadhar_file` varchar(255) DEFAULT NULL,
  `photo_file` varchar(255) DEFAULT NULL,
  `delete` tinyint(4) NOT NULL DEFAULT 0,
  `is_active` tinyint(4) NOT NULL DEFAULT 0,
  `a_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `emp_number`, `email`, `mobile`, `password`, `role`, `pan_number`, `pan_file`, `aadhar_number`, `aadhar_file`, `photo_file`, `delete`, `is_active`, `a_id`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'P-001', 'omkar9497@gmail.com', '8551071325', '$2y$10$Aoz3QRjSoX4LjhZJeOrgM.qd0Ag6uMa2yUHpRBS4PlSsFzrKOqAxu', '0', '', NULL, '', NULL, NULL, 0, 0, 0, NULL, '2023-04-06 21:31:47'),
(2, 'Admin', 'P-002', 'omkar9497@gmail.com', '7744886960', '$2y$10$Aoz3QRjSoX4LjhZJeOrgM.qd0Ag6uMa2yUHpRBS4PlSsFzrKOqAxu', '1', 'ASDF12563W', NULL, '789456123569', NULL, NULL, 0, 0, 1, '2023-04-02 12:00:18', '2023-04-06 23:09:56'),
(3, 'RUSHI TAMBE', 'P-003', 'rushi@gmail.com', '9834783216', '$2y$10$Aoz3QRjSoX4LjhZJeOrgM.qd0Ag6uMa2yUHpRBS4PlSsFzrKOqAxu', '3', 'ASDF12563W', NULL, '741258632586', NULL, NULL, 0, 0, 2, '2023-04-02 13:09:18', '2023-04-24 23:53:30'),
(4, 'ACCOUNTANT', 'P-004', 'Falguni7008jagtap@gmail.com', '7888077008', '$2y$10$Aoz3QRjSoX4LjhZJeOrgM.qd0Ag6uMa2yUHpRBS4PlSsFzrKOqAxu', '2', 'ASDF12563W', NULL, '741236985556', NULL, NULL, 0, 0, 1, '2023-04-03 01:33:04', '2023-04-06 02:32:09'),
(5, 'AJAY PAWAR', 'P-005', 'ajaypawar@gmail.com', '8265015714', '$2y$10$Aoz3QRjSoX4LjhZJeOrgM.qd0Ag6uMa2yUHpRBS4PlSsFzrKOqAxu', '3', 'ASDF12563W', NULL, '741236985556', NULL, NULL, 0, 0, 2, '2023-04-03 01:37:47', '2023-04-24 23:49:17'),
(11, 'DEMOS', 'P-009', 'demmo@gmail.com', '7896547899', '$2y$10$v82yWP.6RNG4B7Q6h/sRLeZgQMk3T58bI3PPoTTxXNW7C4UDPkFtC', '2', 'ASDF12563O', '45.jpg', '741236985559', '896.jpg', '121.jpg', 0, 0, 1, '2023-04-06 00:52:48', '2023-04-07 04:55:54'),
(12, 'DEMO2', 'P-010', 'demo2@gmail.com', '7845698574', '$2y$10$SJ6AB8.MzAmIqiPmCJP9kuCy/D2.kGS4vMlkF71Zs1mG1RpCs9NvW', '1', 'ASDF12563W', NULL, '741236985556', NULL, NULL, 1, 1, 1, '2023-04-06 02:38:33', '2023-04-06 23:28:45'),
(13, 'DEMO3', 'P-011', 'demo3@gmail.com', '7878787878', '$2y$10$tFhiQmZb6G.uQHE18HPDiuMYVS4fn4Oz6smgw145A8aqH8ShLZ/Di', '2', 'ASDF12563W', '315.jpg', '741236985556', '225.jpg', '64.jpg', 0, 0, 1, '2023-04-06 02:45:09', '2023-04-07 04:21:25'),
(14, 'AJIT KALEKAR', 'P-012', 'ajit@avians.com', '9970992879', '$2y$10$xbywbastabNM2UAcCjJsxe9j8/rh3VVKGfGx096wrFXw6INffremq', '2', 'ASDF12563W', '37.jpg', '741236985556', '701.jpg', '264.jpg', 0, 0, 1, '2023-04-08 01:38:19', '2023-04-25 09:00:28'),
(15, 'SAMEER PATIL', 'P-013', 'sameer@gmaiil.com', '9874568925', '$2y$10$Aoz3QRjSoX4LjhZJeOrgM.qd0Ag6uMa2yUHpRBS4PlSsFzrKOqAxu', '3', 'ASDF12563W', '585.jpg', '741236985556', '392.jpg', '238.jpg', 0, 0, 2, '2023-04-24 23:56:18', '2023-04-25 01:52:33'),
(16, 'SAGAR PAWAR', 'P-014', 'sagar@gmail.com', '8888888888', '$2y$10$FSBe.5xJoOOEL5tKdIjSjuIYKTl22Efxi/X0XQcxUmV4lxZRfmRFO', '3', 'ASDF12563W', '121.jpg', '741236985556', '125.jpg', '714.jpg', 0, 0, 2, '2023-05-06 03:37:32', '2023-05-06 03:37:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auto_values`
--
ALTER TABLE `auto_values`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_profile`
--
ALTER TABLE `company_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `labour_payments`
--
ALTER TABLE `labour_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `punch_in_out`
--
ALTER TABLE `punch_in_out`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `technician_expenses`
--
ALTER TABLE `technician_expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transfer_payments`
--
ALTER TABLE `transfer_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `travel_expenses`
--
ALTER TABLE `travel_expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_mobile_unique` (`mobile`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auto_values`
--
ALTER TABLE `auto_values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_profile`
--
ALTER TABLE `company_profile`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `labour_payments`
--
ALTER TABLE `labour_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `punch_in_out`
--
ALTER TABLE `punch_in_out`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `technician_expenses`
--
ALTER TABLE `technician_expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transfer_payments`
--
ALTER TABLE `transfer_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `travel_expenses`
--
ALTER TABLE `travel_expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
