<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WidgetSettings.php 6590 2010-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$db = Engine_Db_Table::getDefaultAdapter();
$sitepage_layout_cover_photo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layout.cover.photo', 1);

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepage_admin_main_activity_feed", "sitepage", "Activity Feed", "",\'{"route":"admin_default","module":"sitepage","controller":"settings","action":"activity-feed"}\', "sitepage_admin_main", "", 1, 0, 998);');

$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitepage_main_location');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitepage_main_location';
    $menu_item->module = 'sitepage';
    $menu_item->label = 'Browse Locations';
    $menu_item->plugin = 'Sitepage_Plugin_Menus::canViewSitepages';
    $menu_item->params = '{"route":"sitepage_general","action":"map"}';
    $menu_item->menu = 'sitepage_main';
    $menu_item->submenu = '';
    $menu_item->order = 4;
    $menu_item->save();
}


$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitepage_main_pinboardbrowse');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitepage_main_pinboardbrowse';
    $menu_item->module = 'sitepage';
    $menu_item->label = 'Pinboard';
    $menu_item->plugin = 'Sitepage_Plugin_Menus::canViewSitepages';
    $menu_item->params = '{"route":"sitepage_general","action":"pinboard-browse"}';
    $menu_item->menu = 'sitepage_main';
    $menu_item->submenu = '';
    $menu_item->order = 3;
    $menu_item->save();
}
$menuitemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
$menuitemsTableName = $menuitemsTable->info('name');

$selectmenuitems = $menuitemsTable->select()
        ->from($menuitemsTableName, array('name'))
        ->where('name =?', 'core_admin_main_plugins_sitepageextensions')
        ->where('module =?', 'sitepage')
        ->limit(1);
$fetchmenuitems = $selectmenuitems->query()->fetchAll();
if (empty($fetchmenuitems)) {
    $menuitems = $menuitemsTable->createRow();
    $menuitems->name = 'core_admin_main_plugins_sitepageextensions';
    $menuitems->module = 'sitepage';
    $menuitems->label = 'SEAO - Directory/Pages - Extensions';
    $menuitems->plugin = Null;
    $menuitems->params = '{"route":"admin_default","module":"sitepage","controller":"extension", "action": "index"}';
    $menuitems->menu = 'core_admin_main_plugins';
    $menuitems->submenu = Null;
    $menuitems->enabled = '1';
    $menuitems->custom = '0';
    $menuitems->order = '999';
    $menuitems->save();
}

$contentTable = Engine_Api::_()->getDbtable('content', 'core');
$contentTableName = $contentTable->info('name');
$pageTable = Engine_Api::_()->getDbtable('pages', 'core');
$pageTableName = $pageTable->info('name');
$selectPage = $pageTable->select()
        ->from($pageTableName, array('page_id'))
        ->where('name =?', 'sitepage_index_home')
        ->limit(1);
