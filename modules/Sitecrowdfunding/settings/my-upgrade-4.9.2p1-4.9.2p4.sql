

-- added transactions tab and updated the commisions tab according to new work done

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitecrowdfunding_admin_main_transactions", "sitecrowdfunding", "Transactions", "Sitecrowdfunding_Plugin_Menus::showAdminTransactionsTab", '{"route":"admin_default","module":"sitecrowdfunding","controller":"packages","action":"package-transactions"}', "sitecrowdfunding_admin_main", "", 76);

UPDATE  `engine4_core_menuitems` SET  `plugin` =  'Sitecrowdfunding_Plugin_Menus::showAdminCommissionTab' WHERE  `engine4_core_menuitems`.`name` ='sitecrowdfunding_admin_main_commissions';
