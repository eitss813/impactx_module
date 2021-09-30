<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Installer extends Engine_Package_Installer_Module {

    function onPreinstall() {
        $db = $this->getDb();

        $getErrorMsg = $this->_getVersion();
        if (!empty($getErrorMsg)) {
            return $this->_error($getErrorMsg);
        }

        $PRODUCT_TYPE = 'sitemember';
        $PLUGIN_TITLE = 'Sitemember';
        $PLUGIN_VERSION = '5.0.1';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'Our Advanced Members Plugin showcase the members in a super attractive manner as they are the one who contributes in building up a social community.<br /> Better browsing and searching options with an awesome brand new look of members enhances SocialEngine’s core members plugin in all ways. You just need to choose layout amongst the 4 views: List, Grid, Pinboard and Map and you can have your personal Browse Members Page with every possible configurable options.<br /> Reviews and ratings to your friends can highlight site members as Top Rated, Most Recommended Members of the site and the members giving reviews can be listed under ‘Top Reviewers’ of site.';
        $PRODUCT_TITLE = 'Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.9.2p2';
        $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
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

        //START THE WORK FOR MAKE WIDGETIZE PAGE OF USERLOCATION OR MAP.
        $db = $this->getDb();

        // for complimentcategory_id entry in sea
        $db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES ('sitemember', 'complimentcategory_id', '1', '17234', 'Compliment');");

        $db->query("UPDATE  `engine4_core_content` SET  `name` =  'sitemember.profile-info' WHERE  `engine4_core_content`.`name` = 'user.profile-info' LIMIT 1 ;");

        $db->query("UPDATE  `engine4_core_menuitems` SET  `label` =  'Popular Members' WHERE  `engine4_core_menuitems`.`name` = 'sitemember_review_top-rated' LIMIT 1 ;");

        //CHANGE IN CORE COMMENT TABLE
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_core_comments'")->fetch();
        if (!empty($table_exist)) {
            $column_exist = $db->query("SHOW COLUMNS FROM `engine4_core_comments` LIKE 'parent_comment_id'")->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE  `engine4_core_comments` ADD  `parent_comment_id` INT( 11 ) NOT NULL DEFAULT  '0';");
            }
        }

        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitemember';");

        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitemember_profilemaps` (
			`profilemap_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`option_id` int(11) NOT NULL,
			`profile_type` int(11) NOT NULL,
			PRIMARY KEY (`profilemap_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");

        // ADD COLUMN SEAO_LOCATIONID , LOCATION , FEATURED AND SPONSORED IN USER TABLE
        $table_exist = $db->query('SHOW TABLES LIKE \'engine4_users\'')->fetch();
        if (!empty($table_exist)) {
            $column_exist = $db->query('SHOW COLUMNS FROM engine4_users LIKE \'seao_locationid\'')->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE engine4_users ADD `seao_locationid` INT( 11 ) NOT NULL");
            }

            $column_exist = $db->query('SHOW COLUMNS FROM engine4_users LIKE \'location\'')->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE engine4_users ADD `location` VARCHAR( 264 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
            }
        }

        $showLocationIdIndex = $db->query("SHOW INDEX FROM `engine4_users` WHERE Key_name = 'seao_locationid'")->fetch();
        if (empty($showLocationIdIndex)) {
            $db->query('ALTER TABLE `engine4_users` ADD INDEX ( `seao_locationid` )')->fetch();
        }

        $userinfotable_exist = $db->query('SHOW TABLES LIKE \'engine4_seaocore_userinfo\'')->fetch();
        if (!empty($userinfotable_exist)) {
            $column_exist = $db->query('SHOW COLUMNS FROM engine4_seaocore_userinfo LIKE \'featured\'')->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_seaocore_userinfo`  ADD `featured` TINYINT UNSIGNED NOT NULL DEFAULT '0';");
            }

            $column_exist = $db->query('SHOW COLUMNS FROM engine4_seaocore_userinfo LIKE \'sponsored\'')->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_seaocore_userinfo`  ADD `sponsored` TINYINT UNSIGNED NOT NULL DEFAULT '0';");
            }
        }


        if (!empty($userinfotable_exist)) {
            $featuredColumnIndex = $db->query("SHOW INDEX FROM `engine4_seaocore_userinfo` WHERE Key_name = 'featured'")->fetch();
            if (empty($featuredColumnIndex)) {
                $db->query("ALTER TABLE `engine4_seaocore_userinfo` ADD INDEX ( `featured` );");
            }

            $sponsoredColumnIndex = $db->query("SHOW INDEX FROM `engine4_seaocore_userinfo` WHERE Key_name = 'sponsored'")->fetch();
            if (empty($sponsoredColumnIndex)) {
                $db->query("ALTER TABLE `engine4_seaocore_userinfo` ADD INDEX ( `sponsored` );");
            }
        }

        // ADD COLUMN MEMBER IN USER TABLE META TABLE
        $field_meta_table_exist = $db->query('SHOW TABLES LIKE \'engine4_user_fields_meta\'')->fetch();
        if (!empty($field_meta_table_exist)) {
            $column_exist = $db->query('SHOW COLUMNS FROM engine4_user_fields_meta LIKE \'member\'')->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE `engine4_user_fields_meta`  ADD `member` TINYINT UNSIGNED NOT NULL DEFAULT '0';");
            }
        }



        // DATA TRANSFER FROM  SITETAGCHECKIN_PROFILEMAPS TO SITEMEMBER_PROFILEMAPS
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitetagcheckin')
                ->where('enabled = ?', 1);
        $check_sitetagcheckin = $select->query()->fetchObject();

        if (!empty($check_sitetagcheckin)) {
            $sitetagcheckin_profilemap_table_exist = $db->query('SHOW TABLES LIKE \'engine4_sitetagcheckin_profilemaps\'')->fetch();
            $sitemember_profilemap_table_exist = $db->query('SHOW TABLES LIKE \'engine4_sitemember_profilemaps\'')->fetch();

            if ($sitetagcheckin_profilemap_table_exist && $sitemember_profilemap_table_exist) {
                $result = $db->query("SELECT * FROM  `engine4_sitetagcheckin_profilemaps`")->fetchAll();
                if (!empty($result)) {
                    foreach ($result as $results) {
                        $db->query("INSERT IGNORE INTO `engine4_sitemember_profilemaps` (`option_id`, `profile_type`) VALUES ('" . $results['option_id'] . "', '" . $results['profile_type'] . "')");
                    }
                }
            }
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_settings', 'value')
                ->where('name = ?', 'sitemember.isActivate');
        $sitemember_isActivate_value = $select->query()->fetchColumn();

        if (!$sitemember_isActivate_value) {
            //Advanced Member - Browse Member
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'sitemember_location_userby-locations')
                    ->limit(1);
            $page_id = $select->query()->fetchObject()->page_id;
            if (empty($page_id)) {
                $db->insert('engine4_core_pages', array(
                    'name' => 'sitemember_location_userby-locations',
                    'displayname' => 'Advanced Member - Browse Member',
                    'title' => 'Advanced Member - Browse Member',
                    'description' => 'This is browse members page.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId('engine4_core_pages');
                if (!empty($page_id)) {
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
                    if (!empty($container_id)) {
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
                        if (!empty($middle_id)) {
                            //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
                            $db->insert('engine4_core_content', array(
                                'page_id' => $page_id,
                                'type' => 'widget',
                                'name' => 'sitemember.navigation-sitemember',
                                'parent_content_id' => $middle_id,
                                'order' => 1,
                                'params' => '{"0":"","title":"","titleCount":true}',
                            ));
                            $db->insert('engine4_core_content', array(
                                'page_id' => $page_id,
                                'type' => 'widget',
                                'name' => 'sitemember.search-sitemember',
                                'parent_content_id' => $middle_id,
                                'order' => 3,
                                'params' => '{"title":"","titleCount":true,"viewType":"horizontal","locationDetection":"0","whatWhereWithinmile":"1","advancedSearch":"0","nomobile":"0","name":"sitemember.search-sitemember"}',
                            ));
                            $db->insert('engine4_core_content', array(
                                'page_id' => $page_id,
                                'type' => 'widget',
                                'name' => 'sitemember.browse-members-sitemember',
                                'parent_content_id' => $middle_id,
                                'order' => 4,
                                'params' => '{"title":"","titleCount":true,"layouts_views":["1","2","3","4"],"layouts_order":"2","columnWidth":"202","truncationGrid":"50","columnHeight":"190","has_photo":"0","links":"","showDetailLink":"1","memberInfo":["ratingStar","location","directionLink","profileField","age"],"customParams":"5","custom_field_title":"0","custom_field_heading":"0","titlePosition":"1","orderby":"featured","show_content":"3","withoutStretch":"0","show_buttons":["facebook","twitter","pinit","like"],"pinboarditemWidth":"255","sitemember_map_sponsored":"1","itemCount":"20","truncation":"16","detactLocation":"0","defaultLocationDistance":"0","nomobile":"0","name":"sitemember.browse-members-sitemember"}',
                            ));
                        }
                    }
                }
            }


            //MEMBER PROFILE PAGE
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'user_profile_index')
                    ->limit(1);
            $page_id = $select->query()->fetchObject()->page_id;
            if (!empty($page_id)) {

                // container_id (will always be there)
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_core_content')
                        ->where('page_id = ?', $page_id)
                        ->where('type = ?', 'container')
                        ->where('name = ?', 'main')
                        ->limit(1);
                $container_id = $select->query()->fetchObject()->content_id;
                if (!empty($container_id)) {

                    // left_id (will always be there)
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_content')
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->limit(1);
                    $middle_id = $select->query()->fetchObject()->content_id;
                    if (!empty($middle_id)) {

                        // left_id (will always be there)
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_content')
                                ->where('parent_content_id = ?', $container_id)
                                ->where('type = ?', 'container')
                                ->where('name = ?', 'left')
                                ->limit(1);
                        $left_id = $select->query()->fetchObject()->content_id;

                        // @Make an condition
                        if (!empty($left_id)) {

                            // Check if it's already been placed
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_content')
                                    ->where('parent_content_id = ?', $left_id)
                                    ->where('type = ?', 'widget')
                                    ->where('name = ?', 'sitemember.profile-photo-sitemembers');

                            $infoSitemember = $select->query()->fetch();

                            if (empty($infoSitemember)) {
                                $select = new Zend_Db_Select($db);
                                $select
                                        ->from('engine4_core_content')
                                        ->where('parent_content_id = ?', $left_id)
                                        ->where('type = ?', 'widget')
                                        ->where('name = ?', 'user.profile-photo');
                                $infoUser = $select->query()->fetch();

                                if (!empty($infoUser)) {
                                    // tab on profile
                                    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name`= 'user.profile-photo' AND `engine4_core_content`.`parent_content_id` = $left_id AND `engine4_core_content`.`type` = 'widget';");
                                    $db->insert('engine4_core_content', array(
                                        'page_id' => $page_id,
                                        'type' => 'widget',
                                        'name' => 'sitemember.profile-photo-sitemembers',
                                        'parent_content_id' => $left_id,
                                        'order' => 1,
                                        'params' => '',
                                    ));
                                } elseif (empty($infoUser)) {
                                    $db->insert('engine4_core_content', array(
                                        'page_id' => $page_id,
                                        'type' => 'widget',
                                        'name' => 'sitemember.profile-photo-sitemembers',
                                        'parent_content_id' => $left_id,
                                        'order' => 1,
                                        'params' => '',
                                    ));
                                }
                            }
                        }
                    }
                }
            }

            //MEMBER PROFILE PAGE
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'user_profile_index')
                    ->limit(1);
            $page_id = $select->query()->fetchObject()->page_id;
            if (!empty($page_id)) {

                // container_id (will always be there)
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_core_content')
                        ->where('page_id = ?', $page_id)
                        ->where('type = ?', 'container')
                        ->where('name = ?', 'main')
                        ->limit(1);
                $container_id = $select->query()->fetchObject()->content_id;
                if (!empty($container_id)) {

                    // left_id (will always be there)
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_content')
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->limit(1);
                    $middle_id = $select->query()->fetchObject()->content_id;
                    if (!empty($middle_id)) {

                        // left_id (will always be there)
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_content')
                                ->where('parent_content_id = ?', $middle_id)
                                ->where('type = ?', 'widget')
                                ->where('name = ?', 'core.container-tabs')
                                ->limit(1);
                        $container_id = $select->query()->fetchObject()->content_id;
                        ;
                        // @Make an condition
                        if (!empty($container_id)) {

                            // Check if it's already been placed
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_content')
                                    ->where('parent_content_id = ?', $container_id)
                                    ->where('type = ?', 'widget')
                                    ->where('name = ?', 'sitemember.profile-friends-sitemember');

                            $infoSitemember = $select->query()->fetch();

                            if (empty($infoSitemember)) {
                                $select = new Zend_Db_Select($db);
                                $select
                                        ->from('engine4_core_content')
                                        ->where('parent_content_id = ?', $container_id)
                                        ->where('type = ?', 'widget')
                                        ->where('name = ?', 'user.profile-friends');
                                $infoUser = $select->query()->fetch();

                                if (!empty($infoUser)) {
                                    // tab on profile
                                    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name`= 'user.profile-friends' AND `engine4_core_content`.`parent_content_id` = $container_id AND `engine4_core_content`.`type` = 'widget';");
                                    $db->insert('engine4_core_content', array(
                                        'page_id' => $page_id,
                                        'type' => 'widget',
                                        'name' => 'sitemember.profile-friends-sitemember',
                                        'parent_content_id' => $container_id,
                                        'order' => 1,
                                        'params' => '{"title":"Friends","titleCount":true,"loaded_by_ajax":"1","itemCount":"20","nomobile":"0","name":"sitemember.profile-friends-sitemember"}',
                                    ));
                                } elseif (empty($infoUser)) {
                                    $db->insert('engine4_core_content', array(
                                        'page_id' => $page_id,
                                        'type' => 'widget',
                                        'name' => 'sitemember.profile-friends-sitemember',
                                        'parent_content_id' => $container_id,
                                        'order' => 1,
                                        'params' => '{"title":"Friends","titleCount":true,"loaded_by_ajax":"1","itemCount":"20","nomobile":"0","name":"sitemember.profile-friends-sitemember"}',
                                    ));
                                }
                            }
                        }
                    }
                }
            }

            //Member Home Page
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'user_index_home')
                    ->limit(1);
            $page_id = $select->query()->fetchObject()->page_id;
            if (!empty($page_id)) {
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_core_content')
                        ->where('page_id = ?', $page_id)
                        ->where('type = ?', 'container')
                        ->where('name = ?', 'main')
                        ->limit(1);
                $container_id = $select->query()->fetchObject()->content_id;
                if (!empty($container_id)) {
                    // $LEFT_ID (will always be there)
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_content')
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'left')
                            ->limit(1);
                    $left_id = $select->query()->fetchObject()->content_id;


                    // @Make an condition
                    if (!empty($left_id)) {

                        // Check if it's already been placed
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_content')
                                ->where('parent_content_id = ?', $left_id)
                                ->where('type = ?', 'widget')
                                ->where('name = ?', 'sitemember.profile-photo-sitemembers');
                        if (empty($infoSitemember)) {
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_content')
                                    ->where('parent_content_id = ?', $left_id)
                                    ->where('type = ?', 'widget')
                                    ->where('name = ?', 'user.home-photo');
                            $infoUser = $select->query()->fetch();

                            if (!empty($infoUser)) {
                                // tab on profile
                                $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name`= 'user.home-photo' AND `engine4_core_content`.`parent_content_id` = $left_id AND `engine4_core_content`.`type` = 'widget';");
                                $db->insert('engine4_core_content', array(
                                    'page_id' => $page_id,
                                    'type' => 'widget',
                                    'name' => 'sitemember.profile-photo-sitemembers',
                                    'parent_content_id' => $left_id,
                                    'order' => 1,
                                    'params' => '',
                                ));
                            } elseif (empty($infoUser)) {
                                $db->insert('engine4_core_content', array(
                                    'page_id' => $page_id,
                                    'type' => 'widget',
                                    'name' => 'sitemember.profile-photo-sitemembers',
                                    'parent_content_id' => $left_id,
                                    'order' => 1,
                                    'params' => '',
                                ));
                            }
                        }
                    }
                }
            }



//MEMBER PROFILE PAGE
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'user_profile_index')
                    ->limit(1);
            $page_id = $select->query()->fetchObject()->page_id;

