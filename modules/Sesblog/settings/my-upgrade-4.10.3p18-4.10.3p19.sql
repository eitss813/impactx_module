INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sesblog_blogadmin', 'sesblog', 'You are now a admin in this blog {item:$object}.', 0, ''),
('sesblog_acceptadminre', 'sesblog', '{item:$subject} accepted as admin request for blog {item:$object}.', 0, ''),
('sesblog_rejestadminre', 'sesblog', '{item:$subject} rejected as admin request for blog {item:$object}.', 0, ''),
('sesblog_removeadminre', 'sesblog', '{item:$subject} removed as admin request for blog {item:$object}.', 0, '');

ALTER TABLE `engine4_sesblog_roles` ADD `resource_approved` TINYINT(1) NOT NULL DEFAULT "1";

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sesblog_profile_member', 'sesblog', 'Member', 'Sesblog_Plugin_Menus', '', 'sesblog_gutter', '', 3);
