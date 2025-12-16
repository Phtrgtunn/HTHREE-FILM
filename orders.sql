-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 16, 2025 at 07:03 PM
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
(28, 'ORD20251206211', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 07:50:56', '2025-12-06 07:50:56', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251206211&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206211', '2025-12-06 08:05:56', NULL, NULL),
(29, 'ORD20251206079', 103, 'ầdasdasd', 'vngs009@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 12:46:07', '2025-12-06 12:46:07', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251206079&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206079', '2025-12-06 13:01:07', NULL, NULL),
(30, 'ORD20251206792', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 150000.00, 7500.00, 142500.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 14:04:04', '2025-12-06 14:04:04', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=142500.00&addInfo=HTHREE+ORD20251206792&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206792', '2025-12-06 14:19:04', NULL, NULL),
(31, 'ORD20251206262', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 14:04:15', '2025-12-06 14:04:15', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251206262&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206262', '2025-12-06 14:19:15', NULL, NULL),
(32, 'ORD20251206023', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 15:02:32', '2025-12-06 15:02:32', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251206023&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206023', '2025-12-06 15:17:32', NULL, NULL),
(33, 'ORD20251206515', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 15:05:15', '2025-12-06 15:05:15', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251206515&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206515', '2025-12-06 15:20:15', NULL, NULL),
(34, 'ORD20251206561', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 15:06:32', '2025-12-06 15:06:32', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251206561&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206561', '2025-12-06 15:21:32', NULL, NULL),
(35, 'ORD20251206089', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 15:35:26', '2025-12-06 15:35:26', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251206089&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206089', '2025-12-06 15:50:26', NULL, NULL),
(36, 'ORD20251206616', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 15:40:07', '2025-12-06 15:40:07', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251206616&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206616', '2025-12-06 15:55:07', NULL, NULL),
(37, 'ORD20251206906', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-06 15:40:34', '2025-12-06 15:40:34', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251206906&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251206906', '2025-12-06 15:55:34', NULL, NULL),
(38, 'ORD20251210803', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-10 02:12:58', '2025-12-10 02:12:58', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210803&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210803', '2025-12-10 02:27:58', NULL, NULL),
(39, 'ORD20251210652', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-10 02:15:24', '2025-12-10 02:15:24', NULL, '2025-12-10 02:15:24', '2025-12-10 02:15:24', NULL, NULL, NULL, 'MANUAL_1765332924', NULL),
(40, 'ORD20251210405', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-10 02:15:42', '2025-12-10 02:15:42', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210405&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210405', '2025-12-10 02:30:42', NULL, NULL),
(41, 'ORD20251210995', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'paid', 'completed', NULL, NULL, '2025-12-10 03:03:11', '2025-12-10 03:03:11', NULL, '2025-12-10 03:03:11', '2025-12-10 03:03:11', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210995&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210995', '2025-12-10 03:18:11', 'VIETQR_AUTO_1765335791722', NULL),
(42, 'ORD20251210659', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'paid', 'completed', NULL, NULL, '2025-12-10 03:05:39', '2025-12-10 03:05:39', NULL, '2025-12-10 03:05:24', '2025-12-10 03:05:39', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210659&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210659', '2025-12-10 03:20:24', 'VIETQR_SIM_1765335939292', NULL),
(43, 'ORD20251210188', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-10 03:07:51', '2025-12-10 03:07:51', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210188&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210188', '2025-12-10 03:22:51', NULL, NULL),
(44, 'ORD20251210962', 107, 'TÚ', '23600300@kthcm.edu.vn', '', 300000.00, 30000.00, 270000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-10 03:23:36', '2025-12-10 03:23:36', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=270000.00&addInfo=HTHREE+ORD20251210962&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210962', '2025-12-10 03:38:36', NULL, NULL),
(45, 'ORD20251210833', 107, 'TÚ', '23600300@kthcm.edu.vn', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-10 03:24:39', '2025-12-10 03:24:39', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210833&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210833', '2025-12-10 03:39:39', NULL, NULL),
(46, 'ORD20251210031', 107, 'TÚ', '23600300@kthcm.edu.vn', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-10 03:25:05', '2025-12-10 03:25:05', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210031&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210031', '2025-12-10 03:40:05', NULL, NULL),
(47, 'ORD20251210363', 107, 'TÚ', '23600300@kthcm.edu.vn', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-10 03:26:05', '2025-12-10 03:26:05', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210363&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210363', '2025-12-10 03:41:05', NULL, NULL),
(48, 'ORD20251210928', 107, 'TÚ', '23600300@kthcm.edu.vn', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-10 03:27:22', '2025-12-10 03:27:22', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210928&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210928', '2025-12-10 03:42:22', NULL, NULL),
(49, 'ORD20251210854', 107, 'TÚ', '23600300@kthcm.edu.vn', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-10 03:33:08', '2025-12-10 03:33:08', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210854&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210854', '2025-12-10 03:48:08', NULL, NULL),
(50, 'ORD20251210722', 107, 'TÚ', '23600300@kthcm.edu.vn', '', 50000.00, 0.00, 50000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-10 03:33:26', '2025-12-10 03:33:26', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210722&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210722', '2025-12-10 03:48:26', NULL, NULL),
(51, 'ORD20251210139', 107, 'TÚ', '23600300@kthcm.edu.vn', '', 50000.00, 0.00, 50000.00, 'vietqr', 'paid', 'completed', NULL, NULL, '2025-12-10 03:33:52', '2025-12-10 03:33:52', NULL, '2025-12-10 03:33:42', '2025-12-10 03:33:52', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210139&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210139', '2025-12-10 03:48:42', 'LOCALHOST_AUTO_1765337632569', NULL),
(52, 'ORD20251210667', 107, 'TÚ', '23600300@kthcm.edu.vn', '', 50000.00, 0.00, 50000.00, 'vietqr', 'paid', 'completed', NULL, NULL, '2025-12-10 03:38:01', '2025-12-10 03:38:01', NULL, '2025-12-10 03:37:46', '2025-12-10 03:38:01', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210667&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210667', '2025-12-10 03:52:46', 'SIMULATE_15S_1765337881801', NULL),
(53, 'ORD20251210927', 107, 'TÚ', '23600300@kthcm.edu.vn', '', 50000.00, 0.00, 50000.00, 'vietqr', 'paid', 'completed', NULL, NULL, '2025-12-10 03:40:29', '2025-12-10 03:40:29', NULL, '2025-12-10 03:40:14', '2025-12-10 03:40:29', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=50000.00&addInfo=HTHREE+ORD20251210927&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251210927', '2025-12-10 03:55:14', 'SIMULATE_15S_1765338029955', NULL),
(54, 'ORD20251216865', 107, 'TÚ', '23600300@kthcm.edu.vn', '', 2000.00, 0.00, 2000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-16 12:18:00', '2025-12-16 12:18:00', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=2000.00&addInfo=HTHREE+ORD20251216865&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251216865', '2025-12-16 12:33:00', NULL, NULL),
(55, 'ORD20251216645', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 1500.00, 0.00, 1500.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-16 13:06:10', '2025-12-16 13:06:10', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=1500.00&addInfo=HTHREE+ORD20251216645&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251216645', '2025-12-16 13:21:10', NULL, NULL),
(56, 'ORD20251216384', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 2000.00, 0.00, 2000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-16 13:06:41', '2025-12-16 13:06:41', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=2000.00&addInfo=HTHREE+ORD20251216384&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251216384', '2025-12-16 13:21:41', NULL, NULL),
(57, 'ORD20251216513', 104, 'Phạm Trung Tuấn', 'vngs007@gmail.com', '', 1500.00, 0.00, 1500.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 13:27:16', '2025-12-16 13:27:16', NULL, '2025-12-16 13:27:16', '2025-12-16 13:27:16', NULL, NULL, NULL, 'MANUAL_1765891636', NULL),
(58, 'ORD20251216730', 108, 'Hùng', 'vngs006@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 13:38:01', '2025-12-16 13:38:01', NULL, '2025-12-16 13:38:01', '2025-12-16 13:38:01', NULL, NULL, NULL, 'MANUAL_1765892281', NULL),
(59, 'ORD20251216909', 108, 'Hùng', 'vngs006@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 13:48:20', '2025-12-16 13:48:20', NULL, '2025-12-16 13:48:20', '2025-12-16 13:48:20', NULL, NULL, NULL, 'MANUAL_1765892900', NULL),
(60, 'ORD20251216183', 108, 'Hùng', 'vngs006@gmail.com', '', 2000.00, 0.00, 2000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-16 14:05:51', '2025-12-16 14:05:51', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=2000.00&addInfo=HTHREE+ORD20251216183&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251216183', '2025-12-16 14:20:51', NULL, NULL),
(61, 'ORD20251216439', 108, 'Hùng', 'vngs006@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:05:59', '2025-12-16 14:05:59', NULL, '2025-12-16 14:05:58', '2025-12-16 14:05:59', NULL, NULL, NULL, 'MANUAL_1765893959', NULL),
(62, 'ORD20251216919', 108, 'Hùng', 'vngs006@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:06:22', '2025-12-16 14:06:22', NULL, '2025-12-16 14:06:22', '2025-12-16 14:06:22', NULL, NULL, NULL, 'MANUAL_1765893982', NULL),
(63, 'ORD20251216756', 108, 'Hùng', 'vngs006@gmail.com', '', 4000.00, 0.00, 4000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:07:08', '2025-12-16 14:07:08', NULL, '2025-12-16 14:07:08', '2025-12-16 14:07:08', NULL, NULL, NULL, 'MANUAL_1765894028', NULL),
(64, 'ORD20251216652', 108, 'Hùng', 'vngs006@gmail.com', '', 4000.00, 0.00, 4000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:08:30', '2025-12-16 14:08:30', NULL, '2025-12-16 14:08:30', '2025-12-16 14:08:30', NULL, NULL, NULL, 'MANUAL_1765894110', NULL),
(65, 'ORD20251216671', 108, 'Hùng', 'vngs006@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:09:19', '2025-12-16 14:09:19', NULL, '2025-12-16 14:09:19', '2025-12-16 14:09:19', NULL, NULL, NULL, 'MANUAL_1765894159', NULL),
(66, 'ORD20251216747', 108, 'Hùng', 'vngs006@gmail.com', '', 4000.00, 0.00, 4000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:13:53', '2025-12-16 14:13:53', NULL, '2025-12-16 14:13:53', '2025-12-16 14:13:53', NULL, NULL, NULL, 'MANUAL_1765894433', NULL),
(67, 'ORD20251216437', 108, 'Hùng', 'vngs006@gmail.com', '', 5000.00, 0.00, 5000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:14:08', '2025-12-16 14:14:08', NULL, '2025-12-16 14:14:08', '2025-12-16 14:14:08', NULL, NULL, NULL, 'MANUAL_1765894448', NULL),
(68, 'ORD20251216953', 108, 'Hùng', 'vngs006@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:17:14', '2025-12-16 14:17:14', NULL, '2025-12-16 14:17:14', '2025-12-16 14:17:14', NULL, NULL, NULL, 'MANUAL_1765894634', NULL),
(69, 'ORD20251216918', 108, 'Hùng', 'vngs006@gmail.com', '', 4000.00, 0.00, 4000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:20:21', '2025-12-16 14:20:21', NULL, '2025-12-16 14:20:21', '2025-12-16 14:20:21', NULL, NULL, NULL, 'MANUAL_1765894821', NULL),
(70, 'ORD20251216508', 108, 'Hùng', 'vngs006@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:25:30', '2025-12-16 14:25:30', NULL, '2025-12-16 14:25:30', '2025-12-16 14:25:30', NULL, NULL, NULL, 'MANUAL_1765895130', NULL),
(71, 'ORD20251216992', 108, 'Hùng', 'vngs006@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:40:26', '2025-12-16 14:40:26', NULL, '2025-12-16 14:40:26', '2025-12-16 14:40:26', NULL, NULL, NULL, 'MANUAL_1765896026', NULL),
(72, 'ORD20251216138', 108, 'Hùng', 'vngs006@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:40:49', '2025-12-16 14:40:49', NULL, '2025-12-16 14:40:49', '2025-12-16 14:40:49', NULL, NULL, NULL, 'MANUAL_1765896049', NULL),
(73, 'ORD20251216245', 107, 'TÚ', '23600300@kthcm.edu.vn', '', 4000.00, 0.00, 4000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 14:56:55', '2025-12-16 14:56:55', NULL, '2025-12-16 14:56:55', '2025-12-16 14:56:55', NULL, NULL, NULL, 'MANUAL_1765897015', NULL),
(74, 'DEMO_1765898562_1', 1, 'Demo User', 'demo@test.com', NULL, 2000.00, 0.00, 2000.00, 'vietqr', 'paid', 'completed', NULL, NULL, NULL, NULL, NULL, '2025-12-16 15:22:42', '2025-12-16 15:22:42', NULL, NULL, NULL, NULL, NULL),
(75, 'DEMO_1765898650_1', 1, 'Demo User', 'demo@test.com', NULL, 2000.00, 0.00, 2000.00, 'vietqr', 'paid', 'completed', NULL, NULL, NULL, NULL, NULL, '2025-12-16 15:24:10', '2025-12-16 15:24:10', NULL, NULL, NULL, NULL, NULL),
(76, 'DEMO_1765898718_1', 1, 'Demo User', 'demo@test.com', NULL, 2000.00, 0.00, 2000.00, 'vietqr', 'paid', 'completed', NULL, NULL, NULL, NULL, NULL, '2025-12-16 15:25:18', '2025-12-16 15:25:18', NULL, NULL, NULL, NULL, NULL),
(77, 'ORD20251216025', 101, 'Phạm Trung Tuấn', 'phadshada@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 15:34:26', '2025-12-16 15:34:26', NULL, '2025-12-16 15:34:26', '2025-12-16 15:34:26', NULL, NULL, NULL, 'MANUAL_1765899266', NULL),
(78, 'ORD20251216970', 101, 'Phạm Trung Tuấn', 'phadshada@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 15:41:28', '2025-12-16 15:41:28', NULL, '2025-12-16 15:41:28', '2025-12-16 15:41:28', NULL, NULL, NULL, 'MANUAL_1765899688', NULL),
(80, 'DEMO_1765900498_1', 1, 'Demo User', 'demo@test.com', NULL, 2000.00, 0.00, 2000.00, 'vietqr', 'paid', 'completed', NULL, NULL, NULL, NULL, NULL, '2025-12-16 15:54:58', '2025-12-16 15:54:58', NULL, NULL, NULL, NULL, NULL),
(82, 'DEMO_1765900675_1', 1, 'Demo User', 'demo@test.com', NULL, 2000.00, 0.00, 2000.00, 'vietqr', 'paid', 'completed', NULL, NULL, NULL, NULL, NULL, '2025-12-16 15:57:55', '2025-12-16 15:57:55', NULL, NULL, NULL, NULL, NULL),
(84, 'DEMO_1765901647_1', 1, 'Demo User', 'demo@test.com', NULL, 2000.00, 0.00, 2000.00, 'vietqr', 'paid', 'completed', NULL, NULL, NULL, NULL, NULL, '2025-12-16 16:14:07', '2025-12-16 16:14:07', NULL, NULL, NULL, NULL, NULL),
(85, 'DEMO_1765901662_1', 1, 'Demo User', 'demo@test.com', NULL, 2000.00, 0.00, 2000.00, 'vietqr', 'paid', 'completed', NULL, NULL, NULL, NULL, NULL, '2025-12-16 16:14:22', '2025-12-16 16:14:22', NULL, NULL, NULL, NULL, NULL),
(86, 'DEMO_1765901671_1', 1, 'Demo User', 'demo@test.com', NULL, 2000.00, 0.00, 2000.00, 'vietqr', 'paid', 'completed', NULL, NULL, NULL, NULL, NULL, '2025-12-16 16:14:31', '2025-12-16 16:14:31', NULL, NULL, NULL, NULL, NULL),
(88, 'DEMO_1765901677_1', 1, 'Demo User', 'demo@test.com', NULL, 2000.00, 0.00, 2000.00, 'vietqr', 'paid', 'completed', NULL, NULL, NULL, NULL, NULL, '2025-12-16 16:14:37', '2025-12-16 16:14:37', NULL, NULL, NULL, NULL, NULL),
(89, 'DEMO_1765901787_1', 1, 'Demo User', 'demo@test.com', NULL, 2000.00, 0.00, 2000.00, 'vietqr', 'paid', 'completed', NULL, NULL, NULL, NULL, NULL, '2025-12-16 16:16:27', '2025-12-16 16:16:27', NULL, NULL, NULL, NULL, NULL),
(90, 'ORD20251216109', 110, 'tuấn', 'hient71232@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 16:17:53', '2025-12-16 16:17:53', NULL, '2025-12-16 16:17:53', '2025-12-16 16:17:53', NULL, NULL, NULL, 'MANUAL_1765901873', NULL),
(91, 'ORD20251216685', 110, 'tuấn', 'hient71232@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 16:22:41', '2025-12-16 16:22:41', NULL, '2025-12-16 16:22:41', '2025-12-16 16:22:41', NULL, NULL, NULL, 'MANUAL_1765902161', NULL),
(92, 'DEMO_1765902394_2', 2, 'Demo User', 'demo@test.com', NULL, 2000.00, 0.00, 2000.00, 'vietqr', 'paid', 'completed', NULL, NULL, NULL, NULL, NULL, '2025-12-16 16:26:34', '2025-12-16 16:26:34', NULL, NULL, NULL, NULL, NULL),
(93, 'ORD20251216518', 110, 'tuấn', 'hient71232@gmail.com', '', 4000.00, 0.00, 4000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 16:28:02', '2025-12-16 16:28:02', NULL, '2025-12-16 16:28:02', '2025-12-16 16:28:02', NULL, NULL, NULL, 'MANUAL_1765902482', NULL),
(94, 'ORD20251216954', 110, 'tuấn', 'hient71232@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 16:35:49', '2025-12-16 16:35:49', NULL, '2025-12-16 16:35:49', '2025-12-16 16:35:49', NULL, NULL, NULL, 'MANUAL_1765902949', NULL),
(95, 'ORD20251216658', 110, 'tuấn', 'hient71232@gmail.com', '', 2000.00, 0.00, 2000.00, 'vietqr', 'pending', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-12-16 16:37:32', '2025-12-16 16:37:32', 'https://img.vietqr.io/image/970422-0825591211-compact2.png?amount=2000.00&addInfo=HTHREE+ORD20251216658&accountName=PHAM+TRUNG+TUAN', 'HTHREE ORD20251216658', '2025-12-16 16:52:32', NULL, NULL),
(96, 'ORD20251216628', 110, 'tuấn', 'hient71232@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 16:37:45', '2025-12-16 16:37:45', NULL, '2025-12-16 16:37:44', '2025-12-16 16:37:45', NULL, NULL, NULL, 'MANUAL_1765903065', NULL),
(99, 'ORD20251216682', 101, 'Phạm Trung Tuấn', 'phadshada@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 17:06:41', '2025-12-16 17:06:41', NULL, '2025-12-16 17:06:41', '2025-12-16 17:06:41', NULL, NULL, NULL, 'MANUAL_1765904801', NULL),
(101, 'ORD20251216679', 111, 'Phạm Trung Tuấn', 'vngs004@gmail.com', '', 2000.00, 0.00, 2000.00, 'bank_transfer', 'paid', 'completed', NULL, NULL, '2025-12-16 17:14:53', '2025-12-16 17:14:53', NULL, '2025-12-16 17:14:53', '2025-12-16 17:14:53', NULL, NULL, NULL, 'MANUAL_1765905293', NULL),
(115, 'DEMO_1765911561_109', 109, 'Demo User', 'demo@test.com', NULL, 2000.00, 0.00, 2000.00, 'vietqr', 'paid', 'completed', NULL, NULL, NULL, NULL, NULL, '2025-12-16 18:59:21', '2025-12-16 18:59:21', NULL, NULL, NULL, NULL, NULL);

--
-- Triggers `orders`
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
