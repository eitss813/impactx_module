<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: widgetSettings.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitealbum_main_home');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitealbum_main_home';
    $menu_item->module = 'sitealbum';
    $menu_item->label = 'Albums Home';
    $menu_item->plugin = 'Sitealbum_Plugin_Menus::canViewAlbums';
    $menu_item->params = '{"route":"sitealbum_general","action":"index"}';
    $menu_item->menu = 'sitealbum_main';
    $menu_item->submenu = '';
    $menu_item->order = 1;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitealbum_main_browse');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitealbum_main_browse';
    $menu_item->module = 'sitealbum';
    $menu_item->label = 'Browse Albums';
    $menu_item->plugin = 'Sitealbum_Plugin_Menus::canViewAlbums';
    $menu_item->params = '{"route":"sitealbum_general","action":"browse"}';
    $menu_item->menu = 'sitealbum_main';
    $menu_item->submenu = '';
    $menu_item->order = 2;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitealbum_main_location');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitealbum_main_location';
    $menu_item->module = 'sitealbum';
    $menu_item->label = 'Locations';
    $menu_item->plugin = 'Sitealbum_Plugin_Menus::canViewAlbums';
    $menu_item->params = '{"route":"sitealbum_general","module":"sitealbum","action":"map"}';
    $menu_item->menu = 'sitealbum_main';
    $menu_item->submenu = '';
    $menu_item->order = 3;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitealbum_main_pinboard');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitealbum_main_pinboard';
    $menu_item->module = 'sitealbum';
    $menu_item->label = 'Albums Pinboard';
    $menu_item->plugin = 'Sitealbum_Plugin_Menus::canViewAlbums';
    $menu_item->params = '{"route":"sitealbum_general","module":"sitealbum","action":"pinboard"}';
    $menu_item->menu = 'sitealbum_main';
    $menu_item->submenu = '';
    $menu_item->order = 4;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitealbum_main_manage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitealbum_main_manage';
    $menu_item->module = 'sitealbum';
    $menu_item->label = 'My Albums';
    $menu_item->plugin = 'Sitealbum_Plugin_Menus::canCreateAlbums';
    $menu_item->params = '{"route":"sitealbum_general","action":"manage"}';
    $menu_item->menu = 'sitealbum_main';
    $menu_item->submenu = '';
    $menu_item->order = 5;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitealbum_main_upload');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitealbum_main_upload';
    $menu_item->module = 'sitealbum';
    $menu_item->label = 'Add New Photos';
    $menu_item->plugin = 'Sitealbum_Plugin_Menus::canCreateAlbums';
    $menu_item->params = '{"route":"sitealbum_general","action":"upload"}';
    $menu_item->menu = 'sitealbum_main';
    $menu_item->submenu = '';
    $menu_item->order = 6;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitealbum_main_categories');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitealbum_main_categories';
    $menu_item->module = 'sitealbum';
    $menu_item->label = 'Categories';
    $menu_item->plugin = 'Sitealbum_Plugin_Menus::canViewAlbums';
    $menu_item->params = '{"route":"sitealbum_general","action":"categories"}';
    $menu_item->menu = 'sitealbum_main';
    $menu_item->submenu = '';
    $menu_item->order = 7;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitealbum_quick_upload');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitealbum_quick_upload';
    $menu_item->module = 'sitealbum';
    $menu_item->label = 'Add New Photos';
    $menu_item->plugin = 'Sitealbum_Plugin_Menus::canCreateAlbums';
    $menu_item->params = '{"route":"sitealbum_general","action":"upload","class":"buttonlink icon_photos_new"}';
    $menu_item->menu = 'sitealbum_quick';
    $menu_item->submenu = '';
    $menu_item->order = 1;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitealbum_quick_badge');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitealbum_quick_badge';
    $menu_item->module = 'sitealbum';
    $menu_item->label = 'Create Photos Badge';
    $menu_item->plugin = 'Sitealbum_Plugin_Menus::canCreateBadge';
    $menu_item->params = '{"route":"sitealbum_badge","action":"create","class":"buttonlink sitealbum_icon_badge_create"}';
    $menu_item->menu = 'sitealbum_quick';
    $menu_item->submenu = '';
    $menu_item->order = 2;
    $menu_item->save();
}

