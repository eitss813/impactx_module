/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 * @author     Consecutive Bytes
 */

-- --------------------------------------------------------

--
-- Table structure for table `engine4_cbpageanalytics_pages`
--

DROP TABLE IF EXISTS `engine4_cbpageanalytics_pages`;
CREATE TABLE `engine4_cbpageanalytics_pages` (
  `page_id` int(11) unsigned NOT NULL auto_increment,
  `page_original_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `subject_type` varchar(128) DEFAULT NULL,
  `subject_id` int(11) unsigned DEFAULT NULL,
  `subject_name` varchar(128) DEFAULT NULL,
  `page_url` text NOT NULL,
  `referrer_page` varchar(128) DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `page_name` text NOT NULL,
  `module` varchar(128) NOT NULL,
  `controller` varchar(128) NOT NULL,
  `action` varchar(128) NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '1',
  `creation_date` datetime NOT NULL,
  `status` tinyint NOT NULL default '1',
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_cbpageanalytics', 'cbpageanalytics', 'CB - Page Analytics', '', '{"route":"admin_default","module":"cbpageanalytics","controller":"settings"}', 'core_admin_main_plugins', '', 999),
('cbpageanalytics_admin_main_settings', 'cbpageanalytics', 'Global Settings', '', '{"route":"admin_default","module":"cbpageanalytics","controller":"settings"}', 'cbpageanalytics_admin_main', '', 1),
('cbpageanalytics_admin_main_analytics', 'cbpageanalytics', 'Page Analytics', '', '{"route":"admin_default","module":"cbpageanalytics","controller":"settings","action":"page-analytics"}', 'cbpageanalytics_admin_main', '', 2),
('cbpageanalytics_admin_main_graphs', 'cbpageanalytics', 'Graph Analytics', '', '{"route":"admin_default","module":"cbpageanalytics","controller":"settings","action":"graph-analytics"}', 'cbpageanalytics_admin_main', '', 3)
;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  
('cbpageanalytics', 'CB - Page Analytics', 'Tracks visits on all pages.', '4.10.4', 1, 'extra');
