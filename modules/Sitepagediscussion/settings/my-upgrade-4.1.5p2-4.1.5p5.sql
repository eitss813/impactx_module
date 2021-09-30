INSERT  IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('sitepage_admin_topic_create', 'sitepagediscussion', '{item:$subject} posted a new discussion topic:', 1, 1, 2, 1, 1, 0);

DELETE FROM `engine4_activity_actiontypes` WHERE `engine4_activity_actiontypes`.`type` = 'sitepage_topic_create' ;

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('sitepage_topic_create', 'sitepage', '{item:$subject} posted a new discussion topic in the page {item:$object}:', 1, 3, 2, 1, 1, 0);