/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitecoretheme', 'Versatile - Responsive Multi-Purpose Theme', 'Versatile - Responsive Multi-Purpose Theme', '4.10.5p5', 1, 'extra') ;


DROP TABLE IF EXISTS `engine4_sitecoretheme_images`;
CREATE TABLE IF NOT EXISTS `engine4_sitecoretheme_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR( 64 ) NOT NULL,
  `icon_id` int(11) NOT NULL DEFAULT "0",
  `file_id` int(11) NOT NULL DEFAULT "0",
  `enabled` tinyint(4) NOT NULL DEFAULT "1",
  `order` tinyint(4) NOT NULL DEFAULT "99",
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `engine4_sitecoretheme_subscriptions`;
CREATE TABLE IF NOT EXISTS `engine4_sitecoretheme_subscriptions` (
  `subscription_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR( 64 ) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT "0",
  PRIMARY KEY (`subscription_id`),
  UNIQUE KEY  (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)
VALUES ("sitecoretheme_admin_settings", "sitecoretheme", "SEAO - Versatile Theme", NULL , '{"route":"admin_default","module":"sitecoretheme","controller":"settings"}', "core_admin_main_plugins", NULL , "1", "0", "999"),
("sitecoretheme_admin_settings_index", "sitecoretheme", "Global Settings", "", '{"route":"admin_default","module":"sitecoretheme","controller":"settings","action":"index"}', "sitecoretheme_admin_main", "", 1, 0, 1),
("sitecoretheme_admin_configure_pages", "sitecoretheme", "Configure Pages", "", '{"route":"admin_default","module":"sitecoretheme","controller":"settings","action":"configure-pages"}', "sitecoretheme_admin_main", "", 1, 0, 22),

("sitecoretheme_admin_landingpage", "sitecoretheme", "Landing Page", "", '{"route":"admin_default","module":"sitecoretheme","controller":"landing-page", "action": "slider"}', "sitecoretheme_admin_main", "", 1, 0, 9),
("sitecoretheme_admin_landingpage_slider", "sitecoretheme", "Landing Page Slider", "", '{"route":"admin_default","module":"sitecoretheme","controller":"landing-page", "action": "slider"}', "sitecoretheme_admin_landingpage", "", 1, 0, 1),
("sitecoretheme_admin_landingpage_cta", "sitecoretheme", "Action Buttons", "", '{"route":"admin_default","module":"sitecoretheme","controller":"landing-page", "action": "cta-buttons"}', "sitecoretheme_admin_landingpage", "", 1, 0, 11),
("sitecoretheme_admin_landingpage_text_banner", "sitecoretheme", "Banner Tagline", "", '{"route":"admin_default","module":"sitecoretheme","controller":"landing-page", "action": "text-banner"}', "sitecoretheme_admin_landingpage", "", 1, 0, 7),
("sitecoretheme_admin_landingpage_app_banner", "sitecoretheme", "Promotion Banner", "", '{"route":"admin_default","module":"sitecoretheme","controller":"landing-page", "action": "app-banner"}', "sitecoretheme_admin_landingpage", "", 1, 0, 13),
("sitecoretheme_admin_landingpage_markers", "sitecoretheme", "Markers", "", '{"route":"admin_default","module":"sitecoretheme","controller":"landing-page", "action": "markers"}', "sitecoretheme_admin_landingpage", "", 1, 0, 14),
("sitecoretheme_admin_landingpage_stats", "sitecoretheme", "Achievement Block", "", '{"route":"admin_default","module":"sitecoretheme","controller":"landing-page", "action": "stats"}', "sitecoretheme_admin_landingpage", "", 1, 0, 5),
("sitecoretheme_admin_landingpage_services", "sitecoretheme", "Services Block", "", '{"route":"admin_default","module":"sitecoretheme","controller":"landing-page", "action": "services"}', "sitecoretheme_admin_landingpage", "", 1, 0, 9),
("sitecoretheme_admin_landingpage_videobanner", "sitecoretheme", "Video Banner", "", '{"route":"admin_default","module":"sitecoretheme","controller":"landing-page", "action": "video-banner"}', "sitecoretheme_admin_landingpage", "", 1, 0, 3),
("sitecoretheme_admin_landingpage_highlights", "sitecoretheme", "Highlights Block", "", '{"route":"admin_default","module":"sitecoretheme","controller":"landing-page", "action": "highlights"}', "sitecoretheme_admin_landingpage", "", 1, 0, 4),
("sitecoretheme_admin_settings_signin_popup", "sitecoretheme", "Manage Sign-in Popup", "Sitecoretheme_Plugin_Menus", '{"route":"admin_default","module":"sitecoretheme","controller":"settings", "action": "signin-popup"}', "sitecoretheme_admin_main", "", 1, 0, 7),
("sitecoretheme_admin_header_index", "sitecoretheme", "Manage Header", "", '{"route":"admin_default","module":"sitecoretheme","controller":"header", "action": "index"}', "sitecoretheme_admin_main", "", 1, 0, 11),

