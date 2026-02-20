-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th2 20, 2026 lúc 04:29 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `bonsai`
--

DELIMITER $$
--
-- Thủ tục
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `seed_inventory` ()   BEGIN
  DECLARE i INT DEFAULT 21;

  WHILE i <= 79 DO

    -- Nhóm 1: hết hàng
    IF i BETWEEN 21 AND 40 THEN
      INSERT INTO inventory VALUES
      (NULL,i,'S',0,NOW(),200000),
      (NULL,i,'M',0,NOW(),200000),
      (NULL,i,'L',0,NOW(),200000);

    -- Nhóm 2: thiếu size
    ELSEIF i BETWEEN 41 AND 60 THEN
      INSERT INTO inventory VALUES
      (NULL,i,'S',10,NOW(),220000),
      (NULL,i,'M',0,NOW(),220000),
      (NULL,i,'L',15,NOW(),220000);

    -- Nhóm 3: đầy đủ
    ELSE
      INSERT INTO inventory VALUES
      (NULL,i,'S',20,NOW(),250000),
      (NULL,i,'M',20,NOW(),250000),
      (NULL,i,'L',20,NOW(),250000);
    END IF;

    SET i = i + 1;

  END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`, `updated_at`) VALUES
(4, 14, '2026-02-19 14:28:51', '2026-02-19 14:28:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `size`, `quantity`, `price`) VALUES
(77, 4, 51, 'S', 1, 450000.00),
(78, 4, 51, 'L', 1, 480000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Cây để bàn'),
(2, 'Cây cảnh nội thất'),
(3, 'Cây cảnh ngoại thất'),
(4, 'Chậu');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` enum('S','M','L') NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `import_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_adjust` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `size`, `quantity`, `updated_at`, `import_price`, `price_adjust`) VALUES
(1, 1, 'S', 15, '2026-02-19 13:06:26', 0.00, 0.00),
(2, 1, 'M', 20, '2026-02-19 14:49:45', 0.00, 15000.00),
(3, 1, 'L', 10, '2026-02-19 14:49:45', 0.00, 30000.00),
(4, 2, 'S', 12, '2026-02-19 13:06:26', 0.00, 0.00),
(5, 2, 'M', 18, '2026-02-19 14:49:45', 0.00, 15000.00),
(6, 2, 'L', 9, '2026-02-19 14:49:45', 0.00, 30000.00),
(7, 3, 'S', 25, '2026-02-19 13:06:26', 0.00, 0.00),
(8, 3, 'M', 30, '2026-02-19 14:49:45', 0.00, 15000.00),
(9, 3, 'L', 15, '2026-02-19 14:49:45', 0.00, 30000.00),
(10, 4, 'S', 14, '2026-02-19 13:06:26', 0.00, 0.00),
(11, 4, 'M', 16, '2026-02-19 14:49:45', 0.00, 15000.00),
(12, 4, 'L', 8, '2026-02-19 14:49:45', 0.00, 30000.00),
(13, 5, 'S', 22, '2026-02-19 13:06:26', 0.00, 0.00),
(14, 5, 'M', 27, '2026-02-19 14:49:45', 0.00, 15000.00),
(15, 5, 'L', 13, '2026-02-19 14:49:45', 0.00, 30000.00),
(16, 6, 'S', 10, '2026-02-19 13:06:26', 0.00, 0.00),
(17, 6, 'M', 15, '2026-02-19 14:49:45', 0.00, 15000.00),
(18, 6, 'L', 7, '2026-02-19 14:49:45', 0.00, 30000.00),
(19, 7, 'S', 18, '2026-02-19 13:06:26', 0.00, 0.00),
(20, 7, 'M', 24, '2026-02-19 14:49:45', 0.00, 15000.00),
(21, 7, 'L', 12, '2026-02-19 14:49:45', 0.00, 30000.00),
(22, 8, 'S', 20, '2026-02-19 13:06:26', 0.00, 0.00),
(23, 8, 'M', 26, '2026-02-19 14:49:45', 0.00, 15000.00),
(24, 8, 'L', 14, '2026-02-19 14:49:45', 0.00, 30000.00),
(25, 9, 'S', 17, '2026-02-19 13:06:26', 0.00, 0.00),
(26, 9, 'M', 21, '2026-02-19 14:49:45', 0.00, 15000.00),
(27, 9, 'L', 11, '2026-02-19 14:49:45', 0.00, 30000.00),
(28, 10, 'S', 13, '2026-02-19 13:06:26', 0.00, 0.00),
(29, 10, 'M', 19, '2026-02-19 14:49:45', 0.00, 15000.00),
(30, 10, 'L', 9, '2026-02-19 14:49:45', 0.00, 30000.00),
(31, 11, 'S', 16, '2026-02-19 13:06:26', 0.00, 0.00),
(32, 11, 'M', 22, '2026-02-19 14:49:45', 0.00, 15000.00),
(33, 11, 'L', 10, '2026-02-19 14:49:45', 0.00, 30000.00),
(34, 12, 'S', 19, '2026-02-19 13:06:26', 0.00, 0.00),
(35, 12, 'M', 25, '2026-02-19 14:49:45', 0.00, 15000.00),
(36, 12, 'L', 12, '2026-02-19 14:49:45', 0.00, 30000.00),
(37, 13, 'S', 21, '2026-02-19 13:06:26', 0.00, 0.00),
(38, 13, 'M', 28, '2026-02-19 14:49:45', 0.00, 15000.00),
(39, 13, 'L', 14, '2026-02-19 14:49:45', 0.00, 30000.00),
(40, 14, 'S', 11, '2026-02-19 13:06:26', 0.00, 0.00),
(41, 14, 'M', 17, '2026-02-19 14:49:45', 0.00, 15000.00),
(42, 14, 'L', 8, '2026-02-19 14:49:45', 0.00, 30000.00),
(43, 15, 'S', 23, '2026-02-19 13:06:26', 0.00, 0.00),
(44, 15, 'M', 29, '2026-02-19 14:49:45', 0.00, 15000.00),
(45, 15, 'L', 15, '2026-02-19 14:49:45', 0.00, 30000.00),
(46, 16, 'S', 14, '2026-02-19 13:06:26', 0.00, 0.00),
(47, 16, 'M', 20, '2026-02-19 14:49:45', 0.00, 15000.00),
(48, 16, 'L', 9, '2026-02-19 14:49:45', 0.00, 30000.00),
(49, 17, 'S', 18, '2026-02-19 13:06:26', 0.00, 0.00),
(50, 17, 'M', 23, '2026-02-19 14:49:45', 0.00, 15000.00),
(51, 17, 'L', 11, '2026-02-19 14:49:45', 0.00, 30000.00),
(52, 18, 'S', 24, '2026-02-19 13:06:26', 0.00, 0.00),
(53, 18, 'M', 31, '2026-02-19 14:49:45', 0.00, 15000.00),
(54, 18, 'L', 16, '2026-02-19 14:49:45', 0.00, 30000.00),
(55, 19, 'S', 12, '2026-02-19 13:06:26', 0.00, 0.00),
(56, 19, 'M', 18, '2026-02-19 14:49:45', 0.00, 15000.00),
(57, 19, 'L', 7, '2026-02-19 14:49:45', 0.00, 30000.00),
(58, 20, 'S', 26, '2026-02-19 13:06:26', 0.00, 0.00),
(59, 20, 'M', 33, '2026-02-19 14:49:45', 0.00, 15000.00),
(60, 20, 'L', 17, '2026-02-19 14:49:45', 0.00, 30000.00),
(67, 21, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(68, 21, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(69, 21, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(70, 22, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(71, 22, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(72, 22, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(73, 23, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(74, 23, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(75, 23, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(76, 24, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(77, 24, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(78, 24, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(79, 25, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(80, 25, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(81, 25, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(82, 26, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(83, 26, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(84, 26, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(85, 27, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(86, 27, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(87, 27, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(88, 28, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(89, 28, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(90, 28, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(91, 29, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(92, 29, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(93, 29, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(94, 30, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(95, 30, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(96, 30, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(97, 31, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(98, 31, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(99, 31, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(100, 32, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(101, 32, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(102, 32, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(103, 33, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(104, 33, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(105, 33, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(106, 34, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(107, 34, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(108, 34, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(109, 35, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(110, 35, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(111, 35, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(112, 36, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(113, 36, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(114, 36, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(115, 37, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(116, 37, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(117, 37, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(118, 38, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(119, 38, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(120, 38, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(121, 39, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(122, 39, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(123, 39, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(124, 40, 'S', 0, '2026-02-19 13:50:35', 200000.00, 0.00),
(125, 40, 'M', 0, '2026-02-19 14:49:45', 200000.00, 15000.00),
(126, 40, 'L', 0, '2026-02-19 14:49:45', 200000.00, 30000.00),
(127, 41, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(128, 41, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(129, 41, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(130, 42, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(131, 42, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(132, 42, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(133, 43, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(134, 43, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(135, 43, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(136, 44, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(137, 44, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(138, 44, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(139, 45, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(140, 45, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(141, 45, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(142, 46, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(143, 46, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(144, 46, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(145, 47, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(146, 47, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(147, 47, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(148, 48, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(149, 48, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(150, 48, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(151, 49, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(152, 49, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(153, 49, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(154, 50, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(155, 50, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(156, 50, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(157, 51, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(158, 51, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(159, 51, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(160, 52, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(161, 52, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(162, 52, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(163, 53, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(164, 53, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(165, 53, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(166, 54, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(167, 54, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(168, 54, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(169, 55, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(170, 55, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(171, 55, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(172, 56, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(173, 56, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(174, 56, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(175, 57, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(176, 57, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(177, 57, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(178, 58, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(179, 58, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(180, 58, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(181, 59, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(182, 59, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(183, 59, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(184, 60, 'S', 10, '2026-02-19 13:50:35', 220000.00, 0.00),
(185, 60, 'M', 0, '2026-02-19 14:49:45', 220000.00, 15000.00),
(186, 60, 'L', 15, '2026-02-19 14:49:45', 220000.00, 30000.00),
(187, 61, 'S', 20, '2026-02-19 13:50:35', 250000.00, 0.00),
(188, 61, 'M', 20, '2026-02-19 14:49:45', 250000.00, 15000.00),
(189, 61, 'L', 20, '2026-02-19 14:49:45', 250000.00, 30000.00),
(190, 62, 'S', 20, '2026-02-19 13:50:35', 250000.00, 0.00),
(191, 62, 'M', 20, '2026-02-19 14:49:45', 250000.00, 15000.00),
(192, 62, 'L', 20, '2026-02-19 14:49:45', 250000.00, 30000.00),
(193, 63, 'S', 20, '2026-02-19 13:50:35', 250000.00, 0.00),
(194, 63, 'M', 20, '2026-02-19 14:49:45', 250000.00, 15000.00),
(195, 63, 'L', 20, '2026-02-19 14:49:45', 250000.00, 30000.00),
(196, 64, 'S', 20, '2026-02-19 13:50:35', 250000.00, 0.00),
(197, 64, 'M', 20, '2026-02-19 14:49:45', 250000.00, 15000.00),
(198, 64, 'L', 20, '2026-02-19 14:49:45', 250000.00, 30000.00),
(199, 65, 'S', 20, '2026-02-19 13:50:35', 250000.00, 0.00),
(200, 65, 'M', 20, '2026-02-19 14:49:45', 250000.00, 15000.00),
(201, 65, 'L', 20, '2026-02-19 14:49:45', 250000.00, 30000.00),
(202, 66, 'S', 20, '2026-02-19 13:50:35', 250000.00, 0.00),
(203, 66, 'M', 20, '2026-02-19 14:49:45', 250000.00, 15000.00),
(204, 66, 'L', 20, '2026-02-19 14:49:45', 250000.00, 30000.00),
(205, 67, 'S', 20, '2026-02-19 13:50:35', 250000.00, 0.00),
(206, 67, 'M', 20, '2026-02-19 14:49:45', 250000.00, 15000.00),
(207, 67, 'L', 20, '2026-02-19 14:49:45', 250000.00, 30000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` enum('S','M','L') NOT NULL,
  `type` enum('import','export') NOT NULL,
  `quantity` int(11) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory_logs`
--

INSERT INTO `inventory_logs` (`id`, `product_id`, `size`, `type`, `quantity`, `note`, `created_at`) VALUES
(1, 1, 'S', 'import', 15, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(2, 1, 'M', 'import', 20, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(3, 1, 'L', 'import', 10, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(4, 2, 'S', 'import', 12, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(5, 2, 'M', 'import', 18, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(6, 2, 'L', 'import', 9, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(7, 3, 'S', 'import', 25, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(8, 3, 'M', 'import', 30, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(9, 3, 'L', 'import', 15, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(10, 4, 'S', 'import', 14, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(11, 4, 'M', 'import', 16, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(12, 4, 'L', 'import', 8, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(13, 5, 'S', 'import', 22, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(14, 5, 'M', 'import', 27, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(15, 5, 'L', 'import', 13, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(16, 6, 'S', 'import', 10, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(17, 6, 'M', 'import', 15, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(18, 6, 'L', 'import', 7, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(19, 7, 'S', 'import', 18, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(20, 7, 'M', 'import', 24, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(21, 7, 'L', 'import', 12, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(22, 8, 'S', 'import', 20, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(23, 8, 'M', 'import', 26, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(24, 8, 'L', 'import', 14, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(25, 9, 'S', 'import', 17, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(26, 9, 'M', 'import', 21, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(27, 9, 'L', 'import', 11, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(28, 10, 'S', 'import', 13, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(29, 10, 'M', 'import', 19, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(30, 10, 'L', 'import', 9, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(31, 11, 'S', 'import', 16, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(32, 11, 'M', 'import', 22, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(33, 11, 'L', 'import', 10, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(34, 12, 'S', 'import', 19, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(35, 12, 'M', 'import', 25, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(36, 12, 'L', 'import', 12, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(37, 13, 'S', 'import', 21, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(38, 13, 'M', 'import', 28, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(39, 13, 'L', 'import', 14, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(40, 14, 'S', 'import', 11, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(41, 14, 'M', 'import', 17, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(42, 14, 'L', 'import', 8, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(43, 15, 'S', 'import', 23, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(44, 15, 'M', 'import', 29, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(45, 15, 'L', 'import', 15, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(46, 16, 'S', 'import', 14, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(47, 16, 'M', 'import', 20, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(48, 16, 'L', 'import', 9, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(49, 17, 'S', 'import', 18, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(50, 17, 'M', 'import', 23, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(51, 17, 'L', 'import', 11, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(52, 18, 'S', 'import', 24, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(53, 18, 'M', 'import', 31, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(54, 18, 'L', 'import', 16, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(55, 19, 'S', 'import', 12, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(56, 19, 'M', 'import', 18, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(57, 19, 'L', 'import', 7, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(58, 20, 'S', 'import', 26, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(59, 20, 'M', 'import', 33, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(60, 20, 'L', 'import', 17, 'Nhập kho ban đầu', '2026-02-19 13:07:44'),
(67, 41, 'S', 'import', 10, 'Nhập hàng', '2026-02-19 13:53:50'),
(68, 41, 'M', 'import', 20, 'Nhập hàng', '2026-02-19 13:53:50'),
(69, 41, 'M', 'export', 20, 'Bán hết size M', '2026-02-19 13:53:50'),
(70, 41, 'L', 'import', 15, 'Nhập hàng', '2026-02-19 13:53:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `status` enum('pending','processing','shipping','delivered','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `created_at`, `updated_at`) VALUES
(1, 11, 450000.00, 'pending', '2026-02-19 19:29:20', '2026-02-19 19:29:20'),
(2, 11, 720000.00, 'shipping', '2026-02-19 19:29:20', '2026-02-19 19:29:20'),
(3, 11, 1200000.00, 'delivered', '2026-02-19 19:29:20', '2026-02-19 19:29:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 5, 1, 150000.00),
(2, 1, 8, 2, 150000.00),
(3, 2, 10, 1, 300000.00),
(4, 2, 15, 1, 420000.00),
(5, 3, 20, 2, 400000.00),
(6, 3, 25, 1, 400000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profit_rate` int(11) DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image`, `category_id`, `created_at`, `profit_rate`) VALUES
(1, 'Sen đá nâu', 35000, 'Sen đá là loại cây mọng nước có hình dáng nhỏ gọn và lá xếp tầng vô cùng bắt mắt. Cây có khả năng chịu hạn tốt, không cần tưới nước thường xuyên nên rất phù hợp với người bận rộn. Sen đá thích hợp đặt trên bàn học, bàn làm việc hoặc trang trí kệ sách. Màu sắc đa dạng và hình dáng độc đáo giúp không gian trở nên sinh động và hiện đại hơn. Ngoài giá trị thẩm mỹ, sen đá còn mang ý nghĩa phong thủy tượng trưng cho sự bền bỉ và may mắn. Sản phẩm được chăm sóc kỹ lưỡng trước khi giao đến khách hàng để đảm bảo cây luôn khỏe mạnh và phát triển tốt.', 'senda1.png', 1, '2026-01-29 14:08:50', 30),
(2, 'Sen đá kim cương', 40000, 'Sen đá là loại cây mọng nước có hình dáng nhỏ gọn và lá xếp tầng vô cùng bắt mắt. Cây có khả năng chịu hạn tốt, không cần tưới nước thường xuyên nên rất phù hợp với người bận rộn. Sen đá thích hợp đặt trên bàn học, bàn làm việc hoặc trang trí kệ sách. Màu sắc đa dạng và hình dáng độc đáo giúp không gian trở nên sinh động và hiện đại hơn. Ngoài giá trị thẩm mỹ, sen đá còn mang ý nghĩa phong thủy tượng trưng cho sự bền bỉ và may mắn. Sản phẩm được chăm sóc kỹ lưỡng trước khi giao đến khách hàng để đảm bảo cây luôn khỏe mạnh và phát triển tốt.', 'senda2.png', 1, '2026-01-29 14:08:50', 30),
(3, 'Sen đá móng rồng', 45000, 'Sen đá là loại cây mọng nước có hình dáng nhỏ gọn và lá xếp tầng vô cùng bắt mắt. Cây có khả năng chịu hạn tốt, không cần tưới nước thường xuyên nên rất phù hợp với người bận rộn. Sen đá thích hợp đặt trên bàn học, bàn làm việc hoặc trang trí kệ sách. Màu sắc đa dạng và hình dáng độc đáo giúp không gian trở nên sinh động và hiện đại hơn. Ngoài giá trị thẩm mỹ, sen đá còn mang ý nghĩa phong thủy tượng trưng cho sự bền bỉ và may mắn. Sản phẩm được chăm sóc kỹ lưỡng trước khi giao đến khách hàng để đảm bảo cây luôn khỏe mạnh và phát triển tốt.', 'senda3.png', 1, '2026-01-29 14:08:50', 30),
(4, 'Sen đá chuỗi ngọc', 50000, 'Sen đá là loại cây mọng nước có hình dáng nhỏ gọn và lá xếp tầng vô cùng bắt mắt. Cây có khả năng chịu hạn tốt, không cần tưới nước thường xuyên nên rất phù hợp với người bận rộn. Sen đá thích hợp đặt trên bàn học, bàn làm việc hoặc trang trí kệ sách. Màu sắc đa dạng và hình dáng độc đáo giúp không gian trở nên sinh động và hiện đại hơn. Ngoài giá trị thẩm mỹ, sen đá còn mang ý nghĩa phong thủy tượng trưng cho sự bền bỉ và may mắn. Sản phẩm được chăm sóc kỹ lưỡng trước khi giao đến khách hàng để đảm bảo cây luôn khỏe mạnh và phát triển tốt.', 'senda4.png', 1, '2026-01-29 14:08:50', 30),
(5, 'Sen đá viền hồng', 42000, 'Sen đá là loại cây mọng nước có hình dáng nhỏ gọn và lá xếp tầng vô cùng bắt mắt. Cây có khả năng chịu hạn tốt, không cần tưới nước thường xuyên nên rất phù hợp với người bận rộn. Sen đá thích hợp đặt trên bàn học, bàn làm việc hoặc trang trí kệ sách. Màu sắc đa dạng và hình dáng độc đáo giúp không gian trở nên sinh động và hiện đại hơn. Ngoài giá trị thẩm mỹ, sen đá còn mang ý nghĩa phong thủy tượng trưng cho sự bền bỉ và may mắn. Sản phẩm được chăm sóc kỹ lưỡng trước khi giao đến khách hàng để đảm bảo cây luôn khỏe mạnh và phát triển tốt.', 'senda5.png', 1, '2026-01-29 14:08:50', 30),
(6, 'Sen đá phật bà', 48000, 'Sen đá là loại cây mọng nước có hình dáng nhỏ gọn và lá xếp tầng vô cùng bắt mắt. Cây có khả năng chịu hạn tốt, không cần tưới nước thường xuyên nên rất phù hợp với người bận rộn. Sen đá thích hợp đặt trên bàn học, bàn làm việc hoặc trang trí kệ sách. Màu sắc đa dạng và hình dáng độc đáo giúp không gian trở nên sinh động và hiện đại hơn. Ngoài giá trị thẩm mỹ, sen đá còn mang ý nghĩa phong thủy tượng trưng cho sự bền bỉ và may mắn. Sản phẩm được chăm sóc kỹ lưỡng trước khi giao đến khách hàng để đảm bảo cây luôn khỏe mạnh và phát triển tốt.', 'senda6.png', 1, '2026-01-29 14:08:50', 30),
(7, 'Sen đá đô la', 38000, 'Sen đá là loại cây mọng nước có hình dáng nhỏ gọn và lá xếp tầng vô cùng bắt mắt. Cây có khả năng chịu hạn tốt, không cần tưới nước thường xuyên nên rất phù hợp với người bận rộn. Sen đá thích hợp đặt trên bàn học, bàn làm việc hoặc trang trí kệ sách. Màu sắc đa dạng và hình dáng độc đáo giúp không gian trở nên sinh động và hiện đại hơn. Ngoài giá trị thẩm mỹ, sen đá còn mang ý nghĩa phong thủy tượng trưng cho sự bền bỉ và may mắn. Sản phẩm được chăm sóc kỹ lưỡng trước khi giao đến khách hàng để đảm bảo cây luôn khỏe mạnh và phát triển tốt.', 'senda7.png', 1, '2026-01-29 14:08:50', 30),
(8, 'Sen đá tím', 52000, 'Sen đá là loại cây mọng nước có hình dáng nhỏ gọn và lá xếp tầng vô cùng bắt mắt. Cây có khả năng chịu hạn tốt, không cần tưới nước thường xuyên nên rất phù hợp với người bận rộn. Sen đá thích hợp đặt trên bàn học, bàn làm việc hoặc trang trí kệ sách. Màu sắc đa dạng và hình dáng độc đáo giúp không gian trở nên sinh động và hiện đại hơn. Ngoài giá trị thẩm mỹ, sen đá còn mang ý nghĩa phong thủy tượng trưng cho sự bền bỉ và may mắn. Sản phẩm được chăm sóc kỹ lưỡng trước khi giao đến khách hàng để đảm bảo cây luôn khỏe mạnh và phát triển tốt.', 'senda8.png', 1, '2026-01-29 14:08:50', 30),
(9, 'Sen đá lá tim', 46000, 'Sen đá là loại cây mọng nước có hình dáng nhỏ gọn và lá xếp tầng vô cùng bắt mắt. Cây có khả năng chịu hạn tốt, không cần tưới nước thường xuyên nên rất phù hợp với người bận rộn. Sen đá thích hợp đặt trên bàn học, bàn làm việc hoặc trang trí kệ sách. Màu sắc đa dạng và hình dáng độc đáo giúp không gian trở nên sinh động và hiện đại hơn. Ngoài giá trị thẩm mỹ, sen đá còn mang ý nghĩa phong thủy tượng trưng cho sự bền bỉ và may mắn. Sản phẩm được chăm sóc kỹ lưỡng trước khi giao đến khách hàng để đảm bảo cây luôn khỏe mạnh và phát triển tốt.', 'senda9.png', 1, '2026-01-29 14:08:50', 30),
(10, 'Sen đá thạch bích', 43000, 'Sen đá là loại cây mọng nước có hình dáng nhỏ gọn và lá xếp tầng vô cùng bắt mắt. Cây có khả năng chịu hạn tốt, không cần tưới nước thường xuyên nên rất phù hợp với người bận rộn. Sen đá thích hợp đặt trên bàn học, bàn làm việc hoặc trang trí kệ sách. Màu sắc đa dạng và hình dáng độc đáo giúp không gian trở nên sinh động và hiện đại hơn. Ngoài giá trị thẩm mỹ, sen đá còn mang ý nghĩa phong thủy tượng trưng cho sự bền bỉ và may mắn. Sản phẩm được chăm sóc kỹ lưỡng trước khi giao đến khách hàng để đảm bảo cây luôn khỏe mạnh và phát triển tốt.', 'senda10.png', 1, '2026-01-29 14:08:50', 30),
(11, 'Xương rồng tròn', 35000, 'Xương rồng là loại cây cảnh nổi bật với hình dáng mạnh mẽ và độc đáo. Cây có khả năng sinh trưởng tốt trong điều kiện khô hạn và ít cần chăm sóc. Đây là lựa chọn lý tưởng để trang trí bàn làm việc, quầy lễ tân hoặc không gian nhỏ. Xương rồng mang ý nghĩa tượng trưng cho sự kiên cường và nghị lực vượt khó. Nhờ khả năng thích nghi cao, cây phù hợp với nhiều môi trường khác nhau kể cả trong nhà lẫn ngoài trời. Mỗi chậu cây đều được tuyển chọn kỹ càng để đảm bảo chất lượng và tính thẩm mỹ cao nhất.', 'cactus1.png', 1, '2026-01-29 14:08:50', 30),
(12, 'Xương rồng tai thỏ', 40000, 'Xương rồng là loại cây cảnh nổi bật với hình dáng mạnh mẽ và độc đáo. Cây có khả năng sinh trưởng tốt trong điều kiện khô hạn và ít cần chăm sóc. Đây là lựa chọn lý tưởng để trang trí bàn làm việc, quầy lễ tân hoặc không gian nhỏ. Xương rồng mang ý nghĩa tượng trưng cho sự kiên cường và nghị lực vượt khó. Nhờ khả năng thích nghi cao, cây phù hợp với nhiều môi trường khác nhau kể cả trong nhà lẫn ngoài trời. Mỗi chậu cây đều được tuyển chọn kỹ càng để đảm bảo chất lượng và tính thẩm mỹ cao nhất.', 'cactus2.png', 1, '2026-01-29 14:08:50', 30),
(13, 'Xương rồng sao', 45000, 'Xương rồng là loại cây cảnh nổi bật với hình dáng mạnh mẽ và độc đáo. Cây có khả năng sinh trưởng tốt trong điều kiện khô hạn và ít cần chăm sóc. Đây là lựa chọn lý tưởng để trang trí bàn làm việc, quầy lễ tân hoặc không gian nhỏ. Xương rồng mang ý nghĩa tượng trưng cho sự kiên cường và nghị lực vượt khó. Nhờ khả năng thích nghi cao, cây phù hợp với nhiều môi trường khác nhau kể cả trong nhà lẫn ngoài trời. Mỗi chậu cây đều được tuyển chọn kỹ càng để đảm bảo chất lượng và tính thẩm mỹ cao nhất.', 'cactus3.png', 1, '2026-01-29 14:08:50', 30),
(14, 'Xương rồng bánh sinh nhật', 50000, 'Xương rồng là loại cây cảnh nổi bật với hình dáng mạnh mẽ và độc đáo. Cây có khả năng sinh trưởng tốt trong điều kiện khô hạn và ít cần chăm sóc. Đây là lựa chọn lý tưởng để trang trí bàn làm việc, quầy lễ tân hoặc không gian nhỏ. Xương rồng mang ý nghĩa tượng trưng cho sự kiên cường và nghị lực vượt khó. Nhờ khả năng thích nghi cao, cây phù hợp với nhiều môi trường khác nhau kể cả trong nhà lẫn ngoài trời. Mỗi chậu cây đều được tuyển chọn kỹ càng để đảm bảo chất lượng và tính thẩm mỹ cao nhất.', 'cactus4.png', 1, '2026-01-29 14:08:50', 30),
(15, 'Xương rồng kim', 42000, 'Xương rồng là loại cây cảnh nổi bật với hình dáng mạnh mẽ và độc đáo. Cây có khả năng sinh trưởng tốt trong điều kiện khô hạn và ít cần chăm sóc. Đây là lựa chọn lý tưởng để trang trí bàn làm việc, quầy lễ tân hoặc không gian nhỏ. Xương rồng mang ý nghĩa tượng trưng cho sự kiên cường và nghị lực vượt khó. Nhờ khả năng thích nghi cao, cây phù hợp với nhiều môi trường khác nhau kể cả trong nhà lẫn ngoài trời. Mỗi chậu cây đều được tuyển chọn kỹ càng để đảm bảo chất lượng và tính thẩm mỹ cao nhất.', 'cactus5.png', 1, '2026-01-29 14:08:50', 30),
(16, 'Xương rồng trụ', 48000, 'Xương rồng là loại cây cảnh nổi bật với hình dáng mạnh mẽ và độc đáo. Cây có khả năng sinh trưởng tốt trong điều kiện khô hạn và ít cần chăm sóc. Đây là lựa chọn lý tưởng để trang trí bàn làm việc, quầy lễ tân hoặc không gian nhỏ. Xương rồng mang ý nghĩa tượng trưng cho sự kiên cường và nghị lực vượt khó. Nhờ khả năng thích nghi cao, cây phù hợp với nhiều môi trường khác nhau kể cả trong nhà lẫn ngoài trời. Mỗi chậu cây đều được tuyển chọn kỹ càng để đảm bảo chất lượng và tính thẩm mỹ cao nhất.', 'cactus6.png', 1, '2026-01-29 14:08:50', 30),
(17, 'Xương rồng móc câu', 39000, 'Xương rồng là loại cây cảnh nổi bật với hình dáng mạnh mẽ và độc đáo. Cây có khả năng sinh trưởng tốt trong điều kiện khô hạn và ít cần chăm sóc. Đây là lựa chọn lý tưởng để trang trí bàn làm việc, quầy lễ tân hoặc không gian nhỏ. Xương rồng mang ý nghĩa tượng trưng cho sự kiên cường và nghị lực vượt khó. Nhờ khả năng thích nghi cao, cây phù hợp với nhiều môi trường khác nhau kể cả trong nhà lẫn ngoài trời. Mỗi chậu cây đều được tuyển chọn kỹ càng để đảm bảo chất lượng và tính thẩm mỹ cao nhất.', 'cactus7.png', 1, '2026-01-29 14:08:50', 30),
(18, 'Xương rồng tai mèo', 46000, 'Xương rồng là loại cây cảnh nổi bật với hình dáng mạnh mẽ và độc đáo. Cây có khả năng sinh trưởng tốt trong điều kiện khô hạn và ít cần chăm sóc. Đây là lựa chọn lý tưởng để trang trí bàn làm việc, quầy lễ tân hoặc không gian nhỏ. Xương rồng mang ý nghĩa tượng trưng cho sự kiên cường và nghị lực vượt khó. Nhờ khả năng thích nghi cao, cây phù hợp với nhiều môi trường khác nhau kể cả trong nhà lẫn ngoài trời. Mỗi chậu cây đều được tuyển chọn kỹ càng để đảm bảo chất lượng và tính thẩm mỹ cao nhất.', 'cactus8.png', 1, '2026-01-29 14:08:50', 30),
(19, 'Xương rồng vàng', 52000, 'Xương rồng là loại cây cảnh nổi bật với hình dáng mạnh mẽ và độc đáo. Cây có khả năng sinh trưởng tốt trong điều kiện khô hạn và ít cần chăm sóc. Đây là lựa chọn lý tưởng để trang trí bàn làm việc, quầy lễ tân hoặc không gian nhỏ. Xương rồng mang ý nghĩa tượng trưng cho sự kiên cường và nghị lực vượt khó. Nhờ khả năng thích nghi cao, cây phù hợp với nhiều môi trường khác nhau kể cả trong nhà lẫn ngoài trời. Mỗi chậu cây đều được tuyển chọn kỹ càng để đảm bảo chất lượng và tính thẩm mỹ cao nhất.', 'cactus9.png', 1, '2026-01-29 14:08:50', 30),
(20, 'Xương rồng đỏ', 55000, 'Xương rồng là loại cây cảnh nổi bật với hình dáng mạnh mẽ và độc đáo. Cây có khả năng sinh trưởng tốt trong điều kiện khô hạn và ít cần chăm sóc. Đây là lựa chọn lý tưởng để trang trí bàn làm việc, quầy lễ tân hoặc không gian nhỏ. Xương rồng mang ý nghĩa tượng trưng cho sự kiên cường và nghị lực vượt khó. Nhờ khả năng thích nghi cao, cây phù hợp với nhiều môi trường khác nhau kể cả trong nhà lẫn ngoài trời. Mỗi chậu cây đều được tuyển chọn kỹ càng để đảm bảo chất lượng và tính thẩm mỹ cao nhất.', 'cactus10.png', 1, '2026-01-29 14:08:50', 30),
(21, 'Sen đá mini mix', 60000, 'Sen đá là loại cây mọng nước có hình dáng nhỏ gọn và lá xếp tầng vô cùng bắt mắt. Cây có khả năng chịu hạn tốt, không cần tưới nước thường xuyên nên rất phù hợp với người bận rộn. Sen đá thích hợp đặt trên bàn học, bàn làm việc hoặc trang trí kệ sách. Màu sắc đa dạng và hình dáng độc đáo giúp không gian trở nên sinh động và hiện đại hơn. Ngoài giá trị thẩm mỹ, sen đá còn mang ý nghĩa phong thủy tượng trưng cho sự bền bỉ và may mắn. Sản phẩm được chăm sóc kỹ lưỡng trước khi giao đến khách hàng để đảm bảo cây luôn khỏe mạnh và phát triển tốt.', 'senda11.png', 1, '2026-01-29 14:08:50', 30),
(22, 'Cactus mini mix', 65000, 'Combo cactus mini đa dạng. Trang trí bàn học rất đẹp.', 'cactus11.png', 1, '2026-01-29 14:08:50', 30),
(23, 'Combo sen đá 3 chậu', 120000, 'Sen đá là loại cây mọng nước có hình dáng nhỏ gọn và lá xếp tầng vô cùng bắt mắt. Cây có khả năng chịu hạn tốt, không cần tưới nước thường xuyên nên rất phù hợp với người bận rộn. Sen đá thích hợp đặt trên bàn học, bàn làm việc hoặc trang trí kệ sách. Màu sắc đa dạng và hình dáng độc đáo giúp không gian trở nên sinh động và hiện đại hơn. Ngoài giá trị thẩm mỹ, sen đá còn mang ý nghĩa phong thủy tượng trưng cho sự bền bỉ và may mắn. Sản phẩm được chăm sóc kỹ lưỡng trước khi giao đến khách hàng để đảm bảo cây luôn khỏe mạnh và phát triển tốt.', 'combo1.png', 1, '2026-01-29 14:08:50', 30),
(24, 'Combo cactus 3 chậu', 130000, 'Combo cactus 3 chậu xinh xắn. Phù hợp decor phòng.', 'combo2.png', 1, '2026-01-29 14:08:50', 30),
(25, 'Kim tiền', 250000, 'Cây kim tiền tượng trưng cho tài lộc. Thường đặt trong phòng khách.', 'noithat1.png', 2, '2026-01-29 14:08:50', 30),
(26, 'Lưỡi hổ', 180000, 'Cây lưỡi hổ giúp lọc không khí. Rất dễ chăm sóc.', 'noithat2.png', 2, '2026-01-29 14:08:50', 30),
(27, 'Trầu bà xanh', 120000, 'Trầu bà xanh sinh trưởng nhanh. Phù hợp treo hoặc để bàn.', 'noithat3.png', 2, '2026-01-29 14:08:50', 30),
(28, 'Trầu bà vàng', 140000, 'Trầu bà vàng mang vẻ đẹp tươi sáng. Dễ trồng trong nhà.', 'noithat4.png', 2, '2026-01-29 14:08:50', 30),
(29, 'Cau tiểu trâm', 160000, 'Cau tiểu trâm thanh lọc không khí tốt. Phù hợp văn phòng.', 'noithat5.png', 2, '2026-01-29 14:08:50', 30),
(30, 'Vạn niên thanh', 150000, 'Vạn niên thanh mang ý nghĩa may mắn. Lá xanh quanh năm.', 'noithat6.png', 2, '2026-01-29 14:08:50', 30),
(31, 'Ngọc ngân', 170000, 'Ngọc ngân có màu sắc sang trọng. Thích hợp trưng bày.', 'noithat7.png', 2, '2026-01-29 14:08:50', 30),
(32, 'Thiết mộc lan', 300000, 'Thiết mộc lan mang phong thủy tốt. Ít cần chăm sóc.', 'noithat8.png', 2, '2026-01-29 14:08:50', 30),
(33, 'Bàng Singapore', 450000, 'Bàng Singapore dáng đẹp. Phù hợp phòng khách.', 'noithat9.png', 2, '2026-01-29 14:08:50', 30),
(34, 'Hồng môn', 200000, 'Hồng môn đỏ rực rỡ. Mang ý nghĩa may mắn.', 'noithat10.png', 2, '2026-01-29 14:08:50', 30),
(35, 'Lan ý', 220000, 'Lan ý giúp lọc không khí. Dễ trồng trong nhà.', 'noithat11.png', 2, '2026-01-29 14:08:50', 30),
(36, 'Phú quý', 190000, 'Phú quý mang tài lộc và thịnh vượng. Màu sắc bắt mắt.', 'noithat12.png', 2, '2026-01-29 14:08:50', 30),
(37, 'Cây thường xuân', 130000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'noithat13.png', 2, '2026-01-29 14:08:50', 30),
(38, 'Đa búp đỏ', 350000, 'Đa búp đỏ sang trọng. Thích hợp decor nội thất.', 'noithat14.png', 2, '2026-01-29 14:08:50', 30),
(39, 'Cọ cảnh', 280000, 'Cọ cảnh dễ sống. Tạo cảm giác xanh mát.', 'noithat15.png', 2, '2026-01-29 14:08:50', 30),
(40, 'Tùng bồng lai', 260000, 'Tùng bồng lai mang ý nghĩa phong thủy. Dễ chăm sóc.', 'noithat16.png', 2, '2026-01-29 14:08:50', 30),
(41, 'Trúc phú quý', 210000, 'Trúc phú quý mang lại may mắn. Hợp trang trí bàn.', 'noithat17.png', 2, '2026-01-29 14:08:50', 30),
(42, 'Ngũ gia bì', 230000, 'Ngũ gia bì giúp thanh lọc không khí. Cây khỏe.', 'noithat18.png', 2, '2026-01-29 14:08:50', 30),
(43, 'Cây phát tài', 240000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'noithat19.png', 2, '2026-01-29 14:08:50', 30),
(44, 'Cây cau vàng', 270000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'noithat20.png', 2, '2026-01-29 14:08:50', 30),
(45, 'Cây monstera', 320000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'noithat21.png', 2, '2026-01-29 14:08:50', 30),
(46, 'Cây dương xỉ', 110000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'noithat22.png', 2, '2026-01-29 14:08:50', 30),
(47, 'Cây dây nhện', 100000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'noithat23.png', 2, '2026-01-29 14:08:50', 30),
(48, 'Cây bạch mã hoàng tử', 290000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'noithat24.png', 2, '2026-01-29 14:08:50', 30),
(49, 'Hoa giấy', 300000, 'Hoa giấy nhiều màu sắc. Dễ trồng và ra hoa đẹp.', 'ngoai1.png', 3, '2026-01-29 14:08:50', 30),
(50, 'Cau vua', 600000, 'Cau vua trồng sân vườn. Tạo bóng mát tốt.', 'ngoai2.png', 3, '2026-01-29 14:08:50', 30),
(51, 'Sứ thái', 450000, 'Sứ thái hoa đẹp, dễ chăm sóc. Thích hợp trồng chậu.', 'ngoai3.png', 3, '2026-01-29 14:08:50', 30),
(52, 'Tùng la hán', 800000, 'Tùng la hán bonsai đẹp. Mang ý nghĩa phong thủy.', 'ngoai4.png', 3, '2026-01-29 14:08:50', 30),
(53, 'Lộc vừng', 900000, 'Lộc vừng mang tài lộc. Hoa đẹp và thơm.', 'ngoai5.png', 3, '2026-01-29 14:08:50', 30),
(54, 'Mai chiếu thủy', 500000, 'Mai chiếu thủy hoa nhỏ, thơm. Phù hợp sân vườn.', 'ngoai6.png', 3, '2026-01-29 14:08:50', 30),
(55, 'San hô đỏ', 350000, 'San hô đỏ màu sắc nổi bật. Dễ trồng.', 'ngoai7.png', 3, '2026-01-29 14:08:50', 30),
(56, 'Dừa cảnh', 700000, 'Dừa cảnh tạo không gian nhiệt đới. Phù hợp sân vườn.', 'ngoai8.png', 3, '2026-01-29 14:08:50', 30),
(57, 'Tre cảnh', 400000, 'Tre cảnh mang vẻ đẹp tự nhiên. Dễ chăm sóc.', 'ngoai9.png', 3, '2026-01-29 14:08:50', 30),
(58, 'Hoa mười giờ', 120000, 'Hoa mười giờ dễ trồng. Ra hoa quanh năm.', 'ngoai10.png', 3, '2026-01-29 14:08:50', 30),
(59, 'Hoa hồng leo', 250000, 'Hoa hồng leo đẹp và thơm. Phù hợp ban công.', 'ngoai11.png', 3, '2026-01-29 14:08:50', 30),
(60, 'Hoa lan vũ nữ', 550000, 'Lan vũ nữ hoa đẹp. Mang vẻ sang trọng.', 'ngoai12.png', 3, '2026-01-29 14:08:50', 30),
(61, 'Cây khế cảnh', 650000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'ngoai13.png', 3, '2026-01-29 14:08:50', 30),
(62, 'Cây me bonsai', 750000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'ngoai14.png', 3, '2026-01-29 14:08:50', 30),
(63, 'Cây sung cảnh', 680000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'ngoai15.png', 3, '2026-01-29 14:08:50', 30),
(64, 'Cây si bonsai', 850000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'ngoai16.png', 3, '2026-01-29 14:08:50', 30),
(65, 'Cây duối', 720000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'ngoai17.png', 3, '2026-01-29 14:08:50', 30),
(66, 'Cây bàng Đài Loan', 900000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'ngoai18.png', 3, '2026-01-29 14:08:50', 30),
(67, 'Cây tùng thơm', 380000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'ngoai20.png', 3, '2026-01-29 14:08:50', 30),
(69, 'Cây nguyệt quế', 460000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'ngoai21.png', 3, '2026-01-29 14:08:50', 30),
(72, 'Cây hoa dâm bụt', 280000, 'Cây cảnh xanh giúp mang lại không gian sống trong lành và gần gũi với thiên nhiên. Sản phẩm phù hợp trang trí phòng khách, văn phòng làm việc, ban công hoặc sân vườn. Cây có khả năng thanh lọc không khí và tạo cảm giác thư giãn cho người sử dụng. Việc chăm sóc tương đối đơn giản và không tốn quá nhiều thời gian. Mỗi cây đều được chọn lựa và chăm sóc kỹ trước khi đến tay khách hàng. Đây cũng là món quà ý nghĩa dành tặng bạn bè, người thân trong các dịp đặc biệt.', 'ngoai24.png', 3, '2026-01-29 14:08:50', 30),
(73, 'Chậu đất nung', 45000, 'Chậu đất nung chống ăn mòn, phù hợp trồng sen đá và cây mini.', 'pot1.png', 4, '2026-02-19 09:00:03', 30),
(74, 'Chậu lan đất nung', 45000, 'Chậu đất nung dành cho lan.', 'pot2.png', 4, '2026-02-19 09:00:03', 30),
(75, 'Chậu đất nung tròn', 30000, 'Chậu gốm thủ công với hoa văn cổ điển phù hợp bonsai mini.', 'pot3.png', 4, '2026-02-19 09:00:03', 30),
(76, 'Chậu đất nung trụ', 55000, 'Chậu đất nung hình trụ thích hợp trang trí ngoài trời', 'pot4.png', 4, '2026-02-19 09:00:03', 30),
(77, 'Chậu nhưa to', 75000, 'Chậu nhựa tiện lợi, khối lượng nhẹ, thể tích lớn.', 'pot5.png', 4, '2026-02-19 09:00:03', 30),
(78, 'Chậu nhựa mềm', 98000, 'Chậu nhựa mỏng nhẹ, tiện lợi', 'pot6.png', 4, '2026-02-19 09:00:03', 30),
(79, 'Chậu nhựa dài', 150000, 'Chậu nhựa nhẹ tiện lợi, thích hợp trang trí phòng khách hoặc ban công.', 'pot7.png', 4, '2026-02-19 09:00:03', 30);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT 5,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 10, 11, 5, 'Cây bonsai rất đẹp, giao hàng nhanh!', '2026-02-18 09:35:49'),
(2, 20, 12, 4, 'Chất lượng ổn, sẽ ủng hộ tiếp.', '2026-02-18 09:35:49'),
(3, 50, 10, 5, 'Trang trí phòng rất hợp.', '2026-02-18 09:35:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `fullname`, `email`, `phone`, `address`, `password`, `created_at`, `reset_token`, `reset_expires`, `role`) VALUES
(11, 'vusigma', 'Phùng Anh Vũ', 'vulmao@gmail.com', '0987654321', 'Lâm Đồng', '123456', '2026-02-18 09:34:15', NULL, NULL, 'customer'),
(12, 'phucsigma', 'Nguyễn Hoàng Phúc', 'phuclmao@gmail.com', '0987654321', 'Bình Dương', '123456', '2026-02-18 09:34:15', NULL, NULL, 'customer'),
(13, 'huysigma', 'Trần Nhựt Huy', 'huylmao@gmail.com', '0987654321', 'Vĩnh Long', '123456', '2026-02-18 09:34:15', NULL, NULL, 'customer'),
(14, 'Vu', 'Phùng Anh Vũ', 'phunganhvu@gmail.com', '', 'TH, Lâm Đồng', '$2y$10$9otdYV/.X4kCeXxXJS9Z8eClPQCB0KhtxyK9lou4zzKgPlQKutHOK', '2026-02-19 08:38:17', NULL, NULL, 'customer');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cart_user` (`user_id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cartitem_cart` (`cart_id`),
  ADD KEY `fk_cartitem_product` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_size` (`product_id`,`size`);

--
-- Chỉ mục cho bảng `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_logs_product` (`product_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT cho bảng `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cartitem_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cartitem_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inventory_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `fk_logs_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
