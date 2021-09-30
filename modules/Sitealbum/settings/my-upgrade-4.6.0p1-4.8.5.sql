-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitealbum_ratings`
--

DROP TABLE IF EXISTS `engine4_sitealbum_ratings`;
CREATE TABLE IF NOT EXISTS `engine4_sitealbum_ratings` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) NOT NULL,
  `resource_type` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`rating_id`),
  KEY `resource_id` (`resource_id`,`resource_type`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Table structure for table `engine4_album_fields_maps`
--

 DROP TABLE IF EXISTS `engine4_album_fields_maps`;
 CREATE TABLE IF NOT EXISTS `engine4_album_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`)
 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------


--
-- Dumping data for table `engine4_album_fields_maps`
--

  INSERT IGNORE INTO `engine4_album_fields_maps` (`field_id`, `option_id`, `child_id`, `order`)     VALUES
 (0, 0, 1, 1);


-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitealbum_album_fields_meta`
--
DROP TABLE IF EXISTS `engine4_album_fields_meta`;
CREATE TABLE IF NOT EXISTS `engine4_album_fields_meta` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(24) NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(32) NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) unsigned NOT NULL,
  `publish` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `show` tinyint(1) unsigned DEFAULT '0',
  `quick_info` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  `config` text COLLATE utf8_unicode_ci NOT NULL,
  `validators` text COLLATE utf8_unicode_ci,
  `filters` text COLLATE utf8_unicode_ci,
  `style` text COLLATE utf8_unicode_ci,
  `error` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Dumping data for table `engine4_album_fields_meta`
--

