INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('sitepage_dashboard', 'standard', 'Page Dashboard Menu', '999');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitepage_dashboard_getstarted', 'sitepage', 'Get Started', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard","action":"get-started"}', 'sitepage_dashboard', '', 1, 0, 1),
('sitepage_dashboard_editinfo', 'sitepage', 'Edit Info', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_edit"}', 'sitepage_dashboard', '', 1, 0, 2),
('sitepage_dashboard_profilepicture', 'sitepage', 'Profile Picture', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"profile-picture"}', 'sitepage_dashboard', '', 1, 0, 3),
('sitepage_dashboard_overview', 'sitepage', 'Overview', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"overview"}', 'sitepage_dashboard', '', 1, 0, 4),
('sitepage_dashboard_contact', 'sitepage', 'Contact Details', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"contact"}', 'sitepage_dashboard', '', 1, 0, 5),
('sitepage_dashboard_managememberroles', 'sitepage', 'Manage Member Roles', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"manage-member-category"}', 'sitepage_dashboard', '', 1, 0, 6),
('sitepage_dashboard_announcements', 'sitepage', 'Manage Announcements', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"announcements"}', 'sitepage_dashboard', '', 1, 0, 7),
('sitepage_dashboard_alllocation', 'sitepage', 'Location', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"all-location"}', 'sitepage_dashboard', '', 1, 0, 8),
('sitepage_dashboard_editlocation', 'sitepage', 'Location', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"edit-location"}', 'sitepage_dashboard', '', 1, 0, 9),
('sitepage_dashboard_profiletype', 'sitepage', 'Profile Info', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"profile-type"}', 'sitepage_dashboard', '', 1, 0, 10),
('sitepage_dashboard_apps', 'sitepage', 'Apps', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"app"}', 'sitepage_dashboard', '', 1, 0, 11),
('sitepage_dashboard_marketing', 'sitepage', 'Marketing', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"marketing"}', 'sitepage_dashboard', '', 1, 0, 12),
('sitepage_dashboard_badge', 'sitepage', 'Badge', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepagebadge_request"}', 'sitepage_dashboard', '', 1, 0, 13),
('sitepage_dashboard_notificationsettings', 'sitepage', 'Manage Notifications', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"notification-settings"}', 'sitepage_dashboard', '', 1, 0, 14),
('sitepage_dashboard_insights', 'sitepage', 'Insights', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_insights"}', 'sitepage_dashboard', '', 1, 0, 15),
('sitepage_dashboard_reports', 'sitepage', 'Reports', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_reports"}', 'sitepage_dashboard', '', 1, 0, 16),
('sitepage_dashboard_manageadmins', 'sitepage', 'Manage Admins', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_manageadmins", "action":"index"}', 'sitepage_dashboard', '', 1, 0, 17),
('sitepage_dashboard_featuredowners', 'sitepage', 'Featured Admins', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"featured-owners"}', 'sitepage_dashboard', '', 1, 0, 18),
('sitepage_dashboard_editstyle', 'sitepage', 'Edit Style', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_dashboard", "action":"edit-style"}', 'sitepage_dashboard', '', 1, 0, 19),
('sitepage_dashboard_editlayout', 'sitepage', 'Edit Layout', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_layout"}', 'sitepage_dashboard', '', 1, 0, 20),
('sitepage_dashboard_updatepackages', 'sitepage', 'Packages', 'Sitepage_Plugin_Dashboardmenus', '{"route":"sitepage_packages", "action":"update-package"}', 'sitepage_dashboard', '', 1, 0, 21);
