<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

require_once realpath(dirname(__FILE__)) . '/sitemodule_install.php';

class Sitepage_Installer extends SiteModule_Installer {
    protected $_deependencyVersion = array(
    'sitemobile' => '4.6.0p2',
    'advancedactivity' => '4.8.0',
    'communityad' => '4.8.0'
  );
    protected $_installConfig = array(
    'sku' => 'sitepage',
  );
    private function getSitepageVersion()   {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules', array('title', 'version'))
               ->where('name = ?', 'sitepage')
               ->where('enabled = ?', 1);
        $getModVersion = $select->query()->fetchObject();
        return $getModVersion;
    }
    function onInstall() {
        parent::onInstall();
        if( $this->_databaseOperationType == 'install') {
            $this->_addWidget();
        }
        $this->_addLayout();
        $db = $this->getDb();
        $db->query('SET @rownr=0;');
        $db->query(' UPDATE `engine4_seaocore_searchformsetting` as t1 INNER JOIN ( select @rownr:=@rownr+1 as rowNumber , searchformsetting_id as id from `engine4_seaocore_searchformsetting` where `module`="sitepage" ORDER BY `order` ASC) as t2 set t1.`order` =t2.`rowNumber` where t1.`searchformsetting_id`=t2.`id` and t1.`module`="sitepage";');
        $db->query('UPDATE `engine4_activity_actiontypes` SET `body` = \'{item:$object} added {var:$count} photo(s) to the album {itemChild:$object:sitepage_album:$child_id}:\' WHERE `engine4_activity_actiontypes`.`type` = \'sitepagealbum_admin_photo_new\' LIMIT 1 ;');
        $db->query('UPDATE `engine4_activity_actiontypes` SET `body` = \'{item:$subject} added {var:$count} photo(s) to the album {itemChild:$object:sitepage_album:$child_id} of page {item:$object}:\' WHERE `engine4_activity_actiontypes`.`type` = \'sitepagealbum_photo_new\' LIMIT 1 ;');


        //END OF CUSTOM SETTINGS

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_settings','value')
                ->where('name = ?', 'sitepage.layoutcreate');
        $removesetting = $select->query()->fetchAll();
        if ($removesetting == 0) {
            $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = 0 WHERE `name` in ('sitepage_admin_main_layoutdefault')");
        }
        $select = new Zend_Db_Select($db);
        $select->from('engine4_activity_actions', array('object_id', 'params', 'action_id'))
                ->where('type =?', 'sitepagealbum_admin_photo_new')
                ->orWhere('type =?', 'sitepagealbum_photo_new');
        $results = $select->query()->fetchAll();
        foreach ($results as $result) {
            if (strstr($result['params'], 'slug')) {
                $decoded_cover_param = Zend_Json_Decoder::decode($result['params']);
                $count = $decoded_cover_param['count'];
                $select = new Zend_Db_Select($db);
                $album_id = $select->from('engine4_sitepage_albums', 'album_id')
                                ->where('page_id =?', $result['object_id'])
                                ->order('album_id DESC')
                                ->limit(1)
                                ->query()->fetchColumn();
                $db->query('UPDATE `engine4_activity_actions` SET `params` = \' ' . array('child_id' => $album_id, 'count' => $count) . ' \' WHERE `engine4_activity_actions`.`action_id` = "' . $result['action_id'] . '" LIMIT 1 ;');
            }
        }
        //START: PUT THE BACK WIDGET ON PAGE PROFILE PAGE IF PAGE IS INTEGRATED WITH CROWDFUNDING
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'sitepage_index_view')
            ->limit(1);
        $page_id = $select->query()->fetchObject()->page_id;
        if (!empty($page_id)) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitecrowdfunding')
                    ->where('enabled = ?', 1);
            $versionCheckCrowdfunding = $select->query()->fetchObject();
            if(!empty($versionCheckCrowdfunding)) {
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_core_modules')
                        ->where('name = ?', 'sitecrowdfundingintegration')
                        ->where('enabled = ?', 1);
                $sitecrowdfundingintegrationEnabled = $select->query()->fetchObject();
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_sitecrowdfunding_modules')
                        ->where('enabled = ?', 1)
                        ->where('item_type = ?', 'sitepage_page')
                        ->where('item_module = ?', 'sitepage');
                $sitepageIntegratedWithCrowdfunding = $select->query()->fetchObject();

                if (!empty($sitecrowdfundingintegrationEnabled) && !empty($sitepageIntegratedWithCrowdfunding)) {
                    $select = new Zend_Db_Select($db);
                    $select_content = $select
                        ->from('engine4_core_content')
                        ->where('page_id = ?', $page_id)
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'sitecrowdfunding.back-project')
                        ->limit(1);
                    $content_id = $select_content->query()->fetchObject()->content_id;
                    if (empty($content_id)) {
                        $select = new Zend_Db_Select($db);
                        $select_right = $select
                            ->from('engine4_core_content')
                            ->where('page_id = ?', $page_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'right')
                            ->limit(1);
                        $right_id = $select_right->query()->fetchObject()->content_id;
                        if (!empty($right_id)) {
                            $select = new Zend_Db_Select($db);
                            $db->insert('engine4_core_content', array(
                                'page_id' => $page_id,
                                'type' => 'widget',
                                'name' => 'sitecrowdfunding.back-project',
                                'parent_content_id' => $right_id,
                                'order' => 5,
                                'params' => '{"title":"","titleCount":true,"backTitle":"Donate Now"}',
                            ));
                        }
                    }
                }
            }
        }
        //END: PUT THE BACK WIDGET ON PAGE PROFILE PAGE IF PAGE IS INTEGRATED WITH CROWDFUNDING
        $db->query("DELETE FROM `engine4_core_content` WHERE name = 'sitepage.foursquare-sitepage' and type = 'widget'");
        $db->query("DELETE FROM `engine4_sitepage_content` WHERE name = 'sitepage.foursquare-sitepage' and type = 'widget'");
        $db->query("DELETE FROM `engine4_sitepage_admincontent` WHERE name = 'sitepage.foursquare-sitepage' and type = 'widget'");
        $db->query("UPDATE `engine4_core_menuitems` SET `plugin` = 'Sitepage_Plugin_Menus::canViewSitepages' WHERE `engine4_core_menuitems`.`name` = 'core_main_sitepage' LIMIT 1");
        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('SITEPAGE_PAGE_CREATION', 'sitepage', '[host],[object_title],[sender],[object_link],[object_description]');");
        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('notify_follow_sitepage_page', 'sitepage', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');");
        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('notify_sitepage_tagged', 'sitebusiness', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');");
        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepage_admin_main_general", "sitepage", "General Settings", "", \'{"route":"admin_default","module":"sitepage","controller":"settings"}\', "sitepage_admin_main_settings", "", "1", "0", "1");');
        $db->query('UPDATE  `engine4_activity_notificationtypes` SET  `body` =  \'{item:$subject} added {var:$count} photo(s) to the album {item:$object}.\' WHERE  `engine4_activity_notificationtypes`.`type` =  "sitepagealbum_create";');
        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepage_admin_main_createedit", "sitepage", "Miscellaneous Settings", "", \'{"route":"admin_default","module":"sitepage","controller":"settings", "action":"create-edit"}\', "sitepage_admin_main_settings", "", "1", "0", "2");');

        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_membership'")->fetch();
        if (!empty($table_exist)) {
            $email_field = $db->query("SHOW COLUMNS FROM engine4_sitepage_membership LIKE 'email'")->fetch();
            if (empty($email_field)) {
                $db->query("ALTER TABLE `engine4_sitepage_membership` ADD `email` TINYINT( 1 ) NOT NULL DEFAULT '1'");
            }
            //For add column in the 'engine4_sitebusiness_membership' table.
            $action_notification_field = $db->query("SHOW COLUMNS FROM engine4_sitepage_membership LIKE 'action_email'")->fetch();
            if (empty($action_notification_field)) {
                $db->query("ALTER TABLE  `engine4_sitepage_membership` ADD `action_email` VARCHAR( 255 ) NULL");
            }
            //For add column in the 'engine4_sitebusiness_membership' table.
            $action_notification_field = $db->query("SHOW COLUMNS FROM engine4_sitepage_membership LIKE 'action_notification'")->fetch();
            if (empty($action_notification_field)) {
                $db->query("ALTER TABLE  `engine4_sitepage_membership` ADD `action_notification` VARCHAR( 255 ) NULL");
            }
        }
        // ADD COLUMN BROWSE IN PAGE TABLE META TABLE
        $meta_table_exist = $db->query('SHOW TABLES LIKE \'engine4_sitepage_page_fields_meta\'')->fetch();
        if (!empty($meta_table_exist)) {
            $column_exist = $db->query('SHOW COLUMNS FROM engine4_sitepage_page_fields_meta LIKE \'browse\'')->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_sitepage_page_fields_meta`  ADD `browse` TINYINT UNSIGNED NOT NULL DEFAULT '0';");
            }
        }
        $column_exist_action_email = $db->query('SHOW COLUMNS FROM engine4_sitepage_manageadmins LIKE \'action_email\'')->fetch();
        if (empty($column_exist_action_email)) {
            $db->query("ALTER TABLE `engine4_sitepage_manageadmins` ADD `action_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL");
        }
        $db->query("DELETE FROM `engine4_seaocore_searchformsetting` WHERE `engine4_seaocore_searchformsetting`.`module` = 'sitepage' AND `engine4_seaocore_searchformsetting`.`name` = 'profile_type' LIMIT 1");
        $sitepagePagesTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_pages\'')->fetch();
        if (!empty($sitepagePagesTable)) {
            $subpage = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'subpage'")->fetch();
            if (empty($subpage)) {
                $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `subpage` TINYINT( 1 ) NOT NULL");
            }
            $parent_id = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'parent_id'")->fetch();
            if (empty($parent_id)) {
                $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `parent_id` INT( 11 ) NOT NULL DEFAULT '0'");
            }
        }
        //DROP THE INDEX FROM THE `engine4_sitepage_lists` TABLE
        $sitepageListsTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_lists\'')->fetch();
        if (!empty($sitepageListsTable)) {
            $sitepagelistsResults = $db->query("SHOW INDEX FROM `engine4_sitepage_lists` WHERE Key_name = 'page_id'")->fetch();
            if (!empty($sitepagelistsResults)) {
                $db->query("ALTER TABLE engine4_sitepage_lists DROP INDEX page_id");
                $db->query("ALTER TABLE `engine4_sitepage_lists` ADD UNIQUE (`owner_id`, `page_id`);");
            }
        }
        //START FOLLOW WORK
        //IF 'engine4_seaocore_follows' TABLE IS NOT EXIST THAN CREATE'
        $seocoreFollowTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_follows\'')->fetch();
        if (empty($seocoreFollowTable)) {
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocore_follows` (
        `follow_id` int(11) unsigned NOT NULL auto_increment,
        `resource_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
        `resource_id` int(11) unsigned NOT NULL,
        `poster_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
        `poster_id` int(11) unsigned NOT NULL,
        `creation_date` datetime NOT NULL,
        PRIMARY KEY  (`follow_id`),
        KEY `resource_type` (`resource_type`, `resource_id`),
        KEY `poster_type` (`poster_type`, `poster_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;");
        }
        $select = new Zend_Db_Select($db);
        $advancedactivity = $select->from('engine4_core_modules', 'name')
                ->where('name = ?', 'advancedactivity')
                ->query()
                ->fetchcolumn();
        $is_enabled = $select->query()->fetchObject();
        if (!empty($advancedactivity)) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`) VALUES ("follow_sitepage_page", "sitepage", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:page}: {body:$body}\', 1, 5, 1, 1, 1, 1, 1)');
        } else {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("follow_sitepage_page", "sitepage", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:page}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        }
        //END FOLLOW WORK
        //START LIKE PRIVACY WORKENTRY FOR LIST TABLE.
        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_lists` (
            `list_id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(64) NOT NULL,
            `owner_id` int(11) NOT NULL,
            `page_id` int(11) NOT NULL,
            PRIMARY KEY (`list_id`),
            UNIQUE KEY `owner_id` (`owner_id`,`page_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitepage')
                ->where('version <= ?', '4.2.9');
        $is_enabled = $select->query()->fetchObject();
        if (!empty($is_enabled)) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_sitepage_pages', array('page_id', 'owner_id'));
            $sitepage_results = $select->query()->fetchAll();
            if (!empty($sitepage_results)) {
                foreach ($sitepage_results as $result) {
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_lists` (`title`, `owner_id`, `page_id`) VALUES ('SITEPAGE_LIKE', " . $result['owner_id'] . " , " . $result['page_id'] . ");");
                }
            }
            //START UPDATE ALL MEMBER LEVEL SETTINGS WITH NEW SETTING LIKE PRIVACY.
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_authorization_levels', array('level_id'))
                    ->where('title != ?', 'public');
            $check_sitepage = $select->query()->fetchAll();
            foreach ($check_sitepage as $modArray) {
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_authorization_permissions', array('params', 'name', 'level_id'))
                        ->where('type LIKE "%sitepage_page%"')
                        ->where('level_id = ?', $modArray['level_id'])
                        ->where('name LIKE "%auth_s%"');
                $result = $select->query()->fetchAll();
                foreach ($result as $results) {
                    $params = !empty($results['params']) ? Zend_Json::decode($results['params']) : array();
                    if ( !in_array( 'like_member', $params )) {
                        array_push( $params, 'like_member' );
                    }
                    $paramss = Zend_Json::encode($params);
                    $db->query("UPDATE `engine4_authorization_permissions` SET `params` = '$paramss' WHERE `engine4_authorization_permissions`.`type` = 'sitepage_page' AND `engine4_authorization_permissions`.`name` = '" . $results['name'] . "' AND `engine4_authorization_permissions`.`level_id` = '" . $results['level_id'] . "';");
                }
            }
            //START UPDATE ALL MEMBER LEVEL SETTINGS WITH NEW SETTING LIKE PRIVACY.
        }
        //END LIKE PRIVACY WORKENTRY FOR LIST TABLE.
        $member_titleCover = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'member_title'")->fetch();
        if (empty($member_titleCover)) {
            $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `member_title` VARCHAR( 64 ) NOT NULL");
        }
        $pageCover = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'page_cover'")->fetch();
        if (empty($pageCover)) {
            $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `page_cover` INT( 11 ) NOT NULL DEFAULT '0'");
        }
        $pageCoverParams = $db->query("SHOW COLUMNS FROM engine4_sitepage_albums LIKE 'cover_params'")->fetch();
        if (empty($pageCoverParams)) {
            $db->query("ALTER TABLE `engine4_sitepage_albums` ADD `cover_params` VARCHAR( 265 ) NULL");
        }
        $column_exist_action_notification = $db->query('SHOW COLUMNS FROM engine4_sitepage_manageadmins LIKE \'action_notification\'')->fetch();
        if (empty($column_exist_action_notification)) {
            $db->query("ALTER TABLE `engine4_sitepage_manageadmins` ADD `action_notification` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL");
        }
        $column_exist_body = $db->query('SHOW COLUMNS FROM engine4_sitepage_pages LIKE \'body\'')->fetch();
        if (!empty($column_exist_body)) {
            $db->query("ALTER TABLE  `engine4_sitepage_pages` CHANGE  `body`  `body` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
        }
        $column_exist_description = $db->query('SHOW COLUMNS FROM engine4_sitepage_contentpages LIKE \'description\'')->fetch();
        if (!empty($column_exist_description)) {
            $db->query("ALTER TABLE  `engine4_sitepage_contentpages` CHANGE  `description`  `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
        }
        $column_exist_locationname = $db->query('SHOW COLUMNS FROM engine4_sitepage_locations LIKE \'locationname\'')->fetch();
        if (empty($column_exist_locationname)) {
            $db->query("ALTER TABLE `engine4_sitepage_locations` ADD `locationname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL");
        }
        $column_exist_follow_count = $db->query('SHOW COLUMNS FROM engine4_sitepage_pages LIKE \'follow_count\'')->fetch();
        if (empty($column_exist_follow_count)) {
            $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `follow_count` int(11) NOT NULL");
        }
        //Notification seetings work
        $column_exist_email = $db->query('SHOW COLUMNS FROM engine4_sitepage_manageadmins LIKE \'email\'')->fetch();
        $column_exist_notification = $db->query('SHOW COLUMNS FROM engine4_sitepage_manageadmins LIKE \'notification\'')->fetch();
        if (empty($column_exist) && empty($column_exist_notification)) {
            $db->query("ALTER TABLE `engine4_sitepage_manageadmins` ADD `email` TINYINT( 1 ) NOT NULL DEFAULT '1'");
            $db->query("ALTER TABLE `engine4_sitepage_manageadmins` ADD `notification` TINYINT( 1 ) NOT NULL");
        }
        //START THE WORK FOR MAKE WIDGETIZE PAGE OF Locatio or map.
        // on fresh install page is not saved so this condition is commented now
        // $select = new Zend_Db_Select($db);
        // $select
        //         ->from('engine4_core_modules')
        //         ->where('name = ?', 'sitepage')
        //         ->where('version < ?', '4.2.3');
        // $is_enabled = $select->query()->fetchObject();
        // if (empty($is_enabled)) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'sitepage_index_map')
                    ->limit(1);
            $info = $select->query()->fetch();
            if (empty($info)) {
                $db->insert('engine4_core_pages', array(
                    'name' => 'sitepage_index_map',
                    'displayname' => 'Browse Pages’ Locations',
                    'title' => 'Browse Pages’ Locations',
                    'description' => 'Browse Pages’ Locations',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId('engine4_core_pages');
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'top',
                    'parent_content_id' => null,
                    'order' => 1,
                    'params' => '',
                ));
                $top_id = $db->lastInsertId('engine4_core_content');
                //CONTAINERS
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'main',
                    'parent_content_id' => Null,
                    'order' => 2,
                    'params' => '',
                ));
                $container_id = $db->lastInsertId('engine4_core_content');
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'middle',
                    'parent_content_id' => $top_id,
                    'params' => '',
                ));
                $top_middle_id = $db->lastInsertId('engine4_core_content');
                //INSERT MAIN - MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'middle',
                    'parent_content_id' => $container_id,
                    'order' => 2,
                    'params' => '',
                ));
                $middle_id = $db->lastInsertId('engine4_core_content');
                // Top Middle
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.browsenevigation-sitepage',
                    'parent_content_id' => $top_middle_id,
                    'order' => 1,
                    'params' => '',
                ));
                //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.location-search',
                    'parent_content_id' => $middle_id,
                    'order' => 2,
                    'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1"}',
                ));
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.browselocation-sitepage',
                    'parent_content_id' => $middle_id,
                    'order' => 3,
                    'params' => '{"title":"","titleCount":"true"}',
                ));
            }
        // }
        //END THE WORK FOR MAKE WIDGETIZE PAGE OF LOCATIO OR MAP.
        //START THE WORK FOR MAKE WIDGETIZE PAGE OF Locatio or map.MOBILE PAGE.
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitepage')
                ->where('version < ?', '4.2.3');
        $is_enabled = $select->query()->fetchObject();
        if (empty($is_enabled)) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'sitepage_index_mobilemap')
                    ->limit(1);
            $info = $select->query()->fetch();
            if (empty($info)) {
                $db->insert('engine4_core_pages', array(
                    'name' => 'sitepage_index_mobilemap',
                    'displayname' => 'Mobile Browse Pages’ Locations',
                    'title' => 'Mobile Browse Pages’ Locations',
                    'description' => 'Mobile Browse Pages’ Locations',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId('engine4_core_pages');
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'top',
                    'parent_content_id' => null,
                    'order' => 1,
                    'params' => '',
                ));
                $top_id = $db->lastInsertId('engine4_core_content');
                //CONTAINERS
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'main',
                    'parent_content_id' => Null,
                    'order' => 2,
                    'params' => '',
                ));
                $container_id = $db->lastInsertId('engine4_core_content');
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'middle',
                    'parent_content_id' => $top_id,
                    'params' => '',
                ));
                $top_middle_id = $db->lastInsertId('engine4_core_content');
                //INSERT MAIN - MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'container',
                    'name' => 'middle',
                    'parent_content_id' => $container_id,
                    'order' => 2,
                    'params' => '',
                ));
                $middle_id = $db->lastInsertId('engine4_core_content');
                // Top Middle
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.browsenevigation-sitepage',
                    'parent_content_id' => $top_middle_id,
                    'order' => 1,
                    'params' => '',
                ));
                //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.location-search',
                    'parent_content_id' => $middle_id,
                    'order' => 2,
                    'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1"}',
                ));
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.browselocation-sitepage',
                    'parent_content_id' => $middle_id,
                    'order' => 3,
                    'params' => '{"title":"","titleCount":"true"}',
                ));
            }
        }
        //END THE WORK FOR MAKE WIDGETIZE PAGE OF LOCATIO OR MAP.MOBILE PAGE.
        //WORK FOR CORE CONTENT PAGES
        $select = new Zend_Db_Select($db);
