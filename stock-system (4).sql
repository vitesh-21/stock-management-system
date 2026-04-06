-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 11:41 PM
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
-- Database: `stock-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_transactions`
--

CREATE TABLE `mpesa_transactions` (
  `id` int(11) NOT NULL,
  `MerchantRequestID` varchar(50) DEFAULT NULL,
  `CheckoutRequestID` varchar(50) DEFAULT NULL,
  `ResultCode` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `MpesaReceiptNumber` varchar(50) DEFAULT NULL,
  `PhoneNumber` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `checkout_id` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `status` enum('Pending','Completed','Failed') DEFAULT 'Pending',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `min_stock` int(11) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `size`, `quantity`, `price`, `min_stock`) VALUES
(60, 'Leather Jacket', 'Jackets', 'XL', 4, 4500.00, 10),
(61, 'Denim Jacket', 'Jackets', 'L', 3, 4200.00, 10),
(62, 'Bomber Jacket', 'Jackets', 'XL', 15, 4800.00, 10),
(63, 'Winter Coat', 'Jackets', 'XXL', 11, 6500.00, 10),
(64, 'Hoodie', 'Hoodies', 'L', 25, 2500.00, 10),
(65, 'Zip Hoodie', 'Hoodies', 'XL', 35, 2700.00, 10),
(66, 'Pullover Hoodie', 'Hoodies', 'M', 29, 2400.00, 10),
(67, 'Plain T-Shirt', 'T-Shirts', 'L', 79, 900.00, 10),
(68, 'Graphic T-Shirt', 'T-Shirts', 'M', 69, 1100.00, 10),
(69, 'Oversized T-Shirt', 'T-Shirts', 'XL', 37, 1300.00, 10),
(70, 'Polo Shirt', 'Shirts', 'L', 45, 1500.00, 10),
(71, 'Formal Shirt', 'Shirts', 'M', 35, 2200.00, 10),
(72, 'Casual Shirt', 'Shirts', 'XL', 40, 1800.00, 10),
(73, 'Flannel Shirt', 'Shirts', 'L', 25, 2000.00, 10),
(74, 'Jeans', 'Trousers', '32', 58, 3200.00, 10),
(75, 'Slim Fit Jeans', 'Trousers', '34', 55, 3500.00, 10),
(76, 'Skinny Jeans', 'Trousers', '30', 50, 3000.00, 10),
(77, 'Cargo Pants', 'Trousers', '36', 35, 3400.00, 10),
(78, 'Chinos', 'Trousers', '34', 40, 3100.00, 10),
(79, 'Track Pants', 'Sportswear', 'L', 44, 2200.00, 10),
(80, 'Joggers', 'Sportswear', 'M', 50, 2000.00, 10),
(81, 'Gym Shorts', 'Sportswear', 'L', 55, 1500.00, 10),
(82, 'Basketball Shorts', 'Sportswear', 'XL', 40, 1700.00, 10),
(83, 'Sweatpants', 'Sportswear', 'M', 38, 2300.00, 10),
(84, 'Sneakers', 'Footwear', '42', 18, 5200.00, 10),
(85, 'Running Shoes', 'Footwear', '41', 25, 5400.00, 10),
(86, 'Canvas Shoes', 'Footwear', '40', 35, 3000.00, 10),
(87, 'Leather Boots', 'Footwear', '43', 20, 6500.00, 10),
(88, 'Sandals', 'Footwear', '40', 45, 1800.00, 10),
(89, 'Slippers', 'Footwear', '39', 50, 900.00, 10),
(90, 'Baseball Cap', 'Accessories', 'Free', 60, 700.00, 10),
(91, 'Beanie', 'Accessories', 'Free', 40, 800.00, 10),
(92, 'Bucket Hat', 'Accessories', 'Free', 35, 1000.00, 10),
(93, 'Scarf', 'Accessories', 'Free', 50, 600.00, 10),
(94, 'Leather Belt', 'Accessories', 'L', 30, 1500.00, 10),
(95, 'Fabric Belt', 'Accessories', 'M', 25, 1200.00, 10),
(96, 'Socks Pack', 'Accessories', 'Free', 100, 500.00, 10),
(97, 'Sports Socks', 'Accessories', 'Free', 90, 600.00, 10),
(98, 'Tie', 'Accessories', 'Free', 30, 1200.00, 10),
(99, 'Bow Tie', 'Accessories', 'Free', 20, 1000.00, 10),
(100, 'Blazer', 'Formal Wear', 'XL', 15, 5200.00, 10),
(101, 'Suit Jacket', 'Formal Wear', 'L', 10, 6500.00, 10),
(102, 'Formal Trousers', 'Formal Wear', '34', 20, 3500.00, 10),
(103, 'Waistcoat', 'Formal Wear', 'M', 18, 2800.00, 10),
(104, 'Three Piece Suit', 'Formal Wear', 'XL', 8, 12000.00, 10),
(105, 'Denim Shorts', 'Shorts', '32', 40, 1900.00, 10),
(106, 'Chino Shorts', 'Shorts', '34', 35, 2000.00, 10),
(107, 'Cargo Shorts', 'Shorts', '36', 30, 2100.00, 10),
(108, 'Beach Shorts', 'Shorts', 'L', 45, 1500.00, 10),
(109, 'Running Shorts', 'Shorts', 'M', 50, 1400.00, 10),
(110, 'Training Jersey', 'Sportswear', 'L', 30, 1700.00, 10),
(111, 'Football Jersey', 'Sportswear', 'XL', 28, 2000.00, 10),
(112, 'Basketball Jersey', 'Sportswear', 'L', 25, 2100.00, 10),
(113, 'Track Jacket', 'Sportswear', 'XL', 20, 3000.00, 10),
(114, 'Sports Hoodie', 'Sportswear', 'L', 18, 2800.00, 10);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `quantity_sold` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` enum('Cash','M-Pesa','Card') DEFAULT 'Cash',
  `mpesa_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `product_id`, `quantity`, `quantity_sold`, `total_amount`, `sale_date`, `payment_method`, `mpesa_code`) VALUES
