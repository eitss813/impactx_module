--
-- Updating data for table `engine4_core_menuitems`
--
UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"sitealbum_general","action":"browse","icon":"fa-image"}'
WHERE `name` = 'core_main_sitealbum';

--
-- Change the Commentable & Shareable values
--
UPDATE engine4_activity_actiontypes SET commentable=3,shareable=3 WHERE (type='comment_album' or type = 'comment_album_photo') and module='sitealbum';