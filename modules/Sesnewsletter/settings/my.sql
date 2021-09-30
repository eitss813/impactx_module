/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: my.sql  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_sesnewsletter', 'sesnewsletter', 'SES - Newsletter / Email Marketing', '', '{"route":"admin_default","module":"sesnewsletter","controller":"settings"}', 'core_admin_main_plugins', '', 999),
('sesnewsletter_admin_main_settings', 'sesnewsletter', 'Global Settings', '', '{"route":"admin_default","module":"sesnewsletter","controller":"settings"}', 'sesnewsletter_admin_main', '', 1);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `enabled`, `custom`, `order`) VALUES
("sesnewsletter_header_invite", "invite", "Invite", "Invite_Plugin_Menus::canInvite", '{"route":"default","module":"invite","icon":"fa-envelope"}', "sesnewsletter_header", 1, 0, 2);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sesnewsletter_settings_newsletter", "sesnewsletter", "Subscribed Newsletters", "Sesnewsletter_Plugin_Menus::canEnabled", '{"route":"sesnewsletter_extended", "module":"sesnewsletter","controller":"settings","action":"newsletter-settings"}', "user_settings", "", "1", "0", "999");
