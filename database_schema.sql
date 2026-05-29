-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 14, 2025 at 09:01 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `staging`
--

-- --------------------------------------------------------

--
-- Table structure for table `abutments`
--

DROP TABLE IF EXISTS `abutments`;
CREATE TABLE IF NOT EXISTS `abutments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `abutments_delivery`
--

DROP TABLE IF EXISTS `abutments_delivery`;
CREATE TABLE IF NOT EXISTS `abutments_delivery` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) NOT NULL,
  `job_id` bigint(20) NOT NULL,
  `abutment_id` bigint(20) DEFAULT NULL,
  `implant_id` bigint(20) DEFAULT NULL,
  `ordered_by` bigint(20) DEFAULT NULL,
  `ordered_on` timestamp NULL DEFAULT NULL,
  `qty` bigint(20) NOT NULL,
  `remaining_qty` bigint(20) NOT NULL,
  `units` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `abutments_receive_logs`
--

DROP TABLE IF EXISTS `abutments_receive_logs`;
CREATE TABLE IF NOT EXISTS `abutments_receive_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `abut_delivery_id` bigint(20) NOT NULL,
  `qty` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_action_created_at_index` (`action`,`created_at`),
  KEY `audit_logs_user_id_index` (`user_id`),
  KEY `audit_logs_subject_type_index` (`subject_type`),
  KEY `audit_logs_subject_id_index` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

DROP TABLE IF EXISTS `banks`;
CREATE TABLE IF NOT EXISTS `banks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bank_abbrev` varchar(100) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `builds`
--

DROP TABLE IF EXISTS `builds`;
CREATE TABLE IF NOT EXISTS `builds` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `set_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `device_used` varchar(20) NOT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_builds_device_status` (`device_used`,`finished_at`,`started_at`),
  KEY `idx_builds_device` (`device_used`),
  KEY `idx_builds_set_at` (`set_at`),
  KEY `idx_builds_started_at` (`started_at`),
  KEY `idx_builds_finished_at` (`finished_at`),
  KEY `idx_builds_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cameras`
--

DROP TABLE IF EXISTS `cameras`;
CREATE TABLE IF NOT EXISTS `cameras` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cases`
--

DROP TABLE IF EXISTS `cases`;
CREATE TABLE IF NOT EXISTS `cases` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `case_id` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `patient_name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `initial_delivery_date` datetime DEFAULT NULL,
  `actual_delivery_date` timestamp NULL DEFAULT NULL,
  `delivered_to_client` tinyint(4) NOT NULL DEFAULT 0,
  `voucher_printed_by` bigint(20) DEFAULT NULL,
  `voucher_recieved_by` bigint(20) DEFAULT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `finisher` bigint(20) DEFAULT NULL,
  `impression_type` bigint(20) UNSIGNED DEFAULT NULL,
  `locked` tinyint(4) NOT NULL DEFAULT 0,
  `notification_sent` tinyint(4) DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_returned` tinyint(4) NOT NULL DEFAULT 0,
  `is_a_remake` tinyint(4) NOT NULL DEFAULT 0,
  `is_rejected` tinyint(4) DEFAULT NULL,
  `contains_modification` tinyint(4) NOT NULL DEFAULT 0,
  `first_case_if_repeated` bigint(20) DEFAULT NULL,
  `delivered_in_box` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `orders_created_by_foreign` (`created_by`),
  KEY `idx_cases_doctor` (`doctor_id`),
  KEY `idx_cases_deleted_at` (`deleted_at`),
  KEY `idx_cases_doctor_deleted` (`doctor_id`,`deleted_at`),
  KEY `idx_cases_delivery_dates` (`actual_delivery_date`,`initial_delivery_date`),
  KEY `idx_cases_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_logs`
--

