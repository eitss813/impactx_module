<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: widgetSettings.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$db = Engine_Db_Table::getDefaultAdapter();


$templateApi = Engine_Api::_()->getApi('settemplate', 'sitevideo');
$templateApi->videoQueries();
$templateApi->videoCategoriesQueries();
if (Engine_Api::_()->hasModuleBootstrap('sitecontentcoverphoto')) {
    $tableName = Engine_Api::_()->getItemtable('sitevideo_channel')->info('name');
    $field = $db->query("SHOW COLUMNS FROM $tableName LIKE 'channel_cover'")->fetch();
    if (empty($field)) {
        $db->query("ALTER TABLE `$tableName` ADD `channel_cover` INT( 11 ) NOT NULL DEFAULT '0'");
    }
}

$db->query('DELETE FROM engine4_core_menuitems WHERE name in("sitevideo_admin_main_video_level","sitevideo_admin_main_level","sitevideo_admin_main_manage","sitevideo_admin_main_video_categories"
,"sitevideo_admin_main_channel_categories"
,"sitevideo_admin_main_fields"
,"sitevideo_admin_main_video_fields"
,"sitevideo_admin_main_profilemaps"
,"sitevideo_admin_main_videoprofilemaps"
,"sitevideo_admin_main_video_featured"
,"sitevideo_admin_main_channel_featured"
,"sitevideo_admin_main_channel_manage"
,"sitevideo_admin_main_channel_manage"
,"sitevideo_admin_main_formsearch"
,"sitevideo_admin_main_utility"
,"sitevideo_admin_main_template"
,"sitevideo_admin_main_integrations"
,"sitevideo_admin_main_module_manage");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitevideo_admin_main_template", "sitevideo", "Layout Templates", NULL, \'{"route":"admin_default","module":"sitevideo","controller":"settings","action":"set-template"}\', "sitevideo_admin_main", NULL,  17),
("sitevideo_admin_main_statistics", "sitevideo", "Statistics", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings","action":"statistic"}\', "sitevideo_admin_main", "", 14),
("sitevideo_admin_main_shorturl", "sitevideo", "Short Channel URL", NULL, \'{"route":"admin_default","module":"sitevideo","controller":"shorturl","action":"index"}\', "sitevideo_admin_main", NULL,  14),
("sitevideo_admin_main_integrations", "sitevideo", "Other Plugin Integrations", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings","action":"integrations"}\', "sitevideo_admin_main", "", 13),
("sitevideo_admin_main_formsearch", "sitevideo", "Search Form Settings", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings","action":"video-form-search"}\', "sitevideo_admin_main", "", 8),
("sitevideo_admin_main_formsearch_video", "sitevideo", "Videos", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings","action":"video-form-search"}\', "sitevideo_admin_main_formsearch", "", 1),
("sitevideo_admin_main_formsearch_channel", "sitevideo", "Channels", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings","action":"form-search"}\', "sitevideo_admin_main_formsearch", "", 2),
("sitevideo_admin_main_channel_manage", "sitevideo", "Manage Channels", "", \'{"route":"admin_default","module":"sitevideo","controller":"manage"}\', "sitevideo_admin_main", "", 9),
("sitevideo_admin_main_video_manage", "sitevideo", "Manage Videos", "", \'{"route":"admin_default","module":"sitevideo","controller":"manage-video"}\', "sitevideo_admin_main", "", 10),
("sitevideo_admin_main_playlist_manage", "sitevideo", "Manage Playlists", "", \'{"route":"admin_default","module":"sitevideo","controller":"manage-playlist"}\', "sitevideo_admin_main", "", 11),
("sitevideo_admin_main_featured", "sitevideo", "Featured Videos / Channels", "", \'{"route":"admin_default","module":"sitevideo","controller":"video", "action":"featured"}\', "sitevideo_admin_main", "", 7),
("sitevideo_admin_main_featured_video", "sitevideo", "Videos", "", \'{"route":"admin_default","module":"sitevideo","controller":"video", "action":"featured"}\', "sitevideo_admin_main_featured", "", 1),
("sitevideo_admin_main_featured_channel", "sitevideo", "Channels", "", \'{"route":"admin_default","module":"sitevideo","controller":"channel", "action":"featured"}\', "sitevideo_admin_main_featured", "", 2),
("sitevideo_admin_main_profilemaps", "sitevideo", "Category Profile Mapping", "", \'{"route":"admin_default","module":"sitevideo","controller":"videoprofilemaps","action":"manage"}\', "sitevideo_admin_main", "", 6),
("sitevideo_admin_main_profilemaps_video", "sitevideo", "Videos", "", \'{"route":"admin_default","module":"sitevideo","controller":"videoprofilemaps","action":"manage"}\', "sitevideo_admin_main_profilemaps", "", 1),
("sitevideo_admin_main_profilemaps_channel", "sitevideo", "Channels", "", \'{"route":"admin_default","module":"sitevideo","controller":"profilemaps","action":"manage"}\', "sitevideo_admin_main_profilemaps", "", 2),
("sitevideo_admin_main_fields", "sitevideo", "Profile Fields", "", \'{"route":"admin_default","module":"sitevideo","controller":"video-fields"}\', "sitevideo_admin_main", "", 5),
("sitevideo_admin_main_fields_video", "sitevideo", "Videos", "", \'{"route":"admin_default","module":"sitevideo","controller":"video-fields"}\', "sitevideo_admin_main_fields", "", 1),
("sitevideo_admin_main_fields_channel", "sitevideo", "Channels", "", \'{"route":"admin_default","module":"sitevideo","controller":"fields"}\', "sitevideo_admin_main_fields", "", 2),
("sitevideo_admin_main_categories", "sitevideo", "Categories", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings", "action":"video-categories"}\', "sitevideo_admin_main", "", 4),
("sitevideo_admin_main_categories_video", "sitevideo", "Videos", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings", "action":"video-categories"}\', "sitevideo_admin_main_categories", "", 1),
("sitevideo_admin_main_categories_channel", "sitevideo", "Channels", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings", "action":"categories"}\', "sitevideo_admin_main_categories", "", 2),
("sitevideo_admin_main_level", "sitevideo", "Member Level Settings", "", \'{"route":"admin_default","module":"sitevideo","controller":"video-level"}\', "sitevideo_admin_main", "", 2),
("sitevideo_admin_main_video_level", "sitevideo", "Videos", "", \'{"route":"admin_default","module":"sitevideo","controller":"video-level"}\', "sitevideo_admin_main_level", "", 1),
("sitevideo_admin_main_channel_level", "sitevideo", "Channels", "", \'{"route":"admin_default","module":"sitevideo","controller":"level"}\', "sitevideo_admin_main_level", "", 2),
("sitevideo_admin_main_general_settings", "sitevideo", "General Settings", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings"}\', "sitevideo_admin_main_settings", "", 1),
("sitevideo_admin_main_video_settings", "sitevideo", "Video Settings", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings","action":"video-settings"}\', "sitevideo_admin_main_settings", "", 2),
("sitevideo_admin_main_channel_settings", "sitevideo", "Channel Settings", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings","action":"channel-settings"}\', "sitevideo_admin_main_settings", "", 3),
("sitevideo_admin_main_utility", "sitevideo", "Video Utilities", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings","action":"utility"}\', "sitevideo_admin_main_settings", "", 4),
("sitevideo_admin_main_htmlblock", "sitevideo", "HTML Block", "", \'{"route":"admin_default","module":"sitevideo","controller":"html-block"}\', "sitevideo_admin_main", "", 13);');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
("sitevideo_dashboard_editinfo", "sitevideo", "Edit Info", "Sitevideo_Plugin_Dashboardmenus", \'{"route":"sitevideo_specific", "action":"edit"}\', "sitevideo_dashboard_content", NULL, "1", "0", "10"),
("sitevideo_dashboard_overview", "sitevideo", "Overview", "Sitevideo_Plugin_Dashboardmenus", \'{"route":"sitevideo_specific", "action":"overview"}\', "sitevideo_dashboard_content", NULL, "1", "0", "20"),
("sitevideo_dashboard_profilepicture", "sitevideo", "Profile Picture", "Sitevideo_Plugin_Dashboardmenus", \'{"route":"sitevideo_dashboard", "action":"change-photo"}\', "sitevideo_dashboard_content", NULL, "1", "0", "40"),
("sitevideo_dashboard_editphoto", "sitevideo", "Manage Photos", "Sitevideo_Plugin_Dashboardmenus", \'{"route":"sitevideo_albumspecific"}\', "sitevideo_dashboard_content", NULL, "1", "0", "70"),
("sitevideo_dashboard_editvideo", "sitevideo", "Manage Videos", "Sitevideo_Plugin_Dashboardmenus", \'{"route":"sitevideo_dashboard"}\', "sitevideo_dashboard_content", NULL, "1", "0", "80"),
("sitevideo_dashboard_editmetakeyword", "sitevideo", "Meta Keywords", "Sitevideo_Plugin_Dashboardmenus", \'{"route":"sitevideo_dashboard", "action":"meta-detail"}\', "sitevideo_dashboard_content", NULL, "1", "0", "95");');

$db->query('INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES ("sitevideo_dashboard_content", "standard", "Advanced Videos - Dashboard Navigation (Content)");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
( "sitevideo_admin_global_url", "sitevideo", "General Settings", "", \'{"route":"admin_default","module":"sitevideo","controller":"shorturl","action":"index"}\', "sitevideo_admin_main_shorturl", "", 1, 0, 1),
("sitevideo_admin_blockurl", "sitevideo", "Banned URLs", "", \'{"route":"admin_default","module":"sitevideo","controller":"shorturl","action":"banningurl"}\', "sitevideo_admin_main_shorturl", "", 1, 0, 2),
("sitevideo_admin_main_shorturl_url", "sitevideo", "Channels with Banned URLs", "", \'{"route":"admin_default","module":"sitevideo","controller":"shorturl","action":"channelurl"}\', "sitevideo_admin_main_shorturl", "", 1, 0, 3);');

$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitevideo_photo_upload", "sitevideo", \'{item:$subject} added {var:$count} photo(s) to the channel {item:$object:$title}: {body:$body}\', 1, 7, 2, 1, 1, 1),
("sitevideo_change_photo", "sitevideo", \'{item:$subject} changed the profile picture of the channel {item:$object:$title}:\', 1, 3, 2, 1, 1, 1);');

$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitevideo_topic_create", "sitevideo", \'{item:$subject} posted a discussion topic {itemSeaoChild:$object:sitevideo_topic:$child_id} in the channel {item:$object}:{body:$body}\', 1, 7, 2, 1, 1, 1),
("sitevideo_topic_reply", "sitevideo", \'{item:$subject} replied to a discussion topic {itemSeaoChild:$object:sitevideo_topic:$child_id} in the channel {item:$object}: {body:$body}\', 1, 3, 2, 1, 1, 1);');

$db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEVIDEO_CREATENOTIFICATION_EMAIL", "sitevideo", "[channel_title],[item_title],[body_content]"),
("SITEVIDEO_CHANNELPOST_EMAIL", "sitevideo", "[channel_title],[item_title],[body_content]"),
("notify_sitevideo_channel_subscribe", "sitevideo", "[channel_title],[user_name]");');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
("sitevideo_discussion_new", "sitevideo", \'{item:$subject} has posted a channel discussion {item:$object} under channel {item:$object:$channel}.\', 0, "", 1),
("sitevideo_discussion_reply", "sitevideo", \'{item:$subject} has {item:$object:posted} on a {itemParent:$object::channel topic} you posted on.\', 0, "", 1),
("sitevideo_discussion_response", "sitevideo", \'{item:$subject} has {item:$object:posted} on a {itemParent:$object::channel topic} you created.\', 0, "", 1),
("favourited", "seaocore", \'{item:$subject} favourites your {item:$object:$label}.\', 0, "", 1),
("sitevideo_rate", "sitevideo", \'{item:$subject} rates your {item:$object:$label}.\', 0, "", 1),
("sitevideo_video_new", "sitevideo", \'{item:$subject} has posted a new video {item:$object} under channel {item:$object:$channel}.\', 0, "", 1),
("sitevideo_subscribed_channel_liked", "sitevideo", \'{item:$subject} likes  your subscribed {item:$object:$label}.\', 0, "", 1),
("sitevideo_subscribed_channel_comment", "sitevideo", \'{item:$subject} comments on your subscribed {item:$object:$label}.\', 0, "", 1),
("sitevideo_subscribed_channel_post", "sitevideo", \'{item:$subject} posts on your subscribed {item:$object:$label}.\', 0, "", 1),
("sitevideo_channel_subscribe", "sitevideo", \'{item:$subject} subscribes your {item:$object:$label}.\', 0, "", 1);');

$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');
$i = 0;
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitevideo_main_video_home');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitevideo_main_video_home';
    $menu_item->module = 'sitevideo';
    $menu_item->label = 'Videos Home';
    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canViewVideos';
    $menu_item->params = '{"route":"sitevideo_video_general","action":"index"}';
    $menu_item->menu = 'sitevideo_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitevideo_main_video_browse');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitevideo_main_video_browse';
    $menu_item->module = 'sitevideo';
    $menu_item->label = 'Browse Videos';
    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canViewVideos';
    $menu_item->params = '{"route":"sitevideo_video_general","action":"browse"}';
    $menu_item->menu = 'sitevideo_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitevideo_main_home');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitevideo_main_home';
    $menu_item->module = 'sitevideo';
    $menu_item->label = 'Channels Home';
    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canViewChannels';
    $menu_item->params = '{"route":"sitevideo_general","action":"index"}';
    $menu_item->menu = 'sitevideo_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitevideo_main_browse');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitevideo_main_browse';
    $menu_item->module = 'sitevideo';
    $menu_item->label = 'Browse Channels';
    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canViewChannels';
    $menu_item->params = '{"route":"sitevideo_general","action":"browse"}';
    $menu_item->menu = 'sitevideo_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitevideo_main_video_manage');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitevideo_main_video_manage';
    $menu_item->module = 'sitevideo';
    $menu_item->label = 'My Videos';
    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canManageVideos';
    $menu_item->params = '{"route":"sitevideo_video_general","action":"manage"}';
    $menu_item->menu = 'sitevideo_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitevideo_main_upload');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitevideo_main_upload';
    $menu_item->module = 'sitevideo';
    $menu_item->label = 'Post New Video';
    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canCreateVideos';
    $menu_item->params = '{"route":"sitevideo_video_general","action":"create","class":"seao_smoothbox","data_SmoothboxSEAOClass":"seao_add_video_lightbox"}';
    $menu_item->menu = 'sitevideo_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitevideo_main_playlist_browse');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitevideo_main_playlist_browse';
    $menu_item->module = 'sitevideo';
    $menu_item->label = 'Browse Playlists';
    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canCreatePlaylists';
    $menu_item->params = '{"route":"sitevideo_playlist_general","action":"browse"}';
    $menu_item->menu = 'sitevideo_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitevideo_main_video_categories');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitevideo_main_video_categories';
    $menu_item->module = 'sitevideo';
    $menu_item->label = 'Video Categories';
    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canViewVideos';
    $menu_item->params = '{"route":"sitevideo_video_general","action":"categories"}';
    $menu_item->menu = 'sitevideo_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitevideo_main_categories');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitevideo_main_categories';
    $menu_item->module = 'sitevideo';
    $menu_item->label = 'Channel Categories';
    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canViewChannels';
    $menu_item->params = '{"route":"sitevideo_general","action":"categories"}';
    $menu_item->menu = 'sitevideo_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}

$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitevideo_main_video_pinboard');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitevideo_main_video_pinboard';
    $menu_item->module = 'sitevideo';
    $menu_item->label = 'Videos Pinboard';
    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canViewVideos';
    $menu_item->params = '{"route":"sitevideo_video_general","module":"sitevideo","action":"pinboard"}';
    $menu_item->menu = 'sitevideo_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitevideo_main_pinboard');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitevideo_main_pinboard';
    $menu_item->module = 'sitevideo';
    $menu_item->label = 'Channels Pinboard';
    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canViewChannels';
    $menu_item->params = '{"route":"sitevideo_channel_general","module":"sitevideo","action":"pinboard"}';
    $menu_item->menu = 'sitevideo_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->save();
}
$select = $check_table->select()
        ->from($check_name, array('id'))
        ->where('name = ?', 'sitevideo_main_location');
$queary_info = $select->query()->fetchAll();
if (empty($queary_info)) {
    $menu_item = $check_table->createRow();
    $menu_item->name = 'sitevideo_main_location';
    $menu_item->module = 'sitevideo';
    $menu_item->label = 'Locations';
    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canViewVideos';
    $menu_item->params = '{"route":"sitevideo_video_general","module":"sitevideo","action":"map"}';
    $menu_item->menu = 'sitevideo_main';
    $menu_item->submenu = '';
    $menu_item->order = ++$i;
    $menu_item->enabled = 0;
    $menu_item->save();
}

//$select = $check_table->select()
//        ->from($check_name, array('id'))
//        ->where('name = ?', 'sitevideo_quick_upload');
//$queary_info = $select->query()->fetchAll();
//if (empty($queary_info)) {
//    $menu_item = $check_table->createRow();
//    $menu_item->name = 'sitevideo_quick_upload';
//    $menu_item->module = 'sitevideo';
//    $menu_item->label = 'Add New Videos';
//    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canCreateChannels';
//    $menu_item->params = '{"route":"sitevideo_general","action":"upload","class":"buttonlink icon_videos_new"}';
//    $menu_item->menu = 'sitevideo_quick';
//    $menu_item->submenu = '';
//    $menu_item->order = 1;
//    $menu_item->save();
//}
//$select = $check_table->select()
//        ->from($check_name, array('id'))
//        ->where('name = ?', 'sitevideo_quick_badge');
//$queary_info = $select->query()->fetchAll();
//if (empty($queary_info)) {
//    $menu_item = $check_table->createRow();
//    $menu_item->name = 'sitevideo_quick_badge';
//    $menu_item->module = 'sitevideo';
//    $menu_item->label = 'Create Videos Badge';
//    $menu_item->plugin = 'Sitevideo_Plugin_Menus::canCreateBadge';
//    $menu_item->params = '{"route":"sitevideo_badge","action":"create","class":"buttonlink sitevideo_icon_badge_create"}';
//    $menu_item->menu = 'sitevideo_quick';
//    $menu_item->submenu = '';
//    $menu_item->order = 2;
//    $menu_item->save();
//}

$db = Zend_Db_Table_Abstract::getDefaultAdapter();

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("core_main_sitevideo", "sitevideo", "Videos", "", \'{"route":"sitevideo_video_general","action":"index","icon":"fa-video-camera"}\', "core_main", "", 3),
("core_sitemap_sitevideo", "sitevideo", "Channels", "", \'{"route":"sitevideo_general","action":"index"}\', "core_sitemap", "", 3),

("mobi_browse_sitevideo", "sitevideo", "Videos", "", \'{"route":"sitevideo_video_general","action":"index"}\', "mobi_browse", "", 2),

( "sitevideo_admin_main_level", "sitevideo", "Member Level Settings", "", \'{"route":"admin_default","module":"sitevideo","controller":"level"}\', "sitevideo_admin_main", "", 2),

("sitevideo_admin_main_fields", "sitevideo", "Profile Fields", "", \'{"route":"admin_default","module":"sitevideo","controller":"fields"}\', "sitevideo_admin_main", "", 7),
("sitevideo_admin_main_formsearch", "sitevideo", "Search Form Settings", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings","action":"form-search"}\', "sitevideo_admin_main", "", 8),

( "sitevideo_admin_main_channel_manage", "sitevideo", "Manage Channels", "", \'{"route":"admin_default","module":"sitevideo","controller":"manage"}\', "sitevideo_admin_main", "", 9),

( "sitevideo_admin_main_integrations", "sitevideo", "Other Plugin Integrations", "", \'{"route":"admin_default","module":"sitevideo","controller":"settings","action":"integrations"}\', "sitevideo_admin_main", "", 11);');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES 
("sitevideo_profile_add", "sitevideo", "Upload Videos", "Sitevideo_Plugin_Menus", "", "channel_profile", "", 1),
("sitevideo_profile_manage", "sitevideo", "Manage Videos", "Sitevideo_Plugin_Menus", "", "channel_profile", "", 2),
("sitevideo_profile_edit", "sitevideo", "Edit Channel", "Sitevideo_Plugin_Menus", "", "channel_profile", "", 3),
("sitevideo_profile_delete", "sitevideo", "Delete Channel", "Sitevideo_Plugin_Menus", "", "channel_profile", "", 4),
("sitevideo_profile_share", "sitevideo", "Share via Badge", "Sitevideo_Plugin_Menus", "", "channel_profile", "", 5),
("sitevideo_profile_makechanneloftheday", "sitevideo", "Make Channel of the Day", "Sitevideo_Plugin_Menus", "", "channel_profile", "", 6), 
("sitevideo_profile_getlink", "sitevideo", "Get Link", "Sitevideo_Plugin_Menus", "", "channel_profile", "", 7),
("sitevideo_profile_suggesttofriend", "sitevideo", "Suggest To Friend", "Sitevideo_Plugin_Menus", "", "channel_profile", "", 9);');

$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` 
(`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitevideo_video_new", "sitevideo", \'{item:$subject} posted a new video {item:$object}:\', 1, 5, 1, 3, 1, 1),
("comment_channel", "sitevideo", \'{item:$subject} commented on {item:$owner}\'\'s {item:$object:channel}: {body:$body}\', 1, 1, 1, 1, 1, 1),
("comment_sitevideo_video", "sitevideo", \'{item:$subject} commented on {item:$owner}\'\'s {item:$object:video}: {body:$body}\', 1, 1, 1, 1, 1, 1),
("sitevideo_channel_new", "sitevideo", \'{item:$subject} has created a new channel {item:$object}:\', 1, 5, 1, 3, 1, 1),
("sitevideo_playlist_new", "sitevideo", \'{item:$subject} has created a new playlist {item:$object}:\', 1, 5, 1, 3, 1, 1);');


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
            ->where('name = ?', 'siteadvsearch_index_browse-page_sitevideo_channel')
            ->limit(1)
            ->query()
            ->fetchColumn();
    if (!$page_id) {
        $db->insert('engine4_core_pages', array(
            'name' => 'siteadvsearch_index_browse-page_sitevideo_channel',
            'displayname' => 'Advanced Search - SEAO - Channels',
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
            'name' => 'sitevideo.search-sitevideo',
            'parent_content_id' => $right_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"viewType":"vertical","showAllCategories":"1","nomobile":"0","name":"sitevideo.search-sitevideo"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitevideo.create-new-channel',
            'parent_content_id' => $right_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true}',
        ));


        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitevideo.browse-channels-sitevideo',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"viewType":["videoView","gridView","listView"],"defaultViewType":"videoView","channelOption":["title","owner","like","comment","favourite","numberOfVideos","subscribe","facebook","twitter","linkedin","googleplus"],"category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","videoViewWidth":"433","videoViewHeight":"330","gridViewWidth":"283","gridViewHeight":"250","show_content":"2","orderby":"creationDate","titleTruncation":"60","titleTruncationGridNVideoView":"30","descriptionTruncation":"350","itemCountPerPage":"12","nomobile":"0","name":"sitevideo.browse-channels-sitevideo"}',
        ));
    }


    $containerCount = 0;
    $widgetCount = 0;
    $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'siteadvsearch_index_browse-page_sitevideo_video')
            ->limit(1)
            ->query()
            ->fetchColumn();
    if (!$page_id) {
        $db->insert('engine4_core_pages', array(
            'name' => 'siteadvsearch_index_browse-page_sitevideo_video',
            'displayname' => 'Advanced Search - SEAO - Advanced Videos',
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
            'name' => 'sitevideo.search-video-sitevideo',
            'parent_content_id' => $right_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"viewType":"vertical","showAllCategories":"1","locationDetection":"1","nomobile":"0","name":"sitevideo.search-video-sitevideo"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitevideo.post-new-video',
            'parent_content_id' => $right_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"upload_button":"1","upload_button_title":"Post New Video","nomobile":"0","name":"sitevideo.post-new-video"}',
        ));


        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitevideo.browse-videos-sitevideo',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":true,"viewType":["videoView","gridView","listView"],"defaultViewType":"videoView","videoOption":["title","owner","creationDate","view","like","comment","duration","rating","watchlater","favourite","facebook","twitter","linkedin","googleplus"],"videoType":"","category_id":"0","subcategory_id":null,"hidden_video_category_id":"","hidden_video_subcategory_id":"","hidden_video_subsubcategory_id":"","videoViewWidth":"350","videoViewHeight":"340","gridViewWidth":"283","gridViewHeight":"250","show_content":"2","orderby":"creationDate","detactLocation":"0","defaultLocationDistance":"0","titleTruncation":"75","titleTruncationGridNVideoView":"30","descriptionTruncation":"370","itemCountPerPage":"12","nomobile":"0","name":"sitevideo.browse-videos-sitevideo"}',
        ));
    }

    $db->query("UPDATE `engine4_siteadvsearch_contents` SET `module_name` = 'sitevideo', `resource_type` = 'sitevideo_video' WHERE `engine4_siteadvsearch_contents`.`resource_type` ='video' LIMIT 1 ;");

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_siteadvsearch_contents')
            ->where('resource_type = ?', 'sitevideo_channel');
    $sitevideo_channel = $select->query()->fetchObject();
    if (!$sitevideo_channel) {
        $db->query("INSERT IGNORE INTO `engine4_siteadvsearch_contents` ( `module_name`, `resource_type`, `resource_title`, `listingtype_id`, `widgetize`, `content_tab`, `main_search`, `order`, `file_id`, `default`, `enabled`) VALUES ( 'sitevideo', 'sitevideo_channel', 'Channels', '0', '1', '1', '1', '999', '', '0', '1');");
    }
}

$pageName = 'sitevideo_badge_create';
$pageDisplayName = 'Advanced Videos - Channel Share by Badge Page';
$title = 'Channel Share by Badge';
$description = 'This page is the channel share by badge page.';
//START CHANNEL EDIT PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', $pageName)
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {

    // Insert page
    $db->insert('engine4_core_pages', array(
        'name' => $pageName,
        'displayname' => $pageDisplayName, //'Advanced Videos - Channel Edit Page',
        'title' => $title, //'Edit Channel',
        'description' => $description, //'This page is the channel edit page.',
        'custom' => 0,
    ));
    $page_id = $db->lastInsertId();

    // Insert top
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
    ));
    $top_id = $db->lastInsertId();

    // Insert main
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
    ));
    $main_id = $db->lastInsertId();

    // Insert top-middle
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
    ));
    $top_middle_id = $db->lastInsertId();

    // Insert main-middle
    $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
    ));
    $main_middle_id = $db->lastInsertId();

    // Insert menu
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'sitevideo.navigation',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
    ));

    // Insert content
    $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
    ));
}

$db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('SITECHANNEL_SEND_EMAIL', 'sitevideo', '[host],[email],[recipient_title],[recipient_link],[recipient_video],[sender_title],[sender_link],[sender_video],[object_title],[object_link],[object_video],[object_description]')");


$reset = false;
$templateApi->playlistCreatePage($reset, true);
$templateApi->playlistBrowsePage($reset, true);
$templateApi->playlistViewPage($reset, true);
$templateApi->tagCloudVideo($reset, true);
$templateApi->tagCloudChannel($reset, true);
$templateApi->topicView($reset, true);
$templateApi->postNewVideo($reset, true);
$templateApi->editVideo($reset, true);
$templateApi->channelCreate($reset, true);
$templateApi->channelEdit($reset, true);
$templateApi->watchLaterManage($reset, true);
$templateApi->subscriptionManage($reset, true);
$templateApi->browseVideo($reset, true);
$templateApi->channelHome($reset, true);
$templateApi->channelView($reset, true);
$templateApi->videoView($reset, true);
$templateApi->browseChannel($reset, true);
$templateApi->channelManage($reset, true);
$templateApi->videoManage($reset, true);
$templateApi->channelCategories($reset, true);
$templateApi->videoCategories($reset, true);
$templateApi->videoHome($reset, true);
$templateApi->pinboardBrowseVideo($reset, true);
$templateApi->pinboardBrowseChannel($reset, true);
$templateApi->setVideoCategories($reset, true);
$templateApi->setChannelCategories($reset, true);
$templateApi->setVideoLocations($reset, true);
$templateApi->setChannelEditVideos($reset, true);
$templateApi->setBadgeCreate($reset, true);
$templateApi->setManagePlaylist($reset, true);
$templateApi->playlistPlayallPage($reset, true);

@ini_set('memory_limit', '-1');
@ini_set('max_execution_time', 600);
Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->uploadDefaultImages();
Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->uploadDefaultImages();
Engine_Api::_()->getDbTable('channels', 'sitevideo')->createDefaultChannels();
Engine_Api::_()->getDbTable('videos', 'sitevideo')->createDefaultVideos();


$coreSettings = Engine_Api::_()->getApi('settings', 'core');
if (Engine_Api::_()->seaocore()->getCurrentActivateTheme()) {
    $coreSettings->setSetting('sitevideoshow.navigation.tabs', 7);
} else {
    $coreSettings->setSetting('sitevideoshow.navigation.tabs', 6);
}
$templateApi->memberProfileChannelParameter(true, true);
$templateApi->memberProfileVideoParameter(true, true);

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES("sitevideo_admin_main_synchronize", "sitevideo", "Synchronize", NULL, \'{"route":"admin_default","module":"sitevideo","controller":"settings","action":"synchronize-video"}\', "sitevideo_admin_main", NULL,  18);');

if (Engine_Api::_()->hasModuleBootstrap('siteadvsearch')) {
    //MAKE DIRECTORY IN PUBLIC FOLDER
    @mkdir(APPLICATION_PATH . "/temporary/siteadvsearch_search_icons", 0777, true);

    //COPY THE ICONS IN NEWLY CREATED FOLDER
    $dir = APPLICATION_PATH . "/application/modules/Siteadvsearch/externals/images/icons";
    $public_dir = APPLICATION_PATH . "/temporary/siteadvsearch_search_icons";

    if (is_dir($dir) && is_dir($public_dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if (strstr($file, '.png')) {
                @copy(APPLICATION_PATH . "/application/modules/Siteadvsearch/externals/images/icons/$file", APPLICATION_PATH . "/temporary/siteadvsearch_search_icons/$file");
            }
        }
        @chmod(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons', 0777);
    }
    $contentTable = Engine_Api::_()->getDbtable('contents', 'siteadvsearch');
    //MAKE QUERY
    $select = $contentTable->select()->from($contentTable->info('name'), array('content_id', 'resource_title', 'file_id'))->where('resource_type in(?)', array('sitevideo_video', 'sitevideo_channel'));
    $contentTypes = $contentTable->fetchAll($select);
    //UPLOAD DEFAULT ICONS
    foreach ($contentTypes as $contentType) {
        $contentTypeName = $contentType->resource_title;
        $iconName = $contentTypeName . '.png';

        @chmod(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons', 0777);

        $file = array();
        $file['tmp_name'] = APPLICATION_PATH . "/temporary/siteadvsearch_search_icons/$iconName";
        $file['name'] = $iconName;

        if (file_exists($file['tmp_name'])) {
            $name = basename($file['tmp_name']);
            $path = dirname($file['tmp_name']);
            $mainName = $path . '/' . $file['name'];

            @chmod($mainName, 0777);

            $photo_params = array(
                'parent_id' => $contentType->content_id,
                'parent_type' => "siteadvsearch_content",
            );

            //RESIZE IMAGE WORK
            $image = Engine_Image::factory();
            $image->open($file['tmp_name']);
            $image->open($file['tmp_name'])
                    ->resample(0, 0, $image->width, $image->height, $image->width, $image->height)
                    ->write($mainName)
                    ->destroy();

            $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);

            //UPDATE FILE ID IN CATEGORY TABLE
            if (!empty($photoFile->file_id)) {
                $contentType = Engine_Api::_()->getItem('siteadvsearch_content', $contentType->content_id);
                $contentType->file_id = $photoFile->file_id;
                $contentType->save();
            }
        }
    }

    //REMOVE THE CREATED PUBLIC DIRECTORY
    if (is_dir(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons')) {
        $files = scandir(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons');
        foreach ($files as $file) {
            $is_exist = file_exists(APPLICATION_PATH . "/temporary/siteadvsearch_search_icons/$file");
            if ($is_exist) {
                @unlink(APPLICATION_PATH . "/temporary/siteadvsearch_search_icons/$file");
            }
        }
        @rmdir(APPLICATION_PATH . '/temporary/siteadvsearch_search_icons');
    }
}

$tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
$header_page_id = Engine_Api::_()->sitevideo()->getWidgetizedPageId(array('name' => 'header'));
$main_content_id = $tableNameContent->select()
        ->from($tableNameContent->info('name'), 'content_id')
        ->where('name =?', 'main')
        ->where('page_id =?', $header_page_id)
        ->query()
        ->fetchColumn();

if (!empty($main_content_id)) {
    $content_id = $tableNameContent->select()
            ->from($tableNameContent->info('name'), 'content_id')
            ->where('name =?', 'core.html-block')
            ->where('page_id =?', $header_page_id)
            ->where('params like (?)', '%jQuery.noConflict()%')->query()
            ->fetchColumn();

    if (!$content_id) {
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'core.html-block',
            'page_id' => $header_page_id,
            'parent_content_id' => $main_content_id,
            'order' => 1,
            'params' => '{"title":"","data":"<script type=\"text\/javascript\"> \r\nif(typeof(window.jQuery) !=  \"undefined\") {\r\njQuery.noConflict();\r\n}\r\n<\/script>","nomobile":"0","name":"core.html-block"}'
        ));
    }
}