//     $select->from('engine4_core_content',array('params'))
//             ->where('name = ?', 'sitepage.socialshare-sitepage');
//      $result = $select->query()->fetchObject();
//     if(!empty($result->params)) {
//          $params = Zend_Json::decode($result->params);
//          if(isset($params['code'])) {
//              $code = $params['code'];
//              $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
//              ('sitepage.code.share','".$code. "');");
//          }
//     }
        //MIGRATE DATA TO 'engine4_seaocore_searchformsetting' FROM 'engine4_sitepage_searchform'
        $seocoreSearchformTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_searchformsetting\'')->fetch();
        $sitepageSearchformTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_searchform\'')->fetch();
        if (!empty($seocoreSearchformTable) && !empty($sitepageSearchformTable)) {
            $datas = $db->query('SELECT * FROM `engine4_sitepage_searchform`')->fetchAll();
            foreach ($datas as $data) {
                $data_module = 'sitepage';
                $data_name = $data['name'];
                $data_display = $data['display'];
                $data_order = $data['order'];
                $data_label = $data['label'];
                $db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES ('$data_module', '$data_name', $data_display, $data_order, '$data_label')");
            }
            $db->query('DROP TABLE IF EXISTS `engine4_sitepage_searchform`');
        }
        $table_exist = $db->query('SHOW TABLES LIKE \'engine4_sitepage_photos\'')->fetch();
        if (!empty($table_exist)) {
            $column_exist = $db->query('SHOW COLUMNS FROM engine4_sitepage_photos LIKE \'description\'')->fetch();
            if (empty($column_exist)) {
                $db->query('ALTER TABLE `engine4_sitepage_photos` CHANGE `description` `description` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
            }
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitepage')
                ->where('version <= ?', '4.1.5p1');
        $is_enabled = $select->query()->fetchObject();
        if (!empty($is_enabled)) {
            $db->query("DROP TABLE IF EXISTS `engine4_sitepage_admincontent`;");
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_admincontent` (
                                  `admincontent_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                  `page_id` int(11) unsigned NOT NULL,
                                  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
                                  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
                                  `parent_content_id` int(11) unsigned DEFAULT NULL,
                                  `order` int(11) NOT NULL DEFAULT '1',
                                  `params` text COLLATE utf8_unicode_ci,
                                  `attribs` text COLLATE utf8_unicode_ci,
                                  PRIMARY KEY (`admincontent_id`),
                                  KEY `page_id` (`page_id`,`order`)
                                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");
            $db->query("DROP TABLE IF EXISTS `engine4_sitepage_hideprofilewidgets`;");
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_hideprofilewidgets` (
                                  `hideprofilewidgets_id` int(11) NOT NULL AUTO_INCREMENT,
                                  `widgetname` varchar(64) NOT NULL,
                                  PRIMARY KEY (`hideprofilewidgets_id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_pages', array('page_id'))
                    ->where('name = ?', 'sitepage_index_view');
            $corepageObject = $select->query()->fetchAll();
            if (!empty($corepageObject)) {
                $page_id = $corepageObject[0]['page_id'];
            }
            if (!empty($page_id)) {
                $select = new Zend_Db_Select($db);
                $db->query("DELETE FROM engine4_sitepage_hideprofilewidgets");
                if (!empty($page_id)) {
                    $select = new Zend_Db_Select($db);
                    $db->query("DELETE FROM engine4_sitepage_admincontent WHERE page_id = $page_id");
                }
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_core_settings', array('value'))
                        ->where('name = ?', 'sitepage.layout.setting');
                $layoutsetting = $select->query()->fetchAll();
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_core_settings', array('value'))
                        ->where('name = ?', 'sitepage.showmore');
                $showmore = $select->query()->fetchAll();
                if (!empty($showmore)) {
                    $showmaxtab = $showmore[0]['value'];
                    $maxtab = "{\"max\":\"$showmaxtab\"}";
                } else {
                    $maxtab = "{\"max\":\"8\"}";
                }
                if ($layoutsetting[0]['value'] == 1) {
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`) VALUES
                    ($page_id, 'container', 'main', '2')");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                            ->where('name = ?', 'main')
                            ->where('type = ?', 'container');
                    $containerObject = $select->query()->fetchAll();
                    $container_id = $containerObject[0]['admincontent_id'];
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
        ($page_id, 'container', 'middle', '6', $container_id)");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                            ->where('name = ?', 'middle')
                            ->where('type = ?', 'container');
                    $containerObject = $select->query()->fetchAll();
                    $middle_id = $containerObject[0]['admincontent_id'];
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
        ($page_id, 'container', 'left', '4', $container_id)");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                            ->where('name = ?', 'left')
                            ->where('type = ?', 'container');
                    $containerObject = $select->query()->fetchAll();
                    $left_id = $containerObject[0]['admincontent_id'];
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
            ($page_id, 'widget', 'core.container-tabs', '7', $middle_id, '$maxtab')");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                            ->where('name = ?', 'core.container-tabs')
                            ->where('type = ?', 'widget');
                    $containerObject = $select->query()->fetchAll();
                    $middle_tab = $containerObject[0]['admincontent_id'];
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
        ($page_id, 'widget', 'sitepage.thumbphoto-sitepage', '1', $middle_id,'{\"title\":\"\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.title-sitepage', '2', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'seaocore.like-button', '3', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'facebookse');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', '4', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagealbum');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                  ($page_id, 'widget', 'sitepage.photorecent-sitepage', '5', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                  ($page_id, 'widget', 'sitepage.albums-sitepage', '23', $left_id,'{\"title\":\"Albums\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagemusic');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                  ($page_id, 'widget', 'sitepagemusic.profile-player', '24', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    }
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
            ($page_id, 'widget', 'sitepage.favourite-page', '25', $left_id,'{\"title\":\"Linked Pages\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.mainphoto-sitepage', '10', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.options-sitepage', '11', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.write-page', '12', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.information-sitepage', '13', $left_id,'{\"title\":\"Information\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'seaocore.people-like', '14', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagereview');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', '15', $left_id,'{\"title\":\"Ratings\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagebadge');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', '16', $left_id,'{\"title\":\"Badge\",\"titleCount\":\"true\"}')");
                    }
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.suggestedpage-sitepage', '17', $left_id,'{\"title\":\"You May Also Like\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.socialshare-sitepage', '18', $left_id,'{\"title\":\"Social Share\",\"titleCount\":\"true\"}')");
//          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
//              ($page_id, 'widget', 'sitepage.foursquare-sitepage', '19', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.insights-sitepage', '21', $left_id,'{\"title\":\"Insights\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.featuredowner-sitepage', '22', $left_id,'{\"title\":\"Owners\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'activity.feed', '1', $middle_tab,'{\"title\":\"Updates\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.info-sitepage', '2', $middle_tab,'{\"title\":\"Info\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.overview-sitepage', '3', $middle_tab,'{\"title\":\"Overview\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
            ($page_id, 'widget', 'sitepage.location-sitepage', '4', $middle_tab,'{\"title\":\"Map\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
            ($page_id, 'widget', 'core.profile-links', '125', $middle_tab,'{\"title\":\"Links\",\"titleCount\":\"true\"}')");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagealbum');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.photos-sitepage', '110', $middle_tab,'{\"title\":\"Photos\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagevideo');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepagevideo.profile-sitepagevideos', '111', $middle_tab,'{\"title\":\"Videos\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagenote');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepagenote.profile-sitepagenotes', '112', $middle_tab,'{\"title\":\"Notes\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagereview');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepagereview.profile-sitepagereviews', '113', $middle_tab,'{\"title\":\"Reviews\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepageform');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepageform.sitepage-viewform', '114', $middle_tab,'{\"title\":\"Form\",\"titleCount\":\"false\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagedocument');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepagedocument.profile-sitepagedocuments', '115', $middle_tab,'{\"title\":\"Documents\",\"titleCount\":\"false\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepageoffer');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepageoffer.profile-sitepageoffers', '116', $middle_tab,'{\"title\":\"Offers\",\"titleCount\":\"false\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepageevent');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepageevent.profile-sitepageevents', '117', $middle_tab,'{\"title\":\"Events\",\"titleCount\":\"false\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagepoll');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepagepoll.profile-sitepagepolls', '118', $middle_tab,'{\"title\":\"Polls\",\"titleCount\":\"false\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagediscussion');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                ($page_id, 'widget', 'sitepage.discussion-sitepage', '119', $middle_tab,'{\"title\":\"Discussions\",\"titleCount\":\"false\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_sitepage_contentpages', array('contentpage_id'))
                            ->where('name =?', 'sitepage_index_view');
                    $contentpages_id = $select->query()->fetchAll();
                    foreach ($contentpages_id as $key => $value) {
                        $page_id = $value['contentpage_id'];
                        $db->query("DELETE FROM engine4_sitepage_content WHERE contentpage_id = $page_id");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`) VALUES
                    ($page_id, 'container', 'main', '2')");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_sitepage_content', array('content_id'))
                                ->where('name = ?', 'main')
                                ->where('type = ?', 'container')
                                ->where('contentpage_id = ?', $page_id)
                        ;
                        $containerObject = $select->query()->fetchAll();
                        $container_id = $containerObject[0]['content_id'];
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
        ($page_id, 'container', 'middle', '6', $container_id)");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_sitepage_content', array('content_id'))
                                ->where('name = ?', 'middle')
                                ->where('type = ?', 'container')
                                ->where('contentpage_id = ?', $page_id);
                        $containerObject = $select->query()->fetchAll();
                        $middle_id = $containerObject[0]['content_id'];
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
        ($page_id, 'container', 'left', '4', $container_id)");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_sitepage_content', array('content_id'))
                                ->where('name = ?', 'left')
                                ->where('type = ?', 'container')
                                ->where('contentpage_id = ?', $page_id);
                        $containerObject = $select->query()->fetchAll();
                        $left_id = $containerObject[0]['content_id'];
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
    ($page_id, 'widget', 'core.container-tabs', '7', $middle_id, '$maxtab')");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_sitepage_content', array('content_id'))
                                ->where('name = ?', 'core.container-tabs')
                                ->where('type = ?', 'widget')
                                ->where('contentpage_id = ?', $page_id);
                        ;
                        $containerObject = $select->query()->fetchAll();
                        $middle_tab = $containerObject[0]['content_id'];
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
    ($page_id, 'widget', 'sitepage.thumbphoto-sitepage', '1', $middle_id,'{\"title\":\"\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                        ($page_id, 'widget', 'sitepage.title-sitepage', '2', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                        ($page_id, 'widget', 'seaocore.like-button', '3', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'facebookse');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', '4', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagealbum');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.photorecent-sitepage', '5', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.albums-sitepage', '23', $left_id,'{\"title\":\"Albums\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagemusic');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                              ($page_id, 'widget', 'sitepagemusic.profile-player', '24', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        }
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
            ($page_id, 'widget', 'sitepage.favourite-page', '25', $left_id,'{\"title\":\"Linked Pages\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.mainphoto-sitepage', '10', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.options-sitepage', '11', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.write-page', '12', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.information-sitepage', '13', $left_id,'{\"title\":\"Information\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'seaocore.people-like', '14', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagereview');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', '15', $left_id,'{\"title\":\"Ratings\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagebadge');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', '16', $left_id,'{\"title\":\"Badge\",\"titleCount\":\"true\"}')");
                        }
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.suggestedpage-sitepage', '17', $left_id,'{\"title\":\"You May Also Like\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.socialshare-sitepage', '18', $left_id,'{\"title\":\"Social Share\",\"titleCount\":\"true\"}')");
//            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
//                          ($page_id, 'widget', 'sitepage.foursquare-sitepage', '19', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.insights-sitepage', '21', $left_id,'{\"title\":\"Insights\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.featuredowner-sitepage', '22', $left_id,'{\"title\":\"Owners\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'activity.feed', '1', $middle_tab,'{\"title\":\"Updates\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.info-sitepage', '2', $middle_tab,'{\"title\":\"Info\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.overview-sitepage', '3', $middle_tab,'{\"title\":\"Overview\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.location-sitepage', '4', $middle_tab,'{\"title\":\"Map\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'core.profile-links', '125', $middle_tab,'{\"title\":\"Links\",\"titleCount\":\"true\"}')");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagealbum');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.photos-sitepage', '110', $middle_tab,'{\"title\":\"Photos\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagevideo');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagevideo.profile-sitepagevideos', '111', $middle_tab,'{\"title\":\"Videos\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagenote');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagenote.profile-sitepagenotes', '112', $middle_tab,'{\"title\":\"Notes\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagereview');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagereview.profile-sitepagereviews', '113', $middle_tab,'{\"title\":\"Reviews\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepageform');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepageform.sitepage-viewform', '114', $middle_tab,'{\"title\":\"Form\",\"titleCount\":\"false\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagedocument');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagedocument.profile-sitepagedocuments', '115', $middle_tab,'{\"title\":\"Documents\",\"titleCount\":\"false\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepageoffer');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepageoffer.profile-sitepageoffers', '116', $middle_tab,'{\"title\":\"Offers\",\"titleCount\":\"false\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepageevent');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepageevent.profile-sitepageevents', '117', $middle_tab,'{\"title\":\"Events\",\"titleCount\":\"false\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagepoll');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagepoll.profile-sitepagepolls', '118', $middle_tab,'{\"title\":\"Polls\",\"titleCount\":\"false\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagediscussion');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.discussion-sitepage', '119', $middle_tab,'{\"title\":\"Discussions\",\"titleCount\":\"false\"}')");
                        }
                    }
                } else {
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`) VALUES
    ($page_id, 'container', 'main', '2')");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                            ->where('name = ?', 'main')
                            ->where('type = ?', 'container');
                    $containerObject = $select->query()->fetchAll();
                    $container_id = $containerObject[0]['admincontent_id'];
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
    ($page_id, 'container', 'middle', '6', $container_id)");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                            ->where('name = ?', 'middle')
                            ->where('type = ?', 'container');
                    $containerObject = $select->query()->fetchAll();
                    $middle_id = $containerObject[0]['admincontent_id'];
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
    ($page_id, 'container', 'left', '4', $container_id)");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                            ->where('name = ?', 'left')
                            ->where('type = ?', 'container');
                    $containerObject = $select->query()->fetchAll();
                    $left_id = $containerObject[0]['admincontent_id'];
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                        ($page_id, 'widget', 'sitepage.title-sitepage', '2', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                        ($page_id, 'widget', 'seaocore.like-button', '3', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'facebookse');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', '4', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagemusic');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                              ($page_id, 'widget', 'sitepagemusic.profile-player', '24', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    }
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
            ($page_id, 'widget', 'sitepage.favourite-page', '25', $left_id,'{\"title\":\"Linked Pages\",\"titleCount\":\"true\"}')");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagealbum');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.photorecent-sitepage', '5', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.albums-sitepage', '23', $left_id,'{\"title\":\"Albums\",\"titleCount\":\"true\"}')");
                    }
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.mainphoto-sitepage', '10', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.widgetlinks-sitepage', '11', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.options-sitepage', '12', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.write-page', '13', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.information-sitepage', '14', $left_id,'{\"title\":\"Information\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'seaocore.people-like', '15', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagereview');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', '16', $left_id,'{\"title\":\"Ratings\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagebadge');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', '17', $left_id,'{\"title\":\"Badge\",\"titleCount\":\"true\"}')");
                    }
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.socialshare-sitepage', '18', $left_id,'{\"title\":\"Social Share\",\"titleCount\":\"true\"}')");
//          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
//                          ($page_id, 'widget', 'sitepage.foursquare-sitepage', '19', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.insights-sitepage', '21', $left_id,'{\"title\":\"Insights\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.featuredowner-sitepage', '22', $left_id,'{\"title\":\"Owners\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'activity.feed', '6', $middle_id,'{\"title\":\"Updates\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.info-sitepage', '7', $middle_id,'{\"title\":\"Info\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.overview-sitepage', '8', $middle_id,'{\"title\":\"Overview\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.location-sitepage', '9', $middle_id,'{\"title\":\"Map\",\"titleCount\":\"true\"}')");
                    $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'core.profile-links', '125', $middle_id,'{\"title\":\"Links\",\"titleCount\":\"true\"}')");
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagealbum');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.photos-sitepage', '110', $middle_id,'{\"title\":\"Photos\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagevideo');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagevideo.profile-sitepagevideos', '111', $middle_id,'{\"title\":\"Videos\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagenote');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagenote.profile-sitepagenotes', '112', $middle_id,'{\"title\":\"Notes\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagereview');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagereview.profile-sitepagereviews', '113', $middle_id,'{\"title\":\"Reviews\",\"titleCount\":\"true\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepageform');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepageform.sitepage-viewform', '114', $middle_id,'{\"title\":\"Form\",\"titleCount\":\"false\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagedocument');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagedocument.profile-sitepagedocuments', '115', $middle_id,'{\"title\":\"Documents\",\"titleCount\":\"false\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepageoffer');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepageoffer.profile-sitepageoffers', '116', $middle_id,'{\"title\":\"Offers\",\"titleCount\":\"false\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepageevent');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepageevent.profile-sitepageevents', '117', $middle_id,'{\"title\":\"Events\",\"titleCount\":\"false\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagepoll');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagepoll.profile-sitepagepolls', '118', $middle_id,'{\"title\":\"Polls\",\"titleCount\":\"false\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_modules')
                            ->where('name = ?', 'sitepagediscussion');
                    $is_enabled = $select->query()->fetchObject();
                    if (!empty($is_enabled)) {
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.discussion-sitepage', '119', $middle_id,'{\"title\":\"Discussions\",\"titleCount\":\"false\"}')");
                    }
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_sitepage_contentpages', array('contentpage_id'))
                            ->where('name =?', 'sitepage_index_view');
                    $contentpages_id = $select->query()->fetchAll();
                    foreach ($contentpages_id as $key => $value) {
                        $page_id = $value['contentpage_id'];
                        $db->query("DELETE FROM engine4_sitepage_content WHERE contentpage_id = $page_id");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`) VALUES
                            ($page_id, 'container', 'main', '2')");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_sitepage_content', array('content_id'))
                                ->where('name = ?', 'main')
                                ->where('type = ?', 'container')
                                ->where('contentpage_id = ?', $page_id)
                        ;
                        $containerObject = $select->query()->fetchAll();
                        $container_id = $containerObject[0]['content_id'];
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
    ($page_id, 'container', 'middle', '6', $container_id)");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_sitepage_content', array('content_id'))
                                ->where('name = ?', 'middle')
                                ->where('type = ?', 'container')
                                ->where('contentpage_id = ?', $page_id);
                        $containerObject = $select->query()->fetchAll();
                        $middle_id = $containerObject[0]['content_id'];
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
    ($page_id, 'container', 'left', '4', $container_id)");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_sitepage_content', array('content_id'))
                                ->where('name = ?', 'left')
                                ->where('type = ?', 'container')
                                ->where('contentpage_id = ?', $page_id);
                        $containerObject = $select->query()->fetchAll();
                        $left_id = $containerObject[0]['content_id'];
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                        ($page_id, 'widget', 'sitepage.title-sitepage', '2', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                        ($page_id, 'widget', 'sitepage.favourite-page', '25', $left_id,'{\"title\":\"Linked Pages\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                        ($page_id, 'widget', 'seaocore.like-button', '3', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'facebookse');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', '4', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagealbum');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.photorecent-sitepage', '5', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.albums-sitepage', '23', $left_id,'{\"title\":\"Albums\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagemusic');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                              ($page_id, 'widget', 'sitepagemusic.profile-player', '24', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        }
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.mainphoto-sitepage', '10', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.widgetlinks-sitepage', '11', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.options-sitepage', '12', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.write-page', '13', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.information-sitepage', '14', $left_id,'{\"title\":\"Information\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'seaocore.people-like', '15', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagereview');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', '16', $left_id,'{\"title\":\"Ratings\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagebadge');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', '17', $left_id,'{\"title\":\"Badge\",\"titleCount\":\"true\"}')");
                        }
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.socialshare-sitepage', '18', $left_id,'{\"title\":\"Social Share\",\"titleCount\":\"true\"}')");
//            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
//                          ($page_id, 'widget', 'sitepage.foursquare-sitepage', '19', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.insights-sitepage', '21', $left_id,'{\"title\":\"Insights\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.featuredowner-sitepage', '22', $left_id,'{\"title\":\"Owners\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'activity.feed', '6', $middle_id,'{\"title\":\"Updates\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.info-sitepage', '7', $middle_id,'{\"title\":\"Info\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.overview-sitepage', '8', $middle_id,'{\"title\":\"Overview\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.location-sitepage', '9', $middle_id,'{\"title\":\"Map\",\"titleCount\":\"true\"}')");
                        $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'core.profile-links', '125', $middle_id,'{\"title\":\"Links\",\"titleCount\":\"true\"}')");
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagealbum');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.photos-sitepage', '110', $middle_id,'{\"title\":\"Photos\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagevideo');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagevideo.profile-sitepagevideos', '111', $middle_id,'{\"title\":\"Videos\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagenote');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagenote.profile-sitepagenotes', '112', $middle_id,'{\"title\":\"Notes\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagereview');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagereview.profile-sitepagereviews', '113', $middle_id,'{\"title\":\"Reviews\",\"titleCount\":\"true\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepageform');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepageform.sitepage-viewform', '114', $middle_id,'{\"title\":\"Form\",\"titleCount\":\"false\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagedocument');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagedocument.profile-sitepagedocuments', '115', $middle_id,'{\"title\":\"Documents\",\"titleCount\":\"false\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepageoffer');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepageoffer.profile-sitepageoffers', '116', $middle_id,'{\"title\":\"Offers\",\"titleCount\":\"false\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepageevent');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepageevent.profile-sitepageevents', '117', $middle_id,'{\"title\":\"Events\",\"titleCount\":\"false\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagepoll');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepagepoll.profile-sitepagepolls', '118', $middle_id,'{\"title\":\"Polls\",\"titleCount\":\"false\"}')");
                        }
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_modules')
                                ->where('name = ?', 'sitepagediscussion');
                        $is_enabled = $select->query()->fetchObject();
                        if (!empty($is_enabled)) {
                            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
                            ($page_id, 'widget', 'sitepage.discussion-sitepage', '119', $middle_id,'{\"title\":\"Discussions\",\"titleCount\":\"false\"}')");
                        }
                    }
                }
            }
        }
        $type_array = $db->query("SHOW COLUMNS FROM engine4_core_likes LIKE 'creation_date'")->fetch();
        if (empty($type_array)) {
            $run_query = $db->query("ALTER TABLE `engine4_core_likes` ADD `creation_date` DATETIME NOT NULL");
        }
        //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }
        //CODE FOR INCREASE THE SIZE OF engine4_authorization_allow's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_allow LIKE 'resource_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_allow` CHANGE `resource_type` `resource_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }
        //CODE FOR INCREASE THE SIZE OF engine4_activity_attachments's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_attachments LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_attachments` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }
        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'subject_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `subject_type` `subject_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }
        $pageTime = time();
        $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
        ('sitepage.basetime', $pageTime ),
        ('sitepage.isvar', 0 ),
        ('sitepage.filepath', 'Sitepage/controllers/license/license2.php');");
        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'object_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `object_type` `object_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }
        //
        // Mobile Pages Home
        // page
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitepage_mobi_home')
                ->limit(1);
        ;
        $info = $select->query()->fetch();
        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitepage_mobi_home',
                'displayname' => 'Mobile Pages Home',
                'title' => 'Mobile Pages Home',
                'description' => 'This is the mobile verison of a Pages home page.',
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
                'name' => 'sitepage.browsenevigation-sitepage',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.zeropage-sitepage',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.search-sitepage',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.recently-popular-random-sitepage',
                'parent_content_id' => $middle_id,
                'order' => 4,
                'params' => '{"title":"","titleCount":"true"}',
            ));
        }
        // Mobile Browse Pages
        // page
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitepage_mobi_index')
                ->limit(1);
        ;
        $info = $select->query()->fetch();
        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitepage_mobi_index',
                'displayname' => 'Mobile Browse Pages',
                'title' => 'Mobile Browse Pages',
                'description' => 'This is the mobile verison of a pages browse page.',
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
                'name' => 'sitepage.browsenevigation-sitepage',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.search-sitepage',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.pages-sitepage',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":"true"}',
            ));
        }
        //
        // Mobile Pages Profile
        // page
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitepage_mobi_view')
                ->limit(1);
        ;
        $info = $select->query()->fetch();
        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitepage_mobi_view',
                'displayname' => 'Mobile Page Profile',
                'title' => 'Mobile Page Profile',
                'description' => 'This is the mobile verison of a listing profile.',
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
                'name' => 'sitepage.title-sitepage',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.mainphoto-sitepage',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            // middle tabs
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'parent_content_id' => $middle_id,
                'order' => 4,
                'params' => '{"max":"6"}',
            ));
            $tab_middle_id = $db->lastInsertId('engine4_core_content');
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'activity.feed',
                'parent_content_id' => $tab_middle_id,
                'order' => 1,
                'params' => '{"title":"Updates","titleCount":"true","max_photo":"8"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.info-sitepage',
                'parent_content_id' => $tab_middle_id,
                'order' => 2,
                'params' => '{"title":"Info","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.overview-sitepage',
                'parent_content_id' => $tab_middle_id,
                'order' => 3,
                'params' => '{"title":"Overview","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.location-sitepage',
                'parent_content_id' => $tab_middle_id,
                'order' => 4,
                'params' => '{"title":"Map","titleCount":"true"}',
            ));
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitepage')
                ->where('version >= ?', '4.1.6')
                ->where('version < ?', '4.1.7');
        $oldVersion = $select->query()->fetchObject();
        if (!empty($oldVersion)) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitepage.profile.search')
                    ->limit(1);
            $info = $select->query()->fetch();
            if (!empty($info)) {
                if ($info['value'] == 1) {
                    $db->update('engine4_seaocore_searchformsetting', array('display' => $info['value']), array('module' => 'sitepage', 'name = ?' => 'profile_type'));
                } else {
                    $db->update('engine4_seaocore_searchformsetting', array('display' => $info['value']), array('module' => 'sitepage', 'name = ?' => 'profile_type'));
                }
                $db->delete('engine4_core_settings', array('name = ?' => 'sitepage.profile.search'));
            }
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitepage')
                ->where('version < ?', '4.1.7p1');
        $is_latestversion = $select->query()->fetchObject();
        if (!empty($is_latestversion)) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_activity_actiontypes')->where('type =?', 'sitepage_profile_photo_update')->where('module =?', 'sitepage')->limit(1);
            $fetchInfo = $select->query()->fetch();
            if (empty($fetchInfo)) {
                $db->insert('engine4_activity_actiontypes', array(
                    'type' => 'sitepage_profile_photo_update',
                    'module' => 'sitepage',
                    'body' => '{item:$subject} changed their Page profile photo.',
                    'enabled' => 1,
                    'displayable' => 3,
                    'attachable' => 2,
                    'commentable' => 1,
                    'shareable' => 1,
                    'is_generated' => 1,
                ));
            }
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_pages')->where('name =?', 'sitepage_index_index')->limit(1);
            $fetchPageId = $select->query()->fetch();
            if (!empty($fetchPageId)) {
                $select = new Zend_Db_Select($db);
                $select = $select->from('engine4_core_content')
                        ->where('page_id =?', $fetchPageId['page_id'])
                        ->where('type = ?', 'container')
                        ->where('name = ?', 'top')
                        ->limit(1);
                $container_id = $select->query()->fetch();
                if (!empty($container_id)) {
                    $select = new Zend_Db_Select($db);
                    $select = $select->from('engine4_core_content')
                            ->where('page_id =?', $fetchPageId['page_id'])
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->where('parent_content_id = ?', $container_id['content_id'])
                            ->limit(1);
                    $middle_id = $select->query()->fetch();
                    if (!empty($middle_id)) {
                        $select = new Zend_Db_Select($db);
                        $select = $select->from('engine4_core_content')
                                ->where('page_id =?', $fetchPageId['page_id'])
                                ->where('name = ?', 'sitepage.alphabeticsearch-sitepage')
                                ->where('parent_content_id = ?', $middle_id['content_id'])
                                ->limit(1);
                        $fetchWidgetContentId = $select->query()->fetchAll();
                        if (empty($fetchWidgetContentId)) {
                            $db->insert('engine4_core_content', array(
                                'page_id' => $fetchPageId['page_id'],
                                'type' => 'widget',
                                'name' => 'sitepage.alphabeticsearch-sitepage',
                                'parent_content_id' => $middle_id['content_id'],
                                'order' => 4,
                                'params' => '{"title":"","titleCount":"true"}',
                            ));
                        }
                    }
                }
            }
            $adColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_packages LIKE 'ads'")->fetch();
            if (empty($adColumn)) {
                $run_query = $db->query("ALTER TABLE `engine4_sitepage_packages` ADD `ads` BOOL NOT NULL DEFAULT '1'");
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_core_modules')
                        ->where('name = ?', 'communityad');
                $is_enabled = $select->query()->fetchObject();
                if ($is_enabled) {
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_settings')
                            ->where('name = ?', 'sitepage.communityads')
                            ->limit(1);
                    $info = $select->query()->fetch();
                    if (!empty($info)) {
                        $communitadSetting = $info['value'];
                        if (!empty($communitadSetting)) {
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_settings')
                                    ->where('name = ?', 'sitepage.adwithpackage')
                                    ->limit(1);
                            $info = $select->query()->fetch();
                            if (!empty($info)) {
                                $showAdWithPackage = $info['value'];
                                if (!empty($showAdWithPackage)) {
                                    $select = new Zend_Db_Select($db);
                                    $select->from('engine4_sitepage_packages')->where('price > ?', 0);
                                    $info = $select->query()->fetchAll();
                                    foreach ($info as $data) {
                                        $db->update('engine4_sitepage_packages', array('ads' => $showAdWithPackage), array('package_id = ?' => $data['package_id']));
                                    }
                                } else {
                                    $select = new Zend_Db_Select($db);
                                    $select->from('engine4_sitepage_packages')->where('price > ?', 0);
                                    $info = $select->query()->fetchAll();
                                    foreach ($info as $data) {
                                        $db->update('engine4_sitepage_packages', array('ads' => $showAdWithPackage), array('package_id = ?' => $data['package_id']));
                                    }
                                    $select = new Zend_Db_Select($db);
                                    $select->from('engine4_sitepage_packages')->where('price = ?', 0);
                                    $info = $select->query()->fetchAll();
                                    foreach ($info as $data) {
                                        $db->update('engine4_sitepage_packages', array('ads' => 1), array('package_id = ?' => $data['package_id']));
                                    }
                                }
                                $db->delete('engine4_core_settings', array('name = ?' => 'sitepage.adwithpackage'));
                            }
                        }
                    }
                }
            }
        }
        //REMOVED WIDGET SETTING TAB FROM ADMIN PANEL
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')
                ->where('name = ?', 'sitepage')
                ->where('version <= ?', '4.1.7p2');
        $is_enabled = $select->query()->fetchObject();
        if (!empty($is_enabled)) {
            $widget_names = array('comment', 'recent', 'likes', 'popular', 'random', 'mostdiscussed', 'usersitepage', 'suggest', 'locations', 'recently', 'recentlyfriend', 'pagelike', 'favourite', 'feature', 'sponserdsitepage');
            foreach ($widget_names as $widget_name) {
                $widget_type = $widget_name;
                $widget_name = 'sitepage.' . $widget_name . '-sitepage';
                $setting_name = 'sitepage.' . $widget_type . '.widgets';
                if ($widget_type == 'locations') {
                    $setting_name = 'sitepage.' . 'popular' . '.locations';
                    $widget_name = 'sitepage.' . 'popularlocations' . '-sitepage';
                } elseif ($widget_type == 'recently') {
                    $setting_name = 'sitepage.' . 'recently' . '.view';
                    $widget_name = 'sitepage.' . 'recentview' . '-sitepage';
                } elseif ($widget_type == 'recentlyfriend') {
                    $setting_name = 'sitepage.' . 'recentlyfriend' . '_view';
                    $widget_name = 'sitepage.' . 'recentfriend' . '-sitepage';
                } elseif ($widget_type == 'pagelike') {
                    $setting_name = 'sitepage.' . 'pagelike' . '.view';
                    $widget_name = 'sitepage.' . 'page' . '-like';
                } elseif ($widget_type == 'favourite') {
                    $setting_name = 'sitepage.' . 'favourite' . '.pages';
                    $widget_name = 'sitepage.' . 'favourite' . '-page';
                } elseif ($widget_type == 'suggest') {
                    $setting_name = 'sitepage.' . 'suggest' . '.sitepages';
                    $widget_name = 'sitepage.' . 'suggestedpage' . '-sitepage';
                } elseif ($widget_type == 'comment') {
                    $widget_name = 'sitepage.' . 'mostcommented' . '-sitepage';
                } elseif ($widget_type == 'recent') {
                    $widget_name = 'sitepage.' . 'recentlyposted' . '-sitepage';
                } elseif ($widget_type == 'likes') {
                    $widget_name = 'sitepage.' . 'mostlikes' . '-sitepage';
                } elseif ($widget_type == 'popular') {
                    $widget_name = 'sitepage.' . 'mostviewed' . '-sitepage';
                } elseif ($widget_type == 'mostdiscussed') {
                    $widget_name = 'sitepage.' . 'mostdiscussion' . '-sitepage';
                } elseif ($widget_type == 'usersitepage') {
                    $widget_name = 'sitepage.' . 'userpage' . '-sitepage';
                } elseif ($widget_type == 'feature') {
                    $widget_name = 'sitepage.' . 'slideshow' . '-sitepage';
                } elseif ($widget_type == 'sponserdsitepage') {
                    $widget_name = 'sitepage.' . 'sponsored' . '-sitepage';
                }
                $total_items = $db->select()
                        ->from('engine4_core_settings', array('value'))
                        ->where('name = ?', $setting_name)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
                if (empty($total_items)) {
                    $total_items = '';
                }
                //WORK FOR CORE CONTENT PAGES
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
                $widgets = $select->query()->fetchAll();
                foreach ($widgets as $widget) {
                    $explode_params = explode('}', $widget['params']);
                    if (!empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"')) {
                        $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';
                        $db->update('engine4_core_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
                    }
                }
                //WORK FOR ADMIN USER CONTENT PAGE
                $select = new Zend_Db_Select($db);
                $select->from('engine4_sitepage_admincontent', array('name', 'params', 'admincontent_id'))->where('name = ?', $widget_name);
                $widgets = $select->query()->fetchAll();
                foreach ($widgets as $widget) {
                    $explode_params = explode('}', $widget['params']);
                    if (!empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"')) {
                        $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';
                        $db->update('engine4_sitepage_admincontent', array('params' => $params), array('admincontent_id = ?' => $widget['admincontent_id'], 'name = ?' => $widget_name));
                    }
                }
                //WORK FOR USER CONTENT PAGES
                $select = new Zend_Db_Select($db);
                $select->from('engine4_sitepage_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
                $widgets = $select->query()->fetchAll();
                foreach ($widgets as $widget) {
                    $explode_params = explode('}', $widget['params']);
                    if (!empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"')) {
                        $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';
                        $db->update('engine4_sitepage_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
                    }
                }
            }
            // SITEPAGE AJAX BASED TAB HOME PAGE WIDGETS START
            $viewsOfPageDb = $db->select()
                    ->from('engine4_core_settings', array('value'))
                    ->where('name like ?', 'sitepage.ajax.widgets.layout%')
                    ->query()
                    ->fetchAll();
            if (count($viewsOfPageDb) > 0) {
                $viewsOfPage = array();
                foreach ($viewsOfPageDb as $value)
                    $viewsOfPage[] = $value['value'];
            } else {
                $viewsOfPage = array("0" => "1", "1" => "2", "2" => "3");
            }
            $diffaultView = $db->select()
                    ->from('engine4_core_settings', array('value'))
                    ->where('name = ?', 'sitepage.ajax.layouts.oder')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (empty($diffaultView)) {
                $diffaultView = 1;
            }
            $select = new Zend_Db_Select($db);
            $widget_name = 'sitepage.pages-sitepage';
            $select->from('engine4_core_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
            $widgets = $select->query()->fetchAll();
            foreach ($widgets as $widget) {
                $explode_params = explode('}', $widget['params']);
                if (!empty($explode_params[0]) && !strstr($explode_params[0], '"layouts_views"')) {
                    $params = $explode_params[0] . ',"layouts_views":' . '["' . join('","', $viewsOfPage) . '"]' . ',"layouts_oder":' . $diffaultView . '}';
                    $db->update('engine4_core_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
                }
            }
            $enableTabsDb = $db->select()
                    ->from('engine4_core_settings', array('value'))
                    ->where('name like ?', 'sitepage.ajax.widgets.list%')
                    ->query()
                    ->fetchAll();
            if (count($enableTabsDb) > 0) {
                $enableTabs = array();
                foreach ($enableTabsDb as $value)
                    $enableTabs[] = $value['value'];
            } else {
                $enableTabs = array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5');
            }
            $select = new Zend_Db_Select($db);
            $widget_name = 'sitepage.recently-popular-random-sitepage';
            $select->from('engine4_core_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
            $widgets = $select->query()->fetchAll();
            foreach ($widgets as $widget) {
                $explode_params = explode('}', $widget['params']);
                if (!empty($explode_params[0]) && !strstr($explode_params[0], '"layouts_views"')) {
                    $params = $explode_params[0] . ',"layouts_views":' . '["' . join('","', $viewsOfPage) . '"]' . ',"layouts_oder":' . $diffaultView . ',"layouts_tabs":' . '["' . join('","', $enableTabs) . '"]' . ',"recent_order":1,"popular_order":2,"random_order":3,"featured_order":4,"sponosred_order":5,"list_limit":10,"grid_limit":15}';
                    $db->update('engine4_core_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
                }
            }
            $db->delete('engine4_core_settings', array('name like ?' => 'sitepage.ajax.widgets.layout%'));
            $db->delete('engine4_core_settings', array('name = ?' => 'sitepage.ajax.layouts.oder'));
            $db->delete('engine4_core_settings', array('name like ?' => 'sitepage.ajax.widgets.list%'));
            // SITEPAGE AJAX BASED TAB HOME PAGE WIDGETS END
        }
        //DROP THE INDEX FROM THE "engine4_sitepage_itemofthedays" TABLE
        $itemofthedayResults = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'itemoftheday_id'")->fetch();
        if (!empty($itemofthedayResults)) {
            $db->query("ALTER TABLE engine4_sitepage_itemofthedays DROP INDEX itemoftheday_id");
        }
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_albums'")->fetch();
        if (!empty($table_exist)) {
            //ADD THE INDEX FROM THE "engine4_sitepageevent_membership" TABLE
            $ownerIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_albums` WHERE Key_name = 'owner_id'")->fetch();
            if (empty($ownerIdColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_albums` ADD INDEX ( `owner_id` );");
            }
            //DROP THE COLUMN FROM THE "engine4_sitepage_albums" TABLE
            $ownerTypeColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_albums LIKE 'owner_type'")->fetch();
            if (!empty($ownerTypeColumn)) {
                $db->query("ALTER TABLE `engine4_sitepage_albums` DROP `owner_type`");
            }
            //DROP THE COLUMN FROM THE "engine4_sitepage_albums" TABLE
            $typeTypeColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_albums LIKE 'type'")->fetch();
            if (!empty($typeTypeColumn)) {
                $db->query("ALTER TABLE `engine4_sitepage_albums` CHANGE `type` `type` ENUM( 'note', 'overview','wall', 'announcements', 'discussions', 'cover' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
            }
        }
        //QUERIES TRANFER FROM UPGRADE FILE OF 4.1.7P2
        $itemTable = $db->query("SHOW TABLES LIKE 'engine4_sitepage_itemofthedays'")->fetch();
        if (!empty($itemTable)) {
            $titleColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'title'")->fetch();
            if (!empty($titleColumn)) {
                $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` DROP `title`");
            }
            $pageIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'page_id'")->fetch();
            $endTimeColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'endtime'")->fetch();
            $dateColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'date'")->fetch();
            $endDateColoum = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'end_date'")->fetch();
            $startDateColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'start_date'")->fetch();
            //$dateColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'date'")->fetch();
            //$endTimeColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'endtime'")->fetch();
            $endDateColoumIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'end_date'")->fetch();
            $startDateColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'start_date'")->fetch();
//      if (!empty($dateColumn) && empty($dateColumnIndex)) {
//        $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` ADD INDEX ( `date` );");
//      }
//
//      if (!empty($endTimeColumn) && empty($endTimeColumnIndex)) {
//        $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` ADD INDEX ( `endtime` );");
//      }
            if (!empty($endDateColoum) && empty($endDateColoumIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` ADD INDEX ( `end_date` );");
            }
            if (!empty($startDateColumn) && empty($startDateColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` ADD INDEX ( `start_date` );");
            }
            if (!empty($pageIdColumn) && !empty($endTimeColumn) && !empty($dateColumn)) {
                $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` CHANGE `page_id` `resource_id` INT( 11 ) NOT NULL ,
                    CHANGE `endtime` `end_date` DATE NOT NULL, CHANGE `date` `start_date` DATE NOT NULL");
            }
            $resourceTypeColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'resource_type'")->fetch();
            $resourceTypeColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'resource_type'")->fetch();
            if (empty($resourceTypeColumn)) {
                $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` ADD `resource_type` VARCHAR( 64 ) NOT NULL");
            }
            if (!empty($resourceTypeColumn) && empty($resourceTypeColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` ADD INDEX (`resource_type`)");
                $db->query("UPDATE `engine4_sitepage_itemofthedays` SET `resource_type` = 'sitepage_page' WHERE `engine4_sitepage_itemofthedays` .`resource_type` = ''");
            }
        }
        $pageTable = $db->query("SHOW TABLES LIKE 'engine4_sitepage_pages'")->fetch();
        $networkPrivacyColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'networks_privacy'")->fetch();
        if (!empty($pageTable)) {
            if (empty($networkPrivacyColumn)) {
                $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `networks_privacy` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
            }
            $subsubCategoryIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'subsubcategory_id'")->fetch();
            if (empty($subsubCategoryIdColumn)) {
                $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `subsubcategory_id` INT( 11 ) NOT NULL");
            }
        }
        $categoryTable = $db->query("SHOW TABLES LIKE 'engine4_sitepage_categories'")->fetch();
        if (!empty($categoryTable)) {
            $userIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_categories LIKE 'user_id'")->fetch();
            if (!empty($userIdColumn)) {
                $db->query("ALTER TABLE `engine4_sitepage_categories` DROP `user_id`");
            }
            $subcatDependencyColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_categories LIKE 'subcat_dependency'")->fetch();
            if (empty($subcatDependencyColumn)) {
                $db->query("ALTER TABLE `engine4_sitepage_categories` ADD `subcat_dependency` INT( 11 ) NOT NULL");
            }
        }
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_claims'")->fetch();
        if (!empty($table_exist)) {
            //ADD THE INDEX FROM THE "engine4_sitepage_claims" TABLE
            $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_claims` WHERE Key_name = 'page_id'")->fetch();
            if (empty($pageIdColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_claims` ADD INDEX ( `page_id` );");
            }
            //ADD THE INDEX FROM THE "engine4_sitepage_claims" TABLE
            $userIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_claims` WHERE Key_name = 'user_id'")->fetch();
            if (empty($userIdColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_claims` ADD INDEX ( `user_id` );");
            }
        }
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_contentpages`'")->fetch();
        if (!empty($table_exist)) {
            //ADD THE INDEX FROM THE "engine4_sitepage_contentpages" TABLE
            $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_contentpages` WHERE Key_name = 'page_id'")->fetch();
            if (empty($pageIdColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_contentpages` ADD INDEX ( `page_id` );");
            }
            //ADD THE INDEX FROM THE "engine4_sitepage_contentpages" TABLE
            $userIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_contentpages` WHERE Key_name = 'user_id'")->fetch();
            if (empty($userIdColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_contentpages` ADD INDEX ( `user_id` );");
            }
            //ADD THE INDEX FROM THE "engine4_sitepage_contentpages" TABLE
            $nameColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_contentpages` WHERE Key_name = 'name'")->fetch();
            if (empty($nameColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_contentpages` ADD INDEX ( `name` );");
            }
        }
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_favourites'")->fetch();
        if (!empty($table_exist)) {
            //ADD THE INDEX FROM THE "engine4_sitepage_favourites" TABLE
            $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_favourites` WHERE Key_name = 'page_id'")->fetch();
            if (empty($pageIdColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_favourites` ADD INDEX ( `page_id` );");
            }
            //ADD THE INDEX FROM THE "engine4_sitepage_favourites" TABLE
            $ownerIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_favourites` WHERE Key_name = 'owner_id'")->fetch();
            if (empty($ownerIdColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_favourites` ADD INDEX ( `owner_id` );");
            }
        }
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_manageadmins'")->fetch();
        if (!empty($table_exist)) {
            //ADD THE INDEX FROM THE "engine4_sitepage_manageadmins" TABLE
            $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_manageadmins` WHERE Key_name = 'page_id'")->fetch();
            if (empty($pageIdColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_manageadmins` ADD INDEX ( `page_id` );");
            }
        }
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_pagestatistics'")->fetch();
        if (!empty($table_exist)) {
            //ADD THE INDEX FROM THE "engine4_sitepage_pagestatistics" TABLE
            $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pagestatistics` WHERE Key_name = 'page_id'")->fetch();
            if (empty($pageIdColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_pagestatistics` ADD INDEX ( `page_id` );");
            }
            //ADD THE INDEX FROM THE "engine4_sitepage_pagestatistics" TABLE
            $viewerIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pagestatistics` WHERE Key_name = 'viewer_id'")->fetch();
            if (empty($viewerIdColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_pagestatistics` ADD INDEX ( `viewer_id` );");
            }
        }
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_listmemberclaims'")->fetch();
        if (!empty($table_exist)) {
            //ADD THE INDEX FROM THE "engine4_sitepage_listmemberclaims" TABLE
            $userIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_listmemberclaims` WHERE Key_name = 'user_id'")->fetch();
            if (empty($userIdColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_listmemberclaims` ADD INDEX ( `user_id` );");
            }
        }
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_content'")->fetch();
        if (!empty($table_exist)) {
            $widgetAdminColumn = $db->query("SHOW COLUMNS FROM `engine4_sitepage_content` LIKE 'widget_admin'")->fetch();
            if (empty($widgetAdminColumn)) {
                $db->query("ALTER TABLE `engine4_sitepage_content` ADD `widget_admin` BOOL NOT NULL DEFAULT '1'");
            }
            if (!empty($widgetAdminColumn)) {
                $db->query("ALTER TABLE `engine4_sitepage_content` CHANGE `widget_admin` `widget_admin` TINYINT( 1 ) NOT NULL DEFAULT '1'");
            }
        }
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')
                ->where('name = ?', 'sitepage')
                ->where('version <= ?', '4.2.3');
        $is_old_version = $select->query()->fetchObject();
        if ($is_old_version) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_sitepage_admincontent');
            $adminContentResults = $select->query()->fetchAll();
            if (!empty($adminContentResults)) {
                $contentArray = array();
                foreach ($adminContentResults as $value) {
                    if (!in_array($value['name'], array('core.html-block', 'core.ad-campaign'))) {
                        $db->update('engine4_sitepage_content', array('widget_admin' => 1), array('name = ?' => $value['name']));
                    } else {
                        $contentArray[] = $value;
                    }
                }
                foreach ($contentArray as $value) {
                    $db->update('engine4_sitepage_content', array('widget_admin' => 1), array('name = ?' => $value['name'], 'params = ?' => $value['params']));
                }
            }
        }
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_admincontent'")->fetch();
        if (!empty($table_exist)) {
            $defaultColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_admincontent LIKE 'default_admin_layout'")->fetch();
            if (empty($defaultColumn)) {
                $db->query("ALTER TABLE `engine4_sitepage_admincontent` ADD `default_admin_layout` BOOL NOT NULL DEFAULT '0'");
            }
        }
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_settings', array('value'))
                ->where('name = ?', 'sitepage.feed.type');
        $feedType = $select->query()->fetchAll();
        if (!empty($feedType) && $feedType[0]['value'] == 1) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')->where('name = ?', 'sitepage')->where('version <= ?', '4.2.0p1');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_activity_actions')->where('subject_type = ?', 'sitepage_page');
                $resultAction = $select->query()->fetchAll();
                if (!empty($resultAction)) {
                    foreach ($resultAction as $result) {
                        $db->query("UPDATE `engine4_activity_actions` SET `subject_type` = '" . $result['object_type'] . "',
        `subject_id` = " . $result['object_id'] . ", `object_type` = '" . $result['subject_type'] . "',
        `object_id` = " . $result['subject_id'] . " WHERE `engine4_activity_actions`.`action_id` =
        " . $result['action_id'] . " ;");
                        $db->query("UPDATE `engine4_activity_stream` SET `subject_type` = '" . $result['object_type'] . "',
        `subject_id` = " . $result['object_id'] . ", `object_type` = '" . $result['subject_type'] . "',
        `object_id` = " . $result['subject_id'] . " WHERE `engine4_activity_stream`.`action_id` =
        " . $result['action_id'] . " ;");
                    }
                }
                $select = new Zend_Db_Select($db);
                $select->from('engine4_activity_stream')->where('object_type = ?', 'sitepage_page')->group('action_id');
                $resultStreams = $select->query()->fetchAll();
                if (!empty($resultStreams)) {
                    foreach ($resultStreams as $result) {
                        $db->query("INSERT IGNORE INTO `engine4_activity_stream` (`target_type`, `target_id`,
        `subject_type`, `subject_id`, `object_type`, `object_id`, `type`, `action_id`) VALUES ('sitepage_page',
        " . $result['object_id'] . " , '" . $result['subject_type'] . "', " . $result['subject_id'] . ",
        '" . $result['object_type'] . "', " . $result['object_id'] . ", '" . $result['type'] . "', " .
                                $result['action_id'] . ");");
                    }
                }
            }
        }
        //ADD NEW COLUMN IN engine4_sitepage_imports TABLE
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_imports'")->fetch();
        if (!empty($table_exist)) {
            $column_exist = $db->query("SHOW COLUMNS FROM engine4_sitepage_imports LIKE 'userclaim'")->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_sitepage_imports` ADD `userclaim` TINYINT( 1 ) NOT NULL DEFAULT '0'");
            }
        }
//    //CHECK THAT foursquare_text COLUMN EXIST OR NOT IN PAGE TABLE
//    $column_exist = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'foursquare_text'")->fetch();
//    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_pages'")->fetch();
//    if (!empty($column_exist) && !empty($table_exist)) {
//
//      $column_type = $db->query("SELECT data_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'engine4_sitepage_pages' AND COLUMN_NAME = 'foursquare_text'")->fetch();
//
//      if ($column_type != 'tinyint') {
//
//        //FETCH PAGES
//        $pages = $db->select()->from('engine4_sitepage_pages', array('foursquare_text', 'page_id'))->query()->fetchAll();
//
//        if (!empty($pages)) {
//          foreach ($pages as $page) {
//            $page_id = $page['page_id'];
//            $foursquare_text = $page['foursquare_text'];
//
//            if (!empty($page_id)) {
//
//              //UPDATE FOURSQUARE TEXT VALUE
//              if (!empty($foursquare_text)) {
//                $db->update('engine4_sitepage_pages', array('foursquare_text' => 1), array('page_id = ?' => $page_id));
//              } else {
//                $db->update('engine4_sitepage_pages', array('foursquare_text' => 0), array('page_id = ?' => $page_id));
//              }
//            }
//          }
//        }
//      }
//
//      $db->query("ALTER TABLE `engine4_sitepage_pages` CHANGE `foursquare_text` `foursquare_text` TINYINT(1) NULL DEFAULT '0'");
//    }
        //START SOCIAL SHARE WIDGET WORK
        //CHECK PLUGIN VERSION
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitepage')
                ->where('version < ?', '4.2.1');
        $is_enabled_module = $select->query()->fetchObject();
        if (!empty($is_enabled_module)) {
            $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitepage.socialshare-sitepage"}';
            $db->update('engine4_core_content', array('params' => $social_share_default_code,), array('name =?' => 'sitepage.socialshare-sitepage'));
            $db->update('engine4_sitepage_content', array('params' => $social_share_default_code,), array('name =?' => 'sitepage.socialshare-sitepage'));
        }
        //MAKING A COLOMN IN THE SITEPAGE_PAGE TABLE
        $type_array = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'fbpage_url' ")->fetch();
        if (empty($type_array)) {
            $run_query = $db->query("ALTER TABLE  `engine4_sitepage_pages` ADD  `fbpage_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
        }
        //END SOCIAL SHARE WIDGET WORK
        $db->query('UPDATE  `engine4_core_content` SET  `name` =  "seaocore.like-button" WHERE  `engine4_core_content`.`name` ="sitepage.page-like-button";');
        $db->query('UPDATE  `engine4_sitepage_content` SET  `name` =  "seaocore.like-button" WHERE  `engine4_sitepage_content`.`name` ="sitepage.page-like-button";');
        $db->query('UPDATE  `engine4_core_content` SET  `name` =  "seaocore.people-like" WHERE  `engine4_core_content`.`name` ="sitepage.page-like";');
        $db->query('UPDATE  `engine4_sitepage_content` SET  `name` =  "seaocore.people-like" WHERE  `engine4_sitepage_content`.`name` ="sitepage.page-like";');
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitepage_index_pinboard_browse')
                ->limit(1);
        $info = $select->query()->fetch();
        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitepage_index_pinboard_browse',
                'displayname' => 'Browse Pages’ Pinboard View',
                'title' => 'Browse Pages’ Pinboard View',
                'description' => 'Browse Pages’ Pinboard View',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');
            //CONTAINERS
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => Null,
                'order' => 2,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'params' => '',
            ));
            $top_middle_id = $db->lastInsertId('engine4_core_content');
            //INSERT MAIN - MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');
            // Top Middle
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.browsenevigation-sitepage',
                'parent_content_id' => $top_middle_id,
                'order' => 1,
                'params' => '',
            ));
            //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.horizontal-search',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1","browseredirect":"pinboard"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitepage.pinboard-browse',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":true,"postedby":"1","showoptions":["likeCount","followCount","memberCount"],"detactLocation":"0","defaultlocationmiles":"1000","itemWidth":"274","withoutStretch":"0","itemCount":"12","show_buttons":["comment","like","share","facebook","twitter","pinit","tellAFriend"],"truncationDescription":"100","nomobile":"0"}',
            ));
        }