DROP TABLE IF EXISTS `case_logs`;
CREATE TABLE IF NOT EXISTS `case_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `case_id` bigint(20) UNSIGNED NOT NULL,
  `stage` float NOT NULL,
  `is_completion` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_logs_order_id_foreign` (`case_id`),
  KEY `order_logs_user_id_foreign` (`user_id`),
  KEY `idx_case_logs_case` (`case_id`,`created_at`),
  KEY `idx_case_logs_user` (`user_id`,`created_at`),
  KEY `idx_case_logs_stage` (`stage`,`is_completion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_tags`
--

DROP TABLE IF EXISTS `case_tags`;
CREATE TABLE IF NOT EXISTS `case_tags` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) NOT NULL,
  `tag_id` bigint(20) NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `phone` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `clinic_phone` varchar(255) DEFAULT NULL,
  `address` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `balance` double DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `doc_password` varchar(191) DEFAULT NULL,
  `clinic_password` varchar(191) DEFAULT NULL,
  `doc_notification_token` varchar(255) DEFAULT NULL,
  `clinic_notification_token` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `opening_balance` decimal(8,2) DEFAULT NULL,
  `opening_balance_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_username_unique` (`username`),
  UNIQUE KEY `clients_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_discounts`
--

DROP TABLE IF EXISTS `client_discounts`;
CREATE TABLE IF NOT EXISTS `client_discounts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `discount` double NOT NULL,
  `type` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discounts_doctor_id_foreign` (`client_id`),
  KEY `discounts_material_id_foreign` (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_materials`
--

DROP TABLE IF EXISTS `client_materials`;
CREATE TABLE IF NOT EXISTS `client_materials` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

DROP TABLE IF EXISTS `colors`;
CREATE TABLE IF NOT EXISTS `colors` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
CREATE TABLE IF NOT EXISTS `devices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `sorting_order` tinyint(4) NOT NULL DEFAULT 0,
  `img` varchar(255) DEFAULT NULL,
  `hidden` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_logs`
--

DROP TABLE IF EXISTS `device_logs`;
CREATE TABLE IF NOT EXISTS `device_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) NOT NULL,
  `sintered_with` bigint(20) DEFAULT NULL,
  `Impressed_with` bigint(20) DEFAULT NULL,
  `zircon_milled_with` bigint(20) DEFAULT NULL,
  `emax_wax_milled_with` bigint(20) DEFAULT NULL,
  `printed_with` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
CREATE TABLE IF NOT EXISTS `discounts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) UNSIGNED NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `discount` double NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discounts_doctor_id_foreign` (`case_id`),
  KEY `discounts_material_id_foreign` (`reason`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edit_logs`
--

DROP TABLE IF EXISTS `edit_logs`;
CREATE TABLE IF NOT EXISTS `edit_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unit_num` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `type` bigint(20) UNSIGNED NOT NULL,
  `color` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `style` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `is_modification` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `unit_failed` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `original_job_id` bigint(20) UNSIGNED NOT NULL,
  `note_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `failed_jobs_material_id_foreign` (`material_id`),
  KEY `failed_jobs_type_foreign` (`type`),
  KEY `failed_jobs_order_id_foreign` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failure_causes`
--

DROP TABLE IF EXISTS `failure_causes`;
CREATE TABLE IF NOT EXISTS `failure_causes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `text` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failure_logs`
--

DROP TABLE IF EXISTS `failure_logs`;
CREATE TABLE IF NOT EXISTS `failure_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) NOT NULL,
  `original_case_id` bigint(20) DEFAULT NULL,
  `failure_type` tinyint(4) DEFAULT NULL,
  `cause_id` bigint(20) NOT NULL,
  `explanation` varchar(255) DEFAULT NULL,
  `done_by` bigint(20) NOT NULL,
  `old_delivery_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rate` int(11) NOT NULL,
  `note` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `patient_name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `case_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) NOT NULL,
  `path` varchar(255) NOT NULL,
  `added_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_media`
--

DROP TABLE IF EXISTS `gallery_media`;
CREATE TABLE IF NOT EXISTS `gallery_media` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `implants`
--

DROP TABLE IF EXISTS `implants`;
CREATE TABLE IF NOT EXISTS `implants` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `impression_types`
--

DROP TABLE IF EXISTS `impression_types`;
CREATE TABLE IF NOT EXISTS `impression_types` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `internal_transactions`
--

