INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitepage_topic_view', 'sitepage_topic', 'sitepage_topic');


INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('Sitepagediscussion_topic_watch', 'sitepagediscussion', 'Watch Topic', 'Sitepagediscussion_Plugin_Menus', '', 'sitepage_topic', NULL, '0', '1', '1', '1'), 
('Sitepagediscussion_topic_sticky', 'sitepagediscussion', 'Make Sticky', 'Sitepagediscussion_Plugin_Menus', '', 'sitepage_topic', NULL, '0', '2', '1', '1'),
('Sitepagediscussion_topic_open', 'sitepagediscussion', 'Open', 'Sitepagediscussion_Plugin_Menus', '', 'sitepage_topic', NULL, '0', '3', '1', '1'),
('Sitepagediscussion_topic_rename', 'sitepagediscussion', 'Rename', 'Sitepagediscussion_Plugin_Menus', '', 'sitepage_topic', NULL, '0', '4', '1', '1'),
('Sitepagediscussion_topic_delete', 'sitepagediscussion', 'Delete', 'Sitepagediscussion_Plugin_Menus', '', 'sitepage_topic', NULL, '0', '5', '1', '1');

INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
('sitepage_topic', 'standard', 'Page Topic Options Menu', '999');
