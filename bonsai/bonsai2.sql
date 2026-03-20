-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 20, 2026 lúc 03:53 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `bonsai2`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-02-22 11:39:02', '2026-02-22 11:39:02'),
(2, 2, '2026-03-02 10:23:36', '2026-03-02 10:23:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` enum('S','M','L') NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `size`, `quantity`, `price`) VALUES
(14, 1, 24, 'S', 1, 78000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `status`) VALUES
(1, 'Cây để bàn', 'active'),
(2, 'Cây nội thất', 'active'),
(3, 'Cây ngoại thất', 'active'),
(4, 'Chậu cây', 'active');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_receipts`
--

CREATE TABLE `import_receipts` (
  `id` int(11) NOT NULL,
  `import_date` date DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `import_price` decimal(10,2) DEFAULT NULL,
  `total_value` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `import_receipts`
--

INSERT INTO `import_receipts` (`id`, `import_date`, `product_id`, `size`, `quantity`, `import_price`, `total_value`, `status`) VALUES
(3, '2026-03-19', 76, 'L', 12, 23000.00, 276000.00, 'completed'),
(4, '2026-03-19', 75, 'L', 13, 12000.00, 156000.00, 'completed'),
(5, '2026-03-19', 3, 'L', 12, 12000000.00, 99999999.99, 'cancelled'),
(6, '2026-03-19', 1, 'L', 12, 12000.00, 144000.00, 'completed'),
(7, '2026-03-19', 2, 'L', 14, 13000.00, 182000.00, 'cancelled'),
(8, '2026-03-20', 16, 'L', 20, 35000.00, 700000.00, 'completed'),
(9, '2026-03-20', 1, 'L', 34, 23000.00, 782000.00, 'pending');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` enum('S','M','L') NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price_adjust` decimal(12,2) NOT NULL DEFAULT 0.00,
  `avg_import_price` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `size`, `quantity`, `price_adjust`, `avg_import_price`) VALUES
(192, 24, 'S', 78, 0.00, 60357),
(193, 75, 'L', 13, 0.00, 12000),
(194, 76, 'L', 12, 0.00, 23000),
(195, 1, 'L', 12, 0.00, 12000),
(200, 16, 'L', 20, 0.00, 35000);

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
  `import_price` decimal(12,2) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory_logs`
--

INSERT INTO `inventory_logs` (`id`, `product_id`, `size`, `type`, `quantity`, `import_price`, `note`, `created_at`) VALUES
(262, 24, 'S', 'import', 30, 60000.00, '', '2026-02-22 11:40:24'),
(263, 24, 'S', 'import', 30, 65000.00, 'lần2', '2026-02-22 11:44:12'),
(264, 24, 'S', 'export', 2, 62500.00, 'Xuất kho cho đơn hàng #7', '2026-02-22 11:46:46'),
(265, 24, 'S', 'export', 8, 62500.00, 'Xuất kho cho đơn hàng #8', '2026-02-22 11:48:48'),
(266, 24, 'S', 'import', 20, 55000.00, '', '2026-02-22 11:49:59'),
(267, 24, 'S', 'import', 8, 0.00, 'Hoàn kho do hủy đơn #8', '2026-02-22 12:51:27'),
(268, 75, 'L', 'import', 2, 10000000.00, 'f u', '2026-03-02 03:23:07'),
(269, 75, 'L', 'export', 1, 10000000.00, 'Xuất kho cho đơn hàng #9', '2026-03-02 03:24:22'),
(270, 24, 'S', 'export', 1, 60357.00, 'Xuất kho cho đơn hàng #9', '2026-03-02 03:24:22'),
(271, 75, 'S', 'import', 1, 0.00, 'Hoàn kho do hủy đơn #9', '2026-03-02 03:25:53'),
(272, 24, 'S', 'import', 1, 0.00, 'Hoàn kho do hủy đơn #9', '2026-03-02 03:25:53'),
(273, 75, 'L', 'export', 2, 10000000.00, 'Xuất kho cho đơn hàng #10', '2026-03-02 03:27:20'),
(274, 76, 'L', 'import', 12, 23000.00, NULL, '2026-03-19 13:55:31'),
(275, 75, 'L', 'import', 13, 12000.00, NULL, '2026-03-19 13:56:43'),
(276, 1, 'L', 'import', 12, 12000.00, NULL, '2026-03-19 14:17:00'),
(277, 16, 'L', 'import', 20, 35000.00, NULL, '2026-03-20 14:31:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` enum('processing','processed','shipping','shipped','cancelled') DEFAULT 'processing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `note`, `payment_method`, `total_amount`, `status`, `created_at`, `updated_at`, `total_price`, `fullname`, `email`, `phone`, `address`) VALUES
(4, 1, 'Duma có coincard', 'momo', 0.00, 'processing', '2026-02-22 07:56:52', '2026-02-22 14:56:52', 700000.00, 'HuyloreLowCortisol', 'trannhuthuy897@gmail.com', '0962713941', 'đường Trần Hưng Đạo, TPHCM'),
(5, 1, '', 'cod', 0.00, 'cancelled', '2026-02-22 08:30:23', '2026-02-22 15:44:48', 88000.00, 'Huylore', 'trannhuthuy897@gmail.com', '0962713941', 'Còn cái nịt'),
(6, 1, 'Khỏi giao', 'cod', 0.00, 'processing', '2026-02-22 08:45:55', '2026-02-22 15:45:55', 700000.00, 'Huylore', 'trannhuthuy897@gmail.com', '0962713941', '320/30, Trần Bình Trọng,  p. Chợ Quán, quận 5, TP.HCM'),
(7, 1, '', 'cod', 0.00, 'processing', '2026-02-22 11:46:46', '2026-02-22 18:46:46', 182000.00, 'Huylore', 'trannhuthuy897@gmail.com', '0962713941', '320/30, Trần Bình Trọng,  p. Chợ Quán, quận 5, TP.HCM'),
(8, 1, '', 'cod', 0.00, 'cancelled', '2026-02-22 11:48:48', '2026-02-22 19:51:27', 668000.00, 'Huylore', 'trannhuthuy897@gmail.com', '0962713941', '320/30, Trần Bình Trọng,  p. Chợ Quán, quận 5, TP.HCM'),
(9, 2, '', 'cod', 0.00, 'cancelled', '2026-03-02 03:24:22', '2026-03-02 10:25:53', 13098000.00, 'Nguyễn Hoàng Phúc', 'hoangphuc20092020@gmail.com', '0839444302', 'bl'),
(10, 2, '', 'cod', 0.00, 'processing', '2026-03-02 03:27:20', '2026-03-02 10:27:20', 26020000.00, 'Nguyễn Hoàng Phúc', 'hoangphuc20092020@gmail.com', '0839444302', 'bl');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` enum('S','M','L') NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `size`, `quantity`, `price`) VALUES
(4, 4, 24, 'S', 10, 68000.00),
(5, 5, 24, 'S', 1, 68000.00),
(6, 6, 24, 'S', 10, 68000.00),
(7, 7, 24, 'S', 2, 81000.00),
(8, 8, 24, 'S', 8, 81000.00),
(9, 9, 75, 'L', 1, 13000000.00),
(10, 9, 24, 'S', 1, 78000.00),
(11, 10, 75, 'L', 2, 13000000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profit_rate` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `image`, `created_at`, `profit_rate`, `status`) VALUES
(1, 1, 'Sen đá nâu', 'Sen đá nâu có tông màu trầm ấm, lá dày và mọng nước. Cây nhỏ gọn, dễ chăm sóc và phù hợp đặt trên bàn làm việc hoặc kệ sách.', 'senda1.png', '2026-01-29 07:08:50', 30, 1),
(2, 1, 'Sen đá kim cương', 'Sen đá kim cương nổi bật với lớp lá óng ánh như phủ sương. Dáng cây xếp tầng hài hòa, thích hợp trang trí không gian hiện đại.', 'senda2.png', '2026-01-29 07:08:50', 30, 1),
(3, 1, 'Sen đá móng rồng', 'Sen đá móng rồng có lá nhọn độc đáo, tạo điểm nhấn mạnh mẽ. Cây chịu hạn tốt và rất dễ chăm sóc.', 'senda3.png', '2026-01-29 07:08:50', 30, 1),
(4, 1, 'Sen đá chuỗi ngọc', 'Chuỗi ngọc có lá tròn xinh xắn mọc thành dây rủ nhẹ. Phù hợp treo ban công hoặc đặt trên cao.', 'senda4.png', '2026-01-29 07:08:50', 30, 1),
(5, 1, 'Sen đá viền hồng', 'Sen đá viền hồng nổi bật với mép lá hồng nhạt. Khi đủ nắng, màu sắc càng rực rỡ.', 'senda5.png', '2026-01-29 07:08:50', 30, 1),
(6, 1, 'Sen đá phật bà', 'Phật bà có dáng xếp tầng đều đặn như đài sen. Mang ý nghĩa bình an và may mắn.', 'senda6.png', '2026-01-29 07:08:50', 30, 1),
(7, 1, 'Sen đá đô la', 'Lá tròn nhỏ như đồng xu tượng trưng cho tài lộc. Phù hợp làm quà tặng phong thủy.', 'senda7.png', '2026-01-29 07:08:50', 30, 1),
(8, 1, 'Sen đá tím', 'Sen đá tím có màu tím nhẹ nhàng, tạo cảm giác sang trọng. Phù hợp decor bàn học.', 'senda8.png', '2026-01-29 07:08:50', 30, 1),
(9, 1, 'Sen đá lá tim', 'Lá cây hình trái tim độc đáo. Thích hợp làm quà tặng cho người thân.', 'senda9.png', '2026-01-29 07:08:50', 30, 1),
(10, 1, 'Sen đá thạch bích', 'Thạch bích xanh mát quanh năm, dễ sống và ít cần chăm sóc.', 'senda10.png', '2026-01-29 07:08:50', 30, 1),
(11, 1, 'Xương rồng tròn', 'Xương rồng tròn nhỏ gọn, hình cầu dễ thương. Phù hợp đặt bàn làm việc.', 'cactus1.png', '2026-01-29 07:08:50', 30, 1),
(12, 1, 'Xương rồng tai thỏ', 'Tai thỏ có hai nhánh mọc như tai thỏ đáng yêu. Chịu hạn tốt.', 'cactus2.png', '2026-01-29 07:08:50', 30, 1),
(13, 1, 'Xương rồng sao', 'Dáng sao độc đáo với các múi nổi bật. Cây khỏe và ít sâu bệnh.', 'cactus3.png', '2026-01-29 07:08:50', 30, 1),
(14, 1, 'Xương rồng bánh sinh nhật', 'Hình dáng tròn nhiều tầng như chiếc bánh sinh nhật nhỏ.', 'cactus4.png', '2026-01-29 07:08:50', 30, 1),
(15, 1, 'Xương rồng kim', 'Xương rồng kim phủ gai mảnh đều, mang vẻ đẹp mạnh mẽ.', 'cactus5.png', '2026-01-29 07:08:50', 30, 1),
(16, 1, 'Xương rồng trụ', 'Dáng trụ cao khỏe khoắn, thích hợp trang trí ban công.', 'cactus6.png', '2026-01-29 07:08:50', 30, 1),
(17, 1, 'Xương rồng móc câu', 'Gai cong như móc câu tạo vẻ độc đáo riêng biệt.', 'cactus7.png', '2026-01-29 07:08:50', 30, 1),
(18, 1, 'Xương rồng tai mèo', 'Tai mèo mềm mại, hình dáng đáng yêu.', 'cactus8.png', '2026-01-29 07:08:50', 30, 1),
(19, 1, 'Xương rồng vàng', 'Xương rồng vàng nổi bật với sắc vàng rực rỡ.', 'cactus9.png', '2026-01-29 07:08:50', 30, 1),
(20, 1, 'Xương rồng đỏ', 'Sắc đỏ nổi bật, tạo điểm nhấn mạnh mẽ cho không gian.', 'cactus10.png', '2026-01-29 07:08:50', 30, 1),
(21, 1, 'Sen đá mini mix', 'Combo nhiều loại sen đá mini đa dạng màu sắc.', 'senda11.png', '2026-01-29 07:08:50', 30, 1),
(22, 1, 'Cactus mini mix', 'Combo xương rồng mini nhỏ gọn, dễ trang trí.', 'cactus11.png', '2026-01-29 07:08:50', 30, 1),
(23, 1, 'Combo sen đá 3 chậu', 'Bộ 3 chậu sen đá phối hợp hài hòa.', 'combo1.png', '2026-01-29 07:08:50', 30, 1),
(24, 1, 'Combo cactus', 'Combo 3 chậu cactus đa dạng hình dáng.', 'combo2.png', '2026-01-29 07:08:50', 30, 1),
(25, 2, 'Kim tiền', 'Kim tiền tượng trưng tài lộc và thịnh vượng.', 'noithat1.png', '2026-01-29 07:08:50', 30, 1),
(26, 2, 'Lưỡi hổ', 'Lưỡi hổ giúp lọc không khí, rất dễ trồng.', 'noithat2.png', '2026-01-29 07:08:50', 30, 1),
(27, 2, 'Trầu bà xanh', 'Trầu bà xanh sinh trưởng nhanh, phù hợp treo tường.', 'noithat3.png', '2026-01-29 07:08:50', 30, 1),
(28, 2, 'Trầu bà vàng', 'Trầu bà vàng mang sắc tươi sáng cho căn phòng.', 'noithat4.png', '2026-01-29 07:08:50', 30, 1),
(29, 2, 'Cau tiểu trâm', 'Cau tiểu trâm thanh lọc không khí tốt.', 'noithat5.png', '2026-01-29 07:08:50', 30, 1),
(30, 2, 'Vạn niên thanh', 'Lá xanh bền bỉ quanh năm.', 'noithat6.png', '2026-01-29 07:08:50', 30, 1),
(31, 2, 'Ngọc ngân', 'Ngọc ngân có màu lá pha trắng sang trọng.', 'noithat7.png', '2026-01-29 07:08:50', 30, 1),
(32, 2, 'Thiết mộc lan', 'Thiết mộc lan mang ý nghĩa phong thủy tốt.', 'noithat8.png', '2026-01-29 07:08:50', 30, 1),
(33, 2, 'Bàng Singapore', 'Lá lớn xanh đậm, thích hợp phòng khách.', 'noithat9.png', '2026-01-29 07:08:50', 30, 1),
(34, 2, 'Hồng môn', 'Hoa đỏ nổi bật, mang ý nghĩa may mắn.', 'noithat10.png', '2026-01-29 07:08:50', 30, 1),
(35, 2, 'Lan ý', 'Lan ý giúp lọc không khí hiệu quả.', 'noithat11.png', '2026-01-29 07:08:50', 30, 1),
(36, 2, 'Phú quý', 'Phú quý có sắc đỏ xanh nổi bật.', 'noithat12.png', '2026-01-29 07:08:50', 30, 1),
(37, 2, 'Cây thường xuân', 'Thường xuân xanh mát, thích hợp treo.', 'noithat13.png', '2026-01-29 07:08:50', 30, 1),
(38, 2, 'Đa búp đỏ', 'Đa búp đỏ sang trọng, dễ chăm sóc.', 'noithat14.png', '2026-01-29 07:08:50', 30, 1),
(39, 2, 'Cọ cảnh', 'Cọ cảnh tạo cảm giác nhiệt đới.', 'noithat15.png', '2026-01-29 07:08:50', 30, 1),
(40, 2, 'Tùng bồng lai', 'Tùng bồng lai mang vẻ đẹp phong thủy.', 'noithat16.png', '2026-01-29 07:08:50', 30, 1),
(41, 2, 'Trúc phú quý', 'Trúc phú quý mang tài lộc.', 'noithat17.png', '2026-01-29 07:08:50', 30, 1),
(42, 2, 'Ngũ gia bì', 'Ngũ gia bì xanh tốt quanh năm.', 'noithat18.png', '2026-01-29 07:08:50', 30, 1),
(43, 2, 'Cây phát tài', 'Cây phát tài tượng trưng thịnh vượng.', 'noithat19.png', '2026-01-29 07:08:50', 30, 1),
(44, 2, 'Cây cau vàng', 'Cau vàng tạo điểm nhấn tươi sáng.', 'noithat20.png', '2026-01-29 07:08:50', 30, 1),
(45, 2, 'Cây monstera', 'Monstera lá xẻ độc đáo, phong cách hiện đại.', 'noithat21.png', '2026-01-29 07:08:50', 30, 1),
(46, 2, 'Cây dương xỉ', 'Dương xỉ xanh mướt, ưa bóng mát.', 'noithat22.png', '2026-01-29 07:08:50', 30, 1),
(47, 2, 'Cây dây nhện', 'Dây nhện dễ sống, thích hợp treo cao.', 'noithat23.png', '2026-01-29 07:08:50', 30, 1),
(48, 2, 'Cây bạch mã hoàng tử', 'Bạch mã hoàng tử sang trọng và nổi bật.', 'noithat24.png', '2026-01-29 07:08:50', 30, 1),
(49, 3, 'Hoa giấy', 'Hoa giấy nhiều màu sắc, nở rực rỡ.', 'ngoai1.png', '2026-01-29 07:08:50', 30, 1),
(50, 3, 'Cau vua', 'Cau vua cao lớn, tạo bóng mát sân vườn.', 'ngoai2.png', '2026-01-29 07:08:50', 30, 1),
(51, 3, 'Sứ thái', 'Sứ thái hoa đẹp và bền màu.', 'ngoai3.png', '2026-01-29 07:08:50', 30, 1),
(52, 3, 'Tùng la hán', 'Tùng la hán bonsai phong thủy đẹp.', 'ngoai4.png', '2026-01-29 07:08:50', 30, 1),
(53, 3, 'Lộc vừng', 'Lộc vừng hoa rủ đẹp và thơm nhẹ.', 'ngoai5.png', '2026-01-29 07:08:50', 30, 1),
(54, 3, 'Mai chiếu thủy', 'Hoa nhỏ trắng thơm dịu.', 'ngoai6.png', '2026-01-29 07:08:50', 30, 1),
(55, 3, 'San hô đỏ', 'San hô đỏ màu sắc nổi bật.', 'ngoai7.png', '2026-01-29 07:08:50', 30, 1),
(56, 3, 'Dừa cảnh', 'Dừa cảnh tạo không gian nhiệt đới.', 'ngoai8.png', '2026-01-29 07:08:50', 30, 1),
(57, 3, 'Tre cảnh', 'Tre cảnh tự nhiên, thanh lịch.', 'ngoai9.png', '2026-01-29 07:08:50', 30, 1),
(58, 3, 'Hoa mười giờ', 'Hoa mười giờ nở quanh năm.', 'ngoai10.png', '2026-01-29 07:08:50', 30, 1),
(59, 3, 'Hoa hồng leo', 'Hoa hồng leo thơm và đẹp.', 'ngoai11.png', '2026-01-29 07:08:50', 30, 1),
(60, 3, 'Hoa lan vũ nữ', 'Lan vũ nữ mang vẻ sang trọng.', 'ngoai12.png', '2026-01-29 07:08:50', 30, 1),
(61, 3, 'Cây khế cảnh', 'Khế cảnh bonsai độc đáo.', 'ngoai13.png', '2026-01-29 07:08:50', 30, 1),
(62, 3, 'Cây me bonsai', 'Me bonsai dáng cổ kính.', 'ngoai14.png', '2026-01-29 07:08:50', 30, 1),
(63, 3, 'Cây sung cảnh', 'Sung cảnh mang ý nghĩa sung túc.', 'ngoai15.png', '2026-01-29 07:08:50', 30, 1),
(64, 3, 'Cây si bonsai', 'Si bonsai dáng thế đẹp.', 'ngoai16.png', '2026-01-29 07:08:50', 30, 1),
(65, 3, 'Cây duối', 'Duối bonsai mang vẻ cổ thụ.', 'ngoai17.png', '2026-01-29 07:08:50', 30, 1),
(66, 3, 'Cây bàng Đài Loan', 'Bàng Đài Loan cao lớn, tán rộng.', 'ngoai18.png', '2026-01-29 07:08:50', 30, 1),
(67, 3, 'Cây tùng thơm', 'Tùng thơm tỏa hương nhẹ dễ chịu.', 'ngoai20.png', '2026-01-29 07:08:50', 30, 1),
(68, 3, 'Cây nguyệt quế', 'Nguyệt quế hoa trắng thơm dịu.', 'ngoai21.png', '2026-01-29 07:08:50', 30, 1),
(70, 4, 'Chậu đất nung', 'Chậu đất nung truyền thống bền đẹp.', 'pot1.png', '2026-02-19 02:00:03', 30, 1),
(71, 4, 'Chậu lan đất nung', 'Chậu chuyên dụng trồng lan.', 'pot2.png', '2026-02-19 02:00:03', 30, 1),
(72, 4, 'Chậu đất nung tròn', 'Chậu tròn cổ điển phù hợp bonsai mini.', 'pot3.png', '2026-02-19 02:00:03', 30, 1),
(73, 4, 'Chậu đất nung trụ', 'Chậu trụ chắc chắn cho cây cao.', 'pot4.png', '2026-02-19 02:00:03', 30, 1),
(75, 4, 'Chậu nhựa mềm', 'Chậu nhựa mỏng nhẹ dễ di chuyển.', 'pot6.png', '2026-02-19 02:00:03', 30, 0),
(76, 4, 'Chậu nhựa Dài', 'Chậu nhựa dài phù hợp trồng hàng cây.', 'pot7.png', '2026-02-19 02:00:03', 30, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive','warning') CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL DEFAULT 'active',
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `phone`, `address`, `password`, `status`, `role`, `created_at`, `avatar`) VALUES
(1, 'Huylore', 'Huy', 'trannhuthuy897@gmail.com', '0962713941', '320/30, Trần Bình Trọng,  p. Chợ Quán, quận 12,hCM', '$2y$10$Q0eKA8AY9qKGEPh69ijm7uKPa7e6062jC3Y46z5rJunTcm42.9Twq', 'active', 'customer', '2026-02-22 04:20:02', '1773196770_Screenshot 2025-12-06 191243.png'),
(2, 'Nguyễn Hoàng Phúc', 'Nguyen Hoang Phuc', 'hoangphuc20092020@gmail.com', '0839444302', 'bl', '$2y$10$aVwiS78MnEeQ1VpiyMcU9uVukw4igxizf71zaWL3jBS9gcu03PpIi', 'active', 'customer', '2026-03-02 02:53:23', NULL),
(3, 'Nguyễn Hoàng Phúc', 'Phuc', 'phuc8386@gmail.com', '0919000291', '12 bạc liêu', '$2y$10$V2I0zi6aV2u7WNZqLcnAReXvnKp4JJhor8sOvjgqlnYQPVvQgwMNe', 'active', 'admin', '2026-03-02 13:59:35', NULL),
(4, 'Vũ Phùng Anh', 'Vũ', 'phungvumao@gmail.com', '0393067863', '26 Trần văn thành', '$2y$10$y7NsO.PJ.kfzJs3zTg6dN.7GfFs6BpU31V935Ah9uQMSX8z5rrJC2', 'active', 'customer', '2026-03-16 03:11:24', NULL),
(6, 'Vũ Phùng Anh', 'hảo', 'phunganhvutthp@gmail.com', '0393067863', 'bl', '$2y$10$ZYW9JZrB4z.7akn1W7BTy.frOfKW4VA1OFivybJUX6o/kBdcdYVBO', 'active', 'customer', '2026-03-20 09:38:35', NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `import_receipts`
--
ALTER TABLE `import_receipts`
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
  ADD KEY `product_id` (`product_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `import_receipts`
--
ALTER TABLE `import_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT cho bảng `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=278;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