$fetchPageId = $selectPage->query()->fetchAll();
if (empty($fetchPageId)) {
    $pageCreate = $pageTable->createRow();
    $pageCreate->name = 'sitepage_index_home';
    $pageCreate->displayname = 'Pages Home';
    $pageCreate->title = 'Pages Home';
    $pageCreate->description = 'This is the page home page.';
    $pageCreate->custom = 0;
    $pageCreate->save();
    $page_id = $pageCreate->page_id;

    //INSERT MAIN CONTAINER
    $mainContainer = $contentTable->createRow();
    $mainContainer->page_id = $page_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

    //INSERT MAIN-MIDDLE CONTAINER
    $mainMiddleContainer = $contentTable->createRow();
    $mainMiddleContainer->page_id = $page_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

    //INSERT MAIN-LEFT CONTAINER
    $mainLeftContainer = $contentTable->createRow();
    $mainLeftContainer->page_id = $page_id;
    $mainLeftContainer->type = 'container';
    $mainLeftContainer->name = 'left';
    $mainLeftContainer->parent_content_id = $container_id;
    $mainLeftContainer->order = 4;
    $mainLeftContainer->save();
    $left_id = $mainLeftContainer->content_id;

    //INSERT MAIN-RIGHT CONTAINER
    $mainRightContainer = $contentTable->createRow();
    $mainRightContainer->page_id = $page_id;
    $mainRightContainer->type = 'container';
    $mainRightContainer->name = 'right';
    $mainRightContainer->parent_content_id = $container_id;
    $mainRightContainer->order = 5;
    $mainRightContainer->save();
    $right_id = $mainRightContainer->content_id;

    //INSERT TOP CONTAINER
    $topContainer = $contentTable->createRow();
    $topContainer->page_id = $page_id;
    $topContainer->type = 'container';
    $topContainer->name = 'top';
    $topContainer->order = 1;
    $topContainer->save();
    $top_id = $topContainer->content_id;

    //INSERT TOP-MIDDLE CONTAINER
    $topMiddleContainer = $contentTable->createRow();
    $topMiddleContainer->page_id = $page_id;
    $topMiddleContainer->type = 'container';
    $topMiddleContainer->name = 'middle';
    $topMiddleContainer->parent_content_id = $top_id;
    $topMiddleContainer->order = 1;
    $topMiddleContainer->save();
    $top_middle_id = $topMiddleContainer->content_id;

    //INSERT "Page of the day" WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.item-sitepage', $left_id, 1, "Page of the day", "true");

    //INSERT NAVIGATION WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.browsenevigation-sitepage', $top_middle_id, 2, '', 'true');
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.horizontal-searchbox-sitepage', $top_middle_id, 3, '', 'true');

    //INSERT ZERO PAGE WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.zeropage-sitepage', $middle_id, 3, '', 'true');

    //INSERT "Featured Pages" WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.slideshow-sitepage', $middle_id, 4, "Featured Pages", "true");

    //INSERT "Categories" WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.categories', $middle_id, 5, "Categories", "true");

    //INSERT RANDOM PAGES WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.recently-popular-random-sitepage', $middle_id, 6, '', 'true');

    //INSERT SEARCH WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.search-sitepage', $right_id, 7, '', 'true');

    //INSERT NEW PAGE WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.newpage-sitepage', $right_id, 8, '', 'true');

    //INSERT "Sponsored Pages" WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.sponsored-sitepage', $right_id, 9, "Sponsored Pages", "true");

    //INSERT TAG CLOUD WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.tagcloud-sitepage', $right_id, 10, '', 'true');

    //INSERT "Recommended Pages" WIDGET
    $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    if (!empty($isModEnabled)) {
        Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'suggestion.common-suggestion', $right_id, 999, '', 'true', '{"title":"Recommended Pages","resource_type":"sitepage","getWidAjaxEnabled":"1","getWidLimit":"5","nomobile":"0","name":"suggestion.common-suggestion"}');
    }

    //INSERT "Most Liked Pages" WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.mostlikes-sitepage', $left_id, 11, "Most Liked Pages", "true");

    //INSERT "Most Followed Pages" WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.mostfollowers-sitepage', $left_id, 12, "Most Followed Pages", "true");

    //INSERT "Most Commented Pages" WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.mostcommented-sitepage', $left_id, 13, "Most Commented Pages", "true");

    //INSERT "Recently Viewed" WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.recentview-sitepage', $left_id, 14, "Recently Viewed", "true");

    //INSERT "Recently Viewed By Friends" WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.recentfriend-sitepage', $left_id, 15, "Recently Viewed By Friends", "true");
}

