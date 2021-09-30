DROP TABLE IF EXISTS `engine4_sesnewsletter_emails`;
CREATE TABLE `engine4_sesnewsletter_emails` (
  `email_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_address` varchar(124) DEFAULT NULL,
  `from_name` varchar(124) DEFAULT NULL,
  `subject` varchar(124) DEFAULT NULL,
  `body` text NULL,
  `email` varchar(124) DEFAULT NULL,
  `template_id` int(11) unsigned NOT NULL DEFAULT "0",
  `stop` tinyint(1) NOT NULL DEFAULT "1",
PRIMARY KEY (`email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sesnewsletter_admin_message_mail", "sesnewsletter",  "Email All Members", "",  '{"route":"admin_default","module":"sesnewsletter","controller":"message","action":"mail"}',  "sesnewsletter_admin_main", "",  990);

INSERT IGNORE INTO `engine4_core_tasks` (`title`,  `module`, `plugin`,`timeout`, `processes`) VALUES ("SES - Newsletter Email Send to Members",  "sesnewsletter", "Sesnewsletter_Plugin_Task_Newsletteremail",  120, 1);

ALTER TABLE `engine4_sesnewsletter_campaigns` ADD `choose_member` INT(11) NOT NULL DEFAULT "0";
ALTER TABLE `engine4_sesnewsletter_campaigns` ADD `member_levels` VARCHAR(255) NULL AFTER `choose_member`, ADD `networks` VARCHAR(255) NULL AFTER `member_levels`, ADD `profile_types` VARCHAR(255) NULL AFTER `networks`;
