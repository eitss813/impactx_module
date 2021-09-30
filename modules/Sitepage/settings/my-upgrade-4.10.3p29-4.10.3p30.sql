ALTER TABLE `engine4_sitepage_pages` 
ADD `show_name`  TINYINT(1) NOT NULL DEFAULT '1' AFTER `fbpage_url`,
ADD `join_enable`  TINYINT(1) NOT NULL DEFAULT '1' AFTER `show_name`,
ADD `days` TINYINT(1) NOT NULL DEFAULT '1' AFTER `join_enable`,
ADD `layout_id` INT NOT NULL AFTER `days`;

ALTER TABLE `engine4_sitepage_packages` 
ADD `service` TINYINT(1) NOT NULL DEFAULT '1' AFTER `ads`,
ADD `layout_id` INT NOT NULL AFTER `service`;

CREATE TABLE IF NOT EXISTS `engine4_sitepage_services` (
	`service_id` INT NOT NULL AUTO_INCREMENT ,
	`page_id` INT NOT NULL ,
	`title` VARCHAR(50) NOT NULL ,
	`body` VARCHAR(150) NOT NULL ,
	`photo_id` VARCHAR(300) NOT NULL ,
	`duration` INT NOT NULL ,
	`duration_type` ENUM('hours','minutes','days') NOT NULL,
	PRIMARY KEY (`service_id`)) ENGINE = InnoDB;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('sitepage_dashboard_services', 'sitepage', 'Services', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_services","action":"services"}', 'sitepage_dashboard_content', '', 1, 0, 22),
('sitepage_dashboard_linkpages', 'sitepage', 'Linked Pages', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_linkpages","action":"linkpages"}', 'sitepage_dashboard_promotion', '', 1, 0, 23),
('sitepage_dashboard_timing', 'sitepage', 'Operating Hours', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_timing","action":"timing"}', 'sitepage_dashboard_content', '', 1, 0, 24);

CREATE TABLE IF NOT EXISTS `engine4_sitepage_timings` (
	`timing_id` INT NOT NULL AUTO_INCREMENT ,
	`page_id` INT NOT NULL ,
	`day` ENUM('sunday','monday','tuesday','wednesday','thursday','friday','saturday') NULL DEFAULT NULL,
	`start` TIME NOT NULL ,
	`end` TIME NOT NULL ,
	PRIMARY KEY (`timing_id`)) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `engine4_sitepage_buttons` (
	`button_id` INT NOT NULL AUTO_INCREMENT ,
	`page_id` INT NOT NULL ,
	`label` VARCHAR(65) NOT NULL ,
	`url` VARCHAR(2083) NOT NULL ,
	PRIMARY KEY (`button_id`)) ENGINE = InnoDB;

INSERT IGNORE INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES (NULL, 'sitepage_admin_main_predefinedlayout', 'sitepage', 'Pre-defined Layouts', '', '{"route":"admin_default","module":"sitepage","controller":"predefinedlayout"}', 'sitepage_admin_main', '', '1', '0', '26');

ALTER TABLE `engine4_sitepage_categories` ADD `layout_id` INT NOT NULL AFTER `file_id`;

CREATE TABLE IF NOT EXISTS `engine4_sitepage_definedlayouts` (
	`definedlayout_id` INT NOT NULL AUTO_INCREMENT ,
	`page_id` INT NOT NULL ,
	`title` VARCHAR(180) NOT NULL ,
	`photo_id` VARCHAR(250) NOT NULL ,
	`status` TINYINT(1) NOT NULL ,
	`style` TEXT NOT NULL ,
	PRIMARY KEY (`definedlayout_id`)) ENGINE = InnoDB;

UPDATE `engine4_core_menuitems` SET `menu` = 'sitepage_dashboard_content' WHERE `name` in("sitepage_dashboard_getstarted","sitepage_dashboard_editinfo","sitepage_dashboard_profilepicture","sitepage_dashboard_profiletype","sitepage_dashboard_editlocation","sitepage_dashboard_alllocation","sitepage_dashboard_editstyle","sitepage_dashboard_editlayout","sitepage_dashboard_updatepackages","sitepage_dashboard_badge");
UPDATE `engine4_core_menuitems` SET `menu` = 'sitepage_dashboard_admin' WHERE `name` in("sitepage_dashboard_contact","sitepage_dashboard_managememberroles","sitepage_dashboard_apps","sitepage_dashboard_insights","sitepage_dashboard_reports","sitepage_dashboard_manageadmins","sitepage_dashboard_featuredowners","sitepage_dashboard_notificationsettings");
UPDATE `engine4_core_menuitems` SET `menu` = 'sitepage_dashboard_promotion' WHERE `name` in("sitepage_dashboard_announcements","sitepage_dashboard_overview","sitepage_dashboard_marketing");

UPDATE `engine4_sitepage_categories` SET `layout_id` = 1 WHERE `category_name` in ("Automobile", "Jobs", "Movies - TV");
UPDATE `engine4_sitepage_categories` SET `layout_id` = 2 WHERE `category_name` in ("Fashion", "Real Estate", "Sports");
UPDATE `engine4_sitepage_categories` SET `layout_id` = 3 WHERE `category_name` in ("Travel", "Electronics", "Places");
