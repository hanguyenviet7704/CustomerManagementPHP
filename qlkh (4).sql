-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th6 25, 2025 lúc 12:48 PM
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
-- Cơ sở dữ liệu: `qlkh`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `phone_number` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`customer_id`, `first_name`, `last_name`, `email`, `phone_number`, `date_of_birth`, `address`, `registration_date`) VALUES
(2, 'Lan', 'Trần', 'tran.lan@gmail.com', '0909876543', '1990-05-22', '456 Nguyễn Huệ, Quận 1, TP.HCM', '2025-05-11 14:44:28'),
(3, 'Hải', 'Phạm', 'pham.hai@yahoo.com', '0988776655', '1982-07-30', '789 Trường Chinh, Tân Bình, TP.HCM', '2025-05-11 14:44:28'),
(4, 'Hương', 'Vũ', 'vu.huong@gmail.com', '0977665544', '1995-11-02', '321 Cách Mạng Tháng 8, Quận 3, TP.HCM', '2025-05-11 14:44:28'),
(5, 'Tuấn', 'Lê', 'le.tuan@gmail.com', '0966554433', '1980-04-10', '654 Điện Biên Phủ, Bình Thạnh, TP.HCM', '2025-05-11 14:44:28'),
(6, 'Mai', 'Đỗ', 'do.mai@gmail.com', '0955443322', '1988-08-25', '987 Lý Thường Kiệt, Quận 10, TP.HCM', '2025-05-11 14:44:28'),
(7, 'Nam', 'Hoàng', 'hoang.nam@gmail.com', '0944332211', '1979-01-16', '135 Võ Thị Sáu, Quận 3, TP.HCM', '2025-05-11 14:44:28'),
(8, 'Anh', 'Ngô', 'ngo.anh@gmail.com', '0933221100', '1992-12-05', '246 Hai Bà Trưng, Quận 1, TP.HCM', '2025-05-11 14:44:28'),
(9, 'Quang', 'Đinh', 'dinh.quang@gmail.com', '0922110099', '1986-03-19', '357 Pasteur, Quận 3, TP.HCM', '2025-05-11 14:44:28'),
(10, 'Linh', 'Bùi', 'bui.linh@gmail.com', '0911009988', '1994-09-23', '468 Nguyễn Trãi, Quận 5, TP.HCM', '2025-05-11 14:44:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_reviews`
--

CREATE TABLE `customer_reviews` (
  `review_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review_text` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customer_reviews`
--

INSERT INTO `customer_reviews` (`review_id`, `customer_id`, `product_name`, `rating`, `review_text`, `review_date`) VALUES
(2, 2, 'Điện thoại thông minh', 5, 'Hiệu năng tốt, mượt mà.', '2025-05-11 14:44:28'),
(3, 3, 'Tai nghe', 3, 'Âm thanh ổn, đeo lâu hơi khó chịu.', '2025-05-11 14:44:28'),
(4, 4, 'Máy tính bảng', 4, 'Xem phim rất tốt, hình ảnh đẹp.', '2025-05-11 14:44:28'),
(5, 5, 'Đồng hồ thông minh', 5, 'Tiện lợi cho theo dõi sức khỏe.', '2025-05-11 14:44:28'),
(6, 6, 'Bàn phím', 2, 'Phím bấm cứng, gõ không mượt.', '2025-05-11 14:44:28'),
(7, 7, 'Chuột máy tính', 5, 'Phù hợp cho chơi game.', '2025-05-11 14:44:28'),
(8, 8, 'Màn hình máy tính', 4, 'Màu sắc đẹp nhưng chân đế không điều chỉnh được.', '2025-05-11 14:44:28'),
(9, 9, 'Cục sạc', 5, 'Sạc nhanh, dùng ổn định.', '2025-05-11 14:44:28'),
(10, 10, 'Loa Bluetooth', 4, 'Âm thanh tốt nhưng không quá lớn.', '2025-05-11 14:44:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `discounts`
--

CREATE TABLE `discounts` (
  `discount_id` int(11) NOT NULL,
  `discount_code` varchar(255) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `discount_type` varchar(50) NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(11) DEFAULT NULL,
  `promotion_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `discounts`
--

INSERT INTO `discounts` (`discount_id`, `discount_code`, `discount_amount`, `discount_type`, `start_date`, `end_date`, `customer_id`, `promotion_id`) VALUES
(2, 'MUAHE20', 20.00, 'percentage', '2025-05-31 17:00:00', '2025-06-29 17:00:00', 2, 2),
(4, 'TRUONG10', 10.00, 'percentage', '2025-08-14 17:00:00', '2025-08-19 17:00:00', 4, 4),
(5, 'CYBER25', 25.00, 'percentage', '2025-11-29 17:00:00', '2025-12-01 17:00:00', 5, 5),
(6, 'CHOPNHOANG30', 30.00, 'percentage', '2025-05-09 17:00:00', '2025-05-10 17:00:00', 6, 6),
(7, 'TETMUA1TANG1', 1.00, 'amount', '2024-12-31 17:00:00', '2025-01-04 17:00:00', 7, 7),
(8, 'LEHOIXUAN15', 15.00, 'percentage', '2025-03-31 17:00:00', '2025-04-14 17:00:00', 8, 8),
(9, 'VALENTINE20', 20.00, 'percentage', '2025-02-13 17:00:00', '2025-02-14 17:00:00', 9, 9),
(10, 'PHUCSINH10', 10.00, 'percentage', '2025-03-31 17:00:00', '2025-04-03 17:00:00', 10, 10);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `total_amount`, `status`) VALUES
(2, 2, '2025-05-11 14:44:28', 220.50, 'Đang giao hàng'),
(3, 3, '2025-05-11 14:44:28', 100.75, 'Đã nhận'),
(4, 4, '2025-05-11 14:44:28', 180.30, 'Đang xử lý'),
(5, 5, '2025-05-11 14:44:28', 300.00, 'Đã nhận'),
(6, 6, '2025-05-11 14:44:28', 250.25, 'Đã hủy'),
(7, 7, '2025-05-11 14:44:28', 400.00, 'Đã giao'),
(8, 8, '2025-05-11 14:44:28', 175.50, 'Đang xử lý'),
(9, 9, '2025-05-11 14:44:28', 350.75, 'Đã giao'),
(10, 10, '2025-05-11 14:44:28', 120.00, 'Đã nhận');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_name`, `quantity`, `price`) VALUES
(3, 2, 'Điện thoại thông minh', 2, 400.00),
(4, 3, 'Tai nghe', 1, 100.75),
(5, 4, 'Máy tính bảng', 1, 200.00),
(6, 5, 'Đồng hồ thông minh', 2, 150.00),
(7, 6, 'Bàn phím', 1, 50.25),
(8, 7, 'Chuột máy tính', 2, 25.00),
(9, 8, 'Màn hình máy tính', 1, 175.50),
(10, 9, 'Cục sạc', 3, 30.25);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_date`, `payment_method`, `payment_amount`, `payment_status`) VALUES
(2, 2, '2025-05-11 14:44:28', 'Ví điện tử', 220.50, 'Đã thanh toán'),
(3, 3, '2025-05-11 14:44:28', 'Thẻ ghi nợ', 100.75, 'Đã thanh toán'),
(4, 4, '2025-05-11 14:44:28', 'Thẻ tín dụng', 180.30, 'Chờ xử lý'),
(5, 5, '2025-05-11 14:44:28', 'Chuyển khoản', 300.00, 'Đã thanh toán'),
(6, 6, '2025-05-11 14:44:28', 'Ví điện tử', 250.25, 'Thất bại'),
(7, 7, '2025-05-11 14:44:28', 'Thẻ tín dụng', 400.00, 'Đã thanh toán'),
(8, 8, '2025-05-11 14:44:28', 'Thẻ ghi nợ', 175.50, 'Chờ xử lý'),
(9, 9, '2025-05-11 14:44:28', 'Ví điện tử', 350.75, 'Đã thanh toán'),
(10, 10, '2025-05-11 14:44:28', 'Thẻ tín dụng', 120.00, 'Đã thanh toán');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

