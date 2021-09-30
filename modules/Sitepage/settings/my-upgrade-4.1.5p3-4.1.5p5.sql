INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `order`) VALUES
('sitepage_gutter_favourite', 'sitepage', 'Link to your Page', 'Sitepage_Plugin_Menus', '{"route":"sitepage_dashboard","class":"buttonlink smoothbox", "action": "favourite"}', 'sitepage_gutter', '', 1, 999),
('sitepage_gutter_favouritedelete', 'sitepage', 'Unlink from your Page', 'Sitepage_Plugin_Menus',
 '{"route":"sitepage_dashboard","class":"buttonlink icon_sitepage_delete smoothbox", "action": "favourite-delete"}', 'sitepage_gutter', '', 1, 999);



DROP TABLE IF EXISTS `engine4_sitepage_favourites`;
CREATE TABLE IF NOT EXISTS `engine4_sitepage_favourites` (
  `favourite_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `page_id_for` int(11) NOT NULL,
  PRIMARY KEY (`favourite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


INSERT IGNORE INTO `engine4_core_settings` (`name` ,`value`)VALUES ('sitepage.addfavourite.show', '1');




-- ADMIN, MODERATOR
--  style
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitepage_page' as `type`,
    'style' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
--  style
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitepage_page' as `type`,
    'style' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');


UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepage_general","action":"home"}'  WHERE `engine4_core_menuitems`.`name` ='core_main_sitepage' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepage_general","action":"home"}' WHERE `engine4_core_menuitems`.`name` ='core_sitemap_sitepage' LIMIT 1 ;

ALTER TABLE `engine4_sitepage_packages` ADD `update_list` BOOL NOT NULL DEFAULT '1';

ALTER TABLE `engine4_sitepage_pages` ADD `all_post` BOOL NOT NULL DEFAULT '1' AFTER `status`;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitepage_imports`
--

DROP TABLE IF EXISTS `engine4_sitepage_imports`;
CREATE TABLE IF NOT EXISTS `engine4_sitepage_imports` (
  `import_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`page_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`category` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
	`sub_category` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
	`price` int(11) NOT NULL DEFAULT '0',
  `location` text COLLATE utf8_unicode_ci,
  `overview` text COLLATE utf8_unicode_ci,
	`tags` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`userclaim` TINYINT( 1 ) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`import_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


-- --------------------------------------------------------


--
-- Table structure for table `engine4_sitepage_importfiles`
--

DROP TABLE IF EXISTS `engine4_sitepage_importfiles`;
CREATE TABLE IF NOT EXISTS `engine4_sitepage_importfiles` (
  `importfile_id` int(11) unsigned NOT NULL auto_increment,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`status` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
	`first_import_id` int(11) unsigned NOT NULL,
  `last_import_id` int(11) unsigned NOT NULL,
	`current_import_id` int(11) unsigned NOT NULL,
  `first_page_id` int(11) unsigned NOT NULL,
  `last_page_id` int(11) unsigned NOT NULL,
	`creation_date` datetime NOT NULL,
	`view_privacy` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `comment_privacy` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`importfile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


-- --------------------------------------------------------
ALTER TABLE `engine4_sitepage_albums` CHANGE `type` `type` ENUM( 'note', 'overview' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

-- --------------------------------------------------------
UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepage_dashboard","action":"overview","class":"buttonlink icon_sitepages_edit"}' WHERE `engine4_core_menuitems`.`name` ='sitepage_gutter_editoverview' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepage_dashboard","action":"edit-style","class":"buttonlink icon_sitepages_edit"}' WHERE `engine4_core_menuitems`.`name` ='sitepage_gutter_editstyle' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepage_claimpages"}' WHERE `engine4_core_menuitems`.`name` ='sitepage_main_claim' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepage_packages","class":"buttonlink icon_sitepage_new"}' WHERE `engine4_core_menuitems`.`name` ='sitepage_quick_create' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"sitepage_packages"}' WHERE `engine4_core_menuitems`.`name` ='sitepage_main_create' LIMIT 1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `order`) VALUES
('sitepage_admin_main_import', 'sitepage', 'Import', '', '{"route":"admin_default","module":"sitepage","controller":"importlisting"}', 'sitepage_admin_main', '', 1, 15);
-- --------------------------------------------------------
ALTER TABLE `engine4_sitepage_photos` ADD `view_count` INT( 11 ) NOT NULL DEFAULT '0';
-- --------------------------------------------------------
