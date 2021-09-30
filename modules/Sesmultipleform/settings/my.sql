
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: my.sql 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_sesmultipleform', 'sesmultipleform', 'SES - All in One Multiple Forms', '', 
'{"route":"admin_default","module":"sesmultipleform","controller":"settings"}', 
'core_admin_main_plugins', '', 1),
('sesmultipleform_admin_main_settings', 'sesmultipleform', 'Global Settings', '', 
'{"route":"admin_default","module":"sesmultipleform","controller":"settings"}', 
'sesmultipleform_admin_main', '', 1);
