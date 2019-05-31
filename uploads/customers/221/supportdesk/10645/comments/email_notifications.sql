/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : snsit

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2015-01-22 17:25:47
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `email_notifications`
-- ----------------------------
DROP TABLE IF EXISTS `email_notifications`;
CREATE TABLE `email_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unique_name` varchar(50) NOT NULL,
  `name` varchar(250) NOT NULL,
  `message` text NOT NULL,
  `module` varchar(25) NOT NULL,
  `not_in_groups` int(1) NOT NULL DEFAULT '0',
  `name_tab` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of email_notifications
-- ----------------------------
INSERT INTO `email_notifications` VALUES ('1', 'ea_new', 'Send E-mail when EA is Created', 'The Following EA has been Created:<br />\r\n<a href=\"{url}\">EA #{ea_number}</a><br />\r\nDescription: {description}<br />\r\nCustomer: {customer_name}<br />{project_name}\r\nAuthor: {author}<br />\r\nTotal Amount: {total_amount} {currency}<br />\r\nDiscount: {discount}<br />\r\nTotal Net Amount: {total_net_amount} {currency}<br />\r\n{total_man_days_by_category}<br />\r\n{net_man_day_rate_by_category}', 'eas', '0', null);
INSERT INTO `email_notifications` VALUES ('2', 'ea_approved', 'Send E-mail when EA is Approved', 'The Following EA has been Approved:<br />\r\n<a href=\"{url}\">EA #{ea_number}</a><br />\r\nDescription: {description}<br />\r\nCustomer: {customer_name}<br />{project_name}\r\nAuthor: {author}<br />\r\nTotal Amount: {total_amount} {currency}<br />\r\nDiscount: {discount}<br />\r\nTotal Net Amount: {total_net_amount} {currency}<br />\r\n{total_man_days_by_category}<br />\r\n{net_man_day_rate_by_category}', 'eas', '0', null);
INSERT INTO `email_notifications` VALUES ('3', 'ea_cancelled', 'Send E-mail when EA is Cancelled', 'The Following EA has been Cancelled:<br />\r\n<a href=\"{url}\">EA #{ea_number}</a><br />\r\nDescription: {description}<br />\r\nCustomer: {customer_name}<br />{project_name}\r\nAuthor: {author}<br />\r\nTotal Amount: {total_amount} {currency}<br />\r\nDiscount: {discount}<br />\r\nTotal Net Amount: {total_net_amount} {currency}<br />\r\n{total_man_days_by_category}<br />\r\n{net_man_day_rate_by_category}', 'eas', '0', null);
INSERT INTO `email_notifications` VALUES ('4', 'expenses_submitted', 'Expense Sheet ID {no} Submitted', 'Expense Sheet ID {no_url} with total amount of {amount} USD has been Submitted by: {username}<br/>\r\nThe following expense sheet entries are billable:<br/>\r\n{billableItems}\r\n\r\nDetails:<br/>\r\n- Total Amount:{amount} USD<br/>\r\n- Payable Amount: {payable} USD<br/>\r\n- Non-Payable Amount: {not_payable} USD<br/>\r\n- Billable Amount: {billable} USD<br/>\r\n- Non-Billable Amount: {not_billable} USD<br/>\r\n', 'expenses', '0', null);
INSERT INTO `email_notifications` VALUES ('5', 'expenses_paid', 'Expense Sheet ID {no} Paid', 'Expense Sheet ID {no_url} submitted by {username} has been Paid by the {currentUser}<br/>\r\n\r\nDetails:<br/>\r\n- Total Amount:{amount} USD<br/>\r\n- Payable Amount: {payable} USD<br/>\r\n- Non-Payable Amount: {not_payable} USD<br/>\r\n- Billable Amount: {billable} USD<br/>\r\n- Non-Billable Amount: {not_billable} USD<br/>', 'expenses', '0', null);
INSERT INTO `email_notifications` VALUES ('6', 'expenses_approved', 'Expense Sheet ID {no} Approved', 'Expense Sheet {no_url} submitted by {username} has been Approved by {currentUser}<br/>\r\n\r\nDetails:</p>\r\n- Total Amount:{amount} USD<br/>\r\n- Payable Amount: {payable} USD<br/>\r\n- Non-Payable Amount: {not_payable} USD<br/>\r\n- Billable Amount: {billable} USD<br/>\r\n- Non-Billable Amount: {not_billable} USD<br/>', 'expenses', '0', null);
INSERT INTO `email_notifications` VALUES ('7', 'expenses_rejected', 'Expense Sheet ID {no} Rejected', 'Expense Sheet ID {no_url} submitted by {username} has been Rejected by  {currentUser} for the following reason: <br/><p>{reason}</p>', 'expenses', '0', null);
INSERT INTO `email_notifications` VALUES ('8', 'project_assigned_act', 'New Project Assignment', '<p>Hi {firstname},\r\n\r\nYou have been assigned to a new tasks related to {eaCategory} project for customer {customer} in {country}.\r\n\r\nIn case of travel please make sure to collect expense receipts during your trips.</p>', 'projects', '1', null);
INSERT INTO `email_notifications` VALUES ('9', 'project_assigned_not', 'New Project Assignment', '<p>Hi {firstname},\r\n\r\nYou have been assigned to a new tasks related to {eaCategory} project for customer {customer} in {country}.\r\n\r\nIn case of travel the standard SNS per diem policy will apply on this project.</p>', 'projects', '1', null);
INSERT INTO `email_notifications` VALUES ('10', 'milestone', '{projectname}- Milestone {number} is now Closed', '', 'projects', '0', null);
INSERT INTO `email_notifications` VALUES ('11', 'unsubmitted_timesheets', 'Send E-mail with pending timesheets', '<p>The following time sheets need to be completed:</p>\r\n<p>{timesheets_pending}</p>', 'timesheets', '0', null);
INSERT INTO `email_notifications` VALUES ('12', 'personal_unsubmitted_timesheets', 'Send E-mail with personal pending timesheets', 'Dear {name},</br>\r\nYou are late in submitting the below time sheets. Please make sure they are completed ASAP.<br>\r\n{timesheets_pending}</br>', 'timesheets', '1', null);
INSERT INTO `email_notifications` VALUES ('13', 'invoices_to_print', 'Invoices To Print', 'The following invoice numbers are ready to be Printed:\r\n{invoices}Total Invoices per partner:\r\n{invoicesPartner}', 'invoices', '0', null);
INSERT INTO `email_notifications` VALUES ('14', 'invoices_printed', '{sns} Invoice #{invoice_number}', 'Dear {bill_to_contact},<br/>\r\nPlease find enclosed Invoice number {invoice_number} related to Project {project_name} and EA#{ea_id}. We appreciate if the payment of the invoice can be made by the {date}.<br/>\r\nIn case you have any questions please feel free to contact me at nadine.daaboul@sns-emea.com<br/>\r\nBest Regards,\r\nNadine Daaboul\r\n', 'incoices', '0', null);
INSERT INTO `email_notifications` VALUES ('15', 'requests_new', '{request_type} Request for {user_fullname}', '{user_fullname} has requested a \"{request_type}\" from {startDate} until {endDate}.\r\nPlease {approve_link} or {reject_link}', 'requests', '0', null);
INSERT INTO `email_notifications` VALUES ('16', 'requests_approved', 'Your {request_type} Request  From {startDate} To {endDate} is Approved', '', 'requests', '0', null);
INSERT INTO `email_notifications` VALUES ('17', 'requests_rejected', 'Your {request_type} request is Rejected', '', 'requests', '0', null);
INSERT INTO `email_notifications` VALUES ('18', 'requests_hr_new', '{requests_hr_type}  Request for {requests_hr_fullname}', '{requests_hr_fullname} has requested a {requests_hr_type} to be completed by the {requests_hr_date}\r\n{requests_hr_note}\r\n\r\nPlease click here when the required documents are {requests_hr_link}', 'requests_hr', '0', null);
INSERT INTO `email_notifications` VALUES ('19', 'requests_hr_completed', 'Your {requests_hr_type} is ready for collection', '', 'requests_hr', '0', null);
INSERT INTO `email_notifications` VALUES ('20', 'support_desk_users', '{customer_name} - SR#{no} Update ', 'Dear {customer_name},\r\n\r\nPlease find below updated information related to SR#{no}\r\n\r\nDate: {date}\r\nComment From: SNS\r\nNew Comment: {comment}\r\nStatus:In Progress\r\n{history}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', '1', null);
INSERT INTO `email_notifications` VALUES ('22', 'support_desk_customers', '{customer_name} - SR#{no} Update ', 'Dear {user_name},\r\n\r\nPlease find below updated information related to SR#{no} \r\n\r\nDate: {date}\r\nComment From: {customer_name}\r\nNew Comment: {comment}\r\nStatus:In Progress\r\n{history}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', '1', null);
INSERT INTO `email_notifications` VALUES ('23', 'support_desk_close', '{customer_name} - SR#{no} Closed: {subject}', 'Dear {customer_name},\r\n\r\nPlease find below updated information related to SR#{no}\r\n\r\nDate: {date}\r\nComment From: {user_name}\r\nStatus: CLOSED\r\n\r\n{history}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', '1', null);
INSERT INTO `email_notifications` VALUES ('24', 'support_desk_pending_info', '{customer_name} - SR#{no} Pending Info: {subject}', 'Dear {customer_name},\r\n\r\nPlease find below updated information related to SR#{no}\r\n\r\nDate: {date}\r\nComment From: {user_name}\r\nStatus: Pending Info\r\n\r\n{history}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', '1', null);
INSERT INTO `email_notifications` VALUES ('25', 'support_desk_reopened', '{customer_name} - SR#{no} Reopened: {subject}', 'Dear {cs_representative},\r\n\r\nPlease find below updated information related to SR#{no}\r\n\r\nDate: {date}\r\nComment From: {customer_name}\r\nStatus: Reopened\r\n\r\n{history}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', '1', null);
INSERT INTO `email_notifications` VALUES ('26', 'support_desk_assigned_to', 'SR#{no}  has been Re-assigned', 'Dear {customer_name},\r\n\r\nThis is to inform you that SR#{no} has been re-assigned to {user_name}.\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', '1', null);
INSERT INTO `email_notifications` VALUES ('27', 'support_desk_new_sr', '{customer_name} - New SR#{no} : {subject}', 'Dear {customer_name},\r\n\r\nWe have received your Support Request and it has been logged by our support team. The issue has been assigned a severity {severity} with the following Support Request Number (SR#): {no}.\r\n\r\nWe would like to inform you that {cs_representative} ({cs_representative_email}) will be supporting and assisting you in resolving this issue.\r\n\r\nAs a reference to your support issue, please use the assigned SR number mentioned above. The Technical Consultant will contact you if further information is needed or when the issue is resolved.\r\n\r\nSR#:{no_link}\r\n\r\nSchema: {schema}\r\n\r\nEnvironment: {environment}\r\n\r\nLogged by: {customer_contact}\r\n\r\nLog Date: {date}\r\n\r\nIncident Description:\r\n{description}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', '1', null);
INSERT INTO `email_notifications` VALUES ('28', 'support_desk_cron_close', '{customer_name} - SR#({ct}) To Confirm Close', 'Dear {customer_name},\r\n\r\nKindly Confirm Close the following SR({ct}): {list} \r\n\r\nRegards,\r\nSNS Customer Service\r\n\r\n', 'support_desk', '1', null);
INSERT INTO `email_notifications` VALUES ('29', 'support_desk_cron_pending', '{customer_name} - SR#({ct}) with Pending Information', 'Dear {customer_name},\r\n\r\nKindly provide us with the information required to close the following SR({ct}): {list} \r\n\r\nRegards,\r\nSNS Customer Service\r\n\r\n', 'support_desk', '1', null);
INSERT INTO `email_notifications` VALUES ('30', 'support_desk_weekly_performance', 'CS Weekly Performance Snapshot', 'Dear All,\r\n\r\nPlease find below a summary of the CS performance during the week starting {start_date} and ending on {end_date}:\r\n\r\n{list1}\r\nExceptions:\r\n{list_exceptions}\r\n{list_exceptions_2}\r\nTop 5 Customers:\r\n{list_customer}\r\nTop 3 CS Performers:\r\n{list_performers}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', '1', null);
INSERT INTO `email_notifications` VALUES ('31', 'support_desk_weekly_summary', 'CS Weekly Status Report', 'Dear All,\r\n\r\nPlease find below a summary of the current CS SRs Status:\r\n\r\n1-SRs in status New: {ct_new}\r\n2-SRs In Progress: {ct_in_progress}\r\n\r\nCustomers with HIGH Strategic Rating:\r\n1-SRs in status New for more than 48 hours: {ct_high_new}\r\n2-SRs In Progress for more than 96 hours: {ct_high_in_progress}\r\n\r\nCustomers with MEDIUM Strategic Rating:\r\n1-SRs in status New for more than 48 hours: {ct_medium_new}\r\n2-SRs In Progress for more than 96 hours: {ct_medium_in_progress}\r\n\r\nCustomers with LOW Strategic Rating:\r\n1-SRs in status New for more than 48 hours: {ct_low_new}\r\n2-SRs In Progress for more than 96 hours: {ct_low_in_progress}\r\n\r\nRegards,\r\nSNS Customer Service', 'support_desk', '1', null);
INSERT INTO `email_notifications` VALUES ('32', 'support_desk_general_permission', 'SR Update Notifications', '', 'support_desk', '0', null);
INSERT INTO `email_notifications` VALUES ('34', 'support_desk_system_down', '{customer_name} - System Down SR#{no} : {subject}', 'System Down at {customer_name}\r\n\r\nSR#: {no}\r\n\r\nSchema: {schema}\r\n\r\nEnvironment: {environment}\r\n\r\nLogged by: {customer_contact}\r\n\r\nLog Date: {date}\r\n\r\nIncident Description:\r\n\r\n{description}\r\n\r\n', 'support_desk', '0', null);
INSERT INTO `email_notifications` VALUES ('35', 'invoices_paid', 'Invoices Paid', 'Dear All,<br>\r\nThe following invoices have been paid:<br>\r\n{invoices}', 'invoices', '0', null);
