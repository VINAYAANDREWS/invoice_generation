-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 06, 2021 at 12:25 AM
-- Server version: 5.7.33-0ubuntu0.16.04.1
-- PHP Version: 7.0.33-29+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test_invoice`
--

-- --------------------------------------------------------

--
-- Table structure for table `test_items`
--

CREATE TABLE `test_items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL COMMENT 'in $'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `test_items`
--

INSERT INTO `test_items` (`id`, `item_name`, `price`) VALUES
(1, 'Item 1', '100'),
(2, 'Item 2', '50'),
(3, 'Item 3', '100'),
(4, 'Item 4', '50');

-- --------------------------------------------------------

--
-- Table structure for table `test_users`
--

CREATE TABLE `test_users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `test_users`
--

INSERT INTO `test_users` (`id`, `name`, `email`, `address`, `phone`, `username`, `password`, `user_type`) VALUES
(1, 'admin', '', '', '', 'admin', '$2y$10$09SWclzH2hEVS/RCD4/nJOsPBm19OC4haml8IlHlWR17Y3T1tn3u6', 'admin'),
(2, 'Customer 1', 'c1@gmail.com', 'c1 address', '1234567890', '', '', 'customer'),
(3, 'c2', 'c2@gmail.com', 'address2', '345123145456', '', '', 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `test_user_bill`
--

CREATE TABLE `test_user_bill` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bill_number` varchar(255) NOT NULL,
  `sub_total` double NOT NULL,
  `tax_percentage` float NOT NULL,
  `tax_amount` double NOT NULL,
  `subtotal_with_tax` double NOT NULL,
  `discount_percentage` double NOT NULL,
  `discount_amount` double NOT NULL,
  `total_discount` double NOT NULL,
  `final_amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `test_items`
--
ALTER TABLE `test_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_users`
--
ALTER TABLE `test_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_user_bill`
--
ALTER TABLE `test_user_bill`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `bill_number` (`bill_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `test_items`
--
ALTER TABLE `test_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `test_users`
--
ALTER TABLE `test_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `test_user_bill`
--
ALTER TABLE `test_user_bill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
