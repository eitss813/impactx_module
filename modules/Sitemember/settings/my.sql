/**'
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 2014-07-20 00:00:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

-- ------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--


INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES ('sitemember', 'Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin', 'Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin', '5.0.1', 1, 'extra');


-- ------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('sitemember.reviews.ratings', '2');
-- ------------------------------------------------------
--
-- Dumping data for table `engine4_core_menuitems`
--

UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"user_general","action":"browse","icon":"fa-user"}'
WHERE `name` = 'core_main_user';


--
-- Table structure for table `engine4_sitemember_compliment_categories`
--

DROP TABLE IF EXISTS `engine4_sitemember_compliment_categories`;
CREATE TABLE IF NOT EXISTS `engine4_sitemember_compliment_categories` (
  `complimentcategory_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(1000) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

--
-- Table structure for table `engine4_sitemember_views`
--

DROP TABLE IF EXISTS `engine4_sitemember_views`;
CREATE TABLE IF NOT EXISTS `engine4_sitemember_views` (
  `viewer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
   PRIMARY KEY (`viewer_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

--
-- Table structure for table `engine4_sitemember_compliments`
--

DROP TABLE IF EXISTS `engine4_sitemember_compliments`;
CREATE TABLE IF NOT EXISTS `engine4_sitemember_compliments` (
  `compliment_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `resource_type` varchar(50) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `complimentcategory_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

