INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sesblog_main_import', 'sesblog', 'Import Blogs', 'Sesblog_Plugin_Menus::canCreateSesblogs', '{"route":"sesblog_import"}', 'sesblog_main', '', 1, 0, 999),
('sesblog_main_rss', 'sesblog', '<img title="RSS" src="./application/modules/Sesblog/externals/images/rss.png"> RSS Feed', 'Sesblog_Plugin_Menus::canViewRssblogs', '{"route":"sesblog_general", "action":"rss-feed"}', 'sesblog_main', '', 1, 0, 999);

DELETE FROM `engine4_activity_notificationtypes` WHERE `engine4_activity_notificationtypes`.`type` = 'sesblog_link_blog';
DELETE FROM `engine4_activity_notificationtypes` WHERE `engine4_activity_notificationtypes`.`type` = 'sesblog_link_event';
DELETE FROM `engine4_activity_notificationtypes` WHERE `engine4_activity_notificationtypes`.`type` = 'sesblog_reject_blog_request';
DELETE FROM `engine4_activity_notificationtypes` WHERE `engine4_activity_notificationtypes`.`type` = 'sesblog_reject_event_request';
DELETE FROM `engine4_activity_notificationtypes` WHERE `engine4_activity_notificationtypes`.`type` = 'sesuser_claimadmin_blog';