$selectPage = $pageTable->select()
        ->from($pageTableName, array('page_id'))
        ->where('name =?', 'sitepage_index_index')
        ->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (empty($page_id)) {
    $pageCreate = $pageTable->createRow();
    $pageCreate->name = 'sitepage_index_index';
    $pageCreate->displayname = 'Browse Pages';
    $pageCreate->title = 'Browse Pages';
    $pageCreate->description = 'This is the page browse page.';
    $pageCreate->custom = 0;
    $pageCreate->save();
    $page_id = $pageCreate->page_id;

    //INSERT MAIN CONTAINER
    $mainContainer = $contentTable->createRow();
    $mainContainer->page_id = $page_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

    //INSERT MAIN - MIDDLE CONTAINER
    $mainMiddleContainer = $contentTable->createRow();
    $mainMiddleContainer->page_id = $page_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

    //INSERT MAIN - RIGHT CONTAINER
    $mainRightContainer = $contentTable->createRow();
    $mainRightContainer->page_id = $page_id;
    $mainRightContainer->type = 'container';
    $mainRightContainer->name = 'right';
    $mainRightContainer->parent_content_id = $container_id;
    $mainRightContainer->order = 5;
    $mainRightContainer->save();
    $right_id = $mainRightContainer->content_id;

    //INSERT TOP CONTAINER
    $topContainer = $contentTable->createRow();
    $topContainer->page_id = $page_id;
    $topContainer->type = 'container';
    $topContainer->name = 'top';
    $topContainer->order = 1;
    $topContainer->save();
    $top_id = $topContainer->content_id;

    //INSERT TOP- MIDDLE CONTAINER
    $topMiddleContainer = $contentTable->createRow();
    $topMiddleContainer->page_id = $page_id;
    $topMiddleContainer->type = 'container';
    $topMiddleContainer->name = 'middle';
    $topMiddleContainer->parent_content_id = $top_id;
    $topMiddleContainer->order = 6;
    $topMiddleContainer->save();
    $top_middle_id = $topMiddleContainer->content_id;


    //INSERT NAVIGATION WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.browsenevigation-sitepage', $top_middle_id, 1, '', 'true');

    //INSERT NAVIGATION WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.alphabeticsearch-sitepage', $top_middle_id, 2, '', 'true');

    //INSERT PAGES WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.pages-sitepage', $middle_id, 2, '', 'true');

    //INSERT "Categories" WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.categories-sitepage', $right_id, 3, "Categories", "true");

    //INSERT SEARCH PAGE WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.search-sitepage', $right_id, 4, '', 'true');

    //INSERT NEW PAGE WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.newpage-sitepage', $right_id, 5, '', 'true');

    //INSERT "Popular Locations" WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.popularlocations-sitepage', $right_id, 6, "Popular Locations", 'true');

    //INSERT TAG CLOUD WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.tagcloud-sitepage', $right_id, 7, '', 'true');

    //Insert verified pages widget
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.verified-pages', $right_id, 7, '', 'true');
}

