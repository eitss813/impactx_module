UPDATE `engine4_activity_actiontypes` SET `enabled` = '0' , `is_generated` = '1'  WHERE `engine4_activity_actiontypes`.`type` = 'Delete  	sitepage_admin_topic_create' LIMIT 1 ;


UPDATE `engine4_activity_actiontypes` SET `module` = 'sitepagediscussion' WHERE `engine4_activity_actiontypes`.`type` = 'sitepage_topic_create' LIMIT 1 ;

UPDATE `engine4_activity_actiontypes` SET `module` = 'sitepagediscussion' WHERE `engine4_activity_actiontypes`.`type` = 'sitepage_topic_reply' LIMIT 1 ;

UPDATE `engine4_activity_notificationtypes` SET `module` = 'sitepagediscussion' WHERE `engine4_activity_notificationtypes`.`type` = 'sitepage_discussion_reply' LIMIT 1 ;

UPDATE `engine4_activity_notificationtypes` SET `module` = 'sitepagediscussion' WHERE `engine4_activity_notificationtypes`.`type` = 'sitepage_discussion_response' LIMIT 1 ;