(1, 60, NULL, 12, 54000.00, '2026-03-11 12:09:53', 'Cash', NULL),
(2, 61, NULL, 15, 63000.00, '2026-03-11 12:10:49', 'M-Pesa', NULL),
(3, 69, NULL, 13, 16900.00, '2026-03-11 12:14:22', 'M-Pesa', NULL),
(4, 60, NULL, 2, 9000.00, '2026-03-11 12:20:42', 'M-Pesa', NULL),
(5, 61, NULL, 1, 4200.00, '2026-03-11 12:22:58', 'M-Pesa', NULL),
(6, 67, 1, NULL, 900.00, '2026-03-11 12:53:49', 'M-Pesa', NULL),
(7, 62, 1, NULL, 4800.00, '2026-03-11 13:04:35', 'M-Pesa', NULL),
(8, 61, 1, NULL, 4200.00, '2026-03-11 13:05:06', 'M-Pesa', NULL),
(9, 62, 1, NULL, 4800.00, '2026-03-11 13:09:45', 'M-Pesa', NULL),
(10, 62, 1, NULL, 4800.00, '2026-03-11 13:09:49', 'M-Pesa', NULL),
(11, 66, 1, NULL, 2400.00, '2026-03-11 13:12:27', 'M-Pesa', NULL),
(12, 74, 1, NULL, 3200.00, '2026-03-11 13:16:53', 'M-Pesa', NULL),
(13, 63, 1, NULL, 6500.00, '2026-03-11 13:20:00', 'M-Pesa', NULL),
(14, 63, 1, NULL, 6500.00, '2026-03-11 13:20:36', 'M-Pesa', NULL),
(15, 63, 1, NULL, 6500.00, '2026-03-11 13:20:36', 'M-Pesa', NULL),
(16, 68, 1, NULL, 1100.00, '2026-03-11 13:21:27', 'M-Pesa', NULL),
(17, 63, 1, NULL, 6500.00, '2026-03-11 13:22:09', 'M-Pesa', NULL),
(18, 60, 1, NULL, 4500.00, '2026-03-11 13:30:22', 'M-Pesa', NULL),
(19, 60, 1, NULL, 4500.00, '2026-03-11 13:30:33', 'M-Pesa', NULL),
(20, 60, 1, NULL, 4500.00, '2026-03-11 13:30:56', 'M-Pesa', NULL),
(21, 74, 1, NULL, 3200.00, '2026-03-11 13:37:10', 'M-Pesa', NULL),
(22, 79, 1, NULL, 2200.00, '2026-03-11 13:37:31', 'Cash', NULL),
(23, 84, 12, NULL, 62400.00, '2026-03-11 13:37:51', '', NULL),
(24, 60, 3, NULL, 13500.00, '2026-03-12 09:56:37', 'Cash', NULL),
(25, 64, 5, NULL, 12500.00, '2026-03-12 13:20:30', 'M-Pesa', NULL),
(26, 64, 5, NULL, 12500.00, '2026-03-12 13:20:44', 'M-Pesa', NULL),
(27, 64, 5, NULL, 12500.00, '2026-03-12 13:20:54', 'M-Pesa', NULL),
(28, 60, 1, NULL, 4500.00, '2026-03-17 19:58:13', 'Cash', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `security_question` varchar(255) DEFAULT NULL,
  `security_answer` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `security_question`, `security_answer`, `role`) VALUES
(1, 'admin', '$2y$10$wUUY0ZXAYB.MjC7/VwVPOuasK9jm4GUmLd89sz.w4bgOehAsk.w82', NULL, NULL, 'admin'),
(2, 'staff1', '$2y$10$Kv73noarOFPMnJjiGqcurudB9Y/TFcrTOA3TPJ4WyS9snjV5m1OG.', NULL, NULL, 'staff'),
(3, 'abcc', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'staff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mpesa_transactions`
--
ALTER TABLE `mpesa_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mpesa_transactions`
--
ALTER TABLE `mpesa_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
