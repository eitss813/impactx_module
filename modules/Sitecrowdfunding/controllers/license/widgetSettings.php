<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: widgetSettings.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$db = Engine_Db_Table::getDefaultAdapter();
$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');
$i = 0;
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitecrowdfunding_main_home');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitecrowdfunding_main_home';
    $menu_item->module = 'sitecrowdfunding';
    $menu_item->label = 'Projects Home';
    $menu_item->plugin = 'Sitecrowdfunding_Plugin_Menus::canView';
    $menu_item->params = '{"route":"sitecrowdfunding_general","action":"index"}';
    $menu_item->menu = 'sitecrowdfunding_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitecrowdfunding_main_browse');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitecrowdfunding_main_browse';
    $menu_item->module = 'sitecrowdfunding';
    $menu_item->label = 'Browse Projects';
    $menu_item->plugin = 'Sitecrowdfunding_Plugin_Menus::canView';
    $menu_item->params = '{"route":"sitecrowdfunding_project_general","action":"browse"}';
    $menu_item->menu = 'sitecrowdfunding_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitecrowdfunding_main_manage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitecrowdfunding_main_manage';
    $menu_item->module = 'sitecrowdfunding';
    $menu_item->label = 'My Projects';
    $menu_item->plugin = 'Sitecrowdfunding_Plugin_Menus::canManage';
    $menu_item->params = '{"route":"sitecrowdfunding_project_general","action":"manage"}';
    $menu_item->menu = 'sitecrowdfunding_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitecrowdfunding_main_create');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitecrowdfunding_main_create';
    $menu_item->module = 'sitecrowdfunding';
    $menu_item->label = 'Create a Project';
    $menu_item->plugin = 'Sitecrowdfunding_Plugin_Menus::canCreate';
    $menu_item->params = '{"route":"sitecrowdfunding_project_general","action":"create"}';
    $menu_item->menu = 'sitecrowdfunding_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitecrowdfunding_main_categories');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitecrowdfunding_main_categories';
    $menu_item->module = 'sitecrowdfunding';
    $menu_item->label = 'Categories';
    $menu_item->plugin = 'Sitecrowdfunding_Plugin_Menus::canView';
    $menu_item->params = '{"route":"sitecrowdfunding_general","action":"categories"}';
    $menu_item->menu = 'sitecrowdfunding_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitecrowdfunding_main_pinboard');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitecrowdfunding_main_pinboard';
    $menu_item->module = 'sitecrowdfunding';
    $menu_item->label = 'Pinboard';
    $menu_item->plugin = 'Sitecrowdfunding_Plugin_Menus::canView';
    $menu_item->params = '{"route":"sitecrowdfunding_project_general","action":"pinboard"}';
    $menu_item->menu = 'sitecrowdfunding_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitecrowdfunding_main_location');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitecrowdfunding_main_location';
    $menu_item->module = 'sitecrowdfunding';
    $menu_item->label = 'Locations';
    $menu_item->plugin = 'Sitecrowdfunding_Plugin_Menus::canViewLocation';
    $menu_item->params = '{"route":"sitecrowdfunding_project_general","action":"map"}';
    $menu_item->menu = 'sitecrowdfunding_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;

    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitecrowdfunding_main_projectownerfaq');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitecrowdfunding_main_projectownerfaq';
    $menu_item->module = 'sitecrowdfunding';
    $menu_item->label = 'FAQs for Project Owner';
    $menu_item->plugin = 'Sitecrowdfunding_Plugin_Menus::canViewProjectOwnerFaq';
    $menu_item->params = '{"route":"sitecrowdfunding_general","action":"project-owner-faq"}';
    $menu_item->menu = 'sitecrowdfunding_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;

    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitecrowdfunding_main_backersfaq');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitecrowdfunding_main_backersfaq';
    $menu_item->module = 'sitecrowdfunding';
    $menu_item->label = 'FAQs for Backers';
    $menu_item->plugin = 'Sitecrowdfunding_Plugin_Menus::canViewBackersFaq';
    $menu_item->params = '{"route":"sitecrowdfunding_general","action":"backers-faq"}';
    $menu_item->menu = 'sitecrowdfunding_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}
Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->uploadDefaultImages();
Engine_Api::_()->getDbtable('projects', 'sitecrowdfunding')->createDefaultProjects();

