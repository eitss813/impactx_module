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

INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
('sitealbum_main', 'standard', 'Advanced Album Main Options Menu', '150');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('core_main_sitealbum', 'sitealbum', 'Albums', 'Sitealbum_Plugin_Menus::canViewAlbums', '{"route":"sitealbum_general","action":"index"}', 'core_main','',15, 1, 1),
('sitealbum_main_home', 'sitealbum', 'Albums Home', 'Sitealbum_Plugin_Menus::canViewAlbums', '{"route":"sitealbum_general","action":"index"}', 'sitealbum_main', '', 1, 1, 1),
('sitealbum_main_browse', 'sitealbum', 'Browse Albums', 'Sitealbum_Plugin_Menus::canViewAlbums', '{"route":"sitealbum_general","action":"browse"}', 'sitealbum_main', '', 2, 1, 1),
('sitealbum_main_photos', 'sitealbum', 'Photos', 'Sitealbum_Plugin_Menus::canViewAlbums', '{"route":"sitealbum_general","action":"photos"}', 'sitealbum_main', '',3, 1, 1),
('sitealbum_main_manage', 'sitealbum', 'My Albums', 'Sitealbum_Plugin_Menus::canCreateAlbums', '{"route":"sitealbum_general","action":"manage"}', 'sitealbum_main', '',4, 1, 1),
('sitealbum_quick_upload', 'sitealbum', 'Add New Photos', 'Sitealbum_Plugin_Menus::canCreateAlbums', '{"route":"sitealbum_general","action":"upload","class":"buttonlink"}', 'sitealbum_quick', '', 1, 1, 1);
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_searchform`
--

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitealbum_index_browse', 'Sitealbum_Form_Search_Search', 'search', '', '', ''),
('sitealbum_index_index', 'Sitealbum_Form_Search_Search', 'search', '', '', ''),
('sitealbum_index_manage', 'Sitealbum_Form_Search_Search', 'search', '', '', '');


INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES
('sitealbum_photo_view', 'standard', 'Album Photo View Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES 
('sitealbum', 'sitealbum_quick', ''), 
('sitealbum_album_view', 'sitealbum_profile', 'album'),
('sitealbum_photo_view', 'sitealbum_photo_view', 'album_photo');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitealbum_photo_edit', 'sitealbum', 'Edit', 'Sitealbum_Plugin_Menus', '', 'sitealbum_photo_view', NULL, '0', '1', '1', '1'),
('sitealbum_photo_delete', 'sitealbum', 'Delete', 'Sitealbum_Plugin_Menus', '', 'sitealbum_photo_view', NULL, '0', '2', '1', '1'),
('sitealbum_photo_share', 'sitealbum', 'Share', 'Sitealbum_Plugin_Menus', '', 'sitealbum_photo_view', NULL, '0', '3', '1', '1'),
('sitealbum_photo_report', 'sitealbum', 'Report', 'Sitealbum_Plugin_Menus', '', 'sitealbum_photo_view', NULL, '0', '4', '1', '1'),
('sitealbum_photo_makeprofilephoto', 'sitealbum', 'Make Profile Photo', 'Sitealbum_Plugin_Menus', '', 'sitealbum_photo_view', NULL, '0', '5', '1', '1'),
('sitealbum_photo_location', 'sitealbum', 'Edit Location', 'Sitealbum_Plugin_Menus', '', 'sitealbum_photo_view', NULL, '0', '6', '1', '1');


INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES
('sitealbum_profile', 'standard', 'Album View Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitealbum_album_add', 'sitealbum', 'Add Photos', 'Sitealbum_Plugin_Menus::onMenuInitialize_SitealbumProfileAdd', '', 'sitealbum_profile', NULL, '0', '1', '1', '1'),
('sitealbum_album_edit', 'sitealbum', 'Edit', 'Sitealbum_Plugin_Menus::onMenuInitialize_SitealbumProfileEdit', '', 'sitealbum_profile', NULL, '0', '2', '1', '1'),
('sitealbum_album_delete', 'sitealbum', 'Delete', 'Sitealbum_Plugin_Menus::onMenuInitialize_SitealbumProfileDelete', '', 'sitealbum_profile', NULL, '0', '3', '1', '1'),
('sitealbum_album_location', 'sitealbum', 'Edit Location', 'Sitealbum_Plugin_Menus::onMenuInitialize_SitealbumProfileEditlocation', '', 'sitealbum_profile', NULL, '0', '4', '1', '1');