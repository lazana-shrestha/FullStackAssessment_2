-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2026 at 07:06 PM
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
-- Database: `product_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 'Laptop', 'High-performance laptop with 16GB RAM', 999.99, 'Electronics', 25, '2026-01-26 17:59:45', '2026-01-26 17:59:45'),
(2, 'Smartphone', 'Latest model with 128GB storage', 699.99, 'Electronics', 50, '2026-01-26 17:59:45', '2026-01-26 17:59:45'),
(3, 'Coffee Maker', 'Automatic coffee machine', 89.99, 'Home Appliances', 30, '2026-01-26 17:59:45', '2026-01-26 17:59:45'),
(4, 'Desk Chair', 'Ergonomic office chair', 199.99, 'Furniture', 15, '2026-01-26 17:59:45', '2026-01-26 17:59:45'),
(5, 'Wireless Headphones', 'Noise-cancelling headphones', 149.99, 'Electronics', 40, '2026-01-26 17:59:45', '2026-01-26 17:59:45'),
(6, 'Bookshelf', '5-tier wooden bookshelf', 129.99, 'Furniture', 10, '2026-01-26 17:59:45', '2026-01-26 17:59:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
