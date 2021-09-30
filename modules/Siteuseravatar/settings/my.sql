INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('siteuseravatar', 'Member Avatars Plugin', 'Member Avatars Plugin', '5.0.0', 1, 'extra') ;



-- --------------------------------------------------------

--
-- Table structure for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_admin_main_plugins_siteuseravatar', 'siteuseravatar', 'Member Avatars Plugin', '', '{"route":"admin_default","module":"siteuseravatar","controller":"settings"}', 'core_admin_main_plugins', '', 1, 0, 999),
( 'siteuseravatar_admin_main_global', 'siteuseravatar', 'Global Settings', NULL,
'{"route":"admin_default","module":"siteuseravatar","controller":"settings","action":"index"}',
'siteuseravatar_admin_main', NULL, 1, 0, 1),
( 'siteuseravatar_admin_main_faq', 'siteuseravatar', 'FAQ', NULL,
'{"route":"admin_default","module":"siteuseravatar","controller":"settings","action":"faq"}',
'siteuseravatar_admin_main', NULL, 1, 0, 5);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_user_settings`
--

DROP TABLE IF EXISTS `engine4_siteuseravatar_avatars`;
CREATE TABLE IF NOT EXISTS `engine4_siteuseravatar_avatars` (
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `avatar_id` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;