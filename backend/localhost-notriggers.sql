-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 06, 2025 at 08:28 AM
-- Server version: 8.0.44
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hthree_film`
--
-- Database: railway (using existing)
-- USE railway;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
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
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `plan_id`, `quantity`, `created_at`, `updated_at`) VALUES
(20, 4, 2, 2, '2025-11-25 08:09:40', '2025-11-25 08:09:56'),
(33, 5, 3, 1, '2025-11-25 12:56:16', '2025-11-25 12:56:16'),
(34, 99, 3, 1, '2025-11-25 13:03:09', '2025-11-25 13:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
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
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `movie_slug`, `parent_id`, `content`, `likes`, `created_at`, `updated_at`) VALUES
(1, 1, 'avatar-2-dong-chay-cua-nuoc', NULL, 'Phim hay quá! Đáng xem!', 234, '2025-11-22 06:41:27', '2025-11-22 06:41:27'),
(2, 2, 'robin-hood', NULL, 'Diễn xuất tuyệt vời!', 189, '2025-11-22 06:41:27', '2025-11-22 06:41:27'),
(3, 1, 'son-than-di-van-luc', NULL, 'Phim Trung Quốc hay nhất năm', 156, '2025-11-22 06:41:27', '2025-11-22 06:41:27');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
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
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `description`, `discount_type`, `discount_value`, `min_order_value`, `max_discount`, `usage_limit`, `usage_count`, `user_limit`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME2025', 'Giảm 20% cho đơn hàng đầu tiên', 'percent', 20.00, 50000.00, 50000.00, 100, 0, 1, '2025-11-25 03:33:06', '2025-12-25 03:33:06', 1, '2025-11-25 03:33:06', '2025-11-25 03:33:06'),
(2, 'SUMMER50K', 'Giảm 50k cho đơn từ 100k', 'fixed', 50000.00, 100000.00, NULL, 50, 0, 1, '2025-11-25 03:33:06', '2026-01-24 03:33:06', 1, '2025-11-25 03:33:06', '2025-11-25 03:33:06'),
(3, 'VIP30', 'Giảm 30% cho gói VIP', 'percent', 30.00, 150000.00, 100000.00, 20, 0, 1, '2025-11-25 03:33:06', '2026-02-23 03:33:06', 1, '2025-11-25 03:33:06', '2025-11-25 03:33:06');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usage`
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
-- Table structure for table `favorites`
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
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `movie_slug`, `movie_name`, `movie_poster`, `movie_year`, `movie_quality`, `added_at`) VALUES
(1, 1, 'avatar-2-dong-chay-cua-nuoc', 'Avatar 2: Dòng Chảy Của Nước', 'https://phimimg.com/...', 2022, 'HD', '2025-11-22 06:41:40'),
(2, 1, 'robin-hood', 'Robin Hood', 'https://phimimg.com/...', 2018, 'FHD', '2025-11-22 06:41:40');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
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
  `payment_method` enum('vnpay','momo','zalopay','bank_transfer','cod','vietqr') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `status` enum('pending','processing','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `admin_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `paid_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `qr_code_url` text COLLATE utf8mb4_unicode_ci COMMENT 'URL của mã QR',
  `transfer_content` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nội dung chuyển khoản',
  `expires_at` datetime DEFAULT NULL COMMENT 'Thời gian hết hạn đơn hàng',
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID giao dịch từ Casso',
  `payment_note` text COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `subtotal`, `discount`, `total`, `payment_method`, `payment_status`, `status`, `note`, `admin_note`, `paid_at`, `completed_at`, `cancelled_at`, `created_at`, `updated_at`, `qr_code_url`, `transfer_content`, `expires_at`, `transaction_id`, `payment_note`) VALUES
