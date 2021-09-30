/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my-upgrade-4.9.2-4.9.2p1.sql 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitecrowdfunding_admin_main_landingpage_setup", "sitecrowdfunding", "Landing Page Setup", "", '{"route":"admin_default","module":"sitecrowdfunding","controller":"settings","action":"landing-page-setup"}', "sitecrowdfunding_admin_main", "", 87),
("sitecrowdfunding_admin_main_support", "sitecrowdfunding", "Support", "", '{"route":"admin_default","module":"sitecrowdfunding","controller":"support"}', "sitecrowdfunding_admin_main", "", 89);
