UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$object} posted a new discussion topic:', `displayable` = '6', `is_object_thumb` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'sitepage_admin_topic_create' LIMIT 1 ;

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES ('sitepage_admin_topic_reply', 'sitepagediscussion', '{item:$object} replied to a discussion in the page:', '0', '6', '2', '1', '1', '1', '1');

UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$subject} replied to a discussion in the page {item:$object}:' WHERE `engine4_activity_actiontypes`.`type` = 'sitepage_topic_reply' LIMIT 1;

UPDATE `engine4_activity_actiontypes` SET `is_generated` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'sitepage_topic_create' LIMIT 1;

