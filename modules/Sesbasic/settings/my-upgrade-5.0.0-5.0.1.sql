ALTER TABLE `engine4_sesbasic_locations` CHANGE `lat` `lat` VARCHAR(128) NULL DEFAULT NULL;
ALTER TABLE `engine4_sesbasic_locations` CHANGE `lng` `lng` VARCHAR(128) NULL DEFAULT NULL;
UPDATE `engine4_core_menuitems` SET `label` = 'SNS - Basic Required' WHERE `engine4_core_menuitems`.`name` = 'core_admin_plugins_sesbasic';