include_once APPLICATION_PATH . '/application/modules/Sitepage/controllers/AdminviewpagewidgetController.php';
$selectPage = $pageTable->select()
        ->from($pageTableName, array('page_id'))
        ->where('name =?', 'user_profile_index')
        ->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (!empty($page_id)) {
    $page_id = $page_id[0]['page_id'];
    $selectWidgetId = $contentTable->select()
            ->from($contentTableName, array('content_id'))
            ->where('page_id =?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'core.container-tabs')
            ->limit(1);
    $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
    if (!empty($fetchWidgetContentId)) {
        $tab_id = $fetchWidgetContentId[0]['content_id'];
        $contentWidget = $contentTable->createRow();
        $contentWidget->page_id = $page_id;
        $contentWidget->type = 'widget';
        $contentWidget->name = 'sitepage.profile-sitepage';
        $contentWidget->parent_content_id = $tab_id;
        $contentWidget->order = 999;
        $contentWidget->params = '{"title":"Pages","titleCount":true}';
        $contentWidget->save();
    }
}

$tableContent = Engine_Api::_()->getDbtable('admincontent', 'sitepage');
$tableContentName = $tableContent->info('name');
$select = new Zend_Db_Select($db);
$select_page = $select
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sitepage_index_view')
        ->limit(1);
$page = $select_page->query()->fetchAll();
if (!empty($page)) {
    $page_id = $page[0]['page_id'];
    //INSERT MAIN CONTAINER
    $mainContainer = $tableContent->createRow();
    $mainContainer->page_id = $page_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->admincontent_id;

    //INSERT MAIN-MIDDLE CONTAINER
    $mainMiddleContainer = $tableContent->createRow();
    $mainMiddleContainer->page_id = $page_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->admincontent_id;

    //INSERT MAIN-LEFT CONTAINER
    $mainLeftContainer = $tableContent->createRow();
    $mainLeftContainer->page_id = $page_id;
    $mainLeftContainer->type = 'container';
    $mainLeftContainer->name = 'left';
    $mainLeftContainer->parent_content_id = $container_id;
    $mainLeftContainer->order = 4;
    $mainLeftContainer->save();
    $left_id = $mainLeftContainer->admincontent_id;
    $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showmore', 8);

    //INSERT MAIN-MIDDLE TAB CONTAINER
    $middleTabContainer = $tableContent->createRow();
    $middleTabContainer->page_id = $page_id;
    $middleTabContainer->type = 'widget';
    $middleTabContainer->name = 'core.container-tabs';
    $middleTabContainer->parent_content_id = $middle_id;
    $middleTabContainer->order = 10;
    $middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
    $middleTabContainer->save();
    $middle_tab = $middleTabContainer->admincontent_id;

    //INSERTING THUMB PHOTO WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.thumbphoto-sitepage', $middle_id, 3, '', 'true');

    if (empty($sitepage_layout_cover_photo)) {

        //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.page-profile-breadcrumb', $middle_id, 1, '', 'true');

        //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
            Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepagemember.pagecover-photo-sitepagemembers', $middle_id, 2, '', 'true');
        }



        //INSERTING TITLE WIDGET
        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.title-sitepage', $middle_id, 4, '', 'true');

        //INSERTING LIKE WIDGET
        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'seaocore.like-button', $middle_id, 5, '', 'true');

        //INSERTING FOLLOW WIDGET
        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'seaocore.seaocore-follow', $middle_id, 6, '', 'true');

        //INSERTING FACEBOOK LIKE WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
            Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'Facebookse.facebookse-commonlike', $middle_id, 7, '', 'true');
        }

        //INSERTING MAIN PHOTO WIDGET
        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.mainphoto-sitepage', $left_id, 10, '', 'true');
    } else {

        //INSERTING PAGE PROFILE PAGE COVER PHOTO WIDGET
        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.page-profile-breadcrumb', $middle_id, 1, '', 'true');

        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.page-cover-information-sitepage', $middle_id, 2, '', 'true');
    }

    //INSERTING CONTACT DETAIL WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.contactdetails-sitepage', $middle_id, 8, '', 'true');

//   //INSERTING PHOTO STRIP WIDGET
//   if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
//     Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.photorecent-sitepage', $middle_id, 9, '', 'true');
//   }
    //INSERTING OPTIONS WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.options-sitepage', $left_id, 11, '', 'true');

    //INSERTING INFORMATION WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.information-sitepage', $left_id, 10, 'Information', 'true');

    //INSERTING WRITE SOMETHING ABOUT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

    //INSERTING RATING WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', $left_id, 16, 'Ratings', 'true');
    }

    //INSERTING BADGE WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge')) {
        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', $left_id, 17, 'Badge', 'true');
    }

    //INSERTING YOU MAY ALSO LIKE WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.suggestedpage-sitepage', $left_id, 18, 'You May Also Like', 'true');

    $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitepage.socialshare-sitepage"}';

    //INSERTING SOCIAL SHARE WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.socialshare-sitepage', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

