-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2023 at 09:56 AM
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
(1, 12, 23, 24, '2023-04-05 03:00:17', '2023-04-08 01:38:19');

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_profile`
--

INSERT INTO `company_profile` (`id`, `company_name`, `website`, `company_email`, `account_email`, `address`, `city`, `state`, `pincode`, `pan_number`, `pan_file`, `gst_number`, `gst_file`, `iec_code`, `logo`, `iso_certificate_number`, `iso_file`, `primary_mobile`, `alternate_mobile`, `account_name`, `account_number`, `branch`, `bank_name`, `ifsc_code`, `created_at`, `updated_at`) VALUES
(1, 'AVIANS INNOVATIONS TECHNOLOGY PVT.LTD', ' www.avians.co.in', 'projects@avians.co.in', 'projects@avians.co.in', 'AVIANS INNOVATIONS TECHNOLOGY PVT.LTD: PLOT NO. C-22/6 PHASE 2, CHAKAN INDUSTRIAL AREA MIDC A/P BHAMBOLI, TAL - KHED, PUNE - 410501', 'PUNE', 'MAHARASHTRA', 410501, 'NA', '88.jpg', '27AAGCA2229N1ZI', '840.jpg', '4521685', 'logo.png', 'NA', NULL, '9552509475', '9552509477', 'NA', 'NA', 'NA', 'NA', 'NA', NULL, '2023-04-08 01:22:29');

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

--
-- Dumping data for table `labour_payments`
--

INSERT INTO `labour_payments` (`id`, `u_id`, `so_id`, `p_desc`, `payment_date`, `payment_amnt`, `created_by`, `delete`, `a_id`, `created_at`, `updated_at`) VALUES
(1, '3', '3', 'CASH PAYMENT', '2023-04-11', '1000.00', 4, 0, 4, '2023-04-11 02:57:08', '2023-04-11 02:57:08'),
(2, '5', '3,2,1', 'ONLINE', '2023-04-11', '5000.00', 4, 0, 4, '2023-04-11 02:58:39', '2023-04-11 02:58:39'),
(3, '3', '3,2', 'CASH', '2023-04-11', '1000.00', 4, 0, 4, '2023-04-11 02:59:15', '2023-04-11 02:59:15');

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
(17, '2023_04_14_070746_add_acc_remark_to_technician_expenses_table', 14);

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
  `delete` tinyint(4) NOT NULL DEFAULT 0,
  `a_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_orders`
--

INSERT INTO `sales_orders` (`id`, `so_number`, `client_name`, `project_name`, `address`, `cp_name`, `cp_ph_no`, `labour`, `delete`, `a_id`, `created_at`, `updated_at`) VALUES
(1, 'OA-12364', 'TATA', 'TATA GROUP', 'NASHIK', 'KUNAL', '7898654512', '3', 0, 1, '2023-04-11 02:54:35', '2023-04-11 02:54:35'),
(2, 'OA-12365', 'BIRLAS', 'BIRLA GROUPS', 'MUMBAI', 'ASHOK', '7485968574', '3', 0, 1, '2023-04-11 02:55:14', '2023-04-11 02:55:14'),
(3, 'OA-12366', 'ADANI', 'ADANI GROUP', 'PUNE', 'RAM', '8956457898', '5,3', 0, 1, '2023-04-11 02:55:52', '2023-04-11 02:55:52');

-- --------------------------------------------------------

--
-- Table structure for table `technician_expenses`
--

CREATE TABLE `technician_expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `so_id` varchar(255) NOT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `exp_type` varchar(255) NOT NULL,
  `exp_date` date DEFAULT NULL,
  `exp_desc` varchar(255) DEFAULT NULL,
  `acc_remark` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `a_id` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Uncleared',
  `delete` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `technician_expenses`
--

INSERT INTO `technician_expenses` (`id`, `so_id`, `acc_id`, `exp_type`, `exp_date`, `exp_desc`, `acc_remark`, `amount`, `attachment`, `a_id`, `status`, `delete`, `created_at`, `updated_at`) VALUES
(1, '3,2,1', NULL, 'hotel', '2023-04-12', 'JTRTRTR', NULL, '5000.00', '6436f7b568776.jpg', 3, 'Uncleared', 0, '2023-04-12 05:27:45', '2023-04-12 12:55:57'),
(2, '3,2', NULL, 'hotel', '2023-04-12', 'ONLINE', NULL, '50000.00', '6436f855be9fd.jpg', 3, 'Uncleared', 0, '2023-04-12 05:28:56', '2023-04-12 12:58:37'),
(3, '3,2', NULL, 'labour', '2023-04-12', 'HTHTH', NULL, '50070.00', '6436f830d0ecd.jpg', 3, 'Uncleared', 0, '2023-04-12 06:34:23', '2023-04-12 12:58:00'),
(4, '3,2', NULL, 'labour', '2023-04-11', 'ASDFGD', NULL, '450000.00', NULL, 3, 'Uncleared', 0, '2023-04-12 09:39:07', '2023-04-12 09:39:07'),
(5, '1', 4, 'other', '2023-04-12', 'DEMO', 'DHJFJF J', '0.00', '6436f8833aee3.jpg', 3, 'Cancelled', 0, '2023-04-12 12:59:23', '2023-04-14 04:25:30'),
(6, '2', 4, 'labour', '2023-04-13', 'DEMOO', 'RGRGRE', '5000.00', '64379f55aa64a.jpg', 3, 'Cleared', 0, '2023-04-13 00:50:43', '2023-04-14 04:07:15'),
(7, '3', NULL, 'Material Purchase', '2023-04-14', 'SRGREGRE', NULL, '1000.00', '64395bf82d03f.jpg', 3, 'Uncleared', 0, '2023-04-14 08:28:16', '2023-04-14 08:28:16'),
(8, '2', NULL, 'Material Purchase', '2023-04-15', 'FVFDVfd', NULL, '1000.00', '643ac184475f3.jpg', 3, 'Uncleared', 0, '2023-04-15 09:53:48', '2023-04-15 09:53:48');

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

