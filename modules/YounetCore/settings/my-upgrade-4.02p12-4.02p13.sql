UPDATE `engine4_core_modules` SET `version` = '4.02p13' where 'name' = 'younet-core';

UPDATE `engine4_core_menuitems` SET `label` = 'YouNetCo Core' WHERE `engine4_core_menuitems`.`name` = 'core_admin_plugins_younet_core';
UPDATE `engine4_core_menuitems` SET `label` = 'YouNetCo Plugins' WHERE `engine4_core_menuitems`.`name` = 'younet_core_admin_main_younet';