//  //INSERTING FOUR SQUARE WIDGET
//  Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.foursquare-sitepage', $left_id, 20, '', 'true');
    //INSERTING INSIGHTS WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.insights-sitepage', $left_id, 21, 'Insights', 'true');

    //INSERTING FEATURED OWNER WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.featuredowner-sitepage', $left_id, 22, 'Owners', 'true');

    //INSERTING ALBUM WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.albums-sitepage', $left_id, 23, 'Albums', 'true');
    }

    //INSERTING PAGE PROFILE PLAYER WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepagemusic.profile-player', $left_id, 24, '', 'true');
    }

    //INSERTING 'Linked Pages' WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.favourite-page', $left_id, 25, 'Linked Pages', '', 'true');

    //INSERTING ACTIVITY FEED WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
        $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true', $advanced_activity_params);
    } else {
        Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'activity.feed', $middle_tab, 2, 'Updates', 'true');
    }

    //INSERTING INFORAMTION WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.info-sitepage', $middle_tab, 3, 'Info', 'true');

    //INSERTING OVERVIEW WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.overview-sitepage', $middle_tab, 4, 'Overview', 'true');

    //INSERTING LOCATION WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'sitepage.location-sitepage', $middle_tab, 5, 'Map', 'true');

    //INSERTING LINKS WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($tableContent, $tableContentName, $page_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');
}

// Work for advancedactivity feed plugin(feed widget place by default).
$aafModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled(
        'advancedactivity');
//Quary for update widget of seaocore activity feed.
if (!empty($aafModuleEnabled)) {
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_content')->where('name = ?', 'seaocore.feed');
    $results = $select->query()->fetchAll();
    if (!empty($results)) {
        foreach ($results as $result) {
            $params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
            $db->query('UPDATE  `engine4_core_content` SET  `name` =  "advancedactivity.home-feeds",
`params`=\'' . $params . '\' WHERE `engine4_core_content`.`name` ="seaocore.feed";');
        }
    }

    $db->query("INSERT IGNORE INTO `engine4_advancedactivity_contents` ( `module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES ('sitepage', 'sitepage', 'Pages', '1', '999', '1')");
    $db->query("INSERT IGNORE INTO `engine4_advancedactivity_customtypes` ( `module_name`, `resource_type`, `resource_title`, `enabled`, `order`, `default`) VALUES ('sitepage', 'sitepage_page', 'Pages', '1', '999', '1')");
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'siteevent')
        ->where('enabled = ?', 1);
$is_siteevent_object = $select->query()->fetchObject();
if ($is_siteevent_object) {
    $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_page_host", "siteevent", \'{item:$subject} has made your page {var:$page} host of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.\', "");');
    $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES("SITEEVENT_PAGE_HOST", "siteevent", "[host],[email],[sender],[event_title_with_link],[event_url],[page_title_with_link]");');
    $db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Events', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_manage", "siteevent", "Manage Events", "", \'{"uri":"admin/siteevent/manage/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 1, 0, 24);');
    $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "siteevent.event.leader.owner.sitepage.page", "1");');
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitevideointegration')
        ->where('enabled = ?', 1);
$is_sitevideointegration_object = $select->query()->fetchObject();
if ($is_sitevideointegration_object) {
    $db->query("INSERT IGNORE INTO `engine4_sitevideo_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Videos', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_managevideo", "sitevideointegration", "Manage Videos", "", \'{"uri":"admin/sitevideo/manage-video/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 0, 0, 24);');
    $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitevideo.video.leader.owner.sitepage.page", "1");');
}



$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', "sitepage_index_manage")
        ->limit(1);
$info = $select->query()->fetch();

