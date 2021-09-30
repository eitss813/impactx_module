<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Installer extends Engine_Package_Installer_Module {

    public function onPreinstall() {
        $getErrorMsg = $this->getVersion();
        if (!empty($getErrorMsg)) {
            return $this->_error($getErrorMsg);
        }

        $PRODUCT_TYPE = 'sitevideo';
        $PLUGIN_TITLE = 'Sitevideo';
        $PLUGIN_VERSION = '4.10.5p7';

        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'Advanced Videos / Channels / Playlists Plugin';
        $PRODUCT_TITLE = 'Advanced Videos / Channels / Playlists Plugin';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.10.3p6';
        $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
        $db = $this->getDb();
        $engine4VideoFieldsMeta = $db->query("SHOW TABLES LIKE 'engine4_video_fields_meta'")->fetch();
        if ($engine4VideoFieldsMeta && !$this->_columnExist('engine4_video_fields_meta', 'quick_info')) {
            $db->query("ALTER  TABLE  engine4_video_fields_meta ADD COLUMN  `quick_info` tinyint(1) DEFAULT 1;");
        }

        $is_file = file_exists($file_path);
        if (empty($is_file)) {
            include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
        } else {
            $db = $this->getDb();
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
            $is_Mod = $select->query()->fetchObject();
            if (empty($is_Mod)) {
                include_once $file_path;
            }
        }

        parent::onPreinstall();
    }

    public function onInstall() {
        $db = $this->getDb();

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("sitevideo_channel_video_new", "sitevideo", \'{item:$subject} created a new video in the channel {item:$object}:\', "1", "7", "2", "4", "1", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("sitevideo_sitepage_video_new", "sitevideo", \'{item:$subject} created a new video in the page {item:$object}:\', "1", "7", "2", "1", "1", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("sitevideo_sitebusiness_video_new", "sitevideo", \'{item:$subject} created a new video in the business {item:$object}:\', "1", "7", "2", "1", "1", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("sitevideo_sitegroup_video_new", "sitevideo", \'{item:$subject} created a new video in the group {item:$object}:\', "1", "7", "2", "1", "1", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("sitevideo_sitestore_video_new", "sitevideo", \'{item:$subject} created a new video in the store {item:$object}:\', "1", "7", "2", "1", "1", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("sitevideo_sitestoreproduct_video_new", "sitevideo", \'{item:$subject} created a new video in the product {item:$object}:\', "1", "7", "2", "1", "1", "1");');

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("sitevideo_siteevent_video_new", "sitevideo", \'{item:$subject} created a new video in the event {item:$object}:\', "1", "7", "2", "1", "1", "1");');

        $db->query(" INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('notify_sitevideo_processed', 'sitevideo', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), ('notify_sitevideo_processed_failed', 'sitevideo', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');");

        // remove modified_date column from stream table if it exists.
        if ($this->_columnExist('engine4_activity_stream', 'modified_date')) {
            $db->query("ALTER  TABLE  engine4_activity_stream DROP COLUMN `modified_date`;");
        }

        // remove the Trigger also
        $connection = Engine_Db_Export::factory($db)->getAdapter()->getConnection();
        if (method_exists($connection, "query")) {
            $connection->query("DROP trigger if exists `set_stream_modified_date`");
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitevideo');
        $is_sitevideo_object = $select->query()->fetchObject();

        if (!empty($is_sitevideo_object)) {
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)
    VALUES
    ("sitevideo_admin_main_support", "sitevideo", "Support", "", \'{"route":"admin_default","module":"sitevideo","controller":"support"}\', "sitevideo_admin_main", "", 1, 0, 998)
    ');
        }
        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)
    VALUES
    ("sitevideo_admin_main_videoswiper", "sitevideo", "VideoSwiper Settings", "", \'{"route":"admin_default","module":"sitevideo","controller":"video-swiper-settings"}\', "sitevideo_admin_main", "", 1, 0, 20)
    ');
        $db->query("INSERT IGNORE INTO engine4_core_tasks (`title`,`module`,`plugin`,`timeout`)
    values('Sitevideo Autoupload Video','sitevideo','Sitevideo_Plugin_Task_UploadYoutubeVideos',1200);
    ");
        if (!$this->_columnExist('engine4_core_likes', 'creation_date')) {
            $db->query("ALTER  TABLE  engine4_core_likes ADD COLUMN  `creation_date` DATETIME;");
        }
        $this->_setActivityFeeds();
        $db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES ('sitevideo_video', 'content_type', '1', '25', 'Content Type');");

        $db->query("UPDATE `engine4_core_menus` SET `title` = 'Advanced Videos / Channels Main Navigation' WHERE `engine4_core_menus`.`name` = 'sitevideo_main';");
        $db->query("DELETE FROM `engine4_core_menus` WHERE `engine4_core_menus`.`name` = 'sitevideo_quick';");
        $db->query("DELETE FROM `engine4_core_menus` WHERE `engine4_core_menus`.`name` = 'video_main';");

        $engine4siteadvsearchTable = $db->query("SHOW TABLES LIKE 'engine4_siteadvsearch_contents'")->fetch();
        if ($engine4siteadvsearchTable) {
            $db->query("UPDATE `engine4_siteadvsearch_contents` SET `module_name` = 'sitevideo', `resource_type` = 'sitevideo_video', `resource_title` = 'Videos',`main_search` = '1'  WHERE `engine4_siteadvsearch_contents`.`resource_type` ='video' LIMIT 1 ;");

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_siteadvsearch_contents')
                    ->where('resource_type = ?', 'sitevideo_channel');
            $sitevideo_channel_isActivate_object = $select->query()->fetchObject();
            if (!$sitevideo_channel_isActivate_object) {
                $db->query("INSERT IGNORE INTO `engine4_siteadvsearch_contents` ( `module_name`, `resource_type`, `resource_title`, `listingtype_id`, `widgetize`, `content_tab`, `main_search`, `order`, `file_id`, `default`, `enabled`) VALUES ( 'sitevideo', 'sitevideo_channel', 'Channels', '0', '1', '1', '1', '999', '', '1', '1');");
            }
        }

        $db->query("UPDATE `engine4_core_modules` SET `enabled` = 0 WHERE name = 'sitevideoview'");
        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = 0 WHERE name = 'core_main_video'");
        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = 0 WHERE name = 'core_admin_main_plugins_video'");
        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = 0 WHERE name = 'mobi_browse_video'");

        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitevideo';");
        $engine4VideoRatingsTable = $db->query("SHOW TABLES LIKE 'engine4_video_ratings'")->fetch();

        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ("sitevideo_processed", "sitevideo", \'Your {item:$object:video} is ready to be viewed.\', "0", "", "1");');
        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ("sitevideo_processed_failed", "sitevideo", \'Your {item:$object:video} has failed to process.\', "0", "", "1");');


        if (!empty($engine4VideoRatingsTable)) {
            $this->importRatingRecords();
        }

        // Suggestion Integration
        $suggestion = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'suggestion')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (!empty($suggestion)) {
            $isModExist = $db->query("SELECT * FROM `engine4_suggestion_module_settings` WHERE `module` LIKE 'sitevideo' LIMIT 1")->fetch();
            if (empty($isModExist)) {

                $db->query("INSERT INTO `engine4_suggestion_module_settings` (`module`, `item_type`, `field_name`, `owner_field`, `item_title`, `button_title`, `enabled`, `notification_type`, `quality`, `link`, `popup`, `recommendation`, `default`, `settings`) VALUES ('sitevideo', 'sitevideo_video', 'video_id', 'owner_id', 'Video', 'View this Video', 1, 'sitevideo_video_suggestion', 1, 1, 0, 0, 1, 'a:1:{s:7:\"default\";i:1;}');");

                $db->query("INSERT INTO `engine4_suggestion_module_settings` (`module`, `item_type`, `field_name`, `owner_field`, `item_title`, `button_title`, `enabled`, `notification_type`, `quality`, `link`, `popup`, `recommendation`, `default`, `settings`) VALUES ('sitevideo', 'sitevideo_channel', 'channel_id', 'owner_id', 'Channel', 'View this Channel', 1, 'sitevideo_channel_suggestion', 1, 1, 0, 0, 1, 'a:1:{s:7:\"default\";i:1;}');");

                $db->query("UPDATE `engine4_suggestion_module_settings` SET `enabled` = '0' WHERE `module` = 'video';");
            }
        }

        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_activity_notificationtypes'")->fetch();

        if ($table_exist) {
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("sitevideo_video_suggestion", "suggestion", \'{item:$subject} has suggested to you a {item:$object:videos}.\', 1, "suggestion.widget.get-notify")');

            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("sitevideo_channel_suggestion", "suggestion", \'{item:$subject} has suggested to you a {item:$object:channels}.\', 1, "suggestion.widget.get-notify")');
        }

        //Sitelike Integration
        $sitelike = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitelike')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (!empty($sitelike)) {

            $isModExist = $db->query("SELECT * FROM `engine4_sitelike_mixsettings` WHERE `module` LIKE 'sitevideo' LIMIT 1")->fetch();
            if (empty($isModExist)) {
                $db->query("INSERT IGNORE INTO `engine4_sitelike_mixsettings` (`module`, `resource_type`, `resource_id`, `item_title`, `title_items`, `value`, `default`, `enabled`) VALUES ('sitevideo', 'video', 'video_id', 'Videos', 'Video', 1, 0, 1);");


                $db->query("INSERT IGNORE INTO `engine4_sitelike_mixsettings` (`module`, `resource_type`, `resource_id`, `item_title`, `title_items`, `value`, `default`, `enabled`) VALUES ('sitevideo', 'sitevideo_channel', 'channel_id', 'Channels', 'Channel', 1, 0, 1);");
                $db->query("UPDATE `engine4_sitelike_mixsettings` SET `enabled` = '0' WHERE `module` = 'video';");
            }
        }

        //facebookse integration
        $facebookse = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'facebookse')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (!empty($facebookse)) {

            $isModExist = $db->query("SELECT * FROM `engine4_facebookse_mixsettings` WHERE `module` LIKE 'sitevideo' LIMIT 1")->fetch();
            if (empty($isModExist)) {
                $db->query("INSERT IGNORE INTO `engine4_facebookse_mixsettings` (`module`, `module_name`, `resource_type`, `resource_id`, `owner_field`, `module_title`, `module_description`, `enable`, `send_button`, `like_type`, `like_faces`, `like_width`, `like_font`, `like_color`, `layout_style`, `opengraph_enable`, `title`, `photo_id`, `description`, `types`, `fbadmin_appid`, `commentbox_enable`, `commentbox_privacy`, `commentbox_width`, `commentbox_color`, `module_enable`, `default`, `activityfeed_type`, `streampublish_message`, `streampublish_story_title`, `streampublish_link`, `streampublish_caption`, `streampublish_description`, `streampublish_action_link_text`, `streampublish_action_link_url`, `streampublishenable`, `activityfeedtype_text`) VALUES

( 'sitevideo', 'Video', 'video', 'video_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', '', 'standard', 1, '', 0, '', '', 0, 1, 1, 450, 'light', 1, 1, 'sitevideo_video_new', 'View my Video!', '{*sitevideo_title*}', '{*sitevideo_url*}', '{*actor*} created a Video on {*site_title*}: {*site_url*}.', '{*sitevideo_desc*}', 'View Video', '{*sitevideo_url*}', 1, 'Creating a Video'),

( 'sitevideo', 'Video Channels', 'sitevideo_channel', 'channel_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', '', 'standard', 1, '', 0, '', '', 0, 1, 1, 450, 'light', 1, 1, 'sitevideo_channel_new', 'View my Video Channel!', '{*sitevideo_title*}', '{*sitevideo_url*}', '{*actor*} created a Video Channel on {*site_title*}: {*site_url*}.', '{*sitevideo_desc*}', 'View Video Channel', '{*sitevideo_url*}', 1, 'Creating a Video Channel');");
            }
        }


        //communityad integration
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'communityad')
                ->where('enabled = ?', 1);
        $is_communityad_object = $select->query()->fetchObject();
        if (!empty($is_communityad_object)) {
            $db->query("DELETE FROM `engine4_communityad_modules` WHERE `engine4_communityad_modules`.`module_name` = 'video' LIMIT 1");
            $db->query("INSERT IGNORE INTO `engine4_communityad_modules` (`module_name`, `module_title`, `table_name`, `title_field`, `body_field`, `owner_field`, `is_delete`, `displayable`) VALUES
('sitevideo', 'Video', 'video', 'title', 'description', 'owner_id', '1', '7');");
        }

        if (!$this->_columnExist('engine4_sitevideo_channels', 'youtube_channel_id')) {
            $db->query("ALTER TABLE `engine4_sitevideo_channels` add column youtube_channel_id text;");
        }
        if (!$this->_columnExist('engine4_sitevideo_channels', 'pending_video')) {
            $db->query("ALTER TABLE `engine4_sitevideo_channels` add column pending_video text;");
        }
        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("sitevideo_dashboard_youtubevideos", "sitevideo", "Youtube Channel", "Sitevideo_Plugin_Dashboardmenus", \'{"route":"sitevideo_dashboard", "action":"youtube-videos"}\', "sitevideo_dashboard_content", NULL, "1", "0", "95");
');
        $db->query("
INSERT IGNORE INTO `engine4_core_jobtypes`(`title`,`type`,`module`,`plugin`,`enabled`,`priority`,`multi`)
                values('Sitevideo Upload','Sitevideo_Upload','sitevideo','Sitevideo_Plugin_Job_Upload',1,75,1);

");

        $this->playlistPlayallPage();

//        $select = new Zend_Db_Select($db);
//        $select
//                ->from('engine4_core_modules')
//                ->where('name = ?', 'advancedactivity')
//                ->where('enabled = ?', 1);
//        $check_s = $select->query()->fetchObject();
//        if ($check_sitevideo) {
//            $table_page_exist = $db->query('SHOW TABLES LIKE "engine4_advancedactivity_contents"')->fetch();
//            if (!empty($table_page_exist)) {
//                $db->query("UPDATE `engine4_advancedactivity_contents` SET `module_name` = 'sitevideo' AND `filter_type` = 'video' WHERE `engine4_advancedactivity_contents`.`module_name` = 'video';");
//            }
//        }

        $params = '["1","2","3","4","5"]';
        $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
            SELECT
                level_id as `level_id`,
               'video' as `type`,
               'source' as `name`,
                5 as `value`,
                '$params' as `params`
            FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user' , 'public');");
        $isMod = $db->query("SELECT * FROM  `engine4_core_modules` WHERE  `name`='sitevideo'")->fetch();
        if (!empty($isMod) && !empty($isMod['version'])) {
            $result = $this->checkVersion($isMod['version'], '4.8.13p4');
            if (empty($result)) {
                $db->query("UPDATE `engine4_core_settings` SET `value` = true WHERE name = 'sitevideo.html5'");
            }
        }
        parent::onInstall();
    }

    public function onEnable() {
        $db = $this->getDb();
        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = 0 WHERE name = 'core_main_video'");
        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = 0 WHERE name = 'core_admin_main_plugins_video'");
        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = 0 WHERE name = 'mobi_browse_video'");
        $db->query("INSERT IGNORE INTO `engine4_core_menus` ( `name`, `type`, `title`, `order`) VALUES ('video_main', 'standard', 'Video Main Navigation Menu', '999');");
        parent::onEnable();
    }

    public function onDisable() {
        $db = $this->getDb();
        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = 1 WHERE name = 'core_main_video'");
        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = 1 WHERE name = 'core_admin_main_plugins_video'");
        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = 1 WHERE name = 'mobi_browse_video'");
        $db->query("DELETE FROM `engine4_core_menus` WHERE `engine4_core_menus`.`name` = 'video_main';");
        parent::onDisable();
    }

    //IMPORT RATING RECORDS from core engine4_video_ratings table
    public function importRatingRecords() {
        $db = $this->getDb();
        $db->query("INSERT IGNORE INTO `engine4_sitevideo_ratings`(`resource_id`,`resource_type`,`user_id`,`rating`) SELECT video_id,'video',user_id,rating FROM engine4_video_ratings; ");
    }

    function _columnExist($table, $column) {
        $db = $this->getDb();

        $columnName = $db->query("
        SHOW COLUMNS FROM `$table`
           LIKE '$column'")->fetch();
        if (!empty($columnName))
            return true;
        return false;
    }

    private function getVersion() {
        $db = $this->getDb();

        $errorMsg = '';
        $finalModules = $getResultArray = array();
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'advancedactivity' => '4.8.10p7',
            'nestedcomment' => '4.8.10p3',
            'sitecontentcoverphoto' => '4.8.10p4',
            'siteadvsearch' => '4.8.10p2'
        );

        foreach ($modArray as $key => $value) {
            $isMod = $db->query("SELECT * FROM  `engine4_core_modules` WHERE  `name` LIKE  '" . $key . "'")->fetch();
            if (!empty($isMod) && !empty($isMod['version'])) {
                $isModSupport = $this->checkVersion($isMod['version'], $value);
                if (empty($isModSupport)) {
                    $finalModules['modName'] = $key;
                    $finalModules['title'] = $isMod['title'];
                    $finalModules['versionRequired'] = $value;
                    $finalModules['versionUse'] = $isMod['version'];
                    $getResultArray[] = $finalModules;
                }
            }
        }

        foreach ($getResultArray as $modArray) {
            $errorMsg .= '<div class="tip"><span>Note: Your website does not have the latest version of "' . $modArray['title'] . '". Please upgrade "' . $modArray['title'] . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Advanced Videos / Channels / Playlists Plugin".<br/> Please <a href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
        }

        return $errorMsg;
    }

    function checkVersion($databaseVersion, $checkDependancyVersion) {
        if (strcasecmp($databaseVersion, $checkDependancyVersion) == 0)
            return -1;
        $databaseVersionArr = explode(".", $databaseVersion);
        $checkDependancyVersionArr = explode('.', $checkDependancyVersion);
        $fValueCount = $count = count($databaseVersionArr);
        $sValueCount = count($checkDependancyVersionArr);
        if ($fValueCount > $sValueCount)
            $count = $sValueCount;
        for ($i = 0; $i < $count; $i++) {
            $fValue = $databaseVersionArr[$i];
            $sValue = $checkDependancyVersionArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                $result = $this->compareValues($fValue, $sValue);
                if ($result == -1) {
                    if (($i + 1) == $count) {
                        return $this->compareValues($fValueCount, $sValueCount);
                    } else
                        continue;
                }
                return $result;
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);
                $result = $this->compareValues($fsArr[0], $sValue);
                return $result == -1 ? 1 : $result;
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);
                $result = $this->compareValues($fValue, $ssArr[0]);
                return $result == -1 ? 0 : $result;
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                $result = $this->compareValues($fsArr[0], $ssArr[0]);
                if ($result != -1)
                    return $result;
                $result = $this->compareValues($fsArr[1], $ssArr[1]);
                return $result;
            }
        }
    }

    public function compareValues($firstVal, $secondVal) {
        $num = $firstVal - $secondVal;
        return ($num > 0) ? 1 : ($num < 0 ? 0 : -1);
    }

    protected function _setActivityFeeds() {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'nestedcomment')
                ->where('enabled = ?', 1);
        $is_nestedcomment_object = $select->query()->fetchObject();
        if ($is_nestedcomment_object) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitevideo_channel", "sitevideo", \'{item:$subject} replied to a comment on {item:$owner}\'\'s channel {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("sitevideo_activityreply", "sitevideo", \'{item:$subject} has replied on {var:$eventname}.\', 0, "");');

            $db->query("INSERT IGNORE INTO `engine4_nestedcomment_modules` (`module`, `resource_type`, `enabled`)
VALUES ('sitevideo', 'sitevideo_channel', 0)");

            $db->query("INSERT IGNORE INTO `engine4_nestedcomment_modules` (`module`, `resource_type`, `enabled`)
VALUES ('sitevideo', 'video', 0)");
        }
    }

    public function playlistPlayallPage() {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $page_id = $select
                        ->from('engine4_core_pages', 'page_id')
                        ->where('name = ?', 'sitevideo_playlist_playall')
                        ->query()->fetchColumn();

        if (!$page_id) {
            $db->query("insert into engine4_core_pages (`name`,`displayname`,`url`,`title`,`description`,`keywords`,`custom`,`fragment`,`layout`,`levels`,`provides`,`view_count`,`search`)values('sitevideo_playlist_playall','Advanced Videos – Playlist Play All Videos Page',NULL,'Playlist Play All Videos Page','This page will play all videos of a playlist','','0','0','',NULL,NULL,'0','0');
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','main',NULL,'2','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_playall' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'6','[\"\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_playall' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','top',NULL,'1','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_playall' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','middle',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='top' and `order`=1 limit 1),'7','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_playall' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'container','right',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='main' and `order`=2 limit 1),'5','[\"[]\"]',NULL from engine4_core_pages where name = 'sitevideo_playlist_playall' ;
");
            $db->query("insert into engine4_core_content (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`,`attribs`) select `page_id`,'widget','sitevideo.playlist-playall',(select content_id from engine4_core_content where `page_id`=engine4_core_pages.page_id and `type`='container' and `name`='middle' and `order`=7 limit 1),'3','{\"title\":\"\",\"playlistOptions\":\"0\",\"height\":\"540\",\"titleTruncation\":\"35\",\"nomobile\":\"0\",\"name\":\"sitevideo.playlist-playall\"}',NULL from engine4_core_pages where name = 'sitevideo_playlist_playall' ;
");
        }
    }

}
