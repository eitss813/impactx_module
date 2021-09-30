<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
class Sitepagemember_Installer extends Engine_Package_Installer_Module
{
    public function onPreInstall()
    {
        $db = $this->getDb();

        //CHECK THAT SITEPAGE PLUGIN IS ACTIVATED OR NOT
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.is.active')
            ->limit(1);
        $sitepage_settings = $select->query()->fetchAll();
        if (!empty($sitepage_settings)) {
            $sitepage_is_active = $sitepage_settings[0]['value'];
        } else {
            $sitepage_is_active = 0;
        }

        //CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('enabled = ?', 1);
        $check_sitepage = $select->query()->fetchObject();
        if (!empty($check_sitepage) && !empty($sitepage_is_active)) {
            $PRODUCT_TYPE               = 'sitepagemember';
            $PLUGIN_TITLE               = 'Sitepagemember';
            $PLUGIN_VERSION             = '4.10.3p4';
            $PLUGIN_CATEGORY            = 'plugin';
            $PRODUCT_DESCRIPTION        = 'Directory / Pages - Page Members Extension';
            $PRODUCT_TITLE              = 'Directory / Pages - Page Members Extension';
            $_PRODUCT_FINAL_FILE        = 0;
            $sitepage_plugin_version    = '4.8.6p7';
            $SocialEngineAddOns_version = '4.8.9p12';
            $file_path                  = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
            $is_file                    = file_exists($file_path);
            if (empty($is_file)) {
                include APPLICATION_PATH . "/application/modules/Sitepage/controllers/license/license4.php";
            } else {
                include $file_path;
            }
            parent::onPreInstall();
        } elseif (!empty($check_sitepage) && empty($sitepage_is_active)) {
            $baseUrl    = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
            $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            if (strstr($url_string, "manage/install")) {
                $calling_from = 'install';
            } else if (strstr($url_string, "manage/query")) {
                $calling_from = 'queary';
            }
            $explode_base_url = explode("/", $baseUrl);
            foreach ($explode_base_url as $url_key) {
                if ($url_key != 'install') {
                    $core_final_url .= $url_key . '/';
                }
            }

            return $this->_error("<span style='color:red'>Note: You have installed the Directory / Pages Plugin but not activated it on your site yet. Please activate it first before installing the Directory / Pages - Page Members Extension.</span><a href='" . 'http://' . $core_final_url . "admin/sitepage/settings/readme'> Click here</a> to activate the Page Plugin.");
        } else {
            $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
            return $this->_error("<span style='color:red'>Note: You have not installed the Directory / Pages Plugin on your site yet. Please install it first before installing the Directory / Pages - Page Members Extension.</span><a href='" . $base_url . "/manage'> Click here</a> to go Manage Packages.");
        }
    }

