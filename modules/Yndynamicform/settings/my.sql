INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('yndynamicform', 'YNC - Dynamic Form', '', '4.01', 1, 'extra') ;

ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);
-- create forms table

DROP TABLE IF EXISTS `engine4_yndynamicform_forms`;
CREATE TABLE IF NOT EXISTS `engine4_yndynamicform_forms` (
  `form_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned,
  `title` varchar(64) NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `category_id` int(11) unsigned,
  `enable` tinyint(1) NOT NULL default '0',
  `total_entries` int(11) unsigned DEFAULT 0,
  `style` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `input_type` ENUM('txt','img') NOT NULL COLLATE 'utf8_unicode_ci' DEFAULT 'txt',
  `btn_text` varchar(64) DEFAULT 'Submit',
  `btn_color` varchar(64) DEFAULT '#619dbe',
  `btn_hover_color` varchar(64) DEFAULT '#7eb6d5',
  `txt_color` varchar(64) DEFAULT '#FFFFFF',
  `txt_hover_color` varchar(64) DEFAULT '#FFFFFF',
  `btn_image` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `btn_hover_image` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `conditional_enabled` tinyint(1),
  `conditional_logic` text NULL,
  `conditional_show` tinyint(1) NULL,
  `conditional_scope` varchar(64) NULL,
  `page_break_config` text NULL,
  `entries_max` int(11) unsigned NOT NULL default '0',
  `entries_max_per` ENUM('total','day', 'week', 'month', 'year') COLLATE 'utf8_unicode_ci',
  `entries_max_message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `entries_editable` tinyint(1) NOT NULL default '0',
  `entries_editable_within` int(11) unsigned,
  `time_unit` ENUM('min','hour') NOT NULL default 'min',
  `require_login` tinyint(1) NOT NULL default '1',
  `show_email_popup` int(11) unsigned NOT NULL default '0',
  `require_login_message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `valid_from_date` date,
  `valid_to_date` date,
  `unlimited_time` tinyint(1) NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `option_id` int(11) NOT NULL DEFAULT 0,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `privacy` int(11) NOT NULL DEFAULT 3,
  `photo_id` int(11) unsigned,
  PRIMARY KEY (`form_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- create confirmations table

DROP TABLE IF EXISTS `engine4_yndynamicform_confirmations`;
CREATE TABLE IF NOT EXISTS `engine4_yndynamicform_confirmations` (
  `confirmation_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(64) NOT NULL DEFAULT '',
  `type` ENUM('text','url') NOT NULL COLLATE 'utf8_unicode_ci' DEFAULT 'text',
  `confirmation_text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `confirmation_url` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `conditional_enabled` tinyint(1) NOT NULL default '0',
  `conditional_logic` text NULL,
  `conditional_show` tinyint(1) NULL,
  `conditional_scope` varchar(64) NULL,
  `enable` tinyint(1) NOT NULL default '0',
  `order` smallint(6) NOT NULL DEFAULT '99',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`confirmation_id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- create confirmations table

DROP TABLE IF EXISTS `engine4_yndynamicform_notifications`;
CREATE TABLE IF NOT EXISTS `engine4_yndynamicform_notifications` (
  `notification_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(11) unsigned NOT NULL,
  `name` varchar(64) NOT NULL default '',
  `conditional_enabled` tinyint(1),
  `conditional_logic` text NULL,
  `conditional_show` tinyint(1) NULL,
  `conditional_scope` varchar(64) NULL,
  `notification_email_subject` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `notification_email_body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `enable` tinyint(1) NOT NULL default '0',
  `order` smallint(6) NOT NULL DEFAULT '99',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- create confirmations table

DROP TABLE IF EXISTS `engine4_yndynamicform_moderators`;
CREATE TABLE IF NOT EXISTS `engine4_yndynamicform_moderators` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `moderator_id` int(11) unsigned NOT NULL,
  `form_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`),
  KEY `moderator_id` (`moderator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- create categories table

DROP TABLE IF EXISTS `engine4_yndynamicform_categories`;
CREATE TABLE IF NOT EXISTS `engine4_yndynamicform_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `pleft` int(11) unsigned NOT NULL,
  `pright` int(11) unsigned NOT NULL,
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '99',
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `pleft` (`pleft`),
  KEY `pright` (`pright`),
  KEY `level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table categories`
--

INSERT INTO `engine4_yndynamicform_categories` (`category_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`, `order`) VALUES
(1, 0, NULL, 1, 158, 0, 'All Categories', 99),
(2, 1, 1, 116, 125, 1, 'Information Providing', 0),
(3, 1, 1, 2, 11, 1, 'Application', 1),
(4, 1, 1, 26, 35, 1, 'Invitation', 2),
(5, 1, 1, 102, 115, 1, 'Survey', 3),
(6, 1, 1, 76, 81, 1, 'Poll', 4),
(7, 1, 1, 88, 101, 1, 'Registration', 6),
(8, 1, 1, 48, 75, 1, 'Order - Payment - Booking', 7),
(9, 1, 1, 36, 45, 1, 'Knowledge & Quiz', 8),
(10, 1, 1, 12, 17, 1, 'CV Submission', 9),
(11, 1, 1, 82, 87, 1, 'Report', 10),
(12, 1, 1, 18, 25, 1, 'Feedback', 11),
(13, 1, 1, 46, 47, 1, 'Others', 12),
(14, 1, 2, 117, 120, 2, 'Contact Information', 99),
(15, 1, 2, 123, 124, 2, 'Signup Shet', 99),
(16, 1, 2, 121, 122, 2, 'Employee Information', 99),
(17, 1, 12, 19, 20, 2, 'Event Feedback', 99),
(18, 1, 12, 21, 22, 2, 'Service Feedback', 99),
(19, 1, 12, 23, 24, 2, 'Product Feedback', 99),
(20, 1, 11, 83, 84, 2, 'Incident Report', 99),
(21, 1, 11, 85, 86, 2, 'Working Report', 99),
(22, 1, 10, 13, 14, 2, 'Artist CV', 99),
(23, 1, 3, 5, 6, 2, 'Job Application', 99),
(24, 1, 3, 7, 8, 2, 'Membership Application', 99),
(25, 1, 3, 9, 10, 2, 'Schoolarship Application', 99),
(26, 1, 3, 3, 4, 2, 'Course Application', 99),
(27, 1, 4, 27, 28, 2, 'Event Invitation', 99),
(28, 1, 4, 33, 34, 2, 'Party Invitation', 99),
(29, 1, 4, 31, 32, 2, 'Membership Invitation', 99),
(30, 1, 4, 29, 30, 2, 'Job Invitation', 99),
(31, 1, 5, 107, 114, 2, 'Research Survey', 99),
(32, 1, 5, 105, 106, 2, 'Post-Event Survey', 99),
(33, 1, 5, 103, 104, 2, 'Branding Questionnaire', 99),
(34, 1, 6, 79, 80, 2, 'Political Poll', 99),
(35, 1, 6, 77, 78, 2, 'Funny Poll', 99),
(36, 1, 7, 95, 96, 2, 'Member Registration', 99),
(37, 1, 7, 93, 94, 2, 'Education Registration', 99),
(38, 1, 7, 89, 90, 2, 'Course Registration', 99),
(39, 1, 7, 99, 100, 2, 'Signup Sheet', 99),
(40, 1, 7, 97, 98, 2, 'Patient Registration', 99),
(41, 1, 7, 91, 92, 2, 'Event Registration', 99),
(42, 1, 8, 51, 54, 2, 'Delivery Order', 0),
(43, 1, 8, 55, 62, 2, 'Product Order', 1),
(44, 1, 8, 63, 74, 2, 'Service Booking', 3),
(45, 1, 8, 49, 50, 2, 'Donation', 4),
(46, 1, 9, 41, 42, 2, 'Science Quiz', 99),
(47, 1, 9, 37, 38, 2, 'Geography Quiz', 99),
(48, 1, 9, 39, 40, 2, 'Mathmatic Quiz', 99),
(49, 1, 9, 43, 44, 2, 'Trivia Quiz', 99),
(50, 1, 10, 15, 16, 2, 'Scientist CV', 99),
(51, 1, 14, 118, 119, 3, 'Contact Us', 99),
(52, 1, 31, 108, 109, 3, 'Market Research Survey', 99),
(53, 1, 31, 110, 111, 3, 'Social Research Survey', 99),
(54, 1, 31, 112, 113, 3, 'Technology Research Survey', 99),
(55, 1, 42, 52, 53, 3, 'Food Delivery', 99),
(56, 1, 43, 60, 61, 3, 'Website Design', 99),
(57, 1, 43, 56, 57, 3, 'Clothes', 99),
(58, 1, 43, 58, 59, 3, 'Furniture', 99),
(59, 1, 44, 64, 65, 3, 'Airplane Booking', 99),
(60, 1, 44, 70, 71, 3, 'Photography Session', 99),
(61, 1, 44, 68, 69, 3, 'Party Preparation', 99),
(62, 1, 44, 66, 67, 3, 'Hotel Booking', 99),
(63, 1, 44, 72, 73, 3, 'Transportation', 99);

-- insert menus
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('yndynamicform_main', 'standard', 'YNC - Dynamic Form - Main Navigation Menu');

INSERT IGNORE INTO engine4_core_menuitems (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_main_yndynamicform', 'yndynamicform', 'Dynamic Form', '', '{"route":"yndynamicform_general"}', 'core_main', '', 1, 0, 999),
('yndynamicform_main_browse', 'yndynamicform', 'Home Page', '', '{"route":"yndynamicform_general"}', 'yndynamicform_main', '', 1, 0, 1),
('yndynamicform_main_manage_entries', 'yndynamicform', 'My Entries', 'Yndynamicform_Plugin_Menus', '{"route":"yndynamicform_entry_general","action":"manage"}', 'yndynamicform_main', '', 1, 0, 2),
('yndynamicform_main_manage_moderated_forms', 'yndynamicform', 'My Moderated Forms', 'Yndynamicform_Plugin_Menus', '{"route":"yndynamicform_general","action":"my-moderated-forms"}', 'yndynamicform_main', '', 1, 0, 3),
('core_admin_main_plugins_yndynamicform', 'yndynamicform', 'YNC - Dynamic Form', '', '{"route":"admin_default","module":"yndynamicform","controller":"manage"}', 'core_admin_main_plugins', '', 1, 0, 999),
('yndynamicform_admin_main_forms', 'yndynamicform', 'Manage Forms', '', '{"route":"admin_default","module":"yndynamicform","controller":"manage", "action":"index"}', 'yndynamicform_admin_main', '', 1, 0, 1),
('yndynamicform_admin_main_settings', 'yndynamicform', 'Global Settings', '', '{"route":"admin_default","module":"yndynamicform","controller":"settings"}', 'yndynamicform_admin_main', '', 1, 0, 2),
('yndynamicform_admin_main_level', 'yndynamicform', 'Member Level Settings', '', '{"route":"admin_default","module":"yndynamicform","controller":"settings","action":"level"}', 'yndynamicform_admin_main', '', 1, 0, 3),
('yndynamicform_admin_main_categories', 'yndynamicform', 'Manage Categories', '', '{"route":"admin_default","module":"yndynamicform","controller":"categories", "action":"index"}', 'yndynamicform_admin_main', '', 1, 0, 4),
('yndynamicform_admin_main_import-export', 'yndynamicform', 'Import/Export', '', '{"route":"admin_default","module":"yndynamicform","controller":"import-export", "action":"index"}', 'yndynamicform_admin_main', '', 1, 0, 5);


-- --------------------------------------------------------
--
-- Table structure for table `engine4_yndynamicform_views`
--

CREATE TABLE IF NOT EXISTS `engine4_yndynamicform_views` (
`view_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL DEFAULT '0',
`entry_id` int(11) unsigned NOT NULL DEFAULT '0',
`creation_date` datetime NOT NULL,
PRIMARY KEY (`view_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_yndynamicform_entry_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_yndynamicform_entry_fields_maps` (
  `field_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_yndynamicform_entry_fields_maps`
--

INSERT IGNORE INTO `engine4_yndynamicform_entry_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_yndynamicform_entry_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_yndynamicform_entry_fields_meta` (
  `field_id` int(11) unsigned NOT NULL auto_increment,

  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL default '',
  `alias` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL default '',
  `required` tinyint(1) NOT NULL default '0',
  `display` tinyint(1) unsigned NOT NULL,
  `publish` tinyint(1) unsigned NOT NULL default '0',
  `search` tinyint(1) unsigned NOT NULL default '0',
  `show` tinyint(1) unsigned NOT NULL default '1',
  `order` smallint(3) unsigned NOT NULL default '999',

  `config` text NULL,
  `validators` text NULL,
  `filters` text NULL,

  `style` text NULL,
  `error` text NULL,

  PRIMARY KEY  (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_yndynamicform_entry_fields_fields`
--

INSERT IGNORE INTO `engine4_yndynamicform_entry_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `config`, `validators`, `filters`, `display`, `search`) VALUES
(1, 'profile_type', 'Profile Type', '', 'profile_type', 1, '', NULL, NULL, 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_yndynamicform_entry_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_yndynamicform_entry_fields_options` (
  `option_id` int(11) unsigned NOT NULL auto_increment,
  `field_id` int(11) unsigned NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL default '999',
  PRIMARY KEY  (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_yndynamicform_entry_fields_options`
--

INSERT IGNORE INTO `engine4_yndynamicform_entry_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES
(1, 1, 'Default Type', 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_yndynamicform_entry_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_yndynamicform_entry_fields_values` (
  `item_id` int(11) unsigned NOT NULL,
  `field_id` int(11) unsigned NOT NULL,
  `index` smallint(3) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  `privacy` varchar(64) default NULL,
  PRIMARY KEY  (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Dumping data for table `engine4_yndynamicform_entry_fields_values`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_yndynamicform_entry_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_yndynamicform_entry_fields_search` (
  `item_id` int(11) unsigned NOT NULL,
  `profile_type` smallint(11) unsigned NULL,
  PRIMARY KEY  (`item_id`),
  KEY (`profile_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


--
-- Table structure for table `engine4_yndynamicform_entries`
--

CREATE TABLE IF NOT EXISTS `engine4_yndynamicform_entries` (
  `entry_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(11) unsigned NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `user_email` varchar(256) DEFAULT NULL,
  `ip` varbinary(16),
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`entry_id`),
  KEY (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

ALTER TABLE `engine4_activity_notificationtypes` MODIFY `type` VARCHAR(128);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('yndynamicform_anonymous_submitted', 'yndynamicform', 'A visitor has submitted an entry on form {item:$object}.', 0, '', 1),
('yndynamicform_user_submitted', 'yndynamicform', '{item:$subject} has submitted an entry on form {item:$object}.', 0, '', 1),
('yndynamicform_linked_form_submission', 'yndynamicform', 'Your account email is linked with some form submissions in the past.', 0, '', 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_yndynamicform_linked_form_submission', 'yndynamicform', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[manage_entries_link],[site_name],[email_address]');

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('yndynamicform_user_submitted', 'yndynamicform', '{item:$subject} has submitted an entry on form {item:$object}', 1, 5, 1, 1, 1, 1);

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ADMIN & MODERATOR
-- view, comment, submission, max

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yndynamicform_form' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yndynamicform_form' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yndynamicform_form' as `type`,
    'submission' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yndynamicform_form' as `type`,
    'max' as `name`,
    3 as `value`,
    50 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- view, comment, submission, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yndynamicform_form' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yndynamicform_form' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yndynamicform_form' as `type`,
    'submission' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yndynamicform_form' as `type`,
    'max' as `name`,
    3 as `value`,
    10 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view, comment, submission, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yndynamicform_form' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yndynamicform_form' as `type`,
    'submission' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yndynamicform_form' as `type`,
    'max' as `name`,
    1 as `value`,
    10 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');
