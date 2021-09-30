UPDATE `engine4_activity_actiontypes` SET `type` = 'sesblog_blog_like' WHERE `engine4_activity_actiontypes`.`type` = 'sesblog_like_blog';

UPDATE `engine4_activity_actions` SET `type` = 'sesblog_blog_like' WHERE `engine4_activity_actions`.`type` = 'sesblog_blog_like';

UPDATE `engine4_activity_actiontypes` SET `type` = 'sesblog_album_like' WHERE `engine4_activity_actiontypes`.`type` = 'sesblog_like_blogalbum';

UPDATE `engine4_activity_actions` SET `type` = 'sesblog_album_like' WHERE `engine4_activity_actions`.`type` = 'sesblog_like_blogalbum';

UPDATE `engine4_activity_actiontypes` SET `type` = 'sesblog_photo_like' WHERE `engine4_activity_actiontypes`.`type` = 'sesblog_like_blogphoto';

UPDATE `engine4_activity_actions` SET `type` = 'sesblog_photo_like' WHERE `engine4_activity_actions`.`type` = 'sesblog_like_blogphoto';

UPDATE `engine4_activity_actiontypes` SET `type` = 'sesblog_blog_favourite' WHERE `engine4_activity_actiontypes`.`type` = 'sesblog_favourite_blog';

UPDATE `engine4_activity_actions` SET `type` = 'sesblog_blog_favourite' WHERE `engine4_activity_actions`.`type` = 'sesblog_favourite_blog';