INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEPAGE_POSTNOTIFICATION_EMAIL", "sitepage", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepage_notificationpost', 'sitepage', '{item:$subject} posted in {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepage_activitycomment', 'sitepage', '{item:$subject} has commented on {var:$eventname}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepage_activitylike', 'sitepage', '{item:$subject} has liked {var:$eventname}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepage_contentlike', 'sitepage', '{item:$subject} has liked {var:$eventname}.', 0, '');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('sitepage_contentcomment', 'sitepage', '{item:$subject} has commented on {var:$eventname}.', 0, '');

ALTER TABLE `engine4_sitepage_albums` CHANGE `type` `type` ENUM( 'note', 'overview','wall', 'announcements', 'discussions','cover' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL; 

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('sitepage_cover_update', 'sitepage', '{item:$subject} updated cover photo of the page {item:$object}:', 1, 3, 2, 1, 1, 1);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`,`is_object_thumb`) VALUES
('sitepage_admin_cover_update', 'sitepage', '{item:$object} updated a new cover photo.', 1, 3, 2, 1, 1, 1,1);


