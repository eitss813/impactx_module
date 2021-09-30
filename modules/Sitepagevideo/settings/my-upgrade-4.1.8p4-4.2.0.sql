INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepagevideo_processed', 'sitepagevideo', 'Your {item:$object:page video} is ready to be viewed.', 0, ''),
('sitepagevideo_processed_failed', 'sitepagevideo', 'Your {item:$object:page video} has failed to process.', 0, '');

UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$object} posted a new video:', `displayable` = '6', `is_object_thumb` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'sitepagevideo_admin_new' LIMIT 1 ;

INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `order` )VALUES
 ('sitepage_main_video', 'sitepagevideo', 'Videos', 'Sitepagevideo_Plugin_Menus::canViewVideos', '{"route":"sitepagevideo_videolist","action":"videolist"}', 'sitepage_main', '', 1, '20');

