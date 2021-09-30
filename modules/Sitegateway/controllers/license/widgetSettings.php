<?php
$db = Engine_Db_Table::getDefaultAdapter();

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitegateway_admin_main_gateways", "sitegateway", "Gateways", "", \'{"route":"admin_default","module":"sitegateway","controller":"gateways"}\', "sitegateway_admin_main", "", 20),
("sitegateway_admin_main_transactions", "sitegateway", "Transactions", "", \'{"route":"admin_default","module":"sitegateway","controller":"index","action":"index"}\', "sitegateway_admin_main", "", 30),
("sitegateway_admin_main_integration", "sitegateway", "New Payment Gateway Integration", "", \'{"route":"admin_default","module":"sitegateway","controller":"integration"}\', "sitegateway_admin_main", "", 40);');