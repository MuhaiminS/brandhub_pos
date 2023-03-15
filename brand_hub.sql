-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2023 at 07:40 PM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `brand_hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `combo_package`
--

CREATE TABLE `combo_package` (
  `id` int(11) NOT NULL,
  `package_name` varchar(225) DEFAULT NULL,
  `package_price` varchar(225) DEFAULT NULL,
  `package_items` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `credit_sale`
--

CREATE TABLE `credit_sale` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(250) NOT NULL,
  `number` varchar(50) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `paid_date` datetime NOT NULL,
  `sale_order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_details`
--

CREATE TABLE `customer_details` (
  `customer_id` int(11) NOT NULL,
  `customer_number` varchar(15) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `customer_address` varchar(225) NOT NULL,
  `customer_email` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customer_details`
--

INSERT INTO `customer_details` (`customer_id`, `customer_number`, `customer_name`, `customer_address`, `customer_email`) VALUES
(1, '88870073539', 'Muhaimin Sheik', 'Chennai India', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `manufacturing_unit_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `qualification` varchar(225) DEFAULT NULL,
  `basic_salary` decimal(9,2) DEFAULT NULL,
  `allowance` decimal(9,2) DEFAULT NULL,
  `country` varchar(250) NOT NULL,
  `state` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `license` varchar(225) DEFAULT NULL,
  `passport` varchar(225) DEFAULT NULL,
  `idproof` varchar(225) DEFAULT NULL,
  `image` varchar(200) NOT NULL,
  `doj` date DEFAULT NULL,
  `is_active` tinyint(1) UNSIGNED DEFAULT '1',
  `date_added` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `name`, `manufacturing_unit_id`, `email`, `phone`, `qualification`, `basic_salary`, `allowance`, `country`, `state`, `city`, `address`, `zip`, `license`, `passport`, `idproof`, `image`, `doj`, `is_active`, `date_added`, `date_updated`) VALUES
(1, 'Staff 1', 0, 'anandbe13@gmail.com', '1234567890', 'B.E', '1000.00', '150.00', 'India', 'Tamil nadu', 'Chennai', 'testing addresss', '607 402', '20170922110501_license.jpg', NULL, NULL, 'd1.png', '2017-09-12', 1, NULL, '2018-07-12 10:10:21'),
(2, 'Staff 2', 0, 'sri@gmail.com', '546789322', 'B.Sc', NULL, NULL, 'india', 't.N', 'chennai', 'p block', 'sdfa', '20170922110501_license.jpg', NULL, NULL, 'd2.jpg', '2017-09-04', 1, '2017-09-22 11:05:01', '2018-01-04 07:10:57');

-- --------------------------------------------------------

--
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `floor_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `floor_name` varchar(200) NOT NULL,
  `floor_no` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `other_name` varchar(255) DEFAULT NULL,
  `cat_id` int(11) NOT NULL,
  `price` double NOT NULL,
  `image` varchar(250) CHARACTER SET utf8 NOT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `barcode_id` varchar(20) NOT NULL,
  `CGST` double DEFAULT NULL,
  `SGST` double DEFAULT NULL,
  `stock` varchar(20) DEFAULT NULL,
  `manuf_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `inward_date` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `other_name`, `cat_id`, `price`, `image`, `weight`, `unit`, `active`, `barcode_id`, `CGST`, `SGST`, `stock`, `manuf_date`, `expiry_date`, `inward_date`) VALUES
(1, 'Allen Solly', NULL, 1, 650, '20230314072518_items.jpg', '', '', '1', '', 0, 0, '994', NULL, NULL, NULL),
(2, 'Narrow Pant', NULL, 2, 1500, '20230314072605_items..jpg', '', '', '1', '', 0, 0, '48', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `item_category`
--

CREATE TABLE `item_category` (
  `id` int(11) NOT NULL,
  `category_title` varchar(200) CHARACTER SET utf8 NOT NULL,
  `category_slug` varchar(200) CHARACTER SET utf8 NOT NULL,
  `category_img` varchar(200) CHARACTER SET utf8 NOT NULL,
  `category_details` text CHARACTER SET utf8 NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item_category`
--

INSERT INTO `item_category` (`id`, `category_title`, `category_slug`, `category_img`, `category_details`, `active`) VALUES
(1, 'Shirts', 'shirts', '', '', '1'),
(2, 'Jeans Pants', 'jeans-pants', '', '', '1'),
(3, 'T Shirts', 't-shirts', '', '', '1');

-- --------------------------------------------------------

--
-- Table structure for table `item_units`
--

CREATE TABLE `item_units` (
  `id` int(11) NOT NULL,
  `unit_name` varchar(225) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item_units`
--

INSERT INTO `item_units` (`id`, `unit_name`) VALUES
(1, 'Kg'),
(2, 'grams');

-- --------------------------------------------------------

--
-- Table structure for table `locations_manufacturing_units`
--

CREATE TABLE `locations_manufacturing_units` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `location` varchar(150) NOT NULL,
  `country` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `locations_manufacturing_units`
--

INSERT INTO `locations_manufacturing_units` (`id`, `name`, `location`, `country`) VALUES
(1, 'yyyyyyyyyyy', '', ''),
(2, 'yyyyyyy', 'jjjjjjjj', 'cccccc');

-- --------------------------------------------------------

--
-- Table structure for table `locations_shops`
--

CREATE TABLE `locations_shops` (
  `id` int(11) NOT NULL,
  `shop_name` varchar(200) NOT NULL,
  `shop_location` varchar(150) NOT NULL,
  `shop_country` varchar(150) NOT NULL,
  `shop_lable` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `locations_shops`
--

INSERT INTO `locations_shops` (`id`, `shop_name`, `shop_location`, `shop_country`, `shop_lable`) VALUES
(1, 'Brand Hub', 'Ramnad', 'IND', 'BH');

-- --------------------------------------------------------

--
-- Table structure for table `opening_cash`
--

CREATE TABLE `opening_cash` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cash_amount` varchar(50) DEFAULT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pay_back`
--

CREATE TABLE `pay_back` (
  `id` int(11) NOT NULL,
  `sale_order_item_id` int(11) NOT NULL,
  `sale_order_id` int(11) NOT NULL,
  `receipt_id` varchar(50) NOT NULL,
  `item_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `payback_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sale_orders`
--

CREATE TABLE `sale_orders` (
  `id` int(11) NOT NULL,
  `receipt_id` varchar(250) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `address` varchar(250) NOT NULL,
  `customer_email` varchar(225) DEFAULT NULL,
  `order_type` enum('counter_sale','delivery','dine_in','take_away','website_order','combo') CHARACTER SET utf8 NOT NULL,
  `payment_type` enum('cash','card','cod','credit') NOT NULL,
  `card_num` int(11) DEFAULT NULL,
  `payment_status` enum('paid','unpaid') NOT NULL,
  `discount` float NOT NULL DEFAULT '0',
  `amount_given` float DEFAULT NULL,
  `balance_amount` float DEFAULT NULL,
  `status` enum('pending','conform','out_for_delivery','delivered','reject') NOT NULL,
  `remarks` text,
  `delivered_in` varchar(100) DEFAULT NULL,
  `reject_reason` text,
  `driver_id` int(11) NOT NULL,
  `ordered_date` datetime NOT NULL,
  `paid_date` datetime NOT NULL,
  `table_id` int(11) DEFAULT NULL,
  `floor_id` int(11) DEFAULT NULL,
  `num_members` int(11) DEFAULT NULL,
  `vat` int(11) DEFAULT NULL,
  `combo_package_name` varchar(150) DEFAULT NULL,
  `combo_package_price` varchar(50) DEFAULT NULL,
  `combo_package_gst` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sale_orders`
--

INSERT INTO `sale_orders` (`id`, `receipt_id`, `user_id`, `shop_id`, `customer_id`, `contact_name`, `contact_number`, `address`, `customer_email`, `order_type`, `payment_type`, `card_num`, `payment_status`, `discount`, `amount_given`, `balance_amount`, `status`, `remarks`, `delivered_in`, `reject_reason`, `driver_id`, `ordered_date`, `paid_date`, `table_id`, `floor_id`, `num_members`, `vat`, `combo_package_name`, `combo_package_price`, `combo_package_gst`) VALUES
(1, 'BH-1', 1, 1, 0, '', '0', '', NULL, 'counter_sale', 'cash', 0, 'paid', 0, 0, 0, 'pending', '', NULL, NULL, 1, '2023-03-14 20:07:43', '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', ''),
(2, 'BH-2', 1, 1, 0, '', '0', '', NULL, 'counter_sale', 'cash', 0, 'paid', 0, 0, 0, 'pending', '', NULL, NULL, 1, '2023-03-14 20:09:50', '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', ''),
(3, 'BH-3', 1, 1, 0, '', '0', '', NULL, 'counter_sale', 'cash', 0, 'paid', 0, 0, 0, 'pending', '', NULL, NULL, 1, '2023-03-14 20:11:32', '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', ''),
(4, 'BH-4', 1, 1, 0, '', '0', '', NULL, 'counter_sale', 'cash', 0, 'paid', 0, 0, 0, 'pending', '', NULL, NULL, 2, '2023-03-14 20:12:33', '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', ''),
(5, 'BH-5', 1, 1, 1, 'Muhaimin Sheik', '88870073539', 'Chennai India', NULL, 'counter_sale', 'cash', 0, 'paid', 0, 0, 0, 'pending', '', NULL, NULL, 1, '2023-03-14 20:13:12', '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `sale_order_items`
--

CREATE TABLE `sale_order_items` (
  `id` int(11) NOT NULL,
  `sale_order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_add_price_id` int(11) NOT NULL,
  `item_name` varchar(250) NOT NULL,
  `price` varchar(50) NOT NULL,
  `tax_without_price` double DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `CGST` double DEFAULT NULL,
  `SGST` double DEFAULT NULL,
  `staff_id` int(11) NOT NULL,
  `date_completed` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sale_order_items`
--

INSERT INTO `sale_order_items` (`id`, `sale_order_id`, `item_id`, `item_add_price_id`, `item_name`, `price`, `tax_without_price`, `qty`, `CGST`, `SGST`, `staff_id`, `date_completed`) VALUES
(1, 1, 1, 0, 'Allen Solly', '650', 650, 1, 0, 0, 0, NULL),
(2, 1, 2, 0, 'Narrow Pant', '1500', 1500, 1, 0, 0, 0, NULL),
(3, 2, 1, 0, 'Allen Solly', '650', 650, 1, 0, 0, 0, NULL),
(4, 3, 2, 0, 'Narrow Pant', '1500', 1500, 1, 0, 0, 0, NULL),
(5, 4, 1, 0, 'Allen Solly', '650', 650, 1, 0, 0, 0, NULL),
(6, 5, 1, 0, 'Allen Solly', '650', 650, 1, 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `set_name` varchar(150) NOT NULL,
  `id` int(11) NOT NULL,
  `set_value` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`set_name`, `id`, `set_value`) VALUES
('CLIENT_NAME', 1, 'Brand Hub'),
('CLIENT_ADDRESS', 2, 'Ramnad, Tamilnadu'),
('CLIENT_NUMBER', 3, '888888888'),
('CLIENT_WEBSITE', 4, ''),
('RECIPT_PRE', 5, 'BH-'),
('CURRENCY', 6, 'â‚¹'),
('BILL_FOOTER', 7, 'Thank you and Visit Again...!'),
('API_KEY', 8, '2sYDrDoDx9z4'),
('OWNER_NUM', 9, ''),
('CLIENT_LOGO', 10, ''),
('BILL_TAX_VAL', 11, '5');

-- --------------------------------------------------------

--
-- Table structure for table `settle_sale`
--

CREATE TABLE `settle_sale` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `cash_at_starting` varchar(50) NOT NULL,
  `cash_sale` varchar(50) NOT NULL,
  `card_sale` varchar(50) NOT NULL,
  `credit_sale` varchar(50) NOT NULL,
  `delivery_sale` varchar(50) NOT NULL,
  `delivery_recover` varchar(50) DEFAULT NULL,
  `online_order_recovery` varchar(50) DEFAULT NULL,
  `credit_recover` varchar(50) NOT NULL,
  `cg_advance` varchar(50) NOT NULL,
  `cg_recover` varchar(50) NOT NULL,
  `gross_total` varchar(50) NOT NULL,
  `discount` varchar(50) NOT NULL,
  `total_vat` varchar(50) DEFAULT NULL,
  `total_cgst` varchar(50) DEFAULT NULL,
  `total_sgst` varchar(50) DEFAULT NULL,
  `total_gst` varchar(50) DEFAULT NULL,
  `net_total` varchar(50) NOT NULL,
  `cash_drawer` varchar(50) NOT NULL,
  `settle_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settle_sale`
--

INSERT INTO `settle_sale` (`id`, `user_id`, `shop_id`, `cash_at_starting`, `cash_sale`, `card_sale`, `credit_sale`, `delivery_sale`, `delivery_recover`, `online_order_recovery`, `credit_recover`, `cg_advance`, `cg_recover`, `gross_total`, `discount`, `total_vat`, `total_cgst`, `total_sgst`, `total_gst`, `net_total`, `cash_drawer`, `settle_date`) VALUES
(1, 1, 1, '', '5600', '0', '0', '0', NULL, '0', '0', '', '', '5600', '0', '0', '0', '0', '0', '5600', '5600', '2023-03-14 20:16:57');

-- --------------------------------------------------------

--
-- Table structure for table `staff_loans`
--

CREATE TABLE `staff_loans` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `loan_amount` decimal(9,2) DEFAULT NULL,
  `loan_type` enum('credit','debit') DEFAULT NULL,
  `loan_date` date DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `staff_salary`
--

CREATE TABLE `staff_salary` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `basic_salary` decimal(9,2) DEFAULT NULL,
  `allowance` decimal(9,2) DEFAULT NULL,
  `month_year` varchar(225) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `staff_salary`
--

INSERT INTO `staff_salary` (`id`, `staff_id`, `basic_salary`, `allowance`, `month_year`, `date_added`, `date_updated`) VALUES
(1, 1, '1000.00', '150.00', 'Jul-2018', '2018-07-12 10:10:21', '2018-07-12 10:10:21');

-- --------------------------------------------------------

--
-- Table structure for table `stock_management_history`
--

CREATE TABLE `stock_management_history` (
  `history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `action_type` enum('add','sub') NOT NULL DEFAULT 'add',
  `stock_value` double NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `stock_management_history`
--

INSERT INTO `stock_management_history` (`history_id`, `user_id`, `product_id`, `action_type`, `stock_value`, `date_added`) VALUES
(1, 1, 274, 'add', 1, '2018-07-31 05:13:02'),
(2, 1, 274, 'add', 1, '2018-07-31 05:13:02'),
(3, 1, 274, 'add', 1, '2018-07-31 05:13:02'),
(4, 1, 274, 'add', 1, '2018-07-31 05:13:39'),
(5, 1, 274, 'add', 1, '2018-07-31 05:13:49'),
(6, 1, 274, 'add', 2, '2018-07-31 05:14:08'),
(7, 1, 274, 'add', 1, '2018-07-31 05:30:55'),
(8, 1, 274, 'add', 1, '2018-07-31 05:30:55'),
(9, 1, 274, 'add', 1, '2018-07-31 05:49:32'),
(10, 1, 274, 'add', 1, '2018-07-31 05:49:32'),
(11, 1, 274, 'add', 1, '2018-07-31 05:51:11'),
(12, 1, 274, 'add', 1, '2018-07-31 05:51:11'),
(13, 1, 274, 'add', 10, '2018-07-31 05:56:14'),
(14, 1, 274, 'add', 10, '2018-07-31 05:56:14'),
(15, 1, 274, 'add', 10, '2018-07-31 05:56:14'),
(16, 1, 274, 'add', 10, '2018-07-31 05:56:14'),
(17, 1, 274, 'add', 10, '2018-07-31 05:56:14'),
(18, 1, 274, 'add', 10, '2018-07-31 05:56:14');

-- --------------------------------------------------------

--
-- Table structure for table `table_management`
--

CREATE TABLE `table_management` (
  `table_id` int(11) NOT NULL,
  `floor_id` int(11) NOT NULL,
  `table_no` int(11) NOT NULL,
  `no_of_seats` int(11) NOT NULL,
  `filled_seats` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_pass` text NOT NULL,
  `role_id` int(11) NOT NULL,
  `manufacturing_unit_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `email` varchar(250) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `is_active` enum('1','0') NOT NULL DEFAULT '1',
  `status` enum('0','1') DEFAULT '0',
  `user_action` text,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fcm_id` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `user_pass`, `role_id`, `manufacturing_unit_id`, `shop_id`, `first_name`, `last_name`, `email`, `phone`, `is_active`, `status`, `user_action`, `created_at`, `updated_at`, `fcm_id`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 1, 1, 'Admin', '', 'anandbe13@gmail.com', '123456789', '1', '1', 'counter_sale,delivery_sale,dine_in,take_away,reports,settle_sale,cod_log,online_order_log,sale_order_details,cash_back,barcode_print', '2016-03-30 10:13:18', '2016-03-30 10:13:18', '');

-- --------------------------------------------------------

--
-- Table structure for table `users_role`
--

CREATE TABLE `users_role` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) CHARACTER SET utf8 NOT NULL,
  `slug` varchar(25) CHARACTER SET utf8 NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users_role`
--

INSERT INTO `users_role` (`id`, `title`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', '2016-03-29 00:00:00', '2016-03-29 00:00:00'),
(2, 'Cashier', 'Cashier', '0000-00-00 00:00:00', '2017-10-08 09:47:51'),
(3, 'Waiter', 'Waiter', '0000-00-00 00:00:00', '2017-10-08 09:48:06'),
(5, 'Delivery Boy', 'delivery-boy', '0000-00-00 00:00:00', '2017-10-08 09:48:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `combo_package`
--
ALTER TABLE `combo_package`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credit_sale`
--
ALTER TABLE `credit_sale`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `customer_details`
--
ALTER TABLE `customer_details`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`floor_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_category`
--
ALTER TABLE `item_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_units`
--
ALTER TABLE `item_units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations_manufacturing_units`
--
ALTER TABLE `locations_manufacturing_units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations_shops`
--
ALTER TABLE `locations_shops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opening_cash`
--
ALTER TABLE `opening_cash`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pay_back`
--
ALTER TABLE `pay_back`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_orders`
--
ALTER TABLE `sale_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_id` (`receipt_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shop_id` (`shop_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `contact_number` (`contact_number`),
  ADD KEY `order_type` (`order_type`),
  ADD KEY `payment_type` (`payment_type`),
  ADD KEY `status` (`status`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `floor_id` (`floor_id`);

--
-- Indexes for table `sale_order_items`
--
ALTER TABLE `sale_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_order_id` (`sale_order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settle_sale`
--
ALTER TABLE `settle_sale`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_loans`
--
ALTER TABLE `staff_loans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_salary`
--
ALTER TABLE `staff_salary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_management_history`
--
ALTER TABLE `stock_management_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `table_management`
--
ALTER TABLE `table_management`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_role`
--
ALTER TABLE `users_role`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `combo_package`
--
ALTER TABLE `combo_package`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `credit_sale`
--
ALTER TABLE `credit_sale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_details`
--
ALTER TABLE `customer_details`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `floor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `item_category`
--
ALTER TABLE `item_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `item_units`
--
ALTER TABLE `item_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `locations_manufacturing_units`
--
ALTER TABLE `locations_manufacturing_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `locations_shops`
--
ALTER TABLE `locations_shops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `opening_cash`
--
ALTER TABLE `opening_cash`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pay_back`
--
ALTER TABLE `pay_back`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_orders`
--
ALTER TABLE `sale_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sale_order_items`
--
ALTER TABLE `sale_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `settle_sale`
--
ALTER TABLE `settle_sale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff_loans`
--
ALTER TABLE `staff_loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_salary`
--
ALTER TABLE `staff_salary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_management_history`
--
ALTER TABLE `stock_management_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `table_management`
--
ALTER TABLE `table_management`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users_role`
--
ALTER TABLE `users_role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