$db = Zend_Db_Table_Abstract::getDefaultAdapter();

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("core_main_sitealbum", "sitealbum", "Albums", "", \'{"route":"sitealbum_general","action":"index","icon":"fa-image"}\', "core_main", "", 3),
("core_sitemap_sitealbum", "sitealbum", "Albums", "", \'{"route":"sitealbum_general","action":"index"}\', "core_sitemap", "", 3),

("mobi_browse_sitealbum", "sitealbum", "Albums", "", \'{"route":"sitealbum_general","action":"index"}\', "mobi_browse", "", 2),

("sitealbum_admin_submain_album_day", "sitealbum", "Album of the Day", "", \'{"route":"admin_default","module":"sitealbum","controller":"album", "action":"album-of-day"}\', "sitealbum_admin_submain", "", 3),

("sitealbum_admin_submain_photo_day", "sitealbum", "Photo of the Day", "", \'{"route":"admin_default","module":"sitealbum","controller":"photo", "action":"photo-of-day"}\', "sitealbum_admin_submain", "", 4),

( "sitealbum_admin_main_level", "sitealbum", "Member Level Settings", "", \'{"route":"admin_default","module":"sitealbum","controller":"level"}\', "sitealbum_admin_main", "", 2),

("sitealbum_admin_main_manage", "sitealbum", "Widget Settings", "", \'{"route":"admin_default","module":"sitealbum","controller":"album", "action":"album-of-day"}\', "sitealbum_admin_main", "", 3),

( "sitealbum_admin_main_categories", "sitealbum", "Categories", "", \'{"route":"admin_default","module":"sitealbum","controller":"settings", "action":"categories"}\', "sitealbum_admin_main", "", 4),

("sitealbum_admin_main_album_featured", "sitealbum", "Featured Albums", "", \'{"route":"admin_default","module":"sitealbum","controller":"album", "action":"featured"}\', "sitealbum_admin_main", "", 5),

("sitealbum_admin_main_photo_featured", "sitealbum", "Featured Photos", "", \'{"route":"admin_default","module":"sitealbum","controller":"photo", "action":"featured"}\', "sitealbum_admin_main", "", 6),

("sitealbum_admin_main_fields", "sitealbum", "Profile Fields", "", \'{"route":"admin_default","module":"sitealbum","controller":"fields"}\', "sitealbum_admin_main", "", 7),

("sitealbum_admin_main_formsearch", "sitealbum", "Search Form Settings", "", \'{"route":"admin_default","module":"sitealbum","controller":"settings","action":"form-search"}\', "sitealbum_admin_main", "", 8),

( "sitealbum_admin_main_album_manage", "sitealbum", "Manage Albums", "", \'{"route":"admin_default","module":"sitealbum","controller":"manage"}\', "sitealbum_admin_main", "", 9),

("sitealbum_admin_main_profilemaps", "sitealbum", "Category-Album Profile Mapping", "", \'{"route":"admin_default","module":"sitealbum","controller":"profilemaps","action":"manage"}\', "sitealbum_admin_main", "", 10),

( "sitealbum_admin_main_integrations", "sitealbum", "Plugins Integrations", "", \'{"route":"admin_default","module":"sitealbum","controller":"settings","action":"integrations"}\', "sitealbum_admin_main", "", 11),

("sitealbum_admin_main_template", "sitealbum", "Layout Templates", "", \'{"route":"admin_default","module":"sitealbum","controller":"settings", "action":"set-template"}\', "sitealbum_admin_main", "", 13);');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES 
("sitealbum_profile_add", "sitealbum", "Add Photos", "Sitealbum_Plugin_Menus", "", "album_profile", "", 1),
("sitealbum_profile_manage", "sitealbum", "Manage Photos", "Sitealbum_Plugin_Menus", "", "album_profile", "", 2),
("sitealbum_profile_edit", "sitealbum", "Edit", "Sitealbum_Plugin_Menus", "", "album_profile", "", 3),
("sitealbum_profile_delete", "sitealbum", "Delete Album", "Sitealbum_Plugin_Menus", "", "album_profile", "", 4),
("sitealbum_profile_share", "sitealbum", "Share via Badge", "Sitealbum_Plugin_Menus", "", "album_profile", "", 5),
("sitealbum_profile_makealbumoftheday", "sitealbum", "Make Album of the Day", "Sitealbum_Plugin_Menus", "", "album_profile", "", 6), 
("sitealbum_profile_getlink", "sitealbum", "Get Link", "Sitealbum_Plugin_Menus", "", "album_profile", "", 7),
("sitealbum_profile_editlocation", "sitealbum", "Edit Location", "Sitealbum_Plugin_Menus", "", "album_profile", "", 8),
("sitealbum_profile_suggesttofriend", "sitealbum", "Suggest To Friend", "Sitealbum_Plugin_Menus", "", "album_profile", "", 9);');

$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("album_photo_new", "sitealbum", \'{item:$subject} added {var:$count} photo(s) to the album {item:$object}:\', 1, 5, 1, 3, 1, 1),
("comment_album", "sitealbum", \'{item:$subject} commented on {item:$owner}\'\'s {item:$object:album}: {body:$body}\', 1, 1, 1, 1, 1, 1),
("comment_album_photo", "sitealbum", \'{item:$subject} commented on {item:$owner}\'\'s {item:$object:photo}: {body:$body}\', 1, 1, 1, 1, 1, 1);');

$db->query('UPDATE `engine4_core_menuitems` SET `enabled` = "0" WHERE `engine4_core_menuitems`.`name` ="core_main_album" LIMIT 1');
$db->query('UPDATE `engine4_core_menuitems` SET `enabled` = "0" WHERE `engine4_core_menuitems`.`name` ="core_sitemap_album" LIMIT 1');
$db->query('UPDATE `engine4_core_menuitems` SET `enabled` = "0" WHERE `engine4_core_menuitems`.`name` ="mobi_browse_album" LIMIT 1');

//ADVANCED ALBUMS - PHOTO VIEW PAGE
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sitealbum_photo_view')
        ->limit(1);
$info = $select->query()->fetch();
if (empty($info)) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitealbum_photo_view',
        'displayname' => 'Advanced Albums - Photo View Page',
        'title' => 'Advanced Albums - Photo View Page',
        'description' => '> This is the main view page of a photo.',
        'custom' => 0
    ));
    $page_id = $db->lastInsertId('engine4_core_pages');

    // containers
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
    ));
    $container_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $container_id,
        'order' => 5,
        'params' => '',
    ));
    $right_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 6,
        'params' => '',
    ));
    $middle_id = $db->lastInsertId('engine4_core_content');

    // widgets entry
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitealbum.photo-view',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '{"titleCount":"true","itemCountPerPage":4,"title":""}',
    ));

    $select = new Zend_Db_Select($db);
    $sitetagcheckinEnabled = $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitetagcheckin')
            ->where('enabled = ?', '1')
            ->query()
            ->fetchObject();
    if (!empty($sitetagcheckinEnabled)) {
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitetagcheckin.location-suggestions-sitetagcheckin',
            'parent_content_id' => $right_id,
            'order' => 4,
            'params' => '{"title":"Add a Location to Your Photos","titleCount":false}',
        ));
    }

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitealbum.list-popular-photos',
        'parent_content_id' => $right_id,
        'order' => 5,
        'params' => '{"title":"Popular Photos","itemCountPerPage":"2","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"0","popularType":"comment","interval":"overall","photoHeight":"200","photoWidth":"200","photoInfo":["viewCount","likeCount","commentCount","albumTitle"],"truncationLocation":"35","photoTitleTruncation":"22","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-photos"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitealbum.list-popular-photos',
        'parent_content_id' => $right_id,
        'order' => 6,
        'params' => '{"title":"Recent Photos","itemCountPerPage":"2","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","featured":"0","popularType":"modified","interval":"overall","photoHeight":"200","photoWidth":"200","photoInfo":["ownerName","viewCount","likeCount","commentCount","photoTitle"],"truncationLocation":"35","photoTitleTruncation":"22","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.list-popular-photos"}',
    ));
}

// MEMBER PROFILE PAGE
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'user_profile_index')
        ->limit(1);
$page_id = $select->query()->fetchObject()->page_id;


// sitealbum.profile-photos
// Check if it's already been placed
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'widget')
        ->where('name = ?', 'sitealbum.profile-photos');