INSERT IGNORE INTO `engine4_album_fields_meta` (`type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `show`, `quick_info`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
('profile_type', 'Profile Type', '', 'profile_type', 1, 0, 0, 2, 0, 1, 999, '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitealbum_album_fields_options`
--
DROP TABLE IF EXISTS `engine4_album_fields_options`;
CREATE TABLE IF NOT EXISTS `engine4_album_fields_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Dumping data for table `engine4_album_fields_options`
--

INSERT IGNORE INTO `engine4_album_fields_options` ( `field_id`, `label`, `order`) VALUES
(1, 'Default Type', 0);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_album_fields_search`
--
DROP TABLE IF EXISTS `engine4_album_fields_search`;
CREATE TABLE IF NOT EXISTS `engine4_album_fields_search` (
  `item_id` int(11) NOT NULL,
  `profile_type` smallint(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `profile_type` (`profile_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_album_fields_values`
--
DROP TABLE IF EXISTS `engine4_album_fields_values`;
CREATE TABLE IF NOT EXISTS `engine4_album_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `priority`) VALUES
-- ('Rebuild Album Privacy', 'sitealbum_maintenance_rebuild_privacy', 'sitealbum', 'Album_Plugin_Job_Maintenance_RebuildPrivacy', 50);

--
-- Dumping data for table `engine4_core_menus`
--

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('sitealbum_main', 'standard', 'Advanced Albums - Advanced Album Main Navigation Menu'),
('sitealbum_quick', 'standard', 'Advanced Albums - Advanced Album Quick Navigation Menu');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES

('core_main_sitealbum', 'sitealbum', 'Albums', '', '{"route":"sitealbum_general","action":"index"}', 'core_main', '', 999),
('core_sitemap_sitealbum', 'sitealbum', 'Albums', '', '{"route":"sitealbum_general","action":"index"}', 'core_sitemap', '', 998),

('sitealbum_main_home', 'sitealbum', 'Albums Home', 'Sitealbum_Plugin_Menus::canViewAlbums', '{"route":"sitealbum_general","action":"index"}', 'sitealbum_main', '', 1),
('sitealbum_main_browse', 'sitealbum', 'Browse Albums', 'Sitealbum_Plugin_Menus::canViewAlbums', '{"route":"sitealbum_general","action":"browse"}', 'sitealbum_main', '', 2),
('sitealbum_main_location', 'sitealbum', 'Locations', 'Sitealbum_Plugin_Menus::canViewAlbums', '{"route":"sitealbum_general","module":"sitealbum","action":"map"}', 'sitealbum_main', '', 3),
('sitealbum_main_pinboard', 'sitealbum', 'Albums Pinboard', 'Sitealbum_Plugin_Menus::canViewAlbums', '{"route":"sitealbum_general","module":"sitealbum","action":"pinboard"}', 'sitealbum_main', '', 4),
('sitealbum_main_manage', 'sitealbum', 'My Albums', 'Sitealbum_Plugin_Menus::canCreateAlbums', '{"route":"sitealbum_general","action":"manage"}', 'sitealbum_main', '', 5),
('sitealbum_main_upload', 'sitealbum', 'Add New Photos', 'Sitealbum_Plugin_Menus::canCreateAlbums', '{"route":"sitealbum_general","action":"upload"}', 'sitealbum_main', '', 6),
('sitealbum_main_categories', 'sitealbum', 'Categories', 'Sitealbum_Plugin_Menus::canViewAlbums', '{"route":"sitealbum_general","action":"categories"}', 'sitealbum_main', '', 7),

('sitealbum_quick_upload', 'sitealbum', 'Add New Photos', 'Sitealbum_Plugin_Menus::canCreateAlbums', '{"route":"sitealbum_general","action":"upload","class":"buttonlink icon_photos_new"}', 'sitealbum_quick', '', 1),
('sitealbum_quick_badge', 'sitealbum', 'Create Photos Badge', 'Sitealbum_Plugin_Menus::canCreateBadge', '{"route":"sitealbum_badge","action":"create","class":"buttonlink sitealbum_icon_badge_create"}', 'sitealbum_quick', '', 2),




('sitealbum_admin_main_level', 'sitealbum', 'Member Level Settings', '', '{"route":"admin_default","module":"sitealbum","controller":"level"}', 'sitealbum_admin_main', '', 2),
('sitealbum_admin_main_categories', 'sitealbum', 'Categories', '', '{"route":"admin_default","module":"sitealbum","controller":"settings", "action":"categories"}', 'sitealbum_admin_main', '', 4),
('sitealbum_admin_main_fields', 'sitealbum', 'Profile Fields', '', '{"route":"admin_default","module":"sitealbum","controller":"fields"}', 'sitealbum_admin_main', '', 7),

('sitealbum_admin_main_formsearch', 'sitealbum', 'Search Form Settings', '', '{"route":"admin_default","module":"sitealbum","controller":"settings","action":"form-search"}', 'sitealbum_admin_main', '', 8),


('sitealbum_admin_main_album_manage', 'sitealbum', 'Manage Albums', '', '{"route":"admin_default","module":"sitealbum","controller":"manage"}', 'sitealbum_admin_main', '', 9),
('sitealbum_admin_main_profilemaps', 'sitealbum', 'Category-Album Profile Mapping', '', '{"route":"admin_default","module":"sitealbum","controller":"profilemaps","action":"manage"}', 'sitealbum_admin_main', '', 10),

( 'sitealbum_admin_main_integrations', 'sitealbum', 'Plugins Integrations', '', '{"route":"admin_default","module":"sitealbum","controller":"settings","action":"integrations"}', 'sitealbum_admin_main', '', 11),



('sitealbum_admin_main_template', 'sitealbum', 'Layout Templates', '', '{"route":"admin_default","module":"sitealbum","controller":"settings", "action":"set-template"}', 'sitealbum_admin_main', '', 13);
-- --------------------------------------------------------


--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('album_photo_new', 'sitealbum', '{item:$subject} added {var:$count} photo(s) to the album {item:$object}:', 1, 5, 1, 3, 1, 1),
('comment_album', 'sitealbum', '{item:$subject} commented on {item:$owner}''s {item:$object:album}: {body:$body}', 1, 1, 1, 1, 1, 1),
('comment_album_photo', 'sitealbum', '{item:$subject} commented on {item:$owner}''s {item:$object:photo}: {body:$body}', 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------


--
-- Dumping data for table `engine4_core_mailtemplates`
--

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('SITEALBUM_SEND_EMAIL', 'sitealbum', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

-- --------------------------------------------------------


-- INSERT IGNORE INTO `engine4_seaocore_tabs` (`module`, `type`, `name`, `title`, `enabled`, `order`, `limit`, `show`) VALUES ('sitealbum', 'photos', 'rating_photos', 'Most Rated', '1', '7', '24', '1');
-- INSERT IGNORE INTO `engine4_seaocore_tabs` (`module`, `type`, `name`, `title`, `enabled`, `order`, `limit`, `show`) VALUES ('sitealbum', 'albums', 'rating_albums', 'Most Rated', '1', '7', '24', '1');

-- categories work remaining.

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'album' as `type`,
    'rate' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'album' as `type`,
    'rate' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_seaocore_searchformsetting`(`module`, `name`, `display`, `order`, `label`) VALUES 
('sitealbum', 'search', '1', '5', 'Name / Keyword'),
('sitealbum', 'view', 1, 12, 'View'),
('sitealbum', 'orderby', '1', '15', 'Browse By'),
('sitealbum', 'location', '1', '35', 'Location'),
('sitealbum', 'proximity', '1', '40', 'Proximity Search'),
('sitealbum', 'street', '1', '45', 'Street'),
('sitealbum', 'city', '1', '50', 'City'),
('sitealbum', 'state', '1', '55', 'State'),
('sitealbum', 'country', '1', '60', 'Country'),
('sitealbum', 'category_id', '1', '70', 'Category');

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"admin_default","module":"sitealbum","controller":"album", "action":"album-of-day"}' WHERE `engine4_core_menuitems`.`name` = 'sitealbum_admin_main_manage';

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES ('album_profile', 'standard', 'Advanced Albums - Advanced Album Profile Options Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES 
			('sitealbum_profile_add', 'sitealbum', 'Add Photos', 'Sitealbum_Plugin_Menus', '', 'album_profile', '', 1),
			('sitealbum_profile_manage', 'sitealbum', 'Manage Photos', 'Sitealbum_Plugin_Menus', '', 'album_profile', '', 2),
			('sitealbum_profile_edit', 'sitealbum', 'Edit', 'Sitealbum_Plugin_Menus', '', 'album_profile', '', 3),
			('sitealbum_profile_delete', 'sitealbum', 'Delete Album', 'Sitealbum_Plugin_Menus', '', 'album_profile', '', 4),
			('sitealbum_profile_share', 'sitealbum', 'Share via Badge', 'Sitealbum_Plugin_Menus', '', 'album_profile', '', 5),
			('sitealbum_profile_makealbumoftheday', 'sitealbum', 'Make Album of the Day', 'Sitealbum_Plugin_Menus', '', 'album_profile', '', 6), 
			('sitealbum_profile_getlink', 'sitealbum', 'Get Link', 'Sitealbum_Plugin_Menus', '', 'album_profile', '', 7),
			('sitealbum_profile_editlocation', 'sitealbum', 'Edit Location', 'Sitealbum_Plugin_Menus', '', 'album_profile', '', 8),
			('sitealbum_profile_suggesttofriend', 'sitealbum', 'Suggest To Friend', 'Sitealbum_Plugin_Menus', '', 'album_profile', '', 9);