CREATE TABLE `promotions` (
  `promotion_id` int(11) NOT NULL,
  `promotion_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `start_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `end_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'active',
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`promotion_id`, `promotion_name`, `description`, `start_date`, `end_date`, `status`, `customer_id`) VALUES
(2, 'Mùa hè sôi động', 'Giảm 20% cho quần áo mùa hè', '2025-05-31 17:00:00', '2025-06-29 17:00:00', 'đang diễn ra', 2),
(4, 'Vào năm học mới', 'Giảm 10% dụng cụ học tập', '2025-08-14 17:00:00', '2025-08-19 17:00:00', 'đã hết hạn', 4),
(5, 'Cyber Monday', 'Giảm 25% máy tính & phụ kiện', '2025-11-29 17:00:00', '2025-12-01 17:00:00', 'đang diễn ra', 5),
(6, 'Giảm giá chớp nhoáng', 'Giảm 30% sản phẩm chọn lọc', '2025-05-09 17:00:00', '2025-05-10 17:00:00', 'đã hết hạn', 6),
(7, 'Tết Dương Lịch', 'Mua 1 tặng 1 tất cả sản phẩm thời trang', '2024-12-31 17:00:00', '2025-01-04 17:00:00', 'đang diễn ra', 7),
(8, 'Lễ hội mùa xuân', 'Giảm 15% toàn bộ sản phẩm', '2025-03-31 17:00:00', '2025-04-14 17:00:00', 'đang diễn ra', 8),
(9, 'Valentine', 'Giảm 20% các mặt hàng quà tặng', '2025-02-13 17:00:00', '2025-02-14 17:00:00', 'đang diễn ra', 9),
(10, 'Ưu đãi Phục Sinh', 'Giảm 10% đồ gia dụng', '2025-03-31 17:00:00', '2025-04-03 17:00:00', 'đã hết hạn', 10);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `customer_reviews`
--
ALTER TABLE `customer_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`discount_id`),
  ADD UNIQUE KEY `discount_code` (`discount_code`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `promotion_id` (`promotion_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promotion_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `customer_reviews`
--
ALTER TABLE `customer_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `discounts`
--
ALTER TABLE `discounts`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `promotions`
--
ALTER TABLE `promotions`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `customer_reviews`
--
ALTER TABLE `customer_reviews`
  ADD CONSTRAINT `customer_reviews_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Các ràng buộc cho bảng `discounts`
--
ALTER TABLE `discounts`
  ADD CONSTRAINT `discounts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discounts_ibfk_2` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`promotion_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Các ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Các ràng buộc cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD CONSTRAINT `promotions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
