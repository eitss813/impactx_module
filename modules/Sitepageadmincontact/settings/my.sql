/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageadmincontact
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2011-11-15 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sitepageadmincontact', 'Directory / Pages - Contact Page Owners Extension', 'Directory / Pages - Contact Page Owners Extension', '4.9.4', 1, 'extra') ;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_admin_main_plugins_sitepageadmincontact', 'sitepageadmincontact', 'SEAO - Directory / Pages - Contact Page Owners', '', '{"route":"admin_default","module":"sitepageadmincontact","controller":"mails"}', 'core_admin_main_plugins', '', 1, 0, 999),
('sitepageadmincontact_admin_main_mails', 'sitepageadmincontact', 'Email Page Admins', '', '{"route":"admin_default","module":"sitepageadmincontact","controller":"mails"}', 'sitepageadmincontact_admin_main', '', 1, 0, 1),
('sitepageadmincontact_admin_main_messages', 'sitepageadmincontact', 'Message Page Admins', '', '{"route":"admin_default","module":"sitepageadmincontact","controller":"messages"}', 'sitepageadmincontact_admin_main', '', 1, 0, 2),
('sitepageadmincontact_admin_main_mailsettings', 'sitepageadmincontact', 'Email Settings', NULL, '{"route":"admin_default","module":"sitepageadmincontact","controller":"mailsettings"}', 'sitepageadmincontact_admin_main', '', 1, 0, 3),
( 'sitepageadmincontact_admin_main_faq', 'sitepageadmincontact', 'FAQ', NULL, '{"route":"admin_default","module":"sitepageadmincontact","controller":"mails","action":"faq"}', 'sitepageadmincontact_admin_main', NULL, 1, 0, 4);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_mailtemplates`
--

INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES
('SITEPAGEADMINCONTACT_CONTACTS_EMAIL_NOTIFICATION', 'sitepageadmincontact', '[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]');