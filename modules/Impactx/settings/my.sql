INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('impactx', 'Impactx Customization', 'Impactx Customization', '5.0.0', 1, 'extra') ;

ALTER TABLE `engine4_yndynamicform_entries` ADD `publish` TINYINT( 4 ) NOT NULL DEFAULT '1';


DROP TABLE IF EXISTS `engine4_impactx_formmappings`;
CREATE TABLE IF NOT EXISTS `engine4_impactx_formmappings` (
  `formmapping_id` int(11) unsigned NOT NULL auto_increment,
  `role_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  PRIMARY KEY  (`formmapping_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;