INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('sitepage_profile_photo_update', 'sitepage', '{item:$subject} changed their Page profile photo.', 1, 3, 2, 1, 1, 1);

DROP TABLE IF EXISTS `engine4_sitepage_vieweds`;
