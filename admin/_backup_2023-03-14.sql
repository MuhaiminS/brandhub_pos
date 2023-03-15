

CREATE TABLE `combo_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_name` varchar(225) DEFAULT NULL,
  `package_price` varchar(225) DEFAULT NULL,
  `package_items` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




CREATE TABLE `credit_sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(250) NOT NULL,
  `number` varchar(50) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `paid_date` datetime NOT NULL,
  `sale_order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `user_id` (`user_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;




CREATE TABLE `customer_details` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_number` varchar(15) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `customer_address` varchar(225) NOT NULL,
  `customer_email` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO customer_details VALUES("1","88870073539","Muhaimin Sheik","Chennai India","");



CREATE TABLE `drivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `is_active` tinyint(1) unsigned DEFAULT '1',
  `date_added` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO drivers VALUES("1","Staff 1","0","anandbe13@gmail.com","1234567890","B.E","1000.00","150.00","India","Tamil nadu","Chennai","testing addresss","607 402","20170922110501_license.jpg","","","d1.png","2017-09-12","1","","2018-07-12 10:10:21");
INSERT INTO drivers VALUES("2","Staff 2","0","sri@gmail.com","546789322","B.Sc","","","india","t.N","chennai","p block","sdfa","20170922110501_license.jpg","","","d2.jpg","2017-09-04","1","2017-09-22 11:05:01","2018-01-04 07:10:57");



CREATE TABLE `floors` (
  `floor_id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `floor_name` varchar(200) NOT NULL,
  `floor_no` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  PRIMARY KEY (`floor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




CREATE TABLE `item_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_title` varchar(200) CHARACTER SET utf8 NOT NULL,
  `category_slug` varchar(200) CHARACTER SET utf8 NOT NULL,
  `category_img` varchar(200) CHARACTER SET utf8 NOT NULL,
  `category_details` text CHARACTER SET utf8 NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO item_category VALUES("1","Shirts","shirts","","","1");
INSERT INTO item_category VALUES("2","Jeans Pants","jeans-pants","","","1");
INSERT INTO item_category VALUES("3","T Shirts","t-shirts","","","1");



CREATE TABLE `item_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO item_units VALUES("1","Kg");
INSERT INTO item_units VALUES("2","grams");



CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `inward_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO items VALUES("1","Allen Solly","","1","650","20230314072518_items.jpg","","","1","","0","0","994","","","");
INSERT INTO items VALUES("2","Narrow Pant","","2","1500","20230314072605_items..jpg","","","1","","0","0","48","","","");



CREATE TABLE `locations_manufacturing_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `location` varchar(150) NOT NULL,
  `country` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO locations_manufacturing_units VALUES("1","yyyyyyyyyyy","","");
INSERT INTO locations_manufacturing_units VALUES("2","yyyyyyy","jjjjjjjj","cccccc");



CREATE TABLE `locations_shops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(200) NOT NULL,
  `shop_location` varchar(150) NOT NULL,
  `shop_country` varchar(150) NOT NULL,
  `shop_lable` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO locations_shops VALUES("1","Brand Hub","Ramnad","IND","BH");



CREATE TABLE `opening_cash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `cash_amount` varchar(50) DEFAULT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




CREATE TABLE `pay_back` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_order_item_id` int(11) NOT NULL,
  `sale_order_id` int(11) NOT NULL,
  `receipt_id` varchar(50) NOT NULL,
  `item_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `payback_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;




CREATE TABLE `sale_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `date_completed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_order_id` (`sale_order_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

INSERT INTO sale_order_items VALUES("1","1","1","0","Allen Solly","650","650","1","0","0","0","");
INSERT INTO sale_order_items VALUES("2","1","2","0","Narrow Pant","1500","1500","1","0","0","0","");
INSERT INTO sale_order_items VALUES("3","2","1","0","Allen Solly","650","650","1","0","0","0","");
INSERT INTO sale_order_items VALUES("4","3","2","0","Narrow Pant","1500","1500","1","0","0","0","");
INSERT INTO sale_order_items VALUES("5","4","1","0","Allen Solly","650","650","1","0","0","0","");
INSERT INTO sale_order_items VALUES("6","5","1","0","Allen Solly","650","650","1","0","0","0","");



CREATE TABLE `sale_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `combo_package_gst` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `receipt_id` (`receipt_id`),
  KEY `user_id` (`user_id`),
  KEY `shop_id` (`shop_id`),
  KEY `customer_id` (`customer_id`),
  KEY `contact_number` (`contact_number`),
  KEY `order_type` (`order_type`),
  KEY `payment_type` (`payment_type`),
  KEY `status` (`status`),
  KEY `table_id` (`table_id`),
  KEY `floor_id` (`floor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

INSERT INTO sale_orders VALUES("1","BH-1","1","1","0","","0","","","counter_sale","cash","0","paid","0","0","0","pending","","","","1","2023-03-14 20:07:43","0000-00-00 00:00:00","0","0","0","0","","","");
INSERT INTO sale_orders VALUES("2","BH-2","1","1","0","","0","","","counter_sale","cash","0","paid","0","0","0","pending","","","","1","2023-03-14 20:09:50","0000-00-00 00:00:00","0","0","0","0","","","");
INSERT INTO sale_orders VALUES("3","BH-3","1","1","0","","0","","","counter_sale","cash","0","paid","0","0","0","pending","","","","1","2023-03-14 20:11:32","0000-00-00 00:00:00","0","0","0","0","","","");
INSERT INTO sale_orders VALUES("4","BH-4","1","1","0","","0","","","counter_sale","cash","0","paid","0","0","0","pending","","","","2","2023-03-14 20:12:33","0000-00-00 00:00:00","0","0","0","0","","","");
INSERT INTO sale_orders VALUES("5","BH-5","1","1","1","Muhaimin Sheik","88870073539","Chennai India","","counter_sale","cash","0","paid","0","0","0","pending","","","","1","2023-03-14 20:13:12","0000-00-00 00:00:00","0","0","0","0","","","");



CREATE TABLE `settings` (
  `set_name` varchar(150) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

INSERT INTO settings VALUES("CLIENT_NAME","1","Brand Hub");
INSERT INTO settings VALUES("CLIENT_ADDRESS","2","Ramnad, Tamilnadu");
INSERT INTO settings VALUES("CLIENT_NUMBER","3","888888888");
INSERT INTO settings VALUES("CLIENT_WEBSITE","4","");
INSERT INTO settings VALUES("RECIPT_PRE","5","BH-");
INSERT INTO settings VALUES("CURRENCY","6","₹");
INSERT INTO settings VALUES("BILL_FOOTER","7","Thank you and Visit Again...!");
INSERT INTO settings VALUES("API_KEY","8","2sYDrDoDx9z4");
INSERT INTO settings VALUES("OWNER_NUM","9","");
INSERT INTO settings VALUES("CLIENT_LOGO","10","");
INSERT INTO settings VALUES("BILL_TAX_VAL","11","5");



CREATE TABLE `settle_sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `settle_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO settle_sale VALUES("1","1","1","","5600","0","0","0","","0","0","","","5600","0","0","0","0","0","5600","5600","2023-03-14 20:16:57");



CREATE TABLE `staff_loans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) DEFAULT NULL,
  `loan_amount` decimal(9,2) DEFAULT NULL,
  `loan_type` enum('credit','debit') DEFAULT NULL,
  `loan_date` date DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




CREATE TABLE `staff_salary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) DEFAULT NULL,
  `basic_salary` decimal(9,2) DEFAULT NULL,
  `allowance` decimal(9,2) DEFAULT NULL,
  `month_year` varchar(225) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO staff_salary VALUES("1","1","1000.00","150.00","Jul-2018","2018-07-12 10:10:21","2018-07-12 10:10:21");



CREATE TABLE `stock_management_history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `action_type` enum('add','sub') NOT NULL DEFAULT 'add',
  `stock_value` double NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`history_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

INSERT INTO stock_management_history VALUES("1","1","274","add","1","2018-07-31 05:13:02");
INSERT INTO stock_management_history VALUES("2","1","274","add","1","2018-07-31 05:13:02");
INSERT INTO stock_management_history VALUES("3","1","274","add","1","2018-07-31 05:13:02");
INSERT INTO stock_management_history VALUES("4","1","274","add","1","2018-07-31 05:13:39");
INSERT INTO stock_management_history VALUES("5","1","274","add","1","2018-07-31 05:13:49");
INSERT INTO stock_management_history VALUES("6","1","274","add","2","2018-07-31 05:14:08");
INSERT INTO stock_management_history VALUES("7","1","274","add","1","2018-07-31 05:30:55");
INSERT INTO stock_management_history VALUES("8","1","274","add","1","2018-07-31 05:30:55");
INSERT INTO stock_management_history VALUES("9","1","274","add","1","2018-07-31 05:49:32");
INSERT INTO stock_management_history VALUES("10","1","274","add","1","2018-07-31 05:49:32");
INSERT INTO stock_management_history VALUES("11","1","274","add","1","2018-07-31 05:51:11");
INSERT INTO stock_management_history VALUES("12","1","274","add","1","2018-07-31 05:51:11");
INSERT INTO stock_management_history VALUES("13","1","274","add","10","2018-07-31 05:56:14");
INSERT INTO stock_management_history VALUES("14","1","274","add","10","2018-07-31 05:56:14");
INSERT INTO stock_management_history VALUES("15","1","274","add","10","2018-07-31 05:56:14");
INSERT INTO stock_management_history VALUES("16","1","274","add","10","2018-07-31 05:56:14");
INSERT INTO stock_management_history VALUES("17","1","274","add","10","2018-07-31 05:56:14");
INSERT INTO stock_management_history VALUES("18","1","274","add","10","2018-07-31 05:56:14");



CREATE TABLE `table_management` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `floor_id` int(11) NOT NULL,
  `table_no` int(11) NOT NULL,
  `no_of_seats` int(11) NOT NULL,
  `filled_seats` int(11) NOT NULL,
  PRIMARY KEY (`table_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




CREATE TABLE `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
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
  `fcm_id` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO users VALUES("1","admin","21232f297a57a5a743894a0e4a801fc3","1","1","1","Admin","","anandbe13@gmail.com","123456789","1","1","counter_sale,delivery_sale,dine_in,take_away,reports,settle_sale,cod_log,online_order_log,sale_order_details,cash_back,barcode_print","2016-03-30 10:13:18","2016-03-30 10:13:18","");



CREATE TABLE `users_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8 NOT NULL,
  `slug` varchar(25) CHARACTER SET utf8 NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO users_role VALUES("1","Admin","admin","2016-03-29 00:00:00","2016-03-29 00:00:00");
INSERT INTO users_role VALUES("2","Cashier","Cashier","0000-00-00 00:00:00","2017-10-08 09:47:51");
INSERT INTO users_role VALUES("3","Waiter","Waiter","0000-00-00 00:00:00","2017-10-08 09:48:06");
INSERT INTO users_role VALUES("5","Delivery Boy","delivery-boy","0000-00-00 00:00:00","2017-10-08 09:48:24");

