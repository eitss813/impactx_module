
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

INSERT IGNORE INTO `engine4_sitemobile_menus` (`id`, `name`, `type`, `title`, `order`) VALUES (NULL, 'sitepagevideo_profile', 'standard', 'Page Video Profile Options Menu', '999');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('sitepage_main_video', 'sitepagevideo', 'Videos', 'Sitepagevideo_Plugin_Menus::canViewVideos', '{"route":"sitepagevideo_browse","action":"browse"}', 'sitepage_main', '','0','70', '1', '1'),
('sitepagevideo_add', 'sitepagevideo', 'Add Video', 'Sitepagevideo_Plugin_Menus', '', 'sitepagevideo_profile', NULL, '0', '1', '1', '1'),
('sitepagevideo_edit', 'sitepagevideo', 'Edit Video', 'Sitepagevideo_Plugin_Menus', '', 'sitepagevideo_profile', NULL, '0', '2', '1', '1'),
('sitepagevideo_delete', 'sitepagevideo', 'Delete Video', 'Sitepagevideo_Plugin_Menus', '', 'sitepagevideo_profile', NULL, '0', '3', '1', '1');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitepagevideo_index_browse', 'Sitepagevideo_Form_Searchwidget', 'search_video', '', '', '');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitepagevideo_index_view', 'sitepagevideo_profile', 'sitepagevideo_video');