CREATE TABLE IF NOT EXISTS `engine4_sitepage_verifies` (
	`verify_id` int(11) unsigned NOT NULL auto_increment,
	`resource_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`resource_id` int(11) unsigned NOT NULL,
	`poster_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`poster_id` int(11) unsigned NOT NULL,`status` int(11) unsigned NOT NULL DEFAULT '1',
	`comments` text COLLATE utf8_unicode_ci,`admin_approve` int(11) unsigned NOT NULL DEFAULT '1',
	`creation_date` datetime NOT NULL,PRIMARY KEY  (`verify_id`),
	KEY `resource_type` (`resource_type`, `resource_id`),
	KEY `poster_type` (`poster_type`, `poster_id`)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES 
('sitepage_verify_new', 'sitepage', 'Your Page {item:$object} have been verified by {item:$subject}.', 0, '', 1), 
('sitepage_verify_admin_approve', 'sitepage', 'Site administrator has approved your verification to {item:$subject}.', 0, '', 1), 
('sitepage_verify_user_request', 'sitepage', '{item:$subject} has requested you to approve verification request.', 0, '', 1);


INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('sitepage_admin_main_verify', 'sitepage', 'Manage Verifications', '', '{"route":"admin_default","module":"sitepage","controller":"verify","action":"index"}', 'sitepage_admin_main', '', 1, 0, 100), 
('sitepage_admin_main_verify_settings', 'sitepage', 'General Settings', '', '{"route":"admin_default","module":"sitepage","controller":"verify","action":"index"}', 'sitepage_admin_main_verify', '', 1, 0, 1), 
('sitepage_admin_main_verify_manage', 'sitepage', 'Manage Verifications', '', '{"route":"admin_default","module":"sitepage","controller":"verify","action":"manage"}', 'sitepage_admin_main_verify', '', 1, 0, 2), 
('sitepage_admin_main_verify_approve', 'sitepage', 'Approve Verifications', '', '{"route":"admin_default","module":"sitepage","controller":"verify","action":"approve"}', 'sitepage_admin_main_verify', '', 1, 0, 3);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
('sitepage_verify_new','sitepage','{item:$object} is verified by {item:$subject}.',1,5,1,1,1,1);