
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menus`
--

INSERT IGNORE INTO `engine4_sitemobile_menus` ( `name`, `type`, `title`, `order`) 
VALUES 
('sitepagealbum_profile', 'standard', 'Page Album Profile Options Menu', '999'),
('sitepagealbum_photo', 'standard', 'Page Album Photo View Options Menu', '999');
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitepagealbum_viewAlbums', 'sitepagealbum', 'View Albums', 'Sitepagealbum_Plugin_Menus', '', 'sitepagealbum_profile', NULL, 0, 1, 1, 1),
('sitepagealbum_add', 'sitepagealbum', 'Add More Photos', 'Sitepagealbum_Plugin_Menus', '', 'sitepagealbum_profile', NULL, 0, 2, 1, 1),
('sitepagealbum_edit', 'sitepagealbum', 'Edit Album', 'Sitepagealbum_Plugin_Menus', '', 'sitepagealbum_profile', NULL, 0, 3, 1, 1),
('sitepagealbum_delete', 'sitepagealbum', 'Delete Album', 'Sitepagealbum_Plugin_Menus', '', 'sitepagealbum_profile', NULL, 0, 4, 1, 1),
('sitepage_main_album', 'sitepagealbum', 'Albums', 'Sitepagealbum_Plugin_Menus::canViewAlbums', '{"route":"sitepagealbum_browse","action":"browse"}', 'sitepage_main', NULL, 0,'30', 1, 1),
 ('sitepagealbum_photo_edit', 'sitepagealbum', 'Edit', 'Sitepagealbum_Plugin_Menus', '', 'sitepagealbum_photo', NULL, '0', '1', '1', '1'),
 ('sitepagealbum_photo_delete', 'sitepagealbum', 'Delete', 'Sitepagealbum_Plugin_Menus', '', 'sitepagealbum_photo', NULL, '0', '2', '1', '1'),
('sitepagealbum_photo_share', 'sitepagealbum', 'Share', 'Sitepagealbum_Plugin_Menus', '', 'sitepagealbum_photo', NULL, '0', '3', '1', '1'),
('sitepagealbum_photo_report', 'sitepagealbum', 'Report', 'Sitepagealbum_Plugin_Menus', '', 'sitepagealbum_photo', NULL, '0', '4', '1', '1'),
('sitepagealbum_photo_profile', 'sitepagealbum', 'Make Page Profile Photo', 'Sitepagealbum_Plugin_Menus', '', 'sitepagealbum_photo', NULL, '0', '5', '1', '1');

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitepage_album_browse', 'Sitepagealbum_Form_Searchwidget', 'search_album', '', '', '');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitepage_album_view', 'sitepagealbum_profile', 'sitepage_album'),
('sitepage_photo_view', 'sitepagealbum_photo', 'sitepage_photo');
