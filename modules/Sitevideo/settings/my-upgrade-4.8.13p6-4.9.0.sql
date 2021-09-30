-- Updating type of columns
--
ALTER TABLE `engine4_video_videos` CHANGE `type` `type` VARCHAR( 32 ) NOT NULL;
ALTER TABLE `engine4_video_videos` CHANGE `code` `code` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;

--
-- Update `type` column in `engine4_video_videos` table
--
UPDATE `engine4_video_videos` SET `type` = 'upload' WHERE `engine4_video_videos`.`type` = '3';
UPDATE `engine4_video_videos` SET `type` = 'youtube' WHERE `engine4_video_videos`.`type` = '1';
UPDATE `engine4_video_videos` SET `type` = 'vimeo' WHERE `engine4_video_videos`.`type` = '2';
UPDATE `engine4_video_videos` SET `type` = 'dailymotion' WHERE `engine4_video_videos`.`type` = '4';
UPDATE `engine4_video_videos` SET `type` = 'embedcode' WHERE `engine4_video_videos`.`type` = '5';
UPDATE `engine4_video_videos` SET `type` = 'instagram' WHERE `engine4_video_videos`.`type` = '6';
UPDATE `engine4_video_videos` SET `type` = 'twitter' WHERE `engine4_video_videos`.`type` = '7';
UPDATE `engine4_video_videos` SET `type` = 'pinterest' WHERE `engine4_video_videos`.`type` = '8';

--
-- Change the Commentable & Shareable values
--
UPDATE engine4_activity_actiontypes SET commentable=3,shareable=3 WHERE (type='comment_channel' or type = 'comment_sitevideo_video') and module='sitevideo';

--
-- Updating data for table `engine4_core_menuitems`
--
UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"sitevideo_video_general","action":"index","icon":"fa-video-camera"}'
WHERE `name` = 'core_main_sitevideo';