if (empty($info)) {
    $db->insert('engine4_core_pages', array(
        'name' => "sitepage_index_manage",
        'displayname' => "Directory / Pages - Manage Pages",
        'title' => "My Pages",
        'description' => 'This page lists a user\'s Pages\'s.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    // Insert main
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
    ));
    $top_id = $db->lastInsertId();

    // Insert top-middle
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
        'order' => 6,
    ));
    $top_middle_id = $db->lastInsertId();

    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitepage.browsenevigation-sitepage',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 3,
    ));

    // Insert main
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
    ));
    $main_id = $db->lastInsertId();

    // Insert main-middle
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 6,
    ));
    $main_middle_id = $db->lastInsertId();

    // Insert main-middle
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 5,
    ));
    $right_id = $db->lastInsertId();

    // Insert content
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitepage.manage-sitepage',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 5,
    ));

    // Insert content
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitepage.links-sitepage',
        'page_id' => $page_id,
        'parent_content_id' => $right_id,
        'order' => 1,
    ));

    // Insert content
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitepage.manage-search-sitepage',
        'page_id' => $page_id,
        'parent_content_id' => $right_id,
        'order' => 2,
    ));

    // Insert content
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitepage.newpage-sitepage',
        'page_id' => $page_id,
        'parent_content_id' => $right_id,
        'order' => 3,
    ));
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'documentintegration')
        ->where('enabled = ?', 1);
$is_documentintegration_object = $select->query()->fetchObject();
if ($is_documentintegration_object) {
    $db->query("INSERT IGNORE INTO `engine4_document_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Documents' ) ");
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_managedocument", "documentintegration", "Manage Documents", "", \'{"uri":"admin/document/manage-document/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 0, 0, 25);');
    $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "document.leader.owner.sitepage.page", "1");');
}
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitecrowdfunding')
        ->where('enabled = ?', 1);
$crowdfundingEnabled = $select->query()->fetchObject();
if(!empty($crowdfundingEnabled)) {
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitecrowdfundingintegration')
            ->where('enabled = ?', 1);
    $is_sitecrowdfundingintegration_object = $select->query()->fetchObject();
    if ($is_sitecrowdfundingintegration_object) {
        $db->query("INSERT IGNORE INTO `engine4_sitecrowdfunding_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Projects', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_manageproject", "sitecrowdfunding", "Manage Projects", "", \'{"uri":"admin/sitecrowdfunding/manage/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 0, 0, 24);');
        $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitecrowdfunding.project.leader.owner.sitepage.page", "1");');
    }
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitemusic')
        ->where('enabled = ?', 1);
$sitemusicEnabled = $select->query()->fetchObject();
if(!empty($sitemusicEnabled)) {
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemusic')
            ->where('enabled = ?', 1);
    $is_sitemusic_object = $select->query()->fetchObject();
    if ($is_sitemusic_object) {
        $db->query("INSERT IGNORE INTO `engine4_sitemusic_modules` (`resource_type`, `resource_id`, `resource_module`, `enabled`, `integrated`, `resource_title`, `resource_membertype`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Music', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_managemusic", "sitemusic", "Manage Music", "", \'{"uri":"admin/sitemusic/manage/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 0, 0, 24);');
        $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitemusic.playlist.leader.owner.sitepage.page", "1");');
    }
}

            //RENAME THE MENU IN ADMIN SITE SETTINGS
            $db = Engine_Db_Table::getDefaultAdapter();
            $check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
            $check_name = $check_table->info('name');
            $select = $check_table->select()
                    ->from($check_name, array('id'))
                    ->where('name = ?', 'sitepage_admin_main_package');
            $queary_info = $select->query()->fetchAll();
            if (!empty($queary_info)) {
                echo "Hi";
                $menu_item = $check_table->update(array(
				      'label' => 'Packages',
				    ), array(
				      '`name` = ?' => 'sitepage_admin_main_package',
				    ));
               // $menu_item->save();
            }

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES (\'sitepage_admin_main_verify\', \'sitepage\', \'Manage Verifications\', \'\', \'{"route":"admin_default","module":"sitepage","controller":"verify","action":"index"}\', \'sitepage_admin_main\', \'\', 1, 0, 100), (\'sitepage_admin_main_verify_settings\', \'sitepage\', \'General Settings\', \'\', \'{"route":"admin_default","module":"sitepage","controller":"verify","action":"index"}\', \'sitepage_admin_main_verify\', \'\', 1, 0, 1), (\'sitepage_admin_main_verify_manage\', \'sitepage\', \'Manage Verifications\', \'\', \'{"route":"admin_default","module":"sitepage","controller":"verify","action":"manage"}\', \'sitepage_admin_main_verify\', \'\', 1, 0, 2), (\'sitepage_admin_main_verify_approve\', \'sitepage\', \'Approve Verifications\', \'\', \'{"route":"admin_default","module":"sitepage","controller":"verify","action":"approve"}\', \'sitepage_admin_main_verify\', \'\', 1, 0, 3);');

$db->query('CREATE TABLE IF NOT EXISTS `engine4_sitepage_verifies` (`verify_id` int(11) unsigned NOT NULL auto_increment,`resource_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,`resource_id` int(11) unsigned NOT NULL,`poster_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,`poster_id` int(11) unsigned NOT NULL,`status` int(11) unsigned NOT NULL DEFAULT \'1\',`comments` text COLLATE utf8_unicode_ci,`admin_approve` int(11) unsigned NOT NULL DEFAULT \'1\',`creation_date` datetime NOT NULL,PRIMARY KEY  (`verify_id`),KEY `resource_type` (`resource_type`, `resource_id`),KEY `poster_type` (`poster_type`, `poster_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES (\'sitepage_verify_new\', \'sitepage\', \'Your Page {item:$object} have been verified by {item:$subject}.\', 0, \'\', 1), (\'sitepage_verify_admin_approve\', \'sitepage\', \'Site administrator has approved your verification to {item:$subject}.\', 0, \'\', 1), (\'sitepage_verify_user_request\', \'sitepage\', \'{item:$subject} has requested you to approve verification request.\', 0, \'\', 1);');

$select = new Zend_Db_Select($db);
$advancedactivity = $select->from('engine4_core_modules', 'name')
        ->where('name = ?', 'advancedactivity')
        ->query()
        ->fetchcolumn();
if (!empty($advancedactivity)) {
    $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes`(`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `editable`, `is_generated`, `is_grouped`, `is_object_thumb`) VALUES (\'sitepage_verify_new\',\'sitepage\',\'{item:$object} is verified by {item:$subject}.\',1,5,1,1,1,0,1,0,0);');
} else {
    $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES (\'sitepage_verify_new\',\'sitepage\',\'{item:$object} is verified by {item:$subject}.\',1,5,1,1,1,1);');
}


