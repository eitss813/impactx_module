
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
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
('sitepage_main', 'standard', 'Page Main Options Menu', '999'),
('sitepage_gutter', 'standard', 'Page Profile Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('core_main_page', 'sitepage', 'Pages',  'Sitepage_Plugin_Menus::canViewSitepages', '{"route":"sitepage_general", "action":"home"}', 'core_main', '', 36, 1, 1),
( 'sitepage_main_home', 'sitepage', 'Pages Home', 'Sitepage_Plugin_Menus::canViewSitepages', '{"route":"sitepage_general","action":"home"}', 'sitepage_main', '', '10', 1, 1),
( 'sitepage_main_browse', 'sitepage', 'Browse Pages', 'Sitepage_Plugin_Menus::canViewSitepages', '{"route":"sitepage_general","action":"index"}', 'sitepage_main', '', '20', 1, 1),
( 'sitepage_main_manage', 'sitepage', 'My Pages', 'Sitepage_Plugin_Menus::canCreateSitepages', '{"route":"sitepage_general","action":"manage"}', 'sitepage_main', '','140', 1, 1),
( 'sitepage_main_manageadmin', 'sitepage', 'Pages I Admin', 'Sitepage_Plugin_Menus::canCreateSitepages', '{"route":"sitepage_manageadmins","action":"my-pages"}', 'sitepage_main', '','150', 1, 1),
( 'sitepage_main_managelike', 'sitepage', 'Pages I Like', 'Sitepage_Plugin_Menus::canCreateSitepages', '{"route":"sitepage_like","action":"mylikes"}', 'sitepage_main', '','160', 1, 1),
('sitepage_gutter_share', 'sitepage', 'Share', 'Sitepage_Plugin_Menus', '', 'sitepage_gutter', '',5, 1, 1),
('sitepage_gutter_messageowner', 'sitepage', 'Message', 'Sitepage_Plugin_Menus', '', 'sitepage_gutter', '', 6,1, 1),
('sitepage_gutter_tfriend', 'sitepage', 'Tell a friend', 'Sitepage_Plugin_Menus', '', 'sitepage_gutter', '', 7, 1, 1),
('sitepage_gutter_claim', 'sitepage', 'Claim this Page', 'Sitepage_Plugin_Menus', '', 'sitepage_gutter', '', 13, 1, 1),
('sitepage_gutter_report', 'sitepage', 'Report', 'Sitepage_Plugin_Menus', '', 'sitepage_gutter', '', 16, 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitepage_index_home', 'Sitepage_Form_Search', 'search', '{"type":"sitepage_page"}', '', '{"route":"sitepage_general","action":"index"}'),
('sitepage_index_index', 'Sitepage_Form_Search', 'search', '{"type":"sitepage_page"}', '', ''),
('sitepage_index_manage', 'Sitepage_Form_ManageSearch', 'search', '{"type":"sitepage_page"}', '', '');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitepage_index_view', 'sitepage_gutter', 'sitepage_page');