//     $db->delete('engine4_core_content', array('name =?' => 'sitepage.thumbphoto-sitepage'));
//     $db->delete('engine4_sitepage_admincontent', array('name =?' => 'sitepage.thumbphoto-sitepage'));
//     $db->delete('engine4_sitepage_content', array('name =?' => 'sitepage.thumbphoto-sitepage'));
//      $select = new Zend_Db_Select($db);
//      $select
//                      ->from('engine4_core_pages', array('page_id'))
//                      ->where('name = ?', 'sitepage_index_view');
//      $corepageObject = $select->query()->fetchObject();
//      $select = new Zend_Db_Select($db);
//      $select
//                      ->from('engine4_core_settings', array('value'))
//                      ->where('name = ?', 'sitepage.core.cover.layout');
//      $coreSettingsObject = $select->query()->fetchObject();
//
//      if (!empty($corepageObject) && empty($coreSettingsObject)) {
//          $page_id = $corepageObject->page_id;
//          $select = new Zend_Db_Select($db);
//
//          $select
//                          ->from('engine4_core_content', array('name'))
//                          ->where('name=?', 'left')->where('name=?', 'right')->where('page_id=?', $page_id);
//          $corepageObject = $select->query()->fetchObject();
//
//          if(empty($corepageObject)) {
//              $select = new Zend_Db_Select($db);
//              $select
//                              ->from('engine4_core_content', array('name'))
//                              ->where('name = ?', 'left')
//                              ->where('page_id = ?', $page_id);
//              $corepageObject = $select->query()->fetchObject();
//              if($corepageObject) {
//                  $db->update('engine4_core_content', array('name' => 'right'), array('name = ?' => 'left', 'page_id =?' => $page_id));
//              }
//              $db->delete('engine4_core_content', array('name =?' => 'sitepage.mainphoto-sitepage'));
//              $db->delete('engine4_core_content', array('name =?' => 'sitepagemember.profile-sitepagemembers-announcements'));
//              $db->delete('engine4_core_content', array('name =?' => 'seaocore.like-buttons'));
//              $db->delete('engine4_core_content', array('name =?' => 'seaocore.seaocore-follow'));
//              $db->delete('engine4_core_content', array('name =?' => 'facebookse.facebookse-commonlike'));
//              $db->delete('engine4_core_content', array('name =?' => 'sitepage.title-sitepage'));
//              $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('sitepage.core.cover.layout', 1);");
//          }
//      }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitetagcheckin')
                ->where('enabled = ?', 1);
        $is_sitetagcheckin_object = $select->query()->fetchObject();
        if (!empty($is_sitetagcheckin_object)) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitetagcheckin_spal_photo_new", "sitetagcheckin", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title} - {var:$prefixadd} {var:$location}.", 1, 5, 1, 3, 1, 1)');
        }
