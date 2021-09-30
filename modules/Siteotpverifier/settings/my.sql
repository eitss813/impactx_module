INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('siteotpverifier', 'One Time Password (OTP) Plugin', 'One Time Password (OTP) Plugin', '5.4.1p1', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("core_admin_main_plugins_siteotpverifier", "siteotpverifier", "One Time Password (OTP) Plugin", "",'{"route":"admin_default","module":"siteotpverifier","controller":"settings"}',"core_admin_main_plugins","", 999),
("siteotpverifier_admin_main_settings","siteotpverifier","Global Settings", "",'{"route":"admin_default","module":"siteotpverifier","controller":"settings"}',"siteotpverifier_admin_main","", 1),
("siteotpverifier_admin_main_faq", "siteotpverifier","FAQ","",'{"route":"admin_default","module":"siteotpverifier","controller":"settings","action":"faq"}',"siteotpverifier_admin_main","", 999),
("siteotpverifier_admin_main_integration", "siteotpverifier", "Service Integration","", '{"route":"admin_default","module":"siteotpverifier","controller":"services","action":"index"}', "siteotpverifier_admin_main","",30),
("siteotpverifier_admin_main_contact", "siteotpverifier", "Manage Users","", '{"route":"admin_default","module":"siteotpverifier","controller":"settings","action":"contactinfo"}', "siteotpverifier_admin_main","",10),
("siteotpverifier_admin_main_level", "siteotpverifier", "Member Level Settings","", '{"route":"admin_default","module":"siteotpverifier","controller":"level"}', "siteotpverifier_admin_main","",40),
("siteotpverifier_admin_main_language", "siteotpverifier", "Manage SMS Templates","", '{"route":"admin_default","module":"siteotpverifier","controller":"settings","action":"language-editor"}', "siteotpverifier_admin_main","",50),
("siteotpverifier_admin_main_message","siteotpverifier","Send Messages", "",'{"route":"admin_default","module":"siteotpverifier","controller":"settings","action":"sendmessage"}',"siteotpverifier_admin_main","", 20),
("siteotpverifier_admin_main_stats","siteotpverifier","Statistics", "",'{"route":"admin_default","module":"siteotpverifier","controller":"statistics"}',"siteotpverifier_admin_main","", 70);

INSERT IGNORE INTO `engine4_core_pages` (`page_id`, `name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `search`) VALUES (NULL, 'siteotpverifier_auth_forgot', 'OTP Verifier - Forgot Password Page', NULL, 'Forgot Password', 'This is the site forgot password page.', '', '0', '0', '', NULL, NULL, '0', '0');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('user_otp_settings_verification', 'siteotpverifier', 'Phone Number Details', 'Siteotpverifier_Plugin_Menus::canShow', '{"route":"siteotpverifier_extended", "module":"siteotpverifier", "controller":"auth", "action":"verification"}', 'user_settings', '', '1', '0', '6');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('siteotpverifier_otpverify', 'siteotpverifier', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[expirytime],[code]');
/*alter user table for columns*/
DROP TABLE IF EXISTS `engine4_siteotpverifier_statistics`;
CREATE TABLE IF NOT EXISTS `engine4_siteotpverifier_statistics` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('login','forget','admin_sent','add','edit','signup') NOT NULL,
  `creation_date` datetime NOT NULL,
  `service` enum('amazon','twilio', 'testmode') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;


DROP TABLE IF EXISTS `engine4_siteotpverifier_sentmessages`;
CREATE TABLE IF NOT EXISTS `engine4_siteotpverifier_sentmessages` (
  `sentmessage_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `profile_type` varchar(50) NOT NULL,
  `member_level` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `service` enum('amazon','twilio', 'testmode') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

DROP TABLE IF EXISTS `engine4_siteotpverifier_mobileno`;
CREATE TABLE IF NOT EXISTS `engine4_siteotpverifier_mobileno` (
  `mobileno_id` int(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL  UNIQUE,
  `phoneno` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `verfied` int(11) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `country_code` varchar(5) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `engine4_siteotpverifier_messages`;
CREATE TABLE IF NOT EXISTS `engine4_siteotpverifier_messages` (
  `message_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `language` varchar(20) NOT NULL,
  `signup` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `forget` varchar(255) NOT NULL,
  `add` varchar(255) NOT NULL,
  `edit` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;


INSERT IGNORE INTO `engine4_siteotpverifier_messages` (`language`, `signup`, `login`, `forget`, `add`, `edit`) VALUES
('en', 'Welcome to [website_name],\r\n[code] is your One Time Password to complete the signup process. It will expire in [expirytime].', 'Hey [username]!\r\n[code] is your One Time Password to validate your login into your account. It will expire in [expirytime].\r\n', 'Hey [username]!\r\n[code] is your One Time Password to reset your account password. It will expire in [expirytime].', 'Hey [username]!\r\n[code] is your One Time Password to associate mobile number with your account. It will expire in [expirytime].', 'Hey [username]!\r\n[code] is your One Time Password to edit the mobile number associated with your account. It will expire in [expirytime].');

DROP TABLE IF EXISTS `engine4_siteotpverifier_forgot`;
CREATE TABLE IF NOT EXISTS `engine4_siteotpverifier_forgot` (
  `forgot_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `verfied` int(11) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `resent` int(11) NOT NULL,
  `type` enum('login','forgot','edit','add') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- USER, ADMIN, MODERATOR
-- login, max_resend, resettime, time
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'siteotpverifier' as `type`,
    'login' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user','moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'siteotpverifier' as `type`,
    'max_resend' as `name`,
    3 as `value`,
    0 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user','moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'siteotpverifier' as `type`,
    'resettime' as `name`,
    3 as `value`,
    86400 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user','moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'siteotpverifier' as `type`,
    'time' as `name`,
    3 as `value`,
    86400 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user','moderator', 'admin');

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('siteotpverifier.singupRequirePhone', '0');


-- COLUMNS REMOVED FROM USER TABLE
DROP TABLE IF EXISTS `engine4_siteotpverifier_users`;
CREATE TABLE IF NOT EXISTS `engine4_siteotpverifier_users` ( 
  `user_id` INT(11) NOT NULL , 
  `phoneno` VARCHAR(11) NOT NULL ,
  `country_code` VARCHAR(6) NOT NULL , 
  `enable_verification` INT(1) NOT NULL ,
  PRIMARY KEY (`user_id`)) ENGINE = InnoDB;