// On install verification functionality
$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
  ('sitepage.verify.enabled',1);");

// Insert the verify button widget on profile
$select = new Zend_Db_Select($db);
$page_select = $select->from('engine4_core_pages')
    ->where('name = ?', 'sitepage_index_view')
    ->limit(1);
$page_id = $page_select->query()->fetchObject()->page_id;
if (!empty($page_id)) {
    $select = new Zend_Db_Select($db);
    $container_select = $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'container')
            ->where('name = ?', 'main')
            ->limit(1);
    $container = $container_select->query()->fetchAll();
    if (!empty($container)) {
        $select = new Zend_Db_Select($db);
        $main_container_id = $container[0]['content_id'];
        $container_select = $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'container')
            ->where('parent_content_id = ?',$main_container_id)
            ->where('name = ?', 'right')
            ->limit(1);
        $container = $container_select->query()->fetchAll();

        if (!empty($container)) {
            $right_container_id = $container[0]['content_id'];

            // add widget on profile
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.verify-button',
                'parent_content_id' => $right_container_id,
                'order' => 1,
                'params' => '{"title":""}',
            ));
        }

    }
}

//RENAME THE MENU IN ADMIN SITE SETTINGS
$db = Engine_Db_Table::getDefaultAdapter();
$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitepage_admin_main_package');
$queary_info = $select->query()->fetchAll();
if (!empty($queary_info)) {
    echo "Hi";
    $menu_item = $check_table->update(array(
  'label' => 'Packages',
), array(
  '`name` = ?' => 'sitepage_admin_main_package',
));
   // $menu_item->save();
}

?>
