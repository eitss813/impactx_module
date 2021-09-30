DROP TABLE IF EXISTS `engine4_siteotpverifier_users`;
CREATE TABLE IF NOT EXISTS `engine4_siteotpverifier_users` ( 
	`user_id` INT(11) NOT NULL , 
	`phoneno` VARCHAR(11) NOT NULL ,
	`country_code` VARCHAR(6) NOT NULL , 
	`enable_verification` INT(1) NOT NULL , 
	PRIMARY KEY (`user_id`)) ENGINE = InnoDB;

INSERT INTO `engine4_siteotpverifier_users` 
SELECT `user_id`, `phoneno`, `country_code`, `enable_verification` FROM `engine4_users`;

ALTER TABLE `engine4_users` DROP `phoneno`;
ALTER TABLE `engine4_users` DROP `country_code`;
ALTER TABLE `engine4_users` DROP `enable_verification`;

UPDATE `engine4_authorization_permissions` SET `type` = 'siteotpverifier' WHERE `type` = 'Siteotpverifier_level';