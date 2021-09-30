

-- added projects tab in the dashboard tabs for donation type backing


INSERT IGNORE INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
(NULL, 'sitepage_dashboard_projects', 'sitepage', 'Projects', 'Sitepage_Plugin_Dashboardmenus', '{\"route\":\"sitepage_dashboard\", \"action\":\"choose-project\"}', 'sitepage_dashboard', '', '1', '0', '12');
