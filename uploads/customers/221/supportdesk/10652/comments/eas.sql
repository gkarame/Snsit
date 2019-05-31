/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : snsit

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2015-07-14 12:22:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `eas`
-- ----------------------------
DROP TABLE IF EXISTS `eas`;
CREATE TABLE `eas` (
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
  `TM` int(3) DEFAULT NULL,
  `billto_contact_person` varchar(255) DEFAULT NULL,
  `billto_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_eas_customers1_idx` (`id_customer`) USING BTREE,
  KEY `author` (`author`) USING BTREE,
  KEY `category` (`category`) USING BTREE,
  KEY `currency` (`currency`) USING BTREE,
  KEY `project_name` (`id_project`) USING BTREE,
  KEY `id_project` (`id_project`) USING BTREE,
  KEY `id_parent_project` (`id_parent_project`) USING BTREE,
  CONSTRAINT `eas_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `eas_ibfk_2` FOREIGN KEY (`author`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `eas_ibfk_3` FOREIGN KEY (`category`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `eas_ibfk_4` FOREIGN KEY (`currency`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `eas_ibfk_5` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `eas_ibfk_6` FOREIGN KEY (`id_parent_project`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=254 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of eas
-- ----------------------------
INSERT INTO `eas` VALUES ('47', '110', '00047', 'Loftware License', null, null, '197', '2', '30', '2015-01-20 10:56:28', '2015-03-11 14:06:41', '20150311125957151.pdf', '25', '', '0', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('48', '110', '00048', 'Changes needed for Pick By Customer as per sheet WMS Development and Process Changes 2015 - V3.0 - Costs', '181', '172', '197', '2', '30', '2015-01-20 10:58:49', '2015-03-11 14:06:07', '20150311125957151.pdf', '28', '', '22.6761812', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('49', '117', '00049', 'Distribution Audit Consultancy Study', '182', null, '9', '2', '22', '2015-01-20 12:57:19', '2015-01-22 14:19:43', 'FW Distribution Audit Consultancy.msg', '26', '', '0', 'Actuals', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('51', '179', '00051', 'CERN SUPPLY CHAIN CONSULTANCY', '186', null, '8', '2', '35', '2015-01-21 13:11:28', '2015-01-21 13:34:06', 'ALOER CA 5890895.pdf', '26', '', '0', '4000', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('52', '173', '00052', 'SDV WM 4000 Customization List 001', '187', '169', '195', '2', '16', '2015-01-22 05:48:37', '2015-02-03 07:17:50', 'Infor (S.E.A.) PTE. LTD. - EA 00052 - Infor-WO-S01204PS_Signed_PO 151055.pdf', '28', '', '20', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('53', '173', '00053', 'SDV WM 4000 Customization List 002', '188', '169', '195', '2', '16', '2015-01-22 07:09:54', '2015-02-02 07:49:08', 'Infor (S.E.A.) PTE. LTD. - EA 00053 - Infor-WO-S01246PS_Signed_PO 151427.pdf', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('54', '162', '00054', 'UTi DEU SW T&M Support', '189', null, '9', '2', '16', '2015-01-22 15:28:14', '2015-01-26 10:06:58', 'UTi Deutschland GmbH - 2015.01.22 - EA 00054 - Signed.pdf', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '1', null, null);
INSERT INTO `eas` VALUES ('55', '180', '00055', 'DP assistance and configuration', '190', null, '9', '2', '14', '2015-01-26 07:51:29', '2015-01-26 08:03:54', 'Softworx.pdf', '26', '', '0', 'Actuals', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('56', '101', '00056', 'Chalhoub INFOR WM9 KSA Roll Out', '191', null, '9', '2', '30', '2015-01-26 08:34:27', '2015-02-02 09:23:47', 'PO_2103_1691000005_SNS KSA 2015.pdf', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('57', '98', '00057', 'Loftware Licenses', null, null, '9', '1', '35', '2015-01-27 08:40:47', null, '', '25', '', '0', '', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('58', '182', '00058', 'Warehouse Design Consultancy (Jafza)', '199', null, '9', '2', '20', '2015-01-28 11:20:04', '2015-01-28 11:29:58', 'Approved_PO_LiquidTelecomInternational.pdf', '26', 'LTINT-001', '0', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('61', '53', '00061', 'Al Yasra - WMS Changes as per QNIE Recommendation', '202', null, '9', '1', '20', '2015-01-29 09:35:11', null, '', '28', '', '0', 'Actuals', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('62', '101', '00062', '5 Days On-Site Support', '203', '177', '9', '2', '30', '2015-01-29 16:54:25', '2015-01-29 19:12:26', 'Re Jan Invoicing.msg', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('63', '33', '00063', 'Infor EM Upgrade to v4.3', '204', null, '9', '1', '16', '2015-02-02 09:38:18', null, '', '27', '', '0', 'N/A', '0000-00-00', '0000-00-00', null, 'Varghese Thomas', 'ABA Tower, Airport Road, P.O.Box 75, Doha - Qatar');
INSERT INTO `eas` VALUES ('64', '52', '00064', '3PL Billing Consultancy', '206', null, '9', '1', '16', '2015-02-03 07:05:34', null, '', '26', '', '10', 'Actuals', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('65', '98', '00065', 'Expeditors Integration Project', '210', '153', '9', '2', '35', '2015-02-03 12:48:00', '2015-02-04 15:21:57', 'RE  Expeditors Integration.msg', '28', '', '12.5', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('68', '181', '00068', 'Grupo Gal On Site Support', '213', null, '9', '2', '35', '2015-02-04 12:55:37', '2015-02-04 12:58:10', 'RE  Charges for the visit to Mexico.msg', '27', '', '0', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('69', '37', '00069', 'Closed DropID', '215', '209', '9', '2', '31', '2015-02-05 13:14:12', '2015-03-02 13:04:12', 'img-213012034.pdf', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('70', '52', '00070', 'Al Talayi - Fares Al Toruk SCE Go-Live', '218', '217', '9', '2', '16', '2015-02-06 06:39:01', '2015-03-25 07:50:33', 'AL TALAYI - EA 00070 - Fares Al Toruk Go-Live_with PO.pdf', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('71', '183', '00071', 'Warehouse Design 2015', '219', null, '9', '2', '14', '2015-02-06 09:52:10', '2015-02-06 09:57:49', 'SNSscanned.zip', '26', '', '0', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('72', '184', '00072', 'Getting Started with Warehouse Management Systems Course at Crowne Plaza Jeddah on 24 & 25 February 2015', null, null, '167', '2', '23', '2015-02-10 10:42:58', '2015-02-10 11:49:47', 'img-2101334-0001.pdf', '24', '', '0', 'Actuals', '2015-02-24', '2015-02-25', null, null, null);
INSERT INTO `eas` VALUES ('73', '185', '00073', 'Getting Started with Warehouse Management Systems Course at Crowne Plaza Jeddah on 24 & 25 February 2015', null, null, '167', '2', '23', '2015-02-10 12:09:22', '2015-02-20 12:46:34', 'Ejada_EA-SNS.pdf', '24', '', '10', 'N/A', '2015-02-24', '2015-02-25', null, null, null);
INSERT INTO `eas` VALUES ('74', '186', '00074', 'In-house Private Sessions of 5 Courses over 6 days', null, null, '9', '2', '19', '2015-02-10 14:42:20', '2015-03-31 13:08:23', 'AUBMC_EA.pdf', '24', '', '0', 'N/A', '2015-02-16', '2015-03-31', null, null, null);
INSERT INTO `eas` VALUES ('76', '182', '00076', 'Getting Started with Warehouse Management Systems Course at Intercontinental Dubai Festival City on 24 & 25 February 2015', null, null, '168', '1', '23', '2015-02-11 11:04:42', null, '', '24', '', '0', '', '2015-02-24', '2015-02-25', null, null, null);
INSERT INTO `eas` VALUES ('77', '190', '00077', 'Getting Started with Warehouse Management Systems Course  on February 24th and 25th, 2015 at Crowne Plaza Jeddah', null, null, '167', '2', '23', '2015-02-12 10:07:28', '2015-02-12 14:40:56', 'scan0001.pdf', '24', '', '0', 'N/A', '2015-02-24', '2015-02-25', null, null, null);
INSERT INTO `eas` VALUES ('78', '191', '00078', 'Getting Started with Warehouse Management Systems Course on February 24th and 25th, 2015 at Intercontinental Festival City, Dubai', null, null, '168', '2', '23', '2015-02-12 10:46:04', '2015-02-12 14:40:11', 'Total_Lubricant.pdf', '24', '', '10', 'N/A', '2015-02-24', '2015-02-25', null, null, null);
INSERT INTO `eas` VALUES ('79', '192', '00079', 'Getting Started with Warehouse Management Systems Course  on February 24th and 25th, 2015 at Crowne Plaza Jeddah,', null, null, '167', '2', '23', '2015-02-16 11:01:07', '2015-02-20 12:49:18', 'SNS Invoice WMS Training 201502.pdf', '24', '', '0', 'N/A', '2015-02-24', '2015-02-25', null, null, null);
INSERT INTO `eas` VALUES ('80', '193', '00080', 'Getting Started with Warehouse Management Systems Course  on February 24th and 25th, 2015 at Crowne Plaza Jeddah, KSA', null, null, '167', '2', '23', '2015-02-17 10:08:42', '2015-02-17 10:39:49', 'SNS.pdf', '24', '', '10', 'Actuals', '2015-02-24', '2015-02-25', null, null, null);
INSERT INTO `eas` VALUES ('81', '194', '00081', 'Multibrands Jeddah Warehouse Design', '220', null, '9', '2', '18', '2015-02-17 10:53:55', '2015-02-17 10:57:10', 'MultiBrands_Warehouse_Design_Proposal_20141222_ToSign.pdf', '26', '', '0', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('82', '101', '00082', 'Stock Report Change Request', '221', '177', '9', '1', '30', '2015-02-18 07:47:33', null, '', '28', '', '0', '', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('83', '195', '00083', 'Almajdouie Riyadh Warehouse Design', '223', null, '9', '2', '35', '2015-02-19 15:23:46', '2015-02-19 15:29:50', 'ALMAJDOUIE_MPS_20150115_140718.pdf', '26', '', '14.285', 'N/A', '0000-00-00', '0000-00-00', null, null, null);
INSERT INTO `eas` VALUES ('84', '173', '00084', 'SDV WM 4000 Dev 003', '228', '169', '195', '2', '16', '2015-02-26 06:05:24', '2015-03-23 11:41:03', 'Infor (S.E.A.) PTE. LTD. - EA 00084 - Infor-WO-S01286PS_Signed_PO.pdf', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('85', '196', '00085', 'Warehousing Best Practices Training at Millennium Hotel Doha on 2, 3 & 4 March 2015', null, null, '9', '0', '23', '2015-02-26 13:25:37', null, '', '24', '', '0', 'N/A', '2015-03-02', '2015-03-04', '0', null, null);
INSERT INTO `eas` VALUES ('87', '190', '00087', 'SETRA - Infor10 SCE Implementation', '230', null, '9', '2', '9', '2015-02-27 14:20:41', '2015-02-27 14:48:58', 'SETRA - Infor WM Executive Proposal_20141208.pdf', '27', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('89', '190', '00089', 'Infor10 Licenses', null, null, '9', '2', '9', '2015-02-27 14:33:46', '2015-02-27 14:52:56', 'SETRA - Infor WM Executive Proposal_20141208.pdf', '25', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('90', '91', '00090', 'Additional Licenses for 5 New Users', null, null, '9', '2', '19', '2015-03-09 11:38:46', '2015-03-09 11:43:14', 'FW WMS Licenses.msg', '25', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('91', '163', '00091', 'UTi USA SW T&M Support', '235', null, '9', '2', '16', '2015-03-09 16:56:08', '2015-04-16 19:11:37', 'UTi Integrated Logistics LLC - 2015.03.09 - EA 00091_Signed.pdf', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '1', 'Deanne Groves', '700 Gervais St. Suite 100 Columbia, SC  29201, USA');
INSERT INTO `eas` VALUES ('92', '181', '00092', 'Grupo Gal Remote Support', '236', null, '9', '2', '35', '2015-03-10 13:22:26', '2015-03-10 13:25:15', 'SERVICES WORK ORDER.pdf', '27', '', '0', 'N/A', '0000-00-00', '0000-00-00', '1', null, null);
INSERT INTO `eas` VALUES ('93', '169', '00093', 'Additional Changes from UAT and support for iUAT', '237', '152', '9', '2', '35', '2015-03-10 18:15:40', '2015-03-18 09:58:49', '4540350739_SNS.pdf', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('94', '147', '00094', 'Oracle Go-Live Support', '238', null, '9', '1', '35', '2015-03-11 07:53:20', null, '', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '1', null, null);
INSERT INTO `eas` VALUES ('95', '100', '00095', 'Maxx - validation on serial capture', '239', '234', '9', '1', '20', '2015-03-12 11:13:29', null, '', '28', '', '20', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('96', '37', '00096', 'Changes to UCC RF Receiving', '240', '209', '9', '1', '31', '2015-03-12 13:32:25', null, '', '28', '', '0', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('103', '33', '99001', 'ABA - Monoprix Implementation', '154', null, '9', '2', '18', '2015-03-13 11:04:43', '2015-03-13 11:04:43', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('104', '170', '99002', 'Abela Freres - Oracle WM Implementation', '150', null, '9', '2', '18', '2015-03-13 11:04:43', '2015-03-13 11:04:43', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('105', '147', '99003', 'Abu Issa Holding - Infor WM 10 Implementation', '151', null, '9', '2', '18', '2015-03-13 11:04:43', '2015-03-13 11:04:43', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('106', '36', '99004', 'ADT - WH Concept Design', '137', null, '9', '2', '18', '2015-03-13 11:04:43', '2015-03-13 11:04:43', '', '26', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('107', '37', '99005', 'Agility Infor WM Implementation', '209', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('108', '41', '99006', 'AKI - InforSCE10 Implementation', '148', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('109', '202', '99007', 'Al Aujan WH Design - Dammam & DIP 2014', '141', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '26', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('110', '174', '99008', 'Al Jomaih WH Design 2014', '167', null, '167', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '26', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('111', '158', '99009', 'Al Rabie WH Design 2014', '136', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '26', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('112', '59', '99010', 'Al Rehab - Warehouse Design', '140', null, '167', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '26', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('113', '52', '99011', 'Al Talayi - SCE Implementation', '217', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('114', '53', '99012', 'Al Yasra-Infor WM 4000', '205', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('115', '54', '99013', 'Al-Zahrani Warehouse Consultancy', '133', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '26', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('116', '166', '99014', 'Andreani - WMS Implementation', '192', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '1', null, null);
INSERT INTO `eas` VALUES ('117', '64', '99015', 'Areej - Integration with AUTOMHA', '157', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('118', '55', '99016', 'Aujan - Infor WM 10', '164', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('119', '71', '99017', 'BCC - SCE10.3 Implementation', '156', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('120', '149', '99018', 'BTA - Infor WM 10 Implementation', '158', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('121', '101', '99019', 'Chalhoub INFOR WM9 Implementation', '178', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('122', '101', '99020', 'Chalhoub INFOR WM9 JAFZA1 Implementation', '176', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('123', '101', '99021', 'Chalhoub INFOR WM9 JAFZA2 Implementation', '177', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('124', '76', '99022', 'Deal Logistics', '222', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('125', '83', '99023', 'eXtra Warehouse Audit and Design', '139', null, '167', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '26', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('126', '160', '99024', 'Fattal SCE10.2 - Nahr Ibrahim Implementation', '155', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('127', '148', '99025', 'Greenhouse WM10 Implementation', '175', null, '9', '2', '18', '2015-03-13 11:04:44', '2015-03-13 11:04:44', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('128', '90', '99026', 'GWC - INFOR WM10.1 Implementation', '197', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('129', '91', '99027', 'Holdal WM4000 Implementation', '185', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('130', '181', '99028', 'Honda Infor WM9 Upgrade and Implementation', '198', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('131', '92', '99029', 'IATCO - T&M Work', '142', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('132', '135', '99030', 'Isuzu IMIT THA - WM 10 Upgrade', '143', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('133', '166', '99031', 'JBS Infor SCE Customizations', '193', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '1', null, null);
INSERT INTO `eas` VALUES ('134', '98', '99032', 'M+M WMS Implementation', '153', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('135', '99', '99033', 'Madi - Warehouse Design', '138', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '26', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('136', '175', '99034', 'NAT - Supply Chain Audit', '207', null, '167', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '26', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('137', '110', '99035', 'Parmalat Infor WM9 Implementation', '172', null, '197', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('138', '119', '99036', 'Redington - InforWM10 Implementation', '149', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('139', '120', '99037', 'RedTag WM10 Implementation', '173', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('140', '173', '99038', 'SDV WM 4000 Customizations', '169', null, '195', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('141', '169', '99039', 'SIBCO WMS Implementation', '152', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('144', '151', '99042', 'Swift(Egypt) - SCE10.3', '208', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('145', '178', '99043', 'Transmed Choueifet WM10 Implementation', '184', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('146', '178', '99044', 'Transmed Mkalles WM10 Implementation', '183', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('147', '139', '99045', 'Unipharm - Infor 103 Implementation', '195', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('148', '163', '99046', 'UTi Americas - T&M Work', '144', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('149', '161', '99047', 'UTi APAC - Infor SCE Training', '146', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('150', '162', '99048', 'UTi EMENA - T&M Work', '145', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('151', '172', '99049', 'Vibrant LTH SIN - SCE 10.3 Implementation', '170', null, '168', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('152', '145', '99050', 'Wilhelmsen Infor 10.3 SCE Upgrade', '194', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'Actuals', null, null, null, null, null);
INSERT INTO `eas` VALUES ('153', '161', '00153', 'UTi APAC SCE Customizations', '241', null, '9', '1', '16', '2015-03-13 12:37:23', null, '', '27', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('155', '160', '00155', 'Additional 10 Infor SCE Licenses', null, null, '9', '2', '35', '2015-03-18 12:06:23', '2015-04-14 20:31:21', 'RE  Infor Licenses  10 ( Y-2015 ).msg', '25', '66014', '0', 'N/A', '0000-00-00', '0000-00-00', '0', 'Talal Hakim', '');
INSERT INTO `eas` VALUES ('156', '198', '00156', 'Fundamentals of Supply Chain Training in Arabic at Dammam KSA on the 1st & 2nd of June 2015', null, null, '9', '2', '23', '2015-03-23 12:10:13', '2015-03-25 10:38:05', 'img-324101145-0001.pdf', '24', '', '0', 'N/A', '2015-06-01', '2015-06-02', '0', null, null);
INSERT INTO `eas` VALUES ('157', '158', '00157', 'Fundamentals of Supply Chain Training in Arabic at Dammam KSA on the 1st & 2nd of June 2015', null, null, '9', '0', '23', '2015-03-23 12:20:41', null, '', '24', '', '0', 'N/A', '2015-06-01', '2015-06-02', '0', '', '');
INSERT INTO `eas` VALUES ('158', '199', '00158', 'Arla Infor SCE Implementation', '243', null, '9', '2', '35', '2015-03-26 13:55:19', '2015-03-26 14:12:41', 'Won Opportunity.msg', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('159', '200', '00159', 'Introdcution to warehouse automation 15th April  - Jeddah', null, null, '9', '0', '23', '2015-03-26 14:07:24', '2015-04-01 09:21:33', 'Roots Group_EA.pdf', '24', '', '5', 'N/A', '2015-04-15', '2015-04-15', '0', null, null);
INSERT INTO `eas` VALUES ('160', '201', '00160', 'Introduction to Warehouse Automation Training on April 15 at  Crowne Plaza Jeddah', null, null, '9', '0', '23', '2015-04-02 11:09:32', '2015-04-02 12:44:51', 'img-402114238-0001.pdf', '24', '', '0', 'N/A', '2015-04-15', '2015-04-15', '0', null, null);
INSERT INTO `eas` VALUES ('161', '130', '00161', 'Introduction to Warehouse Automation Training on April 15 at Crowne Plaza Jeddah', null, null, '9', '0', '23', '2015-04-02 11:22:05', null, '', '24', '', '0', 'N/A', '2015-04-15', '2015-04-15', '0', ' t', 't');
INSERT INTO `eas` VALUES ('162', '203', '00162', 'Grupo Gal Customization', '244', null, '9', '2', '35', '2015-04-07 09:56:45', '2015-04-07 09:59:26', 'DOCTO EDUARDO.PDF', '27', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', 'Mitchell Lafrance', '');
INSERT INTO `eas` VALUES ('163', '92', '00163', 'IATCO Baghdad Warehouse SOP', '245', null, '9', '0', '35', '2015-04-07 10:05:50', '2015-04-07 10:07:52', 'FW  Fw  Conferance Call.msg', '26', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('164', '204', '00164', '3 Additional Licenses for WM4000 for Dammam', null, null, '9', '2', '35', '2015-04-09 17:33:46', '2015-04-09 17:41:27', 'Transmed Dmm SNS LPO.pdf', '25', '15000012/ON', '0', 'N/A', '0000-00-00', '0000-00-00', '0', 'Mohamad Hajj Ali', 'Riyadh');
INSERT INTO `eas` VALUES ('165', '204', '00165', 'Transmed Dammam WM4000 Roll-Out', '246', null, '9', '2', '35', '2015-04-09 18:06:27', '2015-04-09 18:29:27', 'Transmed Dmm SNS LPO.pdf', '27', '15000012/ON', '0', 'Actuals', '0000-00-00', '0000-00-00', '0', 'Mohamad Hajj Ali', 'Riyadh');
INSERT INTO `eas` VALUES ('166', '92', '00166', 'Iraq Warehouse Design Layout Consultancy', '247', null, '9', '2', '30', '2015-04-14 10:26:11', '2015-04-14 10:47:43', 'EA on SNSit.msg', '26', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('168', '206', '00168', 'WBP in Arabic - Medina', null, null, '9', '2', '19', '2015-04-15 16:00:01', '2015-04-17 09:43:52', 'RE Medina Training.msg', '24', '', '0', 'N/A', '2015-04-20', '2015-04-22', '0', '', '');
INSERT INTO `eas` VALUES ('169', '207', '00169', 'UTi Poland SCE Customization - JMP CR 001', '249', null, '9', '2', '16', '2015-04-16 18:33:03', '2015-04-16 18:40:57', 'UTi Poland SP. Z O.O. - 2015.04.16 - EA 00169_Signed.pdf', '27', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('170', '207', '00170', 'UTi Poland SCE Customization - JMP CR 002', '250', '249', '9', '2', '16', '2015-04-16 18:42:16', '2015-04-22 16:35:31', 'UTi Poland SP. Z O.O. - 2015.04.16 - EA 00170_Signed.pdf', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('177', '39', '99051', 'KOMATSU Outbound Changes', '251', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('178', '180', '00178', 'Clover IBP Implementation', '252', null, '9', '2', '14', '2015-04-21 14:47:40', '2015-04-21 14:59:31', 'SOW.zip', '26', 'Clover IBP_Statement of Work_Emile Bassil_v1.0_201', '0', 'Actuals', '0000-00-00', '0000-00-00', '1', 'Sankie Hancke', '');
INSERT INTO `eas` VALUES ('179', '208', '00179', 'AirRoad T&M Support', '253', null, '169', '2', '16', '2015-04-22 16:01:10', '2015-04-23 13:22:00', 'AirRoad Pty Limited - 2015.04.22 - EA 00179_Signed.pdf', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '1', '', '');
INSERT INTO `eas` VALUES ('180', '67', '00180', 'Ayezan CKA - Infor WM Upgrade', '254', null, '9', '2', '20', '2015-04-23 14:26:31', '2015-04-23 14:55:49', 'Ayezan Upgrade Proposal.pdf', '27', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('181', '211', '00181', 'WBP in English in Dubai from 11 till 13 of May 2015', null, null, '9', '2', '19', '2015-04-28 09:17:36', '2015-04-30 12:53:29', 'DOC300415-30042015083856.pdf', '24', '', '0', 'N/A', '2015-05-11', '2015-04-13', '0', '', '');
INSERT INTO `eas` VALUES ('182', '196', '00182', 'WBP in English in Dubai from 11 till 13 of May 2015', null, null, '9', '2', '19', '2015-04-28 09:36:39', '2015-04-29 17:37:32', 'scan.pdf', '24', '', '10', 'N/A', '2015-04-11', '2015-04-13', '0', '', '');
INSERT INTO `eas` VALUES ('183', '53', '00183', 'Al Yasra - WMS Upgrade and Integration Rengineering', '255', null, '9', '2', '20', '2015-04-28 13:30:56', '2015-04-28 19:15:25', 'AlYasra_P.O for WMS upgrade.pdf', '27', '19040', '3', 'Actuals', '0000-00-00', '0000-00-00', '0', 'Mohammed ElHady', 'P.O. Box 3228, Safat, 13033, Kuwait');
INSERT INTO `eas` VALUES ('184', '98', '00184', 'M+M Support  Resource Outsourcing', '256', null, '9', '2', '35', '2015-04-28 19:24:04', '2015-04-28 12:29:37', 'FW  Tony .msg', '27', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('185', '92', '00185', 'WBP in English in Dubai from 11 till 13 of May 2015', null, null, '9', '2', '19', '2015-04-28 15:15:18', '2015-04-29 13:48:05', 'EA_00185_Abudawood-signed.pdf', '24', '', '10', 'N/A', '2015-04-11', '2015-04-13', '0', '', '');
INSERT INTO `eas` VALUES ('186', '148', '00186', 'Extended Support Agreement', '257', null, '9', '2', '30', '2015-04-29 11:07:15', '2015-04-29 11:19:56', 'Green House Extended Support Agreement SignOff.tif', '27', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('187', '212', '00187', 'WBP in English in Dubai from 11 till 13 of May 2015', null, null, '9', '2', '19', '2015-04-29 13:54:25', '2015-05-04 12:52:05', 'Marji.pdf', '24', '', '10', 'N/A', '2015-05-11', '2015-05-13', '0', '', '');
INSERT INTO `eas` VALUES ('188', '119', '00188', 'Billing and System Enhancements', '258', '149', '9', '2', '6', '2015-04-29 16:48:42', '2015-05-18 10:48:33', 'EA#Redington Gulf - 20150503-00188 Dated 29.04.2015.pdf', '28', '', '10', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('190', '101', '00190', 'Gap Analysis Approved Changes', '259', '191', '9', '2', '30', '2015-04-30 17:19:23', '2015-05-12 11:06:04', 'RE KSA Fit Gap Changes.msg', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('191', '149', '00191', 'Integration Hand Over', '260', '158', '9', '2', '18', '2015-05-04 09:14:41', '2015-05-04 09:16:24', 'RE APDP Integration Call.msg', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '1', '', '');
INSERT INTO `eas` VALUES ('192', '213', '00192', 'Zain Sudan Warehouse Design', '261', null, '9', '2', '35', '2015-05-04 17:21:28', '2015-05-12 13:21:43', 'RE  Notes about Sudan trip.msg', '26', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('193', '181', '00193', 'Honda Infor SCE 10.3.2', '262', null, '9', '2', '35', '2015-05-04 21:30:48', '2015-05-07 16:19:56', 'SKM_C224e15033017480.pdf', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '0', 'Gloria Quevedo', 'Infor Mexico');
INSERT INTO `eas` VALUES ('194', '214', '00194', '3 Day WBP at Intercontinental Dubai Festival City from 11 to 13 of May 2015', null, null, '9', '2', '19', '2015-05-05 10:38:52', '2015-05-06 12:48:07', 'Training Registration - Mr. Waleed Boushnaq.pdf', '24', '', '0', 'N/A', '2015-05-11', '2015-05-13', '0', '', '');
INSERT INTO `eas` VALUES ('195', '101', '00195', 'JAFZA2 Approved Changes as per WM9-Action plan Real Fze excel sheet', '263', '177', '9', '2', '30', '2015-05-06 10:46:46', '2015-05-06 12:38:45', 'RE WM9 Enhancements and SNS Work Plan.msg', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('196', '91', '00196', 'NEPCO Move', '264', null, '9', '1', '30', '2015-05-06 15:24:03', null, '', '27', '', '10', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('197', '91', '00197', 'NEPCO Slotting Consultancy', '265', null, '9', '1', '30', '2015-05-06 15:43:18', null, '', '26', '', '10', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('199', '215', '00199', 'Infor 10 SCE licenses for 5 concurrent users', null, null, '9', '2', '10', '2015-05-07 10:16:21', '2015-05-07 10:58:20', 'Takhzeen_Contract.pdf', '25', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('200', '215', '00200', 'Takhzeen SCE10.3 Implementation ', '267', null, '9', '2', '10', '2015-05-07 15:23:41', '2015-05-07 16:47:33', 'Takhzeen_Contract.pdf', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('201', '160', '00201', 'Infor changes and On Site Support', '268', '155', '9', '2', '35', '2015-05-11 17:31:52', '2015-05-24 10:03:54', 'SNS-Excel Logistics.pdf', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('202', '216', '00202', 'Private Sessions - WBP and Warehousing Safety & Security', null, null, '9', '2', '19', '2015-05-14 11:52:07', '2015-05-14 11:59:36', 'SafiDanone.msg', '24', '', '20', 'Actuals', '2015-05-21', '2015-05-24', '0', '', '');
INSERT INTO `eas` VALUES ('203', '173', '00203', 'SDV Go-Live Support May 2015', '269', '169', '195', '2', '16', '2015-05-15 06:00:34', '2015-05-27 07:29:53', 'RE  SDV Go-Live Date.msg', '28', 'WO-S01365PS', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('204', '69', '00204', 'Telkom SCE10 Implementation', '270', null, '9', '2', '30', '2015-05-15 17:02:53', '2015-05-15 17:14:31', 'FW Project Kick Off Date.msg', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('205', '33', '00205', 'test', null, null, '9', '1', '5', '2015-05-19 09:18:58', null, '', '25', '', '0', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('206', '147', '00206', 'Additional Super User Training', '271', null, '9', '1', '35', '2015-05-19 11:10:55', null, '', '27', '', '10', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('207', '122', '00207', '7 additional licenses', null, null, '9', '2', '9', '2015-05-19 11:21:57', '2015-06-08 14:12:01', 'SNS EA.pdf', '25', '', '22', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('208', '101', '00208', 'Work Order Cancellation', '272', '263', '9', '2', '30', '2015-05-21 11:59:37', '2015-05-21 12:27:30', 'RE WO cancellation - ETA and Mandays from Accenture LDC.msg', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('209', '177', '00209', '3424432', '273', '171', '9', '0', '1', '2015-05-21 12:19:46', null, '', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('210', '173', '00210', 'SDV WM 4000 Dev 004', '274', '169', '195', '2', '16', '2015-05-22 13:08:12', '2015-05-27 07:30:51', 'RE  Second Phase UAT Testing .msg', '28', 'WO-S01366PS', '10', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('211', '177', '00211', 'fadfada', null, null, '9', '0', '1', '2015-05-22 15:53:18', null, '', '25', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('212', '177', '00212', 'afdsadfdas', '275', '171', '9', '0', '1', '2015-05-22 15:53:58', null, '', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('213', '136', '00213', 'Transmed Change Requests', '276', '279', '9', '2', '31', '2015-05-24 14:35:20', '2015-06-15 12:36:46', 'Transmed LPO - Mass Closing CR - EA_00213.pdf', '28', '15000553', '0', '2500', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('214', '217', '00214', 'JCB WM4K Portal Support', '277', null, '9', '2', '16', '2015-05-25 13:54:21', '2015-05-29 10:49:56', 'RE  Error in JCB Portal.msg', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '1', '', '');
INSERT INTO `eas` VALUES ('215', '163', '00215', 'T&M - JCB SCE102 RF Putaway Issue', '278', '235', '9', '2', '16', '2015-05-25 15:17:59', '2015-05-27 14:40:46', 'UTi Integrated Logistics LLC - 2015.05.25 - EA 00215_Signed.pdf', '28', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '1', '', '');
INSERT INTO `eas` VALUES ('216', '136', '99052', 'Transmed WM4000 Implementation', '279', null, '9', '2', '1', '2015-05-26 14:05:20', '2015-05-26 14:05:25', '', '27', ' ', '0', 'N/A', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('217', '123', '00217', 'Adding Schemas to DB', '280', '282', '9', '1', '30', '2015-05-26 14:20:20', null, '', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('218', '136', '00218', 'Label per pallet report', '281', '279', '9', '2', '31', '2015-05-26 14:26:24', '2015-06-15 12:35:01', 'Transmed LPO - Label Per Pallet By DropId CR - EA_00218.pdf', '28', '15000554/ON', '0', '2750', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('219', '123', '99053', 'RTT SCE10.1 Implementation', '282', null, '9', '2', '30', '2015-05-26 14:34:24', '2015-05-26 14:34:26', '', '27', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('220', '65', '00220', 'Delivery Note Process - Panda', '283', '233', '9', '1', '22', '2015-05-27 09:22:27', null, '', '28', '', '5', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('221', '91', '00221', 'SECCA Training and Setup', '288', '287', '9', '2', '30', '2015-05-28 13:11:42', '2015-06-02 14:02:04', 'EA_00221 (Signed).pdf', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('222', '67', '00222', 'Additional Licenses', null, null, '9', '1', '31', '2015-05-28 15:07:42', null, '', '25', '', '13.33', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('223', '178', '00223', 'New Toys Business Changes and Implementation', '289', '183', '9', '2', '30', '2015-05-29 09:10:07', '2015-05-29 09:58:46', 'RE New Business on WMS.msg', '28', '', '20', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('224', '63', '00224', 'Introduction to Warehouse Automation at Hilton Habtoor Beirut', null, null, '9', '2', '19', '2015-05-29 10:42:13', '2015-06-01 13:03:24', 'AIL-2015-189 (SNS Course).pdf', '24', 'AIL/2015/189', '0', 'N/A', '2015-06-18', '2015-06-18', '0', '', '');
INSERT INTO `eas` VALUES ('225', '38', '00225', 'Ahmad Tea - SCE/Ramp CR - SKU IMPORT', '290', null, '9', '1', '16', '2015-06-02 07:01:16', null, '', '27', '', '0', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('226', '162', '00226', 'UTi Egypt Synergy Project', '291', null, '9', '2', '16', '2015-06-02 17:28:41', '2015-06-16 05:38:08', 'FW  Mars - Project Synergy - Requirements.msg', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '1', '', '');
INSERT INTO `eas` VALUES ('227', '163', '00227', 'UTi USA - Chicago Facility SCE Assessment', '292', null, '9', '1', '16', '2015-06-03 06:00:08', null, '', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '1', '', '');
INSERT INTO `eas` VALUES ('228', '144', '00228', 'Wared Super User Training', null, null, '167', '2', '35', '2015-06-04 17:08:23', '2015-06-04 17:11:29', '20150601154454961.pdf', '24', '00000126_240-1', '8.33333', 'Actuals', '2015-06-07', '2015-06-11', '0', 'Amr Kronfol', 'Jeddah');
INSERT INTO `eas` VALUES ('229', '144', '00229', 'Wared DB and Reports Training', null, null, '9', '2', '35', '2015-06-04 17:27:38', '2015-06-04 17:41:17', 'PO for Database and Report training (SNS).pdf', '24', '00000071_240-1', '0', 'Actuals', '2015-06-06', '2015-06-08', '0', 'Khalid Salem', 'Jeddah');
INSERT INTO `eas` VALUES ('230', '90', '00230', 'Qatar Gas Consultancy', '293', null, '9', '2', '35', '2015-06-05 09:49:55', '2015-06-05 10:01:45', 'RE  QG warehouse optimization.msg', '26', '', '0', '22000', '0000-00-00', '0000-00-00', '0', 'Naji Nassar', 'P.O. Box: 24434, Qatar');
INSERT INTO `eas` VALUES ('231', '218', '00231', 'Dematic - SCE Implementation', '294', null, '9', '2', '16', '2015-06-09 07:59:35', '2015-06-10 06:51:35', 'Signed SOW for EA 00231.pdf', '27', '', '0', '12560', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('232', '218', '00232', 'Dematic AU SW T&M Support', '295', null, '9', '2', '16', '2015-06-09 08:11:26', '2015-06-10 06:52:03', 'EA 00232_Signed.pdf', '27', '', '0', 'Actuals', '0000-00-00', '0000-00-00', '1', '', '');
INSERT INTO `eas` VALUES ('233', '70', '00233', 'Introduction to Warehouse Automation at Hilton Habtoor Beirut  on June 18th 2015', null, null, '9', '2', '23', '2015-06-10 11:32:18', '2015-06-12 09:37:12', 'SNS-00233.pdf', '24', '', '0', 'N/A', '2015-06-18', '2015-06-18', '0', '', '');
INSERT INTO `eas` VALUES ('234', '219', '00234', 'Introduction to Warehouse Automation at Hilton Habtoor Beirut on June 18th 2015 ', null, null, '9', '2', '23', '2015-06-10 11:57:53', '2015-06-15 11:29:48', 'Abou Adal_EA_00234 (Signed).pdf', '24', '', '0', 'N/A', '2015-06-18', '2015-06-18', '0', '', '');
INSERT INTO `eas` VALUES ('235', '101', '00235', 'Stock Transfer', '297', '263', '9', '1', '30', '2015-06-10 12:53:56', null, '', '28', '', '0', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('236', '220', '00236', 'Introduction to Warehouse Automation at Hilton Habtoor Beirut on June 18th 2015 ', null, null, '9', '2', '23', '2015-06-11 15:30:59', '2015-06-16 13:31:43', 'hrprinter_003175.pdf', '24', '', '0', 'N/A', '2015-06-18', '2015-06-18', '0', '', '');
INSERT INTO `eas` VALUES ('237', '113', '00237', 'ASPEN Integration', null, null, '9', '1', '20', '2015-06-14 12:40:00', null, '', '454', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('243', '178', '00243', 'SIP Integration Software License', null, null, '9', '2', '30', '2015-06-16 18:39:45', '2015-06-17 10:31:08', 'RE Updated Commercials for Jordan.msg', '25', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('244', '178', '00244', 'Jordan Support Services', null, null, '9', '2', '30', '2015-06-16 18:46:09', '2015-06-17 10:31:42', 'RE Updated Commercials for Jordan.msg', '454', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('245', '178', '00245', 'Transmed Jordan SCE10.1 Implementation ', '299', null, '9', '2', '30', '2015-06-16 18:59:57', '2015-06-16 19:08:22', 'RE Updated Commercials for Jordan.msg', '27', '', '20', 'Actuals', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('246', '101', '00246', 'KSA Core Additional Changes', '300', '191', '9', '1', '30', '2015-06-16 22:00:56', null, '', '28', '', '0', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('247', '178', '00247', 'Order and Orderdetail delete restriction', '301', '183', '9', '2', '30', '2015-06-16 22:49:40', '2015-06-16 22:53:36', 'RE Change Request.msg', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('248', '101', '00248', 'L\"Oreal Owner Transfer', '302', '178', '9', '2', '30', '2015-06-17 10:32:21', '2015-06-17 10:36:22', '6817.pdf', '28', '', '0', 'N/A', '0000-00-00', '0000-00-00', '0', '', '');
INSERT INTO `eas` VALUES ('249', '86', '00249', 'Infor WM and Interfaces List of Minor Modifications', '303', '224', '9', '1', '22', '2015-06-17 15:03:25', null, '', '28', '', '0', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('250', '65', '00250', 'Delivery Note Process Option 2', '304', '232', '9', '1', '22', '2015-06-17 16:57:14', null, '', '28', '', '0', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('251', '37', '00251', 'asd', '305', '215', '9', '1', '3', '2015-06-24 12:56:06', null, '', '28', '', '0', '', '0000-00-00', '0000-00-00', '0', null, null);
INSERT INTO `eas` VALUES ('252', '86', '99054', 'GM - InforSCE10 Implementations', '224', null, '9', '2', '18', '2015-03-13 11:04:45', '2015-03-13 11:04:45', '', '27', '', '0', 'N/A', null, null, null, null, null);
INSERT INTO `eas` VALUES ('253', '160', '00253', 'test', null, null, '9', '1', '3', '2015-07-07 09:02:50', null, '', '25', '', '10', '', '0000-00-00', '0000-00-00', '0', null, null);