DROP TABLE IF EXISTS `internal_transactions`;
CREATE TABLE IF NOT EXISTS `internal_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_user` bigint(20) UNSIGNED DEFAULT NULL,
  `to_user` bigint(20) UNSIGNED DEFAULT NULL,
  `from_doc` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` double NOT NULL,
  `date` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `is_collected` tinyint(1) NOT NULL DEFAULT 0,
  `to_bank` int(11) DEFAULT NULL,
  `payment_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL DEFAULT 0,
  `amount` double NOT NULL,
  `amount_before_discount` double DEFAULT NULL,
  `case_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `discount_title` varchar(255) DEFAULT NULL,
  `date_applied` timestamp NULL DEFAULT NULL,
  `rejection_invoice` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unit_num` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `type` bigint(20) UNSIGNED NOT NULL,
  `color` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `style` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `abutment` bigint(20) DEFAULT NULL,
  `implant` bigint(20) DEFAULT NULL,
  `case_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) DEFAULT NULL,
  `stage` int(11) DEFAULT NULL,
  `assignee` tinyint(4) DEFAULT NULL,
  `delivery_accepted` bigint(20) DEFAULT NULL,
  `milling_lab` bigint(20) DEFAULT NULL,
  `unit_price` double DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `unit_failed` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_rejection` tinyint(4) NOT NULL DEFAULT 0,
  `has_been_rejected` tinyint(4) NOT NULL DEFAULT 0,
  `is_repeat` tinyint(4) NOT NULL DEFAULT 0,
  `is_modification` tinyint(4) NOT NULL DEFAULT 0,
  `repeated_job_id` bigint(20) DEFAULT NULL,
  `modified_job_id` bigint(20) DEFAULT NULL,
  `rejected_job_id` bigint(20) DEFAULT NULL,
  `original_job_id` bigint(20) DEFAULT NULL,
  `is_redo` tinyint(4) DEFAULT 0,
  `redone_job_id` bigint(20) DEFAULT NULL,
  `is_set` tinyint(4) DEFAULT NULL,
  `device_id` bigint(20) DEFAULT NULL,
  `is_active` bigint(20) DEFAULT NULL,
  `milling_build_id` bigint(20) UNSIGNED DEFAULT NULL,
  `printing_build_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pressing_build_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sintering_build_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_material_id_foreign` (`material_id`),
  KEY `jobs_type_foreign` (`type`),
  KEY `jobs_order_id_foreign` (`case_id`),
  KEY `jobs_type_id_index` (`type_id`),
  KEY `idx_jobs_stage_assignee` (`stage`,`assignee`),
  KEY `idx_jobs_stage_set_active` (`stage`,`is_set`,`is_active`),
  KEY `idx_jobs_case_stage` (`case_id`,`stage`),
  KEY `idx_jobs_device_stage_set` (`device_id`,`stage`,`is_set`),
  KEY `idx_jobs_milling_build` (`milling_build_id`),
  KEY `idx_jobs_printing_build` (`printing_build_id`),
  KEY `idx_jobs_sintering_build` (`sintering_build_id`),
  KEY `idx_jobs_pressing_build` (`pressing_build_id`),
  KEY `idx_jobs_stage_delivery` (`stage`,`delivery_accepted`),
  KEY `idx_jobs_deleted_at` (`deleted_at`),
  KEY `idx_jobs_stage` (`stage`),
  KEY `idx_jobs_assignee` (`assignee`),
  KEY `idx_jobs_material` (`material_id`),
  KEY `idx_jobs_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_types`
--

DROP TABLE IF EXISTS `job_types`;
CREATE TABLE IF NOT EXISTS `job_types` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `teeth_or_jaw` tinyint(4) NOT NULL,
  `a_secondary_item` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `labs`
--

DROP TABLE IF EXISTS `labs`;
CREATE TABLE IF NOT EXISTS `labs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `phone` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `address` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laravel_logger_activity`
--

