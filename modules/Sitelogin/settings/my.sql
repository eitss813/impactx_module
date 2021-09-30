/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2017-03-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  
('sitelogin', 'Social Login and Sign-up Plugin', 'Social Login and Sign-up Plugin', '5.0.0p1', 1, 'extra');


CREATE TABLE IF NOT EXISTS `engine4_sitelogin_instagram` ( `user_id` INT NOT NULL PRIMARY KEY, `instagram_id` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `access_token` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `code` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `expires` DATETIME NOT NULL ) ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS `engine4_sitelogin_yahoo` ( `user_id` INT NOT NULL PRIMARY KEY, `yahoo_id` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `access_token` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `code` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `expires` DATETIME NOT NULL ) ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS `engine4_sitelogin_pinterest` ( `user_id` INT NOT NULL PRIMARY KEY, `pinterest_id` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `access_token` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `code` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `expires` DATETIME NOT NULL ) ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS `engine4_sitelogin_outlook` ( `user_id` INT NOT NULL PRIMARY KEY, `outlook_id` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `access_token` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `code` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `expires` DATETIME NOT NULL ) ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS `engine4_sitelogin_flickr` ( `user_id` INT NOT NULL PRIMARY KEY, `flickr_id` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `access_token` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `code` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `expires` DATETIME NOT NULL ) ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS `engine4_sitelogin_vk` ( `user_id` INT NOT NULL PRIMARY KEY, `vk_id` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `access_token` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `code` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , `expires` DATETIME NOT NULL ) ENGINE = InnoDB;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
("sitelogin_admin_main_integration_google", "sitelogin", "Google Integration", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"google"}', "sitelogin_admin_main_integration", "", "1", "0", "3"),
("sitelogin_admin_main_integration_linkedin", "sitelogin", "Linkedin Integration", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"linkedin"}', "sitelogin_admin_main_integration", "", "1", "0", "5"),
("sitelogin_admin_main_integration_facebook", "sitelogin", "Facebook Integration", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"facebook"}', "sitelogin_admin_main_integration", "", "1", "0", "1"),
("sitelogin_admin_main_integration_twitter", "sitelogin", "Twitter Integration", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"twitter"}', "sitelogin_admin_main_integration", "", "1", "0", "2"),
("sitelogin_admin_main_integration_instagram", "sitelogin", "Instagram Integration", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"instagram"}', "sitelogin_admin_main_integration", "", "1", "0", "4"),
("sitelogin_admin_main_integration_yahoo", "sitelogin", "Yahoo Integration", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"yahoo"}', "sitelogin_admin_main_integration", "", "1", "0", "7"),
("sitelogin_admin_main_integration_pinterest", "sitelogin", "Pinterest Integration", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"pinterest"}', "sitelogin_admin_main_integration", "", "1", "0", "6"),
("sitelogin_admin_main_integration_vk", "sitelogin", "Vkontakte Integration", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"vk"}', "sitelogin_admin_main_integration", "", "1", "0", "10"),
("sitelogin_admin_main_integration_outlook", "sitelogin", "Outlook Integration", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"outlook"}', "sitelogin_admin_main_integration", "", "1", "0", "8"),
("sitelogin_admin_main_integration_flickr", "sitelogin", "Flickr Integration", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"flickr"}', "sitelogin_admin_main_integration", "", "1", "0", "9"),
("sitelogin_admin_main_integration", "sitelogin", "Social Sites Integration", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"facebook"}', "sitelogin_admin_main", "", "1", "0", "2"),
("core_admin_main_plugins_sitelogin", "sitelogin", "Social Login and Sign-up Plugin - Google Apps and LinkedIn","", '{"route":"admin_default","module":"sitelogin","controller":"settings"}', "core_admin_main_plugins", "", "1", "0", "999"),
("sitelogin_admin_main_settings", "sitelogin", "Global Settings", "", '{"route":"admin_default","module":"sitelogin","controller":"settings"}', "sitelogin_admin_main", "", "1", "0", "1"),
("sitelogin_admin_main_faq", "sitelogin", "FAQ", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"faq"}', "sitelogin_admin_main", "", "1", "0", "99"),
("sitelogin_admin_main_faq_help", "sitelogin", "FAQ", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"faq"}', "sitelogin_admin_main_faq", "", "1", "0", "50"),
("sitelogin_admin_main_faq_app", "sitelogin", "App Creation", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"appfaq"}', "sitelogin_admin_main_faq", "", "1", "0", "51"),
("sitelogin_admin_main_stats", "sitelogin", "Statistics", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"statistics"}', "sitelogin_admin_main", "", "1", "0", "45"),
("sitelogin_admin_main_manage", "sitelogin", "Manage Social Sites Services", "", '{"route":"admin_default","module":"sitelogin","controller":"settings","action":"manage"}', "sitelogin_admin_main", "", "1", "0", "30");


DROP TABLE IF EXISTS `engine4_sitelogin_google`;
CREATE TABLE IF NOT EXISTS `engine4_sitelogin_google` (
  `user_id` int(11) NOT NULL,
  `google_id` varchar(255) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `expires` datetime NOT NULL,
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `engine4_sitelogin_linkedin`;
CREATE TABLE IF NOT EXISTS `engine4_sitelogin_linkedin` (
  `user_id` int(11) NOT NULL,
  `linkedin_id` varchar(255) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `expires` datetime NOT NULL,
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