//     $select = new Zend_Db_Select($db);
//     $select
//             ->from('engine4_core_modules')
//             ->where('name = ?', 'sitepage')
//             ->where('version <= ?', '4.6.0p1');
//     $is_enabled = $select->query()->fetchObject();
//     if (!empty($is_enabled)) {
        // $db->query("DROP TABLE IF EXISTS `engine4_sitepage_mobileadmincontent`;");
        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_mobileadmincontent` (
  `mobileadmincontent_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `parent_content_id` int(11) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `params` text COLLATE utf8_unicode_ci,
  `attribs` text COLLATE utf8_unicode_ci,
  `module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `default_admin_layout` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mobileadmincontent_id`),
  KEY `page_id` (`page_id`,`order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");
        // $db->query("DROP TABLE IF EXISTS `engine4_sitepage_mobilecontent`;");
        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_mobilecontent` (
  `mobilecontent_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mobilecontentpage_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `parent_content_id` int(11) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `params` text COLLATE utf8_unicode_ci,
  `attribs` text COLLATE utf8_unicode_ci,
  `module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `widget_admin` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`mobilecontent_id`),
  KEY `page_id` (`mobilecontentpage_id`,`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
        //$db->query("DROP TABLE IF EXISTS `engine4_sitepage_mobilecontentpages`;");
        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_mobilecontentpages` (
  `mobilecontentpage_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `page_id` int(11) unsigned NOT NULL,
  `name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `displayname` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `keywords` text COLLATE utf8_unicode_ci NOT NULL,
  `custom` tinyint(1) NOT NULL DEFAULT '1',
  `fragment` tinyint(1) NOT NULL DEFAULT '0',
  `layout` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `view_count` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`mobilecontentpage_id`),
  KEY `page_id` (`page_id`),
  KEY `user_id` (`user_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if ($is_sitemobile_object) {
            include APPLICATION_PATH . "/application/modules/Sitepage/controllers/license/mobileLayoutCreation.php";
        }
        // }
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`,`is_object_thumb`) VALUES ("sitepage_admin_profile_photo", "sitepage", "{item:$object} updated a new profile photo.", 1, 3, 2, 1, 1, 1, 1);');
        //MAKE WIDGITIZE PAGE FOR THE PAGES I LIKE AND PAGES I JOINED AND CREATE PAGE AND EDIT PAGE AND EDT.
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitepage')
                ->where('version < ?', '4.8.6p7');
        $is_enabled_module = $select->query()->fetchObject();
        if ($is_enabled_module) {
            $this->makeWidgitizeManagePage('sitepage_index_manage', 'Directory / Pages - Manage Pages', 'My Pages', 'This page lists a user\'s Pages\'s.');
        }
        $this->makeWidgitizePage('sitepage_index_edit', 'Directory / Pages - Edit Page', 'Edit Page', 'This is page edit page.');
        $this->makeWidgitizePage('sitepage_index_create', 'Directory / Pages - Create Page', 'Create new Page', 'This is page create page.');
        $this->makeWidgitizePage('sitepage_like_my-joined', 'Directory / Pages - Manage Page (Pages I\'ve Joined)', 'Pages I\'ve Joined', 'This page lists a user\'s Pages\'s which user\'s have joined.');
        $this->makeWidgitizePage('sitepage_like_mylikes', 'Directory / Pages - Manage Page (Pages I Like)', 'Pages I Like', 'This page lists a user\'s Pages\'s which user\'s likes.');
        $this->makeWidgitizePage('sitepage_manageadmin_my-pages', 'Directory / Pages - Manage Page (Pages I Admin)', 'Pages I Admin', 'This page lists a user\'s Pages\'s of which user\'s is admin.');
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteevent')
                ->where('enabled = ?', 1);
        $is_siteevent_object = $select->query()->fetchObject();
        if ($is_siteevent_object) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitepage.isActivate')
                    ->where('value = ?', 1);
            $sitepage_isActivate_object = $select->query()->fetchObject();
            if ($sitepage_isActivate_object) {
                $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_page_host", "siteevent", \'{item:$subject} has made your page {var:$page} host of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.\', "");');
                $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES("SITEEVENT_PAGE_HOST", "siteevent", "[host],[email],[sender],[event_title_with_link],[event_url],[page_title_with_link]");');
                $itemMemberTypeColumn = $db->query("SHOW COLUMNS FROM `engine4_siteevent_modules` LIKE 'item_membertype'")->fetch();
                if (empty($itemMemberTypeColumn)) {
                    $db->query("ALTER TABLE `engine4_siteevent_modules` ADD `item_membertype` VARCHAR( 255 ) NOT NULL AFTER `item_title`");
                }
                $db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Events', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
                $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_manage", "siteevent", "Manage Events", "", \'{"uri":"admin/siteevent/manage/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 1, 0, 24);');
                $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "siteevent.event.leader.owner.sitepage.page", "1");');
            }
        }
        $db->query('UPDATE `engine4_activity_notificationtypes` SET `body` = \'{item:$subject} has liked {item:$object}.\' WHERE `engine4_activity_notificationtypes`.`type` = "sitepage_contentlike" LIMIT 1 ;');
        $db->query('UPDATE `engine4_activity_notificationtypes` SET `body` = \'{item:$subject} has commented on {item:$object}.\' WHERE `engine4_activity_notificationtypes`.`type` = "sitepage_contentcomment" LIMIT 1 ;');
        $categoriesTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_categories\'')->fetch();
        if (!empty($categoriesTable)) {
            $catDependencyIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_categories` WHERE Key_name = 'cat_dependency'")->fetch();
            if (empty($catDependencyIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_categories` ADD INDEX ( `cat_dependency` )");
            }
            $subcatDependencyIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_categories` WHERE Key_name = 'subcat_dependency'")->fetch();
            if (empty($subcatDependencyIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_categories` ADD INDEX ( `subcat_dependency` )");
            }
        }
        $favouritesTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_favourites\'')->fetch();
        if (!empty($favouritesTable)) {
            $pageIdForIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_favourites` WHERE Key_name = 'page_id_for'")->fetch();
            if (empty($pageIdForIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_favourites` ADD INDEX ( `page_id_for` )");
            }
        }
        $pagesTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_pages\'')->fetch();
        if (!empty($pagesTable)) {
            $categoryIdIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pages` WHERE Key_name = 'category_id'")->fetch();
            if (empty($categoryIdIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX ( `category_id` )");
            }
            $parentIdIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pages` WHERE Key_name = 'parent_id'")->fetch();
            if (empty($parentIdIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX ( `parent_id` )");
            }
            $profileTypeIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pages` WHERE Key_name = 'profile_type'")->fetch();
            if (empty($profileTypeIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX ( `profile_type` )");
            }
            $featuredIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pages` WHERE Key_name = 'featured'")->fetch();
            $sponsoredIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pages` WHERE Key_name = 'sponsored'")->fetch();
            if (empty($featuredIndex) && empty($sponsoredIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX ( `featured` )");
                $db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX ( `sponsored` )");
                $db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX featured_sponsored ( `featured`, `sponsored` )");
            }
            $searchIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pages` WHERE Key_name = 'closed'")->fetch();
            if (empty($searchIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX closed ( `search`,`closed`,`approved`,`declined`,`draft` )");
            }
        }
        $profilemapsTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_profilemaps\'')->fetch();
        if (!empty($profilemapsTable)) {
            $categoryIdIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_profilemaps` WHERE Key_name = 'category_id'")->fetch();
            if (empty($categoryIdIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_profilemaps` ADD INDEX ( `category_id` )");
            }
        }
        $itemTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_itemofthedays\'')->fetch();
        if (!empty($itemTable)) {
            $dateColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'date'")->fetch();
            $endTimeColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'endtime'")->fetch();
            if (!empty($dateColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` DROP INDEX `date`;");
            }
            if (!empty($endTimeColumnIndex)) {
                $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` DROP INDEX `endtime`;");
            }
        }
        $this->_changeWidgetParam($db, 'sitepage', '4.8.0p2');
        $table_import_exist = $db->query('SHOW TABLES LIKE \'engine4_sitepage_imports\'')->fetch();
        if (!empty($table_import_exist)) {
            $img_column = $db->query("SHOW COLUMNS FROM engine4_sitepage_imports LIKE 'img_name'")->fetch();
            if (empty($img_column)) {
                $db->query("ALTER TABLE `engine4_sitepage_imports` ADD `img_name` VARCHAR( 512 ) NOT NULL AFTER `tags`");
            }
            $column_subsub_category = $db->query("SHOW COLUMNS FROM engine4_sitepage_imports LIKE 'subsub_category'")->fetch();
            if (empty($column_subsub_category)) {
                $db->query("ALTER TABLE `engine4_sitepage_imports` ADD `subsub_category` VARCHAR( 255 ) NOT NULL AFTER `sub_category`");
            }
        }
        $table_importfiles_exist = $db->query('SHOW TABLES LIKE \'engine4_sitepage_importfiles\'')->fetch();
        if (!empty($table_importfiles_exist)) {
            $column_filename = $db->query("SHOW COLUMNS FROM engine4_sitepage_importfiles LIKE 'photo_filename'")->fetch();
            if (empty($column_filename)) {
                $db->query("ALTER TABLE `engine4_sitepage_importfiles` ADD `photo_filename` VARCHAR( 255 ) NOT NULL AFTER `filename`");
            }
        }
        $this->setActivityFeeds();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitevideointegration')
                ->where('enabled = ?', 1);
        $is_sitevideointegration_object = $select->query()->fetchObject();
        if ($is_sitevideointegration_object) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitepage.isActivate')
                    ->where('value = ?', 1);
            $sitepage_isActivate_object = $select->query()->fetchObject();
            if ($sitepage_isActivate_object) {
                $db->query("INSERT IGNORE INTO `engine4_sitevideo_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Videos', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
                $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_managevideo", "sitevideointegration", "Manage Videos", "", \'{"uri":"admin/sitevideo/manage-video/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 0, 0, 24);');
                $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitevideo.video.leader.owner.sitepage.page", "1");');
            }
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
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitepage.isActivate')
                    ->where('value = ?', 1);
            $sitepage_isActivate_object = $select->query()->fetchObject();
            if ($sitepage_isActivate_object) {
                $db->query("INSERT IGNORE INTO `engine4_sitecrowdfunding_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Projects', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
                $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_manageproject", "sitecrowdfunding", "Manage Projects", "", \'{"uri":"admin/sitecrowdfunding/manage/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 0, 0, 25);');
                $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitecrowdfunding.project.leader.owner.sitepage.page", "1");');
            }
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
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitepage.isActivate')
                    ->where('value = ?', 1);
            $sitepage_isActivate_object = $select->query()->fetchObject();
            if ($sitepage_isActivate_object) {

                $db->query("INSERT IGNORE INTO `engine4_sitemusic_modules` (`resource_type`, `resource_id`, `resource_module`, `enabled`, `integrated`, `resource_title`, `resource_membertype`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Music', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
                $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_managemusic", "sitecrowdfunding", "Manage Music", "", \'{"uri":"admin/sitemusic/manage/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 0, 0, 25);');
                $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitemusic.playlist.leader.owner.sitepage.page", "1");');
            }
        }
    }


        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'documentintegration')
                ->where('enabled = ?', 1);
        $is_documentintegration_object = $select->query()->fetchObject();
        if ($is_documentintegration_object) {
            $db->query("INSERT IGNORE INTO `engine4_document_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Documents')");
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_managedocument", "documentintegration", "Manage Documents", "", \'{"uri":"admin/document/manage-document/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 0, 0, 25);');
            $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "document.leader.owner.sitepage.page", "1");');
        }
    }
    public function _addWidget()
    {
        $db = $this->getDb();

        // profile page
        $pageId = $db->select()
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'sitepage_index_view')
          ->limit(1)
          ->query()
          ->fetchColumn();

        $select = new Zend_Db_Select($db);
        $select
          ->from('engine4_core_content')
          ->where('page_id = ?', $pageId)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'sitepage.add-button-sitepage')
          ;
        $info = $select->query()->fetch();
        if( empty($info) ) {
          $select = new Zend_Db_Select($db);
          $select
            ->from('engine4_core_content')
            ->where('type = ?', 'container')
            ->where('page_id = ?', $pageId)
            ->where('name = ?', 'right')
            ->limit(1);
          $tabId = $select->query()->fetchObject()->content_id;
          if(empty($tabId)) {
            $select = new Zend_Db_Select($db);
          $select
            ->from('engine4_core_content')
            ->where('type = ?', 'container')
            ->where('page_id = ?', $pageId)
            ->where('name = ?', 'left')
            ->limit(1);
          $tabId = $select->query()->fetchObject()->content_id;
          }

          // tab on profile
          if(!empty($tabId)) {
            $db->insert('engine4_core_content', array(
              'page_id' => $pageId,
              'type'    => 'widget',
              'name'    => 'sitepage.add-button-sitepage',
              'parent_content_id' => $tabId,
              'order'   => 1,
            ));
            $db->insert('engine4_core_content', array(
              'page_id' => $pageId,
              'type'    => 'widget',
              'name'    => 'sitepage.timing-sitepage',
              'parent_content_id' => $tabId,
              'order'   => 1,
              'params' => '{"title":"Operating Hours","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
              'page_id' => $pageId,
              'type'    => 'widget',
              'name'    => 'sitepage.page-services',
              'parent_content_id' => $tabId,
              'order'   => 1,
              'params' => '{"title":"Page services","titleCount":"true"}',
            ));
            }
          }
    }
    public function _addLayout()
    {
        $db = $this->getDb();
        //Create a page for the layout of profile page.
        for ($layoutNum = 1; $layoutNum <= 3; $layoutNum++) {
            $select = new Zend_Db_Select($db);
            $pageLayout_id = $db->select()
                        ->from('engine4_core_pages', 'page_id')
                        ->where('name = ?', "sitepage_index_view_layout_".$layoutNum)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
            if (empty($pageLayout_id)) {
                $db->insert('engine4_core_pages', array(
                    'name' => "sitepage_index_view_layout_".$layoutNum,
                    'displayname' => "Layout ".$layoutNum,
                    'title' => "Layout ".$layoutNum,
                    'description' => "Page Profile Layout",
                    'custom' => 1,
                ));
                $page_id = $db->lastInsertId();
                $db->insert('engine4_sitepage_definedlayouts', array(
                                'page_id' => $page_id,
                                'title' => 'Layout '.$layoutNum,
                                'photo_id' => "application/modules/Sitepage/externals/images/defined_layout/".'layout'."$layoutNum".".png",
                                'status' => 1,
                ));
                //INSERT MAIN
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
                $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'core.container-tabs',
                        'parent_content_id' => $main_middle_id,
                        'order' => 5,
                        'params' => '{"max":"6"}',
                    ));
                $tab_middle_id = $db->lastInsertId();
                // Insert right-middle
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'right',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_id,
                    'order' => 5,
                ));
                $right_id = $db->lastInsertId();
                if ($layoutNum != 3) {
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
                    if($layoutNum == 1) {
                        $db->insert('engine4_core_content', array(
                            'type' => 'widget',
                            'name' => 'sitecontentcoverphoto.content-cover-photo',
                            'page_id' => $page_id,
                            'parent_content_id' => $top_middle_id,
                            'order' => 1,
                            'params' => '{"modulename":"sitepage_page","showContent_0":"","showContent_sitepage_page":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","featured","sponsored","shareOptions"],"profile_like_button":"1","columnHeight":"400","template":"template_3","contentFullWidth":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","title":"","nomobile":"0"}',
                        ));
                    } else {
                        $db->insert('engine4_core_content', array(
                            'type' => 'widget',
                            'name' => 'sitecontentcoverphoto.content-cover-photo',
                            'page_id' => $page_id,
                            'parent_content_id' => $top_middle_id,
                            'order' => 1,
                            'params' => '{"modulename":"sitepage_page","showContent_0":"","showContent_sitepage_page":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","featured","sponsored","shareOptions"],"profile_like_button":"1","columnHeight":"400","template":"template_2","contentFullWidth":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","title":"","nomobile":"0"}',
                        ));
                    }
                } else {
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'sitecontentcoverphoto.content-cover-photo',
                        'page_id' => $page_id,
                        'parent_content_id' => $main_middle_id,
                        'order' => 1,
                        'params' => '{"modulename":"sitepage_page","showContent_0":"","showContent_sitepage_page":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","featured","sponsored","shareOptions"],"profile_like_button":"1","columnHeight":"400","template":"template_2","contentFullWidth":"0","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","title":"","nomobile":"0"}',
                    ));
                }
                $this->insertMiddleWidget($page_id,$main_middle_id,$tab_middle_id);
                $this->insertRightWidget($page_id,$right_id);
            } else {
                $db->query('UPDATE `engine4_core_content` SET `name` = \'' .'activity.feed\''.', `params` = \'{"title":"Updates","titleCount":"true","max_photo":"8"}'.'\' WHERE  `engine4_core_content`.`page_id` = '.$pageLayout_id.' and `engine4_core_content`.`name` = "' .'seaocore.feed'. '" LIMIT 1 ;');
            }
        }
    }
    public function insertMiddleWidget($page_id, $main_middle_id, $tab_middle_id) {
        $db = $this->getDb();
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.page-profile-breadcrumb',
            'page_id' => $page_id,
            'parent_content_id' => $main_middle_id,
            'order' => 2,
        ));
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.contactdetails-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $main_middle_id,
            'order' => 3,
        ));
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'activity.feed',
            'page_id' => $page_id,
            'parent_content_id' => $tab_middle_id,
            'order' => 1,
            'params' => '{"title":"Updates","titleCount":"true","max_photo":"8"}',
        ));
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.info-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $tab_middle_id,
            'order' => 2,
            'params' => '{"title":"Info","titleCount":"true"}',
        ));
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.overview-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $tab_middle_id,
            'order' => 3,
            'params' => '{"title":"Overview","titleCount":"true"}',
        ));
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.location-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $tab_middle_id,
            'order' => 4,
            'params' => '{"title":"Map","titleCount":"true"}',
        ));
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'core.profile-links',
            'page_id' => $page_id,
            'parent_content_id' => $tab_middle_id,
            'order' => 5,
        ));
    }

    public function insertRightWidget($page_id,$right_id) {
        $db = $this->getDb();
         $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.add-button-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 2,
        ));
         // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.information-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 3,
            'params' => '{"title":"Information","titleCount":"true"}',
        ));
         // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.timing-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 4,
            'params' => '{"title":"Operating Hours","titleCount":"true"}',
        ));
        echo "$right_middle_id";
         // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.page-services',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' =>5,
            'params' => '{"title":"Page Services","titleCount":"true"}',
        ));
         // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.verify-button',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 1,
        ));
         // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.options-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 6,
        ));
         // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'seaocore.people-like',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 7,
        ));
         // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.suggestedpage-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 8,
            'params' => '{"title":"You May Also Like","titleCount":"true"}',
        ));
        // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.socialshare-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 9,
            'params' => '{"title":"Social Share","titleCount":"true"}',
        ));
        // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.insights-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 10,
            'params' => '{"title":"Insights","titleCount":"true"}',
        ));
         // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.featuredowner-sitepage',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 11,
            'params' => '{"title":"Page admins","titleCount":"true"}',
        ));
         // Insert content
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'sitepage.favourite-page',
            'page_id' => $page_id,
            'parent_content_id' => $right_id,
            'order' => 12,
            'params' => '{"title":"Social Share","titleCount":"true"}',
        ));
    }
    public function onPostInstall() {
        parent::onPostInstall();
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if (!empty($is_sitemobile_object)) {
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES ('sitepage','1')");
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_sitemobile_modules')
                    ->where('name = ?', 'sitepage')
                    ->where('integrated = ?', 0);
            $is_sitemobile_object = $select->query()->fetchObject();
            if ($is_sitemobile_object) {
                $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
                $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
                if ($controllerName == 'manage' && $actionName == 'install') {
                    $view = new Zend_View();
                    $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitepage/integrated/0/redirect/install');
                }
            }
        }

        //AUTO-FILL FUNCTION FOR DAFAULT FIELDS FOR DEFAULT TEMPLATE 5
        $this->customTableQueries();

        //Work for the word changes in the page plugin .csv file.
//        $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
//        $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
//        if ($controllerName == 'manage' && ($actionName == 'install' || $actionName == 'query')) {
//            $view = new Zend_View();
//            $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
//            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
//            if ($actionName == 'install') {
//                $redirector->gotoUrl($baseUrl . 'admin/sitepage/settings/language/redirect/install');
//            } else {
//                $redirector->gotoUrl($baseUrl . 'admin/sitepage/settings/language/redirect/query');
//            }
//        }
    }
    function onDisable() {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules', array('name'))
                ->where('enabled = ?', 1);
        $moduleData = $select->query()->fetchAll();
        $subModuleArray = array("sitepagealbum", "sitepagebadge", "sitepagediscussion", "sitepagedocument", "sitepageevent", "sitepageform", "sitepageinvite", "sitepagenote", "sitepageoffer", "sitepagepoll", "sitepagereview", "sitepagevideo", "sitepagemusic", "sitepagewishlist", "sitepageadmincontact", "sitepageurl");
        foreach ($moduleData as $key => $moduleName) {
            if (in_array($moduleName['name'], $subModuleArray)) {
                $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
                $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Note: Please disable all the integrated sub-modules of Pages Plugin before disabling the Pages Plugin itself.');
                echo "<div style='background-color: #E9F4FA;border-radius:7px 7px 7px 7px;float:left;overflow: hidden;padding:10px;'><div style='background:#FFFFFF;border:1px solid #D7E8F1;overflow:hidden;padding:20px;'><span style='color:red'>$error_msg1</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.</div></div>";
                die;
            }
        }
        parent::onDisable();
    }
    public function makeWidgitizeManagePage($pagename, $displayname, $title, $description) {
        $db = $this->getDb();
        //Create a page for the edit page.
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', "$pagename")
                ->limit(1);
        $info = $select->query()->fetch();
        if (!empty($info)) {
            $info_page_id = $info['page_id'];
            $db->query("DELETE FROM `engine4_core_pages` WHERE `engine4_core_pages`.`page_id` = $info_page_id LIMIT 1");
            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $info_page_id");
        }
        $db->insert('engine4_core_pages', array(
            'name' => $pagename,
            'displayname' => $displayname,
            'title' => $title,
            'description' => $description,
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
            'params' => '{"title":"","titleCount":false,"showLinks":["pageAdmin","pageClaimed","pageLiked"],"nomobile":"0","name":"sitepage.links-sitepage"}',
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
        //  }
    }
    public function makeWidgitizePage($pagename, $displayname, $title, $description) {
        $db = $this->getDb();
        //Create a page for the edit page.
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', "$pagename")
                ->limit(1);
        $info = $select->query()->fetch();
        // insert if it doesn't exist yet
        if (empty($info)) {
            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => $pagename,
                'displayname' => $displayname,
                'title' => $title,
                'description' => $description,
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();
            // Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $main_id = $db->lastInsertId();
            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
            ));
            $main_middle_id = $db->lastInsertId();
            // Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }
    protected function _changeWidgetParam($db, $pluginName, $version) {
        $isModuleExist = $db->query("SELECT * FROM `engine4_core_modules` WHERE `name` = '$pluginName'")->fetch();
        if (!empty($isModuleExist)) {
            $curr_module_version = strcasecmp($isModuleExist['version'], $version);
            if ($curr_module_version < 0) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_content', array('params', 'content_id'))
                        ->where('name LIKE ?', '%' . $pluginName . '%');
                $results = $select->query()->fetchAll();
                foreach ($results as $result) {
                    if (strstr($result['params'], '"titleCount":}')) {
                        $result['params'] = str_replace('"titleCount":}', '"titleCount":true}', $result['params']);
                        $db->query('UPDATE `engine4_core_content` SET `params` = \' ' . $result['params'] . ' \' WHERE `engine4_core_content`.`content_id` = "' . $result['content_id'] . '" LIMIT 1 ;');
                    }
                }
            }
        }
    }
    public function setActivityFeeds() {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'nestedcomment')
                ->where('enabled = ?', 1);
        $is_nestedcomment_object = $select->query()->fetchObject();
        if ($is_nestedcomment_object) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitepage_page", "sitepage", \'{item:$subject} replied to a comment on {item:$owner}\'\'s page {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitepage_album", "sitepagealbum", \'{item:$subject} replied to a comment on {item:$owner}\'\'s page album {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitepage_photo", "sitepagealbum", \'{item:$subject} replied to a comment on {item:$owner}\'\'s page album photo {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("sitepage_activityreply", "sitepage", \'{item:$subject} has replied on {var:$eventname}.\', 0, "");');
        }
    }

        //QUERIES FOR CUSTOM TYPE PACKAGE TABLES
        public function customTableQueries()
        {
            $db = $this->getDb();
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_templates` (
              `template_id` int(200) NOT NULL AUTO_INCREMENT,
              `template_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
              `template_values` text COLLATE utf8_unicode_ci,
              `layout` int(1) DEFAULT NULL,
              `active` int(1) DEFAULT '0',
              `default` int(1) DEFAULT '0',
              PRIMARY KEY (`template_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_layouts` (
              `layout_id` int(200) NOT NULL AUTO_INCREMENT,
              `layout_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
              `structure_type` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
              `enabled` int(1) DEFAULT '1',
              `default` int(1) DEFAULT '0',
              PRIMARY KEY (`layout_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_fields` (
              `field_id` int(200) NOT NULL AUTO_INCREMENT,
              `structure_type` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
              `feature_order` int(200) DEFAULT NULL,
              `feature_label` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
              PRIMARY KEY (`field_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
             $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_values` (
              `value_id` int(200) NOT NULL AUTO_INCREMENT,
              `field_id` int(200) DEFAULT NULL,
              `package_id` int(200) DEFAULT NULL,
              `value` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
              PRIMARY KEY (`value_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
             $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_packageorder` (
              `packageorder_id` int(200) NOT NULL AUTO_INCREMENT,
              `package_id` int(200) DEFAULT NULL,
              `order` int(5) DEFAULT NULL,
              PRIMARY KEY (`packageorder_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");


            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
            ("sitepage_admin_main_plans", "sitepage", "Manage Packages", "", "{\"route\":\"admin_default\",\"module\":\"sitepage\",\"controller\":\"package\", \"action\" : \"index\"}", "sitepage_admin_main_package", "", "1", "0", "1"),
            ("sitepage_admin_main_templates", "sitepage", "Manage Templates", "", "{\"route\":\"admin_default\",\"module\":\"sitepage\",\"controller\":\"package\", \"action\" : \"subscription-templates\"}", "sitepage_admin_main_package", "", "1", "0", "2"),
            ("sitepage_admin_main_layouts", "sitepage", "Manage Layouts", "", "{\"route\":\"admin_default\",\"module\":\"sitepage\",\"controller\":\"package\", \"action\" : \"manage-layouts\"}", "sitepage_admin_main_package", "", "1", "0", "3"),
            ("sitepage_admin_main_field", "sitepage", "Custom Feature Sets", "", "{\"route\":\"admin_default\",\"module\":\"sitepage\",\"controller\":\"package\", \"action\" : \"subscription-fields\"}", "sitepage_admin_main_package", "", "1", "0", "4"),
            ("sitepage_admin_main_synchronous", "sitepage", "Synchronize Data", "", "{\"route\":\"admin_default\",\"module\":\"sitepage\",\"controller\":\"package\", \"action\" : \"synchronous\"}", "sitepage_admin_main_package", "", "1", "0", "5");');
            $db->query('SET sql_mode="NO_AUTO_VALUE_ON_ZERO";' );
            $db->query('INSERT IGNORE INTO `engine4_sitepage_templates` ( `template_id`, `template_name`, `template_values`, `layout`, `active`, `default`) VALUES
            (0,"Template Zero", "{\"template_packagename_textcolor\": {\"value\":\"000000\", \"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_packagename_textcolor\",\"order\":2,\"label\":\"Package Name Text Color\",\"description\":\"Select text color for package name.\"}]],\"order\":2} },\"template_packagename_textsize\":{\"value\":\"20\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Package Name\",\"order\":3,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_packagename_textfamily\":{\"value\":\"Georgia\", \"type\":\"Select\", \"options\": {\"label\":\"Font Family of Package Name\",\"order\":4,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_packagename_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Package Name\",\"order\":5,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } },\"template_bgcolor_normal\":{\"value\":\"ffffff\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_bgcolor_normal\",\"order\":11,\"label\":\"Background Color for Normal Packages\",\"description\":\"Select the background color for normal packages.\"}]],\"order\":11} },\"template_feature_textsize\":{\"value\":\"16\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Features\",\"order\":14,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"}} },\"template_feature_textfamily\":{\"value\":\"Times New Roman\", \"type\":\"Select\", \"options\": {\"label\":\"Font Family of Features\",\"order\":15,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"} } },\"template_feature_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Features\",\"order\":16,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"}} },\"template_price_textcolor_normal\": {\"value\":\"0dadf6\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_price_textcolor_normal\",\"order\":19,\"label\":\"Price Text Color ( Normal )\",\"description\":\"Select the text color for normal package price text.\"}]],\"order\":19 } },\"template_price_bgcolor_normal\": {\"value\":\"f2f0f0\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_price_bgcolor_normal\",\"order\":20,\"label\":\"Price Background Color ( Normal )\",\"description\":\"Select the text color for normal package price text.\"}]],\"order\":20 } },\"template_price_textsize\":{\"value\":\"24\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Price\",\"order\":21,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"}}}, \"template_price_textfamily\":{\"value\":\"Georgia\",\"type\":\"Select\", \"options\": {\"label\":\"Font Family of Price\",\"order\":22,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_price_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Price Text\",\"order\":23,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"}} },\"template_button_text\": {\"value\":\"Buy Package !!\", \"type\":\"Text\", \"options\": {\"label\":\"Text on the Button\" ,\"order\":24, \"description\":\"Enter the text that will be displayed on select package button.\"} },\"template_button_textsize\": {\"value\":\"16\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Button Text\",\"order\":25,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_button_textfamily\": {\"value\":\"Arial\", \"type\":\"Select\", \"options\": {\"label\":\"Font Family of Button Text\",\"order\":26,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_button_textstyle\":{\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Button Text\",\"order\":27,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } }}", 0, 1, 1),
            (1, "Template One", "{\"template_bgcolor\":{\"value\":\"FFEEC9\", \"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\", {\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_bgcolor\",\"order\":2,\"label\":\"Template Background Color\",\"description\":\"Select the background color for whole template.\"}]],\"order\":2} },\"template_packagename_price_bgcolor_normal\":{\"value\":\"03A9F4\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\", {\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_packagename_price_bgcolor_normal\",\"order\":3,\"label\":\"Package Name and Price Block Background Color (Normal)\",\"description\":\"Select the background color for package name and price block for normal package\"}]],\"order\":3} },\"template_packagename_price_textcolor\": {\"value\":\"fff\", \"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\", {\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_packagename_price_textcolor\",\"order\":5,\"label\":\"Package Name and Price Text Color\",\"description\":\"Select text color for package name and price block.\"}]],\"order\":5} },\"template_packagename_price_textsize\":{\"value\":\"24\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Package Name and Price\", \"order\":6,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\",\"16\":\"16\",\"17\":\"17\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_packagename_price_textfamily\":{\"value\":\"Georgia\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Package Name and Price\", \"order\":7,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_packagename_price_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Package Name and Price\", \"order\":8,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } },\"template_feature_textsize\":{\"value\":\"16\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Features\", \"order\":15,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\", \"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"}} },\"template_feature_textfamily\":{\"value\":\"Arial\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Features\", \"order\":16,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"} } },\"template_feature_textstyle\": {\"value\":\"normal\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Features\", \"order\":17,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"}} },\"template_button_text\": {\"value\":\"Buy Package\", \"type\":\"Text\", \"options\": {\"label\":\"Text on the Button\" , \"order\":18, \"description\":\"Enter the text that will be displayed on select package button (size in em).\"} }, \"template_button_textsize\": {\"value\":\"1.3\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Button Text\", \"order\":19,\"description\":\"\",\"multiOptions\":{\"1.0\":\"1.0\",\"1.1\":\"1.1\",\"1.2\":\"1.2\",\"1.3\":\"1.3\",\"1.4\":\"1.4\",\"1.5\":\"1.5\",\"1.6\":\"1.6\",\"1.7\":\"1.7\", \"1.8\":\"1.8\", \"1.9\":\"1.9\",\"2.0\":\"2.0\"} } },\"template_button_textfamily\": {\"value\":\"Arial\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Button Text\", \"order\":20,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_button_textstyle\":{\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Button Text\", \"order\":21,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } }}", 1, 0, 1),
            (2, "Template Two", "{\"template_bgcolor\":{\"value\":\"ffd1b2\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_bgcolor\",\"order\":2,\"label\":\"Template Background Color\",\"description\":\"Select the background color for whole template.\"}]],\"order\":2} },\"template_packagename_bgcolor_normal\":{\"value\":\"4DB1E2\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_packagename_bgcolor_normal\",\"order\":3,\"label\":\"Package Name Block Background Color (Normal)\",\"description\":\"Select the background color for package name block for normal package\"}]],\"order\":3} },\"template_packagename_textcolor\": {\"value\":\"fff\", \"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_packagename_textcolor\",\"order\":5,\"label\":\"Package Name Text Color\",\"description\":\"Select text color for package name block.\"}]],\"order\":5} },\"template_packagename_textsize\":{\"value\":\"20\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Package Name and Price\",\"order\":6,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_packagename_textfamily\":{\"value\":\"Georgia\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Package Name and Price\",\"order\":7,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_packagename_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Package Name and Price\",\"order\":11,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"}} },\"template_feature_textsize\":{\"value\":\"16\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Features\",\"order\":15,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"}} },\"template_feature_textfamily\":{\"value\":\"Arial\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Features\",\"order\":16,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"} } },\"template_feature_textstyle\": {\"value\":\"normal\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Features\",\"order\":17,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"}} },\"template_price_textsize\":{\"value\":\"32\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Price\",\"order\":18,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_price_textfamily\":{\"value\":\"Tahoma\",\"type\":\"Select\", \"options\": {\"label\":\"Font family of Price\",\"order\":19,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_price_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Price\",\"order\":20,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } },\"template_button_text\": {\"value\":\"Buy Package\", \"type\":\"Text\", \"options\": {\"label\":\"Text on the Button\" ,\"order\":21, \"description\":\"Enter the text that will be displayed on select package button.\"} },\"template_button_textsize\": {\"value\":\"16\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Button Text\",\"order\":22,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_button_textfamily\": {\"value\":\"Arial\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Button Text\",\"order\":23,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_button_textstyle\":{\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Button Text\",\"order\":24,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } }}", 2, 0, 1),
            (3, "Template Three", "{\"template_bgcolor\":{\"value\":\"cgfgfd\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_bgcolor\",\"order\":2,\"label\":\"Template Background Color\",\"description\":\"Select the background color for whole template.\"}]],\"order\":2} },\"template_packagename_bgcolor_normal\":{\"value\":\"00A8AA\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_packagename_bgcolor_normal\",\"order\":3,\"label\":\"Package Name Block Background Color (Normal)\",\"description\":\"Select the background color for package name block for normal package\"}]],\"order\":3} },\"template_packagename_textsize\":{\"value\":\"20\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Package Name\",\"order\":6,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_packagename_textfamily\":{\"value\":\"Georgia\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Package Name\",\"order\":7,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_packagename_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Package Name\",\"order\":8,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } },\"template_feature_textsize\":{\"value\":\"16\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Features\",\"order\":15,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"}} },\"template_feature_textfamily\":{\"value\":\"Arial\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Features\",\"order\":16,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"} } },\"template_feature_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Features\",\"order\":17,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"}} },\"template_price_textcolor\": {\"value\":\"aaaaaa\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_price_textcolor\",\"order\":18,\"label\":\"Price Text Color\",\"description\":\"Select the text color for the price text.\"}]],\"order\":18 } },\"template_price_textsize\":{\"value\":\"24\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Price\",\"order\":19,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_price_textfamily\":{\"value\":\"Tahoma\",\"type\":\"Select\", \"options\": {\"label\":\"Font family of Price\",\"order\":20,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_price_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Price\",\"order\":21,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } },\"template_button_text\": {\"value\":\"Buy Package\", \"type\":\"Text\", \"options\": {\"label\":\"Text on the Button\" ,\"order\":22, \"description\":\"Enter the text that will be displayed on select package button.\"} },\"template_button_textsize\": {\"value\":\"18\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Button Text\",\"order\":23,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_button_textfamily\": {\"value\":\"Arial\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Button Text\",\"order\":24,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_button_textstyle\":{\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Button Text\",\"order\":25,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } }}", 3, 0, 1),
            (4, "Template Four", "{\"template_bgcolor\": {\"value\":\"8591b0\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_bgcolor\",\"order\":2,\"label\":\"Template Background Color\",\"description\":\"Select the background color for whole template.\"}]],\"order\":2} },\"template_packagename_bgcolor_normal\":{\"value\":\"008FD5\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_packagename_bgcolor_normal\",\"order\":3,\"label\":\"Package Name Block Background Color (Normal)\",\"description\":\"Select the background color for package name block for normal package\"}]],\"order\":3} },\"template_packagename_textcolor\": {\"value\":\"fff\", \"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_packagename_textcolor\",\"order\":5,\"label\":\"Package Name Text Color\",\"description\":\"Select text color for package name block.\"}]],\"order\":5} },\"template_packagename_textsize\":{\"value\":\"20\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Package Name\",\"order\":6,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_packagename_textfamily\":{\"value\":\"Georgia\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Package Name\",\"order\":7,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_packagename_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Package Name and Price\",\"order\":8,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } },\"template_feature_textsize\":{\"value\":\"18\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Features\",\"order\":15,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"}} },\"template_feature_textfamily\":{\"value\":\"Arial\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Features\",\"order\":16,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"} } },\"template_feature_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Features\",\"order\":17,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"}} },\"template_price_textcolor\": {\"value\":\"aaaaaa\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_price_textcolor\",\"order\":18,\"label\":\"Price Text Color\",\"description\":\"Select the text color for the price text.\"}]],\"order\":18 } },\"template_price_textsize\":{\"value\":\"20\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Price\",\"order\":19,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_price_textfamily\":{\"value\":\"Georgia\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Price\",\"order\":20,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_price_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Price\",\"order\":21,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } },\"template_button_text\": {\"value\":\"Buy Package\", \"type\":\"Text\", \"options\": {\"label\":\"Text on the Button\" ,\"order\":22, \"description\":\"Enter the text that will be displayed on select package button.\"} },\"template_button_textsize\": {\"value\":\"18\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Button Text\",\"order\":23,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_button_textfamily\": {\"value\":\"Arial\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Button Text\",\"order\":24,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_button_textstyle\":{\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Button Text\",\"order\":25,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } }}", 4, 0, 1),
            (5, "Template Five", "{\"template_bgcolor\": {\"value\":\"65cfff\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_bgcolor\",\"order\":2,\"label\":\"Template Background Color\",\"description\":\"Select the background color for whole template.\"}]],\"order\":2} },\"template_featurelabel_bgcolor\":{\"value\":\"eee\", \"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_featurelabel_bgcolor\",\"order\":3,\"label\":\"Feature Label Block Background Color\",\"description\":\"Select the background color for features block.\"}]],\"order\":3} },\"template_featurelabel_textcolor\":{\"value\":\"7c7c7c\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_featurelabel_textcolor\",\"order\":4,\"label\":\"Feature Label Name Color\",\"description\":\"Select the color for feature name.\"}]],\"order\":4} },\"template_featurelabel_textsize\": {\"value\":\"16\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Feature Label\",\"order\":5,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"}} },\"template_featurelabel_textfamily\": {\"value\":\"Georgia\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Feature Label\",\"order\":6,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"} } },\"template_featurelabel_textstyle\":{\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Feature Label\",\"order\":7,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"}} },\"template_packagename_bgcolor_normal\":{\"value\":\"00a1af\",\"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_packagename_bgcolor_normal\",\"order\":8,\"label\":\"Package Name Block Background Color (Normal)\",\"description\":\"Select the background color for package name block for normal package\"}]],\"order\":8} },\"template_packagename_textcolor\": {\"value\":\"fff\", \"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_packagename_textcolor\",\"order\":10,\"label\":\"Package Name Text Color\",\"description\":\"Select text color for package name block.\"}]],\"order\":10 } },\"template_packagename_textsize\":{\"value\":\"20\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Package Name\",\"order\":11,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"}} },\"template_packagename_textfamily\":{\"value\":\"Georgia\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Package Name\",\"order\":12,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"} } },\"template_packagename_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Package Name\",\"order\":13,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"}} },\"template_price_textcolor\": {\"value\":\"fff\", \"type\":\"Text\", \"options\": {\"decorators\":[[\"ViewScript\",{\"viewScript\":\"_colorpicker.tpl\",\"class\":\"form element colorPickerElement\",\"element_name\":\"template_price_textcolor\",\"order\":20,\"label\":\"Price Text Color\",\"description\":\"Select the text color for the price text.\"}]],\"order\":20 } },\"template_price_textsize\":{\"value\":\"26\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Price\",\"order\":21,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_price_textfamily\":{\"value\":\"Georgia\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Price\",\"order\":22,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_price_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Price\",\"order\":23,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } },\"template_feature_textsize\":{\"value\":\"16\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Features\",\"order\":24,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"}} },\"template_feature_textfamily\":{\"value\":\"Arial\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Features\",\"order\":25,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"} } },\"template_feature_textstyle\": {\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Features\",\"order\":26,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"}} },\"tick_image\":{\"value\":\"application\\/modules\\/Sitepage\\/externals\\/images\\/tick_image.png\", \"type\":\"File\",\"options\":{\"label\":\"Tick Image\",\"order\":27,\"description\":\"Upload image.\",\"value\":0,\"validators\":[[\"Extension\",false,\"jpg,png,gif,jpeg,JPG,PNG,GIF,JPEG\"]]}},\"cross_image\":{\"value\":\"application\\/modules\\/Sitepage\\/externals\\/images\\/cross_image.png\",\"type\":\"File\",\"options\":{\"label\":\"Cross Image\",\"order\":28,\"description\":\"Upload image.\",\"value\":0,\"validators\":[[\"Extension\",false,\"jpg,png,gif,jpeg,JPG,PNG,GIF,JPEG\"]] }},\"template_button_text\":{\"value\":\"Buy Packages\",\"type\":\"Text\", \"options\": {\"label\":\"Text on the Button\" ,\"order\":29, \"description\":\"Enter the text that will be displayed on select package button.\"} },\"template_button_textsize\": {\"value\":\"18\",\"type\":\"Select\", \"options\": {\"label\":\"Font Size of Button Text\",\"order\":30,\"description\":\"\",\"multiOptions\":{\"8\":\"8\",\"9\":\"9\",\"10\":\"10\",\"11\":\"11\",\"12\":\"12\",\"13\":\"13\",\"14\":\"14\",\"15\":\"15\", \"16\":\"16\",\"17\":\"17\",\"18\":\"18\",\"19\":\"19\",\"20\":\"20\",\"21\":\"21\",\"22\":\"22\",\"23\":\"23\",\"24\":\"24\",\"25\":\"25\",\"26\":\"26\",\"27\":\"27\",\"28\":\"28\",\"29\":\"29\",\"30\":\"30\",\"31\":\"31\",\"32\":\"32\",\"33\":\"33\",\"34\":\"34\",\"35\":\"35\"} } },\"template_button_textfamily\": {\"value\":\"Arial\", \"type\":\"Select\", \"options\": {\"label\":\"Font family of Button Text\",\"order\":31,\"description\":\"\",\"multiOptions\":{\"Andale Mono\":\"Andale Mono\",\"Arial\":\"Arial\",\"Arial Black\":\"Arial Black\",\"Book Antiqua\":\"Book Antiqua\",\"Comic Scan MS\":\"Comic Scan MS\",\"Courier New\":\"Courier New\",\"Georgia\":\"Georgia\",\"Helvetica\":\"Helvetica\",\"Impact\":\"Impact\",\"Symbol\":\"Symbol\",\"Tahoma\":\"Tahoma\",\"Terminal\":\"Terminal\",\"Times New Roman\":\"Times New Roman\",\"Trebuchet MS\":\"Trebuchet MS\",\"Verdana\":\"Verdana\",\"Webdings\":\"Webdings\",\"Wingdings\":\"Wingdings\",\"Century Gothic\":\"Century Gothic\"}} },\"template_button_textstyle\":{\"value\":\"bold\",\"type\":\"Select\", \"options\": {\"label\":\"Font Style of Button Text\",\"order\":32,\"description\":\"\",\"multiOptions\":{\"bold\":\"Bold\",\"italic\":\"Italic\",\"normal\":\"Normal\"} } }}", 5, 0, 1);
            ');
            $db->query('INSERT IGNORE `engine4_sitepage_layouts` (`layout_id`, `layout_name`, `structure_type`, `enabled`, `default`) VALUES
            (0, "Layout 0", "type2", 1, 1),
            (1, "Layout 1", "type2", 1, 1),
            (2, "Layout 2", "type2", 1, 1),
            (3, "Layout 3", "type2", 1, 1),
            (4, "Layout 4", "type2", 1, 1),
            (5, "Layout 5", "type1", 1, 1);');


        }

}
?>