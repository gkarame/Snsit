-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2014 at 05:42 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `snsme`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE IF NOT EXISTS `alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL,
  `description` varchar(500) NOT NULL,
  `sql_query` text NOT NULL,
  `values` varchar(255) DEFAULT NULL,
  `severity` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_category` (`id_category`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `alerts`
--

INSERT INTO `alerts` (`id`, `id_category`, `description`, `sql_query`, `values`, `severity`, `date`) VALUES
(1, 27, 'Development Phase started and Milestone "SOP Sign-Off" is not set to status Closed ', '', NULL, '1', '0000-00-00 00:00:00'),
(2, 27, 'Development Phase started since 2 weeks and no Issue List is available in the Documents Tab', '', NULL, '1', '0000-00-00 00:00:00'),
(3, 27, 'Development Phase started and the last Issue List Document available in the Documents Tab  is 2 weeks old', '', NULL, '2', '0000-00-00 00:00:00'),
(4, 27, 'Project Started since 4 weeks and no Status Report Document is available in the Documents Tab', '', NULL, '1', '0000-00-00 00:00:00'),
(5, 27, 'Project started and the last Status Report Document available in the Documents Tab is 2 weeks old', '', NULL, '2', '0000-00-00 00:00:00'),
(6, 27, 'Expenses Spent have reached  {percent}% of Expenses Budget', '', '50,75,90,100', '2,2,1,1', '0000-00-00 00:00:00'),
(7, 27, 'Actual Man Days have reached {percent}% of Total Man Days', '', '50,75,90,100', '2,2,1,1', '0000-00-00 00:00:00'),
(8, 27, 'Milestones with Status Pending have not been updated for more than 4 weeks', '', NULL, '2', '0000-00-00 00:00:00'),
(9, 27, 'UAT Started and no Project Check List Document is available in the Documents Tab', '', NULL, '1', '0000-00-00 00:00:00'),
(10, 27, 'UAT started and the last Project Check List Document is 2 weeks old', '', NULL, '2', '0000-00-00 00:00:00'),
(11, 27, 'UAT Started and Customer Connection Tab is empty', '', NULL, '1', '0000-00-00 00:00:00'),
(12, 27, 'Development Phase started and Milestone 4 is not set to status Closed ', '', NULL, '2', '0000-00-00 00:00:00'),
(13, 27, 'Go-Live Phase started and Milestone 7 is not set to status Closed ', '', NULL, '1', '0000-00-00 00:00:00'),
(14, 27, 'Milestone 8 is set to Closed since 4 weeks and Milestone 9 is still Pending', '', NULL, '2', '0000-00-00 00:00:00'),
(15, 26, 'Project Started since 2 weeks and no Status Report Document is available in the Documents Tab', '', NULL, '1', '0000-00-00 00:00:00'),
(16, 26, 'Project started the last Status Report Document available in the Documents Tab is 3 weeks old', '', NULL, '2', '0000-00-00 00:00:00'),
(17, 26, 'Expenses Spent have reached {percent}% of Expenses Budget', '', '50,75,90,100', '2,2,1,1', '0000-00-00 00:00:00'),
(18, 26, 'Actual Man Days have reached {percent}% of Total Man Days', '', '50,75,90,100', '2,2,1,1', '0000-00-00 00:00:00'),
(19, 26, 'Milestones with Status Pending have not been updated for more than 4 weeks', '', NULL, '2', '0000-00-00 00:00:00'),
(20, 26, 'Milestone 6 is set to status Closed and the Document Category Final Deliverables is empty', '', NULL, '2', '0000-00-00 00:00:00'),
(21, 26, 'Milestone 5 is set to Closed since 4 weeks and Milestone 6 is still Pending', '', NULL, '2', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `codelists`
--

CREATE TABLE IF NOT EXISTS `codelists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codelist` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `id_category` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_category` (`id_category`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `codelists`
--

INSERT INTO `codelists` (`id`, `codelist`, `label`, `description`, `id_category`) VALUES
(1, 'country', 'Country', 'List of Countries available in SNS It!', 1),
(2, 'industry', 'Industry', '', 3),
(3, 'product', 'Product', '', 3),
(4, 'ea_category', 'EA Category', '', 4),
(5, 'ea_milestone', 'EA Milestone', '', 4),
(6, 'region', 'Region', '', 3),
(7, 'connection_type', 'Connection Type', '', 7),
(8, 'currency', 'Currency', '', 3),
(9, 'ea_notes', 'EA Notes', '', 4),
(10, 'training_course', 'Training Course', '', 4),
(11, 'consultancy', 'Consultancy', '', 4),
(12, 'unit', 'Unit', '', 1),
(13, 'branch', 'Branch', '', 1),
(15, 'expenses_type', 'Expense type', '', 8),
(16, 'support_service', 'Support Service', 'Support Service', 9),
(17, 'partner', 'Partner', 'Partner', 10),
(18, 'frequency', 'Frequency', 'Frequency', 9),
(19, 'contract_duration', 'Contract Duration', 'Contract Duration', 9),
(20, 'requests', 'Leaves Type', '', 11),
(21, 'hr_request', 'Leaves Type', '', 12),
(22, 'template_email', 'Template E-mail', '', 13),
(23, 'reason', 'reason', '', 14),
(24, 'supplier_type', 'Supplier Type', '', 15),
(25, 'travel_expense_type', 'Expense Type', '', 16);

-- --------------------------------------------------------

--
-- Table structure for table `codelists_categories`
--

CREATE TABLE IF NOT EXISTS `codelists_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `list_order` int(11) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `codelists_categories`
--

INSERT INTO `codelists_categories` (`id`, `name`, `list_order`) VALUES
(1, 'Users Module', 0),
(3, 'Customers Module', 1),
(4, 'EAs Module', 2),
(5, 'Invoices Module', 3),
(7, 'Connections Module', 4),
(8, 'Expenses module', 6),
(9, 'Maintenance Module', 7),
(10, 'Invoices Module', 8),
(11, 'Leave Requests', 10),
(12, 'HR Requests', 11),
(13, 'Receivables Module', 11),
(14, 'Support desk module', 100),
(15, 'Suppliers module', 101),
(16, 'Travel Module', 102),
(17, 'Not Used Yet', 5);

-- --------------------------------------------------------

--
-- Table structure for table `codelkups`
--

CREATE TABLE IF NOT EXISTS `codelkups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_codelist` int(11) NOT NULL,
  `codelkup` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_codelist` (`id_codelist`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=390 ;

--
-- Dumping data for table `codelkups`
--

INSERT INTO `codelkups` (`id`, `id_codelist`, `codelkup`) VALUES
(8, 8, 'EUR'),
(9, 8, 'USD'),
(15, 7, 'Conn 1'),
(16, 7, 'Conn 2'),
(21, 2, 'Ind 3'),
(24, 4, 'Training'),
(25, 4, 'Licences'),
(26, 4, 'Consulting'),
(27, 4, 'SW Service'),
(28, 4, 'Change Request'),
(29, 12, 'Beirut'),
(31, 13, 'Beirut'),
(35, 5, 'First Milestone'),
(36, 5, 'Second Milestone'),
(42, 15, 'Car Fuel/Rental'),
(43, 15, 'Meals'),
(44, 15, 'Taxi/Transport'),
(45, 15, 'Hotel Accomodation'),
(46, 15, 'Phone/Cards/Internet'),
(47, 15, 'Per Diem'),
(48, 15, 'Cleaning Services/Material'),
(49, 15, 'Visa Fees'),
(50, 15, 'Air Ticket'),
(51, 15, 'Fitness Allowance'),
(52, 15, 'Advance Payment'),
(53, 15, 'Bank Charges'),
(54, 15, 'Books SNS Library'),
(55, 15, 'Other'),
(56, 13, 'Dubai'),
(58, 1, 'Qatar'),
(59, 6, 'Middle-East'),
(60, 6, 'South Africa'),
(61, 6, 'Europe'),
(62, 6, 'US'),
(63, 6, 'Asia'),
(64, 3, 'Infor WM'),
(65, 3, 'Boomi'),
(66, 3, 'SIP'),
(67, 11, 'Warehouse Design Layout'),
(68, 11, 'Supply Network Model'),
(72, 5, 'Upon EA Approval'),
(73, 5, 'Upon Completion of Work'),
(77, 17, 'SNS'),
(78, 17, 'SNSI'),
(79, 17, 'SPAN'),
(80, 18, 'Monthly'),
(81, 18, 'Quarterly'),
(82, 18, 'Biyearly'),
(83, 18, 'Yearly'),
(84, 19, 'Open Ended'),
(85, 19, '1 year'),
(86, 19, '2 years'),
(87, 19, '3 years'),
(88, 19, '5 years'),
(89, 16, 'After Hours Support'),
(90, 20, 'Day Off'),
(91, 20, 'Vacation'),
(92, 20, 'Sick'),
(93, 20, 'Working from Home'),
(99, 24, 'Sweets'),
(100, 24, 'Booze'),
(101, 24, 'Cigarettes'),
(102, 25, 'Airfare'),
(103, 25, 'Insurance'),
(104, 25, 'Visa'),
(111, 1, 'Lebanon'),
(112, 1, 'UAE'),
(113, 1, 'KSA'),
(114, 1, 'Oman'),
(115, 1, 'Jordan'),
(116, 12, 'Professional Services'),
(117, 12, 'Customer Services'),
(118, 12, 'Business Development'),
(119, 12, 'Consulting Practice'),
(120, 13, 'Singapore'),
(121, 13, 'South Africa'),
(122, 1, 'South Africa'),
(123, 1, 'Brazil'),
(124, 1, 'Argentina'),
(125, 1, 'Japan'),
(126, 1, 'Malaysia'),
(127, 1, 'Singapore'),
(128, 1, 'Egypt'),
(129, 1, 'Turkey'),
(130, 1, 'Kuwait'),
(131, 1, 'Afghanistan'),
(132, 1, 'Mexico'),
(133, 1, 'Chile'),
(134, 1, 'Spain'),
(135, 1, 'Germany'),
(136, 1, 'Thailand'),
(137, 2, 'Automotive'),
(138, 2, 'Aviation'),
(139, 2, 'Consulting'),
(140, 2, '3 PL'),
(142, 2, 'IT'),
(143, 2, 'Distribution'),
(144, 2, 'FMCG'),
(145, 2, 'Fashion'),
(146, 2, 'Manufacturing'),
(147, 2, 'Medical Equipments'),
(148, 2, 'Furniture'),
(149, 2, 'Oil & Petrochemical'),
(150, 2, 'Retailer'),
(151, 2, 'Wholesaler'),
(152, 2, 'Pharmaceutical'),
(153, 2, 'Hospitals'),
(154, 2, 'Transportation'),
(155, 2, 'Electronics'),
(156, 2, 'Tires'),
(157, 2, 'Cosmetics & Perfumes'),
(158, 2, 'Beverage'),
(159, 2, 'Labels'),
(160, 3, 'Ramp'),
(161, 3, 'Roadnet'),
(162, 3, 'Infor DP'),
(163, 6, 'Latin America'),
(164, 1, 'USA'),
(165, 3, 'Oracle'),
(166, 8, 'QAR'),
(167, 8, 'SAR'),
(168, 8, 'AED'),
(169, 8, 'AUD'),
(170, 8, 'BHD'),
(171, 8, 'BGN'),
(172, 8, 'CNY'),
(173, 8, 'EGP'),
(174, 8, 'GBP'),
(175, 1, 'UK'),
(176, 8, 'HKD'),
(177, 8, 'IRR'),
(178, 8, 'IRR'),
(179, 8, 'JOD'),
(180, 8, 'JPY'),
(181, 8, 'KWD'),
(182, 8, 'LBP'),
(183, 8, 'MUR'),
(184, 8, 'MXN'),
(185, 8, 'MYR'),
(186, 8, 'OMR'),
(187, 8, 'PHP'),
(188, 8, 'PLN'),
(189, 8, 'RON'),
(190, 8, 'RSD'),
(191, 8, 'RUB'),
(192, 8, 'INR'),
(193, 1, 'India'),
(194, 8, 'SDG'),
(195, 8, 'SGD'),
(196, 8, 'TRY'),
(197, 8, 'ZAR'),
(198, 8, 'ARS'),
(199, 8, 'CLP'),
(200, 8, 'BRL'),
(201, 17, 'SNS APJ'),
(202, 10, 'Warehousing Best Practices'),
(203, 10, 'Warehousing Safety & Security'),
(204, 10, 'How To Slot Your Warehouse'),
(205, 10, 'Fundamentals of Supply Chain'),
(206, 10, 'Warehousing Costing and Billing'),
(207, 10, 'Introduction to RFID in Logistics'),
(208, 10, 'Fundamentals of Demand Planning'),
(209, 10, 'Transportation Best Practices'),
(210, 10, 'How to Measure, Benchmark and Improve your Warehouse Performance'),
(211, 10, 'Getting Started with Warehouse Management Systems'),
(212, 10, 'Introduction To Warehouse Automation'),
(213, 10, 'Infor WM Overview Course'),
(214, 10, 'Infor WM Advanced Course'),
(215, 10, 'Infor WM System Admin Course'),
(216, 10, 'Infor WM DB Model Training'),
(217, 10, 'Boomi EAI Overview Course'),
(218, 10, 'Boomi EAI Advanced Course'),
(219, 10, 'Development of WMS Reports using Crystal Reports'),
(220, 10, 'Development of WMS Reports using Power Builder'),
(221, 10, 'Infor WM DB Model & WMS Reports (PB or Crystal)'),
(222, 9, 'Additional Man days will be charged at the standard man day rate of 1,000 USD'),
(223, 9, 'All Travel and Living Expenses will be charged separately and not included in this EA'),
(224, 9, 'This EA will be charged on a Time Material Basis, a Time Sheet will be sent with the invoice'),
(225, 9, 'Any additional sessions will be treated and charged seperately'),
(226, 9, 'Any additional licenses will be charged seperately'),
(227, 9, 'All the above fees are net of all Saudi and Withholding Taxes'),
(228, 9, 'All the above fees are net of all taxes or any withholding fees'),
(229, 9, 'Additional Man Days will be charged seperately'),
(230, 15, 'Laundry'),
(231, 25, 'Car Rental'),
(232, 25, 'Accomodation'),
(233, 21, 'Employment Letter'),
(234, 21, 'SSNF Employment Letter'),
(235, 21, 'Personal Visa Employment Letter'),
(236, 21, 'Business Visa Employment Letter'),
(237, 21, 'Visa Invitation Letter'),
(238, 21, 'Visa Invitation Letter Template'),
(239, 21, 'Recommendation Letter'),
(240, 3, 'EM'),
(241, 11, 'Warehouse Audit'),
(242, 11, 'Supply Chain Audit'),
(243, 11, 'Supply Chain Org Structure'),
(259, 2, 'Banking'),
(371, 1, 'Mauritius'),
(372, 1, 'Pakistan'),
(373, 1, 'Switzerland'),
(374, 1, 'Belgium'),
(375, 1, 'Japan'),
(376, 1, 'Bahrain'),
(378, 16, 'Infor WMS S&U'),
(379, 16, 'SIP  S&U'),
(380, 16, 'Boomi S&U'),
(381, 16, 'Ramp S&U'),
(382, 16, 'PORTAL S&U'),
(383, 16, 'LMR S&U'),
(384, 16, 'DP S&U'),
(385, 16, 'EM S&U'),
(386, 3, 'LMR'),
(387, 3, 'Portal'),
(388, 11, 'WMS SOP'),
(389, 23, 'TEST');

-- --------------------------------------------------------

--
-- Table structure for table `connections`
--

CREATE TABLE IF NOT EXISTS `connections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `server_name` varchar(255) NOT NULL,
  `password` varchar(50) NOT NULL,
  `file` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_connections_customers1_idx` (`id_customer`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `connections`
--

INSERT INTO `connections` (`id`, `id_customer`, `name`, `type`, `server_name`, `password`, `file`) VALUES
(4, 32, 'TT', 15, 'TT', 'TT@pasd123', '');

-- --------------------------------------------------------

--
-- Table structure for table `currency_rate`
--

CREATE TABLE IF NOT EXISTS `currency_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency` int(11) NOT NULL,
  `rate` double NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `currency` (`currency`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=39 ;

--
-- Dumping data for table `currency_rate`
--

INSERT INTO `currency_rate` (`id`, `currency`, `rate`, `date`) VALUES
(3, 8, 0.76, '2013-06-25'),
(4, 9, 1, '2013-06-25'),
(5, 8, 1.32396, '2014-08-25'),
(6, 166, 0.27448, '2014-08-25'),
(7, 167, 0.26659, '2014-08-25'),
(8, 168, 0.27226, '2014-08-25'),
(9, 169, 0.93137, '2014-08-25'),
(10, 170, 2.6266, '2014-08-25'),
(11, 171, 0.67463, '2014-08-25'),
(12, 172, 0.16225, '2014-08-25'),
(13, 173, 0.13947, '2014-08-25'),
(14, 174, 1.65684, '2014-08-25'),
(15, 176, 0.12902, '2014-08-25'),
(16, 178, 0.00004, '2014-08-25'),
(17, 179, 1.40964, '2014-08-25'),
(18, 180, 0.00962, '2014-08-25'),
(19, 181, 3.5163, '2014-08-25'),
(20, 182, 0.00066, '2014-08-25'),
(21, 183, 0.03135, '2014-08-25'),
(22, 184, 0.07608, '2014-08-25'),
(23, 185, 0.31616, '2014-08-25'),
(24, 186, 2.58994, '2014-08-25'),
(25, 187, 0.02275, '2014-08-25'),
(26, 188, 0.31506, '2014-08-25'),
(27, 189, 0.29995, '2014-08-25'),
(28, 190, 0.01119, '2014-08-25'),
(29, 191, 0.02769, '2014-08-25'),
(30, 191, 0.02769, '2014-08-25'),
(31, 192, 0.01654, '2014-08-25'),
(32, 194, 0.17529, '2014-08-25'),
(33, 195, 0.80003, '2014-08-25'),
(34, 196, 0.45814, '2014-08-25'),
(35, 197, 0.09336, '2014-08-25'),
(36, 198, 0.11914, '2014-08-25'),
(37, 199, 0.00171, '2014-08-25'),
(38, 200, 0.43804, '2014-08-25');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` int(11) DEFAULT NULL,
  `region` tinyint(4) NOT NULL,
  `address` text,
  `main_phone` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0=>inactive, 1=>active',
  `website` varchar(255) DEFAULT NULL,
  `industry` int(11) NOT NULL,
  `bill_to_address` text NOT NULL,
  `bill_to_contact_person` varchar(256) NOT NULL,
  `bill_to_contact_email` varchar(256) NOT NULL,
  `primary_contact_email` varchar(256) NOT NULL,
  `default_currency` int(11) NOT NULL,
  `primary_contact_name` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `mobile_number` varchar(100) DEFAULT NULL,
  `product_1` int(11) DEFAULT NULL,
  `product_2` int(11) DEFAULT NULL,
  `product_3` int(11) DEFAULT NULL,
  `accounting_code` varchar(100) DEFAULT NULL,
  `cs_representative` int(11) DEFAULT NULL,
  `strategic` enum('High','Low','Medium') DEFAULT NULL,
  `week_end` enum('Thursday/Friday','Friday/Saturday','Saturday/Sunday') DEFAULT NULL,
  `time_zone` varchar(256) DEFAULT NULL,
  `support_weekend` enum('Yes','No') NOT NULL DEFAULT 'No',
  `lpo_required` enum('Yes','No') NOT NULL DEFAULT 'No',
  PRIMARY KEY (`id`),
  KEY `country` (`country`) USING BTREE,
  KEY `industry` (`industry`) USING BTREE,
  KEY `product_1` (`product_1`) USING BTREE,
  KEY `product_3` (`product_3`) USING BTREE,
  KEY `product_2` (`product_2`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=157 ;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `city`, `country`, `region`, `address`, `main_phone`, `status`, `website`, `industry`, `bill_to_address`, `bill_to_contact_person`, `bill_to_contact_email`, `primary_contact_email`, `default_currency`, `primary_contact_name`, `job_title`, `mobile_number`, `product_1`, `product_2`, `product_3`, `accounting_code`, `cs_representative`, `strategic`, `week_end`, `time_zone`, `support_weekend`, `lpo_required`) VALUES
(1, 'Testing', 'test', 58, 63, NULL, '12313123', 1, '', 21, 'sadasd', 'qwe', '', '', 9, '', '', '', 64, 64, NULL, '', NULL, '', '', '', 'No', 'No'),
(32, 'A. N. Boukhater', 'Beirut', 111, 59, NULL, '+961 1 888298', 1, '', 137, 'Nahr El Mot, Metn Express Highway, Beirut, Lebanon', 'Anthony Boukhater', '', '', 9, '', '', '', 64, 66, NULL, '', NULL, '', '', '', 'No', 'No'),
(33, 'ABA', 'Doha', 58, 59, NULL, '+974 6005765', 1, NULL, 150, 'ABA Tower, Airport Road, P.O.Box 75, Doha - Qatar ', 'Jon Mayson', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(34, 'Abbar Foods', 'Jeddah', 113, 59, NULL, '+966 2 6474000', 1, NULL, 144, 'Cold Stores Building, Hind Bint Al Walid Street, Petromin Area 2495, Jeddah, KSA', 'Hani Al-Ghamdi ', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(35, 'AbdulWahed Co.', 'Jeddah', 113, 59, NULL, '+966 12 6500282', 1, NULL, 155, '5th Floor, Room No.503, Al-Amal Plaza, P.O.Box: 3611, Jeddah 21481, KSA', 'Yaser AbdulWahed', '', '', 167, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(36, 'ADT', 'Abu Dhabi', 112, 59, NULL, '', 1, NULL, 146, 'P.O.Box 136687, Abu Dhabi, UAE', 'Muzammil Subhan', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(37, 'Agility', 'Safat', 130, 59, NULL, '+974 4500017', 1, NULL, 151, 'P.O.Box 25418, Safat 25418, Kuwait', 'Mohammad Habeeb ', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(38, 'Ahmad Tea', 'RAK', 112, 59, NULL, '+971 4 8811343', 1, NULL, 144, 'P.O.Box 35750, RAK, UAE', 'Ali Afshar', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(39, 'Airlink', 'Dubai', 112, 59, NULL, '+971 4 282 1050', 1, NULL, 151, 'P.O.Box 10466, Jebel Ali Free Zone, Dubai, UAE', 'Chrys Mendonca', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(40, 'Ajlan Bros', 'Riyadh', 113, 59, NULL, '', 1, NULL, 144, 'Commercial Line, South Al-Faisaliah, Olaya, Riyadh, KSA', 'Abdulrahman Ibrahim', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(41, 'AKI', 'Dubai', 112, 59, NULL, '', 1, NULL, 150, 'P.O. BOX 11245, Dubai, UAE', 'Ralph Saad', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(42, 'AKL', 'Dubai', 112, 59, NULL, '', 1, NULL, 144, 'Dubai, UAE', 'Nora Miguel', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(43, 'Al Bayader', 'Sharjah', 112, 59, NULL, '+971 4 229 0288', 1, NULL, 151, 'P.O. BOX 27106, Sharjah, UAE', 'Fadl Obeid ', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(44, 'Al Hail Holding', 'Abu Dhabi', 112, 59, NULL, '', 1, NULL, 140, 'P.O BOX 3590, Abu Dhabi, UAE', 'Nidal Al Khateeb', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(45, 'Al Islami Foods Co.', 'Dubai', 112, 59, NULL, '', 1, NULL, 151, 'Dubai, UAE', 'Joachim Yebouet', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(47, 'Al Malki Group', 'Jeddah', 113, 59, NULL, '+966 2 6518580', 1, NULL, 151, 'PO Box 337, Jeddah 21411, KSA', 'Bassel Omarbasha ', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(48, 'Al Mansour', 'Cairo', 128, 59, NULL, '', 1, NULL, 144, 'Zahraa El Maadi, Industrial Zone, P.O. Box 97, New Maadi, Cairo, Egypt', 'André Jacobs', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(49, 'Al Nahda International FZCO', 'Dubai', 112, 59, NULL, '', 1, NULL, 152, 'P.O.Box 18312, Jebel Ali, Dubai, UAE', 'Manish Mehra', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(50, 'Al Nahdi', '', NULL, 59, NULL, '+966 2 6407575', 0, NULL, 152, '', '', '', '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(51, 'Al Shaya', 'Safat', 130, 59, NULL, '+965 2224 3674', 1, NULL, 150, 'P.O. Box 181, Safat 13002, Kuwait', 'Biju Chandrasekharan', '', '', 9, NULL, NULL, NULL, 65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(52, 'Al Talayi', 'Jeddah', 113, 59, NULL, '', 1, NULL, 151, 'Palestine St., Bridgestone Bldg, P.O.Box 42196, Jeddah 21541, KSA', 'Adbulbaset H. Moafa', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(53, 'Al Yasra', 'Safat', 130, 59, NULL, '+965 224 9411', 1, NULL, 144, 'P.O.Box: 3228, Safat 13033, Kuwait', 'Joel Ferrao', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(54, 'Al Zahrani', 'Dammam', 113, 59, NULL, '', 1, NULL, 155, 'P. O. Box 135 Dammam 31411, 2nd Floor, Business City Building, King Abdulaziz Street, KSA', 'Ahad A. Awaidha', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(55, 'Al-Aujan Group', 'Manama', 376, 59, NULL, '', 1, NULL, 150, 'P.O. Box 904, Aujan House, Govt. Avenue, Manama, Kingdom of Bahrain', 'Mohammed Al Matrook', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(56, 'Al-Ghanim', 'Be', 131, 59, NULL, '+965 188 1111', 1, '', 144, 'asd', 'asd', '', '', 168, '', '', '', NULL, NULL, NULL, '', NULL, '', '', '', 'No', 'No'),
(57, 'Al-Haddad', 'Jeddah', 113, 59, NULL, '', 1, NULL, 155, 'Palestine St. Meshrefa District, PO Box 11629 Jeddah 21463, KSA', 'Karim Safty ', '', '', 167, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(58, 'Almutlaq Furniture', 'Riyadh', 113, 59, NULL, '+ 966 1 270 2835', 1, NULL, 150, 'P.O.Box 1321 Riyadh 11431, KSA', 'Hasan Hassan', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(59, 'Al-Rehab', 'Jeddah', 113, 59, NULL, '+966 12 6081000', 1, NULL, 157, 'Jeddah, Al Balad, Al Khaskia, Jeddah 21431, KSA', 'Salem Baradem ', '', '', 167, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(60, 'Americana Meat KSA', 'Salmiya', 130, 59, NULL, '', 1, NULL, 144, 'Al Zaben Building, P.O.Box 3448, Salmiya 22035, Kuwait', 'Murtada Halabi ', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(61, 'Anham', 'Dubai', 112, 59, NULL, '', 1, NULL, 144, 'Dubai Airport Free Zone, Dafza, East Wing, Building A4, Suite 608, P.O.Box 231082, Dubai, UAE', 'Osama Alriyahi', '', '', 9, NULL, NULL, NULL, 64, 160, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(62, 'Arabian Medical Marketing Co. Ltd', 'Riyadh', 113, 59, NULL, '', 1, NULL, 155, 'P.O.Box 90401, Riyadh 11613, KSA', 'Arsalan Sheikh', '', '', 167, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(63, 'Aramex', 'Dubai', 112, 59, NULL, '+971 4 2865000', 1, NULL, 140, 'P.O.Box 38410, Jebel Ali FreeZone, Dubai, UAE', 'Koshy Abraham', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(64, 'Areej', 'Rusayl', 114, 59, NULL, '', 1, NULL, 151, 'P.O.Box 22, Rusayl, Postal Code 124, Sultanate of Oman', 'Arul Salvan ', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(65, 'Arrow', 'Jeddah', 113, 59, NULL, '', 1, NULL, 144, 'PO BOX 42404, Jeddah 21541, KSA', 'Elie Sioufi', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(66, 'ATS', 'Jeddah', 113, 59, NULL, '+966 2 2243444', 1, NULL, 144, 'P.O Box 53337, Jeddah 21583, KSA', 'Eben M. Philip', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(67, 'Ayezan', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, 'P.O.Box 85315, Jebel Ali South Zone, Dubai, UAE', 'Selvaraj R.N', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(68, 'Banaja', 'Jeddah', 113, 59, NULL, '', 1, NULL, 152, 'P.O. Box 42, Jeddah 21411, KSA', 'Amro Fakahani', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(69, 'Barloworld Logistics (Pty) Ltd', 'Johannesburg', 122, 60, NULL, '', 1, NULL, 150, '180 Katherine Street, Sandton 2146, Johannesburg, South Africa', 'Terrence Payne', '', '', 167, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(70, 'Bassile Freres', 'Beirut', 111, 59, NULL, '', 1, NULL, 140, 'Daroun, Harissa, Lebanon', 'Marwan Bassil', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(71, 'BCC', 'Beirut', 111, 59, NULL, '', 1, NULL, 140, 'Parallael Towers, Sin El Fil, Beirut, lebanon', 'Karim Bassil', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(72, 'BPC', 'Beirut', 111, 59, NULL, '', 1, NULL, 140, 'Bechara El Khoury Boulevard, Bechara El Khoury Tower, P.O. Box 1101 - 2040, Beirut, Lebanon', 'Emile Khoury', '', '', 0, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(73, 'Christian Art Distributors', 'Gauteng', 122, 60, NULL, '', 1, NULL, 150, '20 Smuts Ave, Vereeniging, Gauteng 1930, South Africa', 'Terrence Pringle', '', '', 167, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(74, 'Daher Food', 'Beirut', 111, 59, NULL, '', 1, NULL, 144, 'Near Hamra Plaza, Fourzol Main Road', 'Hisham Katrib', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(75, 'Danzas', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, 'P.O.Box 2623, Dubai, UAE', 'Mazen El Ghosseini', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(76, 'Deal Logistics', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, 'P.O.Box 18601, Dubai, UAE', 'Margareta AbuRas ', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(77, 'DHL', 'Al Khobar', 113, 59, NULL, '', 1, NULL, 140, 'P.O.B.31492, Al Khobar 31952, KSA', 'Nas167 Ahmed', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(78, 'DIB', 'Dubai', 112, 59, NULL, '', 1, NULL, 259, 'P.O.Box 1080, Dubai, UAE', 'Muhammed Aslam', '', '', 9, NULL, NULL, NULL, NULL, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(79, 'DSS', 'Dubai', 112, 59, NULL, '', 1, NULL, 151, 'Arenco Building, Zaabel Road, P.O.Box 52262, Dubai, UAE', 'Tina Malost', '', '', 168, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(80, 'Ducab', 'Dubai', 112, 59, NULL, '', 1, NULL, 151, 'P.O.Box 11529, Dubai, UAE', 'Raihan Amir', '', '', 168, NULL, NULL, NULL, NULL, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(81, 'EasyLog', 'Amman', 115, 59, NULL, '', 1, NULL, 140, 'P.O.Box 831379, Amman 1183, Jordan', 'Zaid Souqi', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(82, 'ED&F MAN', '', 127, 63, NULL, '', 1, NULL, 144, '8 Shenton Way, AXA Tower, #16-02, Singapore 068811', 'Ashley Mcintyre ', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(83, 'Extra', 'Al Khobar', 113, 59, NULL, '', 1, NULL, 155, 'Al Khobar, King Faisal Str., P.O.Box76688, Khobar 31958, KSA', 'Mazen Massalkhi', '', '', 167, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(84, 'Fattal', 'Beirut', 111, 59, NULL, '', 1, NULL, 140, 'Sin El Fil, jisr El Wati, Dany Chamoun Str., Fattal Building, Beirut, Lebanon', 'Ahmad Solh', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(85, 'Glow', 'Hawally', 130, 59, NULL, '', 1, NULL, 151, 'Noura Complex, 8th & 9th Floor, P.O.Box 4284, Hawally 32073, Kuwait', 'Hamad Hammauda', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(86, 'GM - Africa and Middle East', 'Dubai', 112, 59, NULL, '', 1, NULL, 137, 'P.O.Box 92333, Plot M000783, Dubai, UAE', 'Murtaza Hassan', '', '', 9, NULL, NULL, NULL, 64, 160, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(87, 'GMG', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, 'P.O.Box 894, Dubai, UAE', 'Nikel Anand', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(88, 'Golden Food', 'Beirut', 111, 59, NULL, '', 1, NULL, 144, 'First Floor, Abou Naoum Building, Mkalles, Beirut, lebanon', 'Mark Eid', '', '', 9, NULL, NULL, NULL, 64, 66, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(89, 'GSL', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, 'P.O.Box 2022, Dubai, UAE', 'Ajit Handa', '', '', 9, NULL, NULL, NULL, 65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(90, 'GWC', 'Doha', 58, 59, NULL, '', 1, NULL, 150, 'P.O. Box: 24434, Doha, Qatar', 'Maged Emil Kamal ', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(91, 'Holdal', 'Beirut', 111, 59, NULL, '', 1, NULL, 140, 'Dekwaneh, Galerie Matta, Holdal Building, Beirut, Lebanon', 'Danny Kreidy', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(92, 'IATCO', 'Jeddah', 113, 59, NULL, '', 1, NULL, 140, 'Jeddah 21411, KSA', 'Rafik Georges', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(93, 'Inchcape', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, 'Dubai, UAE', 'Mirza Baig', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(94, 'Intercol', 'Manama', 376, 59, NULL, '', 1, NULL, 151, 'P.O. Box: 584, Manama, Bahrain', 'Faiz Syedullah', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(95, 'Jawad', 'Manama', 376, 59, NULL, '', 1, NULL, 151, 'Jawad House, 171 Sh, Issa Avenue, P.O.Box 430, Manama, Bahrain', 'Surendran', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(96, 'Logistica', 'Beirut', 111, 59, NULL, '', 1, NULL, 140, 'Old Saida Road, Hadath, Beirut, Lebanon', 'Alain Bounassif', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(97, 'Logistica e2e', 'Giza', 128, 59, NULL, '', 1, NULL, 140, '6 El-Hesn Street, Giza, Arab Republic of Egypt', 'Sameh Yousef', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(98, 'M+M Hechme', 'Beirut', 111, 59, NULL, '', 1, NULL, 140, 'Plot 110, Dayshunieh, P.O.Box 151, Mansurieh, Lebanon', 'Jean Frederic Alam', '', '', 9, NULL, NULL, NULL, 64, 66, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(99, 'Madi International', 'Dubai', 112, 59, NULL, '', 1, NULL, 157, 'Al Quoz Industrial 2, P.O. Box: 56290, Dubai, UAE', 'Imad Hamdoun', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(100, 'Maxx', 'Dubai', 112, 59, NULL, '', 1, NULL, 151, 'P.O.Box 17740, Jebel Ali FreeZone, Dubai, UAE', 'Mohammed Shaiq', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(101, 'MCT', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, 'P.O.Box 261075, Jafz, Dubai, UAE', 'Rami Alameddine', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(102, 'Mersaco', 'Beirut', 111, 59, NULL, '', 1, NULL, 152, 'Jamil Kfoury SAL building, Sami Solh avenue, Parc sector, Beirut, Lebanon', 'Ghassan Al Mahassni', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(103, 'Mohamed Yousuf naghi & Brothers group', '', NULL, 59, NULL, '', 0, NULL, 137, '', '', '', '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(104, 'Mohebi', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, 'P.O.Box 267, Dubai, UAE', 'Ben Jacob', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(105, 'Nader Group', 'Amman', 115, 59, NULL, '', 1, NULL, 140, 'Al Lawzeh St., Muqablein, Amman 11118, Jordan', 'Andre Leroux', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(106, 'Nestle', 'Dubai', 112, 59, NULL, '', 1, NULL, 144, 'P.O.Box 17740, Jebel Ali, Dubai, UAE', 'Naseema Kadher', '', '', 9, NULL, NULL, NULL, 65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(107, 'NGK', 'Boksburg', 122, 60, NULL, '', 1, NULL, 150, 'Bantry Park, 41 Jansen Road, Jet Park, Boksburg, 1459 South Africa', 'Merle Van Zyl', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(108, 'Pan Emirates', 'Sharjah', 112, 59, NULL, '', 1, NULL, 151, 'P.O.Box 23197, Sharjah, Dubai, UAE', 'Eyas Khashan ', '', '', 9, NULL, NULL, NULL, 64, 160, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(109, 'Panalpina', 'Dubai', 112, 59, NULL, '', 1, NULL, 144, 'Dubai, UAE', 'Anthonie Verploegh', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(110, 'Parmalat', 'Stellenbosch', 122, 60, NULL, '', 1, NULL, 140, 'Strand Road, Stellenbosch 7600, South Africa', 'Hayden Williams ', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(111, 'Pepsico', 'Jeddah', 113, 59, NULL, '', 1, NULL, 158, 'Al Zaben Building, 2nd Floor, Thaliah Street, P.O. Box 11414, Jeddah, KSA, 21453', 'Azzam Adhami', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(112, 'PetroRabigh', 'Riyadh', 113, 59, NULL, '', 1, NULL, 149, 'P.O.Box 666, Riyadh 21911, KSA', 'Yasuhiko Kitaura', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(113, 'Pharmaworld', 'Dubai', 112, 59, NULL, '', 1, NULL, 152, 'Dubai, UAE', 'Mohammad Malek', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(114, 'Phoenix Beverages Group', 'Phoenix', 371, 60, NULL, '', 1, NULL, 158, '3rd Floor , Phoenix House, Pont Fer, Phoenix, Mauritius', 'Mogini Rungasamy', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(115, 'Pierlite Middle East', 'Sharjah', 112, 59, NULL, '', 1, NULL, 140, 'Sharjah FreeZone, P.O.Box 8181, Sharjah, Dubai, UAE', 'Jacob Daniel ', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(116, 'Planet Pharmacies', 'Dubai', 112, 59, NULL, '', 1, NULL, 152, 'Dubai, UAE', 'Hameed Imran', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(117, 'QNIE', 'Dubai', 58, 59, NULL, '', 1, NULL, 151, 'PO. Box: 490 Doha, Qatar', 'Hisham Basheer', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(118, 'Radec', 'Beirut', 111, 59, NULL, '', 1, NULL, 140, 'Beirut, Lebanon', 'Naji Sabbagh', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(119, 'Redington Gulf', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, 'P.O.Box 17266, Dubai, UAE', 'Sunil Dsouza', '', '', 9, NULL, NULL, NULL, 64, 160, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(120, 'REDTAG', 'Dubai', 112, 59, NULL, '', 1, NULL, 150, 'P.O.Box 17474, Dubai, UAE', 'Rajiv Shankar ', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(121, 'RHS', 'Dubai', 112, 59, NULL, '', 1, NULL, 151, 'P.O.Box 7, Dubai, UAE', 'Girish Kurup', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(122, 'RSA Logistics', 'Dubai', 112, 59, NULL, '', 1, NULL, 151, 'Dubai Logistics City, Dubai, UAE', 'Abhishek Shah', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(123, 'RTT', 'Boksburg', 122, 60, NULL, '', 1, NULL, 140, 'Reg No: 2007/003421/07, Cnr Jones & Springbok Roads, Bartlett, Boksburg, South Africa', 'Charm Naicker', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(125, 'S2S IMI Group', 'Jeddah', 113, 59, NULL, '', 1, NULL, 149, 'Engineering, Office 404, 4th Floor Matbouli Plaza, Al Ma''adi Street, Ruwais District, PO Box No. 7972, Jeddah 21472, KSA', 'Afteem Khoury', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(126, 'Saudi Vetonit Co. LTD. SAVETO', 'Riyadh', 113, 59, NULL, '', 1, NULL, 150, 'P.O. Box 52235, Riyadh 11563, KSA', 'Sami Hajjaj', '', '', 167, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(127, 'Sharaf Logistics Pakistan', 'Lahore', 372, 63, NULL, '', 1, NULL, 140, '46 KM, Multan Road, Nathay Khalsa, Manga Mandi, Lahore, Pakistan', 'Rashid Siddique ', '', '', 9, NULL, NULL, NULL, 160, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(129, 'Storall', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, 'Jebel Ali Industrial Area 2, P.O.Box 79775, Dubai, UAE', 'Ghassan Abughazaleh', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(130, 'Sunbulah Group', 'Jeddah', 113, 59, NULL, '', 1, NULL, 144, 'Jeddah, KSA', 'Dalia Khafagy', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(131, 'Supreme', '', 373, 61, NULL, '', 1, NULL, 140, 'Turbinenweg 2, 8866 Ziegelbruecke, Switzerland', 'Gaurav Kumar', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(132, 'Swift', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, 'Freight Management & Services, P.O.Box 50177, Dubai, UAE', 'Anil Mathews', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(134, 'Tamer Group', 'Jeddah', 113, 59, NULL, '', 1, NULL, 152, 'P.O.Box 180, Jeddah 21411, KSA', 'Ahmed Bin Almas', '', '', 9, NULL, NULL, NULL, 64, 66, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(135, 'TD & Company Limited', 'Tokyo', 375, 59, NULL, '', 1, NULL, 140, 'Nishi-Gotanda 1-11-1-508, Shinagawa, Tokyo, Japan 141-0031', 'Julien Obata', '', '', 9, NULL, NULL, NULL, 64, 66, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(136, 'Transmed', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, '9th floor, Gulf Tower, P.O.Box 1604, Dubai, UAE', 'Said Adada', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(137, 'United Beverage', 'Safat', 130, 59, NULL, '', 1, NULL, 158, 'Sabhan Industrial Area, P.O.Box 224, Safat 13003, Kuwait', 'Shamsudeen Nageem', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(138, 'United Group', 'Al Khobar', 113, 59, NULL, '', 1, NULL, 144, 'Al Saeed Towers, Khobar/Dammam Main Highway, Tower Number 1-A, 3rd Floor, Office Number 306, P.O.Box 64, Al-Khobar 31952, KSA', 'Magid Abumahfoud', '', '', 9, NULL, NULL, NULL, 64, 66, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(139, 'Unipharm', 'Beirut', 111, 59, NULL, '', 1, NULL, 152, 'P.O.Box 11-5255, Farid Abou Jaoude, 4th Floor, Assaad Rached St, Jal El dib, Metn, Lebanon', 'Fadi Kibrite', '', '', 9, NULL, NULL, NULL, 64, 66, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(140, 'UPS Supply Chain Solutions', '', NULL, 59, NULL, '', 0, NULL, 140, '', '', '', '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(141, 'UTi', 'South Carolina', 164, 0, NULL, '', 1, NULL, 154, '700 Gervais Street, Suite 100, Columbia, South Carolina,  29201, USA', 'Deanne Groves', '', '', 9, NULL, NULL, NULL, 65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(142, 'UTi', '', 374, 61, NULL, '', 1, NULL, 154, 'Bedrijvenzone Machelen Cargo, Bld 829A, 1830 Machelen, Belgium', 'Karsten Klag', '', '', 9, NULL, NULL, NULL, 65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(143, 'UWC', 'Jeddah', 113, 59, NULL, '', 1, NULL, 140, 'P.O. Box: 31450, Jeddah 21497, KSA', 'Abdulwareth Shamsan', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(144, 'Wared', 'Jeddah', 113, 59, NULL, '', 1, NULL, 140, 'Dar Al-Nahda Business Center, Prince Sultan Street, Jeddah 21540 KSA', 'Amr Kronfol', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(145, 'Wilhelmsen', 'Dubai', 112, 59, NULL, '', 1, NULL, 140, 'Jebel Ali, Dubai, UAE', 'John Martin', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(146, 'Zamil AC', 'Dammam', 113, 59, NULL, '', 1, NULL, 155, 'P.O. Box 41015, Street 106, 2nd Industrial City, Dammam 31521, KSA', 'Azeem ', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(147, 'Abu Issa', 'Doha', 58, 59, NULL, '', 1, NULL, 150, 'P.O.Box 6255, Doha, Qatar', 'Ahmed Al-Tamimi', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(148, 'Green House', 'Sharjah', 112, 59, NULL, '', 1, NULL, 140, 'P.O.Box 5927, Sharjah, UAE', 'Ziad Abdel Massih', '', '', 9, NULL, NULL, NULL, 64, 65, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(149, 'Kalem (BTA)', 'Istanbul', 129, 59, NULL, '', 1, NULL, 144, 'Maltepe Mh. Londra Asfaltı Cd., No:38 Kat : 1/A-B Saadet Plaza, Cevizlibağ-Zeytinburnu, Istanbul, Turkey', 'Banu Usanmaz', '', '', 9, NULL, NULL, NULL, 64, 66, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(150, 'Imperial', 'Jet Park', 122, 60, NULL, '', 1, NULL, 140, '118 Innes Road, Unit 1, Michelle Ferrero, Business Park, Jet Park, South Africa', 'Jean-Marc Desfontaines', '', '', 9, NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(151, 'Swift', 'Cairo', 128, 59, NULL, '', 1, NULL, 140, 'Area #8- industrial zone #6, 6th of October City, Cairo, Egypt', 'Mohamed Megahed', '', '', 9, NULL, NULL, NULL, 64, 66, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(152, 'Al Majdouie', 'Dammam', 113, 59, NULL, '+966 8198170', 1, NULL, 140, 'AlFaisalyah Area,  King Fahd Road, P.O. Box 336, Dammam, Saudi Arabia', 'Mohammed Khaled Arshed', '', '', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No', 'No'),
(154, 'REKME', 'EVERYWHERE', 175, 61, NULL, '03523781', 1, '', 140, 'Bill to ', 'tarek.elnino@gmail.com', '', 'tarek.elnino@gmail.com', 9, 'tarek.elnino@gmail.com', '', '', NULL, NULL, NULL, '', NULL, '', '', '', 'No', 'No'),
(156, 'ABA', 'Any', 58, 63, NULL, '03200200', 1, '', 140, 'Address 1', 'Tarek', '', '', 9, 'Someone', '', '', NULL, NULL, NULL, '', NULL, '', '', '', 'No', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `customers_contacts`
--

CREATE TABLE IF NOT EXISTS `customers_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `email` varchar(255) NOT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `mobile_number` varchar(100) DEFAULT NULL,
  `access` enum('No','Yes') NOT NULL DEFAULT 'No',
  `username` varchar(256) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_customers_contacts_customers1_idx` (`id_customer`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `customers_contacts`
--

INSERT INTO `customers_contacts` (`id`, `id_customer`, `name`, `email`, `job_title`, `mobile_number`, `access`, `username`, `password`) VALUES
(1, 1, 'testing', 'ramy.khattar@sns-emea.com', '', '123', 'Yes', 'ting', 'ting'),
(2, 1, 'ramy khattar', 'khattar.ramy@gmail.com', 'tech', '123', 'Yes', 'ramtest', 'ting'),
(3, 1, 'ram', 'tarek.elnino@gmail.com', 'tech', '123', 'Yes', 'ram', 'ram'),
(5, 154, 'tarek', 'tarek.elnino@gmail.com', 'tarek', '00000000', 'Yes', 'tarekme', 'tarekme'),
(6, 154, 'RAMYK', 'KHATTAR.RAMY@GMAIL.COM', 'te', '123123', 'Yes', 'ramyme', 'ramyme'),
(7, 32, 'Tony', 'tony@tony.com', 'Consultant', '03200200', 'No', '', ''),
(8, 32, 'Tony', 'Tony@tony.com', 'consultant', '03200200', 'No', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `dashboards`
--

CREATE TABLE IF NOT EXISTS `dashboards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(25) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `dashboards`
--

INSERT INTO `dashboards` (`id`, `code`, `name`, `status`) VALUES
(1, 'support_desk', 'Support Desk', 1),
(2, 'financial', 'Financial', 1),
(3, 'performance', 'Performance', 1),
(4, 'other', 'Other', 1);

-- --------------------------------------------------------

--
-- Table structure for table `default_tasks`
--

CREATE TABLE IF NOT EXISTS `default_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `id_parent` int(11) DEFAULT NULL,
  `billable` enum('Yes','No') NOT NULL,
  `id_maintenance` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_parent` (`id_parent`) USING BTREE,
  KEY `id_maintenance` (`id_maintenance`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=160 ;

--
-- Dumping data for table `default_tasks`
--

INSERT INTO `default_tasks` (`id`, `name`, `id_parent`, `billable`, `id_maintenance`) VALUES
(1, 'Trainings', NULL, 'Yes', NULL),
(2, 'Leaves', NULL, 'Yes', NULL),
(3, 'Core Tech', NULL, 'Yes', NULL),
(4, 'Management', NULL, 'Yes', NULL),
(5, 'Sales', NULL, 'Yes', NULL),
(6, 'Meetings', NULL, 'Yes', NULL),
(7, 'Internal Projects', NULL, 'Yes', NULL),
(8, 'Recruitment', NULL, 'Yes', NULL),
(9, 'Customer Training', 1, 'Yes', NULL),
(10, 'In-House Training', 1, 'No', NULL),
(11, 'Day Off', 2, 'Yes', NULL),
(12, 'Sick Leave', 2, 'Yes', NULL),
(13, 'Vacation', 2, 'Yes', NULL),
(14, 'Public Holiday', 2, 'Yes', NULL),
(15, 'Emergency Leave', 2, 'Yes', NULL),
(16, 'HW Sizing', 3, 'Yes', NULL),
(17, 'SW/HW Maintenance', 3, 'Yes', NULL),
(18, 'Team Management', 4, 'Yes', NULL),
(19, 'Calls', 5, 'Yes', NULL),
(20, 'Proposals', 5, 'Yes', NULL),
(21, 'Demo', 5, 'Yes', NULL),
(22, 'Internal Meetings', 6, 'Yes', NULL),
(23, 'Internal Projects', 7, 'Yes', NULL),
(24, 'Screening', 8, 'Yes', NULL),
(25, 'Interviews', 8, 'Yes', NULL),
(26, 'Test Review', 8, 'Yes', NULL),
(27, 'Support', NULL, 'Yes', NULL),
(28, 'A. N. Boukhater - Infor WMS yearly Maintenance and Upgrade starting 17/06/2015 - 16/06/2016', 27, 'Yes', NULL),
(29, 'A. N. Boukhater - Infor WMS yearly Maintenance and Upgrade starting 17/06/2015 - 16/06/2016', 27, 'Yes', NULL),
(30, 'Airlink - Infor WMS S&U for One Year starting from 01/01/2015 until 31/12/2015', 27, 'Yes', NULL),
(31, 'Airlink - Infor yearly S&U for 16 additional licenses from 01/02/2015 - 30/01/2016', 27, 'Yes', NULL),
(32, 'A. N. Boukhater - Infor WMS yearly Maintenance and Upgrade starting 17/06/2015 - 16/06/2016', 27, 'Yes', NULL),
(33, 'A. N. Boukhater - Infor WMS yearly Maintenance and Upgrade starting 17/06/2015 - 16/06/2016', 27, 'Yes', NULL),
(34, 'A. N. Boukhater - Infor WMS yearly Maintenance and Upgrade starting 17/06/2015 - 16/06/2016', 27, 'Yes', NULL),
(35, 'A. N. Boukhater - Infor WMS yearly Maintenance and Upgrade starting 17/06/2015 - 16/06/2016', 27, 'Yes', NULL),
(36, 'A. N. Boukhater - Infor WMS yearly Maintenance and Upgrade starting 17/06/2015 - 16/06/2016', 27, 'Yes', NULL),
(37, 'A. N. Boukhater - Infor WMS yearly Maintenance and Upgrade starting 17/06/2015 - 16/06/2016', 27, 'Yes', NULL),
(38, 'Airlink - Boomi Support and Upgrade for the period of one year from 14/02/2015 to 13/02/2016', 27, 'Yes', NULL),
(39, 'Ahmad Tea - Infor WMS Support & Upgrade for One Year starting from 11/03/2014 - 10/03/2015', 27, 'Yes', NULL),
(40, 'Anham - Infor WMS, Ramp, Portal & LMR Yearly S&U from 01 Jan 2015 till 31 Dec 2015', 27, 'Yes', NULL),
(41, 'Anham - Infor WMS, Ramp, Portal & LMR Yearly S&U from 01 Jan 2015 till 31 Dec 2015', 27, 'Yes', NULL),
(42, 'AKI - Infor WMS Support & Upgrade for One year starting from 26/06/2015 to 25/06/2016', 27, 'Yes', NULL),
(43, 'A. N. Boukhater - Infor WMS yearly Maintenance and Upgrade starting 17/06/2015 - 16/06/2016', 27, 'Yes', NULL),
(44, 'A. N. Boukhater - Infor WMS yearly Maintenance and Upgrade starting 17/06/2015 - 16/06/2016', 27, 'Yes', NULL),
(45, 'Testing - test', 27, 'Yes', NULL),
(46, 'Testing - test', 27, 'Yes', NULL),
(47, 'Testing - test', 27, 'Yes', NULL),
(48, 'Testing - Infor WMS AMC', 27, 'Yes', NULL),
(49, 'A. N. Boukhater - Infor WMS yearly Maintenance and Upgrade starting 17/06/2015 - 16/06/2016', 27, 'Yes', NULL),
(50, 'A. N. Boukhater - Infor WMS yearly Maintenance and Upgrade starting 17/06/2015 - 16/06/2016', 27, 'Yes', NULL),
(51, 'A. N. Boukhater - Infor WM AMC', 27, 'Yes', NULL),
(52, 'A. N. Boukhater - SIP AMC', 27, 'Yes', 11),
(53, 'A. N. Boukhater - Infor WM AMC', 27, 'Yes', NULL),
(54, 'Airlink - Infor WM 3.7 AMC', 27, 'Yes', NULL),
(55, 'Airlink - Infor WM 3.7 AMC', 27, 'Yes', 13),
(56, 'Airlink - Boomi AMC', 27, 'Yes', NULL),
(57, 'Airlink - Boomi AMC', 27, 'Yes', NULL),
(58, 'Airlink - Boomi AMC', 27, 'Yes', 15),
(59, 'Ahmad Tea - Infor WM AMC', 27, 'Yes', 16),
(60, 'Ahmad Tea - Infor WM AMC Additional Users', 27, 'Yes', 17),
(61, 'Anham - Infor WM AMC', 27, 'Yes', 18),
(62, 'Anham - Infor WM AMC KWT', 27, 'Yes', 18),
(63, 'Ahmad Tea - Infor WM AMC Additional Users', 27, 'Yes', 17),
(64, 'Anham - Infor WM AMC - Afgn', 27, 'Yes', 19),
(65, 'Anham - Ramp AMC', 27, 'Yes', 20),
(66, 'Anham - Ramp AMC', 27, 'Yes', 20),
(67, 'Anham - Portal AMC', 27, 'Yes', 21),
(68, 'AKI - Infor WM AMC', 27, 'Yes', 22),
(69, 'Al Yasra - Infor WM AMC - KWT', 27, 'Yes', 23),
(70, 'Al Yasra - Boomi AMC', 27, 'Yes', NULL),
(71, 'Al Yasra - Boomi AMC', 27, 'Yes', 25),
(72, 'Al Yasra - Infor WM AMC - KWT Additional', 27, 'Yes', 26),
(73, 'Al Yasra - Infor WM AMC - KSA', 27, 'Yes', 27),
(74, 'ABA - Infor WM AMC', 27, 'Yes', 28),
(75, 'ABA - Boomi AMC', 27, 'Yes', 29),
(76, 'ABA - Infor EM AMC', 27, 'Yes', 30),
(77, 'Al Talayi - Infor WM AMC', 27, 'Yes', 31),
(78, 'Al Talayi - Boomi AMC', 27, 'Yes', 32),
(79, 'Americana Meat KSA - Infor WM AMC', 27, 'Yes', 33),
(80, 'Americana Meat KSA - Infor WM AMC Americana Rest', 27, 'Yes', 34),
(81, 'Aramex - Infor WM AMC - Egypt', 27, 'Yes', 35),
(82, 'Aramex - Infor WM AMC - support UAE & AMMAN', 27, 'Yes', 36),
(83, 'Aramex - Infor WM AMC - support UAE & AMMAN', 27, 'Yes', 36),
(84, 'Aramex - Infor WM AMC - Support UAE & AMMAN', 27, 'Yes', 37),
(85, 'Aramex - Infor WM AMC 30 Additional Users', 27, 'Yes', 38),
(86, 'Aramex - Infor WM AMC 20 Additional Users', 27, 'Yes', 39),
(87, 'Aramex - Boomi AMC', 27, 'Yes', NULL),
(88, 'Aramex - Boomi AMC', 27, 'Yes', 41),
(89, 'Aramex - Portal AMC', 27, 'Yes', 42),
(90, 'Aramex - BOOMI AS2 Adapter', 27, 'Yes', 43),
(91, 'Areej - Infor WM AMC', 27, 'Yes', 44),
(92, 'Areej - Boomi AMC', 27, 'Yes', 45),
(93, 'Arrow - Infor WM AMC', 27, 'Yes', 46),
(94, 'Arrow - Infor WM AMC Additional 18 Licenses', 27, 'Yes', 47),
(95, 'Ayezan - Infor WM AMC', 27, 'Yes', 48),
(96, 'Ayezan - Boomi AMC', 27, 'Yes', 49),
(97, 'Wilhelmsen - Infor WM AMC', 27, 'Yes', 50),
(98, 'Wilhelmsen - Boomi AMC', 27, 'Yes', 51),
(99, 'Al Bayader - Infor WM AMC', 27, 'Yes', NULL),
(100, 'Al Bayader - Infor WM AMC', 27, 'Yes', 53),
(101, 'Al Bayader - Boomi AMC', 27, 'Yes', 54),
(102, 'Al Bayader - Portal AMC', 27, 'Yes', 55),
(103, 'BPC - Infor WM AMC', 27, 'Yes', 56),
(104, 'BPC - Boomi AMC', 27, 'Yes', 57),
(105, 'Radec - Infor WM AMC', 27, 'Yes', 58),
(106, 'Radec - Infor WM AMC Additional Users', 27, 'Yes', 59),
(107, 'Danzas - Infor WM AMC', 27, 'Yes', 60),
(108, 'Danzas - Boomi AMC', 27, 'Yes', 61),
(109, 'Danzas - Infor WM AMC Huawei', 27, 'Yes', 62),
(110, 'Deal Logistics - Infor WM AMC', 27, 'Yes', 63),
(111, 'Deal Logistics - Infor WM AMC Additional Licenses', 27, 'Yes', 64),
(112, 'Deal Logistics - Boomi AMC', 27, 'Yes', 65),
(113, 'Deal Logistics - Portal AMC', 27, 'Yes', 66),
(114, 'DSS - Infor WM AMC', 27, 'Yes', 67),
(115, 'DIB - Boomi AMC', 27, 'Yes', 68),
(116, 'EasyLog - Infor WM AMC', 27, 'Yes', 69),
(117, 'DHL - Infor WM AMC', 27, 'Yes', 70),
(118, 'DHL - Boomi AMC', 27, 'Yes', 71),
(119, 'Glow - Infor WM AMC', 27, 'Yes', 72),
(120, 'Glow - Boomi AMC', 27, 'Yes', 73),
(121, 'Glow - Portal AMC', 27, 'Yes', 74),
(122, 'GMG - Infor WM AMC', 27, 'Yes', 75),
(123, 'GSL - Boomi AMC', 27, 'Yes', 76),
(124, 'GWC - Infor WM AMC', 27, 'Yes', 77),
(125, 'Golden Food - Infor WM AMC', 27, 'Yes', 78),
(126, 'Golden Food - SIP AMC', 27, 'Yes', 79),
(127, 'Holdal - Infor WM AMC 4 Additional Licenses', 27, 'Yes', 80),
(128, 'Holdal - Infor WM AMC - 5Additional Licenses', 27, 'Yes', 81),
(129, 'Holdal - Infor WM AMC', 27, 'Yes', 82),
(130, 'Holdal - Boomi AMC', 27, 'Yes', 83),
(131, 'Holdal - Infor WM AMC - 22 Additional Licenses', 27, 'Yes', 84),
(132, 'IATCO - Infor WM AMC', 27, 'Yes', 85),
(133, 'IATCO - Infor WM AMC', 27, 'Yes', 86),
(134, 'IATCO - Boomi AMC', 27, 'Yes', 87),
(135, 'Inchcape - Infor WM AMC DLA', 27, 'Yes', 88),
(136, 'Intercol - Infor WM AMC', 27, 'Yes', 89),
(137, 'Inchcape - Infor WM AMC', 27, 'Yes', 90),
(138, 'Inchcape - Boomi AMC', 27, 'Yes', 91),
(139, 'Jawad - Infor WM AMC', 27, 'Yes', 92),
(140, 'Jawad - Boomi AMC', 27, 'Yes', 93),
(141, 'Jawad - Portal AMC', 27, 'Yes', 94),
(142, 'Testing - test', 27, 'Yes', 95),
(143, 'Testing - test', 27, 'Yes', 95),
(144, 'Testing - testtttt', 27, 'Yes', 96),
(145, 'Testing - testtttt', 27, 'Yes', 96),
(146, 'Testing - testtttt', 27, 'Yes', 96),
(147, 'Testing - testtttt', 27, 'Yes', 96),
(148, 'Testing - testtttt', 27, 'Yes', 96),
(149, 'Testing - testtttt', 27, 'Yes', 96),
(150, 'Testing - testtttt', 27, 'Yes', 96),
(151, 'Testing - testtttt', 27, 'Yes', 96),
(152, 'Testing - testtttt', 27, 'Yes', 96),
(153, 'Testing - testtttt', 27, 'Yes', 96),
(154, 'A. N. Boukhater - Test 2-2', 27, 'Yes', NULL),
(155, 'A. N. Boukhater - Test 2-2', 27, 'Yes', NULL),
(156, 'A. N. Boukhater - Test 2-2', 27, 'Yes', NULL),
(157, 'A. N. Boukhater - Test 2-2', 27, 'Yes', 98),
(158, 'A. N. Boukhater - Infor WM AMC', 27, 'Yes', 99),
(159, 'A. N. Boukhater - Infor WM AMC', 27, 'Yes', 99);

-- --------------------------------------------------------

--
-- Table structure for table `default_tasks_group`
--

CREATE TABLE IF NOT EXISTS `default_tasks_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_group` int(11) NOT NULL,
  `id_default_task` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_group` (`id_group`) USING BTREE,
  KEY `id_default_task` (`id_default_task`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE IF NOT EXISTS `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_model` int(11) NOT NULL,
  `model_table` varchar(50) NOT NULL,
  `id_category` int(11) NOT NULL,
  `document_title` varchar(500) NOT NULL,
  `uploaded` datetime DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `description` text,
  `file` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_contracts_customers1_idx` (`id_model`) USING BTREE,
  KEY `uploaded_by` (`uploaded_by`) USING BTREE,
  KEY `id_category` (`id_category`) USING BTREE,
  KEY `id_model` (`id_model`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `id_model`, `model_table`, `id_category`, `document_title`, `uploaded`, `uploaded_by`, `description`, `file`) VALUES
(1, 5, 'users', 10, 'University Certificate', '2014-08-28 12:57:20', 4, '', 'C_ALEXANDRE_TARABAY.jpg'),
(2, 5, 'users', 11, 'Passport', '2014-08-28 12:58:47', 4, '', 'P_ALEXANDRE_TARABAY_2.pdf'),
(3, 5, 'users', 11, 'Passport', '2014-08-28 13:01:01', 4, '', 'P_ALEXANDRE_TARABAY_1.pdf'),
(4, 5, 'users', 12, 'Picture', '2014-08-28 13:02:11', 4, '', 'PH_ALEXANDRE_TARABAY.jpg'),
(5, 6, 'users', 10, 'University Certificate', '2014-08-28 13:13:25', 4, '', 'C_ALLEN_ZEIDAN.jpg'),
(6, 6, 'users', 11, 'Passport', '2014-08-28 13:13:57', 4, '', 'P_ALLEN_ZEIDAN.jpg'),
(7, 6, 'users', 12, 'Picture', '2014-08-28 13:14:25', 4, '', 'PH_ALLEN_ZEIDAN.jpg'),
(8, 7, 'users', 11, 'Passport', '2014-08-28 13:15:47', 4, '', 'P_ANTHONY_SAADEH.pdf'),
(9, 34, 'users', 10, 'University Certificate', '2014-08-28 13:17:42', 4, '', 'C_ANTOINE_OUDAIMY.jpg'),
(10, 34, 'users', 11, 'Passport', '2014-08-28 13:18:23', 4, '', 'P_ANTOINE_OUDAIMY-1.JPG'),
(11, 34, 'users', 11, 'Passport', '2014-08-28 13:18:49', 4, '', 'P_ANTOINE_OUDAIMY_2.jpg'),
(12, 8, 'users', 10, 'University Certificate', '2014-08-28 13:23:06', 4, '', 'C_BASSEL_KHODR.pdf'),
(13, 8, 'users', 11, 'Passport', '2014-08-28 13:23:37', 4, '', 'P_BASSEL_KHODR.pdf'),
(14, 8, 'users', 12, 'Picture', '2014-08-28 13:24:52', 4, '', 'PH_BASSEL_KHODR.jpg'),
(15, 9, 'users', 10, 'University Certificate', '2014-08-28 13:26:07', 4, '', 'C_BERNARD_KHAZZAKA.JPG'),
(16, 9, 'users', 11, 'Passport', '2014-08-28 13:26:28', 4, '', 'P_BERNARD_KHAZZAKA.pdf'),
(17, 9, 'users', 12, 'Picture', '2014-08-28 13:27:03', 4, '', 'PH_BERNARD_KHAZZAKA.JPG'),
(18, 10, 'users', 10, 'University Certificate', '2014-08-28 13:28:20', 4, '', 'C_CHARBEL_AZZI.jpg'),
(19, 10, 'users', 11, 'Passport', '2014-08-28 13:28:50', 4, '', 'P_CHARBEL AZZI.pdf'),
(20, 10, 'users', 12, 'Picture', '2014-08-28 13:29:16', 4, '', 'PH_CHARBEL_AZZI.jpg'),
(21, 11, 'users', 11, 'Passport', '2014-08-28 13:30:53', 4, '', 'P_Claudine_Daaboul.pdf'),
(22, 11, 'users', 12, 'Picture', '2014-08-28 13:32:05', 4, '', 'PH_CLAUDINE.jpg'),
(23, 12, 'users', 10, 'University Certificate', '2014-08-28 13:34:54', 4, '', 'C_DENISE_IBRAHIM.pdf'),
(24, 12, 'users', 11, 'Passport', '2014-08-28 13:35:33', 4, '', 'P_DENISE_IBRAHIM.pdf'),
(25, 12, 'users', 12, 'Picture', '2014-08-28 13:36:06', 4, '', 'PH_DENISE_IBRAHIM.jpg'),
(26, 13, 'users', 10, 'University Certificate', '2014-08-28 13:37:27', 4, '', 'C_DIANA JABBOUR.pdf'),
(27, 13, 'users', 11, 'Passport', '2014-08-28 13:40:52', 4, '', 'P_DIANA JABBOUR.pdf'),
(28, 13, 'users', 12, 'Picture', '2014-08-28 13:41:16', 4, '', 'PH_DIANA JABBOUR.jpg'),
(29, 14, 'users', 10, 'University Certificate', '2014-08-28 13:43:25', 4, '', 'C_EMILE_BASSIL.jpg'),
(30, 14, 'users', 11, 'Passport', '2014-08-28 13:47:01', 4, '', 'P_EMILE_BASSIL_LB.jpg'),
(31, 14, 'users', 11, 'Passport', '2014-08-28 13:47:30', 4, '', 'P_EMILE_BASSIL_AUSTRALIAN.jpg'),
(32, 14, 'users', 12, 'Picture', '2014-08-28 13:49:19', 4, '', 'PH_EMILE_BASSIL.jpg'),
(33, 15, 'users', 10, 'University Certificate', '2014-08-28 13:51:26', 4, '', 'C_GHINA_KARAME.JPG'),
(34, 15, 'users', 11, 'Passport', '2014-08-28 13:57:28', 4, '', 'P_GHINA_KARAME.JPG'),
(35, 15, 'users', 12, 'Picture', '2014-08-28 13:58:07', 4, '', 'PH_GHINA_KARAME.jpg'),
(36, 16, 'users', 10, 'University Certificate', '2014-08-28 14:45:20', 4, '', 'C_HUSSEIN_NAIM_1.JPG'),
(37, 16, 'users', 10, 'University Certificate', '2014-08-28 14:45:35', 4, '', 'C_HUSSEIN_NAIM_2.JPG'),
(38, 16, 'users', 11, 'Passport', '2014-08-28 14:46:26', 4, '', 'P_HUSSEIN_NAIM.pdf'),
(39, 16, 'users', 12, 'Picture', '2014-08-28 14:46:49', 4, '', 'PH_HUSSEIN_NAIM.JPG'),
(40, 17, 'users', 10, 'University Certificate', '2014-08-28 14:50:08', 4, '', 'C_JOSEPH_RAHME.JPG'),
(41, 17, 'users', 11, 'Passport', '2014-08-28 14:50:27', 4, '', 'P_JOSEPH_RAHME.pdf'),
(42, 17, 'users', 12, 'Picture', '2014-08-28 14:51:21', 4, '', 'PH_JOSEPH_RAHME.jpg'),
(43, 18, 'users', 10, 'University Certificate', '2014-08-28 14:52:37', 4, '', 'C_MARIO_GHOSN_1.jpg'),
(44, 18, 'users', 10, 'University Certificate', '2014-08-28 14:52:51', 4, '', 'C_MARIO_GHOSN_2.jpg'),
(45, 18, 'users', 11, 'Passport', '2014-08-28 14:53:22', 4, '', 'P_MARIO_GHOSN.pdf'),
(46, 18, 'users', 11, 'Passport', '2014-08-28 14:53:54', 4, '', 'P_MARIO GHOSN_GREECE_NEW.pdf'),
(47, 18, 'users', 12, 'Picture', '2014-08-28 14:54:24', 4, '', 'PH_MARIO_GHOSN.jpg'),
(48, 20, 'users', 10, 'University Certificate', '2014-08-28 14:57:03', 4, '', 'C_MOHAMAD_OBAIDAH.JPG'),
(49, 20, 'users', 11, 'Passport', '2014-08-28 14:57:27', 4, '', 'P_MOHAMAD_OBAIDAH.jpg'),
(50, 20, 'users', 12, 'Picture', '2014-08-28 14:58:40', 4, '', 'PH_MOHAMAD_OBAIDAH..jpg'),
(51, 21, 'users', 10, 'University Certificate', '2014-08-28 15:00:03', 4, '', 'C_MOHAMAD_ITANI.jpg'),
(52, 21, 'users', 11, 'Passport', '2014-08-28 15:00:32', 4, '', 'P_MOHAMAD_ITANI.pdf'),
(53, 21, 'users', 12, 'Picture', '2014-08-28 15:00:57', 4, '', 'PH_MOHAMAD_ITANI.jpg'),
(54, 22, 'users', 10, 'University Certificate', '2014-08-28 15:01:29', 4, '', 'C_NADIM_KLAT.pdf'),
(55, 22, 'users', 11, 'Passport', '2014-08-28 15:01:48', 4, '', 'P_NADIM_KLAT.jpg'),
(56, 22, 'users', 12, 'Picture', '2014-08-28 15:02:10', 4, '', 'PH_NADIM_KLAT.jpg'),
(57, 24, 'users', 10, 'University Certificate', '2014-08-28 15:04:48', 4, '', 'C_NAJI_ABDELKHALEK.pdf'),
(58, 24, 'users', 11, 'Passport', '2014-08-28 15:05:20', 4, '', 'P_NAJI ABDELKHALEK.pdf'),
(59, 24, 'users', 12, 'Picture', '2014-08-28 15:05:50', 4, '', 'PH_NAJI_ABDELKHALEK.jpg'),
(60, 25, 'users', 10, 'University Certificate', '2014-08-28 15:07:49', 4, '', 'C_PAUL_DONIKIAN.pdf'),
(61, 25, 'users', 11, 'Passport', '2014-08-28 15:08:20', 4, '', 'P_PAUL_DONIKIAN.pdf'),
(62, 25, 'users', 11, 'Passport', '2014-08-28 15:08:43', 4, '', 'P_PAUL DONIKIAN_Renewal.pdf'),
(63, 25, 'users', 12, 'Picture', '2014-08-28 15:09:24', 4, '', 'PH_PAUL_DONIKIAN.jpg'),
(64, 26, 'users', 10, 'University Certificate', '2014-08-28 15:19:44', 4, '', 'C_RAMI_ALLAM.pdf'),
(65, 26, 'users', 11, 'Passport', '2014-08-28 15:20:16', 4, '', 'P_RAMI_ALLAM_1.pdf'),
(66, 26, 'users', 11, 'Passport', '2014-08-28 15:20:33', 4, '', 'P_RAMI_ALLAM_2.pdf'),
(67, 26, 'users', 12, 'Picture', '2014-08-28 15:21:01', 4, '', 'PH_RAMI_ALLAM.jpg'),
(68, 3, 'users', 11, 'Passport', '2014-08-28 15:22:49', 4, '', 'P_RAMI_KHATTAR.pdf'),
(69, 28, 'users', 10, 'University Certificate', '2014-08-28 15:25:09', 4, '', 'C_RAMZI_BALLOUT.JPG'),
(70, 28, 'users', 11, 'Passport', '2014-08-28 15:25:35', 4, '', 'P_RAMZI_BALLOUT.JPG'),
(71, 28, 'users', 12, 'Picture', '2014-08-28 15:25:55', 4, '', 'PH_RAMZI_BALLOUT.jpg'),
(72, 29, 'users', 10, 'University Certificate', '2014-08-28 15:27:14', 4, '', 'C_SAMER_SAAD.pdf'),
(73, 29, 'users', 11, 'Passport', '2014-08-28 15:27:58', 4, '', 'P_SAMER_SAAD.JPG'),
(74, 29, 'users', 12, 'Picture', '2014-08-28 15:28:22', 4, '', 'PH_SAMER_SAAD.jpg'),
(75, 30, 'users', 10, 'University Certificate', '2014-08-28 15:29:52', 4, '', 'C_SERGE_SLAIBY.jpg'),
(76, 30, 'users', 11, 'Passport', '2014-08-28 15:31:31', 4, '', 'P_SERGE _SLAIBY.pdf'),
(77, 30, 'users', 12, 'Picture', '2014-08-28 15:31:50', 4, '', 'PH_SERGE_SLAIBY.jpg'),
(78, 31, 'users', 10, 'University Certificate', '2014-08-28 15:33:14', 4, '', 'C_SIMON_KOSSEIFI.pdf'),
(79, 31, 'users', 11, 'Passport', '2014-08-28 15:33:45', 4, '', 'P_SIMON_KOSSEIFI_1.JPG'),
(80, 31, 'users', 11, 'Passport', '2014-08-28 15:34:05', 4, '', 'P_SIMON_KOSSEIFI_2.jpg'),
(81, 31, 'users', 12, 'Picture', '2014-08-28 15:34:28', 4, '', 'PH_SIMON_KOSSEIFI.jpg'),
(82, 32, 'users', 10, 'University Certificate', '2014-08-28 15:35:46', 4, '', 'C_TAREK_HUSSEINI.pdf'),
(83, 32, 'users', 11, 'Passport', '2014-08-28 15:36:12', 4, '', 'P_TAREK_HUSSEINI.jpg'),
(84, 32, 'users', 12, 'Picture', '2014-08-28 15:36:27', 4, '', 'PH_TAREK_HUSSEINI.jpg'),
(85, 33, 'users', 10, 'University Certificate', '2014-08-28 15:37:41', 4, '', 'C_TEDDY_RICHA_2.jpg'),
(86, 33, 'users', 10, 'University Certificate', '2014-08-28 15:38:13', 4, '', 'C_TEDDY_RICHA_1.jpg'),
(87, 33, 'users', 11, 'Passport', '2014-08-28 15:38:47', 4, '', 'P_TEDDY_RICHA_1.jpg'),
(88, 33, 'users', 11, 'Passport', '2014-08-28 15:39:03', 4, '', 'P_TEDDY_RICHA_2.jpg'),
(89, 33, 'users', 12, 'Picture', '2014-08-28 15:39:20', 4, '', 'PH_TEDDY_RICHA.jpg'),
(90, 35, 'users', 10, 'University Certificate', '2014-08-28 15:40:26', 4, '', 'C_WAEL_MABSOUT_1.jpg'),
(91, 35, 'users', 10, 'University Certificate', '2014-08-28 15:40:42', 4, '', 'C_WAEL_MABSOUT_2.jpg'),
(92, 35, 'users', 11, 'Passport', '2014-08-28 15:41:04', 4, '', 'P_WAEL_MABSOUT.jpg'),
(93, 35, 'users', 12, 'Picture', '2014-08-28 15:41:20', 4, '', 'PH_WAEL_MABSOUT.jpg'),
(94, 7, 'users', 10, 'University Certificate', '2014-08-28 15:48:22', 4, '', 'C_ANTHONY_SAADEH.pdf'),
(95, 7, 'users', 12, 'Picture', '2014-08-28 15:48:52', 4, '', 'PH_ANTHONY_SAADEH.pdf'),
(96, 34, 'users', 12, 'Picture', '2014-08-28 15:59:02', 4, '', 'PH_ANTOINE_OUDAIMY.jpg'),
(97, 3, 'users', 12, 'Picture', '2014-08-28 16:03:45', 4, '', 'PH_RAMY_KHATTAR.pdf'),
(98, 19, 'users', 11, 'Passport', '2014-08-28 16:05:36', 4, '', 'Micha_Passport.pdf'),
(99, 119, 'projects', 19, 'test', '2014-10-09 10:22:35', 3, 'test', 'down.png');

-- --------------------------------------------------------

--
-- Table structure for table `documents_categories`
--

CREATE TABLE IF NOT EXISTS `documents_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL,
  `category` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `id_parent` int(11) NOT NULL DEFAULT '0',
  `item_order` int(11) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `id_parent` (`id_parent`) USING BTREE,
  KEY `category` (`category`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `documents_categories`
--

INSERT INTO `documents_categories` (`id`, `module`, `category`, `name`, `id_parent`, `item_order`) VALUES
(1, 'customers', NULL, 'Contracts', 0, 1),
(2, 'customers', NULL, 'Invoices', 0, 2),
(3, 'customers', NULL, 'Proposals', 0, 0),
(4, 'customers', NULL, 'Misc', 0, 3),
(5, 'customers', NULL, 'SoA', 0, 4),
(10, 'users', NULL, 'Certificates', 0, 0),
(11, 'users', NULL, 'Passport', 0, 1),
(12, 'users', NULL, 'Pictures', 0, 2),
(13, 'users', NULL, 'Miscellaneous', 0, 3),
(14, 'projects', NULL, 'Agendas', 0, 0),
(15, 'projects', NULL, 'FBRs', 0, 1),
(16, 'projects', NULL, 'Integration', 0, 2),
(17, 'projects', NULL, 'Status reports', 0, 3),
(18, 'projects', NULL, 'Issue List', 0, 4),
(19, 'projects', NULL, 'EAs', 0, 5),
(20, 'projects', NULL, 'Project Check List', 0, 6),
(21, 'projects', 26, 'Agendas', 0, 0),
(22, 'projects', 26, 'Project Data', 0, 1),
(23, 'projects', 26, 'Final Deliverables', 0, 2),
(24, 'projects', 26, 'Status Report', 0, 3),
(25, 'projects', 26, 'EAs', 0, 4),
(26, 'users', NULL, 'Profile Pic', 0, 4);

-- --------------------------------------------------------

--
-- Table structure for table `ea_payment_terms`
--

CREATE TABLE IF NOT EXISTS `ea_payment_terms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ea` int(11) NOT NULL,
  `payment_term` double NOT NULL,
  `amount` double NOT NULL,
  `milestone` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `milestone` (`milestone`) USING BTREE,
  KEY `id_ea` (`id_ea`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `ea_payment_terms`
--

INSERT INTO `ea_payment_terms` (`id`, `id_ea`, `payment_term`, `amount`, `milestone`) VALUES
(23, 19, 100, 1000, 72),
(24, 20, 100, 1000, 73),
(25, 21, 100, 10000, 72),
(26, 22, 100, 5000, 72),
(27, 23, 100, 10000, 36),
(28, 24, 100, 1000, 35),
(29, 27, 100, 1000, 73),
(30, 31, 100, 1000, 72);

-- --------------------------------------------------------

--
-- Table structure for table `eas`
--

CREATE TABLE IF NOT EXISTS `eas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer` int(11) NOT NULL,
  `ea_number` varchar(5) NOT NULL,
  `description` text NOT NULL,
  `id_project` int(11) DEFAULT NULL,
  `id_parent_project` int(11) DEFAULT NULL,
  `currency` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `approved` datetime DEFAULT NULL,
  `file` varchar(500) NOT NULL,
  `category` int(11) NOT NULL,
  `customer_lpo` varchar(50) NOT NULL,
  `discount` double NOT NULL,
  `expense` varchar(255) NOT NULL DEFAULT 'N/A',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_eas_customers1_idx` (`id_customer`) USING BTREE,
  KEY `author` (`author`) USING BTREE,
  KEY `category` (`category`) USING BTREE,
  KEY `currency` (`currency`) USING BTREE,
  KEY `project_name` (`id_project`) USING BTREE,
  KEY `id_project` (`id_project`) USING BTREE,
  KEY `id_parent_project` (`id_parent_project`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `eas`
--

INSERT INTO `eas` (`id`, `id_customer`, `ea_number`, `description`, `id_project`, `id_parent_project`, `currency`, `status`, `author`, `created`, `approved`, `file`, `category`, `customer_lpo`, `discount`, `expense`, `start_date`, `end_date`) VALUES
(19, 65, '00019', 'test', NULL, NULL, 9, 1, 3, '2014-09-26 09:19:25', NULL, '', 24, '', 0, '', '2014-09-08', '2014-09-18'),
(20, 1, '00020', 'boomi', NULL, NULL, 9, 1, 3, '2014-09-26 09:28:42', NULL, '', 25, '', 0, '', '0000-00-00', '0000-00-00'),
(21, 1, '00021', 'testing', NULL, NULL, 9, 1, 3, '2014-09-26 10:26:00', NULL, '', 25, '', 0, '', '0000-00-00', '0000-00-00'),
(22, 1, '00022', 'testing', NULL, NULL, 9, 1, 3, '2014-09-26 10:28:38', NULL, '', 24, '', 0, '', '2014-09-02', '2014-09-06'),
(23, 34, '00023', 'Test777', 128, NULL, 9, 1, 1, '2014-11-05 15:43:57', NULL, 'Capture.JPG', 26, '', 0, 'Actuals', '0000-00-00', '0000-00-00'),
(24, 32, '00024', 'Test', NULL, NULL, 9, 1, 32, '2014-11-05 20:54:18', NULL, '', 24, '', 0, '', '2014-11-11', '2014-11-01'),
(25, 50, '00000', '123', NULL, NULL, NULL, 1, 32, '2014-11-05 20:58:06', NULL, '', 25, '', 0, '', '0000-00-00', '0000-00-00'),
(26, 32, '00026', '123', NULL, NULL, 9, 1, 32, '2014-11-05 20:59:18', NULL, '', 25, '', 0, '', '0000-00-00', '0000-00-00'),
(27, 1, '00027', 'testing by Micha', NULL, NULL, 9, 1, 4, '2014-11-06 16:17:30', NULL, 'AUBMC_EA.xlsx', 25, '', 0, 'N/A', '0000-00-00', '0000-00-00'),
(28, 33, '00028', '78787', NULL, NULL, 9, 1, 18, '2014-11-07 10:56:40', NULL, '', 24, '', 0, '', '2014-11-03', '2014-11-04'),
(29, 33, '00029', 'uyuy', NULL, NULL, 9, 1, 18, '2014-11-07 10:59:43', NULL, '', 24, '', 0, '', '2014-11-03', '2014-11-12'),
(30, 112, '00030', 'Test', 129, NULL, 9, 1, 18, '2014-11-07 11:10:34', NULL, '', 26, '', 0, '', '0000-00-00', '0000-00-00'),
(31, 32, '00031', 'test', 130, NULL, 9, 1, 3, '2014-11-07 17:06:22', NULL, 'PB.png', 26, '100', 0, 'Actuals', '0000-00-00', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `eas_items`
--

CREATE TABLE IF NOT EXISTS `eas_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ea` int(11) NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `amount` double NOT NULL,
  `man_days` varchar(50) NOT NULL,
  `settings_codelist` enum('product','training_course','consultancy') CHARACTER SET utf8 DEFAULT NULL,
  `settings_codelkup` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_ea` (`id_ea`) USING BTREE,
  KEY `settings_codelkup` (`settings_codelkup`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `eas_items`
--

INSERT INTO `eas_items` (`id`, `id_ea`, `description`, `amount`, `man_days`, `settings_codelist`, `settings_codelkup`) VALUES
(19, 19, 'testing', 1000, '10', 'training_course', 217),
(20, 20, 'testing', 100, '10', 'product', 65),
(21, 21, 'test', 1000, '10', 'product', 65),
(22, 22, 'testing 2', 5000, '2', 'training_course', 214),
(23, 23, 'test', 10000, '10', 'consultancy', 242),
(24, 24, 'test', 1000, '1', 'training_course', 217),
(25, 27, 'testing micha', 1000, '1', 'product', 65),
(26, 29, '5656', 5000, '5', 'training_course', 207),
(27, 31, 'teasdasd', 1000, '2', 'consultancy', 243);

-- --------------------------------------------------------

--
-- Table structure for table `eas_notes`
--

CREATE TABLE IF NOT EXISTS `eas_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ea` int(11) NOT NULL,
  `id_note` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_note` (`id_note`) USING BTREE,
  KEY `id_ea` (`id_ea`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `eas_notes`
--

INSERT INTO `eas_notes` (`id`, `id_ea`, `id_note`) VALUES
(1, 27, 227);

-- --------------------------------------------------------

--
-- Table structure for table `email_notifications`
--

CREATE TABLE IF NOT EXISTS `email_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unique_name` varchar(50) NOT NULL,
  `name` varchar(250) NOT NULL,
  `message` text NOT NULL,
  `module` varchar(25) NOT NULL,
  `not_in_groups` int(1) NOT NULL DEFAULT '0',
  `name_tab` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `email_notifications`
--

INSERT INTO `email_notifications` (`id`, `unique_name`, `name`, `message`, `module`, `not_in_groups`, `name_tab`) VALUES
(1, 'ea_new', 'Send E-mail when EA is Created', 'The Following EA has been Created:<br />\r\n<a href="{url}">EA #{ea_number}</a><br />\r\nDescription: {description}<br />\r\nCustomer: {customer_name}<br />{project_name}\r\nAuthor: {author}<br />\r\nTotal Amount: {total_amount} {currency}<br />\r\nDiscount: {discount}<br />\r\nTotal Net Amount: {total_net_amount} {currency}<br />\r\n{total_man_days_by_category}<br />\r\n{net_man_day_rate_by_category}', 'eas', 0, NULL),
(2, 'ea_approved', 'Send E-mail when EA is Approved', 'The Following EA has been Approved:<br />\r\n<a href="{url}">EA #{ea_number}</a><br />\r\nDescription: {description}<br />\r\nCustomer: {customer_name}<br />{project_name}\r\nAuthor: {author}<br />\r\nTotal Amount: {total_amount} {currency}<br />\r\nDiscount: {discount}<br />\r\nTotal Net Amount: {total_net_amount} {currency}<br />\r\n{total_man_days_by_category}<br />\r\n{net_man_day_rate_by_category}', 'eas', 0, NULL),
(3, 'ea_cancelled', 'Send E-mail when EA is Cancelled', 'The Following EA has been Cancelled:<br />\r\n<a href="{url}">EA #{ea_number}</a><br />\r\nDescription: {description}<br />\r\nCustomer: {customer_name}<br />{project_name}\r\nAuthor: {author}<br />\r\nTotal Amount: {total_amount} {currency}<br />\r\nDiscount: {discount}<br />\r\nTotal Net Amount: {total_net_amount} {currency}<br />\r\n{total_man_days_by_category}<br />\r\n{net_man_day_rate_by_category}', 'eas', 0, NULL),
(4, 'expenses_submitted', 'Expense Sheet ID {no} Submitted', 'Expense Sheet ID {no_url} with total amount of {amount} USD has been Submitted by: {username}<br/>\r\nThe following expense sheet entries are billable:<br/>\r\n{billableItems}\r\n\r\nDetails:<br/>\r\n- Total Amount:{amount} USD<br/>\r\n- Payable Amount: {payable} USD<br/>\r\n- Non-Payable Amount: {not_payable} USD<br/>\r\n- Billable Amount: {billable} USD<br/>\r\n- Non-Billable Amount: {not_billable} USD<br/>\r\n', 'expenses', 0, NULL),
(5, 'expenses_paid', 'Expense Sheet ID {no} Paid', 'Expense Sheet ID {no_url} submitted by {username} has been Paid by the {currentUser}<br/>\r\n\r\nDetails:<br/>\r\n- Total Amount:{amount} USD<br/>\r\n- Payable Amount: {payable} USD<br/>\r\n- Non-Payable Amount: {not_payable} USD<br/>\r\n- Billable Amount: {billable} USD<br/>\r\n- Non-Billable Amount: {not_billable} USD<br/>', 'expenses', 0, NULL),
(6, 'expenses_approved', 'Expense Sheet ID {no} Approved', 'Expense Sheet {no_url} submitted by {username} has been Approved by {currentUser}<br/>\r\n\r\nDetails:</p>\r\n- Total Amount:{amount} USD<br/>\r\n- Payable Amount: {payable} USD<br/>\r\n- Non-Payable Amount: {not_payable} USD<br/>\r\n- Billable Amount: {billable} USD<br/>\r\n- Non-Billable Amount: {not_billable} USD<br/>', 'expenses', 0, NULL),
(7, 'expenses_rejected', 'Expense Sheet ID {no} Rejected', 'Expense Sheet ID {no_url} submitted by {username} has been Rejected by  {currentUser} for the following reason: <br/><p>{reason}</p>', 'expenses', 0, NULL),
(8, 'project_assigned_act', 'New Project Assignment', '<p>Hi {firstname},\r\n\r\nYou have been assigned to a new tasks related to {eaCategory} project for customer {customer} in {country}.\r\n\r\nIn case of travel please make sure to collect expense receipts during your trips.</p>', 'projects', 1, NULL),
(9, 'project_assigned_not', 'New Project Assignment', '<p>Hi {firstname},\r\n\r\nYou have been assigned to a new tasks related to {eaCategory} project for customer {customer} in {country}.\r\n\r\nIn case of travel the standard SNS per diem policy will apply on this project.</p>', 'projects', 1, NULL),
(10, 'milestone', '{projectname}- Milestone {number} is now Closed', '', 'projects', 0, NULL),
(11, 'unsubmitted_timesheets', 'Send E-mail with pending timesheets', '<p>The following time sheets need to be completed:</p>\r\n<p>{timesheets_pending}</p>', 'timesheets', 0, NULL),
(12, 'personal_unsubmitted_timesheets', 'Send E-mail with personal pending timesheets', 'Dear {name},</br>\r\nYou are late in submitting the below time sheets. Please make sure they are completed ASAP.<br>\r\n{timesheets_pending}</br>', 'timesheets', 1, NULL),
(13, 'invoices_to_print', 'Invoices To Print', 'The following invoice numbers are ready to be Printed:\r\n{invoices}Total Invoices per partner:\r\n{invoicesPartner}', 'invoices', 0, NULL),
(14, 'invoices_printed', '{sns} Invoice #{invoice_number}', 'Dear {bill_to_contact},<br/>\r\nPlease find enclosed Invoice number {invoice_number} related to Project {project_name} and EA#{ea_id}. We appreciate if the payment of the invoice can be made by the {date}.<br/>\r\nIn case you have any questions please feel free to contact me at nadine.daaboul@sns-emea.com<br/>\r\nBest Regards,\r\nNadine Daaboul\r\n', 'incoices', 0, NULL),
(15, 'requests_new', '{request_type} Request for {user_fullname}', '{user_fullname} has requested a "{request_type}" from {startDate} until {endDate}.\r\nPlease {approve_link} or {reject_link}', 'requests', 0, NULL),
(16, 'requests_approved', 'Your {request_type} Request  From {startDate} To {endDate} is Approved', '', 'requests', 0, NULL),
(17, 'requests_rejected', 'Your {request_type} request is Rejected', '', 'requests', 0, NULL),
(18, 'requests_hr_new', '{requests_hr_type}  Request for {requests_hr_fullname}', '{requests_hr_fullname} has requested a {requests_hr_type} to be completed by the {requests_hr_date}\r\n{requests_hr_note}\r\n\r\nPlease click here when the required documents are {requests_hr_link}', 'requests_hr', 0, NULL),
(19, 'requests_hr_completed', 'Your {requests_hr_type} is ready for collection', '', 'requests_hr', 0, NULL),
(20, 'support_desk_users', '{customer_name} - SR#{no} Update ', 'Dear {customer_name},\r\n\r\nPlease find below updated information related to SR#{no}\r\n\r\nDate: {date}\r\nComment From: SNS\r\nNew Comment: {comment}\r\nStatus:In Progress\r\n{history}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', 1, NULL),
(22, 'support_desk_customers', '{customer_name} - SR#{no} Update ', 'Dear {user_name},\r\n\r\nPlease find below updated information related to SR#{no} \r\n\r\nDate: {date}\r\nComment From: {customer_name}\r\nNew Comment: {comment}\r\nStatus:In Progress\r\n{history}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', 1, NULL),
(23, 'support_desk_close', '{customer_name} - SR#{no} Closed: {subject}', 'Dear {customer_name},\r\n\r\nPlease find below updated information related to SR#{no}\r\n\r\nDate: {date}\r\nComment From: {user_name}\r\nStatus: CLOSED\r\n\r\n{history}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', 1, NULL),
(24, 'support_desk_pending_info', '{customer_name} - SR#{no} Pending Info: {subject}', 'Dear {customer_name},\r\n\r\nPlease find below updated information related to SR#{no}\r\n\r\nDate: {date}\r\nComment From: {user_name}\r\nStatus: Pending Info\r\n\r\n{history}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', 1, NULL),
(25, 'support_desk_reopened', '{customer_name} - SR#{no} Reopened: {subject}', 'Dear {cs_representative},\r\n\r\nPlease find below updated information related to SR#{no}\r\n\r\nDate: {date}\r\nComment From: {customer_name}\r\nStatus: Reopened\r\n\r\n{history}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', 1, NULL),
(26, 'support_desk_assigned_to', 'SR#{no}  has been Re-assigned', 'Dear {customer_name},\r\n\r\nThis is to inform you that SR#{no} has been re-assigned to {user_name}.\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', 1, NULL),
(27, 'support_desk_new_sr', '{customer_name} - New SR#{no} : {subject}', 'Dear {customer_name},\r\n\r\nWe have received your Support Request and it has been logged by our support team. The issue has been assigned a severity {severity} with the following Support Request Number (SR#): {no}.\r\n\r\nWe would like to inform you that {cs_representative} ({cs_representative_email}) will be supporting and assisting you in resolving this issue.\r\n\r\nAs a reference to your support issue, please use the assigned SR number mentioned above. The Technical Consultant will contact you if further information is needed or when the issue is resolved.\r\n\r\nSR#:{no_link}\r\n\r\nSchema: {schema}\r\n\r\nEnvironment: {environment}\r\n\r\nLogged by: {customer_contact}\r\n\r\nLog Date: {date}\r\n\r\nIncident Description:\r\n{description}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', 1, NULL),
(28, 'support_desk_cron_close', '{customer_name} - SR#({ct}) To Confirm Close', 'Dear {customer_name},\r\n\r\nKindly Confirm Close the following SR({ct}): {list} \r\n\r\nRegards,\r\nSNS Customer Service\r\n\r\n', 'support_desk', 1, NULL),
(29, 'support_desk_cron_pending', '{customer_name} - SR#({ct}) with Pending Information', 'Dear {customer_name},\r\n\r\nKindly provide us with the information required to close the following SR({ct}): {list} \r\n\r\nRegards,\r\nSNS Customer Service\r\n\r\n', 'support_desk', 1, NULL),
(30, 'support_desk_weekly_performance', 'CS Weekly Performance Snapshot', 'Dear All,\r\n\r\nPlease find below a summary of the CS performance during the week starting {start_date} and ending on {end_date}:\r\n\r\n{list1}\r\nExceptions:\r\n{list_exceptions}\r\n{list_exceptions_2}\r\nTop 5 Customers:\r\n{list_customer}\r\nTop 3 CS Performers:\r\n{list_performers}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', 1, NULL),
(31, 'support_desk_weekly_summary', 'CS Weekly Status Report', 'Dear All,\r\n\r\nPlease find below a summary of the current CS SRs Status:\r\n\r\n1-SRs in status New: {ct_new}\r\n2-SRs In Progress: {ct_in_progress}\r\n\r\nCustomers with HIGH Strategic Rating:\r\n1-SRs in status New for more than 48 hours: {ct_high_new}\r\n2-SRs In Progress for more than 96 hours: {ct_high_in_progress}\r\n\r\nCustomers with MEDIUM Strategic Rating:\r\n1-SRs in status New for more than 48 hours: {ct_medium_new}\r\n2-SRs In Progress for more than 96 hours: {ct_medium_in_progress}\r\n\r\nCustomers with LOW Strategic Rating:\r\n1-SRs in status New for more than 48 hours: {ct_low_new}\r\n2-SRs In Progress for more than 96 hours: {ct_low_in_progress}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', 1, NULL),
(32, 'support_desk_general_permission', 'SR Update Notifications', '', 'support_desk', 0, NULL),
(34, 'support_desk_system_down', '{customer_name} - System Down SR#{no} : {subject}', 'System Down at {customer_name}\r\n\r\nSR#: {no}\r\n\r\nSchema: {schema}\r\n\r\nEnvironment: {environment}\r\n\r\nLogged by: {customer_contact}\r\n\r\nLog Date: {date}\r\n\r\nIncident Description:\r\n\r\n{description}\r\n\r\n', 'support_desk', 0, NULL),
(35, 'invoices_paid', 'Invoices Paid', 'Dear All,<br>\r\nThe following invoices have been paid:<br>\r\n{invoices}', 'invoices', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_notifications_groups`
--

CREATE TABLE IF NOT EXISTS `email_notifications_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_group` int(11) NOT NULL,
  `id_email_notification` int(11) NOT NULL,
  `activate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `email_notifications_groups`
--

INSERT INTO `email_notifications_groups` (`id`, `id_group`, `id_email_notification`, `activate`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 1, 4, 1),
(5, 1, 5, 1),
(6, 1, 6, 1),
(7, 1, 7, 1),
(8, 1, 10, 1),
(9, 1, 11, 1),
(10, 1, 13, 1),
(11, 1, 14, 1),
(12, 1, 15, 1),
(13, 1, 16, 1),
(14, 1, 17, 1),
(15, 1, 18, 1),
(16, 1, 19, 1),
(17, 1, 20, 1),
(18, 1, 22, 1),
(19, 1, 23, 1),
(20, 1, 24, 1),
(21, 1, 25, 1),
(22, 1, 26, 1),
(23, 1, 27, 1),
(24, 1, 28, 1),
(25, 1, 29, 1),
(26, 1, 30, 1),
(27, 1, 31, 1),
(28, 1, 32, 1),
(29, 1, 34, 1),
(30, 1, 35, 1);

-- --------------------------------------------------------

--
-- Table structure for table `email_notifications_sent`
--

CREATE TABLE IF NOT EXISTS `email_notifications_sent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_model` int(11) NOT NULL,
  `table_model` varchar(50) NOT NULL,
  `id_notification` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `email_notifications_sent`
--

INSERT INTO `email_notifications_sent` (`id`, `id_model`, `table_model`, `id_notification`) VALUES
(1, 21, 'eas', 1),
(2, 22, 'eas', 1),
(3, 24, 'eas', 1),
(4, 27, 'eas', 1),
(5, 31, 'eas', 1);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(5) COLLATE utf8_bin NOT NULL,
  `customer_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `status` enum('New','Submitted','Approved','Rejected','Paid','Invoiced') COLLATE utf8_bin NOT NULL DEFAULT 'New',
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `currency` int(11) NOT NULL,
  `total_amount` double NOT NULL DEFAULT '0',
  `billable` enum('yes','no') COLLATE utf8_bin NOT NULL DEFAULT 'no',
  `billable_amount` double NOT NULL DEFAULT '0',
  `payable_amount` double NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `creationDate` date NOT NULL,
  `number_file` int(1) NOT NULL,
  `training` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`,`project_id`,`currency`,`user_id`) USING BTREE,
  KEY `project_id` (`project_id`) USING BTREE,
  KEY `currency` (`currency`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `no` (`no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `expenses_details`
--

CREATE TABLE IF NOT EXISTS `expenses_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expenses_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `original_amount` double NOT NULL,
  `original_currency` int(11) NOT NULL,
  `amount` double NOT NULL,
  `currency` int(11) NOT NULL DEFAULT '9',
  `currency_rate_id` int(11) NOT NULL,
  `billable` enum('Yes','No') COLLATE utf8_bin NOT NULL,
  `payable` enum('Yes','No') COLLATE utf8_bin NOT NULL,
  `date` date NOT NULL,
  `notes` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_id` (`expenses_id`,`type`) USING BTREE,
  KEY `type` (`type`,`currency`,`currency_rate_id`) USING BTREE,
  KEY `currency` (`currency`) USING BTREE,
  KEY `currency_rate_id` (`currency_rate_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `expenses_uploads`
--

CREATE TABLE IF NOT EXISTS `expenses_uploads` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `expenses_id` int(10) NOT NULL,
  `file` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_id` (`expenses_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'administrator', 'admins'),
(2, 'CS', 'Technical Consultants - Customer Service'),
(3, 'PS', 'Technical Consultants - Professional Service'),
(4, 'OPS', 'Operation Consultants '),
(6, 'HR', 'Admin Team'),
(7, 'Sys', 'System Administrators'),
(8, 'Managers', 'Managers');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer` int(11) NOT NULL,
  `invoice_number` varchar(5) NOT NULL,
  `final_invoice_number` varchar(7) DEFAULT NULL,
  `invoice_title` text NOT NULL,
  `id_project` int(11) DEFAULT '0',
  `project_name` varchar(255) NOT NULL,
  `id_ea` int(10) DEFAULT NULL,
  `type` enum('Standard','Airfare/Visas','Expenses') DEFAULT NULL,
  `payment` varchar(11) NOT NULL,
  `payment_procente` int(10) NOT NULL,
  `status` enum('New','To Print','Printed','Cancelled','Paid') NOT NULL,
  `currency` int(11) NOT NULL,
  `partner` int(10) DEFAULT NULL,
  `sns_share` int(11) DEFAULT NULL,
  `invoice_date_month` int(2) DEFAULT NULL,
  `invoice_date_year` int(4) DEFAULT NULL,
  `sold_by` int(11) NOT NULL,
  `old` enum('Yes','No') NOT NULL DEFAULT 'No',
  `printed_date` date DEFAULT NULL,
  `partner_status` enum('Paid','Not Paid') DEFAULT NULL,
  `partner_inv` varchar(7) DEFAULT NULL,
  `net_amount` float DEFAULT NULL,
  `gross_amount` float DEFAULT NULL,
  `partner_amount` float DEFAULT NULL,
  `file` text,
  `amount` int(11) DEFAULT NULL,
  `id_expenses` int(11) DEFAULT NULL,
  `paid_date` date NOT NULL,
  `notes` varchar(255) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `id_assigned` int(11) NOT NULL,
  `id_resource` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_invoices_customers1_idx` (`id_customer`) USING BTREE,
  KEY `currency` (`currency`) USING BTREE,
  KEY `partner` (`partner`) USING BTREE,
  KEY `id_ea` (`id_ea`) USING BTREE,
  KEY `id_resource` (`id_resource`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `id_customer`, `invoice_number`, `final_invoice_number`, `invoice_title`, `id_project`, `project_name`, `id_ea`, `type`, `payment`, `payment_procente`, `status`, `currency`, `partner`, `sns_share`, `invoice_date_month`, `invoice_date_year`, `sold_by`, `old`, `printed_date`, `partner_status`, `partner_inv`, `net_amount`, `gross_amount`, `partner_amount`, `file`, `amount`, `id_expenses`, `paid_date`, `notes`, `remarks`, `id_assigned`, `id_resource`) VALUES
(2, 1, '00002', '0001/14', 'Testing Micha', 0, 'Testing Micha', NULL, NULL, '1', 100, 'Printed', 9, 77, 100, 11, 2014, 29, 'No', '2014-11-04', NULL, NULL, 1000, 1000, 0, NULL, 1000, NULL, '2014-12-04', '', '', 0, NULL),
(3, 33, '00003', NULL, 'Invoice', 0, 'Invoice', NULL, NULL, '02', 0, 'To Print', 9, 77, 100, 1, 2016, 29, 'No', NULL, NULL, NULL, 0, 0, 0, NULL, 100, NULL, '0000-00-00', '', '', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoices_expenses`
--

CREATE TABLE IF NOT EXISTS `invoices_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_invoice` int(11) NOT NULL,
  `id_expenses_details` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `currency` int(11) NOT NULL,
  `currency_rate_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_invoice` (`id_invoice`) USING BTREE,
  KEY `id_expenses_details` (`id_expenses_details`) USING BTREE,
  KEY `currency_rate_id` (`currency_rate_id`) USING BTREE,
  KEY `currency` (`currency`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `lieu_of`
--

CREATE TABLE IF NOT EXISTS `lieu_of` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user_time` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `lieu_of`
--

INSERT INTO `lieu_of` (`id`, `id_user_time`, `date`) VALUES
(1, 7, '1970-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE IF NOT EXISTS `maintenance` (
  `id_maintenance` int(11) NOT NULL AUTO_INCREMENT,
  `contract_description` text NOT NULL,
  `customer` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `support_service` int(11) DEFAULT NULL,
  `frequency` int(11) NOT NULL,
  `original_amount` double NOT NULL,
  `currency` int(11) NOT NULL,
  `amount` double NOT NULL,
  `currency_usd` int(11) NOT NULL DEFAULT '9',
  `currency_rate_id` int(11) NOT NULL,
  `escalation_factor` float NOT NULL DEFAULT '0',
  `sns_share` int(3) NOT NULL,
  `starting_date` date NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `short_description` varchar(250) NOT NULL,
  `file` varchar(250) NOT NULL,
  `contract_duration` enum('1 Year','2 Years','3 Years','5 Years') NOT NULL,
  `travel_expenses` enum('Billable','Not Billable') NOT NULL DEFAULT 'Billable',
  `po_renewal` enum('Yes','No') NOT NULL DEFAULT 'No',
  `weekend_support` enum('Yes','No') NOT NULL DEFAULT 'No',
  `support_from_time` time NOT NULL,
  `support_to_time` time NOT NULL,
  PRIMARY KEY (`id_maintenance`),
  KEY `owner` (`owner`) USING BTREE,
  KEY `customer` (`customer`) USING BTREE,
  KEY `product` (`product`) USING BTREE,
  KEY `frequency` (`frequency`) USING BTREE,
  KEY `currency` (`currency`) USING BTREE,
  KEY `support_service` (`support_service`) USING BTREE,
  KEY `currency_rate_id` (`currency_rate_id`) USING BTREE,
  KEY `currency_usd` (`currency_usd`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`id_maintenance`, `contract_description`, `customer`, `owner`, `product`, `support_service`, `frequency`, `original_amount`, `currency`, `amount`, `currency_usd`, `currency_rate_id`, `escalation_factor`, `sns_share`, `starting_date`, `status`, `short_description`, `file`, `contract_duration`, `travel_expenses`, `po_renewal`, `weekend_support`, `support_from_time`, `support_to_time`) VALUES
(11, 'SIP yearly Maintenance and Upgrade', 32, 77, 66, 379, 83, 837.6, 9, 837.6, 9, 4, 0, 100, '2015-06-17', 'Active', 'SIP AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '06:00:00'),
(13, 'Infor yearly S&U for 16 additional licenses', 39, 79, 64, 378, 83, 3840, 9, 3840, 9, 4, 0, 25, '2015-02-01', 'Active', 'Infor WM 3.7 AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(15, 'Boomi Support and Upgrade for the period of one year', 39, 79, 65, 380, 83, 2200, 9, 2200, 9, 4, 0, 30, '2015-02-14', 'Active', 'Boomi AMC', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(16, 'Infor WMS Support & Upgrade for One Year', 38, 79, 64, NULL, 83, 7600, 9, 7600, 9, 4, 0, 25, '2015-03-11', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(17, 'Infor WMS Support & Upgrade for One Year', 38, 79, 64, 378, 83, 3187.5, 9, 3187.5, 9, 4, 0, 25, '2015-03-11', 'Active', 'Infor WM AMC Additional Users', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(18, 'Infor WMS Yearly S&U', 61, 79, 64, 378, 83, 15000, 9, 15000, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC KWT', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(19, 'Infor WMS Yearly S&U', 61, 79, 64, NULL, 83, 15000, 9, 15000, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC - Afgn', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(20, 'Ramp Yearly S&U', 61, 79, 160, 381, 83, 4000, 9, 4000, 9, 4, 0, 30, '2015-01-01', 'Active', 'Ramp AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(21, 'Portal Yearly S&U', 61, 77, 387, 382, 83, 11000, 9, 11000, 9, 4, 0, 100, '2015-01-01', 'Active', 'Portal AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(22, 'Infor WMS Support & Upgrade for One year', 41, 79, 64, NULL, 83, 32000, 9, 32000, 9, 4, 0, 25, '2015-06-26', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(23, 'Infor WMS Support and Upgrade for 1 year ', 53, 79, 64, 378, 83, 10000, 9, 10000, 9, 4, 0, 25, '2015-07-01', 'Active', 'Infor WM AMC - KWT', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(25, 'Boomi Support and Upgrade for 1 year', 53, 79, 65, NULL, 83, 2000, 9, 2000, 9, 4, 0, 37, '2015-07-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(26, 'Infor WMS S&U for the 10 additional User Licenses for KUWAIT Operation', 53, 79, 64, 378, 83, 3000, 9, 3000, 9, 4, 0, 25, '2015-07-01', 'Active', 'Infor WM AMC - KWT Additional', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(27, 'Infor WMS S&U for the 5 additional User Licenses for KSA Operation', 53, 79, 64, NULL, 83, 2996.25, 9, 2996.25, 9, 4, 0, 25, '2015-07-01', 'Active', 'Infor WM AMC - KSA', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(28, 'WMS Support & Upgrade fee for One Year', 33, 79, 64, NULL, 83, 17245, 9, 17245, 9, 4, 0, 25, '2015-06-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(29, 'Boomi Support & Upgrade fee for One Year', 33, 79, 65, NULL, 83, 1000, 9, 1000, 9, 4, 0, 30, '2015-06-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(30, 'Event Management Support & Upgrade for 1 year', 33, 79, 240, NULL, 83, 6000, 9, 6000, 9, 4, 0, 30, '2015-01-01', 'Active', 'Infor EM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(31, 'WMS Support & Upgrade for One Year', 52, 79, 64, 378, 83, 12500, 9, 12500, 9, 4, 0, 25, '2015-04-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(32, 'Boomi Support & Upgrade for One Year', 52, 79, 65, 380, 83, 3500, 9, 3500, 9, 4, 0, 30, '2015-04-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(33, 'Infor WMS Yearly Support & Upgrade Fees for One Year', 60, 79, 64, NULL, 83, 13800, 9, 13800, 9, 4, 0, 25, '2015-08-14', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(34, 'Infor WMS Yearly Support & Upgrade Fees for One Year', 60, 79, 64, NULL, 83, 1200, 9, 1200, 9, 4, 0, 25, '2015-08-14', 'Active', 'Infor WM AMC Americana Rest', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(35, 'WMS Support and Upgrade for One Year', 63, 79, 64, 378, 83, 6670, 9, 6670, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC - Egypt', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(36, 'WMS Support for quarter', 63, 79, 64, 378, 81, 10350, 9, 10350, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC - support UAE & AMMAN', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(37, 'WMS Upgrade for quarter', 63, 79, 64, 378, 83, 11214, 9, 11214, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC - Support UAE & AMMAN', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(38, 'WMS Annual Maintenance Fee for one year', 63, 79, 64, NULL, 83, 3000, 9, 3000, 9, 4, 0, 25, '2015-09-01', 'Active', 'Infor WM AMC 30 Additional Users', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(39, ' WMS Annual Maintenance Fee for one year', 63, 79, 64, 378, 83, 2000, 9, 2000, 9, 4, 0, 25, '2015-09-01', 'Active', 'Infor WM AMC 20 Additional Users', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(41, 'BOOMI Support & Upgrade for UAE and Egypt for 1 year ', 63, 79, 65, 380, 83, 8000, 9, 8000, 9, 4, 0, 30, '2015-05-18', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(42, 'Portal Support & Upgrade for UAE and Egypt for 1 year', 63, 77, 387, 382, 83, 1725, 9, 1725, 9, 4, 0, 100, '2015-05-18', 'Active', 'Portal AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(43, 'BOOMI AS2 Adapter Support & Upgrade for One year', 63, 79, 65, NULL, 83, 800, 9, 800, 9, 4, 0, 30, '2015-05-01', 'Active', 'BOOMI AS2 Adapter', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(44, 'SSA WM S&U FOR 1 YEAR', 64, 79, 64, 378, 83, 5600, 9, 5600, 9, 4, 0, 25, '2015-06-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(45, 'BOOMI S&U FOR 1 YEAR', 64, 79, 65, 380, 83, 1000, 9, 1000, 9, 4, 0, 30, '2015-06-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(46, 'WMS Support & Upgrade Fees for One Year', 65, 79, 64, NULL, 83, 4400, 9, 4400, 9, 4, 0, 25, '2015-07-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(47, 'WMS Support & Upgrade Fees for One Year', 65, 79, 64, 378, 83, 4320, 9, 4320, 9, 4, 0, 25, '2015-07-01', 'Active', 'Infor WM AMC Additional 18 Licenses', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(48, 'Infor WMS Annual Support and Upgrade', 67, 79, 64, 378, 83, 20500, 9, 20500, 9, 4, 0, 25, '2015-10-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(49, 'Boomi Annual Support and Upgrade', 67, 79, 65, 380, 83, 5760, 9, 5760, 9, 4, 0, 30, '2015-10-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(50, 'Support & Upgrade for quarter', 145, 79, 64, 378, 81, 20008.92, 9, 20008.92, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(51, 'BOOMI S&U Fee for one year', 145, 79, 65, 380, 83, 3400, 9, 3400, 9, 4, 0, 30, '2015-10-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(53, 'WMS S&U FOR 1 YEAR', 43, 79, 64, NULL, 83, 13698, 9, 13698, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(54, ' BOOMI S&U FOR 1 YEAR', 43, 79, 65, 380, 83, 2520, 9, 2520, 9, 4, 0, 30, '2015-01-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(55, 'PORTAL S&U FOR 1 YEAR', 43, 77, 387, 382, 83, 2520, 9, 2520, 9, 4, 0, 100, '2015-01-01', 'Active', 'Portal AMC', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(56, 'WMS Support & Upgeade for One Year', 72, 79, 64, 378, 83, 5850, 9, 5850, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(57, 'Boomi Support & Upgrade for One Year ', 72, 79, 65, 380, 83, 1000, 9, 1000, 9, 4, 0, 30, '2015-01-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(58, 'WMS Support and Upgrade for 1 year', 118, 79, 64, NULL, 83, 6000, 9, 6000, 9, 4, 0, 50, '2015-10-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(59, 'WMS Support and Upgrade', 118, 79, 64, 378, 83, 1500, 9, 1500, 9, 4, 0, 50, '2015-10-01', 'Active', 'Infor WM AMC Additional Users', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(60, 'S&U Fee for the entire Exceed server setups', 75, 79, 64, 378, 83, 47745, 9, 47745, 9, 4, 0, 25, '2015-09-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(61, 'Boomi Support & Upgrade for One Year', 75, 79, 65, 380, 83, 2120, 9, 2120, 9, 4, 0, 50, '2015-02-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(62, 'WMS Support & Upgrade for quarter ', 75, 79, 64, 378, 81, 36600, 9, 36600, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC Huawei', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(63, 'WMS Support & Upgrade fee for One Year', 76, 79, 64, 378, 83, 15000, 9, 15000, 9, 4, 0, 25, '2015-05-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(64, 'WMS Support & Upgrade fee for One Year', 76, 79, 64, 378, 83, 4400, 9, 4400, 9, 4, 0, 25, '2015-05-01', 'Active', 'Infor WM AMC Additional Licenses', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(65, 'Boomi Support & Upgrade fee for One Year', 76, 79, 65, 380, 83, 5000, 9, 5000, 9, 4, 0, 30, '2015-05-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(66, 'Portal Support & Upgrade fee for One Year', 76, 77, 387, 382, 83, 3000, 9, 3000, 9, 4, 0, 100, '2015-05-01', 'Active', 'Portal AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(67, 'WMS S&U Fee for One Year ', 79, 79, 64, NULL, 83, 4931.51, 9, 4931.51, 9, 4, 0, 25, '2015-09-15', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(68, 'BOOMI Support and Upgrade fee for 1 year', 78, 79, 65, 380, 83, 3000, 9, 3000, 9, 4, 0, 30, '2015-09-15', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(69, ' Infor WMS Support & Upgrade Fees for One Year', 81, 79, 64, 378, 83, 9450, 9, 9450, 9, 4, 0, 25, '2015-12-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(70, 'Annual WMS Support & Upgrade fee for 1 Year', 77, 79, 64, 378, 83, 38625, 9, 38625, 9, 4, 0, 25, '2015-05-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(71, 'Boomi Support & Upgrade fee for 1 year', 77, 79, 65, 380, 83, 7500, 9, 7500, 9, 4, 0, 30, '2015-05-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(72, 'WMS S&U for one year', 85, 79, 64, 378, 83, 13000, 9, 13000, 9, 4, 0, 25, '2015-04-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(73, 'BOOMI S&U for one year', 85, 79, 65, NULL, 83, 2000, 9, 2000, 9, 4, 0, 30, '2015-04-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(74, 'Portal S&U for one year', 85, 77, 387, 382, 83, 2000, 9, 2000, 9, 4, 0, 100, '2015-04-01', 'Active', 'Portal AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(75, 'WMS Support & Upgrade Fees for One Year', 87, 79, 64, 378, 83, 16912, 9, 16912, 9, 4, 0, 25, '2015-03-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(76, 'BOOMI S&U FOR 1 YEAR', 89, 79, 65, NULL, 83, 2400, 9, 2400, 9, 4, 0, 30, '2015-10-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(77, 'Infor WMS Support for 1 Year', 90, 79, 64, NULL, 83, 35000, 9, 35000, 9, 4, 0, 25, '2015-02-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(78, 'WMS Support & Upgrade Fees for One Year', 88, 79, 64, 378, 83, 4620, 9, 4620, 9, 4, 0, 25, '2015-02-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(79, 'SIP Support & Upgrade Fees for One Year', 88, 77, 66, 378, 83, 2280, 9, 2280, 9, 4, 0, 100, '2015-02-01', 'Active', 'SIP AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(80, 'S&U for Additional Licenses for 1 Year', 91, 79, 64, 378, 83, 800, 9, 800, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC 4 Additional Licenses', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(81, 'S&U for Additional Licenses for 1 Year', 91, 79, 64, 378, 83, 1000, 9, 1000, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC - 5Additional Licenses', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(82, 'WMS Maintenance Charges for 1 Year', 91, 79, 64, 378, 83, 8500, 9, 8500, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:30:00'),
(83, 'BOOMI Maintenance Charges for 1 Year', 91, 79, 65, 380, 83, 1000, 9, 1000, 9, 4, 0, 30, '2015-01-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(84, 'Yearly Support & Upgrade for 22 Additional Licenses', 91, 79, 64, 378, 83, 4400, 9, 4400, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC - 22 Additional Licenses', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(85, 'Support & Upgrade for 1 year', 92, 79, 64, 378, 83, 11200, 9, 11200, 9, 4, 0, 25, '2015-11-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(86, 'Support & Upgrade for 1 year', 92, 79, 64, 378, 83, 2160, 9, 2160, 9, 4, 0, 25, '2015-11-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(87, 'Support & Upgrade for 1 year', 92, 79, 65, NULL, 83, 1000, 9, 1000, 9, 4, 0, 30, '2015-11-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(88, 'Annual Maintenance Fees for Additional  User for  the DLA Project', 93, 79, 64, NULL, 83, 1152, 9, 1152, 9, 4, 0, 25, '2015-07-01', 'Active', 'Infor WM AMC DLA', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00'),
(89, 'WMS Support & Upgrade for quarter', 94, 79, 64, 378, 81, 15000, 9, 15000, 9, 4, 0, 25, '2015-01-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(90, 'WMS Support & Upgrade fee for One Year', 93, 79, 64, 378, 83, 12100, 9, 12100, 9, 4, 0, 25, '2015-06-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(91, 'Boomi Support & Upgrade fee for One Year', 93, 79, 65, 378, 83, 3000, 9, 3000, 9, 4, 0, 30, '2015-06-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(92, 'WMS S&U FOR 3 MONTHS', 95, 79, 64, 378, 81, 6880, 9, 6880, 9, 4, 0, 25, '2015-03-01', 'Active', 'Infor WM AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(93, ' Boomi S&U FOR 3 MONTHS', 95, 79, 65, 378, 81, 800, 9, 800, 9, 4, 0, 30, '2015-03-01', 'Active', 'Boomi AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(94, 'PORTAL S&U FOR 3 MONTHS', 95, 77, 387, 382, 81, 1280, 9, 1280, 9, 4, 0, 100, '2015-03-01', 'Active', 'Portal AMC', '', '', 'Billable', 'Yes', 'No', '08:00:00', '18:00:00'),
(95, 'tst1', 1, 77, 66, 379, 80, 1500, 9, 1000, 9, 4, 0, 100, '2014-11-07', 'Active', 'test', '', '', 'Billable', 'No', 'No', '01:00:00', '01:30:00'),
(96, 'test2', 1, 201, 240, 385, 81, 100, 168, 272.26, 9, 8, 0, 12, '2014-10-23', 'Active', 'testtttt', '', '1 Year', 'Billable', 'No', 'No', '01:00:00', '02:30:00'),
(98, 'Test 2', 32, 79, 65, 380, 80, 3000, 9, 3000, 9, 4, 0, 30, '2014-11-04', 'Active', 'Test 2-2', '', '', 'Billable', 'No', 'No', '07:00:00', '18:30:00'),
(99, 'Infor WMS yearly Maintenance and Upgrade', 32, 79, 64, 89, 83, 4462.4, 9, 4462.4, 9, 4, 0, 25, '2015-06-17', 'Active', 'Infor WM AMC', '', '', 'Billable', 'No', 'No', '08:00:00', '18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_invoices`
--

CREATE TABLE IF NOT EXISTS `maintenance_invoices` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_invoice` int(10) NOT NULL,
  `id_contract` int(10) NOT NULL,
  `from_period` date NOT NULL,
  `to_period` date NOT NULL,
  `amount` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_invoice` (`id_invoice`) USING BTREE,
  KEY `id_contract` (`id_contract`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_items`
--

CREATE TABLE IF NOT EXISTS `maintenance_items` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_contract` int(10) NOT NULL,
  `contract_description` text NOT NULL,
  `amount` double NOT NULL,
  `currency` int(11) NOT NULL,
  `amount_usd` double NOT NULL,
  `currency_usd` int(11) NOT NULL DEFAULT '9',
  `currency_rate_id` int(11) NOT NULL,
  `sns_share` int(3) DEFAULT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_contract` (`id_contract`) USING BTREE,
  KEY `currency` (`currency`) USING BTREE,
  KEY `sns_share` (`sns_share`) USING BTREE,
  KEY `currency_rate_id` (`currency_rate_id`) USING BTREE,
  KEY `currency_usd` (`currency_usd`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `milestones`
--

CREATE TABLE IF NOT EXISTS `milestones` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_category` int(10) NOT NULL,
  `milestone_number` int(2) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `milestone_number` (`id_category`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `milestones`
--

INSERT INTO `milestones` (`id`, `id_category`, `milestone_number`, `description`) VALUES
(1, 27, 1, 'Project Kick-Off Meeting'),
(2, 27, 2, 'Education/Training'),
(3, 27, 3, 'SOP Design and Documentation'),
(4, 27, 4, 'SOP Sign Off'),
(5, 27, 5, 'Development of Customizations and Integration'),
(6, 27, 6, 'UAT'),
(7, 27, 7, 'UAT Sign Off'),
(8, 27, 8, 'Go-Live'),
(9, 27, 9, 'Project Sign-Off'),
(10, 26, 1, 'Project Kick Off'),
(11, 26, 2, 'Info Gathering/Data Collection'),
(12, 26, 3, 'Analysis'),
(13, 26, 4, 'Deliverables'),
(14, 26, 5, 'Customer Presentation'),
(15, 26, 6, 'Project Sign-Off');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `group_id` int(11) unsigned NOT NULL,
  `page` varchar(255) NOT NULL,
  `read` varchar(3) NOT NULL,
  `write` tinyint(1) unsigned NOT NULL,
  KEY `user_id` (`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`group_id`, `page`, `read`, `write`) VALUES
(1, 'general-users', '1', 1),
(1, 'general-groups', '1', 1),
(1, 'general-settings', '1', 1),
(1, 'general-customers', '1', 1),
(1, 'general-eas', '1', 1),
(1, 'general-projects', '1', 1),
(1, 'general-expenses', '1', 1),
(1, 'general-timesheets', '1', 1),
(1, 'general-financial', '1', 1),
(1, 'general-alerts', '1', 1),
(1, 'general-dashboard', '1', 1),
(1, 'general-quicklinks', '1', 1),
(1, 'general-supportdesk', '1', 1),
(1, 'general-suppliers', '1', 1),
(1, 'general-travel', '1', 1),
(1, 'users-list', '1', 1),
(1, 'users-personal', '1', 1),
(1, 'users-hr', '1', 1),
(1, 'users-visas', '1', 1),
(1, 'users-attachments', '1', 1),
(1, 'groups-list', '1', 1),
(1, 'groups-users', '1', 1),
(1, 'groups-permissions', '1', 1),
(1, 'groups-notifications', '1', 1),
(1, 'settings-general_settings', '1', 1),
(1, 'settings-codelists', '1', 1),
(1, 'customers-list', '1', 1),
(1, 'customers-general_customers', '1', 1),
(1, 'customers-eas', '1', 1),
(1, 'customers-invoices', '1', 1),
(1, 'customers-attachments', '1', 1),
(1, 'customers-connections', '1', 1),
(1, 'eas-list', '1', 1),
(1, 'projects-list', '1', 1),
(1, 'projects-projects_general', '1', 1),
(1, 'projects-tasks', '1', 1),
(1, 'projects-milestones', '1', 1),
(1, 'projects-alerts', '1', 1),
(1, 'projects-attachments', '1', 1),
(1, 'expenses-expenses_approval', '111', 1),
(1, 'timesheets-timesheets_approval', '1', 1),
(1, 'financial-maintenance', '1', 1),
(1, 'financial-invoices', '1', 1),
(1, 'financial-receivables', '1', 1),
(1, 'alerts-project_alerts', '1', 0),
(1, 'alerts-birthdays', '1', 0),
(1, 'alerts-timesheets_submittet', '1', 0),
(1, 'alerts-timesheets_new', '1', 0),
(1, 'alerts-visas_alerts', '1', 0),
(1, 'alerts-passports_alerts', '1', 0),
(1, 'alerts-expenses_sheets', '1', 0),
(1, 'alerts-system_down', '1', 0),
(1, 'alerts-issue_tickets', '1', 0),
(1, 'dashboard-WidgetEas', '1', 1),
(1, 'dashboard-WidgetBillability', '1', 1),
(1, 'dashboard-WidgetTime', '1', 1),
(1, 'dashboard-WidgetSubmittedCustomer', '1', 1),
(1, 'dashboard-WidgetSrClose', '1', 1),
(1, 'dashboard-WidgetProjectFinancials', '1', 1),
(1, 'dashboard-WidgetProjects', '1', 1),
(1, 'dashboard-WidgetQuickLinks', '1', 1),
(1, 'dashboard-WidgetSupport', '1', 1),
(1, 'dashboard-WidgetCountryRevenues', '1', 1),
(1, 'dashboard-WidgetEaTypeRevenues', '1', 1),
(1, 'dashboard-WidgetSoldByRevenues', '1', 1),
(1, 'dashboard-WidgetRevenues', '1', 1),
(1, 'dashboard-WidgetSrCloseResource', '1', 1),
(1, 'dashboard-WidgetSubmittedReason', '1', 1),
(1, 'dashboard-WidgetSrCustomer', '1', 1),
(1, 'dashboard-WidgetSrSubmitted', '1', 1),
(1, 'dashboard-WidgetSrTopCustomer', '1', 1),
(1, 'dashboard-WidgetCustomers', '1', 1),
(1, 'quicklinks-id1', '1', 0),
(1, 'quicklinks-id4', '1', 0),
(1, 'quicklinks-id3', '1', 0),
(1, 'quicklinks-id2', '1', 0),
(1, 'quicklinks-id5', '1', 0),
(1, 'supportdesk-list', '1', 1),
(1, 'suppliers-list', '1', 1),
(1, 'travel-list', '1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `permissions_references`
--

CREATE TABLE IF NOT EXISTS `permissions_references` (
  `group_id` int(11) unsigned NOT NULL,
  `page` varchar(255) NOT NULL,
  `read` varchar(3) NOT NULL,
  `write` tinyint(1) unsigned NOT NULL,
  KEY `user_id` (`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permissions_references`
--

INSERT INTO `permissions_references` (`group_id`, `page`, `read`, `write`) VALUES
(1, 'general-users', '1', 1),
(1, 'general-groups', '1', 1),
(1, 'general-settings', '1', 1),
(1, 'general-customers', '1', 1),
(1, 'general-eas', '1', 1),
(1, 'general-projects', '1', 1),
(1, 'general-expenses', '1', 1),
(1, 'general-timesheets', '1', 1),
(1, 'general-financial', '1', 1),
(1, 'general-alerts', '1', 1),
(1, 'general-dashboard', '1', 1),
(1, 'general-quicklinks', '1', 1),
(1, 'general-supportdesk', '1', 1),
(1, 'general-suppliers', '1', 1),
(1, 'general-travel', '1', 1),
(1, 'users-list', '1', 1),
(1, 'users-personal', '1', 1),
(1, 'users-hr', '1', 1),
(1, 'users-visas', '1', 1),
(1, 'users-attachments', '1', 1),
(1, 'groups-list', '1', 1),
(1, 'groups-users', '1', 1),
(1, 'groups-permissions', '1', 1),
(1, 'groups-notifications', '1', 1),
(1, 'settings-general_settings', '1', 1),
(1, 'settings-codelists', '1', 1),
(1, 'customers-list', '1', 1),
(1, 'customers-general_customers', '1', 1),
(1, 'customers-eas', '1', 1),
(1, 'customers-invoices', '1', 1),
(1, 'customers-attachments', '1', 1),
(1, 'customers-connections', '1', 1),
(1, 'eas-list', '1', 1),
(1, 'projects-list', '1', 1),
(1, 'projects-projects_general', '1', 1),
(1, 'projects-tasks', '1', 1),
(1, 'projects-milestones', '1', 1),
(1, 'projects-alerts', '1', 1),
(1, 'projects-attachments', '1', 1),
(1, 'expenses-expenses_approval', '111', 1),
(1, 'timesheets-timesheets_approval', '1', 1),
(1, 'financial-maintenance', '1', 1),
(1, 'financial-invoices', '1', 1),
(1, 'financial-receivables', '1', 1),
(1, 'alerts-project_alerts', '1', 0),
(1, 'alerts-birthdays', '1', 0),
(1, 'alerts-timesheets_submittet', '1', 0),
(1, 'alerts-timesheets_new', '1', 0),
(1, 'alerts-visas_alerts', '1', 0),
(1, 'alerts-passports_alerts', '1', 0),
(1, 'alerts-expenses_sheets', '1', 0),
(1, 'alerts-system_down', '1', 0),
(1, 'alerts-issue_tickets', '1', 0),
(1, 'dashboard-WidgetEas', '1', 1),
(1, 'dashboard-WidgetBillability', '1', 1),
(1, 'dashboard-WidgetTime', '1', 0),
(1, 'dashboard-WidgetSubmittedCustomer', '1', 1),
(1, 'dashboard-WidgetSrClose', '1', 1),
(1, 'dashboard-WidgetProjectFinancials', '1', 1),
(1, 'dashboard-WidgetProjects', '1', 1),
(1, 'dashboard-WidgetQuickLinks', '1', 1),
(1, 'dashboard-WidgetSupport', '1', 1),
(1, 'dashboard-WidgetCountryRevenues', '1', 1),
(1, 'dashboard-WidgetEaTypeRevenues', '1', 1),
(1, 'dashboard-WidgetSoldByRevenues', '1', 1),
(1, 'dashboard-WidgetRevenues', '1', 1),
(1, 'dashboard-WidgetSrCloseResource', '1', 1),
(1, 'dashboard-WidgetSubmittedReason', '1', 1),
(1, 'dashboard-WidgetSrCustomer', '1', 1),
(1, 'dashboard-WidgetSrSubmitted', '1', 1),
(1, 'dashboard-WidgetSrTopCustomer', '1', 1),
(1, 'dashboard-WidgetCustomers', '1', 1),
(1, 'quicklinks-id1', '1', 0),
(1, 'quicklinks-id4', '1', 0),
(1, 'quicklinks-id3', '1', 0),
(1, 'quicklinks-id2', '1', 0),
(1, 'supportdesk-list', '1', 1),
(1, 'suppliers-list', '1', 1),
(1, 'travel-list', '1', 1),
(1, 'quicklinks-id5', '1', 0),
(1, 'holiday-list', '1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `phases`
--

CREATE TABLE IF NOT EXISTS `phases` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `id_category` int(2) NOT NULL,
  `phase` text NOT NULL,
  `phase_number` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `phases`
--

INSERT INTO `phases` (`id`, `id_category`, `phase`, `phase_number`) VALUES
(1, 27, 'Project Kick-Off', 1),
(2, 27, 'Information Gathering', 2),
(3, 27, 'Education/Training', 3),
(4, 27, 'SOP Design', 4),
(5, 27, 'Development', 5),
(6, 27, 'User Acceptance Testing', 6),
(7, 27, 'Go-Live', 7),
(8, 27, 'Support Transition', 8),
(9, 27, 'Post Implementation Assessment', 9),
(10, 27, 'Project Management', 10),
(11, 26, 'Project Kick-Off', 1),
(12, 26, 'Info Gathering and Data Collection', 2),
(14, 26, 'Analysis and Documention', 3),
(15, 26, 'Presentation', 4),
(16, 26, 'Project Management', 5),
(17, 28, 'Development', 1),
(18, 28, 'Project Management', 2);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `is_billable` int(1) NOT NULL DEFAULT '0',
  `project_manager` int(11) DEFAULT NULL,
  `business_manager` int(11) DEFAULT NULL,
  `id_parent` int(11) DEFAULT NULL,
  `id_type` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `deactivate_alerts` enum('Yes','No') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`) USING BTREE,
  KEY `project_manager` (`project_manager`) USING BTREE,
  KEY `id_parent` (`id_parent`) USING BTREE,
  KEY `id_type` (`id_type`) USING BTREE,
  KEY `business_manager` (`business_manager`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=131 ;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `customer_id`, `is_billable`, `project_manager`, `business_manager`, `id_parent`, `id_type`, `status`, `deactivate_alerts`) VALUES
(119, 'test', 0, 1, NULL, NULL, NULL, 27, 1, 'Yes'),
(120, 'Project1', 1, 1, NULL, NULL, NULL, 27, 0, NULL),
(121, '678', 1, 0, NULL, NULL, NULL, 26, 0, NULL),
(122, 'adadad', 33, 0, NULL, NULL, NULL, 26, 0, NULL),
(123, 'PROJECTAN', 32, 0, NULL, NULL, NULL, 26, 0, NULL),
(124, 'PROJECTTEST', 1, 0, NULL, NULL, NULL, 26, 0, NULL),
(125, 'test', 32, 0, NULL, NULL, NULL, 26, 0, NULL),
(126, 'RDTST', 1, 0, NULL, NULL, NULL, 26, 0, NULL),
(127, 'test', 33, 0, NULL, NULL, NULL, 26, 0, NULL),
(128, 'Test777', 34, 1, NULL, NULL, NULL, 26, 0, NULL),
(129, 'Al Rabie', 112, 0, NULL, NULL, NULL, 26, 0, NULL),
(130, '07112014', 32, 1, NULL, NULL, NULL, 26, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `projects_alerts`
--

CREATE TABLE IF NOT EXISTS `projects_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_project` int(11) NOT NULL,
  `alerts` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects_emails`
--

CREATE TABLE IF NOT EXISTS `projects_emails` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_project` int(10) NOT NULL,
  `id_user` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`) USING BTREE,
  KEY `id_category` (`id_project`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects_milestones`
--

CREATE TABLE IF NOT EXISTS `projects_milestones` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_project` int(10) NOT NULL,
  `id_milestone` int(11) NOT NULL,
  `status` enum('Pending','In Progress','Closed') NOT NULL,
  `estimated_date_of_completion` date NOT NULL,
  `last_updated` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_project_2` (`id_project`,`id_milestone`) USING BTREE,
  KEY `id_project` (`id_project`) USING BTREE,
  KEY `id_milestone` (`id_milestone`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects_phases`
--

CREATE TABLE IF NOT EXISTS `projects_phases` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_project` int(10) NOT NULL,
  `phase_number` int(10) NOT NULL,
  `description` text NOT NULL,
  `id_phase` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_project_2` (`id_project`,`id_phase`) USING BTREE,
  KEY `id_project` (`id_project`) USING BTREE,
  KEY `id_phase` (`id_phase`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects_tasks`
--

CREATE TABLE IF NOT EXISTS `projects_tasks` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_project_phase` int(10) NOT NULL,
  `man_days_budgeted` int(5) NOT NULL,
  `billable` enum('Yes','No') NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_project_phase_2` (`id_project_phase`,`description`) USING BTREE,
  KEY `id_project_phase` (`id_project_phase`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `quicklinks`
--

CREATE TABLE IF NOT EXISTS `quicklinks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `quicklinks`
--

INSERT INTO `quicklinks` (`id`, `name`, `url`) VALUES
(1, 'Book a Leave or Working from Home Request', '/requests/create'),
(2, 'Submit an HR Request', '/requestsHr/create'),
(3, 'Create New Expense Sheet', '/expenses/create'),
(4, 'Create New EA', '/eas/create'),
(5, 'View Public Holidays', '/requestsHr/holiday');

-- --------------------------------------------------------

--
-- Stand-in structure for view `receivables`
--
CREATE TABLE IF NOT EXISTS `receivables` (
`id` int(11)
,`id_customer` int(11)
,`invoice_number` text
,`final_invoice_number` varchar(7)
,`invoice_title` text
,`project_name` varchar(255)
,`id_ea` int(10)
,`payment` text
,`payment_procente` decimal(32,0)
,`status` enum('New','To Print','Printed','Cancelled','Paid')
,`currency` int(11)
,`partner` int(10)
,`sns_share` int(11)
,`invoice_date_month` int(2)
,`invoice_date_year` int(4)
,`sold_by` int(11)
,`old` enum('Yes','No')
,`printed_date` date
,`partner_status` enum('Paid','Not Paid')
,`partner_inv` varchar(7)
,`net_amount` double
,`gross_amount` double
,`partner_amount` double
,`amount` decimal(32,0)
,`id_expenses` text
,`paid_date` date
,`notes` text
,`remarks` text
,`id_assigned` int(11)
);
-- --------------------------------------------------------

--
-- Table structure for table `receivables_template_emails`
--

CREATE TABLE IF NOT EXISTS `receivables_template_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_codelkup` int(11) NOT NULL,
  `template` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE IF NOT EXISTS `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `type` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 = new',
  `cc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `requests_hr`
--

CREATE TABLE IF NOT EXISTS `requests_hr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `startDate` date NOT NULL,
  `type` int(11) NOT NULL,
  `note` text NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `id_type` int(11) NOT NULL,
  `main_contact` varchar(100) NOT NULL,
  `main_phone` varchar(20) NOT NULL,
  `other_phone` varchar(20) DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `swift` varchar(50) DEFAULT NULL,
  `iban` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_type` (`id_type`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `id_type`, `main_contact`, `main_phone`, `other_phone`, `account_name`, `bank_name`, `swift`, `iban`) VALUES
(1, 'Test', 101, 'Test', '70112441', 'Test', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `support_desk`
--

CREATE TABLE IF NOT EXISTS `support_desk` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sd_no` varchar(5) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `severity` enum('Medium','High','Low') NOT NULL,
  `status` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `description` varchar(1024) NOT NULL,
  `short_description` varchar(128) NOT NULL,
  `system_down` enum('No','Yes') NOT NULL DEFAULT 'No',
  `product` int(11) NOT NULL,
  `schema` varchar(4) DEFAULT NULL,
  `environment` enum('Test','UAT','Live') DEFAULT NULL,
  `issue_incurred_previously` enum('No','Yes') NOT NULL,
  `issue` enum('No','Yes') NOT NULL,
  `assigned_to` int(10) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `reason` int(10) DEFAULT NULL,
  `repeat` enum('Yes','No') DEFAULT NULL,
  `responsibility` enum('CS','PS') DEFAULT NULL,
  `files` int(10) DEFAULT NULL,
  `comm_type` int(11) NOT NULL COMMENT '0-no comm,1-user comm,2-customer comm,3-all comm',
  `customer_contact_id` int(11) NOT NULL,
  `submitter_name` varchar(50) NOT NULL,
  `reopen` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_customer` (`id_customer`) USING BTREE,
  KEY `reason` (`reason`) USING BTREE,
  KEY `product` (`product`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=64 ;

--
-- Dumping data for table `support_desk`
--

INSERT INTO `support_desk` (`id`, `sd_no`, `id_customer`, `severity`, `status`, `date`, `description`, `short_description`, `system_down`, `product`, `schema`, `environment`, `issue_incurred_previously`, `issue`, `assigned_to`, `due_date`, `reason`, `repeat`, `responsibility`, `files`, `comm_type`, `customer_contact_id`, `submitter_name`, `reopen`) VALUES
(1, '00001', 1, 'Low', 1, '2014-09-09 13:17:53', 'qweasdxcverhrhrhrhrh', 'test', 'No', 64, 'WH1', 'Live', 'No', 'No', 27, NULL, 389, NULL, NULL, 0, 0, 1, 'test', 0),
(2, '00002', 1, 'Medium', 0, '2014-09-10 09:13:53', 'dear test, \r\nwe are testing the test envronment of the new snsit test schema.\r\nThank you, \r\nTEST ', 'testing', 'No', 240, 'WH2', 'Test', 'Yes', 'No', 4, NULL, NULL, NULL, 'PS', 0, 0, 1, 'RAMYK', 0),
(3, '00003', 1, 'Medium', 3, '2014-09-11 09:18:29', '12313asdasdad  adadad ', 'test', 'No', 162, 'WH3', 'Test', 'Yes', 'No', 11, NULL, 389, NULL, 'CS', 0, 0, 1, 'qweasd', 0),
(60, '00060', 154, 'High', 0, '2014-11-03 19:22:59', 'Are we now able to send emails to all customer emails assigned as SD?', 'Send emails SNSit', 'No', 387, 'WH1', 'Test', 'Yes', 'No', 8, NULL, NULL, NULL, NULL, 0, 0, 5, 'Tarek', 0),
(61, '00061', 154, 'High', 0, '2014-11-03 19:31:04', 'Are we now able to send emails to all customer emails assigned as SD?', 'Short Descr 2', 'No', 387, 'WH1', 'Test', 'Yes', 'No', 17, NULL, NULL, NULL, NULL, 0, 0, 5, 'Tarek 2 ', 0),
(62, '00062', 154, 'High', 1, '2014-11-03 19:31:58', 'Are we now able to send emails to all customer emails assigned as SD?', 'sssadasd', 'No', 387, 'WH1', 'Test', 'Yes', 'No', 15, NULL, NULL, NULL, NULL, 0, 0, 5, 'tare', 0),
(63, '00063', 1, 'Low', 0, '2014-11-04 16:09:05', 'micha testing', 'test micha', 'No', 64, 'WH5', 'UAT', 'Yes', 'No', NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 'micheline daaboul', 0);

-- --------------------------------------------------------

--
-- Table structure for table `support_desk_comm_files`
--

CREATE TABLE IF NOT EXISTS `support_desk_comm_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_support_desk` int(11) NOT NULL,
  `id_comm` int(11) DEFAULT NULL,
  `filename` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_support_desk` (`id_support_desk`) USING BTREE,
  KEY `id_comm` (`id_comm`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `support_desk_comments`
--

CREATE TABLE IF NOT EXISTS `support_desk_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_support_desk` int(11) NOT NULL,
  `comment` varchar(512) NOT NULL,
  `date` datetime NOT NULL,
  `id_user` int(11) NOT NULL,
  `is_admin` tinyint(4) NOT NULL,
  `files` varchar(128) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `sender` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_support_desk` (`id_support_desk`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `support_desk_comments`
--

INSERT INTO `support_desk_comments` (`id`, `id_support_desk`, `comment`, `date`, `id_user`, `is_admin`, `files`, `status`, `sender`) VALUES
(1, 1, 'test 23/09/2014', '2014-09-23 17:19:11', 3, 1, '0', 0, 'SNS'),
(2, 1, 'test 23/09/2014', '2014-09-23 17:19:12', 3, 1, '0', 1, 'SNS'),
(3, 1, 'test 23/09/2014', '2014-09-23 17:19:16', 3, 1, '0', 1, 'SNS'),
(4, 1, '1', '2014-09-23 17:20:54', 3, 1, '0', 1, 'SNS'),
(5, 1, '1', '2014-09-23 17:21:02', 3, 1, NULL, 2, 'SNS'),
(6, 1, 'tt', '2014-09-23 17:21:42', 3, 1, '0', 1, 'SNS'),
(7, 3, 'TESTING\r\n', '2014-11-03 19:05:39', 1, 0, '0', 0, 'Testing'),
(8, 3, 'testing2', '2014-11-03 19:10:46', 3, 1, '0', 1, 'SNS'),
(9, 62, 'HOPE WE DO ,\r\n\r\nTHANK YOU ,\r\nRAMY', '2014-11-03 19:35:37', 3, 1, '0', 0, 'SNS'),
(10, 62, 'I dont', '2014-11-03 19:36:19', 5, 0, '0', 1, 'REKME'),
(11, 62, 'I still don''t. Test #2 ', '2014-11-03 19:39:46', 5, 0, '0', 1, 'REKME'),
(12, 62, 'for the record', '2014-11-03 19:41:06', 5, 0, '0', 1, 'REKME'),
(13, 62, 'follow up', '2014-11-03 20:08:33', 5, 0, '0', 1, 'REKME'),
(14, 62, 'A', '2014-11-03 20:10:46', 5, 0, '0', 1, 'REKME'),
(15, 62, 'sdf', '2014-11-03 20:16:11', 5, 0, '0', 1, 'REKME'),
(16, 62, 'WOWWWW WE DID IT ', '2014-11-03 20:17:35', 3, 1, '0', 1, 'SNS'),
(17, 62, 'You can consider the issue fixed. Please close it.\r\n\r\nPS: Note that Mario is copied so keep it low ;) ', '2014-11-03 20:18:48', 5, 0, '0', 1, 'REKME'),
(18, 3, '', '2014-11-05 21:05:35', 32, 1, NULL, 2, 'SNS'),
(19, 3, 'No Close ', '2014-11-05 21:10:02', 32, 1, '0', 2, 'SNS'),
(20, 3, 'Comment button disappeared and Im not able to close the issue. ', '2014-11-05 21:10:31', 32, 1, '0', 1, 'SNS'),
(21, 3, 'I want to close ', '2014-11-05 21:17:48', 32, 1, NULL, 3, 'SNS'),
(22, 3, 'I can still post anything.', '2014-11-05 21:18:15', 32, 1, '0', 3, 'SNS');

-- --------------------------------------------------------

--
-- Table structure for table `support_desk_files`
--

CREATE TABLE IF NOT EXISTS `support_desk_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_support_desk` int(11) DEFAULT NULL,
  `filename` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `support_desk_files`
--

INSERT INTO `support_desk_files` (`id`, `id_support_desk`, `filename`) VALUES
(1, NULL, 'PB.png'),
(2, NULL, '20140303_132105.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `system_parameters`
--

CREATE TABLE IF NOT EXISTS `system_parameters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `system_parameter` varchar(50) NOT NULL,
  `label` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `system_parameters`
--

INSERT INTO `system_parameters` (`id`, `system_parameter`, `label`, `value`) VALUES
(1, 'page_size', 'Max Rows Retrieved', '20'),
(2, 'bank_details', 'SNS Bank Details', 'Bank Of Beirut - Bauchrieh Branch\r\nSwift: BABELBBE\r\nUSD IBAN:LB77 0075 0000 0001 1401 6490 5400'),
(3, 'vat_no', 'Vat No.', '1320296-601'),
(4, 'registration_no', 'Registration No.', '1801519'),
(5, 'man_hour_cost', 'Man Hour Cost', '68.75');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `id_phase` int(2) NOT NULL,
  `task` text NOT NULL,
  `billable` enum('Yes','No') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_phase` (`id_phase`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=59 ;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `id_phase`, `task`, `billable`) VALUES
(1, 1, 'Project Kick-Off Meeting', 'Yes'),
(2, 2, 'Information Gathering Meetings', 'Yes'),
(3, 2, 'Site Survey', 'Yes'),
(4, 3, 'Training', 'Yes'),
(5, 3, 'Training Preparation', 'Yes'),
(6, 4, 'SOP Meetings', 'Yes'),
(7, 4, 'Integration Meetings', 'Yes'),
(8, 4, 'SOP Documentation', 'Yes'),
(9, 4, 'Integration Documentation', 'Yes'),
(10, 4, 'SOP Review and Sign-Off', 'Yes'),
(11, 5, 'Client Task Check List', 'Yes'),
(12, 5, 'Environment Setup', 'Yes'),
(13, 5, 'Integration Testing', 'Yes'),
(14, 5, 'FBRXXX', 'Yes'),
(15, 5, 'INTXXX', 'Yes'),
(16, 5, 'Technical Audit', 'Yes'),
(17, 5, 'Data Upload and Migration', 'Yes'),
(18, 5, 'Integrated Testing', 'Yes'),
(19, 6, 'Installations', 'Yes'),
(20, 6, 'Deployment', 'Yes'),
(21, 6, 'Interface Testing', 'Yes'),
(22, 6, 'Master Data Upload and Configuration', 'Yes'),
(23, 6, 'Super User Training', 'Yes'),
(24, 6, 'UAT Check List Review', 'Yes'),
(25, 6, 'UAT Issue Resolution', 'Yes'),
(26, 7, 'Verify Production Environment', 'Yes'),
(27, 7, 'Go-Live Check List Review', 'Yes'),
(28, 7, 'Go-Live Support', 'Yes'),
(29, 7, 'Post-Go-Live Support', 'Yes'),
(30, 8, 'Support Transition and Handover', 'No'),
(31, 9, 'System Assessment and Evaluation', 'Yes'),
(32, 10, 'Internal Meetings', 'Yes'),
(33, 10, 'Project Management', 'Yes'),
(34, 10, 'Conf Calls and Meeting', 'Yes'),
(35, 10, 'Research/Internal Work', 'No'),
(36, 10, 'Source Safe Maintenance', 'No'),
(37, 10, 'Travel', 'No'),
(38, 11, 'Project Kick-Off Meeting', 'Yes'),
(39, 12, 'Site Visit', 'Yes'),
(40, 12, 'Data Collection and Validation', 'Yes'),
(41, 14, 'Analysis and Modeling', 'Yes'),
(42, 14, 'Documentation', 'Yes'),
(43, 15, 'Presentation Meeting', 'Yes'),
(44, 15, 'Presentation Review', 'Yes'),
(45, 15, 'Customer Sign-Off', 'Yes'),
(46, 16, 'Internal Meetings', 'No'),
(47, 16, 'Project Management', 'Yes'),
(48, 16, 'Conf Calls and Meetings', 'Yes'),
(49, 16, 'Research/Internal Work', 'No'),
(50, 16, 'Travel', 'No'),
(51, 17, 'FBRXXX', 'Yes'),
(52, 17, 'INTXXX', 'Yes'),
(53, 17, 'Tech Audit', 'No'),
(54, 18, 'Internal Meetings', 'No'),
(55, 18, 'Project Management', 'Yes'),
(56, 18, 'Conf Calls and Meetings', 'Yes'),
(57, 18, 'Research/Internal Work', 'No'),
(58, 18, 'Travel', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `timesheets`
--

CREATE TABLE IF NOT EXISTS `timesheets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timesheet_cod` varchar(5) NOT NULL,
  `id_user` int(11) NOT NULL,
  `week` int(2) NOT NULL,
  `week_start` datetime NOT NULL,
  `week_end` datetime NOT NULL,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_user_2` (`id_user`,`week`) USING BTREE,
  KEY `id_user` (`id_user`) USING BTREE,
  KEY `id_status` (`status`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `week` (`week`) USING BTREE,
  KEY `week_start` (`week_start`) USING BTREE,
  KEY `week_end` (`week_end`) USING BTREE,
  KEY `status_2` (`status`) USING BTREE,
  KEY `week_2` (`week`) USING BTREE,
  KEY `week_start_2` (`week_start`) USING BTREE,
  KEY `week_end_2` (`week_end`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=385 ;

--
-- Dumping data for table `timesheets`
--

INSERT INTO `timesheets` (`id`, `timesheet_cod`, `id_user`, `week`, `week_start`, `week_end`, `status`) VALUES
(350, '00350', 1, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(351, '00351', 3, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(352, '00352', 4, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(353, '00353', 5, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(354, '00354', 6, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(355, '00355', 7, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(356, '00356', 8, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(357, '00357', 9, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(358, '00358', 10, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(359, '00359', 11, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(360, '00360', 12, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(361, '00361', 13, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(362, '00362', 14, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(363, '00363', 15, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(364, '00364', 16, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(365, '00365', 17, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(366, '00366', 18, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(367, '00367', 19, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(368, '00368', 20, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(369, '00369', 21, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(370, '00370', 22, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(371, '00371', 23, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(372, '00372', 24, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(373, '00373', 25, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(374, '00374', 26, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(375, '00375', 27, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(376, '00376', 28, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(377, '00377', 29, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(378, '00378', 30, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(379, '00379', 31, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(380, '00380', 32, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(381, '00381', 33, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(382, '00382', 34, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(383, '00383', 35, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New'),
(384, '00384', 36, 45, '2014-11-03 00:00:00', '2014-11-09 00:00:00', 'New');

-- --------------------------------------------------------

--
-- Table structure for table `trainings`
--

CREATE TABLE IF NOT EXISTS `trainings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_customer` int(10) NOT NULL,
  `id_eas` int(10) NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_eas` (`id_eas`) USING BTREE,
  KEY `id_customer` (`id_customer`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `travel`
--

CREATE TABLE IF NOT EXISTS `travel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `travel_cod` varchar(5) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `id_project` int(11) NOT NULL,
  `expense_type` int(11) NOT NULL,
  `amount` double NOT NULL,
  `currency` int(11) NOT NULL,
  `billable` enum('yes','no') NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - New, 1 - Invoiced, 2 - Closed',
  `date` date NOT NULL,
  `inv_number` int(11) DEFAULT NULL,
  `final_inv_number` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`) USING BTREE,
  KEY `id_customer` (`id_customer`) USING BTREE,
  KEY `id_project` (`id_project`) USING BTREE,
  KEY `currency` (`currency`) USING BTREE,
  KEY `expense_type` (`expense_type`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `travel`
--

INSERT INTO `travel` (`id`, `travel_cod`, `id_user`, `id_customer`, `id_project`, `expense_type`, `amount`, `currency`, `billable`, `status`, `date`, `inv_number`, `final_inv_number`) VALUES
(1, '00001', 4, 1, 119, 102, 1000, 9, 'no', 2, '2014-11-01', NULL, NULL),
(2, '00002', 3, 34, 119, 232, 1000, 9, 'yes', 2, '2014-11-04', NULL, NULL),
(3, '00003', 3, 33, 119, 104, 2323, 9, 'yes', 0, '2014-11-04', NULL, NULL),
(4, '00004', 3, 33, 119, 104, 6767, 9, 'yes', 0, '2014-11-03', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `url_log`
--

CREATE TABLE IF NOT EXISTS `url_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `short_description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_group` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_user` (`id_user`,`id_group`),
  KEY `id_user_2` (`id_user`),
  KEY `id_group` (`id_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=69 ;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`id`, `id_user`, `id_group`) VALUES
(1, 1, 1),
(65, 3, 1),
(66, 4, 1),
(2, 4, 6),
(34, 5, 3),
(35, 6, 4),
(36, 7, 7),
(37, 8, 3),
(39, 10, 4),
(40, 11, 6),
(41, 12, 3),
(42, 13, 7),
(44, 15, 3),
(46, 17, 3),
(68, 18, 1),
(50, 21, 2),
(52, 23, 6),
(53, 24, 4),
(54, 25, 2),
(55, 26, 4),
(56, 27, 2),
(57, 28, 3),
(58, 29, 4),
(67, 32, 1),
(61, 32, 2),
(62, 33, 4),
(63, 34, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_groups_old`
--

CREATE TABLE IF NOT EXISTS `user_groups_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_group` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_user` (`id_user`,`id_group`) USING BTREE,
  KEY `id_user_2` (`id_user`) USING BTREE,
  KEY `id_group` (`id_group`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=67 ;

--
-- Dumping data for table `user_groups_old`
--

INSERT INTO `user_groups_old` (`id`, `id_user`, `id_group`) VALUES
(1, 1, 1),
(65, 3, 1),
(66, 4, 1),
(2, 4, 6),
(34, 5, 3),
(35, 6, 4),
(36, 7, 7),
(37, 8, 3),
(39, 10, 4),
(40, 11, 6),
(41, 12, 3),
(42, 13, 7),
(44, 15, 3),
(46, 17, 3),
(50, 21, 2),
(52, 23, 6),
(53, 24, 4),
(54, 25, 2),
(55, 26, 4),
(56, 27, 2),
(57, 28, 3),
(58, 29, 4),
(61, 32, 2),
(62, 33, 4),
(63, 34, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_hr_details`
--

CREATE TABLE IF NOT EXISTS `user_hr_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `employment_date` date DEFAULT NULL,
  `evaluation_date` date DEFAULT NULL,
  `evaluation_batch` varchar(255) DEFAULT NULL,
  `contract_signed` enum('n','y') DEFAULT NULL,
  `contract_expiry_date` date DEFAULT NULL,
  `hr_manual_signed` enum('n','y') DEFAULT NULL,
  `mof` int(11) DEFAULT NULL,
  `ssnf` int(11) DEFAULT NULL,
  `bank_account` text,
  `iban` text,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `user_hr_details`
--

INSERT INTO `user_hr_details` (`id`, `id_user`, `employment_date`, `evaluation_date`, `evaluation_batch`, `contract_signed`, `contract_expiry_date`, `hr_manual_signed`, `mof`, `ssnf`, `bank_account`, `iban`) VALUES
(1, 3, NULL, NULL, '', 'n', NULL, 'n', 2829595, 901519618, '', ''),
(2, 4, NULL, NULL, '', 'n', NULL, 'n', NULL, NULL, '', ''),
(3, 5, '2011-09-05', '1970-01-01', '', 'y', '2014-09-04', 'y', 1957807, 861323274, '526186', 'LB75 0075 0000 0001 1350 5261 8600'),
(4, 6, '2010-09-01', '0000-00-00', NULL, '', '2012-08-31', '', NULL, NULL, '427536', 'LB59 0075 0000 0001 1350 4275 3600'),
(5, 7, '2014-07-21', '1970-01-01', '', 'y', '1970-01-01', 'y', NULL, 821348295, '', ''),
(6, 8, '2012-07-16', '1970-01-01', '', 'y', '2015-07-15', 'y', 2270221, 871446385, '427628', 'LB76 0075 0000 0001 1350 4276 2800'),
(7, 9, '2007-04-23', '1970-01-01', '', 'y', '2010-04-03', 'y', 1158587, 791219750, '647481', 'LB19 0075 0000 0001 1350 6474 8100'),
(8, 10, '2011-02-14', '1970-01-01', '', 'y', '2013-02-13', 'y', 2930407, 861389814, '427519', 'LB78 0075 0000 0001 1350 4275 1900'),
(9, 11, '2011-05-01', '1970-01-01', '', 'y', '2014-04-30', 'y', 1662305, 831243605, '299041', 'LB54 0075 0000 0001 1350 2990 4100'),
(10, 12, '2012-10-01', '1970-01-01', '', 'y', '2015-09-30', 'y', 2774065, 881436905, '065919', 'LB43 0075 0000 0001 1350 0659 1900'),
(11, 13, '2013-08-12', '1970-01-01', '', 'y', '2016-08-11', 'y', 2745449, 891517387, '429370', 'LB12 0075 0000 0001 1350 4293 7000'),
(12, 14, '2010-03-01', '1970-01-01', '', 'y', '2013-02-28', 'y', 184771, 751032724, '427534', 'LB27 0075 0000 0001 1350 4275 3400'),
(13, 15, '2008-06-01', '1970-01-01', '', 'y', '2011-05-31', 'y', 1837841, 861338397, '427555', 'LB72 0075 0000 0001 1350 4275 5500'),
(14, 16, '2007-07-23', '0000-00-00', NULL, '', '2010-07-22', '', NULL, NULL, '427523', 'LB45 0075 0000 0001 1350 4275 2300'),
(15, 17, '2011-09-01', '1970-01-01', '', 'y', '2014-08-31', 'y', 2386861, 861393051, '427537', 'LB75 0075 0000 0001 1350 4275 3700'),
(16, 18, '1970-01-01', '1970-01-01', '', 'y', '1970-01-01', 'y', 909318, NULL, 'N/A', 'N/A'),
(17, 19, '2006-09-01', '1970-01-01', '', 'y', '1970-01-01', 'y', 1110026, 811218306, '427530', 'LB60 0075 0000 0001 1350 4275 3000'),
(18, 20, '2000-07-09', '0000-00-00', NULL, '', '0000-00-00', '', NULL, NULL, '1102198005501.00', 'AE430260001102198005501'),
(19, 21, '2009-08-15', '1970-01-01', '', 'y', '2012-08-14', 'y', 2135973, 881397686, '427526', 'LB93 0075 0000 0001 1350 4275 2600'),
(20, 22, '2003-09-15', '0000-00-00', NULL, '', '0000-00-00', '', NULL, NULL, '1102198058601.00', 'AE030260001102198058601'),
(21, 23, '2008-04-15', '1970-01-01', '', 'y', '2011-04-14', 'y', 458024, 801141941, '427525', 'LB77 0075 0000 0001 1350 4275 2500'),
(22, 24, '2013-12-01', '1970-01-01', '', 'y', '2016-11-30', 'y', 2169576, 871378158, '429680', 'LB25 0075 0000 0001 1350 4296 8000'),
(23, 25, '2013-04-01', '1970-01-01', '', 'y', '2016-03-31', 'y', 2847129, 901553935, '429053', 'LB81 0075 0000 0001 1350 4290 5300'),
(24, 26, '2012-01-10', '1970-01-01', '', 'y', '2015-01-09', 'y', 1483265, 861288676, '427540', 'LB26 0075 0000 0001 1350 4275 4000'),
(25, 27, '2013-10-01', '1970-01-01', '', 'y', '2016-09-30', 'y', NULL, NULL, '404524', 'LB79 0075 0000 0001 1350 4045 2400'),
(26, 28, '2007-07-23', '0000-00-00', NULL, '', '2010-07-22', '', NULL, NULL, '1102428575701.00', 'AE410260001102428575701'),
(27, 29, '2013-08-01', '1970-01-01', '', 'y', '2016-07-31', 'y', 2880221, 921565662, '429328', 'LB19 0075 0000 0001 1350 4293 2800'),
(28, 30, '2006-08-01', '1970-01-01', '', 'y', '1970-01-01', 'y', 1371690, 831266951, '460914', 'LB25 0075 0000 0001 1350 4609 1400'),
(29, 31, '2006-11-01', '1970-01-01', '', 'y', '1970-01-01', 'y', 1349970, 821230594, '427531', 'LB76 0075 0000 0001 1350 4275 3100'),
(30, 32, '2010-10-01', '1970-01-01', '', 'y', '2012-09-30', 'y', 2466937, 871442645, '427520', 'LB94 0075 0000 0001 1350 4275 2000'),
(31, 33, '2011-07-04', '1970-01-01', '', 'y', '2014-07-03', 'y', 2718799, 891478883, '427532', 'LB92 0075 0000 0001 1350 4275 3200'),
(32, 34, '2010-10-01', '1970-01-01', '', 'y', '2012-09-30', 'y', 1815561, 881331674, '427517', 'LB46 0075 0000 0001 1350 4275 1700'),
(33, 35, '2005-07-22', '1970-01-01', '', 'y', '2012-06-30', 'y', 303061, 801266952, '427516', 'LB30 0075 0000 0001 1350 4275 1600'),
(34, 1, NULL, NULL, '', 'n', NULL, 'n', NULL, NULL, '', ''),
(35, 36, NULL, NULL, '', 'n', NULL, 'n', NULL, NULL, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_hr_details_old`
--

CREATE TABLE IF NOT EXISTS `user_hr_details_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `employment_date` date DEFAULT NULL,
  `evaluation_date` date DEFAULT NULL,
  `evaluation_batch` varchar(255) DEFAULT NULL,
  `contract_signed` enum('n','y') DEFAULT NULL,
  `contract_expiry_date` date DEFAULT NULL,
  `hr_manual_signed` enum('n','y') DEFAULT NULL,
  `mof` int(11) DEFAULT NULL,
  `ssnf` int(11) DEFAULT NULL,
  `bank_account` text,
  `iban` text,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `user_hr_details_old`
--

INSERT INTO `user_hr_details_old` (`id`, `id_user`, `employment_date`, `evaluation_date`, `evaluation_batch`, `contract_signed`, `contract_expiry_date`, `hr_manual_signed`, `mof`, `ssnf`, `bank_account`, `iban`) VALUES
(1, 3, NULL, NULL, '', 'n', NULL, 'n', 2829595, 901519618, '', ''),
(2, 4, NULL, NULL, '', 'n', NULL, 'n', NULL, NULL, '', ''),
(3, 5, '2011-09-05', '1970-01-01', '', 'y', '2014-09-04', 'y', 1957807, 861323274, '526186', 'LB75 0075 0000 0001 1350 5261 8600'),
(4, 6, '2010-09-01', '0000-00-00', NULL, '', '2012-08-31', '', NULL, NULL, '427536', 'LB59 0075 0000 0001 1350 4275 3600'),
(5, 7, '2014-07-21', '1970-01-01', '', 'y', '1970-01-01', 'y', NULL, 821348295, '', ''),
(6, 8, '2012-07-16', '1970-01-01', '', 'y', '2015-07-15', 'y', 2270221, 871446385, '427628', 'LB76 0075 0000 0001 1350 4276 2800'),
(7, 9, '2007-04-23', '1970-01-01', '', 'y', '2010-04-03', 'y', 1158587, 791219750, '647481', 'LB19 0075 0000 0001 1350 6474 8100'),
(8, 10, '2011-02-14', '1970-01-01', '', 'y', '2013-02-13', 'y', 2930407, 861389814, '427519', 'LB78 0075 0000 0001 1350 4275 1900'),
(9, 11, '2011-05-01', '1970-01-01', '', 'y', '2014-04-30', 'y', 1662305, 831243605, '299041', 'LB54 0075 0000 0001 1350 2990 4100'),
(10, 12, '2012-10-01', '1970-01-01', '', 'y', '2015-09-30', 'y', 2774065, 881436905, '065919', 'LB43 0075 0000 0001 1350 0659 1900'),
(11, 13, '2013-08-12', '1970-01-01', '', 'y', '2016-08-11', 'y', 2745449, 891517387, '429370', 'LB12 0075 0000 0001 1350 4293 7000'),
(12, 14, '2010-03-01', '1970-01-01', '', 'y', '2013-02-28', 'y', 184771, 751032724, '427534', 'LB27 0075 0000 0001 1350 4275 3400'),
(13, 15, '2008-06-01', '1970-01-01', '', 'y', '2011-05-31', 'y', 1837841, 861338397, '427555', 'LB72 0075 0000 0001 1350 4275 5500'),
(14, 16, '2007-07-23', '0000-00-00', NULL, '', '2010-07-22', '', NULL, NULL, '427523', 'LB45 0075 0000 0001 1350 4275 2300'),
(15, 17, '2011-09-01', '1970-01-01', '', 'y', '2014-08-31', 'y', 2386861, 861393051, '427537', 'LB75 0075 0000 0001 1350 4275 3700'),
(16, 18, '1970-01-01', '1970-01-01', '', 'y', '1970-01-01', 'y', 909318, NULL, 'N/A', 'N/A'),
(17, 19, '2006-09-01', '1970-01-01', '', 'y', '1970-01-01', 'y', 1110026, 811218306, '427530', 'LB60 0075 0000 0001 1350 4275 3000'),
(18, 20, '2000-07-09', '0000-00-00', NULL, '', '0000-00-00', '', NULL, NULL, '1102198005501.00', 'AE430260001102198005501'),
(19, 21, '2009-08-15', '1970-01-01', '', 'y', '2012-08-14', 'y', 2135973, 881397686, '427526', 'LB93 0075 0000 0001 1350 4275 2600'),
(20, 22, '2003-09-15', '0000-00-00', NULL, '', '0000-00-00', '', NULL, NULL, '1102198058601.00', 'AE030260001102198058601'),
(21, 23, '2008-04-15', '1970-01-01', '', 'y', '2011-04-14', 'y', 458024, 801141941, '427525', 'LB77 0075 0000 0001 1350 4275 2500'),
(22, 24, '2013-12-01', '1970-01-01', '', 'y', '2016-11-30', 'y', 2169576, 871378158, '429680', 'LB25 0075 0000 0001 1350 4296 8000'),
(23, 25, '2013-04-01', '1970-01-01', '', 'y', '2016-03-31', 'y', 2847129, 901553935, '429053', 'LB81 0075 0000 0001 1350 4290 5300'),
(24, 26, '2012-01-10', '1970-01-01', '', 'y', '2015-01-09', 'y', 1483265, 861288676, '427540', 'LB26 0075 0000 0001 1350 4275 4000'),
(25, 27, '2013-10-01', '1970-01-01', '', 'y', '2016-09-30', 'y', NULL, NULL, '404524', 'LB79 0075 0000 0001 1350 4045 2400'),
(26, 28, '2007-07-23', '0000-00-00', NULL, '', '2010-07-22', '', NULL, NULL, '1102428575701.00', 'AE410260001102428575701'),
(27, 29, '2013-08-01', '1970-01-01', '', 'y', '2016-07-31', 'y', 2880221, 921565662, '429328', 'LB19 0075 0000 0001 1350 4293 2800'),
(28, 30, '2006-08-01', '1970-01-01', '', 'y', '1970-01-01', 'y', 1371690, 831266951, '460914', 'LB25 0075 0000 0001 1350 4609 1400'),
(29, 31, '2006-11-01', '1970-01-01', '', 'y', '1970-01-01', 'y', 1349970, 821230594, '427531', 'LB76 0075 0000 0001 1350 4275 3100'),
(30, 32, '2010-10-01', '1970-01-01', '', 'y', '2012-09-30', 'y', 2466937, 871442645, '427520', 'LB94 0075 0000 0001 1350 4275 2000'),
(31, 33, '2011-07-04', '1970-01-01', '', 'y', '2014-07-03', 'y', 2718799, 891478883, '427532', 'LB92 0075 0000 0001 1350 4275 3200'),
(32, 34, '2010-10-01', '1970-01-01', '', 'y', '2012-09-30', 'y', 1815561, 881331674, '427517', 'LB46 0075 0000 0001 1350 4275 1700'),
(33, 35, '2005-07-22', '1970-01-01', '', 'y', '2012-06-30', 'y', 303061, 801266952, '427516', 'LB30 0075 0000 0001 1350 4275 1600'),
(34, 1, NULL, NULL, '', 'n', NULL, 'n', NULL, NULL, '', ''),
(35, 36, NULL, NULL, '', 'n', NULL, 'n', NULL, NULL, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_personal_details`
--

CREATE TABLE IF NOT EXISTS `user_personal_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `skype_id` varchar(255) DEFAULT NULL,
  `gender` enum('f','m') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `marital_status` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `branch` int(11) DEFAULT NULL,
  `unit` int(11) DEFAULT NULL,
  `line_manager` int(11) DEFAULT NULL,
  `home_address` text,
  `mobile` varchar(20) DEFAULT NULL,
  `ice_contact` varchar(255) DEFAULT NULL,
  `ice_mobile` varchar(255) DEFAULT NULL,
  `extension` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `line_manager` (`line_manager`),
  KEY `branch` (`branch`),
  KEY `unit` (`unit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `user_personal_details`
--

INSERT INTO `user_personal_details` (`id`, `id_user`, `email`, `skype_id`, `gender`, `birthdate`, `nationality`, `marital_status`, `job_title`, `branch`, `unit`, `line_manager`, `home_address`, `mobile`, `ice_contact`, `ice_mobile`, `extension`) VALUES
(1, 3, 'ramy.khattar@sns-emea.com', 'khattar_ramy', 'm', '1990-10-04', 'Lebanese', 'single', 'Technical Consultant', 31, 29, 1, 'Lebanon , Zalka , Relax Street , Bloc C , 2nd Floor', '70206002', '', '', ''),
(2, 4, 'Micheline.Daaboul@sns-emea.com', '', 'f', '1981-03-28', 'Lebanese', 'married', 'Admin & Marketing Manager', 31, 29, 1, '', '', '', '', ''),
(3, 5, 'Alexandre.Tarabay@sns-emea.com', 'alextarabay', 'm', '1986-07-14', 'Lebanese', 'married', 'Senior Technical Consultant', 31, 116, 1, 'Jbeil, St Georges Street, Issam Bassil Bldg, 1st Fl', '+961 3 357772', 'Marielle Hayek', '+961 3 965305', ''),
(4, 6, 'allen.zeidan@sns-emea.com', 'allen.zeidan', 'm', '1983-03-09', 'Lebanese', 'single', 'Operation Lead', 31, 29, 1, 'Mohamad Zeidan Bldg., Majdeleon, Saida', '+961 3 635631', NULL, NULL, NULL),
(5, 7, 'anthony.saadeh@sns-emea.com', '', 'm', '1982-08-07', 'Lebanese', 'single', 'System Administrator', 31, 29, 1, 'Adonis, Zouk Mosbeh, Street Nb 53, Michel Nicolas Bldg., 4th Floor', '+96170962960', '', '', ''),
(6, 8, 'bassel.khodr@sns-emea.com', 'bassel.sns', 'm', '1987-06-30', 'Lebanese', 'single', 'Technical Specialist', 31, 29, 1, 'Hamra, Antoun Jmayel Street, Beverly Hills Suites 1, 3rd Floor', '+961 3 119976', '', '', ''),
(7, 9, 'Bernard.Khazzaka@sns-emea.com', 'bernardk1979', 'm', '1979-10-22', 'Lebanese', 'single', 'Technical Manager', 31, 29, 1, 'Mansourieh, Old St., face Sayde center, Chahoud Bldg', '+961 3 16 06 63', '', '', ''),
(8, 10, 'Charbel.Azzi@sns-emea.com', 'cnazzi', 'm', '1986-08-05', 'Lebanese', 'single', 'Operational Specialist ', 31, 29, 1, '6th Fl., Block A, High Land Center, Saydet Al Najat Street, Zalka, Metn,                                           ', '+961 3 827881', '', '', ''),
(9, 11, 'Claudia.Daaboul@sns-emea.com', 'claudine.daaboul', 'f', '1983-04-03', 'Lebanese', 'single', 'Recruitment Consultant', 31, 29, 1, 'Ashrafieh, Chahrouri Street, Mohammad El Harati Bldg., 5th Floor', '+961 3 054387', '', '', ''),
(10, 12, 'denise.ibrahim@sns-emea.com', 'ibrahim.denise', 'f', '1988-12-20', 'Lebanese', 'single', 'Technical Specialist', 31, 29, 1, 'Rawda, St Joseph Street, Dr. Edmond Zayat Bldg., 3rd Floor', '+961 70 121626', '', '', ''),
(11, 13, 'diana.jabbour@sns-emea.com', 'diana.jabbour', 'f', '1989-09-15', 'Lebanese', 'single', 'Systems Administrator ', 31, 29, 1, 'Ain El Jdideh, Al Saydeh Str., Gabriel Jabbour Bldg., Ground Floor', '+961 76 414148', '', '', ''),
(12, 14, 'Emile.bassil@sns-emea.com', 'ebassil', 'm', '1975-01-01', 'Lebanese', 'married', 'Senior Consultant', 31, 29, 1, 'Mezher, Zone 1, Street 60, Safi Bldg., 3rd Floor ', '+961 70 511919', '', '', ''),
(13, 15, 'Ghina.Karame@sns-emea.com', 'ghina_karame', 'f', '1986-01-25', 'Lebanese', 'married', 'Techincal Lead', 31, 29, 1, 'Tripoli, Nakabet El Ateba St., Dounia Center facing Al Faysal Restaurant, 3rd fl', '+961 3 552199', '', '', ''),
(14, 16, 'Hussein.Naim@sns-emea.com', 'husseinnaim', 'm', '1985-11-13', 'Lebanese', 'married', 'Senior Consultant', 31, 29, 1, '38 Farrer Road, #02-02 The Levelz, Singapore 268836', '+961 3 92 17 76/+65 ', NULL, NULL, NULL),
(15, 17, 'Joseph.Rahme@sns-emea.com', 'joe.rahmeh1', 'm', '1986-06-13', 'Lebanese', 'single', 'Techincal Specialist', 31, 29, 1, 'Beit El Chaar, St Elie Street, Bsaibes bldg., Facing COOP St. Michel, 1st Floor', '+961 70 150432', '', '', ''),
(16, 18, 'Mario.Ghosn@sns-emea.com', 'marioghosn', 'm', '1976-05-16', 'Lebanese', 'married', 'General Manager', 31, 29, 1, '4th floor, Samir Samara Bldg., 20m after Sleep Comfort Exit, Baabda', '+961 70 112441/+971 ', '', '', ''),
(17, 19, 'Micheline.Daaboul@sns-emea.com', 'micha_daaboul', 'f', '1981-03-28', 'Lebanese', 'married', 'Admin & Marketing Manager', 31, 29, 1, 'Naccache, Near Supermarket Elie Azar, Edmond El Ashkar Bldg., 2nd fl', '+961 70 333746', '', '', ''),
(18, 20, 'Mohammed.Obaidah@sns-emea.com', '', 'm', '1978-01-25', 'Lebanese', 'married', 'Director of Services', 31, 29, 1, 'N/A', '+971 55 200 8484', NULL, NULL, NULL),
(19, 21, 'Muhammed.Itani@sns-emea.com', 'itanims', 'm', '1988-07-04', 'Lebanese', 'single', 'Technical Specialist', 31, 29, 1, '5th fl, Hamwi Bldg., Malla Istiklal Street, Beirut', '+961 70 136524', '', '', ''),
(20, 22, 'Nadim.Klat@sns-emea.com', 'nadim.klat', 'm', '1980-09-09', 'Lebanese', 'married', 'Senior Manager', 31, 29, 1, 'N/A', '+971 55 2008474', NULL, NULL, NULL),
(21, 23, 'Nadine.Abboud@sns-emea.com', 'najinadine1', 'f', '1980-08-30', 'Lebanese', 'married', 'Office Assistant', 31, 29, 1, 'Zouk Mekhael, Sannine St., Youssef Tannous Bldg., 3rd Fl', '+961 71 203403', '', '', ''),
(22, 24, 'Naji.AbdelKhalek@sns-emea.com', 'naji.abdelkhalek', 'm', '1987-01-27', 'Lebanese', 'single', 'Operational \r\nSpecialist \r\nOperational \r\nSpecialist \r\nOperational \r\nSpecialist \r\nOperational Specialist ', 31, 29, 1, 'Ground Fl, Abdel Khalek Bldg., Public Square, Shaney, Aley, Lebanon', '+961 3 038709', '', '', ''),
(23, 25, 'paul.donikian@sns-emea.com', 'paul.donikian', 'm', '1990-08-06', 'Lebanese', 'single', 'Technical Consultant ', 31, 29, 1, 'Dora, Semiramis St., Antoine Mismis Bldg., 2nd Floor', '+961 71 744363', '', '', ''),
(24, 26, 'Rami.Allam@sns-emea.com', 'rami_allam', 'm', '1986-10-27', 'Lebanese', 'single', 'Operational Consultant', 31, 29, 1, 'Beirut, Dekwaneh, Slaf Str., Elias Sawaya Bldg., 2nd Fl', '+961 70 946164', '', '', ''),
(25, 27, 'ramy.khattar@sns-emea.com', 'khattar_ramy', 'm', '1990-10-04', 'Lebanese', 'single', 'Technical Consultant ', 31, 29, 1, 'Zalka ,Relax street ,Block C ,2nd Floor .', '+961 70 206 002', '', '', ''),
(26, 28, 'Ramzi.Ballout@sns-emea.com', 'ramzi.ballout', 'm', '1983-05-08', 'Lebanese', 'single', 'Technical Lead', 31, 29, 1, 'Verdun, Abdullah El Mashnouk St., Kaaki Bldg., 5th Fl', '+971 55 2008488', NULL, NULL, NULL),
(27, 29, 'samer.saad@sns-emea.com', 'samer.saad1', 'm', '1992-09-21', 'Lebanese', 'single', 'Operational Consultant ', 31, 29, 1, 'Naccache, Area 6, Street 55, Saad Madi Bldg, 2nd Floor', '+961 3 516352', '', '', ''),
(28, 30, 'Serge.Abou.Slaiby@sns-emea.com', 'sacrage', 'm', '1983-07-05', 'Lebanese', 'single', 'Project Manager', 31, 29, 1, 'Naccache, Jamileh Chbib Bldg, Jamileh Chbib Street.', '+961 3 95 20 83', '', '', ''),
(29, 31, 'Simon.Kosseifi@sns-emea.com', 'simon.kosseifi', 'm', '1983-10-31', 'Lebanese', 'married', 'Senior Technica Consultant', 31, 29, 1, 'Amchit, Najib El Khoury St., Khalil El Kosseifi Bldg', '+961 3 157471', '', '', ''),
(30, 32, 'Tarek.Husseini@sns-emea.com', 'nino.1337', 'm', '1988-07-16', 'Lebanese', 'single', 'Technical Specialist  ', 31, 29, 1, 'Tayouneh, Kinge & Itani Street, Bdeir Bldg., 12 Floor', '+961 3 578123', '', '', ''),
(31, 33, 'Teddy.Richa@sns-emea.com', 'teddy.richa', 'm', '1989-09-17', 'Lebanese', 'single', 'Operational Specialist', 31, 29, 1, 'Beit El Chaar, Al Hazira Street, Sleiman Chebli’s building, 4th floor.', '+961 3 645875', '', '', ''),
(32, 34, 'Tony.Oudaimy@sns-emea.com', 'tony.oudaimy', 'm', '1988-01-01', 'Lebanese', 'single', 'Technical Specialist', 31, 29, 1, 'Hazmieh, St Paul & Peter Street, Elie Khoury Bldg., 4th Fl', '+961 3 225289', '', '', ''),
(33, 35, 'Wael.Mabsout@sns-emea.com', 'waelmabsout', 'm', '1980-06-03', 'Lebanese', 'married', 'Senior Manager', 31, 29, 1, 'Hazmieh, Mar Takla, Elias Houbaika St., Saleh Haddad Bldg., 2nd fl', '+961 3 44 56 72', '', '', ''),
(34, 1, 'mario.ghosn@sns-emea.com', '', 'm', NULL, '', 'single', 'General Manager', NULL, NULL, 18, '', '', '', '', ''),
(35, 36, 'snsit@sns.com', '', 'm', '2014-10-07', '', 'single', '', 31, 29, 16, '', '123465', '123', '123', '123');

-- --------------------------------------------------------

--
-- Table structure for table `user_personal_details_old`
--

CREATE TABLE IF NOT EXISTS `user_personal_details_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `skype_id` varchar(255) DEFAULT NULL,
  `gender` enum('f','m') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `marital_status` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `branch` int(11) DEFAULT NULL,
  `unit` int(11) DEFAULT NULL,
  `line_manager` int(11) DEFAULT NULL,
  `home_address` text,
  `mobile` varchar(20) DEFAULT NULL,
  `ice_contact` varchar(255) DEFAULT NULL,
  `ice_mobile` varchar(255) DEFAULT NULL,
  `extension` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`) USING BTREE,
  KEY `line_manager` (`line_manager`) USING BTREE,
  KEY `branch` (`branch`) USING BTREE,
  KEY `unit` (`unit`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `user_personal_details_old`
--

INSERT INTO `user_personal_details_old` (`id`, `id_user`, `email`, `skype_id`, `gender`, `birthdate`, `nationality`, `marital_status`, `job_title`, `branch`, `unit`, `line_manager`, `home_address`, `mobile`, `ice_contact`, `ice_mobile`, `extension`) VALUES
(1, 3, 'ramy.khattar@sns-emea.com', 'khattar_ramy', 'm', '1990-10-04', 'Lebanese', 'single', 'Technical Consultant', 31, 29, 1, 'Lebanon , Zalka , Relax Street , Bloc C , 2nd Floor', '70206002', '', '', ''),
(2, 4, 'Micheline.Daaboul@sns-emea.com', '', 'f', '1981-03-28', 'Lebanese', 'married', 'Admin & Marketing Manager', 31, 29, 1, '', '', '', '', ''),
(3, 5, 'Alexandre.Tarabay@sns-emea.com', 'alextarabay', 'm', '1986-07-14', 'Lebanese', 'married', 'Senior Technical Consultant', 31, 116, 1, 'Jbeil, St Georges Street, Issam Bassil Bldg, 1st Fl', '+961 3 357772', 'Marielle Hayek', '+961 3 965305', ''),
(4, 6, 'allen.zeidan@sns-emea.com', 'allen.zeidan', 'm', '1983-03-09', 'Lebanese', 'single', 'Operation Lead', 31, 29, 1, 'Mohamad Zeidan Bldg., Majdeleon, Saida', '+961 3 635631', NULL, NULL, NULL),
(5, 7, 'anthony.saadeh@sns-emea.com', '', 'm', '1982-08-07', 'Lebanese', 'single', 'System Administrator', 31, 29, 1, 'Adonis, Zouk Mosbeh, Street Nb 53, Michel Nicolas Bldg., 4th Floor', '+96170962960', '', '', ''),
(6, 8, 'bassel.khodr@sns-emea.com', 'bassel.sns', 'm', '1987-06-30', 'Lebanese', 'single', 'Technical Specialist', 31, 29, 1, 'Hamra, Antoun Jmayel Street, Beverly Hills Suites 1, 3rd Floor', '+961 3 119976', '', '', ''),
(7, 9, 'Bernard.Khazzaka@sns-emea.com', 'bernardk1979', 'm', '1979-10-22', 'Lebanese', 'single', 'Technical Manager', 31, 29, 1, 'Mansourieh, Old St., face Sayde center, Chahoud Bldg', '+961 3 16 06 63', '', '', ''),
(8, 10, 'Charbel.Azzi@sns-emea.com', 'cnazzi', 'm', '1986-08-05', 'Lebanese', 'single', 'Operational Specialist ', 31, 29, 1, '6th Fl., Block A, High Land Center, Saydet Al Najat Street, Zalka, Metn,                                           ', '+961 3 827881', '', '', ''),
(9, 11, 'Claudia.Daaboul@sns-emea.com', 'claudine.daaboul', 'f', '1983-04-03', 'Lebanese', 'single', 'Recruitment Consultant', 31, 29, 1, 'Ashrafieh, Chahrouri Street, Mohammad El Harati Bldg., 5th Floor', '+961 3 054387', '', '', ''),
(10, 12, 'denise.ibrahim@sns-emea.com', 'ibrahim.denise', 'f', '1988-12-20', 'Lebanese', 'single', 'Technical Specialist', 31, 29, 1, 'Rawda, St Joseph Street, Dr. Edmond Zayat Bldg., 3rd Floor', '+961 70 121626', '', '', ''),
(11, 13, 'diana.jabbour@sns-emea.com', 'diana.jabbour', 'f', '1989-09-15', 'Lebanese', 'single', 'Systems Administrator ', 31, 29, 1, 'Ain El Jdideh, Al Saydeh Str., Gabriel Jabbour Bldg., Ground Floor', '+961 76 414148', '', '', ''),
(12, 14, 'Emile.bassil@sns-emea.com', 'ebassil', 'm', '1975-01-01', 'Lebanese', 'married', 'Senior Consultant', 31, 29, 1, 'Mezher, Zone 1, Street 60, Safi Bldg., 3rd Floor ', '+961 70 511919', '', '', ''),
(13, 15, 'Ghina.Karame@sns-emea.com', 'ghina_karame', 'f', '1986-01-25', 'Lebanese', 'married', 'Techincal Lead', 31, 29, 1, 'Tripoli, Nakabet El Ateba St., Dounia Center facing Al Faysal Restaurant, 3rd fl', '+961 3 552199', '', '', ''),
(14, 16, 'Hussein.Naim@sns-emea.com', 'husseinnaim', 'm', '1985-11-13', 'Lebanese', 'married', 'Senior Consultant', 31, 29, 1, '38 Farrer Road, #02-02 The Levelz, Singapore 268836', '+961 3 92 17 76/+65 ', NULL, NULL, NULL),
(15, 17, 'Joseph.Rahme@sns-emea.com', 'joe.rahmeh1', 'm', '1986-06-13', 'Lebanese', 'single', 'Techincal Specialist', 31, 29, 1, 'Beit El Chaar, St Elie Street, Bsaibes bldg., Facing COOP St. Michel, 1st Floor', '+961 70 150432', '', '', ''),
(16, 18, 'Mario.Ghosn@sns-emea.com', 'marioghosn', 'm', '1976-05-16', 'Lebanese', 'married', 'General Manager', 31, 29, 1, '4th floor, Samir Samara Bldg., 20m after Sleep Comfort Exit, Baabda', '+961 70 112441/+971 ', '', '', ''),
(17, 19, 'Micheline.Daaboul@sns-emea.com', 'micha_daaboul', 'f', '1981-03-28', 'Lebanese', 'married', 'Admin & Marketing Manager', 31, 29, 1, 'Naccache, Near Supermarket Elie Azar, Edmond El Ashkar Bldg., 2nd fl', '+961 3 50 21 96', '', '', ''),
(18, 20, 'Mohammed.Obaidah@sns-emea.com', '', 'm', '1978-01-25', 'Lebanese', 'married', 'Director of Services', 31, 29, 1, 'N/A', '+971 55 200 8484', NULL, NULL, NULL),
(19, 21, 'Muhammed.Itani@sns-emea.com', 'itanims', 'm', '1988-07-04', 'Lebanese', 'single', 'Technical Specialist', 31, 29, 1, '5th fl, Hamwi Bldg., Malla Istiklal Street, Beirut', '+961 70 136524', '', '', ''),
(20, 22, 'Nadim.Klat@sns-emea.com', 'nadim.klat', 'm', '1980-09-09', 'Lebanese', 'married', 'Senior Manager', 31, 29, 1, 'N/A', '+971 55 2008474', NULL, NULL, NULL),
(21, 23, 'Nadine.Abboud@sns-emea.com', 'najinadine1', 'f', '1980-08-30', 'Lebanese', 'married', 'Office Assistant', 31, 29, 1, 'Zouk Mekhael, Sannine St., Youssef Tannous Bldg., 3rd Fl', '+961 71 203403', '', '', ''),
(22, 24, 'Naji.AbdelKhalek@sns-emea.com', 'naji.abdelkhalek', 'm', '1987-01-27', 'Lebanese', 'single', 'Operational \r\nSpecialist \r\nOperational \r\nSpecialist \r\nOperational \r\nSpecialist \r\nOperational Specialist ', 31, 29, 1, 'Ground Fl, Abdel Khalek Bldg., Public Square, Shaney, Aley, Lebanon', '+961 3 038709', '', '', ''),
(23, 25, 'paul.donikian@sns-emea.com', 'paul.donikian', 'm', '1990-08-06', 'Lebanese', 'single', 'Technical Consultant ', 31, 29, 1, 'Dora, Semiramis St., Antoine Mismis Bldg., 2nd Floor', '+961 71 744363', '', '', ''),
(24, 26, 'Rami.Allam@sns-emea.com', 'rami_allam', 'm', '1986-10-27', 'Lebanese', 'single', 'Operational Consultant', 31, 29, 1, 'Beirut, Dekwaneh, Slaf Str., Elias Sawaya Bldg., 2nd Fl', '+961 70 946164', '', '', ''),
(25, 27, 'ramy.khattar@sns-emea.com', 'khattar_ramy', 'm', '1990-10-04', 'Lebanese', 'single', 'Technical Consultant ', 31, 29, 1, 'Zalka ,Relax street ,Block C ,2nd Floor .', '+961 70 206 002', '', '', ''),
(26, 28, 'Ramzi.Ballout@sns-emea.com', 'ramzi.ballout', 'm', '1983-05-08', 'Lebanese', 'single', 'Technical Lead', 31, 29, 1, 'Verdun, Abdullah El Mashnouk St., Kaaki Bldg., 5th Fl', '+971 55 2008488', NULL, NULL, NULL),
(27, 29, 'samer.saad@sns-emea.com', 'samer.saad1', 'm', '1992-09-21', 'Lebanese', 'single', 'Operational Consultant ', 31, 29, 1, 'Naccache, Area 6, Street 55, Saad Madi Bldg, 2nd Floor', '+961 3 516352', '', '', ''),
(28, 30, 'Serge.Abou.Slaiby@sns-emea.com', 'sacrage', 'm', '1983-07-05', 'Lebanese', 'single', 'Project Manager', 31, 29, 1, 'Naccache, Jamileh Chbib Bldg, Jamileh Chbib Street.', '+961 3 95 20 83', '', '', ''),
(29, 31, 'Simon.Kosseifi@sns-emea.com', 'simon.kosseifi', 'm', '1983-10-31', 'Lebanese', 'married', 'Senior Technica Consultant', 31, 29, 1, 'Amchit, Najib El Khoury St., Khalil El Kosseifi Bldg', '+961 3 157471', '', '', ''),
(30, 32, 'Tarek.Husseini@sns-emea.com', 'nino.1337', 'm', '1988-07-16', 'Lebanese', 'single', 'Technical Specialist', 31, 29, 31, 'Tayouneh, Kinge & Itani Street, Bdeir Bldg., 12 Floor', '+961 3 578123', '', '', ''),
(31, 33, 'Teddy.Richa@sns-emea.com', 'teddy.richa', 'm', '1989-09-17', 'Lebanese', 'single', 'Operational Specialist', 31, 29, 1, 'Beit El Chaar, Al Hazira Street, Sleiman Chebli’s building, 4th floor.', '+961 3 645875', '', '', ''),
(32, 34, 'Tony.Oudaimy@sns-emea.com', 'tony.oudaimy', 'm', '1988-01-01', 'Lebanese', 'single', 'Technical Specialist', 31, 29, 1, 'Hazmieh, St Paul & Peter Street, Elie Khoury Bldg., 4th Fl', '+961 3 225289', '', '', ''),
(33, 35, 'Wael.Mabsout@sns-emea.com', 'waelmabsout', 'm', '1980-06-03', 'Lebanese', 'married', 'Senior Manager', 31, 29, 1, 'Hazmieh, Mar Takla, Elias Houbaika St., Saleh Haddad Bldg., 2nd fl', '+961 3 44 56 72', '', '', ''),
(34, 1, 'mario.ghosn@sns-emea.com', '', 'm', NULL, '', 'single', 'General Manager', NULL, NULL, 18, '', '', '', '', ''),
(35, 36, 'snsme@sns-emea.com', '', 'm', '2014-10-14', '', 'single', '', NULL, 29, 1, '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_task`
--

CREATE TABLE IF NOT EXISTS `user_task` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_user` int(10) NOT NULL,
  `id_task` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_user` (`id_user`,`id_task`),
  KEY `id_user_2` (`id_user`),
  KEY `id_task` (`id_task`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user_task`
--

INSERT INTO `user_task` (`id`, `id_user`, `id_task`) VALUES
(1, 1, 1),
(2, 3, 1),
(3, 4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_task_old`
--

CREATE TABLE IF NOT EXISTS `user_task_old` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_user` int(10) NOT NULL,
  `id_task` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_user` (`id_user`,`id_task`) USING BTREE,
  KEY `id_user_2` (`id_user`) USING BTREE,
  KEY `id_task` (`id_task`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_time`
--

CREATE TABLE IF NOT EXISTS `user_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_task` int(11) NOT NULL,
  `id_timesheet` int(11) NOT NULL,
  `amount` float(4,2) NOT NULL,
  `comment` text NOT NULL,
  `date` date NOT NULL,
  `default` int(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 => default, 1 => approved, -1=> rejected ',
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_task` (`id_task`),
  KEY `id_timesheet` (`id_timesheet`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `user_time`
--

INSERT INTO `user_time` (`id`, `id_user`, `id_task`, `id_timesheet`, `amount`, `comment`, `date`, `default`, `status`) VALUES
(6, 18, 71, 366, 8.00, 'testing', '2014-11-03', 1, 0),
(7, 18, 71, 366, 2.00, 'testing', '2014-11-04', 1, 0),
(8, 18, 41, 366, 0.00, '', '2014-11-03', 1, 0),
(9, 18, 49, 366, 0.00, '', '2014-11-03', 1, 0),
(10, 18, 143, 366, 0.00, '', '2014-11-03', 1, 0),
(11, 18, 40, 366, 6.00, 'qwe', '2014-11-03', 1, 0),
(12, 18, 41, 366, 0.00, '', '2014-11-03', 1, 0),
(13, 18, 40, 366, 8.00, 'qwe', '2014-11-06', 1, 0),
(14, 18, 40, 366, 8.00, 'asd', '2014-11-08', 1, 0),
(15, 18, 40, 366, 0.00, '', '2014-11-09', 1, 0),
(16, 3, 28, 351, 8.00, 'a', '2014-11-03', 1, 0),
(17, 3, 28, 351, 8.00, 'a', '2014-11-03', 1, 0),
(18, 3, 28, 351, 8.00, 'a', '2014-11-03', 1, 0),
(19, 3, 29, 351, 0.00, '', '2014-11-03', 1, 0),
(20, 3, 28, 351, 8.00, 'b', '2014-11-04', 1, 0),
(21, 3, 28, 351, 8.00, 'c', '2014-11-05', 1, 0),
(22, 3, 28, 351, 8.00, 'd', '2014-11-06', 1, 0),
(23, 3, 28, 351, 8.00, 'e', '2014-11-07', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_time_old`
--

CREATE TABLE IF NOT EXISTS `user_time_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_task` int(11) NOT NULL,
  `id_timesheet` int(11) NOT NULL,
  `amount` float(4,2) NOT NULL,
  `comment` text NOT NULL,
  `date` date NOT NULL,
  `default` int(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 => default, 1 => approved, -1=> rejected ',
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`) USING BTREE,
  KEY `id_task` (`id_task`) USING BTREE,
  KEY `id_timesheet` (`id_timesheet`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_visas`
--

CREATE TABLE IF NOT EXISTS `user_visas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `expiry_date` date NOT NULL,
  `visa_type` enum('multiple','single') NOT NULL,
  `duration_of_stay` varchar(255) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_visas_old`
--

CREATE TABLE IF NOT EXISTS `user_visas_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `expiry_date` date NOT NULL,
  `visa_type` enum('multiple','single') NOT NULL,
  `duration_of_stay` varchar(255) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_widgets`
--

CREATE TABLE IF NOT EXISTS `user_widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `widget_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `user_widgets`
--

INSERT INTO `user_widgets` (`id`, `widget_id`, `user_id`, `order`) VALUES
(1, 16, 1, 1),
(2, 11, 1, 2),
(3, 7, 1, 1),
(4, 5, 1, 4),
(5, 15, 1, 3),
(6, 8, 1, 4),
(7, 9, 1, 5),
(8, 1, 1, 6),
(10, 6, 1, 2),
(11, 1, 3, 1),
(12, 4, 1, 7),
(13, 6, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_widgets_old`
--

CREATE TABLE IF NOT EXISTS `user_widgets_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `widget_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `user_widgets_old`
--

INSERT INTO `user_widgets_old` (`id`, `widget_id`, `user_id`, `order`) VALUES
(1, 16, 1, 1),
(2, 11, 1, 2),
(3, 7, 1, 1),
(4, 5, 1, 4),
(5, 15, 1, 3),
(6, 8, 1, 4),
(7, 9, 1, 5),
(8, 1, 1, 6),
(10, 6, 1, 2),
(11, 1, 3, 1),
(12, 4, 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(50) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0->not active, 1->active',
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `password`, `active`, `type`) VALUES
(1, 'admin', 'admin', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 0),
(3, 'Ramy', 'Khattar', 'RamyK', '032bbe260e3d353d24f0df96ca56908455c1235f', 1, 0),
(4, 'Micheline', 'Daaboul', 'Micha', '68cd3c77c8425de81cbde4045dc2a72689b6503d', 0, 0),
(5, 'Alexandre ', 'Tarabay', 'atarabay', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(6, 'Allen ', 'Zeidan', 'azeidan', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(7, 'Anthony ', 'Saadeh', 'asaadeh', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 0, 0),
(8, 'Bassel ', 'Khodr', 'bkhodr', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(9, 'Bernard ', 'Khazzaka', 'bkhazzaka', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(10, 'Charbel ', 'Azzi', 'cazzi', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(11, 'Claudine ', 'Daaboul', 'cdaaboul', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(12, 'Denise ', 'Ibrahim', 'dibrahim', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(13, 'Diana ', 'Jabbour', 'djabbour', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(14, 'Emile ', 'Bassil', 'ebassil', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(15, 'Ghina ', 'Karame', 'gkarame', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(16, 'Hussein ', 'Naim', 'hnaim', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(17, 'Joseph ', 'Rahme', 'jrahme', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(18, 'Mario ', 'Ghosn', 'mghosn', '52c08f78592723fad2d33e5313dcf2a8cded3837', 1, 0),
(19, 'Micheline ', 'Daaboul', 'mdaaboul', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(20, 'Mohammed ', 'Obaidah', 'mobaidah', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(21, 'Mohammed', ' Itani', 'mitani', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(22, 'Nadim ', 'Klat', 'nklat', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(23, 'Nadine ', 'Abboud', 'nabboud', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(24, 'Naji ', 'Abdel Khalek', 'nabdelkhalek', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(25, 'Paul ', 'Donikian', 'pdonikian', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(26, 'Rami', 'Allam', 'rallam', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(27, 'Ramy ', 'Khattar', 'rkhattar', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(28, 'Ramzi ', 'Ballout', 'rballout', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(29, 'Samer ', 'Saad', 'ssaad', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(30, 'Serge ', 'Abou Slaiby', 'sabouslaiby', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(31, 'Simon ', 'El Kosseifi', 'skosseifi', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(32, 'Tarek ', 'El Husseini', 'Rek', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(33, 'Teddy ', 'Richa', 'tricha', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(34, 'Antoine', 'Oudaimy', 'aoudaimy', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(35, 'Wael', 'El Mabsout', 'wmabsout', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(36, 'snsme', 'snsme', 'snsme', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users_copy`
--

CREATE TABLE IF NOT EXISTS `users_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(50) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0->not active, 1->active',
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `users_copy`
--

INSERT INTO `users_copy` (`id`, `firstname`, `lastname`, `username`, `password`, `active`, `type`) VALUES
(1, 'admin', 'admin', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 0),
(3, 'Ramy', 'Khattar', 'RamyK', '032bbe260e3d353d24f0df96ca56908455c1235f', 1, 0),
(4, 'Micheline', 'Daaboul', 'Micha', '05a7cf9cdd7225711881e5d50b18487fbd468925', 1, 0),
(5, 'Alexandre ', 'Tarabay', 'atarabay', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(6, 'Allen ', 'Zeidan', 'azeidan', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(7, 'Anthony ', 'Saadeh', 'asaadeh', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(8, 'Bassel ', 'Khodr', 'bkhodr', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(9, 'Bernard ', 'Khazzaka', 'bkhazzaka', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(10, 'Charbel ', 'Azzi', 'cazzi', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(11, 'Claudine ', 'Daaboul', 'cdaaboul', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(12, 'Denise ', 'Ibrahim', 'dibrahim', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(13, 'Diana ', 'Jabbour', 'djabbour', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(14, 'Emile ', 'Bassil', 'ebassil', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(15, 'Ghina ', 'Karame', 'gkarame', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(16, 'Hussein ', 'Naim', 'hnaim', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(17, 'Joseph ', 'Rahme', 'jrahme', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(18, 'Mario ', 'Ghosn', 'mghosn', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(19, 'Micheline ', 'Daaboul', 'mdaaboul', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(20, 'Mohammed ', 'Obaidah', 'mobaidah', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(21, 'Mohammed', ' Itani', 'mitani', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(22, 'Nadim ', 'Klat', 'nklat', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(23, 'Nadine ', 'Abboud', 'nabboud', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(24, 'Naji ', 'Abdel Khalek', 'nabdelkhalek', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(25, 'Paul ', 'Donikian', 'pdonikian', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(26, 'Rami', 'Allam', 'rallam', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(27, 'Ramy ', 'Khattar', 'rkhattar', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(28, 'Ramzi ', 'Ballout', 'rballout', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(29, 'Samer ', 'Saad', 'ssaad', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(30, 'Serge ', 'Abou Slaiby', 'sabouslaiby', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(31, 'Simon ', 'El Kosseifi', 'skosseifi', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(32, 'Tarek ', 'El Husseini', 'Rek', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(33, 'Teddy ', 'Richa', 'tricha', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(34, 'Antoine', 'Oudaimy', 'aoudaimy', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(35, 'Wael', 'El Mabsout', 'wmabsout', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0),
(36, 'snsit', 'snsit', 'snsit', '415f3c6b6e10cff2446403ad976ab20504c5bddf', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `widgets`
--

CREATE TABLE IF NOT EXISTS `widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_dashboard` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `model` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_dashboard` (`id_dashboard`) USING BTREE,
  KEY `model` (`model`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `widgets`
--

INSERT INTO `widgets` (`id`, `id_dashboard`, `name`, `type`, `model`) VALUES
(1, 4, 'QUICK LINKS', 0, 'WidgetQuickLinks'),
(4, 3, 'APPROVED EAS', 0, 'WidgetEas'),
(5, 3, 'TOP CUSTOMERS', 0, 'WidgetCustomers'),
(6, 2, 'PROJECT SUMMARY', 0, 'WidgetProjects'),
(7, 2, 'BILLABILITY', 0, 'WidgetBillability'),
(8, 1, 'NUMBER OF SRs CLOSED', 0, 'WidgetSrClose'),
(9, 1, 'SRs SUBMITTED PER MONTH', 0, 'WidgetSrSubmitted'),
(10, 1, 'SRs SUBMITTED PER CUSTOMER', 0, 'WidgetSrCustomer'),
(11, 1, 'TOP 10 CUSTOMERS', 0, 'WidgetSrTopCustomer'),
(12, 1, 'NUMBER OF CUSTOMERS VS SRs SUBMITTED', 0, 'WidgetSubmittedCustomer'),
(13, 1, 'SRs SUBMITTED BY REASON', 0, 'WidgetSubmittedReason'),
(14, 1, 'SRs CLOSED BY RESOURCE', 0, 'WidgetSrCloseResource'),
(15, 1, 'ISSUE RESOLUTION TIME', 0, 'WidgetTime'),
(16, 1, 'RESOURCE SUPPORT ALLOCATION', 0, 'WidgetSupport'),
(17, 2, 'REVENUES PIPELINE', 0, 'WidgetRevenues'),
(18, 2, 'REVENUES BY COUNTRY', 0, 'WidgetCountryRevenues'),
(19, 2, 'REVENUES BY EA TYPE', 0, 'WidgetEaTypeRevenues'),
(20, 2, 'REVENUES BY SOLD BY', 0, 'WidgetSoldByRevenues'),
(21, 2, 'PROJECT FINANCIAL OUTLOOK', 0, 'WidgetProjectFinancials');

-- --------------------------------------------------------

--
-- Structure for view `receivables`
--
DROP TABLE IF EXISTS `receivables`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `receivables` AS select `invoices`.`id` AS `id`,`invoices`.`id_customer` AS `id_customer`,group_concat(distinct `invoices`.`invoice_number` order by `invoices`.`invoice_number` ASC separator ' ') AS `invoice_number`,`invoices`.`final_invoice_number` AS `final_invoice_number`,group_concat(distinct `invoices`.`invoice_title` separator ', ') AS `invoice_title`,`invoices`.`project_name` AS `project_name`,`invoices`.`id_ea` AS `id_ea`,group_concat(distinct `invoices`.`payment` order by `invoices`.`payment` ASC separator ', ') AS `payment`,sum(`invoices`.`payment_procente`) AS `payment_procente`,`invoices`.`status` AS `status`,`invoices`.`currency` AS `currency`,`invoices`.`partner` AS `partner`,`invoices`.`sns_share` AS `sns_share`,`invoices`.`invoice_date_month` AS `invoice_date_month`,`invoices`.`invoice_date_year` AS `invoice_date_year`,`invoices`.`sold_by` AS `sold_by`,`invoices`.`old` AS `old`,`invoices`.`printed_date` AS `printed_date`,`invoices`.`partner_status` AS `partner_status`,`invoices`.`partner_inv` AS `partner_inv`,sum(`invoices`.`net_amount`) AS `net_amount`,sum(`invoices`.`gross_amount`) AS `gross_amount`,sum(`invoices`.`partner_amount`) AS `partner_amount`,sum(`invoices`.`amount`) AS `amount`,group_concat(distinct `invoices`.`id_expenses` order by `invoices`.`id_expenses` ASC separator ',') AS `id_expenses`,`invoices`.`paid_date` AS `paid_date`,group_concat(distinct `invoices`.`notes` separator ', ') AS `notes`,group_concat(distinct `invoices`.`remarks` separator ', ') AS `remarks`,`invoices`.`id_assigned` AS `id_assigned` from `invoices` where ((`invoices`.`status` <> 'New') and (`invoices`.`status` <> 'To Print')) group by `invoices`.`final_invoice_number`;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `codelists`
--
ALTER TABLE `codelists`
  ADD CONSTRAINT `codelists_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `codelists_categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `codelkups`
--
ALTER TABLE `codelkups`
  ADD CONSTRAINT `codelkups_ibfk_1` FOREIGN KEY (`id_codelist`) REFERENCES `codelists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `connections`
--
ALTER TABLE `connections`
  ADD CONSTRAINT `connections_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `currency_rate`
--
ALTER TABLE `currency_rate`
  ADD CONSTRAINT `currency_rate_ibfk_1` FOREIGN KEY (`currency`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`country`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `customers_ibfk_2` FOREIGN KEY (`industry`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `customers_ibfk_3` FOREIGN KEY (`product_1`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `customers_ibfk_4` FOREIGN KEY (`product_2`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `customers_ibfk_5` FOREIGN KEY (`product_3`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `customers_contacts`
--
ALTER TABLE `customers_contacts`
  ADD CONSTRAINT `customers_contacts_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `default_tasks`
--
ALTER TABLE `default_tasks`
  ADD CONSTRAINT `default_tasks_ibfk_1` FOREIGN KEY (`id_maintenance`) REFERENCES `maintenance` (`id_maintenance`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `default_tasks_ibfk_2` FOREIGN KEY (`id_parent`) REFERENCES `default_tasks` (`id`);

--
-- Constraints for table `default_tasks_group`
--
ALTER TABLE `default_tasks_group`
  ADD CONSTRAINT `default_tasks_group_ibfk_1` FOREIGN KEY (`id_group`) REFERENCES `groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `default_tasks_group_ibfk_2` FOREIGN KEY (`id_default_task`) REFERENCES `default_tasks` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `documents_categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `documents_categories`
--
ALTER TABLE `documents_categories`
  ADD CONSTRAINT `documents_categories_ibfk_1` FOREIGN KEY (`category`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `ea_payment_terms`
--
ALTER TABLE `ea_payment_terms`
  ADD CONSTRAINT `ea_payment_terms_ibfk_1` FOREIGN KEY (`milestone`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ea_payment_terms_ibfk_2` FOREIGN KEY (`id_ea`) REFERENCES `eas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `eas`
--
ALTER TABLE `eas`
  ADD CONSTRAINT `eas_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eas_ibfk_2` FOREIGN KEY (`author`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `eas_ibfk_3` FOREIGN KEY (`category`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `eas_ibfk_4` FOREIGN KEY (`currency`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `eas_ibfk_5` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eas_ibfk_6` FOREIGN KEY (`id_parent_project`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `eas_items`
--
ALTER TABLE `eas_items`
  ADD CONSTRAINT `eas_items_ibfk_1` FOREIGN KEY (`id_ea`) REFERENCES `eas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eas_items_ibfk_2` FOREIGN KEY (`settings_codelkup`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `eas_notes`
--
ALTER TABLE `eas_notes`
  ADD CONSTRAINT `eas_notes_ibfk_1` FOREIGN KEY (`id_ea`) REFERENCES `eas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eas_notes_ibfk_2` FOREIGN KEY (`id_note`) REFERENCES `codelkups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`currency`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `expenses_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_4` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `expenses_details`
--
ALTER TABLE `expenses_details`
  ADD CONSTRAINT `expenses_details_ibfk_1` FOREIGN KEY (`type`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `expenses_details_ibfk_2` FOREIGN KEY (`currency`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `expenses_details_ibfk_3` FOREIGN KEY (`currency_rate_id`) REFERENCES `currency_rate` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `expenses_details_ibfk_4` FOREIGN KEY (`expenses_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `expenses_uploads`
--
ALTER TABLE `expenses_uploads`
  ADD CONSTRAINT `expenses_uploads_ibfk_1` FOREIGN KEY (`expenses_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`currency`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`partner`) REFERENCES `codelkups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_4` FOREIGN KEY (`id_ea`) REFERENCES `eas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_5` FOREIGN KEY (`id_resource`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoices_expenses`
--
ALTER TABLE `invoices_expenses`
  ADD CONSTRAINT `invoices_expenses_ibfk_1` FOREIGN KEY (`id_invoice`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoices_expenses_ibfk_2` FOREIGN KEY (`id_expenses_details`) REFERENCES `expenses_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoices_expenses_ibfk_3` FOREIGN KEY (`currency`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `invoices_expenses_ibfk_4` FOREIGN KEY (`currency_rate_id`) REFERENCES `currency_rate` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD CONSTRAINT `maintenance_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `maintenance_ibfk_2` FOREIGN KEY (`support_service`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `maintenance_ibfk_3` FOREIGN KEY (`currency_usd`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `maintenance_ibfk_4` FOREIGN KEY (`currency_rate_id`) REFERENCES `currency_rate` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `maintenance_ibfk_5` FOREIGN KEY (`customer`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `maintenance_ibfk_6` FOREIGN KEY (`currency`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `maintenance_ibfk_7` FOREIGN KEY (`product`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `maintenance_ibfk_8` FOREIGN KEY (`frequency`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `maintenance_invoices`
--
ALTER TABLE `maintenance_invoices`
  ADD CONSTRAINT `maintenance_invoices_ibfk_1` FOREIGN KEY (`id_invoice`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `maintenance_invoices_ibfk_2` FOREIGN KEY (`id_contract`) REFERENCES `maintenance` (`id_maintenance`);

--
-- Constraints for table `maintenance_items`
--
ALTER TABLE `maintenance_items`
  ADD CONSTRAINT `maintenance_items_ibfk_1` FOREIGN KEY (`id_contract`) REFERENCES `maintenance` (`id_maintenance`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `maintenance_items_ibfk_2` FOREIGN KEY (`currency`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `maintenance_items_ibfk_3` FOREIGN KEY (`currency_usd`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `maintenance_items_ibfk_4` FOREIGN KEY (`currency_rate_id`) REFERENCES `currency_rate` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `milestones`
--
ALTER TABLE `milestones`
  ADD CONSTRAINT `milestones_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `codelkups` (`id`);

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`project_manager`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `projects_ibfk_4` FOREIGN KEY (`id_type`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `projects_ibfk_5` FOREIGN KEY (`id_parent`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `projects_ibfk_6` FOREIGN KEY (`id_parent`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `projects_ibfk_7` FOREIGN KEY (`business_manager`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `projects_emails`
--
ALTER TABLE `projects_emails`
  ADD CONSTRAINT `projects_emails_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `projects_emails_ibfk_2` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `projects_milestones`
--
ALTER TABLE `projects_milestones`
  ADD CONSTRAINT `projects_milestones_ibfk_1` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `projects_milestones_ibfk_2` FOREIGN KEY (`id_milestone`) REFERENCES `milestones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `projects_phases`
--
ALTER TABLE `projects_phases`
  ADD CONSTRAINT `projects_phases_ibfk_1` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `projects_phases_ibfk_2` FOREIGN KEY (`id_phase`) REFERENCES `phases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `projects_tasks`
--
ALTER TABLE `projects_tasks`
  ADD CONSTRAINT `projects_tasks_ibfk_1` FOREIGN KEY (`id_project_phase`) REFERENCES `projects_phases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `support_desk`
--
ALTER TABLE `support_desk`
  ADD CONSTRAINT `support_desk_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `support_desk_ibfk_2` FOREIGN KEY (`product`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `support_desk_comm_files`
--
ALTER TABLE `support_desk_comm_files`
  ADD CONSTRAINT `support_desk_comm_files_ibfk_1` FOREIGN KEY (`id_support_desk`) REFERENCES `support_desk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `support_desk_comm_files_ibfk_2` FOREIGN KEY (`id_comm`) REFERENCES `support_desk_comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `support_desk_comments`
--
ALTER TABLE `support_desk_comments`
  ADD CONSTRAINT `support_desk_comments_ibfk_1` FOREIGN KEY (`id_support_desk`) REFERENCES `support_desk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`id_phase`) REFERENCES `phases` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `timesheets`
--
ALTER TABLE `timesheets`
  ADD CONSTRAINT `timesheets_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `trainings`
--
ALTER TABLE `trainings`
  ADD CONSTRAINT `trainings_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `trainings_ibfk_2` FOREIGN KEY (`id_eas`) REFERENCES `eas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `travel`
--
ALTER TABLE `travel`
  ADD CONSTRAINT `travel_ibfk_1` FOREIGN KEY (`expense_type`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `travel_ibfk_2` FOREIGN KEY (`currency`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `travel_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `travel_ibfk_4` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `travel_ibfk_5` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD CONSTRAINT `user_groups_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_groups_ibfk_2` FOREIGN KEY (`id_group`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_groups_old`
--
ALTER TABLE `user_groups_old`
  ADD CONSTRAINT `user_groups_old_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_groups_old_ibfk_2` FOREIGN KEY (`id_group`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_hr_details`
--
ALTER TABLE `user_hr_details`
  ADD CONSTRAINT `user_hr_details_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_hr_details_old`
--
ALTER TABLE `user_hr_details_old`
  ADD CONSTRAINT `user_hr_details_old_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_personal_details`
--
ALTER TABLE `user_personal_details`
  ADD CONSTRAINT `user_personal_details_ibfk_1` FOREIGN KEY (`line_manager`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_personal_details_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_personal_details_ibfk_3` FOREIGN KEY (`branch`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_personal_details_ibfk_4` FOREIGN KEY (`unit`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_personal_details_old`
--
ALTER TABLE `user_personal_details_old`
  ADD CONSTRAINT `user_personal_details_old_ibfk_1` FOREIGN KEY (`line_manager`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_personal_details_old_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_personal_details_old_ibfk_3` FOREIGN KEY (`branch`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_personal_details_old_ibfk_4` FOREIGN KEY (`unit`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_task`
--
ALTER TABLE `user_task`
  ADD CONSTRAINT `user_task_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_task_old`
--
ALTER TABLE `user_task_old`
  ADD CONSTRAINT `user_task_old_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_time`
--
ALTER TABLE `user_time`
  ADD CONSTRAINT `user_time_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_time_ibfk_2` FOREIGN KEY (`id_timesheet`) REFERENCES `timesheets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_time_old`
--
ALTER TABLE `user_time_old`
  ADD CONSTRAINT `user_time_old_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_time_old_ibfk_2` FOREIGN KEY (`id_timesheet`) REFERENCES `timesheets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_visas`
--
ALTER TABLE `user_visas`
  ADD CONSTRAINT `user_visas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_visas_old`
--
ALTER TABLE `user_visas_old`
  ADD CONSTRAINT `user_visas_old_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `widgets`
--
ALTER TABLE `widgets`
  ADD CONSTRAINT `widgets_ibfk_1` FOREIGN KEY (`id_dashboard`) REFERENCES `dashboards` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