DROP TABLE IF EXISTS `laravel_logger_activity`;
CREATE TABLE IF NOT EXISTS `laravel_logger_activity` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `userType` varchar(255) NOT NULL,
  `userId` bigint(20) UNSIGNED NOT NULL,
  `route` varchar(255) NOT NULL,
  `ipAddress` varchar(255) NOT NULL,
  `userAgent` text NOT NULL,
  `locale` varchar(255) NOT NULL,
  `referer` varchar(255) DEFAULT NULL,
  `methodType` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

DROP TABLE IF EXISTS `materials`;
CREATE TABLE IF NOT EXISTS `materials` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `price` double NOT NULL,
  `design` tinyint(4) NOT NULL,
  `mill` tinyint(4) NOT NULL,
  `print_3d` tinyint(4) NOT NULL,
  `sinter_furnace` tinyint(4) NOT NULL,
  `press_furnace` tinyint(4) NOT NULL,
  `finish` tinyint(4) NOT NULL,
  `qc` tinyint(4) NOT NULL,
  `delivery` tinyint(4) NOT NULL,
  `restricted` tinyint(4) NOT NULL DEFAULT 0,
  `count_as_unit` tinyint(4) NOT NULL DEFAULT 1,
  `count_in_units_counts_report` tinyint(4) DEFAULT 1,
  `count_in_job_types_report` tinyint(4) NOT NULL DEFAULT 1,
  `count_in_qc_report` tinyint(4) NOT NULL DEFAULT 1,
  `count_in_implants_report` tinyint(4) NOT NULL DEFAULT 1,
  `is_active` mediumint(8) UNSIGNED NOT NULL DEFAULT 1,
  `default_type_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `material_jobtypes`
--

DROP TABLE IF EXISTS `material_jobtypes`;
CREATE TABLE IF NOT EXISTS `material_jobtypes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `material_id` bigint(20) NOT NULL,
  `jobtype_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `material_types`
--

DROP TABLE IF EXISTS `material_types`;
CREATE TABLE IF NOT EXISTS `material_types` (
  `id` bigint(20) NOT NULL,
  `material_id` bigint(20) NOT NULL,
  `type_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `video_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_notifications_tokens`
--

DROP TABLE IF EXISTS `mobile_notifications_tokens`;
CREATE TABLE IF NOT EXISTS `mobile_notifications_tokens` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) NOT NULL,
  `device_id` varchar(255) DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `is_clinic` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_signin_logs`
--

DROP TABLE IF EXISTS `mobile_signin_logs`;
CREATE TABLE IF NOT EXISTS `mobile_signin_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) NOT NULL,
  `device` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `is_clinic` tinyint(4) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `monthly_case_counters`
--

DROP TABLE IF EXISTS `monthly_case_counters`;
CREATE TABLE IF NOT EXISTS `monthly_case_counters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `counter` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
CREATE TABLE IF NOT EXISTS `notes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) UNSIGNED NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `written_by` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
  `id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `scopes` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
CREATE TABLE IF NOT EXISTS `oauth_auth_codes` (
  `id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `scopes` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE IF NOT EXISTS `oauth_clients` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `secret` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `redirect` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
CREATE TABLE IF NOT EXISTS `oauth_personal_access_clients` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_personal_access_clients_client_id_index` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
  `id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `access_token_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount` double NOT NULL,
  `notes` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `collector` bigint(20) UNSIGNED NOT NULL,
  `received_by` bigint(20) DEFAULT NULL,
  `recieved_on` timestamp NULL DEFAULT NULL,
  `is_credit_note` tinyint(4) NOT NULL DEFAULT 0,
  `from_bank` bigint(20) DEFAULT NULL,
  `additional_notes` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_logs_doctor_id_foreign` (`doctor_id`),
  KEY `payment_logs_collector_foreign` (`collector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `printing_tasks`
--

DROP TABLE IF EXISTS `printing_tasks`;
CREATE TABLE IF NOT EXISTS `printing_tasks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) NOT NULL,
  `printed_by` bigint(20) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipt_vouchers`
--

