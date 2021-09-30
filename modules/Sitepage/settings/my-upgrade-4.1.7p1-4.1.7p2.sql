INSERT IGNORE INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES (NULL, 'core_admin_main_plugins_sitepageextensions', 'sitepage', 'SEAO - Directory/Pages - Extensions', NULL, '{"route":"admin_default","module":"sitepage","controller":"extension", "action": "index"}', 'core_admin_main_plugins', NULL, '1', '0', '999');

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"admin_default","module":"sitepage","controller":"extension","action":"index"}' WHERE `name` = 'sitepage_admin_extension';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'sitepage_admin_main_activity_feed', 'sitepage', 'Activity Feed', '', '{"route":"admin_default","module":"sitepage","controller":"settings","action":"activity-feed"}', 'sitepage_admin_main', '', 1, 0, 998);

INSERT IGNORE INTO  `engine4_core_settings` (`name`, `value`) VALUES ('sitepage.categorywithslug', '1');

UPDATE `engine4_sitepage_content` SET `name` = 'seaocore.feed' WHERE `name` = 'activity.feed';
UPDATE `engine4_sitepage_admincontent` SET `name` = 'seaocore.feed' WHERE `name` = 'activity.feed';


UPDATE `engine4_core_content` SET `engine4_core_content`.`name` = 'seaocore.feed' WHERE `engine4_core_content`.`name` = 'activity.feed' and
`engine4_core_content`.`page_id`=(SELECT  `engine4_core_pages`.`page_id`
FROM `engine4_core_pages`
WHERE  (`engine4_core_pages`.`name` = 'sitepage_index_view'   and   `engine4_core_pages`.`page_id`= `engine4_core_content`.`page_id`));

UPDATE `engine4_core_content` SET `engine4_core_content`.`name` = 'seaocore.feed' WHERE `engine4_core_content`.`name` = 'activity.feed' and
`engine4_core_content`.`page_id`=(SELECT  `engine4_core_pages`.`page_id`
FROM `engine4_core_pages`
WHERE  (`engine4_core_pages`.`name` = 'sitepage_mobi_view'   and   `engine4_core_pages`.`page_id`= `engine4_core_content`.`page_id`));