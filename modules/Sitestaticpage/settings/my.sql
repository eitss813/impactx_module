/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitestaticpage', 'Sitestaticpage', 'Sitestaticpage', '5.0.0', 1, 'extra') ;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitestaticpage_pages`
--

INSERT IGNORE INTO `engine4_sitestaticpage_pages` (`owner_id`, `title`, `short_url`, `page_url`, `body`, `params`, `view_count`, `creation_date`, `modified_date`, `menu`, `level_id`, `networks`, `search`, `type`, `meta_info`) VALUES
(1, 'Contact Us', 0, '', '<h3>Contact us for assistance</h3>\r\n<p>&nbsp;</p>\r\n<p><span style="font-size: 10pt;">Fields marked with an asterisk (*) are mandatory.&nbsp;</span><span style="font-size: 10pt;">You''ll be notified when our staff answers your request.</span></p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p style="text-align: left;">[static_form_1]</p>', 'a:1:{i:0;s:2:"_1";}', 0, NOW(), NOW(), 3, '["0"]', '["0"]', 0, 1, '');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitestaticpage_page_fields_options`
--

INSERT IGNORE INTO `engine4_sitestaticpage_page_fields_options` (`option_id`, `field_id`, `label`, `form_heading`, `form_description`, `order`, `email`, `button_text`) VALUES
(1, 1, 'Contact Us', '', '', 1, '', 'Submit'),
(2, 7, 'Login & Registration', NULL, NULL, 999, '', ''),
(3, 7, 'My Account', NULL, NULL, 999, '', ''),
(4, 7, 'Something Seems Brocken', NULL, NULL, 999, '', ''),
(5, 7, 'Website', NULL, NULL, 999, '', ''),
(6, 7, 'Report Policy Violation', NULL, NULL, 999, '', ''),
(7, 8, 'Computer', NULL, NULL, 999, '', ''),
(8, 8, 'iPhone', NULL, NULL, 999, '', ''),
(9, 8, 'iPad', NULL, NULL, 999, '', ''),
(10, 8, 'Tablet', NULL, NULL, 999, '', ''),
(11, 9, 'Internet Explorer', NULL, NULL, 999, '', ''),
(12, 9, 'Chrome', NULL, NULL, 999, '', ''),
(13, 9, 'Firefox', NULL, NULL, 999, '', ''),
(14, 9, 'Safari', NULL, NULL, 999, '', ''),
(15, 9, 'Other', NULL, NULL, 999, '', '');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitestaticpage_page_fields_meta`
--

INSERT IGNORE INTO `engine4_sitestaticpage_page_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `search`, `show`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
(1, 'profile_type', 'Profile Type', '', 'profile_type', 1, 0, 0, 0, 999, '', NULL, NULL, NULL, NULL),
(2, 'first_name', 'First Name *', '', 'first_name', 1, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
(3, 'last_name', 'Last Name', '', 'last_name', 0, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
(4, 'text', 'Your email address *', '', '', 1, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
(5, 'text', 'Email Title *', 'This is the topic of your email.', '', 1, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
(6, 'textarea', 'Let us know what you need help with! *', 'Please enter specific information that may help us to serve you better.', '', 1, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
(7, 'select', 'Help us categorize your problem *', '', '', 1, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
(8, 'select', 'Select the device on which you need our help. *', '', '', 1, 0, 0, 0, 999, '[]', NULL, NULL, '', ''),
(9, 'select', 'Tell us your browser. *', '', '', 1, 0, 0, 0, 999, '[]', NULL, NULL, '', '');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitestaticpage_page_fields_maps`
--

INSERT IGNORE INTO `engine4_sitestaticpage_page_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 1, 1),
(1, 1, 2, 9999),
(1, 1, 3, 9999),
(1, 1, 4, 9999),
(1, 1, 5, 9999),
(1, 1, 6, 9999),
(1, 1, 7, 9999),
(1, 1, 8, 9999),
(1, 1, 9, 9999);