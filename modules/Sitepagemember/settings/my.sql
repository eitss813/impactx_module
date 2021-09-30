INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitepagemember', 'Directory / Pages - Page Members', 'Directory / Pages - Page Members', '4.10.3p4', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitepage_gutter_manage_joined_members', 'sitepagemember', 'Manage Joined Pages', 'Sitepagemember_Plugin_Menus', '', 'user_home', '', 0, 0, 999);