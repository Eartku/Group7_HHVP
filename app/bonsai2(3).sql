-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 27, 2026 lúc 01:32 PM
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
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 3, '2026-03-20 10:28:37', '2026-03-20 10:28:37'),
(2, 9, '2026-03-20 15:53:47', '2026-03-20 15:53:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `status`) VALUES
(1, 'Cây để bàn', NULL, NULL, 'active'),
(2, 'Cây nội thất', '', NULL, 'active'),
(3, 'Cây ngoại thất', NULL, NULL, 'active'),
(4, 'Chậu cây', NULL, NULL, 'active'),
(6, 'Phùng Anh Vũ', 'be chiếm', '1774162375_Gh-v3U0X0AA2NgR.jpg', 'inactive');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_receipts`
--

CREATE TABLE `import_receipts` (
  `id` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `import_receipts`
--

INSERT INTO `import_receipts` (`id`, `note`, `status`, `created_by`, `created_at`) VALUES
(4, '', 'confirmed', 9, '2026-03-21 11:33:30'),
(5, '', 'confirmed', 9, '2026-03-22 08:58:10'),
(6, '', 'confirmed', 9, '2026-03-22 09:17:40'),
(7, '', 'confirmed', 9, '2026-03-27 15:36:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `avg_import_price` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `size_id`, `quantity`, `avg_import_price`) VALUES
(4, 73, 6, 0, 180714.29),
(5, 76, 4, 239, 230000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` int(11) NOT NULL,
  `receipt_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `type` enum('import','export') NOT NULL,
  `quantity` int(11) NOT NULL,
  `import_price` decimal(12,2) DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory_logs`
--

INSERT INTO `inventory_logs` (`id`, `receipt_id`, `order_id`, `product_id`, `size_id`, `type`, `quantity`, `import_price`, `note`, `created_at`, `updated_at`) VALUES
(6, 4, NULL, 73, 6, 'import', 30, 100000.00, 'Nhập kho phiếu #4', '2026-03-21 11:33:30', '2026-03-21 11:33:30'),
(7, NULL, 1, 73, 6, 'export', 2, 100000.00, 'Xuất kho cho đơn hàng #1', '2026-03-21 13:47:47', '2026-03-27 16:15:51'),
(8, NULL, 2, 73, 6, 'export', 3, 100000.00, 'Xuất kho cho đơn hàng #2', '2026-03-21 13:48:27', '2026-03-27 16:18:11'),
(9, NULL, 3, 73, 6, 'export', 3, 100000.00, 'Xuất kho cho đơn hàng #3', '2026-03-21 13:59:26', '2026-03-27 16:19:33'),
(10, NULL, 4, 73, 6, 'export', 2, 100000.00, 'Xuất kho cho đơn hàng #4', '2026-03-21 16:54:54', '2026-03-27 16:19:57'),
(11, NULL, 5, 73, 6, 'export', 2, 100000.00, 'Xuất kho cho đơn hàng #5', '2026-03-21 17:00:18', '2026-03-27 19:15:42'),
(12, 5, NULL, 73, 6, 'import', 10, 300000.00, 'Nhập kho phiếu #5', '2026-03-22 08:58:10', '2026-03-22 08:58:10'),
(13, NULL, 6, 73, 6, 'export', 18, 171428.57, 'Xuất kho cho đơn hàng #6', '2026-03-22 08:59:08', '2026-03-27 19:15:28'),
(14, 6, NULL, 73, 6, 'import', 10, 190000.00, 'Nhập kho phiếu #6', '2026-03-22 09:17:40', '2026-03-22 09:17:40'),
(15, NULL, 7, 73, 6, 'export', 20, 180714.29, 'Xuất kho cho đơn hàng #7', '2026-03-22 12:34:19', '2026-03-27 19:15:13'),
(16, 7, NULL, 76, 4, 'import', 245, 230000.00, 'Nhập kho phiếu #7', '2026-03-27 15:36:41', '2026-03-27 15:36:41'),
(17, NULL, 8, 76, 4, 'export', 6, 230000.00, 'Xuất kho cho đơn hàng #8', '2026-03-27 15:37:17', '2026-03-27 19:14:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `note` text DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `shipping_fee` decimal(12,2) DEFAULT 20000.00,
  `total_price` decimal(12,2) NOT NULL,
  `status` enum('processing','processed','shipping','shipped','cancelled') DEFAULT 'processing',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `fullname`, `email`, `phone`, `address`, `note`, `payment_method`, `shipping_fee`, `total_price`, `status`, `created_at`, `updated_at`) VALUES
(1, 9, 'Trần Nhựt Huy', 'trannhuthuy999@gmail.com', '0962713941', '320/30 Trần Bình Trọng, p. Chợ Quán, 70000, Việt Nam', '', 'cod', 50000.00, 610000.00, 'processing', '2026-03-21 13:47:47', '2026-03-21 13:47:47'),
(2, 9, 'Trần Nhựt Huy', 'trannhuthuy999@gmail.com', '0962713941', '320/30 Trần Bình Trọng, p. Chợ Quán, 70000, Việt Nam', '', 'cod', 50000.00, 890000.00, 'processing', '2026-03-21 13:48:27', '2026-03-21 13:48:27'),
(3, 9, 'Trần Nhựt Huy', 'trannhuthuy999@gmail.com', '0962713941', 'Địt cụ mày', 'Không có gì cả', 'cod', 50000.00, 890000.00, 'processing', '2026-03-21 13:59:26', '2026-03-21 13:59:26'),
(4, 3, 'Trần Nhựt Huy', 'trannhuthuy897@gmail.com', '0962713941', '320/30 Trần Bình Trọng', '', 'cod', 50000.00, 610000.00, 'cancelled', '2026-03-21 16:54:54', '2026-03-22 13:35:46'),
(5, 3, 'Trần Nhựt Huy', 'trannhuthuy897@gmail.com', '0962713941', '320/30 Trần Bình Trọng', '', 'cod', 50000.00, 610000.00, 'cancelled', '2026-03-21 17:00:18', '2026-03-21 17:35:59'),
(6, 3, 'Trần Nhựt Huy', 'trannhuthuy897@gmail.com', '0962713941', '320/30 Trần Bình Trọng', '', 'cod', 50000.00, 6764000.00, 'processed', '2026-03-22 08:59:08', '2026-03-22 13:05:01'),
(7, 3, 'Trần Nhựt Huy', 'trannhuthuy897@gmail.com', '0962713941', '320/30 Trần Bình Trọng', '', 'cod', 50000.00, 7750000.00, 'cancelled', '2026-03-22 12:34:19', '2026-03-22 12:56:42'),
(8, 9, 'Trần Nhựt Huy', 'trannhuthuy999@gmail.com', '0962713941', '320/30 Trần Bình Trọng, p. Chợ Quán, 70000, Việt Nam', '', 'cod', 50000.00, 2144000.00, 'shipped', '2026-03-27 15:37:17', '2026-03-27 15:38:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `size_id`, `quantity`, `price`, `updated_at`) VALUES
(1, 1, 73, 6, 2, 280000.00, '2026-03-21 13:47:47'),
(2, 2, 73, 6, 3, 280000.00, '2026-03-21 13:48:27'),
(3, 3, 73, 6, 3, 280000.00, '2026-03-21 13:59:26'),
(4, 4, 73, 6, 2, 280000.00, '2026-03-21 16:54:54'),
(5, 5, 73, 6, 2, 280000.00, '2026-03-21 17:00:18'),
(6, 6, 73, 6, 18, 373000.00, '2026-03-22 08:59:08'),
(7, 7, 73, 6, 20, 385000.00, '2026-03-22 12:34:19'),
(8, 8, 76, 4, 6, 349000.00, '2026-03-27 15:37:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `base_img` text NOT NULL,
  `description` text DEFAULT NULL,
  `tutorial` text DEFAULT NULL,
  `profit_rate` decimal(5,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `base_img`, `description`, `tutorial`, `profit_rate`, `status`, `created_at`) VALUES
(1, 1, 'Sen đá nâu', 'senda1.png', 'Sen đá nâu sở hữu tông màu trầm ấm đặc trưng, với những chiếc lá dày mọng nước xếp tầng đều đặn như đài hoa. Cây có khả năng chịu hạn tốt, ít cần tưới nước và rất dễ chăm sóc ngay cả với người mới bắt đầu. Kích thước nhỏ gọn, phù hợp đặt trên bàn làm việc, kệ sách hoặc cửa sổ đón nắng nhẹ. Cây phát triển tốt ở điều kiện ánh sáng tự nhiên từ 4–6 tiếng mỗi ngày.', NULL, 5.00, 'active', '2026-01-29 07:08:50'),
(2, 1, 'Sen đá kim cương', 'senda2.png', 'Sen đá kim cương nổi bật với lớp lá óng ánh như phủ sương bạc, tạo hiệu ứng lấp lánh khi ánh sáng chiếu vào. Dáng cây xếp tầng hài hòa, cân đối, màu sắc chuyển đổi từ xanh xám sang tím nhạt tùy vào lượng ánh sáng nhận được. Rất thích hợp trang trí không gian hiện đại, văn phòng hoặc làm quà tặng. Cây dễ nhân giống bằng lá và cực kỳ bền bỉ.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(3, 1, 'Sen đá móng rồng', 'senda3.png', 'Sen đá móng rồng sở hữu những chiếc lá nhọn, cứng cáp như móng vuốt, tạo nên vẻ ngoài mạnh mẽ và cá tính. Màu lá xanh đậm pha tím, đặc biệt nổi bật ở phần đầu lá. Cây chịu hạn tốt, không cần tưới nhiều, chỉ cần ánh sáng vừa phải. Phù hợp cho người bận rộn hoặc mới bắt đầu chơi cây cảnh. Cây còn mang ý nghĩa phong thủy về sức mạnh và bảo vệ.', NULL, 10.00, 'active', '2026-01-29 07:08:50'),
(4, 1, 'Sen đá chuỗi ngọc', 'senda4.png', 'Chuỗi ngọc là loại sen đá đặc biệt với những chiếc lá tròn xanh mướt mọc thành dây rủ xuống nhẹ nhàng như chuỗi ngọc trai. Cây phát triển tốt khi được treo cao hoặc đặt trên kệ để dây lá tự do buông rủ. Rất phù hợp trang trí ban công, cửa sổ hoặc góc phòng. Cần tưới nước ít, chú ý không để ngập úng. Đây là một trong những loài sen đá được ưa chuộng nhất hiện nay.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(5, 1, 'Sen đá viền hồng', 'senda5.png', 'Sen đá viền hồng thu hút ánh nhìn ngay lập tức nhờ mép lá hồng nhạt tinh tế, tạo sự tương phản hài hòa với thân lá xanh. Khi được tiếp xúc đủ nắng, màu hồng ở viền lá càng trở nên rực rỡ và đẹp hơn — đây là đặc điểm độc đáo mà không phải cây nào cũng có. Kích thước vừa phải, phù hợp đặt cạnh cửa sổ hoặc ban công. Cây rất dễ chăm sóc và thích hợp làm quà tặng ý nghĩa.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(6, 1, 'Sen đá phật bà', 'senda6.png', 'Sen đá phật bà có dáng xếp tầng đều đặn, hoàn hảo như đài hoa sen, mang vẻ đẹp thanh thoát và trang nghiêm. Cây được đặt tên theo hình dáng gợi nhớ đến đài sen trong tranh phật giáo. Ngoài vẻ đẹp thẩm mỹ, cây còn mang ý nghĩa bình an, may mắn và trường thọ theo quan niệm phong thủy. Chăm sóc đơn giản, chỉ cần ánh sáng tự nhiên và tưới nước đúng lịch.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(7, 1, 'Sen đá đô la', 'senda7.png', 'Sen đá đô la có những chiếc lá tròn nhỏ như đồng xu, xếp chồng lên nhau gọn gàng và xinh xắn. Theo quan niệm dân gian, hình dáng lá như đồng tiền tượng trưng cho tài lộc và sự thịnh vượng, rất phù hợp đặt trong nhà hoặc văn phòng làm vật phong thủy. Cây nhỏ gọn, dễ trồng và là lựa chọn quà tặng ý nghĩa dịp khai trương, tân gia.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(8, 1, 'Sen đá tím', 'senda8.png', 'Sen đá tím sở hữu màu sắc tím nhẹ nhàng, huyền bí và sang trọng hiếm thấy trong thế giới cây cảnh. Màu tím đặc trưng đến từ sắc tố anthocyanin trong lá, phát triển mạnh hơn khi cây nhận đủ ánh sáng. Cây rất phù hợp trang trí bàn học, góc làm việc hoặc kệ sách, tạo điểm nhấn tinh tế. Kết hợp với chậu màu trắng hoặc gốm, vẻ đẹp của cây càng được tôn lên.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(9, 1, 'Sen đá lá tim', 'senda9.png', 'Sen đá lá tim là loài cây đặc biệt với những chiếc lá hình trái tim độc đáo và dễ thương, mang ý nghĩa yêu thương và gắn kết. Đây là lựa chọn hoàn hảo để làm quà tặng cho người thân, bạn bè hoặc người yêu dịp Valentine, sinh nhật hay kỷ niệm. Cây nhỏ xinh, dễ chăm sóc, chịu được điều kiện thiếu sáng nhẹ và không cần tưới nhiều.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(10, 1, 'Sen đá thạch bích', 'senda10.png', 'Sen đá thạch bích nổi bật với màu xanh tươi mát quanh năm, lá dày và khỏe mạnh. Đây là loài sen đá có sức sống bền bỉ nhất, thích nghi tốt với nhiều điều kiện ánh sáng khác nhau từ trong nhà đến ngoài trời. Cây rất ít sâu bệnh, không cần phân bón thường xuyên. Phù hợp cho người mới chơi cây hoặc những ai thường xuyên đi công tác, ít có thời gian chăm sóc.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(11, 1, 'Xương rồng tròn', 'cactus1.png', 'Xương rồng tròn có hình cầu đều đặn, nhỏ gọn và dễ thương như một viên bi xanh. Bề mặt cây phủ đầy gai mềm mịn, an toàn khi chạm vào. Cây phát triển chậm, duy trì hình dáng tròn đẹp trong nhiều năm mà không cần tỉa tạo hình. Rất phù hợp đặt bàn làm việc, kệ sách hay windowsill. Là loài cây tiêu biểu cho triết lý \"ít chăm sóc, vẫn đẹp\".', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(12, 1, 'Xương rồng tai thỏ', 'cactus2.png', 'Xương rồng tai thỏ có hai nhánh dẹt mọc song song trông giống đôi tai thỏ đáng yêu, tạo nên vẻ ngoài vừa độc đáo vừa hài hước. Bề mặt phủ lông mịn màu trắng thay vì gai nhọn, rất an toàn và thú vị khi chạm vào. Cây chịu hạn tốt, cần ít nước và ánh sáng mạnh. Là lựa chọn trang trí sáng tạo, đặc biệt được trẻ em yêu thích.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(13, 1, 'Xương rồng sao', 'cactus3.png', 'Xương rồng sao có dáng hình sao với các múi nổi bật và đều nhau, tạo nên vẻ đẹp đối xứng hoàn hảo như một tác phẩm hình học tự nhiên. Cây khỏe mạnh, ít sâu bệnh và rất lâu năm. Đôi khi nở hoa nhỏ màu vàng hoặc đỏ ở đỉnh, tạo thêm điểm nhấn đặc biệt. Phù hợp trưng bày trong nhà hoặc văn phòng, kết hợp tốt với các loài xương rồng khác trong vườn mini.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(14, 1, 'Xương rồng bánh sinh nhật', 'cactus4.png', 'Xương rồng bánh sinh nhật có hình dáng tròn nhiều tầng xếp chồng lên nhau như chiếc bánh sinh nhật nhiều lớp, vô cùng độc đáo và bắt mắt. Đây là loài xương rồng hiếm gặp, thường được dùng làm quà tặng sinh nhật ý nghĩa và sáng tạo. Cây phát triển chậm, duy trì hình dạng đẹp lâu dài. Cần ánh sáng đầy đủ và tưới nước rất ít.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(15, 1, 'Xương rồng kim', 'cactus5.png', 'Xương rồng kim phủ đầy gai mảnh dài và đều nhau như những chiếc kim bạc óng ánh, tạo vẻ đẹp mạnh mẽ và ấn tượng. Dưới ánh đèn hoặc ánh mặt trời, những chiếc gai tạo hiệu ứng phản chiếu lung linh rất đẹp. Cây có thể đạt chiều cao đáng kể theo năm tháng, trở thành điểm nhấn nổi bật trong không gian. Chịu hạn tốt, ít cần chăm sóc.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(16, 1, 'Xương rồng trụ', 'cactus6.png', 'Xương rồng trụ có dáng thẳng đứng khỏe khoắn, vươn cao như một cột xanh kiêu hãnh. Cây phát triển nhanh và có thể đạt chiều cao từ 1–2 mét trong điều kiện ngoài trời. Rất thích hợp trang trí ban công, sân thượng hoặc cổng vào. Mang phong cách desert-chic hiện đại, được nhiều designer nội thất ưa chuộng. Chịu nắng tốt, cần tưới nước rất ít.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(17, 1, 'Xương rồng móc câu', 'cactus7.png', 'Xương rồng móc câu nổi bật với những chiếc gai cong như lưỡi câu, vừa tạo vẻ đẹp độc đáo vừa là cơ chế bảo vệ hiệu quả của cây. Màu sắc gai từ vàng đến nâu đỏ, tương phản đẹp với thân cây xanh. Dáng cây tròn hoặc trụ thấp, rất phù hợp đặt trong chậu gốm nhỏ. Là loài cây thu hút sự chú ý và thường là tâm điểm trong bộ sưu tập xương rồng.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(18, 1, 'Xương rồng tai mèo', 'cactus8.png', 'Xương rồng tai mèo có hình dáng mềm mại, các nhánh dẹt phủ lông trắng mịn trông như đôi tai mèo đáng yêu. Không có gai nhọn nên hoàn toàn an toàn, đặc biệt phù hợp cho gia đình có trẻ nhỏ hoặc thú cưng. Cây phát triển thành bụi nhỏ xinh, rất thích hợp trồng kết hợp nhiều cây trong một chậu lớn tạo vườn mini độc đáo.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(19, 1, 'Xương rồng vàng', 'cactus9.png', 'Xương rồng vàng nổi bật với sắc vàng óng ả từ gai dày phủ khắp thân, tạo hiệu ứng rực rỡ và ấm áp cho không gian. Đây là một trong những loài xương rồng đẹp nhất về màu sắc, thường được dùng làm điểm nhấn trong thiết kế cảnh quan. Cây cần ánh sáng mạnh để duy trì màu vàng đặc trưng. Kết hợp với chậu đen hoặc trắng, vẻ đẹp của cây càng thêm sang trọng.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(20, 1, 'Xương rồng đỏ', 'cactus10.png', 'Xương rồng đỏ là loài xương rồng được ghép màu đặc biệt, sở hữu màu đỏ rực rỡ do thiếu chất diệp lục. Màu đỏ nổi bật tạo điểm nhấn mạnh mẽ và cá tính trong bất kỳ không gian nào. Cây cần được ghép lên gốc xương rồng xanh để phát triển, tạo nên sự kết hợp màu sắc độc đáo. Phù hợp cho người yêu thích cây cảnh lạ và muốn tạo điểm nhấn khác biệt.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(21, 1, 'Sen đá mini mix', 'senda11.png', 'Combo sen đá mini mix bao gồm nhiều loài sen đá miniature đa dạng màu sắc và hình dáng được chọn lọc kỹ càng, tạo nên một vườn mini thu nhỏ đầy sắc màu. Mỗi chậu là một bức tranh sống động với các tông màu xanh, tím, hồng, cam hòa quyện. Rất phù hợp trang trí bàn làm việc, kệ sách hay làm quà tặng độc đáo cho người yêu cây cảnh.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(22, 1, 'Cactus mini mix', 'cactus11.png', 'Combo cactus mini mix gồm nhiều loài xương rồng tí hon đa dạng hình dáng được sắp xếp trong một chậu hoặc nhiều chậu nhỏ. Mỗi cây là một tác phẩm nghệ thuật tự nhiên riêng biệt với hình dáng và màu gai khác nhau. Bộ combo tạo nên góc trang trí vô cùng độc đáo và thú vị. Rất dễ chăm sóc, không cần tưới thường xuyên, phù hợp văn phòng hoặc phòng ngủ.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(23, 1, 'Combo sen đá 3 chậu', 'combo1.png', 'Bộ 3 chậu sen đá được chọn lọc và phối hợp màu sắc hài hòa, tạo nên tổng thể trang trí hoàn chỉnh và đẹp mắt. Ba cây có kích thước, hình dáng và màu sắc bổ sung cho nhau, khi đặt cạnh nhau tạo ra hiệu ứng thẩm mỹ tốt hơn so với từng cây riêng lẻ. Đây là lựa chọn lý tưởng để trang trí kệ sách, cửa sổ hoặc làm quà tặng housewarming.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(24, 1, 'Combo cactus', 'combo2.png', 'Combo 3 chậu cactus gồm các loài xương rồng đa dạng hình dáng được chọn lọc để tạo sự tương phản và bổ sung cho nhau về mặt thẩm mỹ. Bộ combo mang phong cách desert garden thu nhỏ, rất được ưa chuộng trong thiết kế nội thất Scandinavian và industrial. Tất cả đều dễ chăm sóc, chịu hạn tốt và có thể tồn tại lâu dài ngay cả với người bận rộn.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(25, 2, 'Kim tiền', 'noithat1.png', 'Cây kim tiền là một trong những loài cây phong thủy phổ biến nhất, với lá tròn bóng xanh mướt trông như đồng tiền vàng. Cây tượng trưng cho tài lộc, thịnh vượng và may mắn, rất được ưa chuộng đặt tại phòng khách, văn phòng hoặc quầy lễ tân. Ngoài ý nghĩa phong thủy, cây còn có khả năng lọc không khí hiệu quả. Chịu được bóng râm một phần, không cần nhiều ánh sáng trực tiếp.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(26, 2, 'Lưỡi hổ', 'noithat2.png', 'Cây lưỡi hổ nổi tiếng với khả năng lọc không khí vượt trội, được NASA liệt kê trong danh sách cây lọc không khí hiệu quả nhất. Lá dài thẳng đứng với viền vàng đặc trưng tạo dáng hiện đại, phù hợp mọi phong cách nội thất. Cây cực kỳ dễ trồng, chịu được bóng tối và rất ít cần tưới nước — thậm chí có thể sống sót nếu bị bỏ quên vài tuần. Là lựa chọn hàng đầu cho người mới bắt đầu chơi cây cảnh.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(27, 2, 'Trầu bà xanh', 'noithat3.png', 'Trầu bà xanh là loài cây leo mạnh mẽ với lá tim xanh bóng, có thể leo trên giàn hoặc để buông rủ tự nhiên tạo hiệu ứng thác lá xanh mướt. Cây sinh trưởng nhanh, dễ nhân giống bằng cách cắt cành cắm nước. Chịu bóng tốt, thích hợp đặt trong nhà hoặc treo trên cao. Cây còn có khả năng lọc khí formaldehyde và các độc tố không khí trong nhà.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(28, 2, 'Trầu bà vàng', 'noithat4.png', 'Trầu bà vàng là biến thể màu vàng của trầu bà xanh, với lá pha màu vàng chanh tươi sáng tạo điểm nhấn rực rỡ cho không gian. Màu vàng đặc trưng xuất hiện đẹp nhất khi cây được tiếp xúc ánh sáng gián tiếp vừa phải. Cây dễ chăm sóc, sinh trưởng nhanh và có thể trang trí theo nhiều cách: treo giỏ, leo giàn hoặc để trong chậu bình thường.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(29, 2, 'Cau tiểu trâm', 'noithat5.png', 'Cau tiểu trâm hay còn gọi là cọ trúc, là loài cây nội thất thanh lịch với nhiều thân mảnh mọc thẳng và tán lá xòe rộng như cây cau thu nhỏ. Cây có khả năng lọc không khí tốt, đặc biệt hiệu quả với các chất độc như benzene và trichloroethylene. Phù hợp đặt góc phòng khách hoặc hành lang. Ưa bóng mát, không cần nhiều ánh sáng mặt trời trực tiếp.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(30, 2, 'Vạn niên thanh', 'noithat6.png', 'Vạn niên thanh là cây cảnh truyền thống được người Việt yêu thích từ lâu đời, với lá xanh bóng bền bỉ quanh năm không phai màu. Tên gọi \"vạn niên\" ý chỉ sức sống lâu bền và trường tồn. Cây rất dễ chăm sóc, chịu bóng tốt và không cần tưới nhiều. Theo phong thủy, cây mang lại sự bền vững, trường thọ và bình an cho gia đình. Thích hợp đặt phòng khách, phòng ngủ hoặc nơi làm việc.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(31, 2, 'Ngọc ngân', 'noithat7.png', 'Ngọc ngân hay còn gọi là cây bạc hà trắng, có lá pha màu trắng bạc đặc trưng tạo vẻ đẹp sang trọng và tinh tế khác biệt. Sự kết hợp giữa xanh và trắng trên cùng một chiếc lá tạo nên hiệu ứng thị giác rất đẹp, phù hợp với phong cách nội thất tối giản hay Scandinavian. Cây chịu bóng vừa phải, cần độ ẩm cao hơn các loài cây khác, nên đặt xa điều hòa.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(32, 2, 'Thiết mộc lan', 'noithat8.png', 'Thiết mộc lan là loài cây phong thủy được đánh giá cao với lá xanh bóng dài hẹp mọc thẳng đứng mạnh mẽ. Theo phong thủy, cây mang ý nghĩa chính trực, bền vững và phú quý. Đặc biệt phù hợp đặt tại cửa vào, phòng khách hoặc văn phòng. Cây rất khỏe mạnh, ít sâu bệnh, chịu hạn tốt và có thể tồn tại trong điều kiện ánh sáng yếu. Đây là loài cây lý tưởng cho người bận rộn.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(33, 2, 'Bàng Singapore', 'noithat9.png', 'Bàng Singapore hay bàng Ấn Độ là loài cây có lá lớn xanh đậm bóng, mọc xòe rộng tạo tán dày và đẹp. Cây tạo cảm giác mát mẻ và gần gũi thiên nhiên, rất phù hợp đặt góc phòng khách lớn hoặc sảnh đón tiếp. Lá cây thay đổi màu sắc theo mùa, từ xanh đậm sang đỏ cam rất đẹp. Cần tưới nước đều đặn và ánh sáng tốt để cây phát triển khỏe mạnh.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(34, 2, 'Hồng môn', 'noithat10.png', 'Hồng môn là loài cây ra hoa quanh năm với những bông hoa đỏ rực rỡ hình trái tim bóng loáng, mang vẻ đẹp sang trọng và lãng mạn. Hoa kéo dài rất lâu, đôi khi đến vài tháng mới tàn. Cây mang ý nghĩa may mắn, tình yêu và thịnh vượng. Rất phù hợp trang trí phòng khách, bàn tiếp khách hoặc làm quà tặng trong các dịp đặc biệt như khai trương, hội nghị.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(35, 2, 'Lan ý', 'noithat11.png', 'Lan ý hay hoa hòa bình là loài cây lọc không khí hàng đầu, nổi tiếng với khả năng hấp thụ các độc tố như ammoniac, formaldehyde và benzene. Cây ra hoa trắng tinh khiết quanh năm, tạo vẻ đẹp thanh tao và dịu dàng. Chịu bóng tốt, có thể đặt trong phòng ít ánh sáng. Rất phù hợp phòng ngủ vì cây còn giúp tăng độ ẩm không khí và cải thiện chất lượng giấc ngủ.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(36, 2, 'Phú quý', 'noithat12.png', 'Cây phú quý hay còn gọi là cây dracaena, sở hữu lá dài đỏ tím nổi bật trên nền xanh đậm, tạo hiệu ứng màu sắc rất ấn tượng. Tên gọi \"phú quý\" phản ánh ý nghĩa phong thủy: mang lại giàu sang, thịnh vượng và danh vọng. Cây dễ chăm sóc, chịu bóng tốt và ít sâu bệnh. Rất được ưa chuộng trong văn phòng, khách sạn và các không gian thương mại.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(37, 2, 'Cây thường xuân', 'noithat13.png', 'Thường xuân là loài cây leo với lá xanh bóng hình ngôi sao năm cánh rất đặc trưng. Cây có thể leo trên tường, treo giỏ hoặc để buông rủ tự nhiên tạo hiệu ứng thác lá xanh mướt trang trí rất đẹp. Cây chịu bóng tốt, thích hợp đặt trong nhà. Ngoài ra, thường xuân còn có khả năng lọc không khí và kháng khuẩn tự nhiên, cải thiện môi trường sống.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(38, 2, 'Đa búp đỏ', 'noithat14.png', 'Đa búp đỏ là loài cây nội thất sang trọng với những chiếc búp lá đỏ tươi nổi bật trên nền lá xanh đậm. Khi búp lá non mở ra, màu đỏ phai dần chuyển sang xanh, tạo hiệu ứng màu sắc thú vị và sống động theo mùa. Cây dễ chăm sóc, phát triển tốt trong điều kiện ánh sáng vừa phải. Là lựa chọn trang trí cao cấp cho phòng khách hoặc không gian tiếp khách.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(39, 2, 'Cọ cảnh', 'noithat15.png', 'Cọ cảnh mang đến cảm giác nhiệt đới và exotic cho không gian nội thất với tán lá rộng xòe ra như những chiếc quạt xanh mướt. Cây tạo không khí mát mẻ và thư giãn, gợi nhớ đến những khu nghỉ dưỡng nhiệt đới sang trọng. Phù hợp đặt góc phòng khách rộng, sảnh khách sạn hoặc spa. Cần ánh sáng tốt và tưới nước đều đặn để tán lá phát triển đẹp.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(40, 2, 'Tùng bồng lai', 'noithat16.png', 'Tùng bồng lai là loài cây bonsai phong thủy nổi tiếng với dáng cây thanh tao, lá kim nhỏ mịn màu xanh đậm quanh năm. Cây mang ý nghĩa trường thọ, bình an và sức mạnh tinh thần trong văn hóa phương Đông. Rất thích hợp đặt trên bàn làm việc, kệ tivi hoặc bàn trà để tạo không gian zen tĩnh lặng. Cần tưới nước vừa phải và ánh sáng gián tiếp.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(41, 2, 'Trúc phú quý', 'noithat17.png', 'Trúc phú quý hay lucky bamboo là loài cây phong thủy cực kỳ phổ biến trên toàn thế giới. Theo phong thủy, số lượng thân trúc mang ý nghĩa khác nhau: 3 thân cho hạnh phúc, 5 thân cho sức khỏe, 8 thân cho thịnh vượng. Cây có thể trồng trong nước hoặc đất, rất dễ chăm sóc và bền bỉ. Là quà tặng ý nghĩa cho dịp khai trương, tân gia hay Tết Nguyên Đán.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(42, 2, 'Ngũ gia bì', 'noithat18.png', 'Ngũ gia bì là loài cây có lá xẻ thùy đặc trưng, mọc xanh tốt quanh năm với sức sống mạnh mẽ. Cây được biết đến với nhiều công dụng trong y học cổ truyền như giảm đau, chống viêm. Trong trang trí, cây tạo cảm giác tự nhiên và gần gũi thiên nhiên. Chịu được bóng mát tốt, phù hợp đặt trong nhà. Dễ chăm sóc và ít sâu bệnh.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(43, 2, 'Cây phát tài', 'noithat19.png', 'Cây phát tài hay money tree là biểu tượng của sự giàu có và thịnh vượng trong văn hóa châu Á. Thân cây thường được tết bện hoặc xoắn lại tạo hình đặc trưng, rất bắt mắt và độc đáo. Lá xanh tươi hình bàn tay 5 ngón mở ra như đang đón nhận may mắn. Cây rất dễ chăm sóc, chịu bóng tốt. Là món quà tặng cao cấp và ý nghĩa dịp khai trương, tân gia, sinh nhật.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(44, 2, 'Cây cau vàng', 'noithat20.png', 'Cây cau vàng hay areca palm là loài cây nội thất phổ biến nhất thế giới, nổi tiếng với khả năng lọc không khí và tạo độ ẩm tự nhiên vượt trội. Lá cây mảnh dài như lá cau, màu xanh vàng tươi sáng tạo điểm nhấn rực rỡ cho không gian. Rất phù hợp đặt góc phòng khách, hành lang hoặc văn phòng. Cần ánh sáng tốt và tưới nước đều đặn.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(45, 2, 'Cây monstera', 'noithat21.png', 'Monstera deliciosa hay \"Swiss cheese plant\" là biểu tượng của xu hướng trang trí nội thất hiện đại với lá xẻ thùy to bản độc đáo. Mỗi chiếc lá trưởng thành có thể đạt đường kính 60–90cm với các lỗ xẻ tự nhiên tạo hình hoa văn độc đáo. Cây phát triển nhanh và ấn tượng, tạo điểm nhấn mạnh mẽ trong không gian rộng. Chịu bóng tốt, tưới nước vừa phải.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(46, 2, 'Cây dương xỉ', 'noithat22.png', 'Dương xỉ là loài cây cổ đại với lịch sử hàng triệu năm, sở hữu những tán lá xẻ thùy mảnh mai xanh mướt tạo vẻ đẹp mềm mại và tự nhiên. Cây ưa độ ẩm cao và bóng mát, rất phù hợp đặt phòng tắm, nhà bếp hoặc những góc ít ánh sáng. Dương xỉ còn có khả năng lọc không khí và tăng độ ẩm phòng. Cần tưới nước đều và phun sương thường xuyên.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(47, 2, 'Cây dây nhện', 'noithat23.png', 'Cây dây nhện hay spider plant là loài cây siêu dễ trồng với lá dài sọc trắng xanh đặc trưng và những cây con nhỏ lơ lửng trên dây như nhện con. Cây lọc không khí tốt, đặc biệt hiệu quả với carbon monoxide. Cực kỳ dễ nhân giống — chỉ cần tách cây con cắm vào đất. Chịu được nhiều điều kiện sáng tối khác nhau. Là loài cây lý tưởng để bắt đầu chơi cây cảnh.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(48, 2, 'Cây bạch mã hoàng tử', 'noithat24.png', 'Bạch mã hoàng tử hay white bird of paradise là loài cây nội thất sang trọng bậc nhất với lá to bản bóng loáng màu xanh đậm, gân lá trắng nổi bật. Cây có dáng vươn cao uy nghi, tạo cảm giác không gian xanh sang trọng đẳng cấp. Rất được ưa chuộng trong các biệt thự, khách sạn 5 sao và showroom cao cấp. Cần ánh sáng tốt và tưới nước đều đặn.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(49, 3, 'Hoa giấy', 'ngoai1.png', 'Hoa giấy là loài cây leo nổi tiếng với những chùm hoa đủ màu sắc rực rỡ nở quanh năm, được trồng phổ biến nhất tại các vùng nhiệt đới. Thực ra phần màu sắc của hoa giấy là lá bắc, không phải cánh hoa thật, nhưng tạo hiệu ứng thị giác vô cùng ấn tượng. Cây leo nhanh, có thể phủ kín giàn, tường hoặc hàng rào. Rất phù hợp trang trí ban công, cổng vào và sân vườn.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(50, 3, 'Cau vua', 'ngoai2.png', 'Cau vua là loài cây biểu tượng của vùng nhiệt đới, vươn thẳng cao lớn với tán lá dài xòe rộng tạo bóng mát tuyệt vời cho sân vườn. Cây có thể đạt chiều cao 20–30 mét khi trưởng thành, tạo cảnh quan hoành tráng và ấn tượng. Rất được ưa chuộng trồng dọc hai bên lối đi trong các khu resort, biệt thự và công viên. Chịu nắng tốt, phù hợp khí hậu nhiệt đới.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(51, 3, 'Sứ thái', 'ngoai3.png', 'Sứ thái hay plumeria là loài hoa đặc trưng của xứ nhiệt đới với những bông hoa 5 cánh thơm ngát, màu sắc phong phú từ trắng, vàng, hồng đến đỏ. Hoa sứ mang hương thơm dịu nhẹ đặc trưng, thường được dùng làm hoa trang trí và chưng bàn thờ. Cây ưa nắng, chịu hạn tốt và ra hoa quanh năm trong điều kiện nhiệt đới. Rất phù hợp trồng sân vườn hoặc ban công rộng.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(52, 3, 'Tùng la hán', 'ngoai4.png', 'Tùng la hán là loài cây bonsai quý hiếm và được đánh giá cao nhất trong nghệ thuật bonsai Việt Nam. Lá kim nhỏ xếp dày, dáng cây cổ kính uy nghiêm như một vị la hán. Cây phát triển chậm nhưng sống rất lâu, có thể tồn tại hàng trăm năm. Mang ý nghĩa phong thủy về sự trường thọ, trí tuệ và quyền uy. Là lựa chọn đầu tư giá trị cho người yêu bonsai nghiêm túc.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(53, 3, 'Lộc vừng', 'ngoai5.png', 'Lộc vừng là loài cây bonsai đặc trưng của vùng Đông Nam Á với những chùm hoa đỏ thắm rủ dài tuyệt đẹp nở vào mùa hè. Hoa có hương thơm nhẹ nhàng và thu hút nhiều loài chim và bướm. Cây có thể trồng trong chậu bonsai hoặc ngoài vườn. Lộc vừng mang ý nghĩa phong thủy tốt, tượng trưng cho sự sung túc và thịnh vượng. Cần chăm sóc kỹ để cây ra hoa đẹp.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(54, 3, 'Mai chiếu thủy', 'ngoai6.png', 'Mai chiếu thủy là loài cây bonsai được yêu thích với những bông hoa trắng nhỏ thơm dịu nở rộ, tạo nên cảnh sắc tinh tế và thanh tao. Tên gọi gợi liên tưởng đến những bông hoa trắng soi bóng xuống mặt nước phẳng lặng. Cây có thể tạo dáng bonsai đẹp với thân cây uốn lượn tự nhiên. Rất phù hợp đặt sân vườn, ban công hoặc trưng bày trong nhà nơi có ánh sáng tốt.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(55, 3, 'San hô đỏ', 'ngoai7.png', 'San hô đỏ hay coral plant là loài cây ngoại thất đặc biệt với những bông hoa đỏ tươi hình ống dài mọc thành chùm, trông giống nhánh san hô biển. Hoa thu hút nhiều loài chim và côn trùng thụ phấn. Cây phát triển nhanh, ưa nắng và chịu nhiệt tốt trong điều kiện khí hậu nhiệt đới. Tạo điểm nhấn màu sắc nổi bật cho sân vườn hoặc hàng rào.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(56, 3, 'Dừa cảnh', 'ngoai8.png', 'Dừa cảnh là phiên bản thu nhỏ của cây dừa, phù hợp trồng trong chậu lớn hoặc sân vườn nhỏ. Cây tạo không gian nhiệt đới đặc trưng với thân cây đặc trưng và tán lá xòe rộng. Rất được ưa chuộng trong thiết kế cảnh quan resort, nhà hàng hải sản và không gian có chủ đề nhiệt đới. Chịu nắng tốt, cần tưới nước đều đặn và phân bón định kỳ để cây khỏe mạnh.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(57, 3, 'Tre cảnh', 'ngoai9.png', 'Tre cảnh mang vẻ đẹp tự nhiên, thanh lịch và đặc trưng của văn hóa châu Á. Thân tre thẳng, lóng đều, lá xanh mướt tạo tiếng xào xạc nhẹ khi có gió — âm thanh thư giãn tuyệt vời. Cây phát triển nhanh và tạo màn xanh che chắn hiệu quả. Rất phù hợp trồng hàng rào sống, tạo không gian privacy hoặc làm backdrop sân vườn kiểu Nhật, zen garden.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(58, 3, 'Hoa mười giờ', 'ngoai10.png', 'Hoa mười giờ hay portulaca là loài hoa cực kỳ dễ trồng với những bông hoa nhỏ xinh đủ màu sắc — đỏ, vàng, cam, hồng, trắng — nở rực rỡ từ sáng đến trưa. Cây rất chịu nắng và chịu hạn, phù hợp trồng ở ban công, sân thượng hoặc dọc lối đi. Dễ nhân giống từ hạt hoặc cành. Là lựa chọn hoàn hảo để tạo thảm hoa màu sắc sống động với chi phí thấp.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(59, 3, 'Hoa hồng leo', 'ngoai11.png', 'Hoa hồng leo là \"nữ hoàng\" của sân vườn với những bông hoa đẹp, thơm ngát và cành dài có thể leo phủ kín giàn, tường hay cổng vào. Hoa nở nhiều đợt trong năm, mỗi đợt kéo dài 2–3 tuần. Nhiều giống hoa hồng leo có hương thơm đặc trưng rất dễ chịu. Cần cắt tỉa định kỳ và bón phân đều đặn để cây ra hoa nhiều. Là biểu tượng của tình yêu và vẻ đẹp vĩnh cửu.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(60, 3, 'Hoa lan vũ nữ', 'ngoai12.png', 'Lan vũ nữ hay Oncidium là loài lan nổi tiếng với những chùm hoa nhỏ vàng rực rỡ xếp dày đặc trên cành dài, trông như những vũ nữ trong trang phục vàng đang múa. Hoa có hương thơm nhẹ và bền, kéo dài nhiều tuần. Cây cho hoa nhiều lần trong năm nếu được chăm sóc tốt. Phù hợp treo trên giàn hoặc đặt trong chậu trồng vỏ cây. Là loài lan phổ biến và dễ chăm sóc nhất.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(61, 3, 'Cây khế cảnh', 'ngoai13.png', 'Khế cảnh bonsai là loài cây ăn quả được thu nhỏ thành tác phẩm nghệ thuật sống, vừa đẹp vừa thực dụng khi vẫn ra quả nhỏ đặc trưng. Quả khế vàng lúc lỉu trên cành bonsai tạo nên vẻ đẹp phong thủy đặc sắc, tượng trưng cho sự sung túc và no đủ. Cây cần ánh sáng đầy đủ và chăm sóc kỹ lưỡng để đạt dáng bonsai đẹp. Rất được ưa chuộng trong giới chơi bonsai.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(62, 3, 'Cây me bonsai', 'ngoai14.png', 'Me bonsai là loài cây bonsai có dáng thế cổ kính, uy nghiêm với thân cây sần sùi đặc trưng và tán lá nhỏ mịn rủ xuống nhẹ nhàng. Cây me bonsai già có thể có giá trị rất cao vì mang dáng cổ thụ tự nhiên khó tạo được. Lá me nhỏ tạo bóng mát dày, thích hợp trưng bày ngoài trời hoặc sân vườn. Cây chịu nắng tốt và khá dễ chăm sóc khi đã quen điều kiện.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(63, 3, 'Cây sung cảnh', 'ngoai15.png', 'Sung cảnh bonsai mang ý nghĩa sung túc, đầy đủ và thịnh vượng — rất được ưa chuộng trong văn hóa người Việt. Cây có quả nhỏ mọc trực tiếp trên thân và cành, tạo hình ảnh cây trĩu quả rất đẹp mắt theo quan niệm phong thủy. Thân cây sần sùi, gốc rễ nổi tạo dáng bonsai tự nhiên và độc đáo. Rất phù hợp đặt sân vườn hoặc ban công rộng có ánh sáng tốt.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(64, 3, 'Cây si bonsai', 'ngoai16.png', 'Si bonsai là loài cây được các nghệ nhân bonsai Việt Nam đặc biệt yêu thích vì khả năng tạo dáng thế phong phú với rễ phụ buông xuống tạo hình rừng rậm thu nhỏ. Cây si cổ thụ với rễ lớn nổi trên mặt đất là hình ảnh đặc trưng của làng quê Việt. Cây dễ tạo hình, sinh trưởng tốt trong nhiều điều kiện khác nhau. Là loài bonsai phù hợp cho người mới bắt đầu.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(65, 3, 'Cây duối', 'ngoai17.png', 'Duối bonsai là loài cây bonsai truyền thống được trồng phổ biến ở các đình chùa, di tích lịch sử Việt Nam từ hàng trăm năm trước. Cây có thân gỗ cứng, vỏ sần sùi đặc trưng, lá nhỏ dày tạo dáng cổ thụ uy nghiêm. Cây duối già có giá trị nghệ thuật và kinh tế cao. Chịu nắng tốt, ít cần chăm sóc. Là di sản văn hóa cây xanh của người Việt Nam.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(66, 3, 'Cây bàng Đài Loan', 'ngoai18.png', 'Bàng Đài Loan hay Terminalia mantaly là loài cây bóng mát cao lớn với tán xòe đều đặn như dù xanh. Cây có thể đạt chiều cao 10–15 mét, tạo bóng mát rộng và đẹp cho sân vườn, đường phố hoặc công viên. Lá thay theo mùa với màu sắc đỏ cam đẹp mắt trước khi rụng. Rất phổ biến trong cảnh quan đô thị. Cây dễ trồng và phát triển nhanh trong điều kiện nhiệt đới.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(67, 3, 'Cây tùng thơm', 'ngoai20.png', 'Tùng thơm là loài cây cảnh quý với lá kim nhỏ tỏa hương thơm dịu đặc trưng khi được chạm vào hoặc khi có gió. Hương thơm của tùng có tác dụng thư giãn, giảm stress và xua đuổi côn trùng. Cây có dáng đẹp tự nhiên, phù hợp trồng trong chậu hoặc tạo hình bonsai. Rất được ưa chuộng đặt trước cửa nhà, văn phòng hoặc trong không gian thư giãn như spa, thiền đường.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(68, 3, 'Cây nguyệt quế', 'ngoai21.png', 'Nguyệt quế là loài cây thường xanh với những bông hoa trắng nhỏ thơm ngát và lá bóng xanh đậm đẹp quanh năm. Hoa nguyệt quế có hương thơm đặc trưng, dịu nhẹ và thanh cao, được sử dụng trong nước hoa và mỹ phẩm cao cấp. Theo phong thủy, nguyệt quế mang lại danh vọng, thành công và học hành đỗ đạt. Rất phù hợp trồng trước cửa nhà hoặc trong sân vườn.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(69, 3, 'Cây hoa dâm bụt', 'ngoai24.png', 'Hoa dâm bụt là loài cây bụi đặc trưng của vùng nhiệt đới với những bông hoa lớn đỏ rực nở quanh năm. Mỗi bông hoa chỉ nở một ngày nhưng cây ra hoa liên tục không ngừng. Ngoài vẻ đẹp trang trí, hoa dâm bụt còn được dùng làm trà thảo mộc giàu vitamin C và anthocyanin có lợi cho sức khỏe. Rất chịu nắng và nóng, phù hợp khí hậu miền Nam Việt Nam.', NULL, 30.00, 'active', '2026-01-29 07:08:50'),
(70, 4, 'Chậu đất nung', 'pot1.png', 'Chậu đất nung truyền thống được làm từ đất sét tự nhiên nung ở nhiệt độ cao, tạo nên độ bền vượt thời gian và vẻ đẹp mộc mạc đặc trưng. Chất liệu đất nung có khả năng thoát khí và ẩm tốt, rất có lợi cho sự phát triển của rễ cây — đặc biệt phù hợp với cây xương rồng, sen đá và bonsai. Màu cam đất ấm áp hòa hợp tự nhiên với mọi phong cách trang trí.', NULL, 30.00, 'active', '2026-02-19 02:00:03'),
(71, 4, 'Chậu lan đất nung', 'pot2.png', 'Chậu lan đất nung được thiết kế chuyên biệt cho việc trồng các loài lan với hệ thống lỗ thoát nước và thông khí đặc biệt xung quanh thân chậu. Thiết kế này giúp rễ lan thở tốt, tránh úng nước và thối rễ — vấn đề phổ biến nhất khi trồng lan. Chậu được làm thủ công từ đất sét tự nhiên, bền đẹp và thân thiện môi trường. Phù hợp với mọi loài lan: hồ điệp, dendrobium, cattleya.', NULL, 30.00, 'active', '2026-02-19 02:00:03'),
(72, 4, 'Chậu đất nung tròn', 'pot3.png', 'Chậu đất nung tròn cổ điển là lựa chọn hoàn hảo cho việc trồng bonsai mini và cây cảnh nhỏ. Hình dạng tròn cân đối giúp cây phát triển đều về mọi phía. Kích thước vừa phải, phù hợp đặt trên bàn, kệ hoặc cửa sổ. Chất liệu đất nung tự nhiên thoát ẩm tốt, giữ nhiệt ổn định cho rễ cây. Thiết kế đơn giản nhưng tinh tế, hài hòa với mọi phong cách trang trí.', NULL, 30.00, 'active', '2026-02-19 02:00:03'),
(73, 4, 'Chậu đất nung trụ', 'pot4.png', 'Chậu đất nung trụ với thiết kế cao và thẳng đứng, phù hợp cho các loài cây cao như lưỡi hổ, cây trụ, và các cây có bộ rễ ăn sâu. Chiều cao của chậu tạo không gian đủ cho rễ phát triển tự do. Chất liệu đất nung chắc chắn, bền bỉ theo năm tháng. Phong cách thiết kế đơn giản, mộc mạc nhưng sang trọng, phù hợp với nội thất công nghiệp hoặc tối giản.', NULL, 30.00, 'active', '2026-02-19 02:00:03'),
(74, 4, 'Chậu nhựa to', 'pot5.png', 'Chậu nhựa to là lựa chọn thực dụng và kinh tế nhất cho việc trồng cây cảnh lớn ngoài sân vườn hoặc ban công. Chất liệu nhựa cao cấp bền bỉ với thời tiết, không bị nứt vỡ hay phai màu. Trọng lượng nhẹ giúp dễ dàng di chuyển và sắp xếp lại theo ý muốn. Có lỗ thoát nước ở đáy, tránh ngập úng. Phù hợp trồng cây to như monstera, cau, dừa cảnh.', NULL, 30.00, 'active', '2026-02-19 02:00:03'),
(75, 4, 'Chậu nhựa mềm', 'pot6.png', 'Chậu nhựa mềm hay túi trồng cây là giải pháp trồng cây hiện đại và tiết kiệm, đặc biệt phổ biến trong nông nghiệp đô thị và vườn ban công. Chất liệu nhựa mềm dẻo cho phép rễ cây phát triển tự nhiên và dễ dàng tháo ra khi cần thay chậu. Nhẹ, dễ xếp gọn khi không dùng. Có nhiều kích cỡ từ nhỏ đến lớn. Phù hợp trồng rau, hoa và cây ăn quả.', NULL, 30.00, 'active', '2026-02-19 02:00:03'),
(76, 4, 'Chậu nhựa dài', 'pot7.png', 'Chậu nhựa dài hay chậu trồng hàng là thiết kế thông minh cho phép trồng nhiều cây liên tiếp trong một chậu duy nhất. Rất phù hợp trồng hàng rào cây xanh, trồng rau thơm hay tạo viền xanh dọc ban công và lan can. Chất liệu nhựa bền bỉ, nhẹ và dễ di chuyển. Thiết kế có nhiều lỗ thoát nước đảm bảo không bị úng. Là giải pháp tiết kiệm không gian thông minh cho nhà phố và căn hộ.', NULL, 30.00, 'active', '2026-02-19 02:00:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_img`
--

CREATE TABLE `product_img` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `size`
--

CREATE TABLE `size` (
  `id` int(11) NOT NULL,
  `size_name` varchar(10) NOT NULL,
  `price_adjust` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `size`
--

INSERT INTO `size` (`id`, `size_name`, `price_adjust`) VALUES
(3, 'S', 1000.00),
(4, 'M', 50000.00),
(5, 'L', 100000.00),
(6, 'XL', 150000.00),
(7, 'XXL', 200000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `status` enum('active','warning','inactive') DEFAULT 'active',
  `role` enum('customer','admin') DEFAULT 'customer',
  `avatar` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `address`, `phone`, `email`, `status`, `role`, `avatar`, `reset_token`, `reset_expires`, `created_at`, `updated_at`) VALUES
(3, 'Huy', '$2y$10$GdhRzu1ninJkZt9EH3nu4eXycNn21lKUG3HkMliaQTL5B.8xeIG.G', 'Trần Nhựt Huy', '320/30 Trần Bình Trọng', '0962713941', 'trannhuthuy897@gmail.com', 'active', 'customer', '1774062730_thumb-1920-1011626.png', NULL, NULL, '2026-03-19 21:00:43', '2026-03-22 13:13:08'),
(9, 'Huyz', '$2y$10$m6IUEwTMNtQ0FMKsRgR8X.AeI2sZTJ5WRS8VcgTTEe86BGaKhj1Yy', 'Trần Nhựt Huy', '320/30 Trần Bình Trọng, p. Chợ Quán, 70000, Việt Nam', '0962713941', 'trannhuthuy999@gmail.com', 'active', 'admin', '1774027661_sú.jpg', NULL, NULL, '2026-03-19 21:00:43', '2026-03-21 00:27:42'),
(11, 'Huydâsd', '$2y$10$lj/bfhdHjeCy9MBBKhMt8eNG.dViq/fqkZCNS9427J.BmoHP3SzR6', 'Trần Nhựt Huy', '232', '0962713941', 'trannhuthuy666@gmail.com', 'warning', 'customer', NULL, NULL, NULL, '2026-03-21 10:10:38', '2026-03-27 13:39:03'),
(12, 'Hảoo', '$2y$10$p4mZyAm11hYLF8YynEzobOGUvS.22z9XYn80MNHU29pVKK9ffvDWq', 'Hảo', 'dasdasdasdasdasd', '0393067863', 'imobile.stg@gmail.com', 'active', 'customer', NULL, NULL, NULL, '2026-03-27 15:35:16', '2026-03-27 15:35:16');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `import_receipts`
--
ALTER TABLE `import_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Chỉ mục cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Chỉ mục cho bảng `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_id` (`receipt_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`),
  ADD KEY `fk_inventory_order` (`order_id`);

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
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `product_img`
--
ALTER TABLE `product_img`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `size`
--
ALTER TABLE `size`
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
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `import_receipts`
--
ALTER TABLE `import_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT cho bảng `product_img`
--
ALTER TABLE `product_img`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `size`
--
ALTER TABLE `size`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_3` FOREIGN KEY (`size_id`) REFERENCES `size` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `import_receipts`
--
ALTER TABLE `import_receipts`
  ADD CONSTRAINT `import_receipts_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `size` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `fk_inventory_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `import_receipts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_logs_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_logs_ibfk_3` FOREIGN KEY (`size_id`) REFERENCES `size` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`size_id`) REFERENCES `size` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `product_img`
--
ALTER TABLE `product_img`
  ADD CONSTRAINT `product_img_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
