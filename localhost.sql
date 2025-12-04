-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th10 25, 2025 lúc 03:10 PM
-- Phiên bản máy phục vụ: 8.0.44
-- Phiên bản PHP: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `hthree_film`
--
CREATE DATABASE IF NOT EXISTS `hthree_film` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hthree_film`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `plan_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `plan_id`, `quantity`, `created_at`, `updated_at`) VALUES
(20, 4, 2, 2, '2025-11-25 08:09:40', '2025-11-25 08:09:56'),
(33, 5, 3, 1, '2025-11-25 12:56:16', '2025-11-25 12:56:16'),
(34, 99, 3, 1, '2025-11-25 13:03:09', '2025-11-25 13:03:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `movie_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `likes` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `movie_slug`, `parent_id`, `content`, `likes`, `created_at`, `updated_at`) VALUES
(1, 1, 'avatar-2-dong-chay-cua-nuoc', NULL, 'Phim hay quá! Đáng xem!', 234, '2025-11-22 06:41:27', '2025-11-22 06:41:27'),
(2, 2, 'robin-hood', NULL, 'Diễn xuất tuyệt vời!', 189, '2025-11-22 06:41:27', '2025-11-22 06:41:27'),
(3, 1, 'son-than-di-van-luc', NULL, 'Phim Trung Quốc hay nhất năm', 156, '2025-11-22 06:41:27', '2025-11-22 06:41:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` int NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `discount_type` enum('percent','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_value` decimal(10,2) DEFAULT '0.00',
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `usage_count` int DEFAULT '0',
  `user_limit` int DEFAULT '1',
  `start_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `description`, `discount_type`, `discount_value`, `min_order_value`, `max_discount`, `usage_limit`, `usage_count`, `user_limit`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME2025', 'Giảm 20% cho đơn hàng đầu tiên', 'percent', 20.00, 50000.00, 50000.00, 100, 0, 1, '2025-11-25 03:33:06', '2025-12-25 03:33:06', 1, '2025-11-25 03:33:06', '2025-11-25 03:33:06'),
(2, 'SUMMER50K', 'Giảm 50k cho đơn từ 100k', 'fixed', 50000.00, 100000.00, NULL, 50, 0, 1, '2025-11-25 03:33:06', '2026-01-24 03:33:06', 1, '2025-11-25 03:33:06', '2025-11-25 03:33:06'),
(3, 'VIP30', 'Giảm 30% cho gói VIP', 'percent', 30.00, 150000.00, 100000.00, 20, 0, 1, '2025-11-25 03:33:06', '2026-02-23 03:33:06', 1, '2025-11-25 03:33:06', '2025-11-25 03:33:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupon_usage`
--

CREATE TABLE `coupon_usage` (
  `id` int NOT NULL,
  `coupon_id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_id` int NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `used_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `movie_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `movie_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `movie_poster` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `movie_year` int DEFAULT NULL,
  `movie_quality` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `movie_slug`, `movie_name`, `movie_poster`, `movie_year`, `movie_quality`, `added_at`) VALUES
(1, 1, 'avatar-2-dong-chay-cua-nuoc', 'Avatar 2: Dòng Chảy Của Nước', 'https://phimimg.com/...', 2022, 'HD', '2025-11-22 06:41:40'),
(2, 1, 'robin-hood', 'Robin Hood', 'https://phimimg.com/...', 2018, 'FHD', '2025-11-22 06:41:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `order_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `customer_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `payment_method` enum('vnpay','momo','zalopay','bank_transfer','cod') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `status` enum('pending','processing','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `admin_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `paid_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `subtotal`, `discount`, `total`, `payment_method`, `payment_status`, `status`, `note`, `admin_note`, `paid_at`, `completed_at`, `cancelled_at`, `created_at`, `updated_at`) VALUES
(1, 'ORD202511255917', 6, 'Admin', 'hient7182@gmail.com', '', 100000.00, 0.00, 100000.00, 'vnpay', 'paid', 'completed', '', NULL, '2025-11-25 14:46:24', '2025-11-25 14:46:24', NULL, '2025-11-25 12:32:24', '2025-11-25 14:46:24'),
(2, 'ORD202511253238', 6, 'Admin', 'hient7182@gmail.com', '', 100000.00, 0.00, 100000.00, 'bank_transfer', 'paid', 'completed', '', NULL, '2025-11-25 14:27:09', '2025-11-25 14:27:09', NULL, '2025-11-25 12:33:35', '2025-11-25 14:27:09'),
(7, 'ORD20251125260', 1, 'adasfdas', 'admin@hthree.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:26:53', '2025-11-25 14:26:53', NULL, '2025-11-25 13:49:55', '2025-11-25 14:26:53'),
(8, 'ORD202511255262', 1, 'admin', 'admin@hthree.com', '0825591211', 400000.00, 0.00, 400000.00, 'bank_transfer', 'paid', 'completed', '', NULL, '2025-11-25 14:24:04', '2025-11-25 14:24:04', NULL, '2025-11-25 13:52:04', '2025-11-25 14:24:04'),
(9, 'ORD20251125383', 1, 'trungtuan', 'admin@hthree.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:15:04', '2025-11-25 14:15:04', NULL, '2025-11-25 14:08:31', '2025-11-25 14:15:04'),
(10, 'ORD20251125918', 1, 'sdaf', 'admin@hthree.com', '0825591211', 200000.00, 0.00, 200000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:23:40', '2025-11-25 14:23:40', NULL, '2025-11-25 14:16:21', '2025-11-25 14:23:40'),
(11, 'ORD20251125972', 1, 'trungtuan', 'admin@hthree.com', '0825591211', 200000.00, 0.00, 200000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:22:54', '2025-11-25 14:22:54', NULL, '2025-11-25 14:19:04', '2025-11-25 14:22:54'),
(12, 'ORD20251125644', 103, 'ầdasdasd', 'vngs009@gmail.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-11-25 14:51:49', '2025-11-25 14:51:49'),
(13, 'ORD20251125029', 103, 'ầdasdasd', 'vngs009@gmail.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:56:19', '2025-11-25 14:56:19', NULL, '2025-11-25 14:56:19', '2025-11-25 14:56:19'),
(14, 'ORD20251125728', 103, 'ầdasdasd', 'vngs009@gmail.com', '0825591211', 100000.00, 0.00, 100000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:57:52', '2025-11-25 14:57:52', NULL, '2025-11-25 14:57:52', '2025-11-25 14:57:52'),
(15, 'ORD20251125730', 103, 'ầdasdasd', 'vngs009@gmail.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:58:07', '2025-11-25 14:58:07', NULL, '2025-11-25 14:58:07', '2025-11-25 14:58:07'),
(16, 'ORD20251125401', 103, 'ầdasdasd', 'vngs009@gmail.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 15:00:10', '2025-11-25 15:00:10', NULL, '2025-11-25 15:00:10', '2025-11-25 15:00:10'),
(17, 'ORD20251125535', 103, 'ầdasdasd', 'vngs009@gmail.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 15:02:49', '2025-11-25 15:02:49', NULL, '2025-11-25 15:02:49', '2025-11-25 15:02:49');

--
-- Bẫy `orders`
--
DELIMITER $$
CREATE TRIGGER `tr_activate_subscription_after_payment` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
    IF NEW.payment_status = 'paid' AND OLD.payment_status != 'paid' THEN
        INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status, order_id)
        SELECT 
            NEW.user_id,
            oi.plan_id,
            NOW(),
            DATE_ADD(NOW(), INTERVAL (sp.duration_days * oi.duration_months) DAY),
            'active',
            NEW.id
        FROM order_items oi
        JOIN subscription_plans sp ON oi.plan_id = sp.id
        WHERE oi.order_id = NEW.id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `plan_id` int NOT NULL,
  `plan_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan_price` decimal(10,2) NOT NULL,
  `duration_months` int DEFAULT '1',
  `quantity` int DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `plan_id`, `plan_name`, `plan_price`, `duration_months`, `quantity`, `price`, `total`, `created_at`) VALUES
(1, 1, 3, 'Premium', 100000.00, 1, 1, 100000.00, 100000.00, '2025-11-25 12:32:24'),
(2, 2, 3, 'Premium', 100000.00, 1, 1, 100000.00, 100000.00, '2025-11-25 12:33:35'),
(3, 7, 2, 'Basic', 50000.00, 1, 1, 50000.00, 50000.00, '2025-11-25 13:49:55'),
(4, 8, 2, 'Basic', 50000.00, 2, 1, 50000.00, 100000.00, '2025-11-25 13:52:04'),
(5, 8, 3, 'Premium', 100000.00, 1, 1, 100000.00, 100000.00, '2025-11-25 13:52:04'),
(6, 8, 4, 'VIP', 200000.00, 1, 1, 200000.00, 200000.00, '2025-11-25 13:52:04'),
(7, 9, 2, 'Basic', 50000.00, 1, 1, 50000.00, 50000.00, '2025-11-25 14:08:31'),
(8, 10, 4, 'VIP', 200000.00, 1, 1, 200000.00, 200000.00, '2025-11-25 14:16:21'),
(9, 11, 4, 'VIP', 200000.00, 1, 1, 200000.00, 200000.00, '2025-11-25 14:19:04'),
(10, 12, 2, 'Basic', 50000.00, 1, 1, 50000.00, 50000.00, '2025-11-25 14:51:49'),
(11, 13, 2, 'Basic', 50000.00, 1, 1, 50000.00, 50000.00, '2025-11-25 14:56:19'),
(12, 14, 3, 'Premium', 100000.00, 1, 1, 100000.00, 100000.00, '2025-11-25 14:57:52'),
(13, 15, 2, 'Basic', 50000.00, 1, 1, 50000.00, 50000.00, '2025-11-25 14:58:07'),
(14, 16, 2, 'Basic', 50000.00, 1, 1, 50000.00, 50000.00, '2025-11-25 15:00:10'),
(15, 17, 2, 'Basic', 50000.00, 1, 1, 50000.00, 50000.00, '2025-11-25 15:02:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ratings`
--

CREATE TABLE `ratings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `movie_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint NOT NULL,
  `review` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `duration_days` int NOT NULL DEFAULT '30',
  `quality` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'SD',
  `max_devices` int DEFAULT '1',
  `has_ads` tinyint(1) DEFAULT '1',
  `can_download` tinyint(1) DEFAULT '0',
  `early_access` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `subscription_plans`
--

INSERT INTO `subscription_plans` (`id`, `name`, `slug`, `description`, `price`, `duration_days`, `quality`, `max_devices`, `has_ads`, `can_download`, `early_access`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Free', 'free', 'Xem phim miễn phí với quảng cáo, chất lượng SD', 0.00, 30, 'SD', 1, 1, 0, 0, 1, 1, '2025-11-25 03:33:06', '2025-11-25 03:33:06'),
(2, 'Basic', 'basic', 'Xem phim HD không quảng cáo trên 1 thiết bị', 50000.00, 30, 'HD', 1, 0, 0, 0, 1, 2, '2025-11-25 03:33:06', '2025-11-25 03:33:06'),
(3, 'Premium', 'premium', 'Xem phim Full HD trên 2 thiết bị, tải về được', 100000.00, 30, 'FHD', 2, 0, 1, 0, 1, 3, '2025-11-25 03:33:06', '2025-11-25 03:33:06'),
(4, 'VIP', 'vip', 'Xem phim 4K trên 4 thiết bị, xem trước phim mới', 200000.00, 30, '4K', 4, 0, 1, 1, 1, 4, '2025-11-25 03:33:06', '2025-11-25 03:33:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `transaction_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','success','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `gateway_response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `transaction_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `firebase_uid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `firebase_uid`, `password`, `full_name`, `avatar`, `created_at`, `updated_at`, `last_login`, `is_active`, `role`) VALUES
(1, 'admin', 'admin@hthree.com', 'dEd8RaIyoCcNmynpV5mXOTiGUd22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', NULL, '2025-11-22 06:39:59', '2025-11-25 14:45:57', '2025-11-25 14:45:57', 1, 'admin'),
(2, 'user1', 'user1@hthree.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Demo', NULL, '2025-11-22 06:39:59', '2025-11-22 06:39:59', NULL, 1, 'user'),
(3, 'khatu', 'tranlackhatucute@gmail.com', NULL, '$2y$10$498nga1O5Rv5gT4UM5js7eJ0s/9ahKx4mUwWOAscbwcRQoVJJzb8e', 'Trần Lạc Khả Tú', NULL, '2025-11-22 06:44:08', '2025-11-22 06:44:15', '2025-11-22 06:44:15', 1, 'user'),
(4, 'user_nXFrwoZy', 'user_nXFrwoZy@firebase.temp', 'nXFrwoZyZyTBxz7sDri1NhG1mJ62', '$2y$10$ceCy5ytgsSOacTUWW6FVy.VYCjkvNUvMXiL10Ouau7fD7mljZuYs.', NULL, NULL, '2025-11-25 06:34:22', '2025-11-25 06:34:22', NULL, 1, 'user'),
(5, 'user_pMHzrqR7', 'user_pMHzrqR7@firebase.temp', 'pMHzrqR71FN0IyJv8fxFHC19d482', '$2y$10$I6.3fJJFAsbaC08qj5jOReWa7YuyJeCkk1v22Au370T/LTATMhyR.', 'Phạm Trung Tuấn', 'https://lh3.googleusercontent.com/a/ACg8ocKT35jtuQm0v9FNmkZlFyqYDOLFuGXLhzZBybMo2tIHXZS_e3YKig=s96-c', '2025-11-25 10:54:55', '2025-11-25 12:35:48', '2025-11-25 12:35:48', 1, 'user'),
(6, 'hient7182', 'hient7182@gmail.com', 'firebase_uid_placeholder', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', NULL, '2025-11-25 12:04:02', '2025-11-25 12:04:02', NULL, 1, 'admin'),
(99, 'admin_test', 'admin@test.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Test', NULL, '2025-11-25 13:03:09', '2025-11-25 13:03:09', NULL, 1, 'admin'),
(100, 'Phạm Tuấn', 'tuan1412@gmail.com', 'siYwEOlLUEdhp7UK3m57SZien8v2', '$2y$10$wBB4KfaCwqkx1Rz3Kf9vR.cXRJ27EH4XEGAirJKV24FuJf8LiN6MK', 'Phạm Tuấn', NULL, '2025-11-25 13:13:02', '2025-11-25 13:14:42', '2025-11-25 13:14:42', 1, 'user'),
(101, 'gsdfgsd', 'phadshada@gmail.com', 'MkIEC3eXIbQYjLdLAqakBANaF8D2', '$2y$10$xBFDUvrCtrxXUUIBUOnWPOOa1amNef/E01G2tj.nwA83nISUw0j6S', 'gsdfgsd', NULL, '2025-11-25 13:18:46', '2025-11-25 13:18:52', '2025-11-25 13:18:52', 1, 'user'),
(103, 'ầdasdasd', 'vngs009@gmail.com', 'X2QNNes7PHNQkxjdBLAmVegxuBk2', '$2y$10$xd2iNTCFQOXfOdIVQ3FjeuZtWmecAF9COEqNNAmaM2hL.38H1gH6.', 'ầdasdasd', NULL, '2025-11-25 14:51:31', '2025-11-25 14:51:37', '2025-11-25 14:51:37', 1, 'user');

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `user_stats`
-- (See below for the actual view)
--
CREATE TABLE `user_stats` (
`id` int
,`username` varchar(50)
,`email` varchar(100)
,`movies_watched` bigint
,`favorites_count` bigint
,`ratings_count` bigint
);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_subscriptions`
--

CREATE TABLE `user_subscriptions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `plan_id` int NOT NULL,
  `start_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date` timestamp NOT NULL,
  `status` enum('active','expired','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `auto_renew` tinyint(1) DEFAULT '0',
  `order_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user_subscriptions`
--

INSERT INTO `user_subscriptions` (`id`, `user_id`, `plan_id`, `start_date`, `end_date`, `status`, `auto_renew`, `order_id`, `created_at`, `updated_at`) VALUES
(14, 1, 3, '2025-11-25 14:24:04', '2025-12-25 14:24:04', 'cancelled', 0, 8, '2025-11-25 14:24:04', '2025-11-25 14:42:10'),
(15, 1, 4, '2025-11-25 14:24:04', '2025-12-25 14:24:04', 'active', 0, 8, '2025-11-25 14:24:04', '2025-11-25 14:24:04'),
(17, 1, 2, '2025-11-25 14:26:53', '2025-12-25 14:26:53', 'cancelled', 0, 7, '2025-11-25 14:26:53', '2025-11-25 14:39:48'),
(20, 6, 3, '2025-11-25 14:46:24', '2026-01-24 14:46:24', 'active', 0, 1, '2025-11-25 14:46:24', '2025-11-25 14:46:24'),
(21, 103, 2, '2025-11-25 14:56:19', '2026-02-23 14:56:19', 'cancelled', 0, 13, '2025-11-25 14:56:19', '2025-11-25 14:59:43'),
(22, 103, 3, '2025-11-25 14:57:52', '2026-01-24 14:57:52', 'cancelled', 0, 14, '2025-11-25 14:57:52', '2025-11-25 14:59:45'),
(23, 103, 2, '2025-11-25 14:58:07', '2025-12-25 14:58:07', 'cancelled', 0, 15, '2025-11-25 14:58:07', '2025-11-25 14:59:47'),
(25, 103, 2, '2025-11-25 15:02:49', '2025-12-25 15:02:49', 'active', 0, 17, '2025-11-25 15:02:49', '2025-11-25 15:02:49');

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_active_subscriptions`
-- (See below for the actual view)
--
CREATE TABLE `v_active_subscriptions` (
`id` int
,`user_id` int
,`username` varchar(50)
,`email` varchar(100)
,`plan_name` varchar(50)
,`plan_slug` varchar(50)
,`quality` varchar(20)
,`max_devices` int
,`start_date` timestamp
,`end_date` timestamp
,`status` enum('active','expired','cancelled')
,`days_remaining` int
);

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_order_stats`
-- (See below for the actual view)
--
CREATE TABLE `v_order_stats` (
`order_date` date
,`total_orders` bigint
,`completed_orders` decimal(23,0)
,`pending_orders` decimal(23,0)
,`cancelled_orders` decimal(23,0)
,`total_revenue` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_top_selling_plans`
-- (See below for the actual view)
--
CREATE TABLE `v_top_selling_plans` (
`id` int
,`name` varchar(50)
,`slug` varchar(50)
,`price` decimal(10,2)
,`total_sold` bigint
,`total_revenue` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `watch_history`
--

CREATE TABLE `watch_history` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `movie_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `movie_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `movie_poster` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `episode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `watch_time` int DEFAULT '0',
  `duration` int DEFAULT '0',
  `watched_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`plan_id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_movie_slug` (`movie_slug`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_active` (`is_active`);

--
-- Chỉ mục cho bảng `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_coupon_user` (`coupon_id`,`user_id`);

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`movie_slug`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_added_at` (`added_at`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `idx_order_code` (`order_code`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `idx_order_id` (`order_id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Chỉ mục cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rating` (`user_id`,`movie_slug`),
  ADD KEY `idx_movie_slug` (`movie_slug`);

--
-- Chỉ mục cho bảng `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_active` (`is_active`);

--
-- Chỉ mục cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_transaction_code` (`transaction_code`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_firebase_uid` (`firebase_uid`);

--
-- Chỉ mục cho bảng `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `idx_user_status` (`user_id`,`status`),
  ADD KEY `idx_end_date` (`end_date`);

--
-- Chỉ mục cho bảng `watch_history`
--
ALTER TABLE `watch_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_movie` (`user_id`,`movie_slug`),
  ADD KEY `idx_watched_at` (`watched_at`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `coupon_usage`
--
ALTER TABLE `coupon_usage`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT cho bảng `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `watch_history`
--
ALTER TABLE `watch_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `user_stats`
--
DROP TABLE IF EXISTS `user_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_stats`  AS SELECT `u`.`id` AS `id`, `u`.`username` AS `username`, `u`.`email` AS `email`, count(distinct `wh`.`movie_slug`) AS `movies_watched`, count(distinct `f`.`movie_slug`) AS `favorites_count`, count(distinct `r`.`movie_slug`) AS `ratings_count` FROM (((`users` `u` left join `watch_history` `wh` on((`u`.`id` = `wh`.`user_id`))) left join `favorites` `f` on((`u`.`id` = `f`.`user_id`))) left join `ratings` `r` on((`u`.`id` = `r`.`user_id`))) GROUP BY `u`.`id`, `u`.`username`, `u`.`email` ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_active_subscriptions`
--
DROP TABLE IF EXISTS `v_active_subscriptions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_active_subscriptions`  AS SELECT `us`.`id` AS `id`, `us`.`user_id` AS `user_id`, `u`.`username` AS `username`, `u`.`email` AS `email`, `sp`.`name` AS `plan_name`, `sp`.`slug` AS `plan_slug`, `sp`.`quality` AS `quality`, `sp`.`max_devices` AS `max_devices`, `us`.`start_date` AS `start_date`, `us`.`end_date` AS `end_date`, `us`.`status` AS `status`, (to_days(`us`.`end_date`) - to_days(now())) AS `days_remaining` FROM ((`user_subscriptions` `us` join `users` `u` on((`us`.`user_id` = `u`.`id`))) join `subscription_plans` `sp` on((`us`.`plan_id` = `sp`.`id`))) WHERE ((`us`.`status` = 'active') AND (`us`.`end_date` > now())) ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_order_stats`
--
DROP TABLE IF EXISTS `v_order_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_order_stats`  AS SELECT cast(`orders`.`created_at` as date) AS `order_date`, count(0) AS `total_orders`, sum((case when (`orders`.`status` = 'completed') then 1 else 0 end)) AS `completed_orders`, sum((case when (`orders`.`status` = 'pending') then 1 else 0 end)) AS `pending_orders`, sum((case when (`orders`.`status` = 'cancelled') then 1 else 0 end)) AS `cancelled_orders`, sum((case when (`orders`.`payment_status` = 'paid') then `orders`.`total` else 0 end)) AS `total_revenue` FROM `orders` GROUP BY cast(`orders`.`created_at` as date) ORDER BY `order_date` DESC ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_top_selling_plans`
--
DROP TABLE IF EXISTS `v_top_selling_plans`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_top_selling_plans`  AS SELECT `sp`.`id` AS `id`, `sp`.`name` AS `name`, `sp`.`slug` AS `slug`, `sp`.`price` AS `price`, count(`oi`.`id`) AS `total_sold`, sum(`oi`.`total`) AS `total_revenue` FROM ((`subscription_plans` `sp` left join `order_items` `oi` on((`sp`.`id` = `oi`.`plan_id`))) left join `orders` `o` on((`oi`.`order_id` = `o`.`id`))) WHERE (`o`.`payment_status` = 'paid') GROUP BY `sp`.`id`, `sp`.`name`, `sp`.`slug`, `sp`.`price` ORDER BY `total_sold` DESC ;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD CONSTRAINT `coupon_usage_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`),
  ADD CONSTRAINT `coupon_usage_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `coupon_usage_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Ràng buộc cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`);

--
-- Ràng buộc cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Ràng buộc cho bảng `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD CONSTRAINT `user_subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_subscriptions_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`);

--
-- Ràng buộc cho bảng `watch_history`
--
ALTER TABLE `watch_history`
  ADD CONSTRAINT `watch_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