//Advanced Search plugin work
$select = new Zend_Db_Select($db);
$select->from('engine4_core_modules')
        ->where('name = ?', 'siteadvsearch');
$is_enabled = $select->query()->fetchObject();
if (!empty($is_enabled)) {

    $containerCount = 0;
    $widgetCount = 0;
    $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'siteadvsearch_index_browse-page_sitecrowdfunding_project')
            ->limit(1)
            ->query()
            ->fetchColumn();
    if (!$page_id) {
        $db->insert('engine4_core_pages', array(
            'name' => 'siteadvsearch_index_browse-page_sitecrowdfunding_project',
            'displayname' => 'Advanced Search - SEAO - Projects',
            'title' => '',
            'description' => '',
            'custom' => 0,
        ));
        $page_id = $db->lastInsertId();

        //TOP CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'top',
            'page_id' => $page_id,
            'order' => $containerCount++,
        ));
        $top_container_id = $db->lastInsertId();

        //MAIN CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'main',
            'page_id' => $page_id,
            'order' => $containerCount++,
        ));
        $main_container_id = $db->lastInsertId();

        //INSERT TOP-MIDDLE
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $top_container_id,
            'order' => $containerCount++,
        ));
        $top_middle_id = $db->lastInsertId();

        //RIGHT CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'right',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => $containerCount++,
        ));
        $right_container_id = $db->lastInsertId();

        //MAIN-MIDDLE CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => $containerCount++,
        ));
        $main_middle_id = $db->lastInsertId();

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitecrowdfunding.search-project-sitecrowdfunding',
            'parent_content_id' => $right_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"Search Projects","titleCount":true,"viewType":"horizontal","showAllCategories":"1","locationDetection":"0","nomobile":"0","name":"sitecrowdfunding.search-project-sitecrowdfunding"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitecrowdfunding.browse-projects',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"projectOption":["title","owner","creationDate","backer","like","favourite","comment","endDate","featured","sponsored","location","facebook","twitter","linkedin","googleplus"],"projectType":"0","selectProjects":"all","viewType":["gridView","listView"],"defaultViewType":"gridView","gridViewWidth":"283","gridViewHeight":"510","orderby":"featuredSponsored","show_content":"2","gridItemCountPerPage":"12","listItemCountPerPage":"9","titleTruncationGridView":"25","titleTruncationListView":"55","descriptionTruncation":"175","detactLocation":"0","defaultLocationDistance":"1000","truncationLocation":"30","nomobile":"0","name":"sitecrowdfunding.browse-projects"}',
        ));
    }
}

    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitecrowdfunding_admin_main_general", "sitecrowdfunding", "General Settings", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"settings"}\', "sitecrowdfunding_admin_main_settings", "", 5),
