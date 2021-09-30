/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitepagealbum', 'Page Albums', 'Sitepagealbum', '4.10.3p2', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `order` )VALUES
 ('sitepage_main_album', 'sitepagealbum', 'Albums', 'Sitepagealbum_Plugin_Menus::canViewAlbums', '{"route":"sitepagealbum_home","action":"home"}', 'sitepage_main', '', 1, '19');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_seaocore_tabs`
--

INSERT IGNORE INTO `engine4_seaocore_tabs` (`module` ,`type` ,`name` ,`title` ,`enabled` ,`order` ,`limit`)VALUES
('sitepagealbum', 'albums', 'recent_pagealbums', 'Recent', '1', '1', '24'),
('sitepagealbum', 'albums', 'liked_pagealbums', 'Most Liked', '1', '2', '24'),
('sitepagealbum', 'albums', 'viewed_pagealbums', 'Most Viewed', '1', '3', '24'),
('sitepagealbum', 'albums', 'commented_pagealbums', 'Most Commented', '0', '4', '24'),
('sitepagealbum', 'albums', 'featured_pagealbums', 'Featured', '0', '5', '24'),
('sitepagealbum', 'albums', 'random_pagealbums', 'Random', '0', '6', '24'),
('sitepagealbum', 'photos', 'recent_pagephotos', 'Recent', '1', '1', '24'),
('sitepagealbum', 'photos', 'liked_pagephotos', 'Most Liked', '1', '2', '24'),
('sitepagealbum', 'photos', 'viewed_pagephotos', 'Most Viewed', '1', '3', '24'),
('sitepagealbum', 'photos', 'commented_pagephotos', 'Most Commented', '0', '4', '24'),
('sitepagealbum', 'photos', 'featured_pagephotos', 'Featured', '0', '5', '24'),
('sitepagealbum', 'photos', 'random_pagephotos', 'Random', '0', '6', '24');


INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagealbum_create', 'sitepagealbum', '{item:$subject} has created a page album {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGEALBUM_CREATENOTIFICATION_EMAIL", "sitepagealbum", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");