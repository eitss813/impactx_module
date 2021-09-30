UPDATE  `engine4_core_settings` SET  `value` =  '7' WHERE  `engine4_core_settings`.`name` =  'sitepageshow.navigation.tabs';


INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES
('sitepage', 'street', 1, 7, 'Street'),
('sitepage', 'city', 1, 8, 'City'),
('sitepage', 'state', 1, 9, 'State'),
('Sitepage', 'country', 1, 10, 'Country');

UPDATE `engine4_seaocore_searchformsetting` SET `order` = '11' WHERE `engine4_seaocore_searchformsetting`.`name` = 'locationmiles' LIMIT 1 ;

UPDATE `engine4_seaocore_searchformsetting` SET `order` = '12' WHERE `engine4_seaocore_searchformsetting`.`name` = 'price' LIMIT 1 ;

UPDATE `engine4_seaocore_searchformsetting` SET `order` = '13' WHERE `engine4_seaocore_searchformsetting`.`name` = 'profile_type' LIMIT 1 ;

UPDATE `engine4_seaocore_searchformsetting` SET `order` = '14' WHERE `engine4_seaocore_searchformsetting`.`name` = 'category_id' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = '4' WHERE `engine4_core_menuitems`.`name` = 'sitepage_main_manage' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = '5' WHERE `engine4_core_menuitems`.`name` = 'sitepage_main_create' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `order` = '6' WHERE `engine4_core_menuitems`.`name` = 'sitepage_main_create' LIMIT 1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitepage_main_location', 'sitepage', 'Browse Locations', 'Sitepage_Plugin_Menus::canViewSitepages', '{"route":"sitepage_general","action":"map"}', 'sitepage_main', '', 1, 0, 3);