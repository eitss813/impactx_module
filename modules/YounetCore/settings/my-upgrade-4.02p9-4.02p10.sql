UPDATE `engine4_core_modules` SET `version` = '4.02p10' where 'name' = 'younet-core';
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('younet_core_admin_main_settings', 'younet-core', 'Global Settings', '', '{"route":"admin_default","module":"younet-core","controller":"settings"}', 'younet_core_admin_main', '', 4);