// @Make an condition
            if (!empty($page_id)) {

                // container_id (will always be there)
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_core_content')
                        ->where('page_id = ?', $page_id)
                        ->where('type = ?', 'container')
                        ->where('name = ?', 'main')
                        ->limit(1);
                $container_id = $select->query()->fetchObject()->content_id;
                if (!empty($container_id)) {

                    // left_id (will always be there)
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_content')
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->limit(1);
                    $middle_id = $select->query()->fetchObject()->content_id;

                    if (!empty($middle_id)) {


                        // left_id (will always be there)
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_content')
                                ->where('parent_content_id = ?', $container_id)
                                ->where('type = ?', 'container')
                                ->where('name = ?', 'left')
                                ->limit(1);
                        $left_id = $select->query()->fetchObject()->content_id;

                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_content')
                                ->where('parent_content_id = ?', $container_id)
                                ->where('type = ?', 'container')
                                ->where('name = ?', 'right')
                                ->limit(1);
                        $right_id = $select->query()->fetchObject()->content_id;

                        // @Make an condition
                        if (!empty($left_id)) {

                            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'user.profile-friends-common' and `engine4_core_content`.`page_id` = '$page_id' LIMIT 1");
                            // Check if it's already been placed
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_content')
                                    ->where('parent_content_id = ?', $left_id)
                                    ->where('type = ?', 'widget')
                                    ->where('name = ?', 'sitemember.profile-friends-mutual');

                            $infoSitemember = $select->query()->fetch();

                            if (empty($infoSitemember)) {

                                $db->insert('engine4_core_content', array(
                                    'page_id' => $page_id,
                                    'type' => 'widget',
                                    'name' => 'sitemember.profile-friends-mutual',
                                    'parent_content_id' => $left_id,
                                    'order' => 999,
                                    'params' => '{"title":"","show":"friends","titlePosition":"1","photoWidth":"97","photoHeight":"97","nomobile":"0","itemCountPerPage":"6","name":"sitemember.profile-friends-mutual"}',
                                ));

                                $db->insert('engine4_core_content', array(
                                    'page_id' => $page_id,
                                    'type' => 'widget',
                                    'name' => 'sitemember.profile-friends-mutual',
                                    'parent_content_id' => $left_id,
                                    'order' => 999,
                                    'params' => '{"title":"","show":"mutualfriends","titlePosition":"1","photoWidth":"97","photoHeight":"97","nomobile":"0","itemCountPerPage":"6","name":"sitemember.profile-friends-mutual"}',
                                ));
                            }

                            // Check if it's already been placed
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_content')
                                    ->where('parent_content_id = ?', $left_id)
                                    ->where('type = ?', 'widget')
                                    ->where('name = ?', 'sitemember.review-button');

                            $infoSitememberReviewButton = $select->query()->fetch();

                            if (empty($infoSitememberReviewButton)) {
                                $db->insert('engine4_core_content', array(
                                    'page_id' => $page_id,
                                    'type' => 'widget',
                                    'name' => 'sitemember.review-button',
                                    'parent_content_id' => $left_id,
                                    'order' => 999
                                ));
                            }

                            // Check if it's already been placed
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_content')
                                    ->where('parent_content_id = ?', $left_id)
                                    ->where('type = ?', 'widget')
                                    ->where('name = ?', 'sitemember.overall-ratings');

                            $infoSitememberOverAllRatings = $select->query()->fetch();

                            if (empty($infoSitememberOverAllRatings)) {
                                $db->insert('engine4_core_content', array(
                                    'page_id' => $page_id,
                                    'type' => 'widget',
                                    'name' => 'sitemember.overall-ratings',
                                    'parent_content_id' => $left_id,
                                    'order' => 999
                                ));
                            }
                        }

                        // @Make an condition
                        if (!empty($right_id)) {
                            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'user.profile-friends-common' and `engine4_core_content`.`page_id` = '$page_id' LIMIT 1");
                            // Check if it's already been placed
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_content')
                                    ->where('parent_content_id = ?', $right_id)
                                    ->where('type = ?', 'widget')
                                    ->where('name = ?', 'sitemember.profile-friends-mutual');

                            $infoSitemember = $select->query()->fetch();

                            if (empty($infoSitemember)) {

                                $db->insert('engine4_core_content', array(
                                    'page_id' => $page_id,
                                    'type' => 'widget',
                                    'name' => 'sitemember.profile-friends-mutual',
                                    'parent_content_id' => $right_id,
                                    'order' => 999,
                                    'params' => '{"title":"","show":"friends","titlePosition":"1","photoWidth":"97","photoHeight":"97","nomobile":"0","itemCountPerPage":"6","name":"sitemember.profile-friends-mutual"}',
                                ));

                                $db->insert('engine4_core_content', array(
                                    'page_id' => $page_id,
                                    'type' => 'widget',
                                    'name' => 'sitemember.profile-friends-mutual',
                                    'parent_content_id' => $right_id,
                                    'order' => 999,
                                    'params' => '{"title":"","show":"mutualfriends","titlePosition":"1","photoWidth":"97","photoHeight":"97","nomobile":"0","itemCountPerPage":"6","name":"sitemember.profile-friends-mutual"}',
                                ));
                            }

                            // Check if it's already been placed
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_content')
                                    ->where('parent_content_id = ?', $right_id)
                                    ->where('type = ?', 'widget')
                                    ->where('name = ?', 'sitemember.review-button');

                            $infoSitememberReviewButton = $select->query()->fetch();

                            if (empty($infoSitememberReviewButton)) {
                                $db->insert('engine4_core_content', array(
                                    'page_id' => $page_id,
                                    'type' => 'widget',
                                    'name' => 'sitemember.review-button',
                                    'parent_content_id' => $right_id,
                                    'order' => 999
                                ));
                            }

                            // Check if it's already been placed
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_content')
                                    ->where('parent_content_id = ?', $right_id)
                                    ->where('type = ?', 'widget')
                                    ->where('name = ?', 'sitemember.overall-ratings');

                            $infoSitememberOverAllRatings = $select->query()->fetch();

                            if (empty($infoSitememberOverAllRatings)) {
                                $db->insert('engine4_core_content', array(
                                    'page_id' => $page_id,
                                    'type' => 'widget',
                                    'name' => 'sitemember.overall-ratings',
                                    'parent_content_id' => $right_id,
                                    'order' => 999
                                ));
                            }
                        }
                    }
                }
            }

