INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('sesblogpackage', 'SNS - Advanced Blogs - Packages for Allowing Blog Creation Extension', 'SNS - Advanced Blogs - Packages for Allowing Blog Creation Extension', '5.3.3', 1, 'extra');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ("sesblog_admin_main_extension", "sesblog", "Extensions", "", '{"route":"admin_default","module":"sesblog","controller":"settings", "action": "extensions"}', "sesblog_admin_main", "", 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sesblog_admin_packagesetting", "sesblogpackage", "Package Settings", "", '{"route":"admin_default","module":"sesblogpackage","controller":"package","action":"settings"}', "sesblog_admin_main", "", 2),
("sesblog_admin_subpackagesetting", "sesblogpackage", "Package Settings", "", '{"route":"admin_default","module":"sesblogpackage","controller":"package","action":"settings"}', "sesblog_admin_packagesetting", "", 1),
("sesblog_main_manage_package", "sesblog", "My Packages", "Sesblog_Plugin_Menus", '{"route":"sesblog_general","action":"package"}', "sesblog_main", "", 7);
