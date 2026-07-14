-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Jul 14, 2026 at 04:00 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e-learning`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_calendar_events`
--

CREATE TABLE `academic_calendar_events` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `category` enum('akademik','ujian','libur','kegiatan','lainnya') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'akademik',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `academic_year_id` bigint UNSIGNED DEFAULT NULL,
  `class_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` bigint UNSIGNED NOT NULL,
  `year` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `year`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2024/2025', 'Tahun Ajaran 2024/2025', 1, '2026-07-07 04:17:04', '2026-07-07 04:17:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `logged_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `subject_type`, `subject_id`, `description`, `properties`, `ip_address`, `user_agent`, `logged_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', '2026-07-07 04:17:13', '2026-07-07 04:17:13', '2026-07-07 04:17:13'),
(2, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 05:20:38', '2026-07-07 05:20:38', '2026-07-07 05:20:38'),
(3, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:35:34', '2026-07-07 06:35:34', '2026-07-07 06:35:34'),
(4, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:35:41', '2026-07-07 06:35:41', '2026-07-07 06:35:41'),
(5, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:36:31', '2026-07-07 06:36:31', '2026-07-07 06:36:31'),
(6, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:36:34', '2026-07-07 06:36:34', '2026-07-07 06:36:34'),
(7, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:43:38', '2026-07-07 06:43:38', '2026-07-07 06:43:38'),
(8, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:53:08', '2026-07-07 06:53:08', '2026-07-07 06:53:08'),
(9, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:53:13', '2026-07-07 06:53:13', '2026-07-07 06:53:13'),
(10, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:55:36', '2026-07-07 06:55:36', '2026-07-07 06:55:36'),
(11, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:55:45', '2026-07-07 06:55:45', '2026-07-07 06:55:45'),
(12, 1, 'settings_update', NULL, NULL, 'Pengaturan attendance diperbarui', '{\"keys\": [\"late_limit\", \"min_attendance\", \"geofence_lat\", \"geofence_lng\", \"geofence_radius\", \"face_detection_min\", \"face_match_min\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:56:23', '2026-07-07 06:56:23', '2026-07-07 06:56:23'),
(13, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:56:26', '2026-07-07 06:56:26', '2026-07-07 06:56:26'),
(14, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:56:29', '2026-07-07 06:56:29', '2026-07-07 06:56:29'),
(15, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:58:49', '2026-07-07 06:58:49', '2026-07-07 06:58:49'),
(16, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 06:59:02', '2026-07-07 06:59:02', '2026-07-07 06:59:02'),
(17, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 07:09:03', '2026-07-07 07:09:03', '2026-07-07 07:09:03'),
(18, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 07:09:05', '2026-07-07 07:09:05', '2026-07-07 07:09:05'),
(19, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 07:09:52', '2026-07-07 07:09:52', '2026-07-07 07:09:52'),
(20, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 07:09:56', '2026-07-07 07:09:56', '2026-07-07 07:09:56'),
(21, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 07:12:33', '2026-07-07 07:12:33', '2026-07-07 07:12:33'),
(22, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:15:16', '2026-07-09 03:15:16', '2026-07-09 03:15:16'),
(23, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:27:47', '2026-07-09 03:27:47', '2026-07-09 03:27:47'),
(24, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:27:51', '2026-07-09 03:27:51', '2026-07-09 03:27:51'),
(25, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:35:09', '2026-07-09 03:35:09', '2026-07-09 03:35:09'),
(26, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:36:24', '2026-07-09 03:36:24', '2026-07-09 03:36:24'),
(27, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:36:39', '2026-07-09 03:36:39', '2026-07-09 03:36:39'),
(28, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:38:58', '2026-07-09 03:38:58', '2026-07-09 03:38:58'),
(29, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:39:04', '2026-07-09 03:39:04', '2026-07-09 03:39:04'),
(30, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:46:04', '2026-07-09 03:46:04', '2026-07-09 03:46:04'),
(31, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:46:07', '2026-07-09 03:46:07', '2026-07-09 03:46:07'),
(32, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:46:07', '2026-07-09 03:46:07', '2026-07-09 03:46:07'),
(33, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:48:51', '2026-07-09 03:48:51', '2026-07-09 03:48:51'),
(34, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:48:56', '2026-07-09 03:48:56', '2026-07-09 03:48:56'),
(35, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:50:14', '2026-07-09 03:50:14', '2026-07-09 03:50:14'),
(36, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:50:20', '2026-07-09 03:50:20', '2026-07-09 03:50:20'),
(37, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:51:14', '2026-07-09 03:51:14', '2026-07-09 03:51:14'),
(38, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:51:19', '2026-07-09 03:51:19', '2026-07-09 03:51:19'),
(39, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:55:36', '2026-07-09 03:55:36', '2026-07-09 03:55:36'),
(40, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:55:39', '2026-07-09 03:55:39', '2026-07-09 03:55:39'),
(41, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:55:39', '2026-07-09 03:55:39', '2026-07-09 03:55:39'),
(42, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:57:09', '2026-07-09 03:57:09', '2026-07-09 03:57:09'),
(43, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:58:19', '2026-07-09 03:58:19', '2026-07-09 03:58:19'),
(44, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:58:20', '2026-07-09 03:58:20', '2026-07-09 03:58:20'),
(45, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:58:49', '2026-07-09 03:58:49', '2026-07-09 03:58:49'),
(46, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 03:58:51', '2026-07-09 03:58:51', '2026-07-09 03:58:51'),
(47, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 04:01:08', '2026-07-09 04:01:08', '2026-07-09 04:01:08'),
(48, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 04:01:15', '2026-07-09 04:01:15', '2026-07-09 04:01:15'),
(49, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 05:29:23', '2026-07-09 05:29:23', '2026-07-09 05:29:23'),
(50, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 05:29:27', '2026-07-09 05:29:27', '2026-07-09 05:29:27'),
(51, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 05:29:28', '2026-07-09 05:29:28', '2026-07-09 05:29:28'),
(52, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 05:29:28', '2026-07-09 05:29:28', '2026-07-09 05:29:28'),
(53, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 05:51:47', '2026-07-09 05:51:47', '2026-07-09 05:51:47'),
(54, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 05:51:57', '2026-07-09 05:51:57', '2026-07-09 05:51:57'),
(55, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 05:59:36', '2026-07-09 05:59:36', '2026-07-09 05:59:36'),
(56, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 05:59:38', '2026-07-09 05:59:38', '2026-07-09 05:59:38'),
(57, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 06:03:23', '2026-07-09 06:03:23', '2026-07-09 06:03:23'),
(58, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 06:03:26', '2026-07-09 06:03:26', '2026-07-09 06:03:26'),
(59, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 06:03:27', '2026-07-09 06:03:27', '2026-07-09 06:03:27'),
(60, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 06:06:08', '2026-07-09 06:06:08', '2026-07-09 06:06:08'),
(61, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 06:06:11', '2026-07-09 06:06:11', '2026-07-09 06:06:11'),
(62, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 06:06:11', '2026-07-09 06:06:11', '2026-07-09 06:06:11'),
(63, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 06:08:58', '2026-07-09 06:08:58', '2026-07-09 06:08:58'),
(64, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 06:09:01', '2026-07-09 06:09:01', '2026-07-09 06:09:01'),
(65, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 06:09:02', '2026-07-09 06:09:02', '2026-07-09 06:09:02'),
(66, 1, 'user_toggle_status', 'App\\Models\\User', 21, 'User 2025015 dinonaktifkan', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 06:55:54', '2026-07-09 06:55:54', '2026-07-09 06:55:54'),
(67, 1, 'user_toggle_status', 'App\\Models\\User', 21, 'User 2025015 diaktifkan', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-09 06:56:10', '2026-07-09 06:56:10', '2026-07-09 06:56:10'),
(68, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-10 09:05:03', '2026-07-10 09:05:03', '2026-07-10 09:05:03'),
(69, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-10 09:05:04', '2026-07-10 09:05:04', '2026-07-10 09:05:04'),
(70, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-10 09:38:55', '2026-07-10 09:38:55', '2026-07-10 09:38:55'),
(71, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-10 09:39:00', '2026-07-10 09:39:00', '2026-07-10 09:39:00'),
(72, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-10 09:39:00', '2026-07-10 09:39:00', '2026-07-10 09:39:00'),
(73, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-10 09:39:01', '2026-07-10 09:39:01', '2026-07-10 09:39:01'),
(74, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-10 12:24:02', '2026-07-10 12:24:02', '2026-07-10 12:24:02'),
(75, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-12 04:25:28', '2026-07-12 04:25:28', '2026-07-12 04:25:28'),
(76, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-12 04:25:28', '2026-07-12 04:25:28', '2026-07-12 04:25:28'),
(77, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-12 04:28:12', '2026-07-12 04:28:12', '2026-07-12 04:28:12'),
(78, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-12 04:29:44', '2026-07-12 04:29:44', '2026-07-12 04:29:44'),
(79, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-12 04:29:48', '2026-07-12 04:29:48', '2026-07-12 04:29:48'),
(80, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-12 04:29:48', '2026-07-12 04:29:48', '2026-07-12 04:29:48'),
(81, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-12 04:29:52', '2026-07-12 04:29:52', '2026-07-12 04:29:52'),
(82, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-12 04:29:55', '2026-07-12 04:29:55', '2026-07-12 04:29:55'),
(83, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-12 04:35:47', '2026-07-12 04:35:47', '2026-07-12 04:35:47'),
(84, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 03:50:41', '2026-07-13 03:50:41', '2026-07-13 03:50:41'),
(85, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:04:36', '2026-07-13 06:04:36', '2026-07-13 06:04:36'),
(86, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:05:51', '2026-07-13 06:05:51', '2026-07-13 06:05:51'),
(87, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:05:54', '2026-07-13 06:05:54', '2026-07-13 06:05:54'),
(88, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:06:34', '2026-07-13 06:06:34', '2026-07-13 06:06:34'),
(89, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:06:37', '2026-07-13 06:06:37', '2026-07-13 06:06:37'),
(90, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:20:09', '2026-07-13 06:20:09', '2026-07-13 06:20:09'),
(91, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:28:04', '2026-07-13 06:28:04', '2026-07-13 06:28:04'),
(92, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:28:15', '2026-07-13 06:28:15', '2026-07-13 06:28:15'),
(93, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:33:37', '2026-07-13 06:33:37', '2026-07-13 06:33:37'),
(94, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:33:40', '2026-07-13 06:33:40', '2026-07-13 06:33:40'),
(95, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:36:30', '2026-07-13 06:36:30', '2026-07-13 06:36:30'),
(96, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:36:33', '2026-07-13 06:36:33', '2026-07-13 06:36:33'),
(97, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:39:40', '2026-07-13 06:39:40', '2026-07-13 06:39:40'),
(98, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:39:44', '2026-07-13 06:39:44', '2026-07-13 06:39:44'),
(99, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:39:45', '2026-07-13 06:39:45', '2026-07-13 06:39:45'),
(100, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:40:09', '2026-07-13 06:40:09', '2026-07-13 06:40:09'),
(101, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:42:23', '2026-07-13 06:42:23', '2026-07-13 06:42:23'),
(102, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:42:40', '2026-07-13 06:42:40', '2026-07-13 06:42:40'),
(103, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:59:40', '2026-07-13 06:59:40', '2026-07-13 06:59:40'),
(104, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 06:59:58', '2026-07-13 06:59:58', '2026-07-13 06:59:58'),
(105, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:03:19', '2026-07-13 07:03:19', '2026-07-13 07:03:19'),
(106, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:03:45', '2026-07-13 07:03:45', '2026-07-13 07:03:45'),
(107, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:06:55', '2026-07-13 07:06:55', '2026-07-13 07:06:55'),
(108, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:06:58', '2026-07-13 07:06:58', '2026-07-13 07:06:58'),
(109, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:07:40', '2026-07-13 07:07:40', '2026-07-13 07:07:40'),
(110, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:07:43', '2026-07-13 07:07:43', '2026-07-13 07:07:43'),
(111, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:10:05', '2026-07-13 07:10:05', '2026-07-13 07:10:05'),
(112, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:10:24', '2026-07-13 07:10:24', '2026-07-13 07:10:24'),
(113, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:10:53', '2026-07-13 07:10:53', '2026-07-13 07:10:53'),
(114, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:10:58', '2026-07-13 07:10:58', '2026-07-13 07:10:58'),
(115, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:11:39', '2026-07-13 07:11:39', '2026-07-13 07:11:39'),
(116, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:11:45', '2026-07-13 07:11:45', '2026-07-13 07:11:45'),
(117, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:11:56', '2026-07-13 07:11:56', '2026-07-13 07:11:56'),
(118, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:12:04', '2026-07-13 07:12:04', '2026-07-13 07:12:04'),
(119, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:14:05', '2026-07-13 07:14:05', '2026-07-13 07:14:05'),
(120, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:14:09', '2026-07-13 07:14:09', '2026-07-13 07:14:09'),
(121, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:15:04', '2026-07-13 07:15:04', '2026-07-13 07:15:04'),
(122, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 07:15:06', '2026-07-13 07:15:06', '2026-07-13 07:15:06'),
(123, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:44:43', '2026-07-14 11:44:43', '2026-07-14 11:44:43'),
(124, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:50:28', '2026-07-14 11:50:28', '2026-07-14 11:50:28'),
(125, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:50:31', '2026-07-14 11:50:31', '2026-07-14 11:50:31'),
(126, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:50:51', '2026-07-14 11:50:51', '2026-07-14 11:50:51'),
(127, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:50:54', '2026-07-14 11:50:54', '2026-07-14 11:50:54'),
(128, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:51:18', '2026-07-14 11:51:18', '2026-07-14 11:51:18'),
(129, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:51:21', '2026-07-14 11:51:21', '2026-07-14 11:51:21'),
(130, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:54:23', '2026-07-14 11:54:23', '2026-07-14 11:54:23'),
(131, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:54:25', '2026-07-14 11:54:25', '2026-07-14 11:54:25'),
(132, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:54:54', '2026-07-14 11:54:54', '2026-07-14 11:54:54'),
(133, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:54:58', '2026-07-14 11:54:58', '2026-07-14 11:54:58'),
(134, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:55:50', '2026-07-14 11:55:50', '2026-07-14 11:55:50'),
(135, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:55:52', '2026-07-14 11:55:52', '2026-07-14 11:55:52'),
(136, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:57:14', '2026-07-14 11:57:14', '2026-07-14 11:57:14'),
(137, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 11:57:17', '2026-07-14 11:57:17', '2026-07-14 11:57:17'),
(138, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:01:10', '2026-07-14 12:01:10', '2026-07-14 12:01:10'),
(139, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:01:12', '2026-07-14 12:01:12', '2026-07-14 12:01:12'),
(140, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:03:49', '2026-07-14 12:03:49', '2026-07-14 12:03:49'),
(141, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:03:51', '2026-07-14 12:03:51', '2026-07-14 12:03:51'),
(142, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:23:50', '2026-07-14 12:23:50', '2026-07-14 12:23:50'),
(143, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:23:53', '2026-07-14 12:23:53', '2026-07-14 12:23:53'),
(144, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:24:47', '2026-07-14 12:24:47', '2026-07-14 12:24:47'),
(145, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:24:49', '2026-07-14 12:24:49', '2026-07-14 12:24:49'),
(146, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:29:01', '2026-07-14 12:29:01', '2026-07-14 12:29:01'),
(147, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:29:04', '2026-07-14 12:29:04', '2026-07-14 12:29:04'),
(148, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:41:13', '2026-07-14 12:41:13', '2026-07-14 12:41:13'),
(149, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:41:17', '2026-07-14 12:41:17', '2026-07-14 12:41:17'),
(150, 1, 'settings_update', NULL, NULL, 'Pengaturan attendance diperbarui', '{\"keys\": [\"late_limit\", \"min_attendance\", \"geofence_lat\", \"geofence_lng\", \"geofence_radius\", \"face_detection_min\", \"face_match_min\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:41:25', '2026-07-14 12:41:25', '2026-07-14 12:41:25'),
(151, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:41:27', '2026-07-14 12:41:27', '2026-07-14 12:41:27'),
(152, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:41:30', '2026-07-14 12:41:30', '2026-07-14 12:41:30'),
(153, 7, 'logout', 'App\\Models\\User', 7, 'User 2025001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:44:50', '2026-07-14 12:44:50', '2026-07-14 12:44:50'),
(154, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:44:58', '2026-07-14 12:44:58', '2026-07-14 12:44:58'),
(155, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:50:07', '2026-07-14 12:50:07', '2026-07-14 12:50:07'),
(156, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:50:11', '2026-07-14 12:50:11', '2026-07-14 12:50:11'),
(157, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:51:50', '2026-07-14 12:51:50', '2026-07-14 12:51:50'),
(158, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 12:51:52', '2026-07-14 12:51:52', '2026-07-14 12:51:52'),
(159, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 13:37:04', '2026-07-14 13:37:04', '2026-07-14 13:37:04'),
(160, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 13:37:09', '2026-07-14 13:37:09', '2026-07-14 13:37:09'),
(161, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 13:42:55', '2026-07-14 13:42:55', '2026-07-14 13:42:55'),
(162, 1, 'logout', 'App\\Models\\User', 1, 'User 198001012005011003 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 13:43:16', '2026-07-14 13:43:16', '2026-07-14 13:43:16'),
(163, 2, 'login', 'App\\Models\\User', 2, 'User 199001012020121001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 13:43:34', '2026-07-14 13:43:34', '2026-07-14 13:43:34'),
(164, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 13:43:49', '2026-07-14 13:43:49', '2026-07-14 13:43:49'),
(165, 7, 'login', 'App\\Models\\User', 7, 'User 2025001 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 13:44:06', '2026-07-14 13:44:06', '2026-07-14 13:44:06'),
(166, 2, 'logout', 'App\\Models\\User', 2, 'User 199001012020121001 keluar dari sistem', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 13:49:59', '2026-07-14 13:49:59', '2026-07-14 13:49:59'),
(167, 1, 'login', 'App\\Models\\User', 1, 'User 198001012005011003 berhasil login', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 13:50:02', '2026-07-14 13:50:02', '2026-07-14 13:50:02');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_size` int UNSIGNED DEFAULT NULL,
  `attachment_mime` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('umum','kelas') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_subject_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` enum('individu','kelompok') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'individu',
  `file_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` int UNSIGNED DEFAULT NULL,
  `mime_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `max_score` smallint UNSIGNED NOT NULL DEFAULT '100',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `uuid`, `class_subject_id`, `title`, `description`, `type`, `file_path`, `file_name`, `file_size`, `mime_type`, `due_date`, `max_score`, `is_published`, `published_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '668023b3-897b-48d7-a8e2-0e6410999ea8', 22, 'Tugas Cerpen Bahasa Indonesia', NULL, 'individu', 'assignments/mTGLACaFjxGeFnBVWjLF3lhdWnofy436FjqunDUZ.pdf', 'CV_19235007_Ammar Syarif.pdf', 369068, 'application/pdf', NULL, 100, 1, '2026-07-09 03:50:09', '2026-07-09 03:50:09', '2026-07-09 07:00:22', NULL),
(2, '6b7aa398-f194-42ec-823d-93cf42b93d12', 22, 'Tester', 'tester', 'individu', NULL, NULL, NULL, NULL, '2026-07-13 15:15:00', 100, 1, '2026-07-13 07:14:35', '2026-07-13 07:14:35', '2026-07-13 07:14:35', NULL),
(3, '04b73b5c-dc70-491a-aa3e-4dd108f2d9c0', 22, 'tester 2', 'tester', 'kelompok', NULL, NULL, NULL, NULL, '2026-07-15 17:17:00', 100, 1, '2026-07-13 07:15:00', '2026-07-13 07:15:00', '2026-07-13 07:15:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `assignment_grades`
--

CREATE TABLE `assignment_grades` (
  `id` bigint UNSIGNED NOT NULL,
  `assignment_submission_id` bigint UNSIGNED NOT NULL,
  `teacher_id` bigint UNSIGNED NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `feedback` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignment_grades`
--

INSERT INTO `assignment_grades` (`id`, `assignment_submission_id`, `teacher_id`, `score`, `feedback`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 90.00, 'bagus', '2026-07-09 03:58:44', '2026-07-09 03:58:44');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` bigint UNSIGNED NOT NULL,
  `assignment_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` int UNSIGNED DEFAULT NULL,
  `mime_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_late` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignment_submissions`
--

INSERT INTO `assignment_submissions` (`id`, `assignment_id`, `student_id`, `content`, `file_path`, `file_name`, `file_size`, `mime_type`, `submitted_at`, `is_late`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'https://drive.google.com/drive/folders/example-gdrive-link', NULL, NULL, NULL, NULL, '2026-07-09 03:51:08', 0, '2026-07-09 03:51:08', '2026-07-09 03:51:08');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `class_id` bigint UNSIGNED NOT NULL,
  `teacher_id` bigint UNSIGNED NOT NULL,
  `session` enum('pagi','siang') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pagi',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `date`, `class_id`, `teacher_id`, `session`, `notes`, `created_at`, `updated_at`) VALUES
(1, '2026-06-08', 1, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(2, '2026-06-08', 2, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(3, '2026-06-08', 3, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(4, '2026-06-08', 4, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(5, '2026-06-08', 5, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(6, '2026-06-08', 6, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(7, '2026-06-08', 7, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(8, '2026-06-09', 1, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(9, '2026-06-09', 2, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(10, '2026-06-09', 3, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(11, '2026-06-09', 4, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(12, '2026-06-09', 5, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(13, '2026-06-09', 6, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(14, '2026-06-09', 7, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(15, '2026-06-10', 1, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(16, '2026-06-10', 2, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(17, '2026-06-10', 3, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(18, '2026-06-10', 4, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(19, '2026-06-10', 5, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(20, '2026-06-10', 6, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(21, '2026-06-10', 7, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(22, '2026-06-11', 1, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(23, '2026-06-11', 2, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(24, '2026-06-11', 3, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(25, '2026-06-11', 4, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(26, '2026-06-11', 5, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(27, '2026-06-11', 6, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(28, '2026-06-11', 7, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(29, '2026-06-12', 1, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(30, '2026-06-12', 2, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(31, '2026-06-12', 3, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(32, '2026-06-12', 4, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(33, '2026-06-12', 5, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(34, '2026-06-12', 6, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(35, '2026-06-12', 7, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(36, '2026-06-15', 1, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(37, '2026-06-15', 2, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(38, '2026-06-15', 3, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(39, '2026-06-15', 4, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(40, '2026-06-15', 5, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(41, '2026-06-15', 6, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(42, '2026-06-15', 7, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(43, '2026-06-16', 1, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(44, '2026-06-16', 2, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(45, '2026-06-16', 3, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(46, '2026-06-16', 4, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(47, '2026-06-16', 5, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(48, '2026-06-16', 6, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(49, '2026-06-16', 7, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(50, '2026-06-17', 1, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(51, '2026-06-17', 2, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(52, '2026-06-17', 3, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(53, '2026-06-17', 4, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(54, '2026-06-17', 5, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(55, '2026-06-17', 6, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(56, '2026-06-17', 7, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(57, '2026-06-18', 1, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(58, '2026-06-18', 2, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(59, '2026-06-18', 3, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(60, '2026-06-18', 4, 1, 'pagi', NULL, '2026-07-07 04:17:09', '2026-07-07 04:17:09'),
(61, '2026-06-18', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(62, '2026-06-18', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(63, '2026-06-18', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(64, '2026-06-19', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(65, '2026-06-19', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(66, '2026-06-19', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(67, '2026-06-19', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(68, '2026-06-19', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(69, '2026-06-19', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(70, '2026-06-19', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(71, '2026-06-22', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(72, '2026-06-22', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(73, '2026-06-22', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(74, '2026-06-22', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(75, '2026-06-22', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(76, '2026-06-22', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(77, '2026-06-22', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(78, '2026-06-23', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(79, '2026-06-23', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(80, '2026-06-23', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(81, '2026-06-23', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(82, '2026-06-23', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(83, '2026-06-23', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(84, '2026-06-23', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(85, '2026-06-24', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(86, '2026-06-24', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(87, '2026-06-24', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(88, '2026-06-24', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(89, '2026-06-24', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(90, '2026-06-24', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(91, '2026-06-24', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(92, '2026-06-25', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(93, '2026-06-25', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(94, '2026-06-25', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(95, '2026-06-25', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(96, '2026-06-25', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(97, '2026-06-25', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(98, '2026-06-25', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(99, '2026-06-26', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(100, '2026-06-26', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(101, '2026-06-26', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(102, '2026-06-26', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(103, '2026-06-26', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(104, '2026-06-26', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(105, '2026-06-26', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(106, '2026-06-29', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(107, '2026-06-29', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(108, '2026-06-29', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(109, '2026-06-29', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(110, '2026-06-29', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(111, '2026-06-29', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(112, '2026-06-29', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(113, '2026-06-30', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(114, '2026-06-30', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(115, '2026-06-30', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(116, '2026-06-30', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(117, '2026-06-30', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(118, '2026-06-30', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(119, '2026-06-30', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(120, '2026-07-01', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(121, '2026-07-01', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(122, '2026-07-01', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(123, '2026-07-01', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(124, '2026-07-01', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(125, '2026-07-01', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(126, '2026-07-01', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(127, '2026-07-02', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(128, '2026-07-02', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(129, '2026-07-02', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(130, '2026-07-02', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(131, '2026-07-02', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(132, '2026-07-02', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(133, '2026-07-02', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(134, '2026-07-03', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(135, '2026-07-03', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(136, '2026-07-03', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(137, '2026-07-03', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(138, '2026-07-03', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(139, '2026-07-03', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(140, '2026-07-03', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(141, '2026-07-06', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(142, '2026-07-06', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(143, '2026-07-06', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(144, '2026-07-06', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(145, '2026-07-06', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(146, '2026-07-06', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(147, '2026-07-06', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(148, '2026-07-07', 1, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(149, '2026-07-07', 2, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(150, '2026-07-07', 3, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(151, '2026-07-07', 4, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(152, '2026-07-07', 5, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(153, '2026-07-07', 6, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(154, '2026-07-07', 7, 1, 'pagi', NULL, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(155, '2026-07-12', 3, 1, 'pagi', NULL, '2026-07-12 04:32:43', '2026-07-12 04:32:43'),
(156, '2026-07-14', 6, 1, 'pagi', NULL, '2026-07-14 12:44:44', '2026-07-14 12:44:44');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_details`
--

CREATE TABLE `attendance_details` (
  `id` bigint UNSIGNED NOT NULL,
  `attendance_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `status` enum('hadir','sakit','izin','alfa','terlambat') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'alfa',
  `check_in` time DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_details`
--

INSERT INTO `attendance_details` (`id`, `attendance_id`, `student_id`, `status`, `check_in`, `notes`) VALUES
(1, 1, 7, 'hadir', '07:01:00', NULL),
(2, 1, 8, 'hadir', '07:25:00', NULL),
(3, 2, 14, 'hadir', '07:27:00', NULL),
(4, 2, 15, 'hadir', '07:09:00', NULL),
(5, 3, 9, 'hadir', '07:20:00', NULL),
(6, 3, 10, 'hadir', '07:18:00', NULL),
(7, 4, 4, 'alfa', '07:15:00', NULL),
(8, 4, 5, 'sakit', '07:29:00', NULL),
(9, 4, 12, 'terlambat', '07:00:00', NULL),
(10, 4, 13, 'hadir', '07:06:00', NULL),
(11, 5, 6, 'hadir', '07:28:00', NULL),
(12, 6, 1, 'izin', '07:13:00', NULL),
(13, 6, 2, 'hadir', '07:21:00', NULL),
(14, 6, 11, 'izin', '07:16:00', NULL),
(15, 7, 3, 'terlambat', '07:08:00', NULL),
(16, 8, 7, 'hadir', '07:16:00', NULL),
(17, 8, 8, 'hadir', '07:08:00', NULL),
(18, 9, 14, 'sakit', '07:26:00', NULL),
(19, 9, 15, 'hadir', '07:19:00', NULL),
(20, 10, 9, 'izin', '07:28:00', NULL),
(21, 10, 10, 'hadir', '07:29:00', NULL),
(22, 11, 4, 'hadir', '07:13:00', NULL),
(23, 11, 5, 'hadir', '07:19:00', NULL),
(24, 11, 12, 'alfa', '07:19:00', NULL),
(25, 11, 13, 'hadir', '07:29:00', NULL),
(26, 12, 6, 'hadir', '07:29:00', NULL),
(27, 13, 1, 'hadir', '07:19:00', NULL),
(28, 13, 2, 'hadir', '07:02:00', NULL),
(29, 13, 11, 'sakit', '07:03:00', NULL),
(30, 14, 3, 'hadir', '07:23:00', NULL),
(31, 15, 7, 'hadir', '07:29:00', NULL),
(32, 15, 8, 'hadir', '07:12:00', NULL),
(33, 16, 14, 'hadir', '07:03:00', NULL),
(34, 16, 15, 'hadir', '07:13:00', NULL),
(35, 17, 9, 'hadir', '07:22:00', NULL),
(36, 17, 10, 'hadir', '07:23:00', NULL),
(37, 18, 4, 'hadir', '07:01:00', NULL),
(38, 18, 5, 'hadir', '07:10:00', NULL),
(39, 18, 12, 'sakit', '07:12:00', NULL),
(40, 18, 13, 'hadir', '07:15:00', NULL),
(41, 19, 6, 'hadir', '07:01:00', NULL),
(42, 20, 1, 'sakit', '07:11:00', NULL),
(43, 20, 2, 'hadir', '07:10:00', NULL),
(44, 20, 11, 'hadir', '07:28:00', NULL),
(45, 21, 3, 'hadir', '07:02:00', NULL),
(46, 22, 7, 'izin', '07:16:00', NULL),
(47, 22, 8, 'hadir', '07:28:00', NULL),
(48, 23, 14, 'hadir', '07:03:00', NULL),
(49, 23, 15, 'hadir', '07:09:00', NULL),
(50, 24, 9, 'hadir', '07:28:00', NULL),
(51, 24, 10, 'hadir', '07:09:00', NULL),
(52, 25, 4, 'hadir', '07:07:00', NULL),
(53, 25, 5, 'hadir', '07:17:00', NULL),
(54, 25, 12, 'sakit', '07:03:00', NULL),
(55, 25, 13, 'hadir', '07:27:00', NULL),
(56, 26, 6, 'hadir', '07:06:00', NULL),
(57, 27, 1, 'hadir', '07:01:00', NULL),
(58, 27, 2, 'hadir', '07:12:00', NULL),
(59, 27, 11, 'hadir', '07:02:00', NULL),
(60, 28, 3, 'hadir', '07:18:00', NULL),
(61, 29, 7, 'izin', '07:00:00', NULL),
(62, 29, 8, 'hadir', '07:11:00', NULL),
(63, 30, 14, 'hadir', '07:10:00', NULL),
(64, 30, 15, 'sakit', '07:00:00', NULL),
(65, 31, 9, 'hadir', '07:26:00', NULL),
(66, 31, 10, 'hadir', '07:08:00', NULL),
(67, 32, 4, 'hadir', '07:08:00', NULL),
(68, 32, 5, 'alfa', '07:23:00', NULL),
(69, 32, 12, 'hadir', '07:18:00', NULL),
(70, 32, 13, 'sakit', '07:24:00', NULL),
(71, 33, 6, 'hadir', '07:26:00', NULL),
(72, 34, 1, 'hadir', '07:20:00', NULL),
(73, 34, 2, 'hadir', '07:01:00', NULL),
(74, 34, 11, 'hadir', '07:25:00', NULL),
(75, 35, 3, 'terlambat', '07:08:00', NULL),
(76, 36, 7, 'hadir', '07:13:00', NULL),
(77, 36, 8, 'hadir', '07:00:00', NULL),
(78, 37, 14, 'hadir', '07:26:00', NULL),
(79, 37, 15, 'hadir', '07:21:00', NULL),
(80, 38, 9, 'terlambat', '07:18:00', NULL),
(81, 38, 10, 'hadir', '07:02:00', NULL),
(82, 39, 4, 'sakit', '07:11:00', NULL),
(83, 39, 5, 'hadir', '07:15:00', NULL),
(84, 39, 12, 'alfa', '07:22:00', NULL),
(85, 39, 13, 'hadir', '07:13:00', NULL),
(86, 40, 6, 'hadir', '07:27:00', NULL),
(87, 41, 1, 'hadir', '07:28:00', NULL),
(88, 41, 2, 'hadir', '07:08:00', NULL),
(89, 41, 11, 'terlambat', '07:01:00', NULL),
(90, 42, 3, 'terlambat', '07:14:00', NULL),
(91, 43, 7, 'hadir', '07:01:00', NULL),
(92, 43, 8, 'izin', '07:26:00', NULL),
(93, 44, 14, 'hadir', '07:29:00', NULL),
(94, 44, 15, 'hadir', '07:07:00', NULL),
(95, 45, 9, 'terlambat', '07:26:00', NULL),
(96, 45, 10, 'alfa', '07:03:00', NULL),
(97, 46, 4, 'alfa', '07:02:00', NULL),
(98, 46, 5, 'hadir', '07:25:00', NULL),
(99, 46, 12, 'sakit', '07:04:00', NULL),
(100, 46, 13, 'izin', '07:07:00', NULL),
(101, 47, 6, 'izin', '07:13:00', NULL),
(102, 48, 1, 'hadir', '07:10:00', NULL),
(103, 48, 2, 'izin', '07:28:00', NULL),
(104, 48, 11, 'alfa', '07:08:00', NULL),
(105, 49, 3, 'hadir', '07:05:00', NULL),
(106, 50, 7, 'hadir', '07:22:00', NULL),
(107, 50, 8, 'hadir', '07:28:00', NULL),
(108, 51, 14, 'sakit', '07:17:00', NULL),
(109, 51, 15, 'hadir', '07:02:00', NULL),
(110, 52, 9, 'izin', '07:03:00', NULL),
(111, 52, 10, 'hadir', '07:26:00', NULL),
(112, 53, 4, 'hadir', '07:18:00', NULL),
(113, 53, 5, 'hadir', '07:18:00', NULL),
(114, 53, 12, 'hadir', '07:21:00', NULL),
(115, 53, 13, 'alfa', '07:07:00', NULL),
(116, 54, 6, 'hadir', '07:09:00', NULL),
(117, 55, 1, 'hadir', '07:29:00', NULL),
(118, 55, 2, 'hadir', '07:18:00', NULL),
(119, 55, 11, 'sakit', '07:11:00', NULL),
(120, 56, 3, 'hadir', '07:18:00', NULL),
(121, 57, 7, 'hadir', '07:15:00', NULL),
(122, 57, 8, 'hadir', '07:16:00', NULL),
(123, 58, 14, 'terlambat', '07:09:00', NULL),
(124, 58, 15, 'hadir', '07:20:00', NULL),
(125, 59, 9, 'hadir', '07:04:00', NULL),
(126, 59, 10, 'hadir', '07:00:00', NULL),
(127, 60, 4, 'hadir', '07:19:00', NULL),
(128, 60, 5, 'hadir', '07:22:00', NULL),
(129, 60, 12, 'hadir', '07:02:00', NULL),
(130, 60, 13, 'hadir', '07:28:00', NULL),
(131, 61, 6, 'hadir', '07:06:00', NULL),
(132, 62, 1, 'hadir', '07:21:00', NULL),
(133, 62, 2, 'hadir', '07:19:00', NULL),
(134, 62, 11, 'alfa', '07:04:00', NULL),
(135, 63, 3, 'hadir', '07:08:00', NULL),
(136, 64, 7, 'hadir', '07:02:00', NULL),
(137, 64, 8, 'hadir', '07:19:00', NULL),
(138, 65, 14, 'izin', '07:11:00', NULL),
(139, 65, 15, 'hadir', '07:23:00', NULL),
(140, 66, 9, 'alfa', '07:25:00', NULL),
(141, 66, 10, 'hadir', '07:11:00', NULL),
(142, 67, 4, 'hadir', '07:30:00', NULL),
(143, 67, 5, 'sakit', '07:19:00', NULL),
(144, 67, 12, 'terlambat', '07:00:00', NULL),
(145, 67, 13, 'hadir', '07:00:00', NULL),
(146, 68, 6, 'izin', '07:24:00', NULL),
(147, 69, 1, 'izin', '07:14:00', NULL),
(148, 69, 2, 'hadir', '07:30:00', NULL),
(149, 69, 11, 'sakit', '07:14:00', NULL),
(150, 70, 3, 'hadir', '07:02:00', NULL),
(151, 71, 7, 'hadir', '07:13:00', NULL),
(152, 71, 8, 'hadir', '07:19:00', NULL),
(153, 72, 14, 'hadir', '07:14:00', NULL),
(154, 72, 15, 'hadir', '07:02:00', NULL),
(155, 73, 9, 'alfa', '07:23:00', NULL),
(156, 73, 10, 'hadir', '07:21:00', NULL),
(157, 74, 4, 'hadir', '07:16:00', NULL),
(158, 74, 5, 'hadir', '07:13:00', NULL),
(159, 74, 12, 'hadir', '07:23:00', NULL),
(160, 74, 13, 'hadir', '07:00:00', NULL),
(161, 75, 6, 'hadir', '07:15:00', NULL),
(162, 76, 1, 'hadir', '07:15:00', NULL),
(163, 76, 2, 'sakit', '07:00:00', NULL),
(164, 76, 11, 'sakit', '07:02:00', NULL),
(165, 77, 3, 'sakit', '07:26:00', NULL),
(166, 78, 7, 'alfa', '07:12:00', NULL),
(167, 78, 8, 'terlambat', '07:26:00', NULL),
(168, 79, 14, 'alfa', '07:02:00', NULL),
(169, 79, 15, 'hadir', '07:21:00', NULL),
(170, 80, 9, 'hadir', '07:29:00', NULL),
(171, 80, 10, 'izin', '07:16:00', NULL),
(172, 81, 4, 'hadir', '07:19:00', NULL),
(173, 81, 5, 'terlambat', '07:06:00', NULL),
(174, 81, 12, 'terlambat', '07:05:00', NULL),
(175, 81, 13, 'alfa', '07:04:00', NULL),
(176, 82, 6, 'hadir', '07:00:00', NULL),
(177, 83, 1, 'sakit', '07:15:00', NULL),
(178, 83, 2, 'terlambat', '07:14:00', NULL),
(179, 83, 11, 'terlambat', '07:17:00', NULL),
(180, 84, 3, 'hadir', '07:22:00', NULL),
(181, 85, 7, 'hadir', '07:03:00', NULL),
(182, 85, 8, 'hadir', '07:24:00', NULL),
(183, 86, 14, 'hadir', '07:16:00', NULL),
(184, 86, 15, 'hadir', '07:04:00', NULL),
(185, 87, 9, 'hadir', '07:23:00', NULL),
(186, 87, 10, 'hadir', '07:11:00', NULL),
(187, 88, 4, 'hadir', '07:29:00', NULL),
(188, 88, 5, 'hadir', '07:20:00', NULL),
(189, 88, 12, 'hadir', '07:06:00', NULL),
(190, 88, 13, 'hadir', '07:14:00', NULL),
(191, 89, 6, 'hadir', '07:14:00', NULL),
(192, 90, 1, 'hadir', '07:23:00', NULL),
(193, 90, 2, 'sakit', '07:22:00', NULL),
(194, 90, 11, 'hadir', '07:27:00', NULL),
(195, 91, 3, 'hadir', '07:23:00', NULL),
(196, 92, 7, 'hadir', '07:03:00', NULL),
(197, 92, 8, 'hadir', '07:27:00', NULL),
(198, 93, 14, 'hadir', '07:01:00', NULL),
(199, 93, 15, 'hadir', '07:27:00', NULL),
(200, 94, 9, 'hadir', '07:20:00', NULL),
(201, 94, 10, 'terlambat', '07:28:00', NULL),
(202, 95, 4, 'izin', '07:00:00', NULL),
(203, 95, 5, 'hadir', '07:27:00', NULL),
(204, 95, 12, 'alfa', '07:05:00', NULL),
(205, 95, 13, 'hadir', '07:00:00', NULL),
(206, 96, 6, 'hadir', '07:06:00', NULL),
(207, 97, 1, 'hadir', '07:18:00', NULL),
(208, 97, 2, 'alfa', '07:24:00', NULL),
(209, 97, 11, 'hadir', '07:14:00', NULL),
(210, 98, 3, 'hadir', '07:12:00', NULL),
(211, 99, 7, 'sakit', '07:20:00', NULL),
(212, 99, 8, 'sakit', '07:21:00', NULL),
(213, 100, 14, 'hadir', '07:28:00', NULL),
(214, 100, 15, 'hadir', '07:17:00', NULL),
(215, 101, 9, 'terlambat', '07:29:00', NULL),
(216, 101, 10, 'sakit', '07:16:00', NULL),
(217, 102, 4, 'hadir', '07:24:00', NULL),
(218, 102, 5, 'hadir', '07:09:00', NULL),
(219, 102, 12, 'hadir', '07:21:00', NULL),
(220, 102, 13, 'hadir', '07:23:00', NULL),
(221, 103, 6, 'hadir', '07:11:00', NULL),
(222, 104, 1, 'terlambat', '07:23:00', NULL),
(223, 104, 2, 'hadir', '07:26:00', NULL),
(224, 104, 11, 'hadir', '07:06:00', NULL),
(225, 105, 3, 'hadir', '07:03:00', NULL),
(226, 106, 7, 'hadir', '07:09:00', NULL),
(227, 106, 8, 'hadir', '07:25:00', NULL),
(228, 107, 14, 'hadir', '07:00:00', NULL),
(229, 107, 15, 'hadir', '07:25:00', NULL),
(230, 108, 9, 'hadir', '07:03:00', NULL),
(231, 108, 10, 'hadir', '07:17:00', NULL),
(232, 109, 4, 'terlambat', '07:28:00', NULL),
(233, 109, 5, 'alfa', '07:13:00', NULL),
(234, 109, 12, 'alfa', '07:20:00', NULL),
(235, 109, 13, 'alfa', '07:05:00', NULL),
(236, 110, 6, 'hadir', '07:10:00', NULL),
(237, 111, 1, 'terlambat', '07:02:00', NULL),
(238, 111, 2, 'hadir', '07:01:00', NULL),
(239, 111, 11, 'hadir', '07:24:00', NULL),
(240, 112, 3, 'hadir', '07:19:00', NULL),
(241, 113, 7, 'alfa', '07:27:00', NULL),
(242, 113, 8, 'hadir', '07:01:00', NULL),
(243, 114, 14, 'izin', '07:04:00', NULL),
(244, 114, 15, 'hadir', '07:25:00', NULL),
(245, 115, 9, 'hadir', '07:04:00', NULL),
(246, 115, 10, 'sakit', '07:04:00', NULL),
(247, 116, 4, 'hadir', '07:04:00', NULL),
(248, 116, 5, 'hadir', '07:13:00', NULL),
(249, 116, 12, 'hadir', '07:13:00', NULL),
(250, 116, 13, 'terlambat', '07:29:00', NULL),
(251, 117, 6, 'alfa', '07:00:00', NULL),
(252, 118, 1, 'hadir', '07:06:00', NULL),
(253, 118, 2, 'hadir', '07:08:00', NULL),
(254, 118, 11, 'hadir', '07:29:00', NULL),
(255, 119, 3, 'terlambat', '07:02:00', NULL),
(256, 120, 7, 'hadir', '07:04:00', NULL),
(257, 120, 8, 'terlambat', '07:17:00', NULL),
(258, 121, 14, 'hadir', '07:26:00', NULL),
(259, 121, 15, 'alfa', '07:11:00', NULL),
(260, 122, 9, 'sakit', '07:30:00', NULL),
(261, 122, 10, 'hadir', '07:28:00', NULL),
(262, 123, 4, 'hadir', '07:08:00', NULL),
(263, 123, 5, 'hadir', '07:03:00', NULL),
(264, 123, 12, 'izin', '07:05:00', NULL),
(265, 123, 13, 'hadir', '07:22:00', NULL),
(266, 124, 6, 'hadir', '07:08:00', NULL),
(267, 125, 1, 'alfa', '07:14:00', NULL),
(268, 125, 2, 'alfa', '07:21:00', NULL),
(269, 125, 11, 'hadir', '07:07:00', NULL),
(270, 126, 3, 'hadir', '07:28:00', NULL),
(271, 127, 7, 'hadir', '07:23:00', NULL),
(272, 127, 8, 'alfa', '07:25:00', NULL),
(273, 128, 14, 'sakit', '07:10:00', NULL),
(274, 128, 15, 'hadir', '07:01:00', NULL),
(275, 129, 9, 'hadir', '07:19:00', NULL),
(276, 129, 10, 'hadir', '07:19:00', NULL),
(277, 130, 4, 'hadir', '07:07:00', NULL),
(278, 130, 5, 'hadir', '07:12:00', NULL),
(279, 130, 12, 'hadir', '07:13:00', NULL),
(280, 130, 13, 'hadir', '07:12:00', NULL),
(281, 131, 6, 'hadir', '07:05:00', NULL),
(282, 132, 1, 'hadir', '07:20:00', NULL),
(283, 132, 2, 'hadir', '07:19:00', NULL),
(284, 132, 11, 'hadir', '07:20:00', NULL),
(285, 133, 3, 'hadir', '07:18:00', NULL),
(286, 134, 7, 'hadir', '07:01:00', NULL),
(287, 134, 8, 'terlambat', '07:13:00', NULL),
(288, 135, 14, 'izin', '07:14:00', NULL),
(289, 135, 15, 'hadir', '07:25:00', NULL),
(290, 136, 9, 'sakit', '07:28:00', NULL),
(291, 136, 10, 'terlambat', '07:29:00', NULL),
(292, 137, 4, 'hadir', '07:00:00', NULL),
(293, 137, 5, 'hadir', '07:29:00', NULL),
(294, 137, 12, 'hadir', '07:03:00', NULL),
(295, 137, 13, 'alfa', '07:11:00', NULL),
(296, 138, 6, 'hadir', '07:24:00', NULL),
(297, 139, 1, 'terlambat', '07:25:00', NULL),
(298, 139, 2, 'alfa', '07:02:00', NULL),
(299, 139, 11, 'hadir', '07:02:00', NULL),
(300, 140, 3, 'hadir', '07:14:00', NULL),
(301, 141, 7, 'hadir', '07:21:00', NULL),
(302, 141, 8, 'hadir', '07:03:00', NULL),
(303, 142, 14, 'izin', '07:26:00', NULL),
(304, 142, 15, 'hadir', '07:10:00', NULL),
(305, 143, 9, 'hadir', '07:28:00', NULL),
(306, 143, 10, 'hadir', '07:03:00', NULL),
(307, 144, 4, 'hadir', '07:02:00', NULL),
(308, 144, 5, 'alfa', '07:19:00', NULL),
(309, 144, 12, 'izin', '07:07:00', NULL),
(310, 144, 13, 'hadir', '07:03:00', NULL),
(311, 145, 6, 'hadir', '07:11:00', NULL),
(312, 146, 1, 'hadir', '07:06:00', NULL),
(313, 146, 2, 'hadir', '07:27:00', NULL),
(314, 146, 11, 'hadir', '07:11:00', NULL),
(315, 147, 3, 'hadir', '07:15:00', NULL),
(316, 148, 7, 'hadir', '07:05:00', NULL),
(317, 148, 8, 'sakit', '07:02:00', NULL),
(318, 149, 14, 'hadir', '07:13:00', NULL),
(319, 149, 15, 'sakit', '07:12:00', NULL),
(320, 150, 9, 'sakit', '07:21:00', NULL),
(321, 150, 10, 'alfa', '07:13:00', NULL),
(322, 151, 4, 'alfa', '07:23:00', NULL),
(323, 151, 5, 'hadir', '07:00:00', NULL),
(324, 151, 12, 'hadir', '07:12:00', NULL),
(325, 151, 13, 'izin', '07:06:00', NULL),
(326, 152, 6, 'hadir', '07:12:00', NULL),
(327, 153, 1, 'alfa', '07:10:00', NULL),
(328, 153, 2, 'hadir', '07:17:00', NULL),
(329, 153, 11, 'hadir', '07:09:00', NULL),
(330, 154, 3, 'sakit', '07:27:00', NULL),
(331, 155, 9, 'hadir', '11:32:00', NULL),
(332, 155, 10, 'sakit', '11:32:00', NULL),
(333, 156, 1, 'terlambat', '19:44:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('lms-sma-islam-assyafiiyah-02-bekasi-cache-356a192b7913b04c54574d18c28d46e6395428ab', 'i:1;', 1784032945),
('lms-sma-islam-assyafiiyah-02-bekasi-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1784032945;', 1784032945),
('lms-sma-islam-assyafiiyah-02-bekasi-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1784037062),
('lms-sma-islam-assyafiiyah-02-bekasi-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1784037062;', 1784037062),
('lms-sma-islam-assyafiiyah-02-bekasi-cache-902ba3cda1883801594b6e1b452790cc53948fda', 'i:2;', 1784033132),
('lms-sma-islam-assyafiiyah-02-bekasi-cache-902ba3cda1883801594b6e1b452790cc53948fda:timer', 'i:1784033132;', 1784033132),
('lms-sma-islam-assyafiiyah-02-bekasi-cache-app.settings', 'a:17:{s:12:\"system_roles\";s:119:\"[{\"value\":\"kepala_sekolah\",\"label\":\"Kepala Sekolah\"},{\"value\":\"guru\",\"label\":\"Guru\"},{\"value\":\"siswa\",\"label\":\"Siswa\"}]\";s:11:\"school_name\";s:31:\"SMA Islam Assyafiiyah 02 Bekasi\";s:11:\"school_npsn\";s:0:\"\";s:14:\"school_address\";s:35:\"Jl. Raya Bekasi, Bekasi, Jawa Barat\";s:12:\"school_phone\";s:0:\"\";s:12:\"school_email\";s:31:\"info@assyafiiyah02bekasi.sch.id\";s:16:\"school_principal\";s:14:\"Kepala Sekolah\";s:14:\"att_start_time\";s:5:\"07:00\";s:14:\"att_late_limit\";s:8:\"07:15:00\";s:12:\"att_end_time\";s:5:\"15:30\";s:11:\"att_min_pct\";s:2:\"75\";s:16:\"att_working_days\";s:2:\"22\";s:16:\"att_geofence_lat\";s:9:\"-6.275701\";s:16:\"att_geofence_lng\";s:10:\"106.913330\";s:19:\"att_geofence_radius\";s:3:\"150\";s:22:\"att_face_detection_min\";s:4:\"0.65\";s:18:\"att_face_match_min\";s:4:\"0.72\";}', 1784040126),
('lms-sma-islam-assyafiiyah-02-bekasi-cache-dashboard_lms_trend_7', 'a:4:{s:6:\"labels\";a:7:{i:0;s:11:\"Rab, 08 Jul\";i:1;s:11:\"Kam, 09 Jul\";i:2;s:11:\"Jum, 10 Jul\";i:3;s:11:\"Sab, 11 Jul\";i:4;s:11:\"Min, 12 Jul\";i:5;s:11:\"Sen, 13 Jul\";i:6;s:11:\"Sel, 14 Jul\";}s:9:\"materials\";a:7:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;}s:11:\"assignments\";a:7:{i:0;i:0;i:1;i:1;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:2;i:6;i:0;}s:7:\"quizzes\";a:7:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;}}', 1784037302);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cbt_cheat_logs`
--

CREATE TABLE `cbt_cheat_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `quiz_attempt_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `quiz_id` bigint UNSIGNED NOT NULL,
  `violation_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` int UNSIGNED NOT NULL DEFAULT '1',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `occurred_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` tinyint NOT NULL,
  `major_id` bigint UNSIGNED DEFAULT NULL,
  `homeroom_teacher_id` bigint UNSIGNED DEFAULT NULL,
  `academic_year_id` bigint UNSIGNED NOT NULL,
  `session` enum('pagi','siang') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pagi',
  `room` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_students` tinyint NOT NULL DEFAULT '36',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `grade`, `major_id`, `homeroom_teacher_id`, `academic_year_id`, `session`, `room`, `max_students`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'X MIPA 1', 10, 1, 1, 1, 'pagi', 'R101', 32, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL),
(2, 'X MIPA 2', 10, 1, 2, 1, 'pagi', 'R102', 30, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL),
(3, 'X IPS 1', 10, 2, 4, 1, 'pagi', 'R103', 28, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL),
(4, 'XI MIPA 1', 11, 1, 3, 1, 'pagi', 'R201', 33, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL),
(5, 'XI IPS 1', 11, 2, NULL, 1, 'pagi', 'R202', 29, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL),
(6, 'XII IPA 1', 12, 1, NULL, 1, 'pagi', 'R301', 31, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL),
(7, 'XII IPS 1', 12, 2, NULL, 1, 'pagi', 'R302', 27, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `class_subject`
--

CREATE TABLE `class_subject` (
  `id` bigint UNSIGNED NOT NULL,
  `class_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `teacher_id` bigint UNSIGNED NOT NULL,
  `academic_year_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_subject`
--

INSERT INTO `class_subject` (`id`, `class_id`, `subject_id`, `teacher_id`, `academic_year_id`, `created_at`, `updated_at`) VALUES
(2, 1, 5, 1, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(7, 3, 3, 1, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(11, 4, 5, 1, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(18, 6, 10, 1, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(22, 6, 3, 1, 1, '2026-07-09 03:42:46', '2026-07-09 03:42:46'),
(23, 6, 4, 2, 1, '2026-07-14 11:57:58', '2026-07-14 11:57:58'),
(24, 6, 11, 4, 1, '2026-07-14 12:14:16', '2026-07-14 12:14:16');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` bigint UNSIGNED NOT NULL,
  `commentable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentable_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discussion_threads`
--

CREATE TABLE `discussion_threads` (
  `id` bigint UNSIGNED NOT NULL,
  `class_subject_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `replies_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('success','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'failed',
  `failure_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logged_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `username`, `ip_address`, `user_agent`, `status`, `failure_reason`, `logged_at`, `created_at`, `updated_at`) VALUES
(1, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'success', NULL, '2026-07-07 04:17:13', '2026-07-07 04:17:13', '2026-07-07 04:17:13'),
(2, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'success', NULL, '2026-07-07 05:20:38', '2026-07-07 05:20:38', '2026-07-07 05:20:38'),
(3, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'success', NULL, '2026-07-07 06:35:41', '2026-07-07 06:35:41', '2026-07-07 06:35:41'),
(4, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'success', NULL, '2026-07-07 06:36:34', '2026-07-07 06:36:34', '2026-07-07 06:36:34'),
(5, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'success', NULL, '2026-07-07 06:43:38', '2026-07-07 06:43:38', '2026-07-07 06:43:38'),
(6, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'success', NULL, '2026-07-07 06:53:13', '2026-07-07 06:53:13', '2026-07-07 06:53:13'),
(7, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'success', NULL, '2026-07-07 06:55:45', '2026-07-07 06:55:45', '2026-07-07 06:55:45'),
(8, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'success', NULL, '2026-07-07 06:56:29', '2026-07-07 06:56:29', '2026-07-07 06:56:29'),
(9, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'success', NULL, '2026-07-07 06:59:02', '2026-07-07 06:59:02', '2026-07-07 06:59:02'),
(10, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'success', NULL, '2026-07-07 07:09:05', '2026-07-07 07:09:05', '2026-07-07 07:09:05'),
(11, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'success', NULL, '2026-07-07 07:09:56', '2026-07-07 07:09:56', '2026-07-07 07:09:56'),
(12, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:15:16', '2026-07-09 03:15:16', '2026-07-09 03:15:16'),
(13, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:27:51', '2026-07-09 03:27:51', '2026-07-09 03:27:51'),
(14, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:35:09', '2026-07-09 03:35:09', '2026-07-09 03:35:09'),
(15, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:36:39', '2026-07-09 03:36:39', '2026-07-09 03:36:39'),
(16, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:39:04', '2026-07-09 03:39:04', '2026-07-09 03:39:04'),
(17, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:46:07', '2026-07-09 03:46:07', '2026-07-09 03:46:07'),
(18, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:46:07', '2026-07-09 03:46:07', '2026-07-09 03:46:07'),
(19, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:48:56', '2026-07-09 03:48:56', '2026-07-09 03:48:56'),
(20, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:50:20', '2026-07-09 03:50:20', '2026-07-09 03:50:20'),
(21, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:51:19', '2026-07-09 03:51:19', '2026-07-09 03:51:19'),
(22, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:55:39', '2026-07-09 03:55:39', '2026-07-09 03:55:39'),
(23, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:55:39', '2026-07-09 03:55:39', '2026-07-09 03:55:39'),
(24, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:58:19', '2026-07-09 03:58:19', '2026-07-09 03:58:19'),
(25, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:58:20', '2026-07-09 03:58:20', '2026-07-09 03:58:20'),
(26, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 03:58:51', '2026-07-09 03:58:51', '2026-07-09 03:58:51'),
(27, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 04:01:15', '2026-07-09 04:01:15', '2026-07-09 04:01:15'),
(28, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 05:29:27', '2026-07-09 05:29:27', '2026-07-09 05:29:27'),
(29, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 05:29:28', '2026-07-09 05:29:28', '2026-07-09 05:29:28'),
(30, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 05:29:28', '2026-07-09 05:29:28', '2026-07-09 05:29:28'),
(31, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 05:51:57', '2026-07-09 05:51:57', '2026-07-09 05:51:57'),
(32, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 05:59:38', '2026-07-09 05:59:38', '2026-07-09 05:59:38'),
(33, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 06:03:26', '2026-07-09 06:03:26', '2026-07-09 06:03:26'),
(34, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 06:03:27', '2026-07-09 06:03:27', '2026-07-09 06:03:27'),
(35, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 06:06:11', '2026-07-09 06:06:11', '2026-07-09 06:06:11'),
(36, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 06:06:11', '2026-07-09 06:06:11', '2026-07-09 06:06:11'),
(37, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 06:09:01', '2026-07-09 06:09:01', '2026-07-09 06:09:01'),
(38, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-09 06:09:02', '2026-07-09 06:09:02', '2026-07-09 06:09:02'),
(39, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-10 09:05:03', '2026-07-10 09:05:03', '2026-07-10 09:05:03'),
(40, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-10 09:05:04', '2026-07-10 09:05:04', '2026-07-10 09:05:04'),
(41, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-10 09:39:00', '2026-07-10 09:39:00', '2026-07-10 09:39:00'),
(42, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-10 09:39:00', '2026-07-10 09:39:00', '2026-07-10 09:39:00'),
(43, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-10 09:39:01', '2026-07-10 09:39:01', '2026-07-10 09:39:01'),
(44, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-10 12:24:02', '2026-07-10 12:24:02', '2026-07-10 12:24:02'),
(45, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-12 04:25:28', '2026-07-12 04:25:28', '2026-07-12 04:25:28'),
(46, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-12 04:25:28', '2026-07-12 04:25:28', '2026-07-12 04:25:28'),
(47, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-12 04:28:12', '2026-07-12 04:28:12', '2026-07-12 04:28:12'),
(48, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-12 04:29:48', '2026-07-12 04:29:48', '2026-07-12 04:29:48'),
(49, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-12 04:29:48', '2026-07-12 04:29:48', '2026-07-12 04:29:48'),
(50, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-12 04:29:55', '2026-07-12 04:29:55', '2026-07-12 04:29:55'),
(51, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 03:50:41', '2026-07-13 03:50:41', '2026-07-13 03:50:41'),
(52, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 06:04:36', '2026-07-13 06:04:36', '2026-07-13 06:04:36'),
(53, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 06:05:54', '2026-07-13 06:05:54', '2026-07-13 06:05:54'),
(54, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 06:06:37', '2026-07-13 06:06:37', '2026-07-13 06:06:37'),
(55, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 06:20:09', '2026-07-13 06:20:09', '2026-07-13 06:20:09'),
(56, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 06:28:15', '2026-07-13 06:28:15', '2026-07-13 06:28:15'),
(57, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 06:33:40', '2026-07-13 06:33:40', '2026-07-13 06:33:40'),
(58, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 06:36:33', '2026-07-13 06:36:33', '2026-07-13 06:36:33'),
(59, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 06:39:44', '2026-07-13 06:39:44', '2026-07-13 06:39:44'),
(60, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 06:40:09', '2026-07-13 06:40:09', '2026-07-13 06:40:09'),
(61, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 06:42:40', '2026-07-13 06:42:40', '2026-07-13 06:42:40'),
(62, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 06:59:58', '2026-07-13 06:59:58', '2026-07-13 06:59:58'),
(63, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 07:03:45', '2026-07-13 07:03:45', '2026-07-13 07:03:45'),
(64, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 07:06:58', '2026-07-13 07:06:58', '2026-07-13 07:06:58'),
(65, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 07:07:43', '2026-07-13 07:07:43', '2026-07-13 07:07:43'),
(66, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 07:10:24', '2026-07-13 07:10:24', '2026-07-13 07:10:24'),
(67, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 07:10:58', '2026-07-13 07:10:58', '2026-07-13 07:10:58'),
(68, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 07:11:45', '2026-07-13 07:11:45', '2026-07-13 07:11:45'),
(69, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 07:12:04', '2026-07-13 07:12:04', '2026-07-13 07:12:04'),
(70, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 07:14:09', '2026-07-13 07:14:09', '2026-07-13 07:14:09'),
(71, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-13 07:15:06', '2026-07-13 07:15:06', '2026-07-13 07:15:06'),
(72, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 11:44:43', '2026-07-14 11:44:43', '2026-07-14 11:44:43'),
(73, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 11:50:31', '2026-07-14 11:50:31', '2026-07-14 11:50:31'),
(74, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 11:50:54', '2026-07-14 11:50:54', '2026-07-14 11:50:54'),
(75, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 11:51:21', '2026-07-14 11:51:21', '2026-07-14 11:51:21'),
(76, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 11:54:25', '2026-07-14 11:54:25', '2026-07-14 11:54:25'),
(77, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 11:54:58', '2026-07-14 11:54:58', '2026-07-14 11:54:58'),
(78, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 11:55:52', '2026-07-14 11:55:52', '2026-07-14 11:55:52'),
(79, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 11:57:17', '2026-07-14 11:57:17', '2026-07-14 11:57:17'),
(80, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 12:01:12', '2026-07-14 12:01:12', '2026-07-14 12:01:12'),
(81, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 12:03:51', '2026-07-14 12:03:51', '2026-07-14 12:03:51'),
(82, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 12:23:53', '2026-07-14 12:23:53', '2026-07-14 12:23:53'),
(83, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 12:24:49', '2026-07-14 12:24:49', '2026-07-14 12:24:49'),
(84, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 12:29:04', '2026-07-14 12:29:04', '2026-07-14 12:29:04'),
(85, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 12:41:17', '2026-07-14 12:41:17', '2026-07-14 12:41:17'),
(86, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 12:41:30', '2026-07-14 12:41:30', '2026-07-14 12:41:30'),
(87, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 12:44:58', '2026-07-14 12:44:58', '2026-07-14 12:44:58'),
(88, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 12:50:11', '2026-07-14 12:50:11', '2026-07-14 12:50:11'),
(89, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 12:51:52', '2026-07-14 12:51:52', '2026-07-14 12:51:52'),
(90, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 13:37:09', '2026-07-14 13:37:09', '2026-07-14 13:37:09'),
(91, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 13:42:55', '2026-07-14 13:42:55', '2026-07-14 13:42:55'),
(92, 2, '199001012020121001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 13:43:34', '2026-07-14 13:43:34', '2026-07-14 13:43:34'),
(93, 7, '2025001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 13:44:06', '2026-07-14 13:44:06', '2026-07-14 13:44:06'),
(94, 1, '198001012005011003', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'success', NULL, '2026-07-14 13:50:02', '2026-07-14 13:50:02', '2026-07-14 13:50:02');

-- --------------------------------------------------------

--
-- Table structure for table `majors`
--

CREATE TABLE `majors` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `majors`
--

INSERT INTO `majors` (`id`, `code`, `name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'MIPA', 'Matematika dan Ilmu Pengetahuan Alam', NULL, 1, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL),
(2, 'IPS', 'Ilmu Pengetahuan Sosial', NULL, 1, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL),
(3, 'BAHASA', 'Bahasa dan Budaya', NULL, 1, '2026-07-07 04:17:05', '2026-07-14 12:13:31', '2026-07-14 12:13:31'),
(4, 'TKJ', 'Teknik Komputer dan Jaringan', NULL, 1, '2026-07-07 04:17:05', '2026-07-14 12:11:54', '2026-07-14 12:11:54');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_subject_id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pdf',
  `file_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` int UNSIGNED DEFAULT NULL,
  `mime_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `topic` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2026_04_01_000001_create_academic_years_table', 1),
(4, '2026_04_01_034041_create_settings_table', 1),
(5, '2026_04_01_042011_create_users_table', 1),
(6, '2026_04_01_042221_create_teachers_table', 1),
(7, '2026_04_01_042931_create_classes_table', 1),
(8, '2026_04_01_043107_create_students_table', 1),
(9, '2026_04_01_043256_create_schedules_table', 1),
(10, '2026_04_01_043504_create_attendance_table', 1),
(11, '2026_04_01_043731_create_attendance_details_table', 1),
(12, '2026_05_03_165510_create_sessions_table', 1),
(13, '2026_05_20_084154_create_subjects_table', 1),
(14, '2026_05_20_084200_create_class_subject_table', 1),
(15, '2026_05_20_084200_create_teacher_subject_table', 1),
(16, '2026_05_20_084201_create_materials_table', 1),
(17, '2026_05_20_085001_create_assignments_table', 1),
(18, '2026_05_20_085002_create_assignment_submissions_table', 1),
(19, '2026_05_20_085003_create_assignment_grades_table', 1),
(20, '2026_05_20_085801_create_quizzes_table', 1),
(21, '2026_05_20_085802_create_questions_table', 1),
(22, '2026_05_20_085803_create_question_options_table', 1),
(23, '2026_05_20_085804_create_quiz_attempts_table', 1),
(24, '2026_05_20_085805_create_student_answers_table', 1),
(25, '2026_05_20_090501_create_report_cards_table', 1),
(26, '2026_05_20_090502_create_report_card_details_table', 1),
(27, '2026_05_20_090901_create_student_parents_table', 1),
(28, '2026_05_20_090902_create_announcements_table', 1),
(29, '2026_06_26_100000_add_orangtua_role_to_users_table', 1),
(30, '2026_06_26_200000_create_login_logs_table', 1),
(31, '2026_06_26_200001_create_activity_logs_table', 1),
(32, '2026_06_26_200002_consolidate_user_roles_to_three', 1),
(33, '2026_06_27_100000_create_majors_table', 1),
(34, '2026_06_27_100001_create_semesters_table', 1),
(35, '2026_06_27_100002_add_soft_deletes_to_master_data_tables', 1),
(36, '2026_06_27_100003_refactor_academic_years_and_add_major_id', 1),
(37, '2026_06_28_100000_enhance_materials_for_lms_module', 1),
(38, '2026_06_28_100001_create_discussion_threads_table', 1),
(39, '2026_06_28_100002_create_comments_table', 1),
(40, '2026_06_28_100003_create_academic_calendar_events_table', 1),
(41, '2026_06_28_100004_enhance_announcements_table', 1),
(42, '2026_06_28_221958_add_uuid_and_soft_deletes_to_lms_tables', 1),
(43, '2026_06_28_222330_enhance_question_options_for_matching', 1),
(44, '2026_06_29_100000_create_question_banks_table', 1),
(45, '2026_06_29_100001_enhance_questions_for_question_bank', 1),
(46, '2026_06_29_100002_create_quiz_questions_table', 1),
(47, '2026_06_29_100003_enhance_quizzes_for_cbt', 1),
(48, '2026_06_29_100004_create_quiz_attempt_questions_table', 1),
(49, '2026_06_29_100005_enhance_quiz_attempts_for_cbt', 1),
(50, '2026_06_30_100000_create_modern_attendance_tables', 1),
(51, '2026_07_01_100000_add_production_performance_indexes', 1),
(52, '2026_07_01_200000_create_cbt_cheat_logs_table', 1),
(53, '2026_07_14_194556_add_session_to_classes_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint UNSIGNED NOT NULL,
  `question_bank_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `quiz_id` bigint UNSIGNED DEFAULT NULL,
  `question_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('multiple_choice','essay') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'multiple_choice',
  `difficulty` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `points` smallint UNSIGNED NOT NULL DEFAULT '1',
  `order_number` smallint UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_banks`
--

CREATE TABLE `question_banks` (
  `id` bigint UNSIGNED NOT NULL,
  `class_subject_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

CREATE TABLE `question_options` (
  `id` bigint UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `option_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `match_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `order_number` smallint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_subject_id` bigint UNSIGNED NOT NULL,
  `question_bank_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `duration` smallint UNSIGNED NOT NULL COMMENT 'Duration in minutes',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `show_results` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Apakah siswa bisa melihat hasil setelah selesai',
  `selection_mode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed' COMMENT 'fixed|random|all_bank',
  `random_question_count` smallint UNSIGNED DEFAULT NULL,
  `shuffle_questions` tinyint(1) NOT NULL DEFAULT '0',
  `shuffle_answers` tinyint(1) NOT NULL DEFAULT '0',
  `auto_submit_on_timeout` tinyint(1) NOT NULL DEFAULT '1',
  `pass_score` decimal(5,2) NOT NULL DEFAULT '75.00',
  `questions_per_page` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` bigint UNSIGNED NOT NULL,
  `quiz_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `total_points` decimal(8,2) DEFAULT NULL,
  `earned_points` decimal(8,2) DEFAULT NULL,
  `needs_grading` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ongoing',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempt_questions`
--

CREATE TABLE `quiz_attempt_questions` (
  `id` bigint UNSIGNED NOT NULL,
  `quiz_attempt_id` bigint UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `display_order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `option_order` json DEFAULT NULL COMMENT 'Urutan opsi untuk attempt ini',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` bigint UNSIGNED NOT NULL,
  `quiz_id` bigint UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `order_number` smallint UNSIGNED NOT NULL DEFAULT '0',
  `points_override` smallint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_cards`
--

CREATE TABLE `report_cards` (
  `id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `academic_year_id` bigint UNSIGNED NOT NULL,
  `semester` enum('ganjil','genap') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attendance_hadir` int NOT NULL DEFAULT '0',
  `attendance_izin` int NOT NULL DEFAULT '0',
  `attendance_sakit` int NOT NULL DEFAULT '0',
  `attendance_alfa` int NOT NULL DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_cards`
--

INSERT INTO `report_cards` (`id`, `student_id`, `academic_year_id`, `semester`, `attendance_hadir`, `attendance_izin`, `attendance_sakit`, `attendance_alfa`, `notes`, `is_published`, `created_at`, `updated_at`) VALUES
(1, 7, 1, 'ganjil', 17, 2, 1, 2, NULL, 0, '2026-07-07 04:18:17', '2026-07-09 05:38:49'),
(2, 8, 1, 'ganjil', 15, 1, 2, 1, NULL, 0, '2026-07-07 04:18:17', '2026-07-07 04:18:17');

-- --------------------------------------------------------

--
-- Table structure for table `report_card_details`
--

CREATE TABLE `report_card_details` (
  `id` bigint UNSIGNED NOT NULL,
  `report_card_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `assignment_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `quiz_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `final_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `grade` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `teacher_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_card_details`
--

INSERT INTO `report_card_details` (`id`, `report_card_id`, `subject_id`, `assignment_score`, `quiz_score`, `final_score`, `grade`, `teacher_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 0.00, 0.00, 0.00, 'E', NULL, '2026-07-07 04:18:17', '2026-07-07 04:18:17'),
(2, 1, 5, 0.00, 0.00, 0.00, 'E', NULL, '2026-07-07 04:18:17', '2026-07-07 04:18:17'),
(3, 1, 9, 0.00, 0.00, 0.00, 'E', NULL, '2026-07-07 04:18:17', '2026-07-07 04:18:17'),
(4, 2, 2, 0.00, 0.00, 0.00, 'E', NULL, '2026-07-07 04:18:17', '2026-07-07 04:18:17'),
(5, 2, 5, 0.00, 0.00, 0.00, 'E', NULL, '2026-07-07 04:18:17', '2026-07-07 04:18:17'),
(6, 2, 9, 0.00, 0.00, 0.00, 'E', NULL, '2026-07-07 04:18:17', '2026-07-07 04:18:17');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `class_id` bigint UNSIGNED NOT NULL,
  `teacher_id` bigint UNSIGNED NOT NULL,
  `subject` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `day_of_week` tinyint NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `academic_year_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `class_id`, `teacher_id`, `subject`, `day_of_week`, `start_time`, `end_time`, `room`, `academic_year_id`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 'Bahasa Indonesia', 1, '08:00:00', '09:30:00', 'R-XII-1', 1, '2026-07-09 03:38:49', '2026-07-09 03:38:49'),
(3, 6, 4, 'Ekonomi', 2, '08:15:00', '10:15:00', NULL, 1, '2026-07-14 12:14:16', '2026-07-14 12:14:16');

-- --------------------------------------------------------

--
-- Table structure for table `self_attendance_logs`
--

CREATE TABLE `self_attendance_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `attendance_detail_id` bigint UNSIGNED DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `distance_meters` decimal(10,2) DEFAULT NULL,
  `within_radius` tinyint(1) NOT NULL DEFAULT '0',
  `face_detection_confidence` decimal(5,4) DEFAULT NULL,
  `face_match_confidence` decimal(5,4) DEFAULT NULL,
  `face_matched` tinyint(1) NOT NULL DEFAULT '0',
  `photo_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `check_in_mode` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'full',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `fallback_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `checked_in_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `self_attendance_logs`
--

INSERT INTO `self_attendance_logs` (`id`, `student_id`, `user_id`, `attendance_detail_id`, `attendance_date`, `latitude`, `longitude`, `distance_meters`, `within_radius`, `face_detection_confidence`, `face_match_confidence`, `face_matched`, `photo_path`, `device_type`, `browser`, `ip_address`, `user_agent`, `check_in_mode`, `status`, `fallback_reason`, `notes`, `checked_in_at`, `created_at`, `updated_at`) VALUES
(1, 1, 7, 333, '2026-07-14', -6.2757010, 106.9133300, 0.00, 1, 1.0000, 1.0000, 1, 'attendance/self/1/D0Eg4rRSjzmVr93G1OxsleJJYz6DyY7zv9pYTcb9.jpg', 'desktop', 'Chrome', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'full', 'success', NULL, NULL, '2026-07-14 12:44:44', '2026-07-14 12:44:44', '2026-07-14 12:44:44');

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` bigint UNSIGNED NOT NULL,
  `academic_year_id` bigint UNSIGNED NOT NULL,
  `number` tinyint NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `academic_year_id`, `number`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2024-07-15', '2024-12-21', 0, '2026-07-07 04:17:04', '2026-07-07 04:17:04', NULL),
(2, 1, 2, '2025-01-06', '2025-06-14', 1, '2026-07-07 04:17:04', '2026-07-07 04:17:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1bpL5d2dtNmhFcaP39h5Uma7RSghicBD38g12D6N', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJKeGZ0aUIwTUYxdjRZYXJ3WlBYcExGdnRaREFsWll1Q2dTVUxkeGExIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784030063),
('3aSGnDi7WbN0QYnwvIT9C5y1sdgmL8a2S80yrCSV', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJIV2w4S3AyWXlEbGxRTFNHSFVzT2NhQXpCdWVjUDg2ZkZ1U05Ib2FBIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1784029478),
('63iDbZjUrNhxP9aboTd8Tt3H3KA1HeX3IH2u8B3W', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJBZGNBNjBYU3ZkMGRaVU5rRXlzT3VxR2F2dHpNenVhU01DaXN4Nnp0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo5MDAwXC9sb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1784036560),
('6IuuWuSYTaWzCdqY71EsfqHCY2x0kw3052ZQjvxZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIwNWFxYm50OXYxZFBvaXEzNkloMVczZnVadjNNVFE4eDl3MDNSTWZJIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784031887),
('79ogb8tFuyql0bP44MThPoN6KQc5fzIzLrJHYIaJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJxMjhTaW80eUVMUEV0cHFRTEZjSFNPd0toNHF2dlpQWFdvbjhXSnl3IiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784033090),
('e5hBOgDwwAoFHX9uSFrcxnyCUWYdD6u51U9afIWv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJYYndON2tlNHYyc2tuTmlGTjVwbzY5QW56bGdhN1ZDZ0h4QmhGWWQxIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784029828),
('eKlsX3gNfGOugQZVRPWmyNEgBnosL5lN2kn31Mlu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ3NUxNMXVnZUY2anU2SnYwSlpJbVFoTkxPOXJ1TkxKa1NRdGFJZHdkIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784030235),
('Fe3jCfLK8PLRoWVHvcp6tcsMRRFau8asveNZpNwB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI2ZVZXTGFrOGR3TDF6WnJZb01QbHVZNkdrZjNUT2VSNlVIcDl2U2dqIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784030630),
('FxdhvKPLa9njBHRH8ohkO6kPGsnDJ4DicpbfpaFo', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJVS09YdlFvVEVLdkRiYlpxRE9MbnVreVBScHRWcXRmaXZBT09nZXB0IiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjkwMDBcL2Rhc2hib2FyZCJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6N30=', 1784036695),
('h3ThExWsLgAqSLEhrVJDuNnYjLkpR6xaoBs4XMuN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI5ZWFRWHBRaUZ2OU1xdURXb2tHUGpySlV2bW8zeXBxakRSVGNKV0lMIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784032873),
('lfZHDs91WgO34b4DNADXHjgMSHrQS3Otl1WVue1h', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJPaU9KaGFzeHlkd3dRZnlOTHhFcXdSVmthUFRFcUxZNWpPWFFBYUdTIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784032141),
('NSI1gfpZ5VtLjKOf0NdRZs72sUaWbV1Ky4oEZBge', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJRQVVtaWlwTkozRE4zOGs1NU53QWtnZmdRSVUwaUhrajNBdU5SNGIzIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjkwMDBcL2xvZ2luIn19', 1784036629),
('obCdekIu33WEF0zhqRJFMRCnPrJS70TVwEvz18pL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI0RnVFNjJNN3dNQmVZS1U5Y0lpSUY0YTFndFcydzdJMlZjdHp2NVBSIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjkwMDBcL2xvZ2luIn19', 1784036999),
('p7uQG0fiP4VEOfkasg3W49jJmTfvB70g2f3D2juZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJMd0l0ellCVWQ5Q0pyRXhmTklSVE43eFdhOG9maWVkR0NRQTRKVk9HIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784033407),
('PACKs5RSYOq1wilarrG2LJ2L8tEd6flOumUSPyJm', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJUOXpUa0k5QnZ5eWh2UkNtVEdvVExLVlg2S3U0VzlTT3FPUEFtNGlpIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784029878),
('qf2I8dPRZUpMXimDRcCritw0CHe3Tyq8gUTbF0yj', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI5S2dCZVRKOTZtenJjZ1p4M1RRR2pOWW12dFdZNVM4N2xwZ00wT2Y3IiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784030150),
('svpMxNNVUWJQtNuuMygRMn4jtCjqDBIJeNGjINHk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJId0F1aTF2c25iY1JlcVFUZW1nMXdHRUZyWFdiSXVJaFFxSDVrc3VVIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjkwMDBcL2xvZ2luIn19', 1784036597),
('UyivDLVLpmV6kmpfTPpjcLmkqiG4EPiZE9wSGA4M', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJNdFNoS3JTM2VkdU1TWmJxNjg2dnJVaGxJTlFWWUJEMEhFM3lFQlhkIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784030470),
('VjXyfuYOiUPsYM66pDhP2dR0DrqEhkS9mEoi4c3u', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJGVnp4MEVmVjIzMEpKUkZVWExQR0ZhT2twcnNoeWl6cDZqenNiNTRhIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjkwMDBcL2FkbWluXC90ZWFjaGVycyJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=', 1784037634),
('wKqh02FblCudBOsaqS9ZH674qKx5d8IYlU1aecl0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ3MUJlUXg3TzhxYnUwU1BnbHBFTWdkeVRGcnRmRmlQTW1kN1hRSHhwIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784032888),
('x3ACao24TMdB0c5OENBIgBgBUTBExwixilh3sOi4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ0ZEhZMjlHWVBNbTFxRnB5SnNEaDg3SHFRckwxNUxBWWwwSXFSNUJwIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784030095),
('XLPBhQux4clzTbntRrfSdStc3rvsb9YShIUVNeVI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJmSlR3NzZPaXphM2VPdk9DZlBlQzdEWndza0NOQlVYYlhKdFNOaXdxIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784031830),
('xyV4MfF3hLMQ0mIXal5dO2dFnVwScUdmURIwGBK2', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJSWlpZMTJmUTFlNHdDdFRCMWcyd2NIVGU2NDJja0E5QVhmd0ZoWnloIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784029851),
('zG3qBoPZVwCLyQbfHIlHlICq9ixdppNnexGgtpef', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJpSWttTGNtSjRsckV1cFdXTE5weFo1WEJLMXo2N0dSbjhjejNvRDNhIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjkwMDBcL2xvZ2luIn19', 1784036225),
('zynpjG75R4hMelgme2ocCgGho7OhWdGnSKBZDsFS', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJIRVRMZDA3MEZieXpkZDNIM3Y1UnNCdUppb3d1bEF4NlgwdUx5bDVJIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIn19', 1784033510);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `group`, `created_at`, `updated_at`) VALUES
(1, 'system_roles', '[{\"value\":\"kepala_sekolah\",\"label\":\"Kepala Sekolah\"},{\"value\":\"guru\",\"label\":\"Guru\"},{\"value\":\"siswa\",\"label\":\"Siswa\"}]', 'system', '2026-07-07 04:17:04', '2026-07-07 04:17:04'),
(2, 'school_name', 'SMA Islam Assyafiiyah 02 Bekasi', 'school', '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(3, 'school_npsn', '', 'school', '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(4, 'school_address', 'Jl. Raya Bekasi, Bekasi, Jawa Barat', 'school', '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(5, 'school_phone', '', 'school', '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(6, 'school_email', 'info@assyafiiyah02bekasi.sch.id', 'school', '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(7, 'school_principal', 'Kepala Sekolah', 'school', '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(8, 'att_start_time', '07:00', 'attendance', '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(9, 'att_late_limit', '07:15:00', 'attendance', '2026-07-07 04:17:10', '2026-07-07 06:56:23'),
(10, 'att_end_time', '15:30', 'attendance', '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(11, 'att_min_pct', '75', 'attendance', '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(12, 'att_working_days', '22', 'attendance', '2026-07-07 04:17:10', '2026-07-07 04:17:10'),
(13, 'att_geofence_lat', '-6.275701', 'attendance', '2026-07-07 06:56:23', '2026-07-14 12:41:25'),
(14, 'att_geofence_lng', '106.913330', 'attendance', '2026-07-07 06:56:23', '2026-07-14 12:41:25'),
(15, 'att_geofence_radius', '150', 'attendance', '2026-07-07 06:56:23', '2026-07-07 06:56:23'),
(16, 'att_face_detection_min', '0.65', 'attendance', '2026-07-07 06:56:23', '2026-07-07 06:56:23'),
(17, 'att_face_match_min', '0.72', 'attendance', '2026-07-07 06:56:23', '2026-07-07 06:56:23');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint UNSIGNED NOT NULL,
  `nis` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nisn` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('L','P') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `pob` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `class_id` bigint UNSIGNED NOT NULL,
  `parent_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `nis`, `nisn`, `name`, `gender`, `dob`, `pob`, `address`, `class_id`, `parent_name`, `parent_phone`, `parent_email`, `photo`, `user_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2025001', '0057391234', 'Andi Kurniawan', 'L', '2007-03-15', NULL, NULL, 6, 'Bapak Surya', '081234567890', NULL, NULL, 7, 1, '2026-07-07 04:17:06', '2026-07-07 04:17:06', NULL),
(2, '2025002', '0057391235', 'Budi Santoso', 'L', '2007-06-22', NULL, NULL, 6, 'Ibu Dewi', '081234567891', NULL, NULL, 8, 1, '2026-07-07 04:17:06', '2026-07-07 04:17:06', NULL),
(3, '2025003', '0057391236', 'Citra Dewi', 'P', '2007-09-10', NULL, NULL, 7, 'Bapak Agus', '081234567892', NULL, NULL, 9, 1, '2026-07-07 04:17:06', '2026-07-07 04:17:06', NULL),
(4, '2025004', '0057391237', 'Dian Purnama', 'P', '2008-01-05', NULL, NULL, 4, 'Ibu Sri', '081234567893', NULL, NULL, 10, 1, '2026-07-07 04:17:06', '2026-07-07 04:17:06', NULL),
(5, '2025005', '0057391238', 'Eka Saputra', 'L', '2008-04-18', NULL, NULL, 4, 'Bapak Hendra', '081234567894', NULL, NULL, 11, 1, '2026-07-07 04:17:06', '2026-07-07 04:17:06', NULL),
(6, '2025006', '0057391239', 'Fajar Rizki', 'L', '2008-07-30', NULL, NULL, 5, 'Ibu Yanti', '081234567895', NULL, NULL, 12, 1, '2026-07-07 04:17:07', '2026-07-07 04:17:07', NULL),
(7, '2025007', '0057391240', 'Gina Amalia', 'P', '2009-02-14', NULL, NULL, 1, 'Bapak Rudi', '081234567896', NULL, NULL, 13, 1, '2026-07-07 04:17:07', '2026-07-07 04:17:07', NULL),
(8, '2025008', '0057391241', 'Hendra Gunawan', 'L', '2009-05-25', NULL, NULL, 1, 'Ibu Lina', '081234567897', NULL, NULL, 14, 1, '2026-07-07 04:17:07', '2026-07-07 04:17:07', NULL),
(9, '2025009', '0057391242', 'Indah Lestari', 'P', '2009-08-11', NULL, NULL, 3, 'Bapak Tono', '081234567898', NULL, NULL, 15, 1, '2026-07-07 04:17:07', '2026-07-07 04:17:07', NULL),
(10, '2025010', '0057391243', 'Joko Prabowo', 'L', '2009-11-03', NULL, NULL, 3, 'Ibu Sari', '081234567899', NULL, NULL, 16, 1, '2026-07-07 04:17:07', '2026-07-07 04:17:07', NULL),
(11, '2025011', '0057391244', 'Kartika Sari', 'P', '2007-12-20', NULL, NULL, 6, 'Bapak Bambang', '081234567800', NULL, NULL, 17, 1, '2026-07-07 04:17:08', '2026-07-07 04:17:08', NULL),
(12, '2025012', '0057391245', 'Lukman Hakim', 'L', '2008-03-07', NULL, NULL, 4, 'Ibu Nunung', '081234567801', NULL, NULL, 18, 1, '2026-07-07 04:17:08', '2026-07-07 04:17:08', NULL),
(13, '2025013', '0057391246', 'Maya Indriati', 'P', '2008-06-15', NULL, NULL, 4, 'Bapak Dodi', '081234567802', NULL, NULL, 19, 1, '2026-07-07 04:17:08', '2026-07-07 04:17:08', NULL),
(14, '2025014', '0057391247', 'Nando Setiawan', 'L', '2009-01-22', NULL, NULL, 2, 'Ibu Ratna', '081234567803', NULL, NULL, 20, 1, '2026-07-07 04:17:08', '2026-07-07 04:17:08', NULL),
(15, '2025015', '0057391248', 'Okta Fitriani', 'P', '2009-04-30', NULL, NULL, 2, 'Bapak Andi', '081234567804', NULL, NULL, 21, 1, '2026-07-07 04:17:08', '2026-07-07 04:17:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `id` bigint UNSIGNED NOT NULL,
  `quiz_attempt_id` bigint UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `question_option_id` bigint UNSIGNED DEFAULT NULL,
  `essay_answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_correct` tinyint(1) DEFAULT NULL,
  `matching_answer` json DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL COMMENT 'Nilai per soal, berguna untuk essay manual grading',
  `feedback` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `graded_by` bigint UNSIGNED DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_face_profiles`
--

CREATE TABLE `student_face_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `landmark_descriptor` json DEFAULT NULL,
  `reference_photo_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_face_profiles`
--

INSERT INTO `student_face_profiles` (`id`, `student_id`, `landmark_descriptor`, `reference_photo_path`, `created_at`, `updated_at`) VALUES
(1, 1, '[0, 0, -0.5596629477581687, -0.6316980005791256, 0.432579613572215, -0.7560149321874933, -0.2617678494887925, 0.43984034934313215, 0.30645956311042044, 0.3732508483503631, 0.0515326192560945, 0.8936073088685796, 0.02553602379310125, -0.47153866121153104]', NULL, '2026-07-07 06:49:33', '2026-07-07 06:49:35');

-- --------------------------------------------------------

--
-- Table structure for table `student_parents`
--

CREATE TABLE `student_parents` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `grade` tinyint DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `code`, `name`, `group`, `description`, `grade`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PAI', 'Pendidikan Agama Islam', 'Muatan Nasional', NULL, NULL, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10', NULL),
(2, 'PPKN', 'Pendidikan Pancasila dan Kewarganegaraan', 'Muatan Nasional', NULL, NULL, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10', NULL),
(3, 'B-IND', 'Bahasa Indonesia', 'Muatan Nasional', NULL, NULL, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10', NULL),
(4, 'MTK-W', 'Matematika Wajib', 'Muatan Nasional', NULL, NULL, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10', NULL),
(5, 'B-ING', 'Bahasa Inggris', 'Muatan Nasional', NULL, NULL, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10', NULL),
(6, 'SEJ', 'Sejarah Indonesia', 'Muatan Nasional', NULL, NULL, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10', NULL),
(7, 'MTK-P', 'Matematika Peminatan', 'Muatan Peminatan', NULL, NULL, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10', NULL),
(8, 'FIS', 'Fisika', 'Muatan Peminatan', NULL, NULL, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10', NULL),
(9, 'KIM', 'Kimia', 'Muatan Peminatan', NULL, NULL, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10', NULL),
(10, 'BIO', 'Biologi', 'Muatan Peminatan', NULL, NULL, 1, '2026-07-07 04:17:10', '2026-07-07 04:17:10', NULL),
(11, 'EKO', 'Ekonomi', 'Muatan Peminatan', NULL, NULL, 1, '2026-07-09 06:28:56', '2026-07-09 06:28:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` bigint UNSIGNED NOT NULL,
  `nip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `nip`, `name`, `subject`, `phone`, `email`, `photo`, `user_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '199001012020121001', 'Dra. Hj. Siti Rahayu, M.Pd', 'Bahasa Indonesia, Bahasa Inggris, Biologi', '081298765432', 'siti.rahayu@assyafiiyah02bekasi.sch.id', NULL, 2, 1, '2026-07-07 04:17:05', '2026-07-09 06:30:18', NULL),
(2, '198503052019031002', 'Budi Santoso, S.Pd', 'Matematika', '081298765433', 'budi.santoso@assyafiiyah02bekasi.sch.id', NULL, 3, 1, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL),
(3, '199208152021122003', 'Indah Permata, S.Si', 'Fisika', '081298765434', 'indah.permata@assyafiiyah02bekasi.sch.id', NULL, 4, 1, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL),
(4, '198712302018031004', 'Ahmad Firdaus, S.Sos', 'Ekonomi', '081298765435', 'ahmad.firdaus@assyafiiyah02bekasi.sch.id', NULL, 5, 1, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL),
(5, '199504122022121005', 'Dewi Kusuma, S.Pd', 'Bahasa Inggris', '081298765436', 'dewi.kusuma@assyafiiyah02bekasi.sch.id', NULL, 6, 1, '2026-07-07 04:17:05', '2026-07-07 04:17:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subject`
--

CREATE TABLE `teacher_subject` (
  `id` bigint UNSIGNED NOT NULL,
  `teacher_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teacher_subject`
--

INSERT INTO `teacher_subject` (`id`, `teacher_id`, `subject_id`) VALUES
(1, 1, 3),
(9, 1, 5),
(7, 1, 10),
(2, 2, 4),
(3, 2, 7),
(4, 3, 8),
(5, 4, 11),
(6, 5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('kepala_sekolah','guru','siswa') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'guru',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `role`, `avatar`, `last_login`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '198001012005011003', '$2y$12$uRwmSuJCz7GstgS68zWk3.r3G54878ry6j3c/2qll8/AUfsG3jm1y', 'Kepala Sekolah', 'kepala@assyafiiyah02bekasi.sch.id', 'kepala_sekolah', NULL, '2026-07-14 20:50:02', 1, NULL, '2026-07-07 04:17:04', '2026-07-14 13:50:02'),
(2, '199001012020121001', '$2y$12$m/YFh8PwX404ngQmZNUvk.yW2kyrNpTsDTUC4f952w6BBx8vXw30y', 'Dra. Hj. Siti Rahayu, M.Pd', 'siti.rahayu@assyafiiyah02bekasi.sch.id', 'guru', NULL, '2026-07-14 20:43:34', 1, NULL, '2026-07-07 04:17:04', '2026-07-14 13:43:34'),
(3, '198503052019031002', '$2y$12$Xw1lSW7RR4XBCsQQFD.Zf.HiLx4iknV8C9VPwrXl6EqdsP8exyb3m', 'Budi Santoso, S.Pd', 'budi.santoso@assyafiiyah02bekasi.sch.id', 'guru', NULL, NULL, 1, NULL, '2026-07-07 04:17:05', '2026-07-07 04:17:05'),
(4, '199208152021122003', '$2y$12$ohLX6cfs7GywKJBSXQtCfeU6JQ1Nc9Cjr3wUun5Pzo7zBXCllSDie', 'Indah Permata, S.Si', 'indah.permata@assyafiiyah02bekasi.sch.id', 'guru', NULL, NULL, 1, NULL, '2026-07-07 04:17:05', '2026-07-07 04:17:05'),
(5, '198712302018031004', '$2y$12$st/Fob4PqkovHcYTkHu7Jen/.LibYpRTQwwFUlnoSN6eYioX9tXZe', 'Ahmad Firdaus, S.Sos', 'ahmad.firdaus@assyafiiyah02bekasi.sch.id', 'guru', NULL, NULL, 1, NULL, '2026-07-07 04:17:05', '2026-07-07 04:17:05'),
(6, '199504122022121005', '$2y$12$7AFp1r7/x1EHuHQ/E6YaaeBhggdsDJL6dP4yDK.ii8W.aKwLnTkXq', 'Dewi Kusuma, S.Pd', 'dewi.kusuma@assyafiiyah02bekasi.sch.id', 'guru', NULL, NULL, 1, NULL, '2026-07-07 04:17:05', '2026-07-07 04:17:05'),
(7, '2025001', '$2y$12$MoTdAKG69rSAcYbEc6ZoB.d3Z4/a.g4H0pVj1wbCsw3pMVX87mb3u', 'Andi Kurniawan', NULL, 'siswa', NULL, '2026-07-14 20:44:06', 1, NULL, '2026-07-07 04:17:06', '2026-07-14 13:44:06'),
(8, '2025002', '$2y$12$BIjA1wQqdD.7pSGQ9JqzWO3oKnoBvu8Na2/kJozMd1KxqRZr6zM1O', 'Budi Santoso', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:06', '2026-07-07 04:17:06'),
(9, '2025003', '$2y$12$HBtJq570727DLT1qDAMnqeoeYU9r2ZasBdLwLwgpTx3V3Z54T3qpS', 'Citra Dewi', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:06', '2026-07-07 04:17:06'),
(10, '2025004', '$2y$12$0fshbVkEpYyfPW29FjriCOvG6ZUAVWdy2wJLb7MBl5sFOYsh3ganu', 'Dian Purnama', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:06', '2026-07-07 04:17:06'),
(11, '2025005', '$2y$12$4Wa3KMXUIDCrB2CU22/DzuJFFj15knHJ/vunQ90FyMK3DZ9swsCKy', 'Eka Saputra', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:06', '2026-07-07 04:17:06'),
(12, '2025006', '$2y$12$FCRzECStwlcXWd3bYL3W3e4fxlBcNvySvvVUWBcdyGZzSK0sU22D2', 'Fajar Rizki', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:07', '2026-07-07 04:17:07'),
(13, '2025007', '$2y$12$OneD1clvHV4bgVqrrxt7R.FS3qYCrhySGnMVmYOfiz/2.8dcFzw.W', 'Gina Amalia', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:07', '2026-07-07 04:17:07'),
(14, '2025008', '$2y$12$Vi8SlI582Kc9BcK6vUnR9e7cqP7Hpkzv.sZ1ufRqpHBtkrh/0J7r2', 'Hendra Gunawan', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:07', '2026-07-07 04:17:07'),
(15, '2025009', '$2y$12$oKuuxErICOxHnEHVLmMf5OtAvqAbX4YEYLRbt357Kh2oU5QdrqvTC', 'Indah Lestari', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:07', '2026-07-07 04:17:07'),
(16, '2025010', '$2y$12$69vA5eZOk3Xr2QrDTwORaeD8IqDStWniCOcij8X.WdgWBuw47yS0.', 'Joko Prabowo', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:07', '2026-07-07 04:17:07'),
(17, '2025011', '$2y$12$a2UdXNcPpg43ZGX.HkRDzeKCfKf92Z4Lx/XrLPSxArn2jxKORpIbq', 'Kartika Sari', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:08', '2026-07-07 04:17:08'),
(18, '2025012', '$2y$12$A5aGvfuv4a2dEN2eys/Hd.JDFaQhZdVhhnRdH.XJBHz/OVMpDbFuS', 'Lukman Hakim', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:08', '2026-07-07 04:17:08'),
(19, '2025013', '$2y$12$gVxKEg1kgzUKhqzMSCbME.E5hKDAy3B18iBjkdPeWMjGWMiwZI0Hu', 'Maya Indriati', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:08', '2026-07-07 04:17:08'),
(20, '2025014', '$2y$12$Z6lQ.gihlrDyapWEjaa.aOt47te9vDScW47GfmCirNN7kGQ8Y16dO', 'Nando Setiawan', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:08', '2026-07-07 04:17:08'),
(21, '2025015', '$2y$12$uflG3MtSQjft/2IXckt4qu3RJoshhki42FjWKEt/966brV2WHXEPC', 'Okta Fitriani', NULL, 'siswa', NULL, NULL, 1, NULL, '2026-07-07 04:17:08', '2026-07-09 06:56:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_calendar_events`
--
ALTER TABLE `academic_calendar_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `academic_calendar_events_academic_year_id_foreign` (`academic_year_id`),
  ADD KEY `academic_calendar_events_class_id_foreign` (`class_id`),
  ADD KEY `academic_calendar_events_created_by_foreign` (`created_by`),
  ADD KEY `academic_calendar_events_start_date_end_date_index` (`start_date`,`end_date`),
  ADD KEY `academic_calendar_events_category_index` (`category`);

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_logged_at_index` (`user_id`,`logged_at`),
  ADD KEY `activity_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  ADD KEY `activity_logs_action_index` (`action`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcements_class_id_foreign` (`class_id`),
  ADD KEY `announcements_created_by_foreign` (`created_by`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assignments_uuid_unique` (`uuid`),
  ADD KEY `assignments_class_subject_id_due_date_index` (`class_subject_id`,`due_date`);

--
-- Indexes for table `assignment_grades`
--
ALTER TABLE `assignment_grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assignment_grades_assignment_submission_id_unique` (`assignment_submission_id`),
  ADD KEY `assignment_grades_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assignment_submissions_assignment_id_student_id_unique` (`assignment_id`,`student_id`),
  ADD KEY `assignment_submissions_assignment_id_is_late_index` (`assignment_id`,`is_late`),
  ADD KEY `assignment_submissions_student_submitted_idx` (`student_id`,`submitted_at`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_date_class_id_session_unique` (`date`,`class_id`,`session`),
  ADD KEY `attendance_class_id_foreign` (`class_id`),
  ADD KEY `attendance_teacher_id_foreign` (`teacher_id`),
  ADD KEY `attendance_date_class_id_index` (`date`,`class_id`);

--
-- Indexes for table `attendance_details`
--
ALTER TABLE `attendance_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_details_attendance_id_student_id_unique` (`attendance_id`,`student_id`),
  ADD KEY `attendance_details_student_id_status_index` (`student_id`,`status`),
  ADD KEY `attendance_details_student_status_idx` (`student_id`,`status`),
  ADD KEY `attendance_details_attendance_idx` (`attendance_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cbt_cheat_logs`
--
ALTER TABLE `cbt_cheat_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbt_cheat_logs_quiz_id_foreign` (`quiz_id`),
  ADD KEY `cbt_cheat_logs_quiz_attempt_id_violation_type_index` (`quiz_attempt_id`,`violation_type`),
  ADD KEY `cbt_cheat_logs_student_id_quiz_id_index` (`student_id`,`quiz_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `classes_name_academic_year_id_unique` (`name`,`academic_year_id`),
  ADD KEY `classes_homeroom_teacher_id_foreign` (`homeroom_teacher_id`),
  ADD KEY `classes_academic_year_id_foreign` (`academic_year_id`),
  ADD KEY `classes_major_id_foreign` (`major_id`);

--
-- Indexes for table `class_subject`
--
ALTER TABLE `class_subject`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `class_subject_class_id_subject_id_academic_year_id_unique` (`class_id`,`subject_id`,`academic_year_id`),
  ADD KEY `class_subject_subject_id_foreign` (`subject_id`),
  ADD KEY `class_subject_teacher_id_foreign` (`teacher_id`),
  ADD KEY `class_subject_academic_year_id_foreign` (`academic_year_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_commentable_type_commentable_id_index` (`commentable_type`,`commentable_id`),
  ADD KEY `comments_user_id_foreign` (`user_id`),
  ADD KEY `comments_parent_id_foreign` (`parent_id`),
  ADD KEY `comments_commentable_type_commentable_id_created_at_index` (`commentable_type`,`commentable_id`,`created_at`);

--
-- Indexes for table `discussion_threads`
--
ALTER TABLE `discussion_threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discussion_threads_user_id_foreign` (`user_id`),
  ADD KEY `discussion_threads_class_subject_id_is_pinned_index` (`class_subject_id`,`is_pinned`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_logs_user_id_logged_at_index` (`user_id`,`logged_at`),
  ADD KEY `login_logs_username_logged_at_index` (`username`,`logged_at`),
  ADD KEY `login_logs_status_index` (`status`);

--
-- Indexes for table `majors`
--
ALTER TABLE `majors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `majors_code_unique` (`code`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `materials_uuid_unique` (`uuid`),
  ADD KEY `materials_class_subject_id_topic_index` (`class_subject_id`,`topic`),
  ADD KEY `materials_class_subject_id_is_published_index` (`class_subject_id`,`is_published`),
  ADD KEY `materials_created_by_foreign` (`created_by`),
  ADD KEY `materials_class_subject_created_idx` (`class_subject_id`,`created_at`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_quiz_id_foreign` (`quiz_id`),
  ADD KEY `questions_created_by_foreign` (`created_by`),
  ADD KEY `questions_question_bank_id_type_is_active_index` (`question_bank_id`,`type`,`is_active`),
  ADD KEY `questions_question_bank_id_difficulty_index` (`question_bank_id`,`difficulty`);

--
-- Indexes for table `question_banks`
--
ALTER TABLE `question_banks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_banks_created_by_foreign` (`created_by`),
  ADD KEY `question_banks_class_subject_id_is_active_index` (`class_subject_id`,`is_active`);

--
-- Indexes for table `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_options_question_id_foreign` (`question_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quizzes_uuid_unique` (`uuid`),
  ADD KEY `quizzes_class_subject_id_foreign` (`class_subject_id`),
  ADD KEY `quizzes_question_bank_id_foreign` (`question_bank_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quiz_attempts_quiz_id_student_id_unique` (`quiz_id`,`student_id`),
  ADD KEY `quiz_attempts_student_created_idx` (`student_id`,`created_at`),
  ADD KEY `quiz_attempts_quiz_status_idx` (`quiz_id`,`status`);

--
-- Indexes for table `quiz_attempt_questions`
--
ALTER TABLE `quiz_attempt_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quiz_attempt_questions_quiz_attempt_id_question_id_unique` (`quiz_attempt_id`,`question_id`),
  ADD KEY `quiz_attempt_questions_question_id_foreign` (`question_id`),
  ADD KEY `quiz_attempt_questions_quiz_attempt_id_display_order_index` (`quiz_attempt_id`,`display_order`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quiz_questions_quiz_id_question_id_unique` (`quiz_id`,`question_id`),
  ADD KEY `quiz_questions_question_id_foreign` (`question_id`),
  ADD KEY `quiz_questions_quiz_id_order_number_index` (`quiz_id`,`order_number`);

--
-- Indexes for table `report_cards`
--
ALTER TABLE `report_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_cards_student_id_academic_year_id_semester_unique` (`student_id`,`academic_year_id`,`semester`),
  ADD KEY `report_cards_year_semester_idx` (`academic_year_id`,`semester`);

--
-- Indexes for table `report_card_details`
--
ALTER TABLE `report_card_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_card_details_report_card_id_subject_id_unique` (`report_card_id`,`subject_id`),
  ADD KEY `report_card_details_subject_id_foreign` (`subject_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_class_id_foreign` (`class_id`),
  ADD KEY `schedules_teacher_id_foreign` (`teacher_id`),
  ADD KEY `schedules_academic_year_id_foreign` (`academic_year_id`);

--
-- Indexes for table `self_attendance_logs`
--
ALTER TABLE `self_attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `self_attendance_logs_user_id_foreign` (`user_id`),
  ADD KEY `self_attendance_logs_attendance_detail_id_foreign` (`attendance_detail_id`),
  ADD KEY `self_attendance_logs_student_id_attendance_date_index` (`student_id`,`attendance_date`),
  ADD KEY `self_attendance_logs_attendance_date_status_index` (`attendance_date`,`status`),
  ADD KEY `self_attendance_student_date_idx` (`student_id`,`attendance_date`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `semesters_academic_year_id_number_unique` (`academic_year_id`,`number`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`),
  ADD KEY `sessions_last_activity_idx` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_nis_unique` (`nis`),
  ADD UNIQUE KEY `students_nisn_unique` (`nisn`),
  ADD KEY `students_user_id_foreign` (`user_id`),
  ADD KEY `students_class_status_idx` (`class_id`,`status`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_answers_quiz_attempt_id_question_id_unique` (`quiz_attempt_id`,`question_id`),
  ADD KEY `student_answers_question_id_foreign` (`question_id`),
  ADD KEY `student_answers_question_option_id_foreign` (`question_option_id`),
  ADD KEY `student_answers_graded_by_foreign` (`graded_by`),
  ADD KEY `student_answers_quiz_attempt_id_question_id_index` (`quiz_attempt_id`,`question_id`);

--
-- Indexes for table `student_face_profiles`
--
ALTER TABLE `student_face_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_face_profiles_student_id_unique` (`student_id`);

--
-- Indexes for table `student_parents`
--
ALTER TABLE `student_parents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_parents_user_id_student_id_unique` (`user_id`,`student_id`),
  ADD KEY `student_parents_student_id_foreign` (`student_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subjects_code_unique` (`code`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teachers_nip_unique` (`nip`),
  ADD KEY `teachers_user_id_foreign` (`user_id`);

--
-- Indexes for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_subject_teacher_id_subject_id_unique` (`teacher_id`,`subject_id`),
  ADD KEY `teacher_subject_subject_id_foreign` (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_calendar_events`
--
ALTER TABLE `academic_calendar_events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `assignment_grades`
--
ALTER TABLE `assignment_grades`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `attendance_details`
--
ALTER TABLE `attendance_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=334;

--
-- AUTO_INCREMENT for table `cbt_cheat_logs`
--
ALTER TABLE `cbt_cheat_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `class_subject`
--
ALTER TABLE `class_subject`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discussion_threads`
--
ALTER TABLE `discussion_threads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `majors`
--
ALTER TABLE `majors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question_banks`
--
ALTER TABLE `question_banks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_attempt_questions`
--
ALTER TABLE `quiz_attempt_questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_cards`
--
ALTER TABLE `report_cards`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `report_card_details`
--
ALTER TABLE `report_card_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `self_attendance_logs`
--
ALTER TABLE `self_attendance_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_face_profiles`
--
ALTER TABLE `student_face_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_parents`
--
ALTER TABLE `student_parents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_calendar_events`
--
ALTER TABLE `academic_calendar_events`
  ADD CONSTRAINT `academic_calendar_events_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `academic_calendar_events_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `academic_calendar_events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `announcements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_class_subject_id_foreign` FOREIGN KEY (`class_subject_id`) REFERENCES `class_subject` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_grades`
--
ALTER TABLE `assignment_grades`
  ADD CONSTRAINT `assignment_grades_assignment_submission_id_foreign` FOREIGN KEY (`assignment_submission_id`) REFERENCES `assignment_submissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_grades_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD CONSTRAINT `assignment_submissions_assignment_id_foreign` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_details`
--
ALTER TABLE `attendance_details`
  ADD CONSTRAINT `attendance_details_attendance_id_foreign` FOREIGN KEY (`attendance_id`) REFERENCES `attendance` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_details_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbt_cheat_logs`
--
ALTER TABLE `cbt_cheat_logs`
  ADD CONSTRAINT `cbt_cheat_logs_quiz_attempt_id_foreign` FOREIGN KEY (`quiz_attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbt_cheat_logs_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbt_cheat_logs_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `classes_homeroom_teacher_id_foreign` FOREIGN KEY (`homeroom_teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `classes_major_id_foreign` FOREIGN KEY (`major_id`) REFERENCES `majors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `class_subject`
--
ALTER TABLE `class_subject`
  ADD CONSTRAINT `class_subject_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_subject_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_subject_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_subject_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discussion_threads`
--
ALTER TABLE `discussion_threads`
  ADD CONSTRAINT `discussion_threads_class_subject_id_foreign` FOREIGN KEY (`class_subject_id`) REFERENCES `class_subject` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussion_threads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_class_subject_id_foreign` FOREIGN KEY (`class_subject_id`) REFERENCES `class_subject` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `materials_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `questions_question_bank_id_foreign` FOREIGN KEY (`question_bank_id`) REFERENCES `question_banks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `question_banks`
--
ALTER TABLE `question_banks`
  ADD CONSTRAINT `question_banks_class_subject_id_foreign` FOREIGN KEY (`class_subject_id`) REFERENCES `class_subject` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `question_banks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `question_options`
--
ALTER TABLE `question_options`
  ADD CONSTRAINT `question_options_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_class_subject_id_foreign` FOREIGN KEY (`class_subject_id`) REFERENCES `class_subject` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_question_bank_id_foreign` FOREIGN KEY (`question_bank_id`) REFERENCES `question_banks` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempt_questions`
--
ALTER TABLE `quiz_attempt_questions`
  ADD CONSTRAINT `quiz_attempt_questions_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempt_questions_quiz_attempt_id_foreign` FOREIGN KEY (`quiz_attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_questions_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `report_cards`
--
ALTER TABLE `report_cards`
  ADD CONSTRAINT `report_cards_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_cards_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `report_card_details`
--
ALTER TABLE `report_card_details`
  ADD CONSTRAINT `report_card_details_report_card_id_foreign` FOREIGN KEY (`report_card_id`) REFERENCES `report_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_card_details_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `self_attendance_logs`
--
ALTER TABLE `self_attendance_logs`
  ADD CONSTRAINT `self_attendance_logs_attendance_detail_id_foreign` FOREIGN KEY (`attendance_detail_id`) REFERENCES `attendance_details` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `self_attendance_logs_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `self_attendance_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `semesters`
--
ALTER TABLE `semesters`
  ADD CONSTRAINT `semesters_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `student_answers_graded_by_foreign` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_answers_question_option_id_foreign` FOREIGN KEY (`question_option_id`) REFERENCES `question_options` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_answers_quiz_attempt_id_foreign` FOREIGN KEY (`quiz_attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_face_profiles`
--
ALTER TABLE `student_face_profiles`
  ADD CONSTRAINT `student_face_profiles_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_parents`
--
ALTER TABLE `student_parents`
  ADD CONSTRAINT `student_parents_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_parents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  ADD CONSTRAINT `teacher_subject_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_subject_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