//MEMBER PROFILE PAGE
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_pages')
                    ->where('name = ?', 'user_profile_index')
                    ->limit(1);
            $page_id = $select->query()->fetchObject()->page_id;

// @Make an condition
            if (!empty($page_id)) {

                // container_id (will always be there)
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_core_content')
                        ->where('page_id = ?', $page_id)
                        ->where('type = ?', 'container')
                        ->where('name = ?', 'main')
                        ->limit(1);
                $container_id = $select->query()->fetchObject()->content_id;
                if (!empty($container_id)) {

                    // left_id (will always be there)
                    $select = new Zend_Db_Select($db);
                    $select
                            ->from('engine4_core_content')
                            ->where('parent_content_id = ?', $container_id)
                            ->where('type = ?', 'container')
                            ->where('name = ?', 'middle')
                            ->limit(1);
                    $middle_id = $select->query()->fetchObject()->content_id;

                    if (!empty($middle_id)) {

                        // left_id (will always be there)
                        $select = new Zend_Db_Select($db);
                        $select
                                ->from('engine4_core_content')
                                ->where('parent_content_id = ?', $middle_id)
                                ->where('type = ?', 'widget')
                                ->where('name = ?', 'core.container-tabs')
                                ->limit(1);
                        $container_tab_id = $select->query()->fetchObject()->content_id;

                        // @Make an condition
                        if (!empty($container_tab_id)) {

                            // Check if it's already been placed
                            $select = new Zend_Db_Select($db);
                            $select
                                    ->from('engine4_core_content')
                                    ->where('parent_content_id = ?', $container_tab_id)
                                    ->where('type = ?', 'widget')
                                    ->where('name = ?', 'sitemember.user-review-sitemember');

                            $infoSitemember = $select->query()->fetch();

                            if (empty($infoSitemember)) {
                                $db->insert('engine4_core_content', array(
                                    'page_id' => $page_id,
                                    'type' => 'widget',
                                    'name' => 'sitemember.user-review-sitemember',
                                    'parent_content_id' => $container_tab_id,
                                    'order' => 999,
                                    'params' => '{"title":"User Reviews","titleCount":"true","loaded_by_ajax":"0","itemProsConsCount":"3","itemReviewsCount":"3","nomobile":"0","name":"sitemember.user-review-sitemember"}',
                                ));
                            }
                        }
                    }
                }
            }


