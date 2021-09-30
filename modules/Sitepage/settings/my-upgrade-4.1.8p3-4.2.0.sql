UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$object}\n{body:$body}', `displayable` = '6',`is_object_thumb` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'sitepage_post_self' LIMIT 1 ;

INSERT IGNORE INTO `engine4_core_mailtemplates` (`mailtemplate_id`, `type`, `module`, `vars`) VALUES (NULL, 'SITEPAGE_CLAIMOWNER_EMAIL', 'sitepage', '[host],[email],[page_title],[object_link],[page_title_with_link]');
--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('sitepage_tagged', 'sitepage', '{item:$subject} has tagged your page in a {item:$object:$label}.', 0, '', 1);

UPDATE `engine4_activity_actiontypes` SET `body` = '{actors:$subject:$object}: {body:$body}' WHERE `engine4_activity_actiontypes`.`type` = 'sitepage_post';

