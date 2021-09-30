<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Installer extends Engine_Package_Installer_Module {

    public function onPreinstall() {
        $PRODUCT_TYPE = 'sitecrowdfunding';
        $PLUGIN_TITLE = 'Sitecrowdfunding';
        $PLUGIN_VERSION = '4.10.5';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'Crowdfunding / Fundraising / Donations Plugin';
        $PRODUCT_TITLE = 'Crowdfunding / Fundraising / Donations Plugin';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.10.3p6';
        $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
        $is_file = @file_exists($file_path);
        if (empty($is_file)) {
            include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
        } else {
            $db = $this->getDb();
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
            $is_Mod = $select->query()->fetchObject();
            if (empty($is_Mod))
                include_once $file_path;
        }

        parent::onPreinstall();
    }

    public function onInstall() {
        $this->userInfoColumns();
        $this->widgetsOnMemberProfilePage();
        $this->moduleIntegration();
        $db = $this->getDb();
        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitecrowdfunding';");
        //Integration with Nested comment
        $this->_setActivityFeeds();
        //Integration with Advanced Search
        $engine4siteadvsearchTable = $db->query("SHOW TABLES LIKE 'engine4_siteadvsearch_contents'")->fetch();
        if ($engine4siteadvsearchTable) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_siteadvsearch_contents')
                    ->where('resource_type = ?', 'sitecrowdfunding_project');
            $sitecrowdfunding_isActivate_object = $select->query()->fetchObject();
            if (!$sitecrowdfunding_isActivate_object) {
                $db->query("INSERT IGNORE INTO `engine4_siteadvsearch_contents` ( `module_name`, `resource_type`, `resource_title`, `listingtype_id`, `widgetize`, `content_tab`, `main_search`, `order`, `file_id`, `default`, `enabled`) VALUES ( 'sitecrowdfunding', 'sitecrowdfunding_project', 'Projects', '0', '1', '1', '1', '999', '', '1', '1');");
            }
        }
        // Integration with advancedactivity entry
        $table_page_exist = $db->query('SHOW TABLES LIKE "engine4_advancedactivity_contents"')->fetch();
        if (!empty($table_page_exist)) {
            $entryExists = $db->select()
                    ->from('engine4_advancedactivity_contents')
                    ->where('module_name = ?', 'sitecrowdfunding')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (empty($entryExists)) {
                $db->query("INSERT IGNORE INTO `engine4_advancedactivity_contents` ( `module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES
('sitecrowdfunding', 'sitecrowdfunding', 'Projects', 1, 7, 1)");
            }
        }

        $table_exist = $db->query('SHOW TABLES LIKE "engine4_advancedactivity_customtypes"')->fetch();
        if (!empty($table_exist)) {
            $entryExists = $db->select()
                    ->from('engine4_advancedactivity_customtypes')
                    ->where('module_name = ?', 'sitecrowdfunding')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (empty($entryExists)) {
                $db->query("INSERT IGNORE INTO `engine4_advancedactivity_customtypes` ( `module_name`, `resource_type`, `resource_title`, `enabled`, `order`, `default`) VALUES
('sitecrowdfunding', 'sitecrowdfunding_project', 'Project', 1, 12, 1)");
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

            $isModExist = $db->query("SELECT * FROM `engine4_facebookse_mixsettings` WHERE `module` LIKE 'sitecrowdfunding' LIMIT 1")->fetch();
            if (empty($isModExist)) {
                $db->query("INSERT INTO `engine4_facebookse_mixsettings` (`module`, `module_name`, `resource_type`, `resource_id`, `owner_field`, `module_title`, `module_description`, `enable`, `send_button`, `like_type`, `like_faces`, `like_width`, `like_font`, `like_color`, `layout_style`, `opengraph_enable`, `title`, `photo_id`, `description`, `types`, `fbadmin_appid`, `commentbox_enable`, `commentbox_privacy`, `commentbox_width`, `commentbox_color`, `module_enable`, `default`, `activityfeed_type`, `streampublish_message`, `streampublish_story_title`, `streampublish_link`, `streampublish_caption`, `streampublish_description`, `streampublish_action_link_text`, `streampublish_action_link_url`, `streampublishenable`, `activityfeedtype_text`, `action_type`, `object_type`, `like_commentbox`, `fbbutton_liketext`, `fbbutton_unliketext`, `show_customicon`, `fbbutton_likeicon`, `fbbutton_unlikeicon`) VALUES ('sitecrowdfunding', 'Project', 'sitcrowdfunding_project', 'project_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', '', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitecrowdfunding_project_new', 'View Project!', '{*sitecrowdfunding_title*}', '{*sitecrowdfunding_url*}', '{*actor*} created a Project on {*site_title*}: {*site_url*}.', '{*sitecrowdfunding_desc*}', 'View Project', '{*sitecrowdfunding_url*}', 1, 'Start a Project', 'og.likes', 'object', 1, 'Like', 'Unlike', 1, '', '');");
            }
        }
        //suggestion integration
        $pluginInstalled = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'suggestion')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if ($pluginInstalled) {
            $table_exist = $db->query("SHOW TABLES LIKE 'engine4_suggestion_module_settings'")->fetch();
            if ($table_exist) {
                $entryExists = $db->select()
                        ->from('engine4_suggestion_module_settings')
                        ->where('module = ?', 'sitecrowdfunding')
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
                if (empty($entryExists)) {
                    $db->query("INSERT IGNORE INTO `engine4_suggestion_module_settings` (`module`, `item_type`, `field_name`, `owner_field`, `item_title`, `button_title`, `enabled`, `notification_type`, `quality`, `link`, `popup`, `recommendation`, `default`, `settings`) VALUES ('sitecrowdfunding', 'sitecrowdfunding_project', 'project_id', 'owner_id', 'Project', 'View this Project', 1, 'sitecrowdfunding_suggestion', 1, 1, 0, 0, 1, 'a:1:{s:7:\"default\";i:1;}');");
                }
            }
        }
        //communityad integration
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'communityad');
        $is_communityad_object = $select->query()->fetchObject();
        if (!empty($is_communityad_object)) {
            $entryExists = $db->select()
                    ->from('engine4_communityad_modules')
                    ->where('module_name = ?', 'sitecrowdfunding')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (empty($entryExists)) {
                $db->query("INSERT IGNORE INTO `engine4_communityad_modules` (`module_name`, `module_title`, `table_name`, `title_field`, `body_field`, `owner_field`, `is_delete`, `displayable`) VALUES ('sitecrowdfunding', 'Project', 'sitecrowdfunding_project', 'title', 'description', 'owner_id', '1', '7');");
            }
        }
        //sitehashtag integration
        $sitehashtag = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitehashtag')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (!empty($sitehashtag)) {
            $isModExist = $db->query("SELECT * FROM `engine4_sitehashtag_contents` WHERE `module_name` LIKE 'sitecrowdfunding' LIMIT 1")->fetch();
            if (empty($isModExist)) {
                $db->query('INSERT IGNORE INTO `engine4_sitehashtag_contents` (`module_name`, `resource_type`, `enabled`) VALUES ("sitecrowdfunding", "sitecrowdfunding", 1);');
            }
        }

        //sitelike integration
        $sitelike = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitelike')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (!empty($sitelike)) {
            $table_exist = $db->query('SHOW TABLES LIKE "engine4_sitelike_mixsettings"')->fetch();
            if (!empty($table_exist)) {
                $isModExist = $db->query("SELECT * FROM `engine4_sitelike_mixsettings` WHERE `module` LIKE 'sitecrowdfunding' LIMIT 1")->fetch();
                if (empty($isModExist)) {
                    $db->query("INSERT IGNORE INTO `engine4_sitelike_mixsettings` (`module`, `resource_type`, `resource_id`, `item_title`, `title_items`, `value`, `default`, `enabled`) VALUES ('sitecrowdfunding', 'sitecrowdfunding_project', 'project_id', 'Project', 'Project', 1, 0, 1);");
                }
            }
        }
        $this->setManageModules();
        parent::onInstall();
    }

    public function isTableExist($tableName) {
        $db = $this->getDb();
        $result = $db->query('SHOW TABLES LIKE \'' . $tableName . '\'')->fetch();
        if (empty($result)) {
            return false;
        }
        return true;
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

    public function moduleIntegration() {
        $db = $this->getDb();
        if ($this->isTableExist('engine4_sitevideo_modules')) {
            $db->query("INSERT IGNORE INTO engine4_sitevideo_modules(`item_type`, `item_id`, `item_module`, `item_title`, `enabled`, `integrated`)
                values('sitecrowdfunding_project', 'project_id', 'sitecrowdfunding', 'Project Videos', 0, 0);");
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitecrowdfunding_admin_main_managevideo", "sitevideointegration", "Manage Videos", "", \'{"uri":"admin/sitevideo/manage-video/index/contentType/sitecrowdfunding_project/contentModule/sitecrowdfunding"}\', "sitecrowdfunding_admin_main", "", 0, 0, 19);');
            $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitevideo.video.leader.owner.sitecrowdfunding.project", "1");');
        }
        if ($this->isTableExist('engine4_sitecontentcoverphoto_modules')) {
            $db->query("INSERT IGNORE INTO engine4_sitecontentcoverphoto_modules(`module`, `resource_type`, `resource_id`, `enabled`)
                values('sitecrowdfunding', 'sitecrowdfunding_project', 'project_id',0);");
        }
    }

    public function userInfoColumns() {
        $db = $this->getDb();
        if ($this->isTableExist('engine4_seaocore_userinfo')) {

            if (!$this->_columnExist('engine4_seaocore_userinfo', 'email'))
                $db->query("ALTER TABLE engine4_seaocore_userinfo ADD email varchar(255);");
            if (!$this->_columnExist('engine4_seaocore_userinfo', 'phone'))
                $db->query("ALTER TABLE engine4_seaocore_userinfo ADD  phone varchar(255);");
            if (!$this->_columnExist('engine4_seaocore_userinfo', 'biography'))
                $db->query("ALTER TABLE engine4_seaocore_userinfo ADD  biography text;");
            if (!$this->_columnExist('engine4_seaocore_userinfo', 'facebook_profile_url'))
                $db->query("ALTER TABLE engine4_seaocore_userinfo ADD  facebook_profile_url varchar(255);");
            if (!$this->_columnExist('engine4_seaocore_userinfo', 'instagram_profile_url'))
                $db->query("ALTER TABLE engine4_seaocore_userinfo ADD  instagram_profile_url varchar(255);");
            if (!$this->_columnExist('engine4_seaocore_userinfo', 'twitter_profile_url'))
                $db->query("ALTER TABLE engine4_seaocore_userinfo ADD  twitter_profile_url varchar(255);");
            if (!$this->_columnExist('engine4_seaocore_userinfo', 'youtube_profile_url'))
                $db->query("ALTER TABLE engine4_seaocore_userinfo ADD  youtube_profile_url varchar(255);");
            if (!$this->_columnExist('engine4_seaocore_userinfo', 'vimeo_profile_url'))
                $db->query("ALTER TABLE engine4_seaocore_userinfo ADD  vimeo_profile_url varchar(255);");
            if (!$this->_columnExist('engine4_seaocore_userinfo', 'website_url'))
                $db->query("ALTER TABLE engine4_seaocore_userinfo ADD  website_url varchar(255);");
        }
    }

    public function widgetsOnMemberProfilePage() {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $page_id = $select
                        ->from('engine4_core_pages', 'page_id')
                        ->where('name = ?', 'user_profile_index')
                        ->query()->fetchColumn();
        if (!$page_id)
            return;

        $select = new Zend_Db_Select($db);
        $content_id = $select
                        ->from('engine4_core_content', 'content_id')
                        ->where('name = ?', 'core.container-tabs')
                        ->where('page_id = ?', $page_id)
                        ->query()->fetchColumn();
        if (!$content_id)
            return;

        $select = new Zend_Db_Select($db);
        $contain_widget = $select
                        ->from('engine4_core_content', 'content_id')
                        ->where('name = ?', 'sitecrowdfunding.user-biography')
                        ->where('parent_content_id = ?', $content_id)
                        ->where('page_id = ?', $page_id)
                        ->query()->fetchColumn();

        if (!$contain_widget) {
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitecrowdfunding.user-biography',
                'parent_content_id' => $content_id,
                'order' => 15,
                'params' => '{"title":"Biography","userBioOption":["phone","biography","facebook","instagram","twitter","youtube","vimeo","website"],"titleTruncation":"20","nomobile":"0","name":"sitecrowdfunding.user-biography"}',
            ));
        }

        $select = new Zend_Db_Select($db);
        $contain_widget = $select
                        ->from('engine4_core_content', 'content_id')
                        ->where('name = ?', 'sitecrowdfunding.contenttype-projects')
                        ->where('parent_content_id = ?', $content_id)
                        ->query()->fetchColumn();

        if (!$contain_widget) {
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitecrowdfunding.contenttype-projects',
                'parent_content_id' => $content_id,
                'order' => 15,
                'params' => '{"title":"Projects","itemCountPerPage":"10","projectWidth":"283","projectHeight":"510","projectOption":["title","owner","backer","like","favourite","comment","endDate","featured","sponsored","location","facebook","twitter","linkedin","googleplus"],"show_content":"2","descriptionTruncation":"175","titleTruncation":"55","nomobile":"0","name":"sitecrowdfunding.contenttype-projects"}',
            ));
        }
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
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitecrowdfunding_project", "sitecrowdfunding", \'{item:$subject} replied to a comment on {item:$owner}\'\'s project {item:$object:$title}: {body:$body}\', 1, 1, 1, 3, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("sitecrowdfunding_activityreply", "sitecrowdfunding", \'{item:$subject} has replied on {var:$projectname}.\', 0, "");');

            $db->query("INSERT IGNORE INTO `engine4_nestedcomment_modules` (`module`, `resource_type`, `enabled`) 
VALUES ('sitecrowdfunding', 'sitecrowdfunding_project', 0)");
        }
    }

    public function setManageModules() {

        $db = $this->getDb();
        //integrating crowdfunding with siteevent 
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_settings')
                ->where('name = ?', 'siteevent.isActivate')
                ->where('value = ?', 1);
        $siteevent_isActivate_object = $select->query()->fetchObject();
        if ($siteevent_isActivate_object) {
            $db->query("INSERT IGNORE INTO `engine4_sitecrowdfunding_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('siteevent_event', 'event_id', 'siteevent', '0', '0', 'Events Projects', '')");
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("siteevent_admin_main_manageproject", "sitecrowdfunding", "Manage Projects", "", \'{"uri":"admin/sitecrowdfunding/manage/index/contentType/siteevent_event/contentModule/siteevent"}\', "siteevent_admin_main", "", 0, 0, 20);');
            $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitecrowdfunding.project.leader.owner.siteevent.event", "1");');

            //ENTER THE MEMBER LEVEL SETTING FOR THE VIDEO CREATION OPTIONS IN THE EVENT PLUGIN
            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
              SELECT
                level_id as `level_id`,
                'siteevent_event' as `type`,
                'sprcreate' as `name`,
                1 as `value`,
                NULL as `params`
              FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
            ");
            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
              SELECT
                level_id as `level_id`,
                'siteevent_event' as `type`,
                'auth_sprcreate' as `name`,
                5 as `value`,
                '[\"registered\",\"owner_network\",\"owner_member_member\", \"owner_member\", \"member\", \"leader\"]' as `params`
              FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
            ");
        }

        //integrating crowdfunding with sitepage  
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitepage')
                ->where('enabled = ?', 1);
        $is_sitepage_object = $select->query()->fetchObject();
        if ($is_sitepage_object) {

            $db->query("INSERT IGNORE INTO `engine4_sitecrowdfunding_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Projects', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_manageproject", "sitecrowdfunding", "Manage Projects", "", \'{"uri":"admin/sitecrowdfunding/manage/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 0, 0, 24);');
            $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitecrowdfunding.project.leader.owner.sitepage.page", "1");');

            //ENTER THE MEMBER LEVEL SETTING FOR THE VIDEO CREATION OPTIONS IN THE PAGE PLUGIN
            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
              SELECT
                level_id as `level_id`,
                'sitepage_page' as `type`,
                'sprcreate' as `name`,
                1 as `value`,
                NULL as `params`
              FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
            ");
            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
              SELECT
                level_id as `level_id`,
                'sitepage_page' as `type`,
                'auth_sprcreate' as `name`,
                5 as `value`,
                '[\"registered\",\"owner_network\",\"owner_member_member\", \"owner_member\", \"leader\"]' as `params`
              FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
            ");
        }

        //integrating crowdfunding with sitebusiness  
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitebusiness')
                ->where('enabled = ?', 1);
        $is_sitebusiness_object = $select->query()->fetchObject();
        if ($is_sitebusiness_object) {

            $db->query("INSERT IGNORE INTO `engine4_sitecrowdfunding_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitebusiness_business', 'business_id', 'sitebusiness', '0', '0', 'Business Projects', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");

            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitebusiness_admin_main_manageproject", "sitecrowdfunding", "Manage Projects", "", \'{"uri":"admin/sitecrowdfunding/manage/index/contentType/sitebusiness_business/contentModule/sitebusiness"}\', "sitebusiness_admin_main", "", 0, 0, 24);');

            //ENTER THE MEMBER LEVEL SETTING FOR THE VIDEO CREATION OPTIONS IN THE BUSINESS PLUGIN
            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
              SELECT
                level_id as `level_id`,
                'sitebusiness_business' as `type`,
                'sprcreate' as `name`,
                1 as `value`,
                NULL as `params`
              FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
            ");
            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
              SELECT
                level_id as `level_id`,
                'sitebusiness_business' as `type`,
                'auth_sprcreate' as `name`,
                5 as `value`,
                '[\"registered\",\"owner_network\",\"owner_member_member\", \"owner_member\", \"leader\"]' as `params`
              FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
            ");
        }

        //integrating crowdfunding with sitegroup  
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitegroup')
                ->where('enabled = ?', 1);
        $is_sitegroup_object = $select->query()->fetchObject();
        if ($is_sitegroup_object) {

            $db->query("INSERT IGNORE INTO `engine4_sitecrowdfunding_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitegroup_group', 'group_id', 'sitegroup', '0', '0', 'Group Projects', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");

            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitegroup_admin_main_manageproject", "sitecrowdfunding", "Manage Projects", "", \'{"uri":"admin/sitecrowdfunding/manage/index/contentType/sitegroup_group/contentModule/sitegroup"}\', "sitegroup_admin_main", "", 0, 0, 24);');

            //ENTER THE MEMBER LEVEL SETTING FOR THE VIDEO CREATION OPTIONS IN THE PAGE PLUGIN
            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
              SELECT
                level_id as `level_id`,
                'sitegroup_group' as `type`,
                'sprcreate' as `name`,
                1 as `value`,
                NULL as `params`
              FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
            ");
            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
              SELECT
                level_id as `level_id`,
                'sitegroup_group' as `type`,
                'auth_sprcreate' as `name`,
                5 as `value`,
                '[\"registered\",\"owner_network\",\"owner_member_member\", \"owner_member\", \"leader\"]' as `params`
              FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
            ");
        }

        //integrating crowdfunding with sitereview
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitereview')
                ->where('enabled = ?', 1);
        $is_sitereview_object = $select->query()->fetchObject();
        if (!empty($is_sitereview_object)) {
            $select = new Zend_Db_Select($db);
            $listingtypeObject = $select
                    ->from('engine4_sitereview_listingtypes', array('listingtype_id', 'title_singular'))
                    ->query()
                    ->fetchAll();
            foreach ($listingtypeObject as $values) {
                $listingtype_id = $values['listingtype_id'];
                $singular_title = ucfirst($values['title_singular']);
                $db->query("INSERT IGNORE INTO `engine4_sitecrowdfunding_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitereview_listing_$listingtype_id', 'listing_id', 'sitereview', '0', '0', '$singular_title Projects', 'a:1:{i:0;s:18:\"contentlikemembers\";}')");
            }

            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitereview_admin_main_manageproject", "sitecrowdfunding", "Manage Projects", "", \'{"uri":"admin/sitecrowdfunding/manage/index/contentType/sitereview_listing_1/contentModule/sitereview"}\', "sitereview_admin_main", "", 0, 0, 83);');
        }
    }

}
