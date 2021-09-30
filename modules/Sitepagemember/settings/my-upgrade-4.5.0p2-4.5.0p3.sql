INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitepage_gutter_manage_joined_members', 'sitepagemember', 'Manage Joined Pages', 'Sitepagemember_Plugin_Menus', '', 'user_home', '', 0, 0, 999);


UPDATE `engine4_core_menuitems` SET `label` = 'SEAO - Directory / Pages - Page Members Extension' WHERE `engine4_core_menuitems`.`name` ='core_admin_main_plugins_sitepagemember' LIMIT 1 ;

UPDATE `engine4_core_modules` SET `title` = 'Directory / Pages - Page Members Extension',
`description` = 'Directory / Pages - Page Members Extension' WHERE `engine4_core_modules`.`name` = 'sitepagemember' LIMIT 1 ;