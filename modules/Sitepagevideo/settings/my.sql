
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitepagevideo', 'Page Videos', 'Page Videos', '4.10.3', 1, 'extra') ;



-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagevideo_processed', 'sitepagevideo', 'Your {item:$object:page video} is ready to be viewed.', 0, ''),
('sitepagevideo_processed_failed', 'sitepagevideo', 'Your {item:$object:page video} has failed to process.', 0, '');



-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `order` )VALUES
 ('sitepage_main_video', 'sitepagevideo', 'Videos', 'Sitepagevideo_Plugin_Menus::canViewVideos', '{"route":"sitepagevideo_home","action":"home"}', 'sitepage_main', '', 1, '20');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_seaocore_tabs`
--

INSERT IGNORE INTO `engine4_seaocore_tabs` (`module` ,`type` ,`name` ,`title` ,`enabled` ,`order` ,`limit`)VALUES
('sitepagevideo', 'videos', 'recent_pagevideos', 'Recent', '1', '1', '24'),
('sitepagevideo', 'videos', 'liked_pagevideos', 'Most Liked', '1', '2', '24'),
('sitepagevideo', 'videos', 'viewed_pagevideos', 'Most Viewed', '1', '3', '24'),
('sitepagevideo', 'videos', 'commented_pagevideos', 'Most Commented', '0', '4', '24'),
('sitepagevideo', 'videos', 'featured_pagevideos', 'Featured', '0', '5', '24'),
('sitepagevideo', 'videos', 'random_pagevideos', 'Random', '0', '6', '24');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagevideo_create', 'sitepagevideo', '{item:$subject} has created a page video {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEVIDEO_CREATENOTIFICATION_EMAIL", "sitepagevideo", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");