("sitecoretheme_admin_theme_custom", "sitecoretheme", "Theme Customization", "", '{"route":"admin_default","module":"sitecoretheme","controller":"themes"}', "sitecoretheme_admin_main", "", 1, 0, 5),
("sitecoretheme_admin_theme_custom_color", "sitecoretheme", "Color Editor", "", '{"route":"admin_default","module":"sitecoretheme","controller":"themes"}', "sitecoretheme_admin_theme_custom", "", 1, 0, 1),
("sitecoretheme_admin_settings_custom_css", "sitecoretheme", "Custom CSS", "", '{"route":"admin_default","module":"sitecoretheme","controller":"settings","action":"custom-css"}', "sitecoretheme_admin_theme_custom", "", 1, 0, 7),

("sitecoretheme_admin_font_index", "sitecoretheme", "Manage Fonts", "", '{"route":"admin_default","module":"sitecoretheme","controller":"fonts", "action": "index"}', "sitecoretheme_admin_theme_custom", "", 1, 0, 3),
("sitecoretheme_admin_layout_index", "sitecoretheme", "Layout Settings", "", '{"route":"admin_default","module":"sitecoretheme","controller":"layout", "action": "index"}', "sitecoretheme_admin_theme_custom", "", 1, 0, 5),

("sitecoretheme_admin_settings_blocks", "sitecoretheme", "Informative Blocks", "", '{"route":"admin_default","module":"sitecoretheme","controller":"blocks","action":"index"}', "sitecoretheme_admin_main", "", 1, 0, 16),

("sitecoretheme_admin_settings_img", "sitecoretheme", "Slider Images", "", '{"route":"admin_default","module":"sitecoretheme","controller":"settings","action":"landing-images"}', "sitecoretheme_admin_main", "", 1, 0, 15),
("sitecoretheme_admin_settings_landing_images", "sitecoretheme", "Landing Page Slider Images", "", '{"route":"admin_default","module":"sitecoretheme","controller":"settings","action":"landing-images"}', "sitecoretheme_admin_settings_img", "", 1, 0, 1),
("sitecoretheme_admin_settings_inner_images", "sitecoretheme", "Inner Page Slider Images", "", '{"route":"admin_default","module":"sitecoretheme","controller":"settings","action":"inner-images"}', "sitecoretheme_admin_settings_img", "", 1, 0, 2),
("sitecoretheme_admin_settings_footer", "sitecoretheme", "Manage Footer", "", '{"route":"admin_default","module":"sitecoretheme","controller":"footer-templates"}', "sitecoretheme_admin_main", "", 1, 0, 13),
("sitecoretheme_admin_footer_templates", "sitecoretheme", "Footer Settings", "", '{"route":"admin_default","module":"sitecoretheme","controller":"footer-templates"}', "sitecoretheme_admin_settings_footer", "", 1, 0, 1),
("sitecoretheme_admin_settings_footer_menu", "sitecoretheme", "Footer Menu", "", '{"route":"admin_default","module":"sitecoretheme","controller":"settings","action":"footer-menu"}', "sitecoretheme_admin_settings_footer", "", 1, 0, 2),
("sitecoretheme_admin_settings_subscription", "sitecoretheme", "Newsletter Subscription", "", '{"route":"admin_default","module":"sitecoretheme","controller":"subscription","action":"index"}', "sitecoretheme_admin_main", "", 1, 0, 19),
("sitecoretheme_admin_settings_faq", "sitecoretheme", "FAQs", "", '{"route":"admin_default","module":"sitecoretheme","controller":"settings","action":"faq"}', "sitecoretheme_admin_main", "", 1, 0, 999)
;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)
VALUES ('sitecoretheme_admin_main_theme', "sitecoretheme", "Versatile Theme", "" , '{"route":"admin_default","module":"sitecoretheme","controller":"settings"}', "core_admin_main", NULL , "1", "0", "999");