    /*
    if first > second => 1
    if first = second => -1
    if first < second => 0
     */
    private function checkVersion($databaseVersion, $checkDependancyVersion)
    {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0) {
            return -1;
        }

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr)) {
            $count = count($fArr);
        } else {
            $count = count($sArr);
        }

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue) {
                    return 1;
                } elseif ($fValue < $sValue) {
                    return 0;
                } else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else {
                        continue;
                    }

                }
            } elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue) {
                    return 1;
                } elseif ($fsArr[0] < $sValue) {
                    return 0;
                } else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0]) {
                    return 1;
                } elseif ($fValue < $ssArr[0]) {
                    return 0;
                } else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0]) {
                    return 1;
                } elseif ($fsArr[0] < $ssArr[0]) {
                    return 0;
                } else {
                    if ($fsArr[1] > $ssArr[1]) {
                        return 1;
                    } elseif ($fsArr[1] < $ssArr[1]) {
                        return 0;
                    } else {
                        return -1;
                    }
                }
            }
        }
    }

    public function onInstall()
    {
        $db = $this->getDb();

        $db->query("UPDATE  `engine4_activity_notificationtypes` SET  `handler` =  'sitepagemember.widget.request-member' WHERE  `engine4_activity_notificationtypes`.`type` =  'sitepagemember_invite' LIMIT 1 ;");

        $select            = new Zend_Db_Select($db);
        $pagememberVersion = $select
            ->from('engine4_core_modules', 'version')
            ->where('name = ?', 'sitepagemember')
            ->query()
            ->fetchColumn();
        $version_check = $this->checkVersion($pagememberVersion, '4.7.1p1');
        if ($version_check != 1) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_sitepage_membership', array('title', 'role_id', 'member_id'));
            $results = $select->query()->fetchAll();
            foreach ($results as $result) {
                $title   = Zend_Json::encode($result['title']);
                $role_id = Zend_Json::encode($result['role_id']);
                $db->update('engine4_sitepage_membership', array('title' => "[" . $title . "]", "role_id" => "[" . $role_id . "]"), array('member_id = ?' => $result['member_id']));
            }
        }

        $role_id = $db->query("SHOW COLUMNS FROM engine4_sitepage_membership LIKE 'role_id'")->fetch();
        if (!empty($role_id)) {
            $db->query("ALTER TABLE `engine4_sitepage_membership` CHANGE `role_id` `role_id` VARCHAR( 255 ) NOT NULL");
        }

        //For add column in the 'engine4_sitepage_membership' table.
        $action_notification_field = $db->query("SHOW COLUMNS FROM engine4_sitepage_membership LIKE 'action_email'")->fetch();
        if (empty($action_notification_field)) {
            $db->query("ALTER TABLE  `engine4_sitepage_membership` ADD `action_email` VARCHAR( 255 ) NULL");
        }

        //For add column in the 'engine4_sitepage_membership' table.
        $action_notification_field = $db->query("SHOW COLUMNS FROM engine4_sitepage_membership LIKE 'action_notification'")->fetch();
        if (empty($action_notification_field)) {
            $db->query("ALTER TABLE  `engine4_sitepage_membership` ADD `action_notification` VARCHAR( 255 ) NULL");
        }

        //For add column in the 'engine4_sitepage_pages' table.
        $email_field = $db->query("SHOW COLUMNS FROM engine4_sitepage_membership LIKE 'email'")->fetch();
        if (empty($email_field)) {
            $db->query("ALTER TABLE `engine4_sitepage_membership` ADD `email` TINYINT( 1 ) NOT NULL DEFAULT '1'");
        }

        //DELETE IMPORTTAB FROM THE ADMIN MENU.
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_menuitems')
            ->where('name = ?', 'sitepagemember_admin_main_import');
        $importTab = $select->query()->fetchAll();
        if (!empty($importTab)) {
            $db->query("DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'sitepagemember_admin_main_import';");
        }

        //START DELETE FOR PAGE JOIN FEED FROM STREAM TABLE.
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules', 'version')
            ->where('name = ?', 'sitepagemember');

        $version_check = $select->query()->fetchColumn();

        if ($this->checkVersion($version_check, '4.5.0p4') != 1) {

            $select = new Zend_Db_Select($db);
            $select->from('engine4_activity_stream', "action_id")->where('type = ?', 'sitepage_join')->group('action_id');
            $str_action_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            $action_ids     = array();

            if (!empty($str_action_ids)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_activity_actions', "action_id")->where('type = ?', 'sitepage_join')->where('action_id IN(?)', $str_action_ids);
                $action_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            }

            $diff_action_ids = array_diff($str_action_ids, $action_ids);

            if ($diff_action_ids) {
                $db->delete('engine4_activity_stream', array('action_id IN(?)' => $diff_action_ids));
            }
        }
        //END DELETE FOR PAGE JOIN FEED FROM STREAM TABLE.

        //For add column in the 'engine4_sitepage_pages' table.
        $member_invite   = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'member_invite'")->fetch();
        $member_approval = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'member_approval'")->fetch();
        $memberCount     = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'member_count'")->fetch();
        if (empty($member_invite)) {
            $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `member_invite` TINYINT( 1 ) NOT NULL DEFAULT '1'");
        }

        if (empty($member_approval)) {
            $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `member_approval` TINYINT( 1 ) NOT NULL DEFAULT '1'");
        }

        if (empty($memberCount)) {
            $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `member_count` smallint(6) unsigned NOT NULL");
        }

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')
            ->where('name = ?', 'sitepagemember')
            ->where('enabled = ?', 1);
        $check_sitepagemember = $select->query()->fetchObject();
        if (empty($check_sitepagemember)) {

            //All entry in manage admin table move in the membership table install page member plugin.
            $select = new Zend_Db_Select($db);
            $select->from('engine4_sitepage_manageadmins', array('page_id', 'user_id'));
            $check_sitepage = $select->query()->fetchAll();
            if (!empty($check_sitepage)) {
                foreach ($check_sitepage as $result) {
                    $db->insert('engine4_sitepage_membership', array(
                        'resource_id' => $result['page_id'],
                        'user_id'     => $result['user_id'],
                        'page_id'     => $result['page_id'],
                    ));
                }
            }

            //For member count for page table.
            $select = new Zend_Db_Select($db);
            $select->from('engine4_sitepage_manageadmins', array('page_id', 'user_id', 'COUNT(*) as count'))->group('page_id');
            $check_count = $select->query()->fetchAll();

            if (!empty($check_count)) {
                foreach ($check_count as $check_counts) {
                    $db->query("UPDATE `engine4_sitepage_pages` SET `member_count` = '" . $check_counts['count'] . "' WHERE `engine4_sitepage_pages`.`page_id` = '" . $check_counts['page_id'] . "';");
                }
            }

            //update all member level settings with new setting member. page plugin version condition.
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
                    ->where('name != ?', 'auth_html')
                    ->where('name LIKE "%auth_%"');
                $result = $select->query()->fetchAll();

                foreach ($result as $results) {
                    $params   = !empty($results['params']) ? Zend_Json::decode($results['params']) : array();
                    $params[] = 'member';
                    $paramss  = Zend_Json::encode($params);
                    $db->query("UPDATE `engine4_authorization_permissions` SET `params` = '$paramss' WHERE `engine4_authorization_permissions`.`type` = 'sitepage_page' AND `engine4_authorization_permissions`.`name` = '" . $results['name'] . "' AND `engine4_authorization_permissions`.`level_id` = '" . $results['level_id'] . "';");
                }
            }
            //END
        }

        //Start Memeber Profile page Widget
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
        $page_id = $select->query()->fetchAll();

        if (!empty($page_id)) {
            $page_id        = $page_id[0]['page_id'];
            $selectWidgetId = new Zend_Db_Select($db);
            $selectWidgetId->from('engine4_core_content', array('content_id'))
                ->where('page_id =?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'core.container-tabs')
                ->limit(1);
            $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
            if (!empty($fetchWidgetContentId)) {
                $tab_id = $fetchWidgetContentId[0]['content_id'];

                // Check if it's already been placed
                $select = new Zend_Db_Select($db);
                $select
                    ->from('engine4_core_content')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitepage.profile-joined-sitepage');
                $info = $select->query()->fetch();

                if (empty($info)) {
                    $db->insert('engine4_core_content', array(
                        'page_id'           => $page_id,
                        'type'              => 'widget',
                        'name'              => 'sitepage.profile-joined-sitepage',
                        'parent_content_id' => $tab_id,
                        'order'             => 999,
                        'params'            => '{"title":"Joined Pages","titleCount":""}',
                    ));
                }
            }
        }
        //End Memeber Profile page Widget

        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'sitepagemember_index_home')
            ->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name'        => 'sitepagemember_index_home',
                'displayname' => 'Page Members Home',
                'title'       => 'Page Members Home',
                'description' => 'This is page member home page.',
                'custom'      => 1,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'container',
                'name'              => 'main',
                'parent_content_id' => null,
                'order'             => 2,
                'params'            => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'container',
                'name'              => 'right',
                'parent_content_id' => $container_id,
                'order'             => 5,
                'params'            => '',
            ));
            $right_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'container',
                'name'              => 'left',
                'parent_content_id' => $container_id,
                'order'             => 4,
                'params'            => '',
            ));
            $left_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'container',
                'name'              => 'top',
                'parent_content_id' => null,
                'order'             => 1,
                'params'            => '',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'container',
                'name'              => 'middle',
                'parent_content_id' => $top_id,
                'order'             => 6,
                'params'            => '',
            ));
            $top_middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'container',
                'name'              => 'middle',
                'parent_content_id' => $container_id,
                'order'             => 6,
                'params'            => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // Top Middle
            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'widget',
                'name'              => 'sitepage.browsenevigation-sitepage',
                'parent_content_id' => $top_middle_id,
                'order'             => 3,
                'params'            => '',
            ));

            // Left
            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'widget',
                'name'              => 'sitepagemember.home-recent-mostvaluable-sitepagemember',
                'parent_content_id' => $left_id,
                'order'             => 16,
                'params'            => '{"title":"Recent Members","select_option":"1","titleCount":"true"}',
            ));

            // Middele
            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'widget',
                'name'              => 'sitepagemember.featured-members-slideshow',
                'parent_content_id' => $left_id,
                'order'             => 14,
                'params'            => '{"title":"Featured Members","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'widget',
                'name'              => 'sitepagemember.list-members-tabs-view',
                'parent_content_id' => $middle_id,
                'order'             => 17,
                'params'            => '{"title":"Members","margin_photo":"12","showViewMore":"1"}',
            ));

            // Right Side
            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'widget',
                'name'              => 'sitepagemember.home-recent-mostvaluable-sitepagemember',
                'parent_content_id' => $right_id,
                'order'             => 20,
                'params'            => '{"title":"Top Page Joiners","select_option":"2","titleCount":"true"}',
            ));

            // Right Side
            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'widget',
                'name'              => 'sitepagemember.search-sitepagemember',
                'parent_content_id' => $right_id,
                'order'             => 18,
                'params'            => '',
            ));

            // Right Side
            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'widget',
                'name'              => 'sitepagemember.sitepagememberlist-link',
                'parent_content_id' => $right_id,
                'order'             => 19,
                'params'            => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'widget',
                'name'              => 'sitepagemember.member-of-the-day',
                'parent_content_id' => $left_id,
                'order'             => 15,
                'params'            => '{"title":"Member of the Day"}',
            ));
        }

        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'communityad')
            ->where('enabled 	 = ?', 1)
            ->limit(1);
        $infomation = $select->query()->fetch();

        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.communityads')
            ->where('value 	 = ?', 1)
            ->limit(1);
        $rowinfo = $select->query()->fetch();

        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'sitepagemember_index_browse')
            ->limit(1);
        $info_browse = $select->query()->fetch();

        if (empty($info_browse)) {
            $db->insert('engine4_core_pages', array(
                'name'        => 'sitepagemember_index_browse',
                'displayname' => 'Browse Page Members',
                'title'       => 'Page Members',
                'description' => 'This is the page members.',
                'custom'      => 1,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            //CONTAINERS
            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'container',
                'name'              => 'main',
                'parent_content_id' => null,
                'order'             => 2,
                'params'            => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            //INSERT MAIN - MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'container',
                'name'              => 'middle',
                'parent_content_id' => $container_id,
                'order'             => 6,
                'params'            => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            //INSERT MAIN - RIGHT CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'container',
                'name'              => 'right',
                'parent_content_id' => $container_id,
                'order'             => 5,
                'params'            => '',
            ));
            $right_id = $db->lastInsertId('engine4_core_content');

            //INSERT TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'container',
                'name'              => 'top',
                'parent_content_id' => null,
                'order'             => 1,
                'params'            => '',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            //INSERT TOP- MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'container',
                'name'              => 'middle',
                'parent_content_id' => $top_id,
                'order'             => 6,
                'params'            => '',
            ));
            $top_middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'widget',
                'name'              => 'sitepage.browsenevigation-sitepage',
                'parent_content_id' => $top_middle_id,
                'order'             => 1,
                'params'            => '{"title":"","titleCount":""}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'widget',
                'name'              => 'sitepagemember.search-sitepagemember',
                'parent_content_id' => $right_id,
                'order'             => 3,
                'params'            => '{"title":"","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id'           => $page_id,
                'type'              => 'widget',
                'name'              => 'sitepagemember.sitepage-member',
                'parent_content_id' => $middle_id,
                'order'             => 2,
                'params'            => '{"title":"","titleCount":""}',
            ));

            if (!empty($infomation) && !empty($rowinfo)) {
                $db->insert('engine4_core_content', array(
                    'page_id'           => $page_id,
                    'type'              => 'widget',
                    'name'              => 'sitepage.page-ads',
                    'parent_content_id' => $right_id,
                    'order'             => 11,
                    'params'            => '{"title":"","titleCount":""}',
                ));
            }
        }

        //Member home page
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
                $select = new Zend_Db_Select($db);
                $select
                    ->from('engine4_core_content')
                    ->where('parent_content_id = ?', $container_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'right')
                    ->limit(1);
                $right_id = $select->query()->fetchObject()->content_id;
                if (!empty($right_id)) {
                    $select = new Zend_Db_Select($db);
                    $select
                        ->from('engine4_core_content')
                        ->where('parent_content_id = ?', $right_id)
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'sitepagemember.mostjoined-sitepage');
                    $info = $select->query()->fetch();
                    if (empty($info)) {
                        $db->insert('engine4_core_content', array(
                            'page_id'           => $page_id,
                            'type'              => 'widget',
                            'name'              => 'sitepagemember.mostjoined-sitepage',
                            'parent_content_id' => $right_id,
                            'order'             => 1,
                            'params'            => '{"title":"Most Joined Pages"}',
                        ));
                    }
                }
            }
        }

        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemobile')
            ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if ($is_sitemobile_object) {
            include APPLICATION_PATH . "/application/modules/Sitepagemember/controllers/license/mobileLayoutCreation.php";
        }

        $pageIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagemember_roles LIKE 'page_id'")->fetch();
        if (!empty($pageIdColumn)) {
            $pageIdIndex = $db->query("SHOW INDEX FROM `engine4_sitepagemember_roles` WHERE Key_name = 'page_id'")->fetch();
            if (empty($pageIdIndex)) {
                $db->query("ALTER TABLE `engine4_sitepagemember_roles` ADD INDEX ( `page_id` )");
            }
        }

        $db->query("INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitepage_gutter_notifications', 'sitepagemember', 'Notification Settings', 'Sitepagemember_Plugin_Menus::sitepageGutterNotificationSettings', '', 'sitepage_gutter', NULL, '1', '0', '999');");

        //updated the member level setting of page if document integration is installed, added option of content member
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'documentintegration.isActivate')
            ->where('value = ?', 1);
        $documentintegration_isActivate_object = $select->query()->fetchObject();
        if ($documentintegration_isActivate_object) {
            $db->query("UPDATE `engine4_authorization_permissions` SET `params` = '[\"registered\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"member\",\"like_member\",\"owner\"]' WHERE `engine4_authorization_permissions`.`level_id` NOT IN(5) AND `engine4_authorization_permissions`.`type` = 'sitepage_page' AND `engine4_authorization_permissions`.`name` = 'auth_sdcreate';
        ");
        }

        //updated the member level setting of page if video integration is installed, added option of content member
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitevideointegration.isActivate')
            ->where('value = ?', 1);
        $sitevideointegration_isActivate_object = $select->query()->fetchObject();
        if ($sitevideointegration_isActivate_object) {
            $db->query("UPDATE `engine4_authorization_permissions` SET `params` = '[\"registered\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"member\",\"like_member\",\"owner\"]' WHERE `engine4_authorization_permissions`.`level_id` NOT IN(5) AND `engine4_authorization_permissions`.`type` = 'sitepage_page' AND `engine4_authorization_permissions`.`name` = 'auth_svcreate';
        ");
        }

        //updated the member level setting of business if crowdfunding is installed, added option of content member
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitecrowdfunding.isActivate')
            ->where('value = ?', 1);
        $sitecrowdfunding_isActivate_object = $select->query()->fetchObject();
        if ($sitecrowdfunding_isActivate_object) {
            $db->query("UPDATE `engine4_authorization_permissions` SET `params` = '[\"registered\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"member\",\"like_member\",\"owner\"]' WHERE `engine4_authorization_permissions`.`level_id` NOT IN(5) AND `engine4_authorization_permissions`.`type` = 'sitepage_page' AND `engine4_authorization_permissions`.`name` = 'auth_sprcreate';
        ");
        }

        parent::onInstall();
    }

    public function onPostInstall()
    {
        $db     = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemobile')
            ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if (!empty($is_sitemobile_object)) {
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('sitepagemember','1')");
            $select = new Zend_Db_Select($db);
            $select
                ->from('engine4_sitemobile_modules')
                ->where('name = ?', 'sitepagemember')
                ->where('integrated = ?', 0);
            $is_sitemobile_object = $select->query()->fetchObject();
            if ($is_sitemobile_object) {
                $actionName     = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
                $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
                if ($controllerName == 'manage' && $actionName == 'install') {
                    $view       = new Zend_View();
                    $baseUrl    = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitepagemember/integrated/0/redirect/install');
                }
            }
        }

        //Work for the word changes in the page plugin .csv file.
        $actionName     = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        if ($controllerName == 'manage' && ($actionName == 'install' || $actionName == 'query')) {
            $view       = new Zend_View();
            $baseUrl    = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            if ($actionName == 'install') {
                $redirector->gotoUrl($baseUrl . 'admin/sitepage/settings/language/redirect/install');
            } else {
                $redirector->gotoUrl($baseUrl . 'admin/sitepage/settings/language/redirect/query');
            }
        }
    }
}