(1, 'ORD202511255917', 6, 'Admin', 'hient7182@gmail.com', '', 100000.00, 0.00, 100000.00, 'vnpay', 'paid', 'completed', '', NULL, '2025-11-25 14:46:24', '2025-11-25 14:46:24', NULL, '2025-11-25 12:32:24', '2025-11-25 14:46:24', NULL, NULL, NULL, NULL, NULL),
(2, 'ORD202511253238', 6, 'Admin', 'hient7182@gmail.com', '', 100000.00, 0.00, 100000.00, 'bank_transfer', 'paid', 'completed', '', NULL, '2025-11-25 14:27:09', '2025-11-25 14:27:09', NULL, '2025-11-25 12:33:35', '2025-11-25 14:27:09', NULL, NULL, NULL, NULL, NULL),
(7, 'ORD20251125260', 1, 'adasfdas', 'admin@hthree.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:26:53', '2025-11-25 14:26:53', NULL, '2025-11-25 13:49:55', '2025-11-25 14:26:53', NULL, NULL, NULL, NULL, NULL),
(8, 'ORD202511255262', 1, 'admin', 'admin@hthree.com', '0825591211', 400000.00, 0.00, 400000.00, 'bank_transfer', 'paid', 'completed', '', NULL, '2025-11-25 14:24:04', '2025-11-25 14:24:04', NULL, '2025-11-25 13:52:04', '2025-11-25 14:24:04', NULL, NULL, NULL, NULL, NULL),
(9, 'ORD20251125383', 1, 'trungtuan', 'admin@hthree.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:15:04', '2025-11-25 14:15:04', NULL, '2025-11-25 14:08:31', '2025-11-25 14:15:04', NULL, NULL, NULL, NULL, NULL),
(10, 'ORD20251125918', 1, 'sdaf', 'admin@hthree.com', '0825591211', 200000.00, 0.00, 200000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:23:40', '2025-11-25 14:23:40', NULL, '2025-11-25 14:16:21', '2025-11-25 14:23:40', NULL, NULL, NULL, NULL, NULL),
(11, 'ORD20251125972', 1, 'trungtuan', 'admin@hthree.com', '0825591211', 200000.00, 0.00, 200000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:22:54', '2025-11-25 14:22:54', NULL, '2025-11-25 14:19:04', '2025-11-25 14:22:54', NULL, NULL, NULL, NULL, NULL),
(12, 'ORD20251125644', 103, 'ầdasdasd', 'vngs009@gmail.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'processing', NULL, NULL, '2025-12-04 12:56:07', NULL, NULL, '2025-11-25 14:51:49', '2025-12-04 12:56:07', NULL, NULL, NULL, NULL, NULL),
(13, 'ORD20251125029', 103, 'ầdasdasd', 'vngs009@gmail.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:56:19', '2025-11-25 14:56:19', NULL, '2025-11-25 14:56:19', '2025-11-25 14:56:19', NULL, NULL, NULL, NULL, NULL),
(14, 'ORD20251125728', 103, 'ầdasdasd', 'vngs009@gmail.com', '0825591211', 100000.00, 0.00, 100000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:57:52', '2025-11-25 14:57:52', NULL, '2025-11-25 14:57:52', '2025-11-25 14:57:52', NULL, NULL, NULL, NULL, NULL),
(15, 'ORD20251125730', 103, 'ầdasdasd', 'vngs009@gmail.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 14:58:07', '2025-11-25 14:58:07', NULL, '2025-11-25 14:58:07', '2025-11-25 14:58:07', NULL, NULL, NULL, NULL, NULL),
(16, 'ORD20251125401', 103, 'ầdasdasd', 'vngs009@gmail.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 15:00:10', '2025-11-25 15:00:10', NULL, '2025-11-25 15:00:10', '2025-11-25 15:00:10', NULL, NULL, NULL, NULL, NULL),
(17, 'ORD20251125535', 103, 'ầdasdasd', 'vngs009@gmail.com', '0825591211', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-11-25 15:02:49', '2025-11-25 15:02:49', NULL, '2025-11-25 15:02:49', '2025-11-25 15:02:49', NULL, NULL, NULL, NULL, NULL),
(18, 'ORD20251204621', 1, 'Phạm Trung Tuấn', 'admin@hthree.com', '0825591211', 100000.00, 0.00, 100000.00, 'bank_transfer', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-04 13:04:16', '2025-12-04 13:04:16', NULL, NULL, NULL, NULL, NULL),
(19, 'ORD20251204144', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'bank_transfer', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-04 13:27:04', '2025-12-04 13:27:04', NULL, NULL, NULL, NULL, NULL),
(20, 'ORD20251205716', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'bank_transfer', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-05 15:57:28', '2025-12-05 15:57:28', NULL, NULL, NULL, NULL, NULL),
(21, 'ORD20251205714', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'paid', 'completed', NULL, NULL, '2025-12-05 16:13:42', '2025-12-05 16:13:42', NULL, '2025-12-05 16:06:11', '2025-12-05 16:13:42', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251205714&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251205714', '2025-12-05 16:21:12', '12021672908', NULL),
(22, 'ORD20251205560', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-05 16:11:09', '2025-12-05 16:11:09', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251205560&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251205560', '2025-12-05 16:26:09', NULL, NULL),
(23, 'ORD20251205261', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'paid', 'completed', NULL, NULL, '2025-12-05 16:16:40', '2025-12-05 16:16:40', NULL, '2025-12-05 16:14:57', '2025-12-05 16:16:40', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251205261&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251205261', '2025-12-05 16:29:58', 'MANUAL_TEST_1764951400074', NULL),
(24, 'ORD20251205004', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'paid', 'pending', NULL, NULL, '2025-12-05 16:32:48', NULL, NULL, '2025-12-05 16:32:37', '2025-12-05 16:32:48', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251205004&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251205004', '2025-12-05 16:47:37', '12864350', 'Thanh toán qua VietQR - Casso'),
(25, 'ORD20251205591', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 2400000.00, 0.00, 2400000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-05 16:51:18', '2025-12-05 16:51:18', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=2400000.00&addInfo=HTHREE+ORD20251205591&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251205591', '2025-12-05 17:06:18', NULL, NULL),
(26, 'ORD20251206389', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 150000.00, 0.00, 150000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 07:38:24', '2025-12-06 07:38:24', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=150000.00&addInfo=HTHREE+ORD20251206389&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206389', '2025-12-06 07:53:24', NULL, NULL),
(27, 'ORD20251206204', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 150000.00, 7500.00, 142500.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 07:49:51', '2025-12-06 07:49:51', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=142500.00&addInfo=HTHREE+ORD20251206204&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206204', '2025-12-06 08:04:51', NULL, NULL),
(28, 'ORD20251206211', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 07:50:56', '2025-12-06 07:50:56', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251206211&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206211', '2025-12-06 08:05:56', NULL, NULL);

--

COMMIT;
