
--
-- Table structure for table `engine4_sitemember_compliment_categories`
--

DROP TABLE IF EXISTS `engine4_sitemember_compliment_categories`;
CREATE TABLE IF NOT EXISTS `engine4_sitemember_compliment_categories` (
  `complimentcategory_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(1000) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

--
-- Table structure for table `engine4_sitemember_views`
--

DROP TABLE IF EXISTS `engine4_sitemember_views`;
CREATE TABLE IF NOT EXISTS `engine4_sitemember_views` (
  `viewer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
   PRIMARY KEY (`viewer_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

--
-- Table structure for table `engine4_sitemember_compliments`
--

DROP TABLE IF EXISTS `engine4_sitemember_compliments`;
CREATE TABLE IF NOT EXISTS `engine4_sitemember_compliments` (
  `compliment_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `resource_type` varchar(50) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `complimentcategory_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;


--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('sitemember_admin_main_compliments', 'sitemember', 'Manage Compliments', '', '{"route":"admin_default","module":"sitemember","controller":"compliment"}', 'sitemember_admin_main', '', '1', '0', '5'),
('sitemember_compliment_browse', 'sitemember', 'Browse Compliments', '', '{"route":"sitemember_compliment_browse"}', 'sitemember_review_main', '', 1, 0, 4);

--
-- Dumping data for table `engine4_core_tasks`
--

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`) VALUES
('Recently Viewed Reset', 'sitemember', 'Sitemember_Plugin_Task_RecentlyViewed', 3600, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'user' as `type`,
    'compliment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');