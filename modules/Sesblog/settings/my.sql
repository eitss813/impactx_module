/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: my.sql 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('sesblogpackage', 'SNS - Advanced Blogs - Packages for Allowing Blog Creation Extension', 'SNS - Advanced Blogs - Packages for Allowing Blog Creation Extension', '5.3.3', 1, 'extra');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("core_admin_main_plugins_sesblog", "sesblog", "SNS - Advanced Blog", "", '{"route":"admin_default","module":"sesblog","controller":"settings"}', "core_admin_main_plugins", "", 999),
("sesblog_admin_main_settings", "sesblog", "Global Settings", "", '{"route":"admin_default","module":"sesblog","controller":"settings"}', "sesblog_admin_main", "", 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("notify_sesblog_subscribed_new", "sesblog", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]"),
("sesblog_blog_owner_claim", "sesblog", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_name],[sender_email],[sender_link],[sender_photo],[message]"),
("sesblog_site_owner_for_claim", "sesblog", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_name],[sender_email],[sender_link],[sender_photo],[message]"),
("sesblog_blog_owner_approve", "sesblog", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_name],[sender_email],[sender_link],[sender_photo],[message]"),
("sesblog_claim_owner_approve", "sesblog", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_name],[sender_email],[sender_link],[sender_photo],[message]"),
("sesblog_claim_owner_request_cancel", "sesblog", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_name],[sender_email],[sender_link],[sender_photo],[message]");

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sesblog_main_import', 'sesblog', 'Import Blogs', 'Sesblog_Plugin_Menus::canCreateSesblogs', '{"route":"sesblog_import"}', 'sesblog_main', '', 1, 0, 999),
('sesblog_main_rss', 'sesblog', 'RSS Feed', 'Sesblog_Plugin_Menus::canViewRssblogs', '{"route":"sesblog_general", "action":"rss-feed"}', 'sesblog_main', '', 1, 0, 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sesblog_admin_main_manageimport", "sesblog", "Import CSV File", "", '{"route":"admin_default","module":"sesblog","controller":"manage-imports", "action":"index"}', "sesblog_admin_main", "", 999);
