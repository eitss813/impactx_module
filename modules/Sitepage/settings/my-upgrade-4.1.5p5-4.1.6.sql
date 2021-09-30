UPDATE `engine4_core_menuitems`  SET `plugin` = '' WHERE `engine4_core_menuitems`.`name` ='core_main_sitepage' LIMIT 1 ;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_seaocore_searchformsetting`
--

INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES
('sitepage', 'show', 1, 1, 'Show'),
('sitepage', 'closed', 1, 2, 'Status'),
('sitepage', 'orderby', 1, 3, 'Browse By'),
('sitepage', 'badge_id', 1, 4, 'Badge'),
('sitepage', 'search', 1, 5, 'Search Pages'),
('sitepage', 'location', 1, 6, 'Location'),
('sitepage', 'locationmiles', 1, 7, 'Within Miles / Within Kilometers'),
('sitepage', 'price', 1, 8, 'Price'),
-- ('sitepage', 'profile_type', 1, 9, 'Page Profile Type'),
('sitepage', 'has_photo', 1, 10000009, 'Only Pages With Photos');



INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'sitepage_admin_main_form_search', 'sitepage', 'Search Form Settings', '', '{"route":"admin_default","module":"sitepage","controller":"settings","action":"form-search"}', 'sitepage_admin_main', '', 1, 0, 14);


-- --------------------------------------------------------

DROP TABLE IF EXISTS `engine4_sitepage_search`;

-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sitepage_admin_extension', 'sitepage', 'Extensions', '', '{"route":"admin_default","module":"sitepage","controller":"extension","action":"upgrade"}', 'sitepage_admin_main', '', 15);