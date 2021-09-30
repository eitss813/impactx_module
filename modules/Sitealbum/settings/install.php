<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Installer extends Engine_Package_Installer_Module {

    function onPreinstall() {
        $db = $this->getDb();

        //CHECK THAT ALBUM PLUGIN IS INSTALLED OR NOT
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'album')
                ->where('enabled = ?', 1);
        $check_album = $select->query()->fetchObject();
        if (empty($check_album)) {
            $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
            return $this->_error("<span style='color:red'>Note: You have not installed the <a href='http://www.socialengine.com/features/php/plugins/albums' target='_blank'>SocialEngine Core - Photo Albums Plugin</a> on your site yet. Please install it first before installing the 'SocialEngineAddOns - Advanced Photo Albums'.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
        }

        $select = new Zend_Db_Select($db);
        $PRODUCT_TYPE = 'sitealbum';
        $PLUGIN_TITLE = 'Sitealbum';
        $PLUGIN_VERSION = '5.0.0p1';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'Advanced Photo Albums Plugin';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.10.3p8';
        $PRODUCT_TITLE = 'Advanced Photo Albums Plugin';
        $getErrorMsg = $this->getVersion();
        if (!empty($getErrorMsg)) {
            return $this->_error($getErrorMsg);
        }
        $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
        $is_file = file_exists($file_path);
        if (empty($is_file)) {
            include_once APPLICATION_PATH . "/application/modules/Sitealbum/controllers/license/license3.php";
        } else {
            include_once $file_path;
        }
        parent::onPreinstall();
    }

    function onInstall() {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitealbum');
        $is_sitealbum_object = $select->query()->fetchObject();

        if (!empty($is_sitealbum_object)) {
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)
    VALUES 
    ("sitealbum_admin_main_htmlblock", "sitealbum", "HTML Block", "", \'{"route":"admin_default","module":"sitealbum","controller":"html-block"}\', "sitealbum_admin_main", "", 1, 0, 10)
    ');
            
            $featured = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'featured\'')->fetch();
            if (empty($featured)) {
                $db->query('ALTER TABLE `engine4_album_albums` ADD `featured` TINYINT(1) DEFAULT NULL;');
            }

            $featuredPhoto = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'featured\'')->fetch();
            if (empty($featuredPhoto)) {
                $db->query('ALTER TABLE `engine4_album_photos` ADD `featured` TINYINT(1) DEFAULT NULL;');
            }
        }
        
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("album_single_photo_upload", "sitealbum", \'{item:$subject} added a new photo to the album {item:$object}:\', "1", "5", "1", "4", "1", "1");');

        $db->query('UPDATE `engine4_core_menuitems` SET `params` = \'{"route":"album_general","action":"upload","class":"seao_smoothbox","data_SmoothboxSEAOClass":"seao_add_photo_lightbox"}\' WHERE `engine4_core_menuitems`.`name` = "album_main_upload" LIMIT 1;');

        $db->query('UPDATE `engine4_core_menuitems` SET `params` = \'{"route":"album_general","action":"upload","class":"icon_photos_new seao_smoothbox","data_SmoothboxSEAOClass":"seao_add_photo_lightbox"}\' WHERE `engine4_core_menuitems`.`name` = "album_quick_upload" LIMIT 1;');

        $db->query('UPDATE `engine4_core_menuitems` SET `params` = \'{"route":"sitealbum_general","action":"upload","class":"seao_smoothbox","data_SmoothboxSEAOClass":"seao_add_photo_lightbox"}\' WHERE `engine4_core_menuitems`.`name` = "sitealbum_main_upload" LIMIT 1;');

        $db->query('UPDATE `engine4_core_menuitems` SET `params` = \'{"route":"sitealbum_general","action":"upload","class":"icon_photos_new seao_smoothbox","data_SmoothboxSEAOClass":"seao_add_photo_lightbox"}\' WHERE `engine4_core_menuitems`.`name` = "sitealbum_quick_upload" LIMIT 1;');

        $password = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'password\'')->fetch();
        if (empty($password)) {
            $db->query('ALTER TABLE `engine4_album_albums` ADD `password` char(32) COLLATE utf8_unicode_ci DEFAULT NULL;');
        }

        $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` SELECT level_id as `level_id`,  'album' as `type`, 'album_password_protected' as `name`, 0 as `value`, NULL as `params` FROM `engine4_authorization_levels` WHERE `type` IN('user');");

        $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` SELECT level_id as `level_id`, 'album' as `type`, 'album_password_protected' as `name`, 0 as `value`, NULL as `params` FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')
                ->where('name = ?', 'sitetagcheckin');
        $is_enabled = $select->query()->fetchObject();
        if (!empty($is_enabled)) {
            $table_exist = $db->query('SHOW TABLES LIKE \'engine4_sitetagcheckin_addlocations\'')->fetch();
            if (!empty($table_exist)) {
                $sync_album = $db->query('SHOW COLUMNS FROM engine4_sitetagcheckin_addlocations LIKE \'sync_album\'')->fetch();
                if (empty($sync_album)) {
                    $db->query('ALTER TABLE `engine4_sitetagcheckin_addlocations` ADD `sync_album` tinyint(4) NOT NULL DEFAULT "0";');
                }
            }
        }

        //ALTER PHOTO ENGINE4_ALBUM_PHOTOS TABLE
        $table_exist = $db->query('SHOW TABLES LIKE \'engine4_album_photos\'')->fetch();
        if (!empty($table_exist)) {

            $photo_hide = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'photo_hide\'')->fetch();
            if (empty($photo_hide)) {
                $db->query('ALTER TABLE `engine4_album_photos` ADD `photo_hide` BOOL NOT NULL DEFAULT "0";');
            }

            $rating = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'rating\'')->fetch();
            if (empty($rating)) {
                $db->query('ALTER TABLE `engine4_album_photos` ADD `rating` float NOT NULL');
            }

            $seao_locationid = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'seao_locationid\'')->fetch();
            if (empty($seao_locationid)) {
                $db->query("ALTER TABLE engine4_album_photos ADD `seao_locationid` INT( 11 ) NOT NULL");
                $db->query("ALTER TABLE  `engine4_album_photos` ADD INDEX (`seao_locationid`);");
            }

            $location = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'location\'')->fetch();
            if (empty($location)) {
                $db->query("ALTER TABLE engine4_album_photos ADD `location` VARCHAR( 264 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
            }
        }

        //ALTER  ENGINE4_ALBUM_ALBUMS TABLE
        $table_exist = $db->query('SHOW TABLES LIKE \'engine4_album_albums\'')->fetch();
        if (!empty($table_exist)) {

            $networks_privacy = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'networks_privacy\'')->fetch();
            if (empty($networks_privacy)) {
                $db->query("ALTER TABLE `engine4_album_albums` ADD `networks_privacy` MEDIUMTEXT NULL;");
            }


            $subcategory_id = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'subcategory_id\'')->fetch();
            if (empty($subcategory_id)) {
                $db->query("ALTER TABLE engine4_album_albums ADD `subcategory_id` int(11) unsigned NOT NULL default '0'");
            }

            $photos_count = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'photos_count\'')->fetch();
            if (empty($photos_count)) {
                $db->query('ALTER TABLE `engine4_album_albums` ADD `photos_count` SMALLINT( 3 ) NOT NULL');
            }

            $rating = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'rating\'')->fetch();
            if (empty($rating)) {
                $db->query("ALTER TABLE engine4_album_albums ADD `rating` float NOT NULL");
            }

            $profile_type = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'profile_type\'')->fetch();
            if (empty($profile_type)) {
                $db->query("ALTER TABLE engine4_album_albums ADD `profile_type`  int(11) NOT NULL DEFAULT '0'");
                $db->query("ALTER TABLE  `engine4_album_albums` ADD INDEX (`profile_type`);");
            }

            $seao_locationid = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'seao_locationid\'')->fetch();
            if (empty($seao_locationid)) {
                $db->query("ALTER TABLE engine4_album_albums ADD `seao_locationid` INT( 11 ) NOT NULL");
                $db->query("ALTER TABLE  `engine4_album_albums` ADD INDEX (`seao_locationid`);");
            }

            $location = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'location\'')->fetch();
            if (empty($location)) {
                $db->query("ALTER TABLE engine4_album_albums ADD `location` VARCHAR( 264 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
            }
        }

        $table_exist = $db->query('SHOW TABLES LIKE \'engine4_album_categories\'')->fetch();
        if (!empty($table_exist)) {

            $db->query("DELETE FROM `engine4_album_categories` WHERE `engine4_album_categories`.`category_id` = 0 LIMIT 1");

            $category_id = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'category_id\'')->fetch();

            if (!empty($category_id)) {
                $max_category_object = $db->query("SELECT MAX(category_id) as max_category_id FROM engine4_album_categories;")->fetchObject();
                if (!empty($max_category_object->max_category_id)) {
                    $db->query('ALTER TABLE `engine4_album_categories` AUTO_INCREMENT = ' . $max_category_object->max_category_id);
                }
            }

            $category_slug = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'category_slug\'')->fetch();
            if (empty($category_slug)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `category_slug` VARCHAR( 128 ) NOT NULL;');
            }

            $cat_dependency = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'cat_dependency\'')->fetch();
            if (empty($cat_dependency)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `cat_dependency` int(11) NOT NULL DEFAULT "0";');
            }

            $photo_id = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'photo_id\'')->fetch();
            if (empty($photo_id)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `photo_id` int(11) NOT NULL DEFAULT "0";');
            }

            $file_id = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'file_id\'')->fetch();
            if (empty($file_id)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `file_id` int(11) NOT NULL DEFAULT "0";');
            }

            $banner_id = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'banner_id\'')->fetch();
            if (empty($banner_id)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `banner_id` int(11) NOT NULL DEFAULT "0";');
            }

            $banner_title = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'banner_title\'')->fetch();
            if (empty($banner_title)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `banner_title` varchar(64) DEFAULT NULL;');
            }

            $banner_url = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'banner_url\'')->fetch();
            if (empty($banner_url)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `banner_url` varchar(255) DEFAULT NULL;');
            }

            $banner_url_window = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'banner_url_window\'')->fetch();
            if (empty($banner_url_window)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `banner_url_window` tinyint(1) NOT NULL DEFAULT "0";');
            }

            $cat_order = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'cat_order\'')->fetch();
            if (empty($cat_order)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `cat_order` smallint(3) NOT NULL DEFAULT "0";');
            }

            $sponsored = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'sponsored\'')->fetch();
            if (empty($sponsored)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `sponsored` tinyint(1) NOT NULL DEFAULT "0";');
            }

            $profile_type = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'profile_type\'')->fetch();
            if (empty($profile_type)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `profile_type` int(11) NOT NULL DEFAULT "0";');
            }

            $meta_title = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'meta_title\'')->fetch();
            if (empty($meta_title)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `meta_title` text NOT NULL ;');
            }

            $meta_description = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'meta_description\'')->fetch();
            if (empty($meta_description)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `meta_description` text NOT NULL ;');
            }

            $meta_keywords = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'meta_keywords\'')->fetch();
            if (empty($meta_keywords)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `meta_keywords` text NOT NULL ;');
            }

            $top_content = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'top_content\'')->fetch();
            if (empty($top_content)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `top_content` text NOT NULL ;');
            }

            $bottom_content = $db->query('SHOW COLUMNS FROM engine4_album_categories LIKE \'bottom_content\'')->fetch();
            if (empty($bottom_content)) {
                $db->query('ALTER TABLE `engine4_album_categories` ADD `bottom_content` text NOT NULL ;');
            }

            $cat_dependency = $db->query("SHOW INDEX FROM `engine4_album_categories` WHERE Key_name = 'cat_dependency'")->fetch();
            if (empty($cat_dependency)) {
                $db->query("ALTER TABLE  `engine4_album_categories` ADD INDEX (`cat_dependency`);");
            }
        }

        $table_exist = $db->query('SHOW TABLES LIKE \'engine4_album_categories\'')->fetch();
        if (empty($table_exist)) {
            $db->query("DROP TABLE IF EXISTS `engine4_album_categories`;");
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_album_categories` (
			`category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`category_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
			`category_slug` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
			`cat_dependency` int(11) NOT NULL DEFAULT '0',
			`photo_id` int(11) NOT NULL DEFAULT '0',
			`file_id` int(11) NOT NULL DEFAULT '0',
			`banner_id` int(11) NOT NULL DEFAULT '0',
			`banner_title` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
			`banner_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			`banner_url_window` tinyint(1) NOT NULL DEFAULT '0',
			`cat_order` smallint(3) NOT NULL DEFAULT '0',
			`sponsored` tinyint(1) NOT NULL DEFAULT '0',
			`profile_type` int(11) NOT NULL DEFAULT '0',
			`meta_title` text COLLATE utf8_unicode_ci,
			`meta_description` text COLLATE utf8_unicode_ci,
			`meta_keywords` text COLLATE utf8_unicode_ci,
			`top_content` text COLLATE utf8_unicode_ci NOT NULL,
			`bottom_content` text COLLATE utf8_unicode_ci NOT NULL,
			PRIMARY KEY (`category_id`),
			KEY `cat_dependency` (`cat_dependency`)
			) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE utf8_unicode_ci;");

            $db->query("INSERT IGNORE INTO `engine4_album_categories` (`category_name`, `category_slug`, `cat_dependency`, `photo_id`, `file_id`, `banner_id`, `banner_title`, `banner_url`, `banner_url_window`, `cat_order`, `sponsored`, `profile_type`,`meta_title`, `meta_description`, `meta_keywords`, `top_content`, `bottom_content`) VALUES
			('Arts & Culture', '', 0, 0, 0, 0, NULL, NULL, 0, 1, 0,0,  NULL, NULL, NULL, '', ''),
			('Business', '', 0, 0, 0, 0, NULL, NULL, 0, 2, 0,0, NULL, NULL, NULL, '', ''),
			('Entertainment', '', 0,  0, 0, 0, NULL, NULL, 0, 3, 0, 0,  NULL, NULL, NULL, '', ''),
			('Family & Home', '', 0,  0, 0, 0, NULL, NULL, 0, 4, 0, 0, NULL, NULL, NULL, '', ''),
			('Health', '', 0, 0, 0,  0, NULL, NULL, 0, 5, 0, 0,NULL, NULL, NULL, '', ''),
			('Recreation', '', 0,  0, 0, 0, NULL, NULL, 0, 6, 0, 0, NULL, NULL, NULL, '', ''),
			('Personal', '', 0, 0, 0, 0, NULL, NULL, 0, 7, 0, 0, NULL, NULL, NULL, '', ''),
			('Shopping', '', 0, 0,  0, 0, NULL, NULL, 0, 8, 0, 0, NULL, NULL, NULL, '', ''),
			('Society', '', 0,  0, 0, 0, NULL, NULL, 0, 9, 0, 0, NULL, NULL, NULL, '', ''),
			('Sports', '', 0, 0, 0, 0, NULL, NULL, 0, 10, 0, 0, NULL, NULL, NULL, '', ''),
			('Technology', '',  0, 0, 0, 0, NULL, NULL, 0, 11, 0, 0, NULL, NULL, NULL, '', ''),
			('Other', '', 0, 0, 0, 0, NULL, NULL, 0, 12, 0, 0, NULL, NULL, NULL, '', '');");
        }

        // UPDATE PHOTOS_COUNT
        $select = new Zend_Db_Select($db);
        $results = $select
                ->from('engine4_album_albums', array('album_id'))
                ->where('photos_count = ?', 0)
                ->order('creation_date DESC')
                ->limit(10000)
                ->query()
                ->fetchAll();
        if (!empty($results)) {
            foreach ($results as $result) {
                $select = new Zend_Db_Select($db);
                $photos_count = $select
                        ->from('engine4_album_photos', new Zend_Db_Expr('COUNT(photo_id)'))
                        ->where('album_id = ?', $result['album_id'])
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
                $db->query("UPDATE `engine4_album_albums` SET `photos_count` = '$photos_count' WHERE `engine4_album_albums`.`album_id` ='" . $result['album_id'] . "' LIMIT 1 ;");
            }
        }

        // UPDATE ENGINE4_SEAOCORES AND ENGINE4_ACTIVITY_ACTIONTYPES TABLES
        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitealbum';");


        //START MAKE WIDGETIZE PAGE FOR CREATE, EDIT, MANAGE AND SHARE.
        $this->createwidgetizePage('sitealbum_album_edit', 'Advanced Albums - Album Edit Page', 'Edit Album', 'This page is the album edit page.');
        $this->createwidgetizePage('sitealbum_index_upload', 'Advanced Albums - Album Create Page', 'Add New Photos', 'This page is the album create page.');
        $this->createwidgetizePage('sitealbum_album_editphotos', 'Advanced Albums - Manage Photos Page', 'Manage Photos', 'This page is the manage photos page.');
        $this->createwidgetizePage('sitealbum_badge_create', 'Advanced Albums - Album Share by Badge Page', 'Album Share by Badge', 'This page is the album share by badge page.');
        //END MAKE WIDGETIZE PAGE FOR CREATE, EDIT, MANAGE AND SHARE.
