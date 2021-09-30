UPDATE `engine4_activity_actiontypes` SET `type` = 'comment_sitepage_page' , `displayable` = '1',
`attachable` = '1',`is_generated` = '0' WHERE `engine4_activity_actiontypes`.`type` = 'comment_sitepage' LIMIT 1 ;