//REVIEW PROFILE PAGE
            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "sitemember_review_view")
                    ->limit(1)
                    ->query()
                    ->fetchColumn();

//CREATE PAGE IF NOT EXIST
            if (!$page_id) {

                $containerCount = 0;
                $widgetCount = 0;

                $db->insert('engine4_core_pages', array(
                    'name' => "sitemember_review_view",
                    'displayname' => 'Advanced Member - Review Profile',
                    'title' => 'Member Review Profile',
                    'description' => 'This is the member review profile page.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();

                //MAIN CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => $containerCount++,
                ));
                $main_container_id = $db->lastInsertId();

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
                    'name' => 'sitemember.profile-review-breadcrumb-sitemember',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"nomobile":"1"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.profile-review-sitemember',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"loaded_by_ajax":"1","name":"sitemember.profile-review-sitemember"}',
                ));
            }



            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "sitemember_review_top-rated")
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (!$page_id) {

                $containerCount = 0;
                $widgetCount = 0;

                $db->insert('engine4_core_pages', array(
                    'name' => "sitemember_review_top-rated",
                    'displayname' => 'Advanced Member - Top Rated Members',
                    'title' => 'Top Rated Members',
                    'description' => 'Display the top rated members of our site with Average weighted ratings.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();

                //TOP CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => 1,
                ));
                $top_container_id = $db->lastInsertId();

                //TOP-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => 6,
                ));
                $top_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.navigation-sitemember',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"nomobile":"1"}',
                ));

                //MAIN CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => 2,
                ));
                $main_container_id = $db->lastInsertId();

                //MAIN-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 6,
                ));
                $main_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.searchbox-sitemember',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":"","locationDetection":"0","formElements":["textElement","profileTypeElement","locationElement","locationmilesSearch"],"textWidth":"275","locationWidth":"250","locationmilesWidth":"125","categoryWidth":"150","nomobile":"0","name":"sitemember.searchbox-sitemember"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.top-rated-table-view',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","itemCount":"7","nomobile":"0","name":"sitemember.top-rated-table-view"}',
                ));

                //RIGHT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'right',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 5,
                ));
                $main_right_id = $db->lastInsertId();
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.featured-reviews',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Featured Reviews","itemCount":"3","nomobile":"0","name":"sitemember.featured-reviews"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.options-sitemember',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Browse By","titleCount":true,"name":"sitemember.options-sitemember"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.most-rated-reviewed-recommend',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Most Recommend Members","titleCount":true,"viewType":"listview","columnWidth":"180","columnHeight":"328","orderby":"recommend_count","memberInfo":["reviewCount","recommendCount"],"itemCount":"5","truncation":"16","nomobile":"0","name":"sitemember.most-rated-reviewed-recommend"}',
                ));
            }



            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "sitemember_review_most-recommended-members")
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (!$page_id) {

                $containerCount = 0;
                $widgetCount = 0;

                $db->insert('engine4_core_pages', array(
                    'name' => "sitemember_review_most-recommended-members",
                    'displayname' => 'Advanced Member - Most Recommended Members',
                    'title' => 'Most Recommended Members',
                    'description' => 'Display the most recommended members of our site on the basis of recommendation they get from the site members.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();

                //TOP CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => 1,
                ));
                $top_container_id = $db->lastInsertId();

                //TOP-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => 6,
                ));
                $top_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.navigation-sitemember',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"nomobile":"1"}',
                ));

                //MAIN CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => 2,
                ));
                $main_container_id = $db->lastInsertId();

                //MAIN-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 6,
                ));
                $main_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.searchbox-sitemember',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":"","locationDetection":"0","formElements":["textElement","profileTypeElement","locationElement","locationmilesSearch"],"textWidth":"275","locationWidth":"250","locationmilesWidth":"125","categoryWidth":"150","nomobile":"0","name":"sitemember.searchbox-sitemember"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.most-recommend-table-view',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":""}',
                ));

                //RIGHT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'right',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 5,
                ));
                $main_right_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.featured-reviews',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Featured Reviews"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.options-sitemember',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Browse By","titleCount":true,"name":"sitemember.options-sitemember"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.most-rated-reviewed-recommend',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Most Reviewed ","titleCount":true,"viewType":"listview","columnWidth":"180","columnHeight":"328","orderby":"review_count","memberInfo":["ratingStar","reviewCount"],"itemCount":"5","truncation":"16","nomobile":"0","name":"sitemember.most-rated-reviewed-recommend"}',
                ));
            }


            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "sitemember_most_reviewer-members")
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (!$page_id) {

                $containerCount = 0;
                $widgetCount = 0;

                $db->insert('engine4_core_pages', array(
                    'name' => "sitemember_most_reviewer-members",
                    'displayname' => 'Advanced Member - Most Reviewed Members',
                    'title' => 'Most Reviewed Members',
                    'description' => 'Display the most reviewed members of our site on the basis of reviews they get from the site members.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();

                //TOP CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => 1,
                ));
                $top_container_id = $db->lastInsertId();

                //TOP-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => 6,
                ));
                $top_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.navigation-sitemember',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"nomobile":"1"}',
                ));

                //MAIN CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => 2,
                ));
                $main_container_id = $db->lastInsertId();

                //MAIN-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 6,
                ));
                $main_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.searchbox-sitemember',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":"","locationDetection":"0","formElements":["textElement","profileTypeElement","locationElement","locationmilesSearch"],"textWidth":"275","locationWidth":"250","locationmilesWidth":"125","categoryWidth":"150","nomobile":"0","name":"sitemember.searchbox-sitemember"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.most-reviewed-table-view',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":""}',
                ));
                //RIGHT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'right',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 5,
                ));
                $main_right_id = $db->lastInsertId();
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.featured-reviews',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Featured Reviews","itemCount":"3","nomobile":"0","name":"sitemember.featured-reviews"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.options-sitemember',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Browse By","titleCount":true,"name":"sitemember.options-sitemember"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.most-rated-reviewed-recommend',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Top Reviewers","titleCount":true,"viewType":"listview","columnWidth":"180","columnHeight":"328","orderby":"top_reviewers","memberInfo":["reviewCount"],"itemCount":"5","truncation":"16","nomobile":"0","name":"sitemember.most-rated-reviewed-recommend"}',
                ));
            }


            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "sitemember_top-reviewers")
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (!$page_id) {

                $containerCount = 0;
                $widgetCount = 0;

                $db->insert('engine4_core_pages', array(
                    'name' => "sitemember_top-reviewers",
                    'displayname' => 'Advanced Member - Top Reviewers',
                    'title' => 'Top Reviewers',
                    'description' => 'Display the members who has given maximum number of reviews.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();

                //TOP CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => 1,
                ));
                $top_container_id = $db->lastInsertId();

                //TOP-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => 6,
                ));
                $top_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.navigation-sitemember',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true}',
                ));

                //MAIN CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => 2,
                ));
                $main_container_id = $db->lastInsertId();

                //LEFT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'left',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => $containerCount++,
                ));
                $main_left_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.ajax-carousel-sitemember',
                    'parent_content_id' => $main_left_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Most Viewed Members","titleCount":true,"fea_spo":"","has_photo":"1","showPagination":"1","viewType":"1","blockHeight":"200","blockWidth":"208","titlePosition":"1","itemCount":"2","orderby":"view_count","links":["addfriend"],"memberInfo":["ratingStar","viewCount","likeCount","reviewCount"],"customParams":"5","custom_field_heading":"0","custom_field_title":"0","interval":"300","truncation":"16","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.ajax-carousel-sitemember"}',
                ));
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.recent-popular-random-members',
                    'parent_content_id' => $main_left_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Popular Members","titleCount":true,"viewType":"gridview","viewtype":"vertical","columnWidth":"62","fea_spo":"","has_photo":"1","titlePosition":"1","viewtitletype":"horizontal","columnHeight":"62","orderby":"view_count","interval":"week","links":"","memberInfo":"","customParams":"5","custom_field_title":"0","custom_field_heading":"0","itemCount":"6","titleLink":"Explore Members","truncation":"16","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.recent-popular-random-members"}',
                ));

                //MAIN-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 6,
                ));
                $main_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.searchbox-sitemember',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":"","locationDetection":"0","formElements":["textElement","locationElement"],"textWidth":"316","locationWidth":"250","locationmilesWidth":"125","categoryWidth":"150","nomobile":"0","name":"sitemember.searchbox-sitemember"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.top-reviewers-table-view',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":""}',
                ));

                //RIGHT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'right',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 5,
                ));
                $main_right_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.featured-reviews',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Featured Reviews","itemCount":"3","nomobile":"0","name":"sitemember.featured-reviews"}',
                ));
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.options-sitemember',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Browse By","titleCount":true,"name":"sitemember.options-sitemember"}',
                ));
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.most-rated-reviewed-recommend',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Most Rated Members","titleCount":true,"viewType":"listview","columnWidth":"180","columnHeight":"328","orderby":"rating_avg","memberInfo":["ratingStar","reviewCount"],"itemCount":"2","truncation":"16","nomobile":"0","name":"sitemember.most-rated-reviewed-recommend"}',
                ));
            }



            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "sitemember_review_member-reviews")
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (!$page_id) {

                $containerCount = 0;
                $widgetCount = 0;

                $db->insert('engine4_core_pages', array(
                    'name' => "sitemember_review_member-reviews",
                    'displayname' => 'Advanced Member - Member Reviews',
                    'title' => 'Members Reviewes',
                    'description' => 'This is the members reviews page.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();

                //TOP CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => 1,
                ));
                $top_container_id = $db->lastInsertId();

                //TOP-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => 6,
                ));
                $top_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.navigation-sitemember',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true}',
                ));

                //MAIN CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => 2,
                ));
                $main_container_id = $db->lastInsertId();

                //MAIN-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 6,
                ));
                $main_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.review-poster-name-sitemember',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","nomobile":"1"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.user-review-sitemember',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"User Reviews","titleCount":"true","loaded_by_ajax":1,"nomobile":"1"}',
                ));

                //RIGHT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'right',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 5,
                ));
                $main_right_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.profile-photo-sitemembers',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"statistics":["featuredLabel","sponsoredLabel"],"title":"","nomobile":"0","name":"sitemember.profile-photo-sitemembers"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteusercoverphoto.user-profile-fields',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Information","customFields":"5","nomobile":"0","name":"siteusercoverphoto.user-profile-fields"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.review-button',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":""}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.reviewed-members-sitemember',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":""}',
                ));
            }


            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "sitemember_review_owner-reviews")
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (!$page_id) {

                $containerCount = 0;
                $widgetCount = 0;

                $db->insert('engine4_core_pages', array(
                    'name' => "sitemember_review_owner-reviews",
                    'displayname' => 'Advanced Member - Owner Reviews',
                    'title' => 'Owner Reviewes',
                    'description' => 'This is the owner reviews page.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();

                //TOP CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => 1,
                ));
                $top_container_id = $db->lastInsertId();

                //TOP-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => 6,
                ));
                $top_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.navigation-sitemember',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true}',
                ));

                //MAIN CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => 2,
                ));
                $main_container_id = $db->lastInsertId();

                //MAIN-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 6,
                ));
                $main_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.review-poster-name-sitemember',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","nomobile":"1"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.owner-review-sitemember',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":"true","loaded_by_ajax":1,"nomobile":"1"}',
                ));

                //RIGHT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'right',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 5,
                ));
                $main_right_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.profile-photo-sitemembers',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"statistics":["featuredLabel","sponsoredLabel"],"title":"","nomobile":"0","name":"sitemember.profile-photo-sitemembers"}',
                ));
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteusercoverphoto.user-profile-fields',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Information","customFields":"5","nomobile":"0","name":"siteusercoverphoto.user-profile-fields"}',
                ));
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.reviews-statistics',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Reviews Statistics","nomobile":"1"}',
                ));
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.reviewed-members-sitemember',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","nomobile":"1"}',
                ));
            }


            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "sitemember_review_browse")
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (!$page_id) {

                $containerCount = 0;
                $widgetCount = 0;
                $db->insert('engine4_core_pages', array(
                    'name' => "sitemember_review_browse",
                    'displayname' => 'Advanced Member - Browse Members Reviews',
                    'title' => 'Browse Member Reviews',
                    'description' => 'This is the browse members reviews page.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();

                //TOP CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => 1,
                ));
                $top_container_id = $db->lastInsertId();

                //TOP-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => 6,
                ));
                $top_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.navigation-sitemember',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"nomobile":"1"}',
                ));

                //MAIN CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => 2,
                ));
                $main_container_id = $db->lastInsertId();

                //MAIN-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 6,
                ));
                $main_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'core.content',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"nomobile":"1"}',
                ));

                //RIGHT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'right',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 5,
                ));
                $main_right_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.review-browse-search',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","nomobile":"1"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.reviews-statistics',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Reviews Statistics"}',
                ));
            }

            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', "sitemember_top-raters")
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (!$page_id) {

                $containerCount = 0;
                $widgetCount = 0;

                $db->insert('engine4_core_pages', array(
                    'name' => "sitemember_top-raters",
                    'displayname' => 'Advanced Member - Top Raters',
                    'title' => 'Top Raters',
                    'description' => 'This is the top raters page.',
                    'custom' => 0,
                ));
                $page_id = $db->lastInsertId();

                //TOP CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'order' => 1,
                ));
                $top_container_id = $db->lastInsertId();

                //TOP-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $top_container_id,
                    'order' => 6,
                ));
                $top_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.navigation-sitemember',
                    'parent_content_id' => $top_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"nomobile":"1"}',
                ));

                //MAIN CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'main',
                    'page_id' => $page_id,
                    'order' => 2,
                ));
                $main_container_id = $db->lastInsertId();

                //MAIN-MIDDLE CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 6,
                ));
                $main_middle_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.searchbox-sitemember',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":"","locationDetection":"0","formElements":["textElement","profileTypeElement","locationElement","locationmilesSearch"],"textWidth":"275","locationWidth":"250","locationmilesWidth":"125","categoryWidth":"150","nomobile":"0","name":"sitemember.searchbox-sitemember"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.top-raters-table-view',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":""}',
                ));

                //RIGHT CONTAINER
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'right',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_container_id,
                    'order' => 5,
                ));
                $main_right_id = $db->lastInsertId();

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.options-sitemember',
                    'parent_content_id' => $main_right_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"nomobile":"1"}',
                ));
            }
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules', array('name'))
                ->where('enabled = ?', 1)
                ->where('name = ?', 'siteusercoverphoto');
        $siteusercoverphotoName = $select->query()->fetchColumn();
        if ($siteusercoverphotoName) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_content', array('params'))
                    ->where('name = ?', 'siteusercoverphoto.user-cover-photo');
            $params = $select->query()->fetchColumn();
            if (!empty($params)) {
                $params = Zend_Json::decode($params);
                if (!isset($params['showContent']['rating'])) {
                    $params['showContent'][] = 'rating';
                    $params = Zend_Json::encode($params);
                    $db->query("UPDATE `engine4_core_content` SET `params` = '$params' WHERE `engine4_core_content`.`name` = 'siteusercoverphoto.user-cover-photo'");
                }
            }
        }


        //start Advanced Search plugin work.
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')
                ->where('name = ?', 'siteadvsearch');
        $is_enabled = $select->query()->fetchObject();
        if (!empty($is_enabled)) {

            $page_id = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', 'siteadvsearch_index_browse-member')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (!$page_id) {

                $containerCount = 0;
                $widgetCount = 0;

                $db->insert('engine4_core_pages', array(
                    'name' => 'siteadvsearch_index_browse-member',
                    'displayname' => 'Advanced Search - SEAO - Advanced Members',
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
                    'name' => 'sitemember.search-sitemember',
                    'parent_content_id' => $right_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"viewType":"vertical","locationDetection":"0","whatWhereWithinmile":"1","advancedSearch":"0","nomobile":"0","name":"sitemember.search-sitemember"}',
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitemember.browse-members-sitemember',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"layouts_views":["1"],"layouts_order":"1","columnWidth":"199","truncationGrid":"50","columnHeight":"190","has_photo":"0","links":["addfriend","messege","likebutton","poke","suggestion"],"showDetailLink":"1","memberInfo":["location","directionLink","mutualFriend","profileField","age"],"customParams":"5","custom_field_title":"0","custom_field_heading":"0","titlePosition":"1","orderby":"featured","show_content":"3","withoutStretch":"0","show_buttons":["facebook","twitter","pinit","like"],"pinboarditemWidth":"255","sitemember_map_sponsored":"1","itemCount":"8","truncation":"50","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.browse-members-sitemember"}',
                ));
            }
        }
        //end advanced search plugin work

        // ADD COLUMN FOR USER FOLLOW WORK
        $column_exist = $db->query("SHOW COLUMNS FROM `engine4_users` LIKE 'follow_count'")->fetch();
        if (empty($column_exist)) {
            $db->query("ALTER TABLE  `engine4_users` ADD  `follow_count` INT( 11 ) NOT NULL DEFAULT  '0';");
        }

        $this->setActivityFeeds();
        parent::onInstall();
    }

    private function getVersion() {

        $db = $this->getDb();

        $errorMsg = '';
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'seaocore' => '4.9.2p2'
        );

        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')
                    ->where('name = ?', "$key")
                    ->where('enabled = ?', 1);
            $isModEnabled = $select->query()->fetchObject();
            if (!empty($isModEnabled)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_modules', array('title', 'version'))
                        ->where('name = ?', "$key")
                        ->where('enabled = ?', 1);
                $getModVersion = $select->query()->fetchObject();

                $isModSupport = strcasecmp($getModVersion->version, $value);
                if ($isModSupport < 0) {
                    $finalModules[] = $getModVersion->title;
                }
            }
        }

        foreach ($finalModules as $modArray) {
            $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
        }
        return $errorMsg;
    }

    public function onPostInstall() {

      $hasVersion = $this->checkVersion($this->_currentVersion, '4.9.2p1');
      if( $this->_databaseOperationType == 'upgrade' && empty($hasVersion) ) {
        $view = new Zend_View();
        $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->gotoUrl($baseUrl . 'admin/sitemember/settings/activate-compliment/flag/install');
      }
        //SITEMOBILE CODE TO CALL MY.SQL ON POST INSTALL
        $moduleName = 'sitemember';
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

    private function _getVersion() {

        $db = $this->getDb();

        $errorMsg = '';
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'seaocore' => '4.9.2p2',
            'sitemobile' => '4.9.2',
            'siteusercoverphoto' => '4.9.1p2',
          );

        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')
                    ->where('name = ?', "$key")
                    ->where('enabled = ?', 1);
            $isModEnabled = $select->query()->fetchObject();
            if (!empty($isModEnabled)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_modules', array('title', 'version'))
                        ->where('name = ?', "$key")
                        ->where('enabled = ?', 1);
                $getModVersion = $select->query()->fetchObject();

                $isModSupport = $this->checkVersion($getModVersion->version, $value);
                if (empty($isModSupport)) {
                    $finalModules[$key] = $getModVersion->title;
                }
            }
        }

        foreach ($finalModules as $modArray) {
            $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "' . $modArray . '".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
        }

        return $errorMsg;
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


            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitemember_review", "sitemember", \'{item:$subject} replied to a comment on {item:$owner}\'\'s member review {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');

            $db->query("INSERT IGNORE INTO `engine4_nestedcomment_modules` (`module`, `resource_type`, `enabled`) VALUES ('sitemember', 'sitemember_review', 0)");
        }
    }

    public function onEnable() {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_menuitems')
                ->where('name = ?', 'user_edit_location');
        $check_row = $select->query()->fetchObject();

        if (empty($check_row)) {
            $db->update('engine4_core_menuitems', array('enabled' => 1), array('name =?' => 'user_edit_location'));
        }

        parent::onEnable();
    }

    public function onDisable() {

        //SET EDIT LOCATION MENU DISABLED
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_menuitems')
                ->where('name = ?', 'user_edit_location');
        $check_row = $select->query()->fetchObject();

        if (!empty($check_row)) {
            $db->update('engine4_core_menuitems', array('enabled' => 0), array('name =?' => 'user_edit_location'));
        }
        parent::onDisable();
    }

    private function checkVersion($databaseVersion, $checkDependancyVersion) {
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

}
