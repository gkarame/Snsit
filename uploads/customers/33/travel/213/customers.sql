/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : snsit

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2014-09-05 09:02:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `customers`
-- ----------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
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
  KEY `country` (`country`),
  KEY `industry` (`industry`),
  KEY `product_1` (`product_1`),
  KEY `product_3` (`product_3`),
  KEY `product_2` (`product_2`),
  CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`country`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `customers_ibfk_2` FOREIGN KEY (`industry`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `customers_ibfk_3` FOREIGN KEY (`product_1`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `customers_ibfk_4` FOREIGN KEY (`product_2`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `customers_ibfk_5` FOREIGN KEY (`product_3`) REFERENCES `codelkups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customers
-- ----------------------------
INSERT INTO `customers` VALUES ('0', 'N/A', 'Dubai', '58', '59', null, '', '0', '', '21', '', '', '', '', '9', '', '', '', null, null, null, '', null, '', '', '', 'No', 'No');
INSERT INTO `customers` VALUES ('1', 'Testing', 'test', '58', '63', null, '12313123', '1', '', '21', 'sadasd', 'qwe', '', '', '9', '', '', '', '64', '64', null, '', null, '', '', '', 'No', 'No');
INSERT INTO `customers` VALUES ('32', 'A. N. Boukhater', 'Beirut', '111', '59', null, '+961 1 888298', '1', null, '137', 'Nahr El Mot, Metn Express Highway, Beirut, Lebanon', 'Anthony Boukhater', '', '', '9', null, null, null, '64', '66', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('33', 'ABA', 'Doha', '58', '59', null, '+974 6005765', '1', null, '150', 'ABA Tower, Airport Road, P.O.Box 75, Doha - Qatar ', 'Jon Mayson', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('34', 'Abbar Foods', 'Jeddah', '113', '59', null, '+966 2 6474000', '1', null, '144', 'Cold Stores Building, Hind Bint Al Walid Street, Petromin Area 2495, Jeddah, KSA', 'Hani Al-Ghamdi ', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('35', 'AbdulWahed Co.', 'Jeddah', '113', '59', null, '+966 12 6500282', '1', null, '155', '5th Floor, Room No.503, Al-Amal Plaza, P.O.Box: 3611, Jeddah 21481, KSA', 'Yaser AbdulWahed', '', '', '167', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('36', 'ADT', 'Abu Dhabi', '112', '59', null, '', '1', null, '146', 'P.O.Box 136687, Abu Dhabi, UAE', 'Muzammil Subhan', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('37', 'Agility', 'Safat', '130', '59', null, '+974 4500017', '1', null, '151', 'P.O.Box 25418, Safat 25418, Kuwait', 'Mohammad Habeeb ', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('38', 'Ahmad Tea', 'RAK', '112', '59', null, '+971 4 8811343', '1', null, '144', 'P.O.Box 35750, RAK, UAE', 'Ali Afshar', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('39', 'Airlink', 'Dubai', '112', '59', null, '+971 4 282 1050', '1', null, '151', 'P.O.Box 10466, Jebel Ali Free Zone, Dubai, UAE', 'Chrys Mendonca', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('40', 'Ajlan Bros', 'Riyadh', '113', '59', null, '', '1', null, '144', 'Commercial Line, South Al-Faisaliah, Olaya, Riyadh, KSA', 'Abdulrahman Ibrahim', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('41', 'AKI', 'Dubai', '112', '59', null, '', '1', null, '150', 'P.O. BOX 11245, Dubai, UAE', 'Ralph Saad', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('42', 'AKL', 'Dubai', '112', '59', null, '', '1', null, '144', 'Dubai, UAE', 'Nora Miguel', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('43', 'Al Bayader', 'Sharjah', '112', '59', null, '+971 4 229 0288', '1', null, '151', 'P.O. BOX 27106, Sharjah, UAE', 'Fadl Obeid ', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('44', 'Al Hail Holding', 'Abu Dhabi', '112', '59', null, '', '1', null, '140', 'P.O BOX 3590, Abu Dhabi, UAE', 'Nidal Al Khateeb', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('45', 'Al Islami Foods Co.', 'Dubai', '112', '59', null, '', '1', null, '151', 'Dubai, UAE', 'Joachim Yebouet', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('47', 'Al Malki Group', 'Jeddah', '113', '59', null, '+966 2 6518580', '1', null, '151', 'PO Box 337, Jeddah 21411, KSA', 'Bassel Omarbasha ', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('48', 'Al Mansour', 'Cairo', '128', '59', null, '', '1', null, '144', 'Zahraa El Maadi, Industrial Zone, P.O. Box 97, New Maadi, Cairo, Egypt', 'André Jacobs', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('49', 'Al Nahda International FZCO', 'Dubai', '112', '59', null, '', '1', null, '152', 'P.O.Box 18312, Jebel Ali, Dubai, UAE', 'Manish Mehra', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('50', 'Al Nahdi', '', null, '59', null, '+966 2 6407575', '0', null, '152', '', '', '', '', '0', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('51', 'Al Shaya', 'Safat', '130', '59', null, '+965 2224 3674', '1', null, '150', 'P.O. Box 181, Safat 13002, Kuwait', 'Biju Chandrasekharan', '', '', '9', null, null, null, '65', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('52', 'Al Talayi', 'Jeddah', '113', '59', null, '', '1', null, '151', 'Palestine St., Bridgestone Bldg, P.O.Box 42196, Jeddah 21541, KSA', 'Adbulbaset H. Moafa', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('53', 'Al Yasra', 'Safat', '130', '59', null, '+965 224 9411', '1', null, '144', 'P.O.Box: 3228, Safat 13033, Kuwait', 'Joel Ferrao', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('54', 'Al Zahrani', 'Dammam', '113', '59', null, '', '1', null, '155', 'P. O. Box 135 Dammam 31411, 2nd Floor, Business City Building, King Abdulaziz Street, KSA', 'Ahad A. Awaidha', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('55', 'Al-Aujan Group', 'Manama', '376', '59', null, '', '1', null, '150', 'P.O. Box 904, Aujan House, Govt. Avenue, Manama, Kingdom of Bahrain', 'Mohammed Al Matrook', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('56', 'Al-Ghanim', '', null, '59', null, '+965 188 1111', '0', null, '144', '', '', '', '', '0', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('57', 'Al-Haddad', 'Jeddah', '113', '59', null, '', '1', null, '155', 'Palestine St. Meshrefa District, PO Box 11629 Jeddah 21463, KSA', 'Karim Safty ', '', '', '167', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('58', 'Almutlaq Furniture', 'Riyadh', '113', '59', null, '+ 966 1 270 2835', '1', null, '150', 'P.O.Box 1321 Riyadh 11431, KSA', 'Hasan Hassan', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('59', 'Al-Rehab', 'Jeddah', '113', '59', null, '+966 12 6081000', '1', null, '157', 'Jeddah, Al Balad, Al Khaskia, Jeddah 21431, KSA', 'Salem Baradem ', '', '', '167', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('60', 'Americana Meat KSA', 'Salmiya', '130', '59', null, '', '1', null, '144', 'Al Zaben Building, P.O.Box 3448, Salmiya 22035, Kuwait', 'Murtada Halabi ', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('61', 'Anham', 'Dubai', '112', '59', null, '', '1', null, '144', 'Dubai Airport Free Zone, Dafza, East Wing, Building A4, Suite 608, P.O.Box 231082, Dubai, UAE', 'Osama Alriyahi', '', '', '9', null, null, null, '64', '160', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('62', 'Arabian Medical Marketing Co. Ltd', 'Riyadh', '113', '59', null, '', '1', null, '155', 'P.O.Box 90401, Riyadh 11613, KSA', 'Arsalan Sheikh', '', '', '167', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('63', 'Aramex', 'Dubai', '112', '59', null, '+971 4 2865000', '1', null, '140', 'P.O.Box 38410, Jebel Ali FreeZone, Dubai, UAE', 'Koshy Abraham', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('64', 'Areej', 'Rusayl', '114', '59', null, '', '1', null, '151', 'P.O.Box 22, Rusayl, Postal Code 124, Sultanate of Oman', 'Arul Salvan ', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('65', 'Arrow', 'Jeddah', '113', '59', null, '', '1', null, '144', 'PO BOX 42404, Jeddah 21541, KSA', 'Elie Sioufi', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('66', 'ATS', 'Jeddah', '113', '59', null, '+966 2 2243444', '1', null, '144', 'P.O Box 53337, Jeddah 21583, KSA', 'Eben M. Philip', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('67', 'Ayezan', 'Dubai', '112', '59', null, '', '1', null, '140', 'P.O.Box 85315, Jebel Ali South Zone, Dubai, UAE', 'Selvaraj R.N', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('68', 'Banaja', 'Jeddah', '113', '59', null, '', '1', null, '152', 'P.O. Box 42, Jeddah 21411, KSA', 'Amro Fakahani', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('69', 'Barloworld Logistics (Pty) Ltd', 'Johannesburg', '122', '60', null, '', '1', null, '150', '180 Katherine Street, Sandton 2146, Johannesburg, South Africa', 'Terrence Payne', '', '', '167', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('70', 'Bassile Freres', 'Beirut', '111', '59', null, '', '1', null, '140', 'Daroun, Harissa, Lebanon', 'Marwan Bassil', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('71', 'BCC', 'Beirut', '111', '59', null, '', '1', null, '140', 'Parallael Towers, Sin El Fil, Beirut, lebanon', 'Karim Bassil', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('72', 'BPC', 'Beirut', '111', '59', null, '', '1', null, '140', 'Bechara El Khoury Boulevard, Bechara El Khoury Tower, P.O. Box 1101 - 2040, Beirut, Lebanon', 'Emile Khoury', '', '', '0', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('73', 'Christian Art Distributors', 'Gauteng', '122', '60', null, '', '1', null, '150', '20 Smuts Ave, Vereeniging, Gauteng 1930, South Africa', 'Terrence Pringle', '', '', '167', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('74', 'Daher Food', 'Beirut', '111', '59', null, '', '1', null, '144', 'Near Hamra Plaza, Fourzol Main Road', 'Hisham Katrib', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('75', 'Danzas', 'Dubai', '112', '59', null, '', '1', null, '140', 'P.O.Box 2623, Dubai, UAE', 'Mazen El Ghosseini', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('76', 'Deal Logistics', 'Dubai', '112', '59', null, '', '1', null, '140', 'P.O.Box 18601, Dubai, UAE', 'Margareta AbuRas ', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('77', 'DHL', 'Al Khobar', '113', '59', null, '', '1', null, '140', 'P.O.B.31492, Al Khobar 31952, KSA', 'Nas167 Ahmed', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('78', 'DIB', 'Dubai', '112', '59', null, '', '1', null, '259', 'P.O.Box 1080, Dubai, UAE', 'Muhammed Aslam', '', '', '9', null, null, null, null, '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('79', 'DSS', 'Dubai', '112', '59', null, '', '1', null, '151', 'Arenco Building, Zaabel Road, P.O.Box 52262, Dubai, UAE', 'Tina Malost', '', '', '168', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('80', 'Ducab', 'Dubai', '112', '59', null, '', '1', null, '151', 'P.O.Box 11529, Dubai, UAE', 'Raihan Amir', '', '', '168', null, null, null, null, '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('81', 'EasyLog', 'Amman', '115', '59', null, '', '1', null, '140', 'P.O.Box 831379, Amman 1183, Jordan', 'Zaid Souqi', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('82', 'ED&F MAN', '', '127', '63', null, '', '1', null, '144', '8 Shenton Way, AXA Tower, #16-02, Singapore 068811', 'Ashley Mcintyre ', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('83', 'Extra', 'Al Khobar', '113', '59', null, '', '1', null, '155', 'Al Khobar, King Faisal Str., P.O.Box76688, Khobar 31958, KSA', 'Mazen Massalkhi', '', '', '167', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('84', 'Fattal', 'Beirut', '111', '59', null, '', '1', null, '140', 'Sin El Fil, jisr El Wati, Dany Chamoun Str., Fattal Building, Beirut, Lebanon', 'Ahmad Solh', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('85', 'Glow', 'Hawally', '130', '59', null, '', '1', null, '151', 'Noura Complex, 8th & 9th Floor, P.O.Box 4284, Hawally 32073, Kuwait', 'Hamad Hammauda', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('86', 'GM - Africa and Middle East', 'Dubai', '112', '59', null, '', '1', null, '137', 'P.O.Box 92333, Plot M000783, Dubai, UAE', 'Murtaza Hassan', '', '', '9', null, null, null, '64', '160', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('87', 'GMG', 'Dubai', '112', '59', null, '', '1', null, '140', 'P.O.Box 894, Dubai, UAE', 'Nikel Anand', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('88', 'Golden Food', 'Beirut', '111', '59', null, '', '1', null, '144', 'First Floor, Abou Naoum Building, Mkalles, Beirut, lebanon', 'Mark Eid', '', '', '9', null, null, null, '64', '66', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('89', 'GSL', 'Dubai', '112', '59', null, '', '1', null, '140', 'P.O.Box 2022, Dubai, UAE', 'Ajit Handa', '', '', '9', null, null, null, '65', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('90', 'GWC', 'Doha', '58', '59', null, '', '1', null, '150', 'P.O. Box: 24434, Doha, Qatar', 'Maged Emil Kamal ', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('91', 'Holdal', 'Beirut', '111', '59', null, '', '1', null, '140', 'Dekwaneh, Galerie Matta, Holdal Building, Beirut, Lebanon', 'Danny Kreidy', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('92', 'IATCO', 'Jeddah', '113', '59', null, '', '1', null, '140', 'Jeddah 21411, KSA', 'Rafik Georges', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('93', 'Inchcape', 'Dubai', '112', '59', null, '', '1', null, '140', 'Dubai, UAE', 'Mirza Baig', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('94', 'Intercol', 'Manama', '376', '59', null, '', '1', null, '151', 'P.O. Box: 584, Manama, Bahrain', 'Faiz Syedullah', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('95', 'Jawad', 'Manama', '376', '59', null, '', '1', null, '151', 'Jawad House, 171 Sh, Issa Avenue, P.O.Box 430, Manama, Bahrain', 'Surendran', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('96', 'Logistica', 'Beirut', '111', '59', null, '', '1', null, '140', 'Old Saida Road, Hadath, Beirut, Lebanon', 'Alain Bounassif', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('97', 'Logistica e2e', 'Giza', '128', '59', null, '', '1', null, '140', '6 El-Hesn Street, Giza, Arab Republic of Egypt', 'Sameh Yousef', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('98', 'M+M Hechme', 'Beirut', '111', '59', null, '', '1', null, '140', 'Plot 110, Dayshunieh, P.O.Box 151, Mansurieh, Lebanon', 'Jean Frederic Alam', '', '', '9', null, null, null, '64', '66', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('99', 'Madi International', 'Dubai', '112', '59', null, '', '1', null, '157', 'Al Quoz Industrial 2, P.O. Box: 56290, Dubai, UAE', 'Imad Hamdoun', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('100', 'Maxx', 'Dubai', '112', '59', null, '', '1', null, '151', 'P.O.Box 17740, Jebel Ali FreeZone, Dubai, UAE', 'Mohammed Shaiq', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('101', 'MCT', 'Dubai', '112', '59', null, '', '1', null, '140', 'P.O.Box 261075, Jafz, Dubai, UAE', 'Rami Alameddine', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('102', 'Mersaco', 'Beirut', '111', '59', null, '', '1', null, '152', 'Jamil Kfoury SAL building, Sami Solh avenue, Parc sector, Beirut, Lebanon', 'Ghassan Al Mahassni', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('103', 'Mohamed Yousuf naghi & Brothers group', '', null, '59', null, '', '0', null, '137', '', '', '', '', '0', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('104', 'Mohebi', 'Dubai', '112', '59', null, '', '1', null, '140', 'P.O.Box 267, Dubai, UAE', 'Ben Jacob', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('105', 'Nader Group', 'Amman', '115', '59', null, '', '1', null, '140', 'Al Lawzeh St., Muqablein, Amman 11118, Jordan', 'Andre Leroux', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('106', 'Nestle', 'Dubai', '112', '59', null, '', '1', null, '144', 'P.O.Box 17740, Jebel Ali, Dubai, UAE', 'Naseema Kadher', '', '', '9', null, null, null, '65', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('107', 'NGK', 'Boksburg', '122', '60', null, '', '1', null, '150', 'Bantry Park, 41 Jansen Road, Jet Park, Boksburg, 1459 South Africa', 'Merle Van Zyl', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('108', 'Pan Emirates', 'Sharjah', '112', '59', null, '', '1', null, '151', 'P.O.Box 23197, Sharjah, Dubai, UAE', 'Eyas Khashan ', '', '', '9', null, null, null, '64', '160', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('109', 'Panalpina', 'Dubai', '112', '59', null, '', '1', null, '144', 'Dubai, UAE', 'Anthonie Verploegh', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('110', 'Parmalat', 'Stellenbosch', '122', '60', null, '', '1', null, '140', 'Strand Road, Stellenbosch 7600, South Africa', 'Hayden Williams ', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('111', 'Pepsico', 'Jeddah', '113', '59', null, '', '1', null, '158', 'Al Zaben Building, 2nd Floor, Thaliah Street, P.O. Box 11414, Jeddah, KSA, 21453', 'Azzam Adhami', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('112', 'PetroRabigh', 'Riyadh', '113', '59', null, '', '1', null, '149', 'P.O.Box 666, Riyadh 21911, KSA', 'Yasuhiko Kitaura', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('113', 'Pharmaworld', 'Dubai', '112', '59', null, '', '1', null, '152', 'Dubai, UAE', 'Mohammad Malek', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('114', 'Phoenix Beverages Group', 'Phoenix', '371', '60', null, '', '1', null, '158', '3rd Floor , Phoenix House, Pont Fer, Phoenix, Mauritius', 'Mogini Rungasamy', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('115', 'Pierlite Middle East', 'Sharjah', '112', '59', null, '', '1', null, '140', 'Sharjah FreeZone, P.O.Box 8181, Sharjah, Dubai, UAE', 'Jacob Daniel ', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('116', 'Planet Pharmacies', 'Dubai', '112', '59', null, '', '1', null, '152', 'Dubai, UAE', 'Hameed Imran', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('117', 'QNIE', 'Dubai', '58', '59', null, '', '1', null, '151', 'PO. Box: 490 Doha, Qatar', 'Hisham Basheer', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('118', 'Radec', 'Beirut', '111', '59', null, '', '1', null, '140', 'Beirut, Lebanon', 'Naji Sabbagh', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('119', 'Redington Gulf', 'Dubai', '112', '59', null, '', '1', null, '140', 'P.O.Box 17266, Dubai, UAE', 'Sunil Dsouza', '', '', '9', null, null, null, '64', '160', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('120', 'REDTAG', 'Dubai', '112', '59', null, '', '1', null, '150', 'P.O.Box 17474, Dubai, UAE', 'Rajiv Shankar ', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('121', 'RHS', 'Dubai', '112', '59', null, '', '1', null, '151', 'P.O.Box 7, Dubai, UAE', 'Girish Kurup', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('122', 'RSA Logistics', 'Dubai', '112', '59', null, '', '1', null, '151', 'Dubai Logistics City, Dubai, UAE', 'Abhishek Shah', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('123', 'RTT', 'Boksburg', '122', '60', null, '', '1', null, '140', 'Reg No: 2007/003421/07, Cnr Jones & Springbok Roads, Bartlett, Boksburg, South Africa', 'Charm Naicker', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('125', 'S2S IMI Group', 'Jeddah', '113', '59', null, '', '1', null, '149', 'Engineering, Office 404, 4th Floor Matbouli Plaza, Al Ma\'adi Street, Ruwais District, PO Box No. 7972, Jeddah 21472, KSA', 'Afteem Khoury', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('126', 'Saudi Vetonit Co. LTD. SAVETO', 'Riyadh', '113', '59', null, '', '1', null, '150', 'P.O. Box 52235, Riyadh 11563, KSA', 'Sami Hajjaj', '', '', '167', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('127', 'Sharaf Logistics Pakistan', 'Lahore', '372', '63', null, '', '1', null, '140', '46 KM, Multan Road, Nathay Khalsa, Manga Mandi, Lahore, Pakistan', 'Rashid Siddique ', '', '', '9', null, null, null, '160', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('128', '66CO', 'Al Khobar', '113', '59', null, '', '1', null, '158', '2nd Industrial City, Dammam-31943, Al-Khobar-31952, KSA', 'Pradeep Kumar/ Abdul Mansoor ', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('129', 'Storall', 'Dubai', '112', '59', null, '', '1', null, '140', 'Jebel Ali Industrial Area 2, P.O.Box 79775, Dubai, UAE', 'Ghassan Abughazaleh', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('130', 'Sunbulah Group', 'Jeddah', '113', '59', null, '', '1', null, '144', 'Jeddah, KSA', 'Dalia Khafagy', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('131', 'Supreme', '', '373', '61', null, '', '1', null, '140', 'Turbinenweg 2, 8866 Ziegelbruecke, Switzerland', 'Gaurav Kumar', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('132', 'Swift', 'Dubai', '112', '59', null, '', '1', null, '140', 'Freight Management & Services, P.O.Box 50177, Dubai, UAE', 'Anil Mathews', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('134', 'Tamer Group', 'Jeddah', '113', '59', null, '', '1', null, '152', 'P.O.Box 180, Jeddah 21411, KSA', 'Ahmed Bin Almas', '', '', '9', null, null, null, '64', '66', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('135', 'TD & Company Limited', 'Tokyo', '375', '59', null, '', '1', null, '140', 'Nishi-Gotanda 1-11-1-508, Shinagawa, Tokyo, Japan 141-0031', 'Julien Obata', '', '', '9', null, null, null, '64', '66', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('136', 'Transmed', 'Dubai', '112', '59', null, '', '1', null, '140', '9th floor, Gulf Tower, P.O.Box 1604, Dubai, UAE', 'Said Adada', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('137', 'United Beverage', 'Safat', '130', '59', null, '', '1', null, '158', 'Sabhan Industrial Area, P.O.Box 224, Safat 13003, Kuwait', 'Shamsudeen Nageem', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('138', 'United Group', 'Al Khobar', '113', '59', null, '', '1', null, '144', 'Al Saeed Towers, Khobar/Dammam Main Highway, Tower Number 1-A, 3rd Floor, Office Number 306, P.O.Box 64, Al-Khobar 31952, KSA', 'Magid Abumahfoud', '', '', '9', null, null, null, '64', '66', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('139', 'Unipharm', 'Beirut', '111', '59', null, '', '1', null, '152', 'P.O.Box 11-5255, Farid Abou Jaoude, 4th Floor, Assaad Rached St, Jal El dib, Metn, Lebanon', 'Fadi Kibrite', '', '', '9', null, null, null, '64', '66', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('140', 'UPS Supply Chain Solutions', '', null, '59', null, '', '0', null, '140', '', '', '', '', '0', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('141', 'UTi', 'South Carolina', '164', '0', null, '', '1', null, '154', '700 Gervais Street, Suite 100, Columbia, South Carolina,  29201, USA', 'Deanne Groves', '', '', '9', null, null, null, '65', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('142', 'UTi', '', '374', '61', null, '', '1', null, '154', 'Bedrijvenzone Machelen Cargo, Bld 829A, 1830 Machelen, Belgium', 'Karsten Klag', '', '', '9', null, null, null, '65', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('143', 'UWC', 'Jeddah', '113', '59', null, '', '1', null, '140', 'P.O. Box: 31450, Jeddah 21497, KSA', 'Abdulwareth Shamsan', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('144', 'Wared', 'Jeddah', '113', '59', null, '', '1', null, '140', 'Dar Al-Nahda Business Center, Prince Sultan Street, Jeddah 21540 KSA', 'Amr Kronfol', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('145', 'Wilhelmsen', 'Dubai', '112', '59', null, '', '1', null, '140', 'Jebel Ali, Dubai, UAE', 'John Martin', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('146', 'Zamil AC', 'Dammam', '113', '59', null, '', '1', null, '155', 'P.O. Box 41015, Street 106, 2nd Industrial City, Dammam 31521, KSA', 'Azeem ', '', '', '9', null, null, null, null, null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('147', 'Abu Issa', 'Doha', '58', '59', null, '', '1', null, '150', 'P.O.Box 6255, Doha, Qatar', 'Ahmed Al-Tamimi', '', '', '9', null, null, null, '64', null, null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('148', 'Green House', 'Sharjah', '112', '59', null, '', '1', null, '140', 'P.O.Box 5927, Sharjah, UAE', 'Ziad Abdel Massih', '', '', '9', null, null, null, '64', '65', null, null, null, null, null, null, 'No', 'No');
INSERT INTO `customers` VALUES ('149', 'Kalem (BTA)', 'Istanbul', '129', '59', null, '', '1', null, '144', 'Maltepe Mh. Londra Asfaltı Cd., No:38 Kat : 1/A-B Saadet Plaza, Cevizlibağ-Zeytinburnu, Istanbul, Turkey', 'Banu Usanmaz', '', '', '9', null, null, null, '64', '66', null, null, null, null, null, null, 'No', 'No');