DROP TABLE IF EXISTS `engine4_sitecoretheme_banners`;
CREATE TABLE IF NOT EXISTS `engine4_sitecoretheme_banners` (
  `banner_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR( 64 ) NOT NULL,
  `icon_id` int(11) NOT NULL DEFAULT "0",
  `file_id` int(11) NOT NULL DEFAULT "0",
  `enabled` tinyint(4) NOT NULL DEFAULT "1",
  `order` tinyint(4) NOT NULL DEFAULT "99",
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `engine4_sitecoretheme_services`;
CREATE TABLE IF NOT EXISTS `engine4_sitecoretheme_services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR( 64 ) NOT NULL,
  `description` VARCHAR( 512 ) NOT NULL,
  `file_id` int(11) NOT NULL DEFAULT "0",
  `enabled` tinyint(4) NOT NULL DEFAULT "1",
  `order` tinyint(4) NOT NULL DEFAULT "99",
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

INSERT IGNORE INTO `engine4_sitecoretheme_services` (`service_id`, `title`, `description`, `file_id`, `enabled`, `order`) VALUES
(1, 'Responsive Design', 'We provide you with completely responsive design of the community, so no matter what device your users are using to access it, they’ll always get a perfect look.', '0', 1, 1),
(2, 'Flexibility', 'We provide a community with flexible features that are easy to use and let you fabricate your community the way you want.', '0', 1, 2),
(3, 'Advertising', 'Advertisements are an excellent way to monetize a social network. It employs an openly sponsored, non-personal message to promote or sell a product, service or idea.', '0', 1, 3),
(4, 'E-Commerce', 'E-commerce is the activity of buying or selling online. E-commerce draws on technologies such as mobile commerce, electronic funds transfer, internet marketing etc', '0', 1, 4),
(5, 'Security', 'Security is must when you are part of a social community where you share your personal data and experiences and keeping this in mind, we take utmost care of developing secured products.', '0', 1, 5),
(6, 'Drag and Drop Content Management', 'You just need to drag and drop elements on required places to get a desired look of pages of your website.', '0', 1, 6),
(7, 'Anti Spam Features', 'A community free of spams helps in enhancing user experience of your website and builds users’ trust in you.', '0', 1, 7),
(8, 'Everything collaborated at one place', 'Want to make the best ever community? Join us. We provides numerous features tailored at one place.', '0', 1, 8),
(9, 'Eye Catching Community', 'A community with pleasant UI and effects catches users attention very quickly and helps in user engagement.', '0', 1, 9);

DROP TABLE IF EXISTS `engine4_sitecoretheme_highlights`;
CREATE TABLE IF NOT EXISTS `engine4_sitecoretheme_highlights` (
  `highlights_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR( 64 ) NOT NULL,
  `description` VARCHAR( 512 ) NOT NULL,
  `file_id` int(11) NOT NULL DEFAULT "0",
  `enabled` tinyint(4) NOT NULL DEFAULT "1",
  `order` tinyint(4) NOT NULL DEFAULT "99",
  PRIMARY KEY (`highlights_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

INSERT IGNORE INTO `engine4_sitecoretheme_highlights` (`highlights_id`,`title`, `description`, `file_id`, `enabled`, `order`) VALUES 
(1,'Work & impact', 'To work until the world knows you. Work harder and smarter with the impact to reach the peak of success. ', '0', '1', '1'),
(2,'Expand globally', 'Doing something which you think can solve the problem and let people know regarding it.', '0', '1', '2'),
(3,'Effectual services', 'Some services provide useful insights into effectiveness of the work growth utilising all the abstracts provided in the community.
', '0', '1', '3'),
(4,'Connect people', 'Connecting more faces around the world makes your virtual family even more enjoyable and happy.
', '0', '1', '4'),
(5,'Gathered potential members', 'Every supporter is a potential outreach hub in his or her social universe. Mobilization involves people to do things.', '0', '1', '5'),
(6,'Give people a place to meet virtually', 'In this life full of hustles, a platform is needed to give people a virtual company to enjoy.
', '0', '1', '6'),
(7, 'Proceed towards success', 'Promoting your expertise is the initial step to grow. Agile promotions always culminate the work.', '0', '1', '7'),
(8, 'Share your memorable experiences', 'Share your memorable experiences with your community members and let them get inspired by you.', '0', '1', '8');


-- DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = "sitecoretheme.navi.auth" LIMIT 1;

-- DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = "sitecoretheme.manage.type" LIMIT 1;
	
-- DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = "sitecoretheme.info.type" LIMIT 1;
	
-- INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES 
--	 ("sitecoretheme.manage.type", "' . $var1 . '"),
--	 ("sitecoretheme.info.type", "' . $var2 . '");


-- INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
--  ('sitecoretheme.global.type', '0'),
--  ('sitecoretheme.isActivate', '1');


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("sitecoretheme_footer_first_column", "sitecoretheme", "First Column", NULL, '{"uri":"javascript:void(0)"}', "sitecoretheme_footer", NULL, "1", "1", "1"),
("sitecoretheme_footer_first_column_1", "sitecoretheme", "First Column - 1", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "2"), 
("sitecoretheme_footer_first_column_2", "sitecoretheme", "First Column - 2", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "3"), 
("sitecoretheme_footer_first_column_3", "sitecoretheme", "First Column - 3", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "4"), 
("sitecoretheme_footer_first_column_4", "sitecoretheme", "First Column - 4", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "5"), 
("sitecoretheme_footer_first_column_5", "sitecoretheme", "First Column - 5", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "6"), 

("sitecoretheme_footer_second_column", "sitecoretheme", "Second Column", NULL , '{"uri":"javascript:void(0)"}', "sitecoretheme_footer", NULL , "1", "1", "10"), 
("sitecoretheme_footer_second_column_1", "sitecoretheme", "Second Column - 1", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "11"), 
("sitecoretheme_footer_second_column_2", "sitecoretheme", "Second Column - 2", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "12"), 
("sitecoretheme_footer_second_column_3", "sitecoretheme", "Second Column - 3", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "13"),
("sitecoretheme_footer_second_column_4", "sitecoretheme", "Second Column - 4", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "14"),
("sitecoretheme_footer_second_column_5", "sitecoretheme", "Second Column - 5", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "15"),

("sitecoretheme_footer_third_column", "sitecoretheme", "Third Column", NULL , '{"uri":"javascript:void(0)"}', "sitecoretheme_footer", NULL , "1", "1", "20"), 
("sitecoretheme_footer_third_column_1", "sitecoretheme", "Third Column - 1", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "21"), 
("sitecoretheme_footer_third_column_2", "sitecoretheme", "Third Column - 2", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "22"), 
("sitecoretheme_footer_third_column_3", "sitecoretheme", "Third Column - 3", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "23"),
("sitecoretheme_footer_third_column_4", "sitecoretheme", "Third Column - 4", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "24"),
("sitecoretheme_footer_third_column_5", "sitecoretheme", "Third Column - 5", "" , '{"route":"default"}', "sitecoretheme_footer", NULL , "1", "1", "25");

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES 
("sitecoretheme_footer", "standard", "Versatile - Responsive Multi-Purpose Theme - Footer Menu", "1");

 

DROP TABLE IF EXISTS `engine4_sitecoretheme_themes`;
CREATE TABLE IF NOT EXISTS `engine4_sitecoretheme_themes` (
  `theme_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`theme_id`),
  UNIQUE KEY `name` (`name`),
  KEY `active` (`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

--
-- Dumping data for table `engine4_sitecoretheme_themes`
--

INSERT IGNORE INTO `engine4_sitecoretheme_themes` ( `name`, `title`, `description`, `active`, `type`) VALUES
('lightpink', 'Pink', '', 0, 1),
('lightskyblue', 'Sky Blue', '', 0, 1),
('lightgreen', 'Green', '', 0, 1),
('lightorange', 'Orange', '', 0, 1),
('lightyellow', 'Yellow', '', 0, 1),
('lightred', 'Red', '', 0, 1),
('pink', 'Pink', '', 0, 2),
('skyblue', 'Sky Blue', '', 0, 2),
('green', 'Green', '', 0, 2),
('orange', 'Orange', '', 0, 2),
('yellow', 'Yellow', '', 0, 2),
('red', 'Red', '', 0, 2),
('darkpink', 'Pink', '', 0, 3),
('darkskyblue', 'Sky Blue', '', 0, 3),
('darkgreen', 'Green', '', 0, 3),
('darkorange', 'Orange', '', 0, 3),
('darkyellow', 'Yellow', '', 0, 3),
('darkred', 'Red', '', 0, 3),
('purplepink', 'Purple & Pink', '', 1, 4),
('bluegreen', 'Blue & Green', '', 0, 4),
('darkgreenorange', 'Dark Green & Dark Orange', '', 0, 4),
('darkblueorange', 'Dark Blue & Orange', '', 0, 4)
;

UPDATE `engine4_core_modules` SET `enabled` = '0' WHERE `engine4_core_modules`.`name` = 'mobi';

INSERT IGNORE INTO `engine4_core_themes` (`name`, `title`, `description`, `active`) VALUES
("sitecoretheme", 'Versatile - Responsive Multi-Purpose Theme', '', 1);
UPDATE `engine4_core_themes` SET `active` = '0';
UPDATE `engine4_core_themes` SET `active` = '1' WHERE `engine4_core_themes`.`name` = "sitecoretheme";

-- --------------------------------------------------------

--
-- Table structure for table `engine4_sitecoretheme_blocks`
--

DROP TABLE IF EXISTS `engine4_sitecoretheme_blocks`;
CREATE TABLE IF NOT EXISTS `engine4_sitecoretheme_blocks` (
  `block_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `subheading` varchar(64) NULL,
  `body` text NOT NULL,
  `photo_id` int(11) unsigned NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;


--
-- Dumping data for table `engine4_core_menus`
--

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('sitecoretheme_social_sites', 'standard', 'Versatile - Responsive Multi-Purpose Theme Header: Social Site Links Menu', 5)
;

--
-- Dumping data for table `engine4_core_menuitems`
--
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `enabled`, `custom`, `order`) VALUES
('sitecoretheme_social_site_facebook', 'core', 'Facebook', '', '{"uri": "","target":"_blank", "icon":"fa-facebook"}', 'sitecoretheme_social_sites', 1, 1, 1),
('sitecoretheme_social_site_twitter', 'core', 'Twitter', '', '{"uri": "","target":"_blank", "icon":"fa-twitter"}', 'sitecoretheme_social_sites', 1, 1, 2),
('sitecoretheme_social_site_linkedin', 'core', 'Linkedin', '', '{"uri": "","target":"_blank", "icon":"fa-linkedin"}', 'sitecoretheme_social_sites', 1, 1, 3),
('sitecoretheme_social_site_youtube', 'core', 'Youtube', '', '{"uri": "","target":"_blank", "icon":"fa-youtube"}', 'sitecoretheme_social_sites', 1, 1, 4),
--  ('sitecoretheme_social_site_googleplus', 'core', 'Google +', '', '{"uri": "","target":"_blank", "icon":"fa-google-plus"}', 'sitecoretheme_social_sites', 1, 1, 5),
('sitecoretheme_social_site_pinterest', 'core', 'Pinterest', '', '{"uri": "","target":"_blank", "icon":"fa-pinterest"}', 'sitecoretheme_social_sites', 1, 1, 6)
--  ('sitecoretheme_social_site_skype', 'core', 'YOUR_SKYPE_NAME', '', '{"uri": "javascript:void()","target":"_blank", "icon":"fa-skype"}', 'sitecoretheme_social_sites', 1, 1, 7)
;


INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('sitecoretheme.social.links.0', 'facebooklink'),
('sitecoretheme.social.links.1', 'twitterlink'),
('sitecoretheme.social.links.2', 'pininterestlink'),
('sitecoretheme.social.links.3', 'youtubelink'),
('sitecoretheme.social.links.4', 'linkedinlink'),
('sitecoretheme.header.sitemenu.fixed', '1'),
('sitecoretheme.header.menu.fixed', '1'),
('sitecoretheme.header.desktop.totalmenu', '6');