<?php


if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.pluginactivated')) {

  $db = Zend_Db_Table_Abstract::getDefaultAdapter();
  
  $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
  ("sesblog_admin_packagesetting", "sesblogpackage", "Package Settings", "", \'{"route":"admin_default","module":"sesblogpackage","controller":"package","action":"settings"}\', "sesblog_admin_main", "", 2),
  ("sesblog_admin_subpackagesetting", "sesblogpackage", "Package Settings", "", \'{"route":"admin_default","module":"sesblogpackage","controller":"package","action":"settings"}\', "sesblog_admin_packagesetting", "", 1),
  ("sesblog_main_manage_package", "sesblog", "My Packages", "Sesblog_Plugin_Menus", \'{"route":"sesblog_general","action":"package"}\', "sesblog_main", "", 7);');

  $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ("sesblog_admin_package", "sesblogpackage", "Manage Packages", "", \'{"route":"admin_default","module":"sesblogpackage","controller":"package"}\', "sesblog_admin_packagesetting", "", 2),
  ("sesblogpackage_admin_main_transaction", "sesblogpackage", "Manage Transactions", "", \'{"route":"admin_default","module":"sesblogpackage","controller":"package", "action":"manage-transaction"}\', "sesblog_admin_packagesetting", "", 3);');

  $db->query('DROP TABLE IF EXISTS `engine4_sesblogpackage_packages`;');
  $db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblogpackage_packages` (
    `package_id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255),
    `description` text,
    `item_count` INT(11) DEFAULT "0",
    `custom_fields` TEXT DEFAULT NULL,
    `member_level` varchar(255) DEFAULT NULL,
    `price` float DEFAULT "0",
    `recurrence` varchar(25) DEFAULT "0",
    `renew_link_days` INT(11) DEFAULT "0",
    `is_renew_link` tinyint(1) DEFAULT "0",
    `recurrence_type` varchar(25) DEFAULT NULL,
    `duration` varchar(25) DEFAULT "0",
    `duration_type` varchar(10) DEFAULT NULL,
    `enabled` tinyint(1) NOT NULL DEFAULT "1",
    `params` text DEFAULT NULL,
    `custom_fields_params` TEXT DEFAULT NULL,
    `default` tinyint(1) NOT NULL DEFAULT "0",
    `order` INT(11) NOT NULL DEFAULT "0",
    `highlight` TINYINT(1) NOT NULL DEFAULT "0",
    `show_upgrade` INT(11) NOT NULL DEFAULT "0",
    `creation_date` datetime NOT NULL,
    `modified_date` datetime NOT NULL,
    PRIMARY KEY (`package_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

  $db->query('INSERT IGNORE INTO `engine4_sesblogpackage_packages` (`title`, `description`, `member_level`, `price`, `recurrence`, `recurrence_type`, `duration`, `duration_type`, `enabled`, `params`, `default`, `creation_date`, `modified_date`) VALUES ("Free Blog Package", NULL, "0,1,2,3,4", "0", "0", "forever", "0", "forever", "1", \'{"is_featured":"1","is_sponsored":"1","is_verified":"1","award_count":"5","allow_participant":null,"upload_cover":"1","upload_mainphoto":"1","blog_choose_style":"1","blog_chooselayout":["1","2","3","4"],"blog_approve":"1","blog_featured":"0","blog_sponsored":"0","blog_verified":"0","blog_hot":0,"blog_seo":"1","blog_overview":"1","blog_bgphoto":"1","blog_contactinfo":"1","blog_enable_contactparticipant":"1","custom_fields":1}\', "1", "NOW()", "NOW()");');

  $db->query('DROP TABLE IF EXISTS `engine4_sesblogpackage_transactions`;');
  $db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblogpackage_transactions` (
    `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
    `package_id` int(11) NOT NULL,
    `owner_id` int(11) NOT NULL,
    `order_id` int(11) NOT NULL,
    `orderspackage_id` int(11) NOT NULL,
    `gateway_id` tinyint(1) DEFAULT NULL,
    `gateway_transaction_id` varchar(128) DEFAULT NULL,
    `gateway_parent_transaction_id` varchar(128) DEFAULT NULL,
    `item_count` int(11) NOT NULL DEFAULT "0",
    `gateway_profile_id` VARCHAR(128) DEFAULT NULL,
    `state` enum("pending","cancelled","failed","imcomplete","complete","refund","okay","overdue","initial","active") NOT NULL DEFAULT "pending",
    `change_rate` float NOT NULL DEFAULT "0",
    `total_amount` float NOT NULL DEFAULT "0",
    `currency_symbol` varchar(45) DEFAULT NULL,
    `gateway_type` varchar(45) DEFAULT NULL,
    `ip_address` varchar(45) NOT NULL DEFAULT "0.0.0.0",
    `expiration_date` datetime NOT NULL,
    `creation_date` datetime NOT NULL,
    `modified_date` datetime NOT NULL,
    PRIMARY KEY (`transaction_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT = 1 ;');

  $db->query('DROP TABLE IF EXISTS `engine4_sesblogpackage_orderspackages`;');
  $db->query('CREATE TABLE IF NOT EXISTS `engine4_sesblogpackage_orderspackages` (
  `orderspackage_id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `item_count` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `state` enum("pending","cancelled","failed","imcomplete","complete","refund","okay","overdue","active") NOT NULL DEFAULT "pending",
  `expiration_date` datetime NOT NULL,
  `ip_address` varchar(45) NOT NULL DEFAULT "0.0.0.0",
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`orderspackage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

  $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("sesblog_payment_notify", "sesblog", \'Make payment of your blog {item:$object} to get your blog approved.\', 0, "");');

  $db->query('INSERT IGNORE INTO `engine4_sesblog_dashboards` (`type`, `title`, `enabled`, `main`) VALUES
  ("upgrade", "Upgrade Package", "1", "0");');

  $db->query('ALTER TABLE `engine4_sesblog_blogs` ADD `package_id` INT(11) NOT NULL DEFAULT "0";');
  $db->query('ALTER TABLE  `engine4_sesblog_blogs` ADD  `transaction_id` INT(11) NOT NULL DEFAULT "0";');
  $db->query('ALTER TABLE  `engine4_sesblog_blogs` ADD  `existing_package_order` INT(11) NOT NULL DEFAULT "0";');
  $db->query('ALTER TABLE  `engine4_sesblog_blogs` ADD  `orderspackage_id` INT(11) NOT NULL DEFAULT "0";');
  
  $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
  ("sesblog_admin_approval", "sesblog", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[object_link],[blog_title]"),
  ("sesblog_send_approval_blog", "sesblog", "[host],[email],[recipient_title],[recipient_link],[recipient_photo],[object_link],[blog_title]");');


  $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
  ("sesblog_send_approval_blog", "sesblog", \'Your blog {item:$object} has been sent to site admin for approval.\', 0, ""),
  ("sesblog_approved_blog", "sesblog", \'Your blog {item:$object} has been approved.\', 0, "");');

  include_once APPLICATION_PATH . "/application/modules/Sesblogpackage/controllers/defaultsettings.php";

  Engine_Api::_()->getApi('settings', 'core')->setSetting('sesblogpackage.pluginactivated', 1);
  $error = 1;
}