DROP TABLE IF EXISTS `receipt_vouchers`;
CREATE TABLE IF NOT EXISTS `receipt_vouchers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) NOT NULL,
  `recieved_by` bigint(20) DEFAULT NULL,
  `brought_by` bigint(20) DEFAULT NULL,
  `recieved_at` timestamp NULL DEFAULT NULL,
  `signed` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `redone_orders`
--

DROP TABLE IF EXISTS `redone_orders`;
CREATE TABLE IF NOT EXISTS `redone_orders` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `redone_to_stage` int(11) DEFAULT NULL,
  `cause` varchar(255) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `redone_by` int(11) NOT NULL,
  `desinged_by` bigint(20) DEFAULT NULL,
  `milled_by` bigint(20) DEFAULT NULL,
  `furnaced_by` bigint(20) DEFAULT NULL,
  `finished_by` bigint(20) DEFAULT NULL,
  `units_redone` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rejected_jobs`
--

DROP TABLE IF EXISTS `rejected_jobs`;
CREATE TABLE IF NOT EXISTS `rejected_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unit_num` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `type` bigint(20) UNSIGNED NOT NULL,
  `color` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `style` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_material_id_foreign` (`material_id`),
  KEY `jobs_type_foreign` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rejected_order_records`
--

DROP TABLE IF EXISTS `rejected_order_records`;
CREATE TABLE IF NOT EXISTS `rejected_order_records` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rejected_order_id` int(11) NOT NULL,
  `original_order_id` bigint(20) NOT NULL,
  `cause` varchar(255) NOT NULL,
  `details` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remade_order_records`
--

DROP TABLE IF EXISTS `remade_order_records`;
CREATE TABLE IF NOT EXISTS `remade_order_records` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `remade_order_id` int(11) NOT NULL,
  `original_order_id` bigint(20) NOT NULL,
  `cause` varchar(255) NOT NULL,
  `details` varchar(255) DEFAULT NULL,
  `remade_to` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repeat_cases`
--

DROP TABLE IF EXISTS `repeat_cases`;
CREATE TABLE IF NOT EXISTS `repeat_cases` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `repeat_cases_user_id_foreign` (`user_id`),
  KEY `repeat_cases_order_id_foreign` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repeat_causes`
--

DROP TABLE IF EXISTS `repeat_causes`;
CREATE TABLE IF NOT EXISTS `repeat_causes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `reason` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stages_for_view_only`
--

DROP TABLE IF EXISTS `stages_for_view_only`;
CREATE TABLE IF NOT EXISTS `stages_for_view_only` (
  `id` bigint(20) NOT NULL,
  `stage_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `text` varchar(200) NOT NULL,
  `color` varchar(200) DEFAULT NULL,
  `initials` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `hidden` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

DROP TABLE IF EXISTS `types`;
CREATE TABLE IF NOT EXISTS `types` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `is_enabled` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `types_material_id_deleted_at_index` (`material_id`,`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_name` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name_initials` varchar(255) DEFAULT NULL,
  `username` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `included_in_reports` tinyint(4) NOT NULL DEFAULT 1,
  `is_developer` tinyint(4) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `has_photo` tinyint(4) DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_permissions_user_id_foreign` (`user_id`),
  KEY `user_permissions_permission_id_foreign` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `case_logs`
--
ALTER TABLE `case_logs`
  ADD CONSTRAINT `order_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_discounts`
--
ALTER TABLE `client_discounts`
  ADD CONSTRAINT `discounts_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD CONSTRAINT `failed_jobs_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `failed_jobs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `failed_jobs_type_foreign` FOREIGN KEY (`type`) REFERENCES `job_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jobs_order_id_foreign` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jobs_type_foreign` FOREIGN KEY (`type`) REFERENCES `job_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jobs_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`);

--
-- Constraints for table `rejected_jobs`
--
ALTER TABLE `rejected_jobs`
  ADD CONSTRAINT `Rjobs_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Rjobs_type_foreign` FOREIGN KEY (`type`) REFERENCES `job_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `repeat_cases`
--
ALTER TABLE `repeat_cases`
  ADD CONSTRAINT `repeat_cases_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repeat_cases_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `types`
--
ALTER TABLE `types`
  ADD CONSTRAINT `types_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`);

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