--
-- Dumping data for table `transfer_payments`
--

INSERT INTO `transfer_payments` (`id`, `u_id`, `so_id`, `p_date`, `p_desc`, `amount`, `a_id`, `delete`, `created_at`, `updated_at`) VALUES
(1, 5, '3,1', '2023-04-11', 'CASHD', '50050.00', 3, 0, '2023-04-11 07:08:04', '2023-04-11 07:40:31'),
(2, 5, '2', '2023-04-11', 'FESFF', '5000.00', 3, 0, '2023-04-11 07:41:06', '2023-04-11 07:41:06'),
(3, 5, '2', '2023-04-12', 'SACSFCS', '5000.00', 3, 1, '2023-04-12 09:44:09', '2023-04-12 09:52:39'),
(4, 5, '2', '2023-04-14', 'GGREGG', '4000.00', 3, 0, '2023-04-14 08:21:26', '2023-04-14 08:21:26');

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
(2, 'Admin', 'P-002', 'omkar9497@gmail.com', '7744886960', '$2y$10$v9kz/RNrhh9LC8z3nQn40Ov5nGPKT9cTXMyLzdHUcq/b7m0jlQ84W', '1', 'ASDF12563W', NULL, '789456123569', NULL, NULL, 0, 0, 1, '2023-04-02 12:00:18', '2023-04-06 23:09:56'),
(3, 'labour 1', 'P-003', 'rushi@gmail.com', '9834783216', '$2y$10$99sdvNc.Lt3SSRz5N5VxBeO9QtbFpNnnzhifMbraj01gWcYGAmVSi', '3', 'ASDF12563W', NULL, '741258632586', NULL, NULL, 0, 0, 2, '2023-04-02 13:09:18', '2023-04-02 13:09:18'),
(4, 'ACCOUNTANT', 'P-004', 'Falguni7008jagtap@gmail.com', '7888077008', '$2y$10$Aoz3QRjSoX4LjhZJeOrgM.qd0Ag6uMa2yUHpRBS4PlSsFzrKOqAxu', '2', 'ASDF12563W', NULL, '741236985556', NULL, NULL, 0, 0, 1, '2023-04-03 01:33:04', '2023-04-06 02:32:09'),
(5, 'labour 2', 'P-005', 'Falguni7008jagtap@gmail.com', '8265015714', '$2y$10$99sdvNc.Lt3SSRz5N5VxBeO9QtbFpNnnzhifMbraj01gWcYGAmVSi', '3', 'ASDF12563W', NULL, '741236985556', NULL, NULL, 0, 0, 2, '2023-04-03 01:37:47', '2023-04-04 05:39:21'),
(11, 'DEMOS', 'P-009', 'demmo@gmail.com', '7896547899', '$2y$10$v82yWP.6RNG4B7Q6h/sRLeZgQMk3T58bI3PPoTTxXNW7C4UDPkFtC', '2', 'ASDF12563O', '45.jpg', '741236985559', '896.jpg', '121.jpg', 0, 0, 1, '2023-04-06 00:52:48', '2023-04-07 04:55:54'),
(12, 'DEMO2', 'P-010', 'demo2@gmail.com', '7845698574', '$2y$10$SJ6AB8.MzAmIqiPmCJP9kuCy/D2.kGS4vMlkF71Zs1mG1RpCs9NvW', '1', 'ASDF12563W', NULL, '741236985556', NULL, NULL, 1, 1, 1, '2023-04-06 02:38:33', '2023-04-06 23:28:45'),
(13, 'DEMO3', 'P-011', 'demo3@gmail.com', '7878787878', '$2y$10$tFhiQmZb6G.uQHE18HPDiuMYVS4fn4Oz6smgw145A8aqH8ShLZ/Di', '2', 'ASDF12563W', '315.jpg', '741236985556', '225.jpg', '64.jpg', 0, 0, 1, '2023-04-06 02:45:09', '2023-04-07 04:21:25'),
(14, 'AJIT KALEKAR', 'P-012', 'ajit@avians.com', '9970992879', '$2y$10$MlE8YOs31n.jGBTx1C9adu0nhZoRJfIsRdPiTDCSruAMqBOnQdRBS', '2', 'ASDF12563W', '37.jpg', '741236985556', '701.jpg', '264.jpg', 0, 0, 1, '2023-04-08 01:38:19', '2023-04-10 10:45:40');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `technician_expenses`
--
ALTER TABLE `technician_expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `transfer_payments`
--
ALTER TABLE `transfer_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
