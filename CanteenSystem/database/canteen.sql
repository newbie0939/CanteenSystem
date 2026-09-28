-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2026 at 09:21 PM
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
-- Database: `canteen_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `admin_id` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `admin_id`, `name`, `email`, `password`, `profile_image`, `created_at`) VALUES
(1, 'ADMIN001', 'Canteen Administrator', 'admin@canteen.com', '$2y$10$na8kkkZ.9cFmiV6Ju/8c5OBaDm7T7JAjlLd2zTshcwWltRSwQwZDi', NULL, '2026-09-27 10:37:09');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `image`, `category`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 'Burger', 'Classic canteen burger', 50.00, 'burger.jpg', 'Meals', 1, '2026-09-27 10:35:27', '2026-09-28 09:25:48'),
(2, 'Fries', 'Crispy french fries', 30.00, 'fries.jpg', 'Snacks', 1, '2026-09-27 10:35:27', '2026-09-28 14:42:59'),
(3, 'Chicken Adobo', 'Chicken adobo with rice', 80.00, 'chicken-adobo.jpg', 'Meals', 1, '2026-09-27 10:35:27', '2026-09-28 14:43:31'),
(4, 'Turon', 'Sweet banana spring roll', 20.00, 'turon.jpg', 'Snacks', 1, '2026-09-27 10:35:27', '2026-09-28 14:43:46'),
(5, 'Halo-Halo', 'Classic Filipino dessert', 60.00, 'halo-halo.jpg', 'Dessert', 1, '2026-09-27 10:35:27', '2026-09-28 14:45:06'),
(6, 'Menudo', 'Pork menudo with rice', 75.00, 'menudo.jpg', 'Meals', 1, '2026-09-27 10:35:27', '2026-09-28 14:44:10'),
(7, 'Juice', 'Refreshing fruit juice', 25.00, 'juice.jpg', 'Drinks', 1, '2026-09-27 10:35:27', '2026-09-28 14:44:49'),
(8, 'Leche Flan', 'Filipino caramel custard', 45.00, 'leche-flan.jpg', 'Dessert', 1, '2026-09-27 10:35:27', '2026-09-28 14:44:33'),
(9, 'Soft Drink', 'Chilled soft drink', 25.00, 'soft-drinks.jpg', 'Drinks', 1, '2026-09-27 10:35:27', '2026-09-27 16:45:41');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_type` enum('student','admin') NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_type` enum('student','admin') NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `student_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `admin_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_type`, `sender_id`, `receiver_type`, `receiver_id`, `order_id`, `message`, `student_deleted`, `admin_deleted`, `is_read`, `created_at`) VALUES
(1, 'student', 1, 'admin', 1, NULL, 'ds', 1, 1, 0, '2026-09-27 13:49:38'),
(2, 'student', 1, 'admin', 1, NULL, 's', 1, 1, 1, '2026-09-27 14:29:41'),
(3, 'admin', 1, 'student', 1, NULL, 's', 1, 1, 1, '2026-09-27 15:23:21'),
(4, 'student', 1, 'admin', 1, NULL, 's', 1, 1, 1, '2026-09-27 15:23:43'),
(5, 'student', 1, 'admin', 1, NULL, 's', 1, 1, 0, '2026-09-28 14:28:21'),
(6, 'admin', 1, 'student', 1, NULL, 's', 1, 1, 1, '2026-09-28 15:36:55'),
(7, 'admin', 1, 'student', 1, NULL, 's', 0, 1, 0, '2026-09-28 15:43:17');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_type` enum('student','admin') NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_type`, `user_id`, `order_id`, `type`, `message`, `is_read`, `is_deleted`, `created_at`) VALUES
(1, 'admin', 1, NULL, 'new_message', 'You received a new message from a student.', 0, 1, '2026-09-27 13:49:38'),
(2, 'admin', 1, NULL, 'new_message', 'You received a new message from a student.', 0, 1, '2026-09-27 14:29:41'),
(3, 'student', 1, 1, 'order_created', 'Your order ORD-20260927171123-381 has been received and is now Pending.', 0, 1, '2026-09-27 15:11:23'),
(4, 'admin', 1, 1, 'new_order', 'New order ORD-20260927171123-381 has been received from a student.', 1, 1, '2026-09-27 15:11:23'),
(5, 'student', 1, 1, 'order_status', 'Your order ORD-20260927171123-381 is now Preparing.', 0, 1, '2026-09-27 15:12:11'),
(6, 'student', 1, 1, 'order_status', 'Your order ORD-20260927171123-381 is now Ready.', 0, 1, '2026-09-27 15:12:48'),
(7, 'student', 1, 1, 'order_status', 'Your order ORD-20260927171123-381 is now Completed.', 0, 1, '2026-09-27 15:13:01'),
(8, 'student', 1, 2, 'order_created', 'Your order ORD-20260927171536-809 has been received and is now Pending.', 0, 1, '2026-09-27 15:15:36'),
(9, 'admin', 1, 2, 'new_order', 'New order ORD-20260927171536-809 has been received from a student.', 1, 1, '2026-09-27 15:15:36'),
(10, 'student', 1, 2, 'order_status', 'Your order ORD-20260927171536-809 is now Preparing.', 0, 1, '2026-09-27 15:21:25'),
(11, 'student', 1, 2, 'order_status', 'Your order ORD-20260927171536-809 is now Ready.', 0, 1, '2026-09-27 15:21:30'),
(12, 'student', 1, 2, 'order_status', 'Your order ORD-20260927171536-809 is now Completed.', 0, 1, '2026-09-27 15:21:32'),
(13, 'student', 1, NULL, 'new_message', 'You received a new message from the administrator.', 0, 1, '2026-09-27 15:23:21'),
(14, 'admin', 1, NULL, 'new_message', 'You received a new message from a student.', 1, 1, '2026-09-27 15:23:43'),
(15, 'student', 1, 3, 'order_created', 'Your order ORD-20260928101004-176 has been received and is now Pending.', 1, 1, '2026-09-28 08:10:04'),
(16, 'admin', 1, 3, 'new_order', 'New order ORD-20260928101004-176 has been received from a student.', 0, 1, '2026-09-28 08:10:04'),
(17, 'student', 1, 4, 'order_created', 'Your order ORD-20260928101101-524 has been received and is now Pending.', 1, 1, '2026-09-28 08:11:01'),
(18, 'admin', 1, 4, 'new_order', 'New order ORD-20260928101101-524 has been received from a student.', 0, 1, '2026-09-28 08:11:01'),
(19, 'student', 1, 3, 'order_status', 'Your order ORD-20260928101004-176 is now Preparing.', 1, 1, '2026-09-28 08:11:30'),
(20, 'student', 1, 4, 'order_status', 'Your order ORD-20260928101101-524 is now Preparing.', 1, 1, '2026-09-28 08:11:38'),
(21, 'student', 1, 3, 'order_status', 'Your order ORD-20260928101004-176 is now Ready.', 1, 1, '2026-09-28 08:11:41'),
(22, 'student', 1, 4, 'order_status', 'Your order ORD-20260928101101-524 is now Ready.', 1, 1, '2026-09-28 08:11:43'),
(23, 'student', 1, 3, 'order_status', 'Your order ORD-20260928101004-176 is now Completed.', 1, 1, '2026-09-28 08:19:24'),
(24, 'student', 1, 4, 'order_status', 'Your order ORD-20260928101101-524 is now Completed.', 1, 1, '2026-09-28 10:23:25'),
(25, 'student', 1, 5, 'order_created', 'Your order ORD-20260928122822-465 has been received and is now Pending.', 1, 1, '2026-09-28 10:28:22'),
(26, 'admin', 1, 5, 'new_order', 'New order ORD-20260928122822-465 has been received from a student.', 0, 1, '2026-09-28 10:28:22'),
(27, 'student', 1, 6, 'order_created', 'Your order ORD-20260928123424-402 has been received and is now Pending.', 1, 1, '2026-09-28 10:34:24'),
(28, 'admin', 1, 6, 'new_order', 'New order ORD-20260928123424-402 has been received from a student.', 0, 1, '2026-09-28 10:34:24'),
(29, 'student', 1, 5, 'order_status', 'Your order ORD-20260928122822-465 is now Preparing.', 1, 1, '2026-09-28 10:35:33'),
(30, 'student', 1, 6, 'order_status', 'Your order ORD-20260928123424-402 is now Preparing.', 1, 1, '2026-09-28 10:35:36'),
(31, 'student', 1, 6, 'order_status', 'Your order ORD-20260928123424-402 is now Ready.', 1, 1, '2026-09-28 10:35:52'),
(32, 'student', 1, 5, 'order_status', 'Your order ORD-20260928122822-465 is now Ready.', 1, 1, '2026-09-28 10:35:57'),
(33, 'student', 1, 5, 'order_status', 'Your order ORD-20260928122822-465 is now Completed.', 1, 1, '2026-09-28 10:36:15'),
(34, 'student', 1, 6, 'order_status', 'Your order ORD-20260928123424-402 is now Completed.', 1, 1, '2026-09-28 10:36:21'),
(35, 'student', 1, 7, 'order_created', 'Your order ORD-20260928123757-808 has been received and is now Pending.', 1, 1, '2026-09-28 10:37:57'),
(36, 'admin', 1, 7, 'new_order', 'New order ORD-20260928123757-808 has been received from a student.', 0, 1, '2026-09-28 10:37:57'),
(37, 'student', 1, 8, 'order_created', 'Your order ORD-20260928124221-540 has been received and is now Pending.', 1, 1, '2026-09-28 10:42:21'),
(38, 'admin', 1, 8, 'new_order', 'New order ORD-20260928124221-540 has been received from a student.', 0, 1, '2026-09-28 10:42:21'),
(39, 'student', 1, 7, 'order_status', 'Your order ORD-20260928123757-808 is now Preparing.', 1, 1, '2026-09-28 10:43:10'),
(40, 'student', 1, 7, 'order_status', 'Your order ORD-20260928123757-808 is now Ready.', 1, 1, '2026-09-28 10:43:59'),
(41, 'student', 1, 8, 'order_status', 'Your order ORD-20260928124221-540 is now Preparing.', 1, 1, '2026-09-28 10:49:51'),
(42, 'student', 1, 8, 'order_status', 'Your order ORD-20260928124221-540 is now Ready.', 1, 1, '2026-09-28 10:50:00'),
(43, 'student', 1, 7, 'order_status', 'Your order ORD-20260928123757-808 is now Completed.', 1, 1, '2026-09-28 10:50:03'),
(44, 'student', 1, 8, 'order_status', 'Your order ORD-20260928124221-540 is now Completed.', 1, 1, '2026-09-28 11:09:15'),
(45, 'student', 1, 9, 'order_created', 'Your order ORD-20260928130932-108 has been received and is now Pending.', 1, 1, '2026-09-28 11:09:32'),
(46, 'admin', 1, 9, 'new_order', 'New order ORD-20260928130932-108 has been received from a student.', 0, 1, '2026-09-28 11:09:32'),
(47, 'student', 1, 10, 'order_created', 'Your order ORD-20260928130951-626 has been received and is now Pending.', 1, 1, '2026-09-28 11:09:51'),
(48, 'admin', 1, 10, 'new_order', 'New order ORD-20260928130951-626 has been received from a student.', 0, 1, '2026-09-28 11:09:51'),
(49, 'student', 1, 9, 'order_status', 'Your order ORD-20260928130932-108 is now Preparing.', 1, 1, '2026-09-28 11:10:09'),
(50, 'student', 1, 10, 'order_status', 'Your order ORD-20260928130951-626 is now Preparing.', 1, 1, '2026-09-28 11:10:20'),
(51, 'student', 1, 10, 'order_status', 'Your order ORD-20260928130951-626 is now Ready.', 1, 1, '2026-09-28 11:10:25'),
(52, 'student', 1, 9, 'order_status', 'Your order ORD-20260928130932-108 is now Ready.', 1, 1, '2026-09-28 11:10:26'),
(53, 'student', 1, 11, 'order_created', 'Your order ORD-20260928131103-324 has been received and is now Pending.', 1, 1, '2026-09-28 11:11:03'),
(54, 'admin', 1, 11, 'new_order', 'New order ORD-20260928131103-324 has been received from a student.', 0, 1, '2026-09-28 11:11:03'),
(55, 'student', 1, 12, 'order_created', 'Your order ORD-20260928131115-396 has been received and is now Pending.', 1, 1, '2026-09-28 11:11:15'),
(56, 'admin', 1, 12, 'new_order', 'New order ORD-20260928131115-396 has been received from a student.', 0, 1, '2026-09-28 11:11:15'),
(57, 'student', 1, 13, 'order_created', 'Your order ORD-20260928131124-178 has been received and is now Pending.', 1, 1, '2026-09-28 11:11:24'),
(58, 'admin', 1, 13, 'new_order', 'New order ORD-20260928131124-178 has been received from a student.', 0, 1, '2026-09-28 11:11:24'),
(59, 'student', 1, 11, 'order_status', 'Your order ORD-20260928131103-324 is now Preparing.', 1, 1, '2026-09-28 11:12:06'),
(60, 'student', 1, 9, 'order_status', 'Your order ORD-20260928130932-108 is now Completed.', 1, 1, '2026-09-28 11:16:27'),
(61, 'student', 1, 10, 'order_status', 'Your order ORD-20260928130951-626 is now Completed.', 1, 1, '2026-09-28 11:16:28'),
(62, 'student', 1, 11, 'order_status', 'Your order ORD-20260928131103-324 is now Ready.', 1, 1, '2026-09-28 11:16:30'),
(63, 'student', 1, 11, 'order_status', 'Your order ORD-20260928131103-324 is now Completed.', 1, 1, '2026-09-28 11:16:33'),
(64, 'student', 1, 12, 'order_status', 'Your order ORD-20260928131115-396 is now Preparing.', 1, 1, '2026-09-28 11:18:30'),
(65, 'student', 1, 13, 'order_status', 'Your order ORD-20260928131124-178 is now Preparing.', 1, 1, '2026-09-28 11:18:30'),
(66, 'student', 1, 13, 'order_status', 'Your order ORD-20260928131124-178 is now Ready.', 1, 1, '2026-09-28 11:18:37'),
(67, 'student', 1, 12, 'order_status', 'Your order ORD-20260928131115-396 is now Ready.', 1, 1, '2026-09-28 11:18:40'),
(68, 'student', 1, 14, 'order_created', 'Your order ORD-20260928131914-245 has been received and is now Pending.', 1, 1, '2026-09-28 11:19:14'),
(69, 'admin', 1, 14, 'new_order', 'New order ORD-20260928131914-245 has been received from a student.', 0, 1, '2026-09-28 11:19:14'),
(70, 'student', 1, 15, 'order_created', 'Your order ORD-20260928131925-147 has been received and is now Pending.', 1, 1, '2026-09-28 11:19:25'),
(71, 'admin', 1, 15, 'new_order', 'New order ORD-20260928131925-147 has been received from a student.', 0, 1, '2026-09-28 11:19:25'),
(72, 'student', 1, 12, 'order_status', 'Your order ORD-20260928131115-396 is now Completed.', 1, 1, '2026-09-28 11:19:35'),
(73, 'student', 1, 13, 'order_status', 'Your order ORD-20260928131124-178 is now Completed.', 1, 1, '2026-09-28 11:19:36'),
(74, 'student', 1, 14, 'order_status', 'Your order ORD-20260928131914-245 is now Preparing.', 1, 1, '2026-09-28 11:20:20'),
(75, 'student', 1, 15, 'order_status', 'Your order ORD-20260928131925-147 is now Preparing.', 1, 1, '2026-09-28 11:20:21'),
(76, 'student', 1, 14, 'order_status', 'Your order ORD-20260928131914-245 is now Ready.', 1, 1, '2026-09-28 11:21:55'),
(77, 'student', 1, 15, 'order_status', 'Your order ORD-20260928131925-147 is now Ready.', 1, 1, '2026-09-28 11:21:56'),
(78, 'student', 1, 14, 'order_status', 'Your order ORD-20260928131914-245 is now Completed.', 1, 1, '2026-09-28 11:22:06'),
(79, 'student', 1, 15, 'order_status', 'Your order ORD-20260928131925-147 is now Completed.', 1, 1, '2026-09-28 11:22:06'),
(80, 'student', 1, 16, 'order_created', 'Your order ORD-20260928134051-915 has been received and is now Pending.', 1, 1, '2026-09-28 11:40:51'),
(81, 'admin', 1, 16, 'new_order', 'New order ORD-20260928134051-915 has been received from a student.', 0, 1, '2026-09-28 11:40:51'),
(82, 'student', 1, 17, 'order_created', 'Your order ORD-20260928134059-165 has been received and is now Pending.', 1, 1, '2026-09-28 11:40:59'),
(83, 'admin', 1, 17, 'new_order', 'New order ORD-20260928134059-165 has been received from a student.', 0, 1, '2026-09-28 11:40:59'),
(84, 'student', 1, 18, 'order_created', 'Your order ORD-20260928134107-507 has been received and is now Pending.', 1, 1, '2026-09-28 11:41:07'),
(85, 'admin', 1, 18, 'new_order', 'New order ORD-20260928134107-507 has been received from a student.', 0, 1, '2026-09-28 11:41:07'),
(86, 'student', 1, 16, 'order_status', 'Your order ORD-20260928134051-915 is now Preparing.', 1, 1, '2026-09-28 11:41:34'),
(87, 'student', 1, 17, 'order_status', 'Your order ORD-20260928134059-165 is now Preparing.', 1, 1, '2026-09-28 11:41:48'),
(88, 'student', 1, 18, 'order_status', 'Your order ORD-20260928134107-507 is now Preparing.', 1, 1, '2026-09-28 11:41:49'),
(89, 'student', 1, 19, 'order_created', 'Your order ORD-20260928134723-355 has been received and is now Pending.', 1, 1, '2026-09-28 11:47:23'),
(90, 'admin', 1, 19, 'new_order', 'New order ORD-20260928134723-355 has been received from a student.', 0, 1, '2026-09-28 11:47:23'),
(91, 'student', 1, 20, 'order_created', 'Your order ORD-20260928134731-399 has been received and is now Pending.', 1, 1, '2026-09-28 11:47:31'),
(92, 'admin', 1, 20, 'new_order', 'New order ORD-20260928134731-399 has been received from a student.', 0, 1, '2026-09-28 11:47:31'),
(93, 'student', 1, 19, 'order_status', 'Your order ORD-20260928134723-355 is now Preparing.', 1, 1, '2026-09-28 11:48:58'),
(94, 'student', 1, 20, 'order_status', 'Your order ORD-20260928134731-399 is now Preparing.', 1, 1, '2026-09-28 11:49:05'),
(95, 'student', 1, 16, 'order_status', 'Your order ORD-20260928134051-915 is now Ready.', 1, 1, '2026-09-28 12:35:45'),
(96, 'student', 1, 17, 'order_status', 'Your order ORD-20260928134059-165 is now Ready.', 1, 1, '2026-09-28 12:35:46'),
(97, 'student', 1, 18, 'order_status', 'Your order ORD-20260928134107-507 is now Ready.', 1, 1, '2026-09-28 12:35:47'),
(98, 'student', 1, 19, 'order_status', 'Your order ORD-20260928134723-355 is now Ready.', 1, 1, '2026-09-28 12:35:48'),
(99, 'student', 1, 20, 'order_status', 'Your order ORD-20260928134731-399 is now Ready.', 1, 1, '2026-09-28 12:35:49'),
(100, 'student', 1, 16, 'order_status', 'Your order ORD-20260928134051-915 is now Completed.', 1, 1, '2026-09-28 12:35:50'),
(101, 'student', 1, 17, 'order_status', 'Your order ORD-20260928134059-165 is now Completed.', 1, 1, '2026-09-28 12:35:51'),
(102, 'student', 1, 18, 'order_status', 'Your order ORD-20260928134107-507 is now Completed.', 1, 1, '2026-09-28 12:35:51'),
(103, 'student', 1, 19, 'order_status', 'Your order ORD-20260928134723-355 is now Completed.', 1, 1, '2026-09-28 12:35:52'),
(104, 'student', 1, 20, 'order_status', 'Your order ORD-20260928134731-399 is now Completed.', 1, 1, '2026-09-28 12:35:52'),
(105, 'student', 1, 21, 'order_created', 'Your order ORD-20260928143734-871 has been received and is now Pending.', 0, 1, '2026-09-28 12:37:34'),
(106, 'admin', 1, 21, 'new_order', 'New order ORD-20260928143734-871 has been received from a student.', 0, 1, '2026-09-28 12:37:34'),
(107, 'student', 1, 22, 'order_created', 'Your order ORD-20260928143740-414 has been received and is now Pending.', 0, 1, '2026-09-28 12:37:40'),
(108, 'admin', 1, 22, 'new_order', 'New order ORD-20260928143740-414 has been received from a student.', 0, 1, '2026-09-28 12:37:40'),
(109, 'student', 1, 23, 'order_created', 'Your order ORD-20260928143749-122 has been received and is now Pending.', 0, 1, '2026-09-28 12:37:49'),
(110, 'admin', 1, 23, 'new_order', 'New order ORD-20260928143749-122 has been received from a student.', 0, 1, '2026-09-28 12:37:49'),
(111, 'student', 1, 21, 'order_status', 'Your order ORD-20260928143734-871 is now Preparing.', 0, 1, '2026-09-28 12:39:09'),
(112, 'student', 1, 21, 'order_status', 'Your order ORD-20260928143734-871 is now Ready.', 0, 1, '2026-09-28 12:39:34'),
(113, 'student', 1, 22, 'order_cancelled', 'Your order ORD-20260928143740-414 has been Cancelled.', 0, 1, '2026-09-28 12:45:57'),
(114, 'student', 1, 23, 'order_cancelled', 'Your order ORD-20260928143749-122 has been Cancelled.', 0, 1, '2026-09-28 12:45:59'),
(115, 'student', 1, 21, 'order_status', 'Your order ORD-20260928143734-871 is now Completed.', 0, 1, '2026-09-28 12:46:18'),
(116, 'student', 1, 24, 'order_created', 'Your order ORD-20260928144659-417 has been received and is now Pending.', 0, 1, '2026-09-28 12:46:59'),
(117, 'admin', 1, 24, 'new_order', 'New order ORD-20260928144659-417 has been received from a student.', 0, 1, '2026-09-28 12:46:59'),
(118, 'student', 1, 24, 'order_status', 'Your order ORD-20260928144659-417 is now Preparing.', 0, 1, '2026-09-28 12:47:12'),
(119, 'student', 1, 24, 'order_cancelled', 'Your order ORD-20260928144659-417 has been Cancelled.', 0, 1, '2026-09-28 13:14:26'),
(120, 'student', 1, 25, 'order_created', 'Your order ORD-20260928151452-813 has been received and is now Pending.', 0, 1, '2026-09-28 13:14:52'),
(121, 'admin', 1, 25, 'new_order', 'New order ORD-20260928151452-813 has been received from a student.', 0, 1, '2026-09-28 13:14:52'),
(122, 'student', 1, 25, 'order_status', 'Your order ORD-20260928151452-813 is now Preparing.', 0, 1, '2026-09-28 13:15:04'),
(123, 'student', 1, 25, 'order_status', 'Your order ORD-20260928151452-813 is now Ready.', 0, 1, '2026-09-28 13:15:07'),
(124, 'student', 1, 25, 'order_status', 'Your order ORD-20260928151452-813 is now Completed.', 0, 1, '2026-09-28 13:15:10'),
(125, 'student', 1, 26, 'order_created', 'Your order ORD-20260928151658-484 has been received and is now Pending.', 0, 1, '2026-09-28 13:16:58'),
(126, 'admin', 1, 26, 'new_order', 'New order ORD-20260928151658-484 has been received from a student.', 0, 1, '2026-09-28 13:16:58'),
(127, 'student', 1, 26, 'order_status', 'Your order ORD-20260928151658-484 is now Preparing.', 0, 1, '2026-09-28 13:17:04'),
(128, 'student', 1, 26, 'order_status', 'Your order ORD-20260928151658-484 is now Ready.', 0, 1, '2026-09-28 13:17:09'),
(129, 'student', 1, 26, 'order_status', 'Your order ORD-20260928151658-484 is now Completed.', 0, 1, '2026-09-28 13:17:12'),
(130, 'student', 1, 27, 'order_created', 'Your order ORD-20260928154332-397 has been received and is now Pending.', 1, 1, '2026-09-28 13:43:32'),
(131, 'admin', 1, 27, 'new_order', 'New order ORD-20260928154332-397 has been received from a student.', 0, 1, '2026-09-28 13:43:32'),
(132, 'student', 1, 27, 'order_status', 'Your order ORD-20260928154332-397 is now Preparing.', 1, 1, '2026-09-28 13:43:47'),
(133, 'student', 1, 27, 'order_status', 'Your order ORD-20260928154332-397 is now Ready.', 1, 1, '2026-09-28 13:43:55'),
(134, 'student', 1, 27, 'order_status', 'Your order ORD-20260928154332-397 is now Completed.', 1, 1, '2026-09-28 13:44:02'),
(135, 'student', 1, 28, 'order_created', 'Your order ORD-20260928154509-877 has been received and is now Pending.', 1, 1, '2026-09-28 13:45:09'),
(136, 'admin', 1, 28, 'new_order', 'New order ORD-20260928154509-877 has been received from a student.', 0, 1, '2026-09-28 13:45:09'),
(137, 'student', 1, 29, 'order_created', 'Your order ORD-20260928154522-104 has been received and is now Pending.', 1, 1, '2026-09-28 13:45:22'),
(138, 'admin', 1, 29, 'new_order', 'New order ORD-20260928154522-104 has been received from a student.', 0, 1, '2026-09-28 13:45:22'),
(139, 'student', 1, 30, 'order_created', 'Your order ORD-20260928154540-549 has been received and is now Pending.', 1, 1, '2026-09-28 13:45:40'),
(140, 'admin', 1, 30, 'new_order', 'New order ORD-20260928154540-549 has been received from a student.', 0, 1, '2026-09-28 13:45:40'),
(141, 'student', 1, 28, 'order_status', 'Your order ORD-20260928154509-877 is now Preparing.', 1, 1, '2026-09-28 13:46:44'),
(142, 'student', 1, 29, 'order_status', 'Your order ORD-20260928154522-104 is now Preparing.', 1, 1, '2026-09-28 13:47:00'),
(143, 'student', 1, 30, 'order_status', 'Your order ORD-20260928154540-549 is now Preparing.', 1, 1, '2026-09-28 13:47:05'),
(144, 'student', 1, 28, 'order_status', 'Your order ORD-20260928154509-877 is now Ready.', 1, 1, '2026-09-28 13:47:19'),
(145, 'student', 1, 29, 'order_status', 'Your order ORD-20260928154522-104 is now Ready.', 1, 1, '2026-09-28 13:47:23'),
(146, 'student', 1, 30, 'order_status', 'Your order ORD-20260928154540-549 is now Ready.', 1, 1, '2026-09-28 13:47:25'),
(147, 'student', 1, 28, 'order_status', 'Your order ORD-20260928154509-877 is now Completed.', 1, 1, '2026-09-28 13:47:32'),
(148, 'student', 1, 29, 'order_status', 'Your order ORD-20260928154522-104 is now Completed.', 1, 1, '2026-09-28 13:47:36'),
(149, 'student', 1, 30, 'order_status', 'Your order ORD-20260928154540-549 is now Completed.', 1, 1, '2026-09-28 13:47:37'),
(150, 'student', 1, 31, 'order_created', 'Your order ORD-20260928155139-113 has been received and is now Pending.', 0, 1, '2026-09-28 13:51:39'),
(151, 'admin', 1, 31, 'new_order', 'New order ORD-20260928155139-113 has been received from a student.', 0, 1, '2026-09-28 13:51:39'),
(152, 'student', 1, 32, 'order_created', 'Your order ORD-20260928155146-620 has been received and is now Pending.', 0, 1, '2026-09-28 13:51:46'),
(153, 'admin', 1, 32, 'new_order', 'New order ORD-20260928155146-620 has been received from a student.', 0, 1, '2026-09-28 13:51:46'),
(154, 'student', 1, 33, 'order_created', 'Your order ORD-20260928155204-589 has been received and is now Pending.', 0, 1, '2026-09-28 13:52:04'),
(155, 'admin', 1, 33, 'new_order', 'New order ORD-20260928155204-589 has been received from a student.', 0, 1, '2026-09-28 13:52:04'),
(156, 'student', 1, 31, 'order_status', 'Your order ORD-20260928155139-113 is now Preparing.', 0, 1, '2026-09-28 13:52:28'),
(157, 'student', 1, 32, 'order_status', 'Your order ORD-20260928155146-620 is now Preparing.', 0, 1, '2026-09-28 13:52:34'),
(158, 'student', 1, 33, 'order_status', 'Your order ORD-20260928155204-589 is now Preparing.', 0, 1, '2026-09-28 13:52:37'),
(159, 'student', 1, 31, 'order_status', 'Your order ORD-20260928155139-113 is now Ready.', 0, 1, '2026-09-28 13:52:42'),
(160, 'student', 1, 32, 'order_status', 'Your order ORD-20260928155146-620 is now Ready.', 0, 1, '2026-09-28 13:52:46'),
(161, 'student', 1, 33, 'order_status', 'Your order ORD-20260928155204-589 is now Ready.', 0, 1, '2026-09-28 13:52:49'),
(162, 'admin', 1, NULL, 'new_message', 'You received a new message from a student.', 0, 1, '2026-09-28 14:28:21'),
(163, 'student', 1, 31, 'order_status', 'Your order ORD-20260928155139-113 is now Completed.', 0, 1, '2026-09-28 14:30:04'),
(164, 'student', 1, 32, 'order_status', 'Your order ORD-20260928155146-620 is now Completed.', 0, 1, '2026-09-28 14:30:11'),
(165, 'student', 1, 33, 'order_status', 'Your order ORD-20260928155204-589 is now Completed.', 0, 1, '2026-09-28 14:30:12'),
(166, 'student', 1, 34, 'order_created', 'Your order ORD-20260928173259-508 has been received and is now Pending.', 1, 1, '2026-09-28 15:32:59'),
(167, 'admin', 1, 34, 'new_order', 'New order ORD-20260928173259-508 has been received from a student.', 0, 0, '2026-09-28 15:32:59'),
(168, 'student', 1, NULL, 'new_message', 'You received a new message from the administrator.', 1, 1, '2026-09-28 15:36:55'),
(169, 'student', 1, 34, 'order_status', 'Your order ORD-20260928173259-508 is now Preparing.', 0, 1, '2026-09-28 15:42:03'),
(170, 'student', 1, 34, 'order_status', 'Your order ORD-20260928173259-508 is now Ready.', 0, 1, '2026-09-28 15:42:06'),
(171, 'student', 1, 34, 'order_status', 'Your order ORD-20260928173259-508 is now Completed.', 0, 1, '2026-09-28 15:42:07'),
(172, 'student', 1, NULL, 'new_message', 'You received a new message from the administrator.', 0, 1, '2026-09-28 15:43:17'),
(173, 'student', 1, 35, 'order_created', 'Your order ORD-20260928185637-523 has been received and is now Pending.', 0, 0, '2026-09-28 16:56:37'),
(174, 'admin', 1, 35, 'new_order', 'New order ORD-20260928185637-523 has been received from a student.', 0, 0, '2026-09-28 16:56:37'),
(175, 'student', 2, 36, 'order_created', 'Your order ORD-20260928185854-300 has been received and is now Pending.', 0, 1, '2026-09-28 16:58:54'),
(176, 'admin', 1, 36, 'new_order', 'New order ORD-20260928185854-300 has been received from a student.', 0, 0, '2026-09-28 16:58:54'),
(177, 'student', 1, 35, 'order_status', 'Your order ORD-20260928185637-523 is now Preparing.', 0, 0, '2026-09-28 16:59:14'),
(178, 'student', 2, 36, 'order_status', 'Your order ORD-20260928185854-300 is now Preparing.', 0, 1, '2026-09-28 16:59:15'),
(179, 'student', 1, 35, 'order_status', 'Your order ORD-20260928185637-523 is now Ready.', 0, 0, '2026-09-28 16:59:16'),
(180, 'student', 2, 36, 'order_status', 'Your order ORD-20260928185854-300 is now Ready.', 0, 1, '2026-09-28 16:59:16'),
(181, 'student', 1, 35, 'order_status', 'Your order ORD-20260928185637-523 is now Completed.', 0, 0, '2026-09-28 16:59:18'),
(182, 'student', 2, 36, 'order_status', 'Your order ORD-20260928185854-300 is now Completed.', 0, 1, '2026-09-28 16:59:18'),
(183, 'student', 2, 37, 'order_created', 'Your order ORD-20260928193534-679 has been received and is now Pending.', 0, 0, '2026-09-28 17:35:34'),
(184, 'admin', 1, 37, 'new_order', 'New order ORD-20260928193534-679 has been received from a student.', 0, 0, '2026-09-28 17:35:34');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(30) NOT NULL,
  `student_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('Pending','Preparing','Ready','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `queue_position` int(11) DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `student_id`, `total_amount`, `status`, `queue_position`, `is_archived`, `created_at`, `updated_at`) VALUES
(1, 'ORD-20260927171123-381', 1, 60.00, 'Completed', NULL, 1, '2026-09-27 15:11:23', '2026-09-28 11:16:16'),
(2, 'ORD-20260927171536-809', 1, 90.00, 'Completed', NULL, 1, '2026-09-27 15:15:36', '2026-09-28 11:16:14'),
(3, 'ORD-20260928101004-176', 1, 310.00, 'Completed', NULL, 1, '2026-09-28 08:10:04', '2026-09-28 11:16:12'),
(4, 'ORD-20260928101101-524', 1, 70.00, 'Completed', NULL, 1, '2026-09-28 08:11:01', '2026-09-28 11:16:10'),
(5, 'ORD-20260928122822-465', 1, 360.00, 'Completed', NULL, 1, '2026-09-28 10:28:22', '2026-09-28 11:16:09'),
(6, 'ORD-20260928123424-402', 1, 110.00, 'Completed', NULL, 1, '2026-09-28 10:34:24', '2026-09-28 11:16:07'),
(7, 'ORD-20260928123757-808', 1, 60.00, 'Completed', NULL, 1, '2026-09-28 10:37:57', '2026-09-28 11:16:05'),
(8, 'ORD-20260928124221-540', 1, 45.00, 'Completed', NULL, 1, '2026-09-28 10:42:21', '2026-09-28 11:16:03'),
(9, 'ORD-20260928130932-108', 1, 105.00, 'Completed', NULL, 1, '2026-09-28 11:09:32', '2026-09-28 11:16:37'),
(10, 'ORD-20260928130951-626', 1, 65.00, 'Completed', NULL, 1, '2026-09-28 11:09:51', '2026-09-28 11:16:38'),
(11, 'ORD-20260928131103-324', 1, 105.00, 'Completed', NULL, 1, '2026-09-28 11:11:03', '2026-09-28 11:16:40'),
(12, 'ORD-20260928131115-396', 1, 50.00, 'Completed', NULL, 1, '2026-09-28 11:11:15', '2026-09-28 11:19:38'),
(13, 'ORD-20260928131124-178', 1, 25.00, 'Completed', NULL, 1, '2026-09-28 11:11:24', '2026-09-28 11:19:40'),
(14, 'ORD-20260928131914-245', 1, 105.00, 'Completed', NULL, 1, '2026-09-28 11:19:14', '2026-09-28 11:30:47'),
(15, 'ORD-20260928131925-147', 1, 25.00, 'Completed', NULL, 1, '2026-09-28 11:19:25', '2026-09-28 11:30:51'),
(16, 'ORD-20260928134051-915', 1, 60.00, 'Completed', NULL, 1, '2026-09-28 11:40:51', '2026-09-28 12:46:01'),
(17, 'ORD-20260928134059-165', 1, 45.00, 'Completed', NULL, 1, '2026-09-28 11:40:59', '2026-09-28 12:46:02'),
(18, 'ORD-20260928134107-507', 1, 25.00, 'Completed', NULL, 1, '2026-09-28 11:41:07', '2026-09-28 12:46:04'),
(19, 'ORD-20260928134723-355', 1, 105.00, 'Completed', NULL, 1, '2026-09-28 11:47:23', '2026-09-28 12:46:06'),
(20, 'ORD-20260928134731-399', 1, 25.00, 'Completed', NULL, 1, '2026-09-28 11:47:31', '2026-09-28 12:46:08'),
(21, 'ORD-20260928143734-871', 1, 60.00, 'Completed', NULL, 1, '2026-09-28 12:37:34', '2026-09-28 12:46:24'),
(22, 'ORD-20260928143740-414', 1, 45.00, 'Cancelled', NULL, 1, '2026-09-28 12:37:40', '2026-09-28 13:42:48'),
(23, 'ORD-20260928143749-122', 1, 25.00, 'Cancelled', NULL, 1, '2026-09-28 12:37:49', '2026-09-28 13:42:50'),
(24, 'ORD-20260928144659-417', 1, 60.00, 'Cancelled', NULL, 1, '2026-09-28 12:46:59', '2026-09-28 13:42:52'),
(25, 'ORD-20260928151452-813', 1, 130.00, 'Completed', NULL, 1, '2026-09-28 13:14:52', '2026-09-28 13:15:24'),
(26, 'ORD-20260928151658-484', 1, 60.00, 'Completed', NULL, 1, '2026-09-28 13:16:58', '2026-09-28 13:17:17'),
(27, 'ORD-20260928154332-397', 1, 60.00, 'Completed', NULL, 1, '2026-09-28 13:43:32', '2026-09-28 13:44:08'),
(28, 'ORD-20260928154509-877', 1, 20.00, 'Completed', NULL, 1, '2026-09-28 13:45:09', '2026-09-28 13:47:50'),
(29, 'ORD-20260928154522-104', 1, 25.00, 'Completed', NULL, 1, '2026-09-28 13:45:22', '2026-09-28 13:47:52'),
(30, 'ORD-20260928154540-549', 1, 25.00, 'Completed', NULL, 1, '2026-09-28 13:45:40', '2026-09-28 13:47:54'),
(31, 'ORD-20260928155139-113', 1, 60.00, 'Completed', NULL, 1, '2026-09-28 13:51:39', '2026-09-28 14:30:17'),
(32, 'ORD-20260928155146-620', 1, 45.00, 'Completed', NULL, 1, '2026-09-28 13:51:46', '2026-09-28 14:30:19'),
(33, 'ORD-20260928155204-589', 1, 25.00, 'Completed', NULL, 1, '2026-09-28 13:52:04', '2026-09-28 14:30:22'),
(34, 'ORD-20260928173259-508', 1, 120.00, 'Completed', NULL, 1, '2026-09-28 15:32:59', '2026-09-28 15:42:25'),
(35, 'ORD-20260928185637-523', 1, 215.00, 'Completed', NULL, 1, '2026-09-28 16:56:37', '2026-09-28 16:59:20'),
(36, 'ORD-20260928185854-300', 2, 105.00, 'Completed', NULL, 1, '2026-09-28 16:58:54', '2026-09-28 16:59:22'),
(37, 'ORD-20260928193534-679', 2, 60.00, 'Pending', 1, 0, '2026-09-28 17:35:34', '2026-09-28 17:35:34');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 5, 1, 60.00, 60.00),
(2, 2, 8, 2, 45.00, 90.00),
(3, 3, 5, 1, 60.00, 60.00),
(4, 3, 8, 1, 45.00, 45.00),
(5, 3, 7, 2, 25.00, 50.00),
(6, 3, 9, 1, 25.00, 25.00),
(7, 3, 1, 1, 50.00, 50.00),
(8, 3, 3, 1, 80.00, 80.00),
(9, 4, 8, 1, 45.00, 45.00),
(10, 4, 7, 1, 25.00, 25.00),
(11, 5, 5, 1, 60.00, 60.00),
(12, 5, 8, 1, 45.00, 45.00),
(13, 5, 7, 1, 25.00, 25.00),
(14, 5, 9, 1, 25.00, 25.00),
(15, 5, 1, 1, 50.00, 50.00),
(16, 5, 3, 1, 80.00, 80.00),
(17, 5, 6, 1, 75.00, 75.00),
(18, 6, 4, 1, 20.00, 20.00),
(19, 6, 2, 1, 30.00, 30.00),
(20, 6, 5, 1, 60.00, 60.00),
(21, 7, 5, 1, 60.00, 60.00),
(22, 8, 8, 1, 45.00, 45.00),
(23, 9, 5, 1, 60.00, 60.00),
(24, 9, 8, 1, 45.00, 45.00),
(25, 10, 8, 1, 45.00, 45.00),
(26, 10, 4, 1, 20.00, 20.00),
(27, 11, 6, 1, 75.00, 75.00),
(28, 11, 2, 1, 30.00, 30.00),
(29, 12, 1, 1, 50.00, 50.00),
(30, 13, 7, 1, 25.00, 25.00),
(31, 14, 5, 1, 60.00, 60.00),
(32, 14, 8, 1, 45.00, 45.00),
(33, 15, 7, 1, 25.00, 25.00),
(34, 16, 5, 1, 60.00, 60.00),
(35, 17, 8, 1, 45.00, 45.00),
(36, 18, 7, 1, 25.00, 25.00),
(37, 19, 5, 1, 60.00, 60.00),
(38, 19, 8, 1, 45.00, 45.00),
(39, 20, 7, 1, 25.00, 25.00),
(40, 21, 5, 1, 60.00, 60.00),
(41, 22, 8, 1, 45.00, 45.00),
(42, 23, 7, 1, 25.00, 25.00),
(43, 24, 5, 1, 60.00, 60.00),
(44, 25, 5, 1, 60.00, 60.00),
(45, 25, 1, 1, 50.00, 50.00),
(46, 25, 4, 1, 20.00, 20.00),
(47, 26, 5, 1, 60.00, 60.00),
(48, 27, 5, 1, 60.00, 60.00),
(49, 28, 4, 1, 20.00, 20.00),
(50, 29, 7, 1, 25.00, 25.00),
(51, 30, 9, 1, 25.00, 25.00),
(52, 31, 5, 1, 60.00, 60.00),
(53, 32, 8, 1, 45.00, 45.00),
(54, 33, 7, 1, 25.00, 25.00),
(55, 34, 5, 1, 60.00, 60.00),
(56, 34, 5, 1, 60.00, 60.00),
(57, 35, 5, 1, 60.00, 60.00),
(58, 35, 8, 1, 45.00, 45.00),
(59, 35, 7, 1, 25.00, 25.00),
(60, 35, 9, 1, 25.00, 25.00),
(61, 35, 5, 1, 60.00, 60.00),
(62, 36, 5, 1, 60.00, 60.00),
(63, 36, 8, 1, 45.00, 45.00),
(64, 37, 5, 1, 60.00, 60.00);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `name`, `email`, `password`, `profile_image`, `created_at`) VALUES
(1, '1921', 'Jai-M Vergara', 'jaivergara282@gmail.com', '$2y$10$08psLJ.j6WQ/.aiFAS38tuvWrgKWYWzYFX8Sy2U2xGdGhP0KKm5w2', NULL, '2026-09-27 10:55:32'),
(2, '01-2526-004882', 'Daniel Padilla', 'daniel123@gmail.com', '$2y$10$WWk/gYAZaFkPHbXr5WrhuOrVd3DzEac7vVMRvBjiElxJwiCXRWylS', NULL, '2026-09-28 16:58:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