$info = $select->query()->fetch();
if (empty($info)) {

    // container_id (will always be there)
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'container')
            ->limit(1);
    $container_id = $select->query()->fetchObject()->content_id;

    // middle_id (will always be there)
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content')
            ->where('parent_content_id = ?', $container_id)
            ->where('type = ?', 'container')
            ->where('name = ?', 'middle')
            ->limit(1);
    $middle_id = $select->query()->fetchObject()->content_id;

    // tab_id (tab container) may not always be there
    $select
            ->reset('where')
            ->where('type = ?', 'widget')
            ->where('name = ?', 'core.container-tabs')
            ->where('page_id = ?', $page_id)
            ->limit(1);
    $tab_id = $select->query()->fetchObject();
    if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
    } else {
        $tab_id = null;
    }

    // tab on profile
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitealbum.profile-photos',
        'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        'order' => 4,
        'params' => '{"title":"Photos","titleCount":true,"itemCountPerPage":"12","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","selectDispalyTabs":["yourphotos","photosofyou","albums","likesphotos"],"margin_photo":"2","albumPhotoHeight":"320","albumPhotoWidth":"445","showPhotosInJustifiedView":"1","rowHeight":"250","maxRowHeight":"0","margin":"5","lastRow":"justify","photoHeight":"211","photoWidth":"229","photoColumnHeight":"211","showaddphoto":"1","albumInfo":["creationDate","viewCount","likeCount","commentCount","location","ratingStar","categoryLink","albumTitle","totalPhotos"],"infoOnHover":"1","albumColumnHeight":"255","photoInfo":["creationDate","viewCount","likeCount","commentCount","location","directionLink","ratingStar","likeCommentStrip","photoTitle"],"showPhotosInLightbox":"1","truncationLocation":"100","titleTruncation":"100","nomobile":"0","name":"sitealbum.profile-photos"}',
    ));


    //ADVANCED ALBUMS - MOBILE ADVANCED ALBUM HOME
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'sitealbum_mobi_index')
            ->limit(1);
    $info = $select->query()->fetch();

    if (empty($info)) {
        $db->insert('engine4_core_pages', array(
            'name' => 'sitealbum_mobi_index',
            'displayname' => 'Advanced Albums - Mobile Advanced Album Home',
            'title' => 'Advanced Albums - Mobile Advanced Album Home',
            'description' => 'This is the mobile verison of a album home page.',
            'custom' => 0
        ));
        $page_id = $db->lastInsertId('engine4_core_pages');

        // containers
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'container',
            'name' => 'main',
            'parent_content_id' => null,
            'order' => 1,
            'params' => '',
        ));
        $container_id = $db->lastInsertId('engine4_core_content');

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'container',
            'name' => 'middle',
            'parent_content_id' => $container_id,
            'order' => 2,
            'params' => '',
        ));
        $middle_id = $db->lastInsertId('engine4_core_content');

        // widgets entry
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitealbum.navigation',
            'parent_content_id' => $middle_id,
            'order' => 1,
            'params' => '',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitealbum.search-sitealbum',
            'parent_content_id' => $middle_id,
            'order' => 2,
            'params' => '',
        ));
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitealbum.list-photos-tabs-view',
            'parent_content_id' => $middle_id,
            'order' => 3,
            'params' => '{"title":"Photos"}',
        ));
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitealbum.list-albums-tabs-view',
            'parent_content_id' => $middle_id,
            'order' => 4,
            'params' => '{"title":"Albums"}',
        ));
    }
}

