
ALTER TABLE `engine4_activity_stream` CHANGE `target_type` `target_type` VARCHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;

INSERT IGNORE INTO `engine4_activity_stream`
  SELECT
    'sitevideo_channel_subscriber' as `target_type`,
    target_id as `target_id`,
    subject_type as `subject_type`,
    subject_id as `subject_id`,
    object_type as `object_type`,
    object_id as `object_id`,
    type as `type`,
    action_id as `action_id`
  FROM `engine4_activity_stream` WHERE `object_type` = 'sitevideo_channel' group by `action_id`;