("sitecrowdfunding_admin_main_createedit", "sitecrowdfunding", "Miscellaneous Settings", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"settings","action":"create-edit"}\', "sitecrowdfunding_admin_main_settings", "", 10),
("sitecrowdfunding_admin_main_projectownerfaq", "sitecrowdfunding", "FAQs for Project Owner", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"settings","action":"project-owner-faq"}\', "sitecrowdfunding_admin_main_settings", "", 15),
("sitecrowdfunding_admin_main_backersfaq", "sitecrowdfunding", "FAQs for Backers", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"settings","action":"backers-faq"}\', "sitecrowdfunding_admin_main_settings", "", 20),
("sitecrowdfunding_admin_main_level", "sitecrowdfunding", "Member Level Settings", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"settings","action":"level"}\', "sitecrowdfunding_admin_main", "", 10),
("sitecrowdfunding_admin_main_categories", "sitecrowdfunding", "Categories", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"settings", "action":"categories"}\', "sitecrowdfunding_admin_main", "", 15),
("sitecrowdfunding_admin_main_fields", "sitecrowdfunding", "Profile Fields", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"fields"}\', "sitecrowdfunding_admin_main", "", 20),
("sitecrowdfunding_admin_main_profilemaps", "sitecrowdfunding", "Category-Project Profile Mapping", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"profilemaps","action":"manage"}\', "sitecrowdfunding_admin_main", "", 25),
("sitecrowdfunding_admin_main_packagesettings", "sitecrowdfunding", "Package Settings", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"packages"}\', "sitecrowdfunding_admin_main_package", "", 5),
("sitecrowdfunding_admin_main_packagemanage", "sitecrowdfunding", "Manage Packages", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"packages","action":"manage"}\', "sitecrowdfunding_admin_main_package", "", 10),
("sitecrowdfunding_admin_main_packagetransactions", "sitecrowdfunding", "Package Transactions", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"packages","action":"package-transactions"}\', "sitecrowdfunding_admin_main_package", "", 30),
("sitecrowdfunding_admin_main_shippinglocation", "sitecrowdfunding", "Shipping Locations", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"location"}\', "sitecrowdfunding_admin_main", "", 35),
("sitecrowdfunding_admin_main_payment", "sitecrowdfunding", "Payment Settings", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"settings","action":"payment"}\', "sitecrowdfunding_admin_main", "",40),
("sitecrowdfunding_admin_main_projectsearchform", "sitecrowdfunding", "Search Form Settings", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"settings","action":"project-form-search"}\', "sitecrowdfunding_admin_main", "", 50),
("sitecrowdfunding_admin_main_manage", "sitecrowdfunding", "Manage Projects", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"manage"}\', "sitecrowdfunding_admin_main", "", 55),
("sitecrowdfunding_admin_main_backers", "sitecrowdfunding", "Manage Backers", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"manage","action":"backers"}\', "sitecrowdfunding_admin_main", "", 60),
("sitecrowdfunding_admin_main_managerewards", "sitecrowdfunding", "Manage Rewards", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"manage","action":"manage-rewards"}\', "sitecrowdfunding_admin_main", "", 65),
("sitecrowdfunding_admin_main_package", "sitecrowdfunding", "Packages", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"packages"}\', "sitecrowdfunding_admin_main", "", 70),
("sitecrowdfunding_admin_main_commissions", "sitecrowdfunding", "Commissions", "Sitecrowdfunding_Plugin_Menus::showAdminCommissionTab", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"manage","action":"commission"}\', "sitecrowdfunding_admin_main", "", 75), 
("sitecrowdfunding_admin_main_transactions", "sitecrowdfunding", "Transactions", "Sitecrowdfunding_Plugin_Menus::showAdminTransactionsTab", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"packages","action":"package-transactions"}\', "sitecrowdfunding_admin_main", "", 76), 
("sitecrowdfunding_admin_main_paymentrequests", "sitecrowdfunding", "Payment Requests", "Sitecrowdfunding_Plugin_Menus::showAdminPaymentRequestTab", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"payment"}\', "sitecrowdfunding_admin_main", "", 77),
("sitecrowdfunding_admin_main_modules", "sitecrowdfundingintegration", "Manage Modules", "", \'{"route":"admin_default","module":"sitecrowdfundingintegration","controller":"modules","action":"index"}\', "sitecrowdfunding_admin_main", "", 79),
("sitecrowdfunding_admin_main_statistics_report", "sitecrowdfunding", "Statistics & Report", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"settings","action":"statistic"}\', "sitecrowdfunding_admin_main", "", 80),
("sitecrowdfunding_admin_main_statistics", "sitecrowdfunding", "Statistics", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"settings","action":"statistic"}\', "sitecrowdfunding_admin_main_statistics_report", "", 5),
("sitecrowdfunding_admin_main_reports", "sitecrowdfunding", "Funding Reports", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"report","action":"index"}\', "sitecrowdfunding_admin_main_statistics_report", "", 10),
("sitecrowdfunding_admin_main_reminder_mails", "sitecrowdfunding", "Reminder Emails", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"mails","action":"index"}\', "sitecrowdfunding_admin_main", "", 85),
("sitecrowdfunding_admin_main_landingpage_setup", "sitecrowdfunding", "Landing Page Setup", "", \'{"route":"admin_default","module":"sitecrowdfunding","controller":"settings","action":"landing-page-setup"}\', "sitecrowdfunding_admin_main", "", 87),
("sitecrowdfunding_profile_edit", "sitecrowdfunding", "Edit Project", "Sitecrowdfunding_Plugin_Menus", "", "sitecrowdfunding_project_profile", "", 1),
("sitecrowdfunding_profile_delete", "sitecrowdfunding", "Delete Project", "Sitecrowdfunding_Plugin_Menus", "", "sitecrowdfunding_project_profile", "", 2),
("sitecrowdfunding_profile_getlink", "sitecrowdfunding", "Get Link", "Sitecrowdfunding_Plugin_Menus", "", "sitecrowdfunding_project_profile", "", 3),
("core_main_sitecrowdfunding", "sitecrowdfunding", "Projects","", \'{"route":"sitecrowdfunding_project_general","action":"index"}\', "core_main", "","10");
');
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
("sitecrowdfunding_dashboard_editinfo", "sitecrowdfunding", "Edit Info", "Sitecrowdfunding_Plugin_Dashboardmenus",\'{"route":"sitecrowdfunding_specific", "action":"edit"}\', "sitecrowdfunding_dashboard_content", NULL, "1", "0", "10"),
("sitecrowdfunding_dashboard_overview", "sitecrowdfunding", "Overview", "Sitecrowdfunding_Plugin_Dashboardmenus",\'{"route":"sitecrowdfunding_dashboard", "action":"overview"}\', "sitecrowdfunding_dashboard_content", NULL, "1", "0", "20"),
("sitecrowdfunding_dashboard_profilepicture", "sitecrowdfunding", "Profile Picture", "Sitecrowdfunding_Plugin_Dashboardmenus",\'{"route":"sitecrowdfunding_dashboard", "action":"change-photo"}\', "sitecrowdfunding_dashboard_content", NULL, "1", "0", "30"),
("sitecrowdfunding_dashboard_settings", "sitecrowdfunding", "Settings", "Sitecrowdfunding_Plugin_Dashboardmenus",\'{"route":"sitecrowdfunding_dashboard", "action":"set-settings"}\', "sitecrowdfunding_dashboard_content", NULL, "1", "0", "35"),
("sitecrowdfunding_dashboard_editlocation", "sitecrowdfunding", "Location", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_content", NULL, "1", "0", "40"),
("sitecrowdfunding_dashboard_editphoto", "sitecrowdfunding", "Manage Photos", "Sitecrowdfunding_Plugin_Dashboardmenus",\'{"route":"sitecrowdfunding_albumspecific"}\', "sitecrowdfunding_dashboard_content", NULL, "1", "0", "50"),
("sitecrowdfunding_dashboard_uploadvideo", "sitecrowdfunding", "Videos", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_content", NULL, "1", "0", "60"),
("sitecrowdfunding_dashboard_aboutyou", "sitecrowdfunding", "About You", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_content", NULL, "1", "0", "70"),
("sitecrowdfunding_dashboard_rewards", "sitecrowdfunding", "Rewards", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_content", NULL, "1", "0", "90"),
("sitecrowdfunding_dashboard_announcements", "sitecrowdfunding", "Announcement", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_content", NULL, "1", "0", "100"),
("sitecrowdfunding_dashboard_editmetakeyword", "sitecrowdfunding", "Meta Keywords", "Sitecrowdfunding_Plugin_Dashboardmenus",\'{"route":"sitecrowdfunding_dashboard", "action":"meta-detail"}\', "sitecrowdfunding_dashboard_content", NULL, "1", "0", "110"),
("sitecrowdfunding_dashboard_manageleaders", "sitecrowdfunding", "Manage Leaders", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_admin", NULL, "1", "0", "120"),
("sitecrowdfunding_dashboard_packages", "sitecrowdfunding", "Package", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_projects", NULL, "1", "0", "130"),
("sitecrowdfunding_dashboard_paymentaccount", "sitecrowdfunding", "Payment Account", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_projects", NULL, "1", "0", "140"),
("sitecrowdfunding_dashboard_paymentmethod", "sitecrowdfunding", "Payment Methods", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_projects", NULL, "1", "0", "140"),
("sitecrowdfunding_dashboard_paymentrequests", "sitecrowdfunding", "Payment Requests", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_projects", NULL, "1", "0", "141"),
("sitecrowdfunding_dashboard_yourbill", "sitecrowdfunding", "Commissions Bill", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_projects", NULL, "1", "0", "141"),
("sitecrowdfunding_dashboard_kycupload", "sitecrowdfunding", "Upload KYC", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_projects", NULL, "1", "0", "142"),
("sitecrowdfunding_dashboard_backersreport", "sitecrowdfunding", "Backers Report", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_projects", NULL, "1", "0", "150"),
("sitecrowdfunding_dashboard_transactiondetails", "sitecrowdfunding", "Transaction Details", "Sitecrowdfunding_Plugin_Dashboardmenus","", "sitecrowdfunding_dashboard_projects", NULL, "1", "0", "160");');

    $db->query('INSERT IGNORE INTO `engine4_core_menus`(`name`,`type`,`title`,`order`) values
("sitecrowdfunding_main","standard","Projects Main Navigation",999),
("sitecrowdfunding_dashboard_content","standard","Projects - Dashboard Navigation (Content)",999),
("sitecrowdfunding_dashboard_admin","standard","Projects - Dashboard Navigation (Admin)",999),
("sitecrowdfunding_dashboard_projects","standard","Projects - Dashboard Navigation (Transaction)",999);');
    $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
("sitecrowdfunding_discussion_reply", "sitecrowdfunding", "{item:$subject} has {item:$object:posted} on a {itemParent:$object::project topic} you posted on.", 0, "", 1),
("sitecrowdfunding_discussion_response", "sitecrowdfunding", "{item:$subject} has {item:$object:posted} on a {itemParent:$object::project topic} you created.", 0, "", 1);');
    $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitecrowdfunding_project_back", "sitecrowdfunding", \'{item:$subject} <span class = "activity_feed_backed_amount" >backed {var:$amount}</span> on the project {item:$object}\', 1, 5, 1, 3, 1, 1);');
    $db->query('INSERT INTO `engine4_sitecrowdfunding_packages` (`package_id`, `title`, `description`, `level_id`, `price`, `recurrence`, `recurrence_type`, `duration`, `duration_type`, `sponsored`, `featured`, `overview`, `video`, `video_count`, `photo`, `photo_count`, `approved`, `enabled`, `defaultpackage`, `renew`, `renew_before`, `order`, `update_list`, `commission_settings`) 
VALUES ("1", "Default Package", "This is the default package for all the Projects", "0", "0.00", "0", "forever", "0", "forever", "0", "0", "0", "0", "0", "0", "0", "1", "1", "1", "0", "0", "0", "1", \'a:3:{s:19:"commission_handling";s:1:"1";s:14:"commission_fee";s:1:"1";s:15:"commission_rate";s:1:"1";}\');');
    $templateApi = Engine_Api::_()->getApi('settemplate', 'sitecrowdfunding');
    $templateApi->checkoutPage();
    $templateApi->projectViewPage();
    $templateApi->topicViewPage();
    $templateApi->rewardSelectionPage();
    $templateApi->categoryHomePage();
    $templateApi->browsePage();
    $templateApi->myProjectPage();
    $templateApi->tagscloudPage();
    $templateApi->packagePage();
    $templateApi->projectMapPage();
    $templateApi->projectCreatePage();
    $templateApi->pinboardPage();
    $templateApi->projectHomePage();
    $templateApi->backerSuccessPage();
    $templateApi->backerFaqPage();
    $templateApi->ownerFaqPage();
    $templateApi->setProjectCategories();
