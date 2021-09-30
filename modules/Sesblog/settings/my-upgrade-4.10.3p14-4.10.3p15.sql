ALTER TABLE `engine4_sesblog_blogs` ADD `cotinuereading` TINYINT(1) NOT NULL DEFAULT '0';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
  level_id as `level_id`,
  "sesblog_blog" as `type`,
  "cotinuereading" as `name`,
  1 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin");

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
  level_id as `level_id`,
  "sesblog_blog" as `type`,
  "cotinuereading" as `name`,
  1 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN("user");

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
  level_id as `level_id`,
  "sesblog_blog" as `type`,
  "cntrdng_dflt" as `name`,
  1 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin");

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
  level_id as `level_id`,
  "sesblog_blog" as `type`,
  "cntrdng_dflt" as `name`,
  1 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN("user");
