/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitevideo', 'Advanced Videos', 'Advanced Videos / Channels / Playlists Plugin', '4.10.5p7', 1, 'extra') ;


ALTER TABLE `engine4_activity_stream` CHANGE `target_type` `target_type` VARCHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;


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