// START ALBUM TAGS PAGE WORK 
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "sitealbum_index_tagscloud")
        ->limit(1)
        ->query()
        ->fetchColumn();
if (empty($page_id)) {

    $containerCount = 0;
    $widgetCount = 0;

    //CREATE PAGE
    $db->insert('engine4_core_pages', array(
        'name' => "sitealbum_index_tagscloud",
        'displayname' => 'Advanced Albums - Album Tags',
        'title' => 'Popular Tags',
        'description' => 'This is the album tags page.',
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
        'name' => 'sitealbum.navigation',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'seaocore.scroll-top',
        'parent_content_id' => $top_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitealbum.tagcloud-sitealbum',
        'parent_content_id' => $main_middle_id,
        'order' => $widgetCount++,
        'params' => '',
    ));
}

$db->query('UPDATE `engine4_core_menuitems` SET `params` = \'{"route":"album_general","action":"upload","class":"seao_smoothbox","data_SmoothboxSEAOClass":"seao_add_photo_lightbox"}\' WHERE `engine4_core_menuitems`.`name` = "album_main_upload" LIMIT 1;');

$db->query('UPDATE `engine4_core_menuitems` SET `params` = \'{"route":"album_general","action":"upload","class":"icon_photos_new seao_smoothbox","data_SmoothboxSEAOClass":"seao_add_photo_lightbox"}\' WHERE `engine4_core_menuitems`.`name` = "album_quick_upload" LIMIT 1;');

$db->query('UPDATE `engine4_core_menuitems` SET `params` = \'{"route":"sitealbum_general","action":"upload","class":"seao_smoothbox","data_SmoothboxSEAOClass":"seao_add_photo_lightbox"}\' WHERE `engine4_core_menuitems`.`name` = "sitealbum_main_upload" LIMIT 1;');

$db->query('UPDATE `engine4_core_menuitems` SET `params` = \'{"route":"sitealbum_general","action":"upload","class":"icon_photos_new seao_smoothbox","data_SmoothboxSEAOClass":"seao_add_photo_lightbox"}\' WHERE `engine4_core_menuitems`.`name` = "sitealbum_quick_upload" LIMIT 1;');


$templateApi = Engine_Api::_()->getApi('settemplate', 'sitealbum');
$templateApi->setAlbumsHomePage(false);
$templateApi->setAlbumsBrowsePage(false);
$templateApi->setAlbumsPhotoBrowsePage(false);
$templateApi->setAlbumsLocationsPage(false);
$templateApi->setAlbumsPinboardViewPage(false);
$templateApi->setAlbumsCategoriesHomePage(false);
$templateApi->setAlbumsManagePage(false);

if (Engine_Api::_()->hasModuleBootstrap('sitecontentcoverphoto')) {
    $templateApi->setAlbumsViewPageWithCoverPhoto(false);
} else {
    $templateApi->setAlbumsViewPageWithoutCoverPhoto(false);
}