//    DELETE FROM `advancedalbum`.`engine4_core_content` WHERE `engine4_core_content`.`content_id` = 1058 LIMIT 1
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
                    ->where('name = ?', 'siteadvsearch_index_browse-album')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (!$page_id) {
                $db->insert('engine4_core_pages', array(
                    'name' => 'siteadvsearch_index_browse-album',
                    'displayname' => 'Advanced Search - SEAO - Advanced Albums',
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
                    'name' => 'sitealbum.search-sitealbum',
                    'parent_content_id' => $right_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"viewType":"vertical","showAllCategories":"1","whatWhereWithinmile":"0","advancedSearch":"0","locationDetection":"0","nomobile":"0","name":"sitealbum.search-sitealbum"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitealbum.browse-menu-quick',
                    'parent_content_id' => $right_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true}',
                ));


                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitealbum.browse-albums-sitealbum',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","margin_photo":"3","photoHeight":"199","photoWidth":"205","columnHeight":"251","albumInfo":["albumTitle","totalPhotos"],"customParams":"1","orderby":"featured","show_content":"3","truncationLocation":"50","albumTitleTruncation":"50","limit":"12","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.browse-albums-sitealbum"}',
                ));
            } else {

                $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = '$page_id'");
                $db->query("UPDATE `engine4_core_pages` SET `displayname` = 'Advanced Search - SEAO - Advanced Albums' WHERE `engine4_core_pages`.`name` ='siteadvsearch_index_browse-album';");

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
                    'name' => 'sitealbum.search-sitealbum',
                    'parent_content_id' => $right_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"viewType":"vertical","showAllCategories":"1","whatWhereWithinmile":"0","advancedSearch":"0","locationDetection":"0","nomobile":"0","name":"sitealbum.search-sitealbum"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitealbum.browse-menu-quick',
                    'parent_content_id' => $right_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitealbum.browse-albums-sitealbum',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","margin_photo":"3","photoHeight":"199","photoWidth":"205","columnHeight":"251","albumInfo":["albumTitle","totalPhotos"],"customParams":"1","orderby":"featured","show_content":"3","truncationLocation":"50","albumTitleTruncation":"50","limit":"12","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.browse-albums-sitealbum"}',
                ));
            }
            $db->query("UPDATE `engine4_siteadvsearch_contents` SET `module_name` = 'sitealbum' WHERE `engine4_siteadvsearch_contents`.`resource_type` ='album' LIMIT 1 ;");
        }
        $this->insertPhotoBrowsePage();
        $dateTakenColumnExists = $db->query('SHOW COLUMNS FROM engine4_album_photos where field=\'date_taken\'')->fetch();
        if (empty($dateTakenColumnExists)) {
            $db->query('ALTER TABLE  `engine4_album_photos` ADD  `date_taken` DATETIME NOT NULL DEFAULT 0;');
        }
        //Default Album 
        $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name` ,`value`) VALUES ('sitealbum.photo.specialalbum', '0');");
        $this->setActivityFeeds();
        parent::onInstall();
    }

    public function insertPhotoBrowsePage() {
        $db = $this->getDb();
        // PHOTO BROWSE PAGE
        $page_id = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitealbum_photo_browse')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!$page_id) {
            // Insert page
            $db->insert('engine4_core_pages', array(
                'name' => 'sitealbum_photo_browse',
                'displayname' => 'Advanced Albums - Photos Browse Page',
                'title' => '',
                'description' => 'This page lists photos entries.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

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
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.navigation',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));


            // Insert search
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.search-sitephoto',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":true,"viewType":"horizontal","showAllCategories":"1","locationDetection":"0","whatWhereWithinmile":"0","advancedSearch":"0","nomobile":"0","name":"sitealbum.search-sitealbum"}',
            ));
            // Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'sitealbum.browse-photos-sitealbum',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 5,
                'params' => '{"title":"","itemCountPerPage":"24","category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","showPhotosInJustifiedView":"1","rowHeight":"375","maxRowHeight":"0","margin":"10","lastRow":"nojustify","photoHeight":"375","photoWidth":"208","orderby":"featuredTakenBy","show_content":"3","photoInfo":["ownerName","creationDate","viewCount","likeCount","commentCount","ratingStar","photoTitle","albumTitle"],"truncationLocation":"35","photoTitleTruncation":"100","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.browse-albums-sitealbum"}',
            ));
        }
        //END PHOTO BROWSE PAGE

        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitealbum_photo_browse", "sitealbum", "Browse Photos", "", \'{"route":"sitealbum_extended","action":"browse"}\', "sitealbum_main", "", 2)');
        $db->query("UPDATE `engine4_core_menuitems` SET `plugin` = 'Sitealbum_Plugin_Menus::canViewAlbums' WHERE `engine4_core_menuitems`.`name` = 'sitealbum_photo_browse';");
    }

    public function updateWidgteName($oldwidgteName, $newWidgetName, $params) {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_content')->where('name = ?', $oldwidgteName)->limit(1);
        $results = $select->query()->fetchAll();
        if (!empty($results)) {
            $db->query('UPDATE  `engine4_core_content` SET  `name` =  \'' . $newWidgetName . '\',
				`params` = \'' . $params . '\'  WHERE  `engine4_core_content`.`name` =\'' . $oldwidgteName . '\';');
        }
    }

    public function createwidgetizePage($pageName, $pageDisplayName, $title, $description) {

        $db = $this->getDb();

        //START ALBUM EDIT PAGE
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
                'displayname' => $pageDisplayName, //'Advanced Albums - Album Edit Page',
                'title' => $title, //'Edit Album',
                'description' => $description, //'This page is the album edit page.',
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
            if ($pageName != 'sitealbum_index_upload') {
                $db->insert('engine4_core_content', array(
                    'type' => 'widget',
                    'name' => 'sitealbum.navigation',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_middle_id,
                    'order' => 1,
                ));
            }

            // Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
        //END ALBUM EDIT PAGE

        if ($pageName == 'sitealbum_index_upload') {
            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'sitealbum.navigation' AND `engine4_core_content`.`page_id` = '$page_id' LIMIT 1");
        }
    }

    private function getVersion() {
        $db = $this->getDb();

        $errorMsg = '';
        $finalModules = $getResultArray = array();
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'seaocore' => '4.8.5p4',
            'sitemobile' => '4.8.6p4'
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
            $errorMsg .= '<div class="tip"><span>Note: Your website does not have the latest version of "' . $modArray['title'] . '". Please upgrade "' . $modArray['title'] . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Advanced Photo Albums Plugin".<br/> Please <a href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
        }

        return $errorMsg;
    }

    private function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }

    public function onPostInstall() {

        //SITEMOBILE CODE TO CALL MY.SQL ON POST INSTALL
        $moduleName = 'sitealbum';
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if (!empty($is_sitemobile_object)) {
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('$moduleName','1')");
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_sitemobile_modules')
                    ->where('name = ?', $moduleName)
                    ->where('integrated = ?', 0);
            $is_sitemobile_object = $select->query()->fetchObject();
            if ($is_sitemobile_object) {
                $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
                $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
                if ($controllerName == 'manage' && $actionName == 'install') {
                    $view = new Zend_View();
                    $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/' . $moduleName . '/integrated/0/redirect/install');
                }
            }
        }
        //END - SITEMOBILE CODE TO CALL MY.SQL ON POST INSTALL
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
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_album", "sitealbum", \'{item:$subject} replied to a comment on {item:$owner}\'\'s album {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_album_photo", "sitealbum", \'{item:$subject} replied to a comment on {item:$owner}\'\'s album photo {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        }
    }

}

?>
