
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

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitepage_main_member', 'sitepagemember', 'Members', 'Sitepagemember_Plugin_Menus::canViewMembers', '{"route":"sitepagemember_browse","action":"browse"}', 'sitepage_main', '','40', '1', '1');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitepage_main_managejoin', 'sitepage', 'Pages I Joined', 'Sitepage_Plugin_Menus::canCreateSitepages', '{"route":"sitepage_like","action":"my-joined"}', 'sitepage_main', '','170', 1, 1);

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitepagemember_index_browse', 'Sitepagemember_Form_Searchwidget', 'search_member', '', '', '');