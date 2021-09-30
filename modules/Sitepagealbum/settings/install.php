<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagealbum_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {

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
      $PRODUCT_TYPE = 'sitepagealbum';
      $PLUGIN_TITLE = 'Sitepagealbum';
      $PLUGIN_VERSION = '4.10.3p2';
      $PLUGIN_CATEGORY = 'plugin';
      $PRODUCT_DESCRIPTION = 'Sitepagealbum Plugin';
      $PRODUCT_TITLE = 'Directory / Pages - Albums Extension';
      $_PRODUCT_FINAL_FILE = 0;
      $sitepage_plugin_version = '4.8.6';
      $SocialEngineAddOns_version = '4.10.3p6';
      $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
      $is_file = file_exists($file_path);
      if (empty($is_file)) {
        include APPLICATION_PATH . "/application/modules/Sitepage/controllers/license/license4.php";
      } else {
        include $file_path;
      }

      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', 'sitepagealbum')
              ->where('version <= ?', '4.1.5p1');
      $is_enabled = $select->query()->fetchObject();
      if (!empty($is_enabled)) {
        $select = new Zend_Db_Select($db);
        $select_page = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitepage_index_view')
                ->limit(1);
        $page = $select_page->query()->fetchAll();
        if (!empty($page)) {
          $page_id = $page[0]['page_id'];
          //INSERT THE PAGE PROFILE PHOTO WIDGET IN ADMIN CONTENT TABLE    
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitepage_admincontent')
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitepage.photos-sitepage')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitepage_admincontent', 'admincontent_id')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['admincontent_id'];
              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_sitepage_admincontent')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('type = ?', 'container')
                      ->where('name = ?', 'middle')
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if (!empty($middle)) {
                $middle_id = $middle[0]['admincontent_id'];
                $select = new Zend_Db_Select($db);
                $select_tab = $select
                        ->from('engine4_sitepage_admincontent')
                        ->where('type = ?', 'widget')
                        ->where('name = ?', 'core.container-tabs')
                        ->where('page_id = ?', $page_id)
                        ->limit(1);
                $tab = $select_tab->query()->fetchAll();
                $tab_id = 0;
                if (!empty($tab)) {
                  $tab_id = $tab[0]['admincontent_id'];
                } else {
                  $tab_id = $middle_id;
                }

                $db->insert('engine4_sitepage_admincontent', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepage.photos-sitepage',
                    'parent_content_id' => $tab_id,
                    'order' => 110,
                    'params' => '{"title":"Photos","titleCount":"true"}',
                ));
              }
            }
          }
        }

        //INSERT THE RANDOM ALBUM WIDGET IN ADMIN CONTENT TABLE
        $select = new Zend_Db_Select($db);
        $select_content = $select
                ->from('engine4_sitepage_admincontent')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'sitepage.albums-sitepage')
                ->limit(1);
        $content = $select_content->query()->fetchAll();
        if (empty($content)) {
          $select = new Zend_Db_Select($db);
          $select_container = $select
                  ->from('engine4_sitepage_admincontent', 'admincontent_id')
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'container')
                  ->limit(1);
          $container = $select_container->query()->fetchAll();
          if (!empty($container)) {
            $container_id = $container[0]['admincontent_id'];
            $select = new Zend_Db_Select($db);
            $select_left = $select
                    ->from('engine4_sitepage_admincontent')
                    ->where('parent_content_id = ?', $container_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'left')
                    ->limit(1);
            $left = $select_left->query()->fetchAll();
            if (!empty($left)) {
              $left_id = $left[0]['admincontent_id'];
              $db->insert('engine4_sitepage_admincontent', array(
                  'page_id' => $page_id,
                  'type' => 'widget',
                  'name' => 'sitepage.albums-sitepage',
                  'parent_content_id' => $left_id,
                  'order' => 25,
                  'params' => '{"title":"Albums","titleCount":""}',
              ));
            }
          }
        }

        //INSERT THE PHOTO STRIP WIDGET IN ADMIN CONTENT TABLE 
        $select = new Zend_Db_Select($db);
        $select_content = $select
                ->from('engine4_sitepage_admincontent')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'sitepage.photorecent-sitepage')
                ->limit(1);
        $content = $select_content->query()->fetchAll();
        if (empty($content)) {
          $select = new Zend_Db_Select($db);
          $select_container = $select
                  ->from('engine4_sitepage_admincontent', 'admincontent_id')
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'container')
                  ->limit(1);
          $container = $select_container->query()->fetchAll();
          if (!empty($container)) {
            $container_id = $container[0]['admincontent_id'];
            $select = new Zend_Db_Select($db);
            $select_middle = $select
                    ->from('engine4_sitepage_admincontent')
                    ->where('parent_content_id = ?', $container_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->limit(1);
            $middle = $select_middle->query()->fetchAll();
            if (!empty($middle)) {
              $middle_id = $middle[0]['admincontent_id'];
              $db->insert('engine4_sitepage_admincontent', array(
                  'page_id' => $page_id,
                  'type' => 'widget',
                  'name' => 'sitepage.photorecent-sitepage',
                  'parent_content_id' => $middle_id,
                  'order' => 5,
                  'params' => '{"title":"","titleCount":""}',
              ));
            }
          }
        }
      }
      parent::onPreInstall();
    } elseif (!empty($check_sitepage) && empty($sitepage_is_active)) {
      $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
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

      return $this->_error("<span style='color:red'>Note: You have installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> but not activated it on your site yet. Please activate it first before installing the Directory / Pages - Photo Albums Extension.</span><br/><a href='" . 'http://' . $core_final_url . "admin/sitepage/settings/readme'>Click here</a> to activate the Directory / Pages Plugin.");
    }
    else {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: You have not installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> on your site yet. Please install it first before installing the <a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-photo-albums' target='_blank'>Directory / Pages - Photo Albums Extension</a>.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
    }
  }

  function onInstall() {

    $db = $this->getDb();
    
    $db->query('UPDATE  `engine4_activity_notificationtypes` SET  `body` =  \'{item:$subject} added {var:$count} photo(s) to the album {item:$object}.\' WHERE  `engine4_activity_notificationtypes`.`type` =  "sitepagealbum_create";');

    //$db->query("UPDATE `engine4_core_menuitems` SET `label` = 'Directory / Pages - Photo Albums' WHERE `engine4_core_menuitems`.`name` = 'core_admin_main_plugins_sitepagealbum' LIMIT 1");

		$column_exist_featured = $db->query('SHOW COLUMNS FROM engine4_sitepage_photos LIKE \'featured\'')->fetch();
		if (empty($column_exist_featured)) {
			$db->query("ALTER TABLE `engine4_sitepage_photos` ADD `featured` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `photo_hide`");
		}

		$column_exist_album_featured = $db->query('SHOW COLUMNS FROM engine4_sitepage_albums LIKE \'featured\'')->fetch();
		if (empty($column_exist_album_featured)) {
			$db->query("ALTER TABLE `engine4_sitepage_albums` ADD `featured` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `type` ");
		}

    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sitepagealbum')
            ->where('version < ?', '4.2.1');
    $is_enabled = $select->query()->fetchObject();
		if (!empty($is_enabled)) {
			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagealbum_admin_main_photo_featured", "sitepagealbum", "Featured Photos", "", \'{"route":"admin_default","module":"sitepagealbum","controller":"settings", "action": "featured"}\', "sitepagealbum_admin_main", "", 1, 0, 4)');

			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagealbum_admin_submain_general_tab", "sitepagealbum", "General Settings", "", \'{"route":"admin_default","module":"sitepagealbum","controller":"widgets", "action": "index"}\', "sitepagealbum_admin_submain", "", 1, 0, 1)');

			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagealbum_admin_submain_album_tab", "sitepagealbum", "Tabbed Albums Widget", "", \'{"route":"admin_default","module":"sitepagealbum","controller":"album", "action": "index"}\', "sitepagealbum_admin_submain", "", 1, 0, 2)');


			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagealbum_admin_submain_photo_tab", "sitepagealbum", "Tabbed Photos Widget", "", \'{"route":"admin_default","module":"sitepagealbum","controller":"photo", "action": "index"}\', "sitepagealbum_admin_submain", "", 1, 0, 3)');

			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagealbum_admin_submain_dayitems", "sitepagealbum", "Album of the Day", "", \'{"route":"admin_default","module":"sitepagealbum","controller":"album", "action": "manage-day-items"}\', "sitepagealbum_admin_submain", "", 1, 0, 4)');

			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagealbum_admin_submain_photo_items", "sitepagealbum", "Photo of the Day", "", \'{"route":"admin_default","module":"sitepagealbum","controller":"photo", "action": "photo-of-day"}\', "sitepagealbum_admin_submain", "", 1, 0, 5)');

      $select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitepage_album_home')
							->limit(1);
			$info = $select->query()->fetch();
			if (empty($info)) {
				$db->insert('engine4_core_pages', array(
      'name' => 'sitepage_album_home',
      'displayname' => 'Page Albums Home',
      'title' => 'Page Albums Home',
      'description' => 'This is page album home page.',
					'custom' => 1
			));
			$page_id = $db->lastInsertId('engine4_core_pages');

			// containers
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
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
					'name' => 'left',
					'parent_content_id' => $container_id,
					'order' => 4,
					'params' => '',
			));
			$left_id = $db->lastInsertId('engine4_core_content');

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
					'params' => '',
			));
			$top_id = $db->lastInsertId('engine4_core_content');

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 6,
					'params' => '',
			));
			$top_middle_id = $db->lastInsertId('engine4_core_content');

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 6,
					'params' => '',
			));
			$middle_id = $db->lastInsertId('engine4_core_content');

		// Top Middle
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepage.browsenevigation-sitepage',
					'parent_content_id' => $top_middle_id,
					'order' => 3,
					'params' => '',
			));
		// Left
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.photo-of-the-day',
					'parent_content_id' => $left_id,
					'order' => 8,
					'params' => '{"title":"Photo of the Day"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.featured-photos',
					'parent_content_id' => $left_id,
					'order' => 9,
					'params' => '{"title":"Featured Photos","titleCount":"true"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepage.popularphotos-sitepage',
					'parent_content_id' => $left_id,
					'order' => 10,
					'params' => '{"title":"Most Popular Photos","titleCount":"true"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.homephotolike-sitepage',
					'parent_content_id' => $left_id,
					'order' => 11,
					'params' => '{"title":"Most Liked Photos","titleCount":"true"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.homephotocomment-sitepage',
					'parent_content_id' => $left_id,
					'order' => 12,
					'params' => '{"title":"Most Commented Photos","titleCount":"true"}',
			));

			// Middle
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.featured-albums-slideshow',
					'parent_content_id' => $middle_id,
					'order' => 13,
					'params' => '{"title":"Featured Albums","titleCount":"true"}',
			));

		// Middele
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.featured-photos-carousel',
					'parent_content_id' => $middle_id,
					'order' => 14,
					'params' => '{"title":"Featured Photos","vertical":"0", "noOfRow":"2","inOneRow":"3","interval":"250","name":"sitepagealbum.featured-photos-carousel"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.list-photos-tabs-view',
					'parent_content_id' => $middle_id,
					'order' => 15,
					'params' => '{"title":"Photos","margin_photo":"12"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.list-albums-tabs-view',
					'parent_content_id' => $middle_id,
					'order' => 16,
					'params' => '{"title":"Albums","margin_photo":"12"}',
			));
			// Right Side
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.sitepagealbumlist-link',
					'parent_content_id' => $right_id,
					'order' => 18,
					'params' => '',
			));

			// Right Side
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.search-sitepagealbum',
					'parent_content_id' => $right_id,
					'order' => 17,
					'params' => '',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.album-of-the-day',
					'parent_content_id' => $right_id,
					'order' => 19,
					'params' => '{"title":"Album of the Day"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.featured-albums',
					'parent_content_id' => $right_id,
					'order' => 20,
					'params' => '{"title":"Featured Albums","itemCountPerPage":4}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.list-popular-albums',
					'parent_content_id' => $right_id,
					'order' => 21,
					'params' => '{"title":"Most Liked Albums","itemCountPerPage":"4","popularType":"like","name":"sitepagealbum.list-popular-albums"}',
			));
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagealbum.list-popular-albums',
					'parent_content_id' => $right_id,
					'order' => 22,
					'params' => '{"title":"Popular Albums","itemCountPerPage":"4","popularType":"view","name":"sitepagealbum.list-popular-albums"}',
			));
      }
      $db->update('engine4_core_pages', array('displayname' => 'Browse Page Albums'), array('displayname = ?' => 'Page Albums')); 
    }
    //REMOVED WIDGET SETTING TAB FROM ADMIN PANEL
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sitepagealbum')
            ->where('version <= ?', '4.1.7p2');
    $is_enabled = $select->query()->fetchObject();
    if (!empty($is_enabled)) {
      $widget_names = array('album', 'mostliked', 'mostcommented', 'mostrecent', 'homerecentphotos', 'mostpopularphotos');

      foreach ($widget_names as $widget_name) {

        $widget_type = $widget_name;
        if ($widget_type == 'album') {
          $setting_name = 'sitepage.' . $widget_type;
          $widget_name = 'sitepage.' . 'photos' . '-sitepage';
        } elseif ($widget_type == 'homerecentphotos') {
          $setting_name = 'sitepage.' . $widget_type . '.widgets';
          $widget_name = 'sitepage.' . 'mostrecentphotos' . '-sitepage';
        } elseif ($widget_type == 'mostpopularphotos') {
          $setting_name = 'sitepage.' . $widget_type . '.widgets';
          $widget_name = 'sitepage.' . 'popularphotos' . '-sitepage';
        } elseif ($widget_type == 'mostrecent') {
          $widget_name = 'sitepage.' . 'photorecent' . '-sitepage';
          $setting_name = 'sitepage.' . $widget_type . '.photos';
        } elseif ($widget_type == 'mostliked') {
          $widget_name = 'sitepage.' . 'photolike' . '-sitepage';
          $setting_name = 'sitepage.' . $widget_type . '.photos';
        } elseif ($widget_type == 'mostcommented') {
          $widget_name = 'sitepage.' . 'photocomment' . '-sitepage';
          $setting_name = 'sitepage.' . $widget_type . '.photos';
        }

        $total_items = $db->select()
                ->from('engine4_core_settings', array('value'))
                ->where('name = ?', $setting_name)
                ->limit(1)
                ->query()
                ->fetchColumn();
        if ($setting_name == 'sitepage.' . 'album') {
          $total_photos = $db->select()
                  ->from('engine4_core_settings', array('value'))
                  ->where('name = ?', 'sitepage.' . 'photo')
                  ->limit(1)
                  ->query()
                  ->fetchColumn();
        }

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
            if ($widget_name == 'sitepage.' . 'photos' . '-sitepage') {
              $params = $explode_params[0] . ',"itemCount":"' . $total_items . '","itemCount_photo":"' . $total_photos . '"}';
            } else {
              $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';
            }
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
            if ($widget_name == 'sitepage.' . 'photos' . '-sitepage') {
              $params = $explode_params[0] . ',"itemCount":"' . $total_items . '","itemCount_photo":"' . $total_photos . '"}';
            } else {
              $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';
            }

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
            if ($widget_name == 'sitepage.' . 'photos' . '-sitepage') {
              $params = $explode_params[0] . ',"itemCount":"' . $total_items . '","itemCount_photo":"' . $total_photos . '"}';
            } else {
              $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';
            }

            $db->update('engine4_sitepage_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
          }
        }
      }
    }

//    //START CODE FOR LIGHTBOX
//    //HERE WE CHECKING THAT SITEPAGEALBUM ENTRY EXIST IN THE CORE MODULE TABLE OR NOT
//    $select = new Zend_Db_Select($db);
//    $select
//            ->from('engine4_core_modules', array('version'))
//            ->where("name =?", "sitepagealbum");
//    $sitepagealbumVersion = $select->query()->fetchAll();
//
//    //IF NOT EXIST THEN WE INSERTING THE LIGHTBOX SHOULD BE DISPLAY OR NOT
//    if (empty($sitepagealbumVersion)) {
//      $value = '';
//      $select = new Zend_Db_Select($db);
//      $value = $select
//              ->from('engine4_core_settings', array('value'))
//              ->where("name =?", "socialengineaddon.display.lightbox")
//              ->query()
//              ->fetchColumn();
//
//      //IF LIGHTBOX IS NOT DISPLAY THEN WE WILL INSERTING THE ACTIVITY FEED VALUE
//      if (empty($value)) {
//        $select = new Zend_Db_Select($db);
//        $select
//                ->from('engine4_core_settings', array('name'))
//                ->where("name Like ?", "%socialengineaddon.lightbox.option.display%");
//        $name = $select->query()->fetchAll();
//
//        $count = count($name);
//
//        $select = new Zend_Db_Select($db);
//        $select
//                ->from('engine4_core_settings', array('name'))
//                ->where("value =?", "activity");
//        $name = $select->query()->fetchColumn();
//        if (empty($name)) {
//          $name = 'socialengineaddon.lightbox.option.display.' . ++$count;
//          $db->insert('engine4_core_settings', array(
//              'name' => $name,
//              'value' => 'activity'
//          ));
//        }
//
//        $select = new Zend_Db_Select($db);
//        $select
//                ->from('engine4_core_settings', array('name'))
//                ->where("value =?", "sitepagealbum");
//        $name = $select->query()->fetchColumn();
//        if (empty($name)) {
//          $name = 'socialengineaddon.lightbox.option.display.' . ++$count;
//          $db->insert('engine4_core_settings', array(
//              'name' => $name,
//              'value' => 'sitepagealbum'
//          ));
//        }       
//      }          
//    }
//    //END CODE FOR LIGHTBOX

    //START THE WORK FOR MAKE WIDGETIZE PAGE OF ALBUMS LISTING AND ALBUM VIEW PAGE
    $select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_modules')
						->where('name = ?', 'sitepagealbum')
						->where('version < ?', '4.2.0');
		$is_enabled = $select->query()->fetchObject();
    if(!empty($is_enabled)) {
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_modules')
							->where('name = ?', 'communityad')
							->where('enabled 	 = ?', 1)
							->limit(1);
				;
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
							->where('name = ?', 'sitepage_album_albumlist')
							->limit(1);
			;
			$info = $select->query()->fetch();

      $select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitepage_album_browse')
							->limit(1);
			;
			$info_browse = $select->query()->fetch();

			if ( empty($info) && empty($info_browse) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitepage_album_browse',
						'displayname' => 'Browse Page Albums',
						'title' => 'Page Albums',
						'description' => 'This is the page albums.',
						'custom' => 1,
				));
				$page_id = $db->lastInsertId('engine4_core_pages');

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

				//INSERT MAIN - MIDDLE CONTAINER
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'container',
						'name' => 'middle',
						'parent_content_id' => $container_id,
						'order' => 6,
						'params' => '',
				));
				$middle_id = $db->lastInsertId('engine4_core_content');


				//INSERT MAIN - RIGHT CONTAINER
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'container',
						'name' => 'right',
						'parent_content_id' => $container_id,
						'order' => 5,
						'params' => '',
				));
				$right_id = $db->lastInsertId('engine4_core_content');


				//INSERT TOP CONTAINER
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'container',
						'name' => 'top',
						'parent_content_id' => Null,
						'order' => 1,
						'params' => '',
				));
				$top_id = $db->lastInsertId('engine4_core_content');


				//INSERT TOP- MIDDLE CONTAINER
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'container',
						'name' => 'middle',
						'parent_content_id' => $top_id,
						'order' => 6,
						'params' => '',
				));
				$top_middle_id = $db->lastInsertId('engine4_core_content');


				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepage.browsenevigation-sitepage',
						'parent_content_id' => $top_middle_id,
						'order' => 1,
						'params' => '{"title":"","titleCount":""}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagealbum.search-sitepagealbum',
						'parent_content_id' => $right_id,
						'order' => 3,
						'params' => '{"title":"","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagealbum.sitepage-album',
						'parent_content_id' => $middle_id,
						'order' => 2,
						'params' => '{"title":"","titleCount":""}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepage.mostrecentphotos-sitepage',
						'parent_content_id' => $right_id,
						'order' => 4,
						'params' => '{"title":"Recent Photos","titleCount":"true"}',
				));

			$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepage.popularphotos-sitepage',
						'parent_content_id' => $right_id,
						'order' => 5,
						'params' => '{"title":"Most Popular Photos","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagealbum.sitepage-sponsoredalbum',
						'parent_content_id' => $right_id,
						'order' => 6,
						'params' => '{"title":"Sponsored Albums","titleCount":"true"}',
				));

				if ( $infomation && $rowinfo ) {
					$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepage.page-ads',
							'parent_content_id' => $right_id,
							'order' => 7,
							'params' => '{"title":"","titleCount":""}',
					));
				}
			}
      else {
        $db->update('engine4_core_pages', array('name' => 'sitepage_album_browse'), array('name = ?' => 'sitepage_album_albumlist'));
      }

			$db = $this->getDb();
			$select = new Zend_Db_Select($db);

			// Check if it's already been placed
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitepage_album_view')
							->limit(1);
			;
			$info = $select->query()->fetch();

			if ( empty($info) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitepage_album_view',
						'displayname' => 'Page Album View Page',
						'title' => 'View Page Album',
						'description' => 'This is the view page for a page album.',
						'custom' => 1,
						'provides' => 'subject=sitepagealbum',
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
						'order' => 1,
						'params' => '',
				));
				$right_id = $db->lastInsertId('engine4_core_content');

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'container',
						'name' => 'middle',
						'parent_content_id' => $container_id,
						'order' => 3,
						'params' => '',
				));
				$middle_id = $db->lastInsertId('engine4_core_content');

				// middle column content
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagealbum.album-content',
						'parent_content_id' => $middle_id,
						'order' => 1,
						'params' => '',
				));

				if ( $infomation && $rowinfo ) {
					$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepage.page-ads',
							'parent_content_id' => $right_id,
							'order' => 1,
							'params' => '{"title":"","titleCount":""}',
					));
				}

			}

			$select = new Zend_Db_Select($db);
			$select
			->from('engine4_core_modules')
			->where('name = ?', 'mobi')
			->where('enabled 	 = ?', 1)
			->limit(1);
			;  

			$infomation = $select->query()->fetch();
			if(!empty($infomation)) {
				$select = new Zend_Db_Select($db);
				$select
				->from('engine4_core_pages')
				->where('name = ?', 'sitepagealbum_mobi_view')
				->limit(1);
				;
				$info = $select->query()->fetch();
				if (empty($info)) {
					$db->insert('engine4_core_pages', array(
								'name' => 'sitepagealbum_mobi_view',
								'displayname' => 'Mobile Page Album Profile',
								'title' => 'Mobile Page Album Profile',
								'description' => 'This is the mobile verison of a Page album profile page.',
								'custom' => 0,
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
								'order' => 1,
								'params' => '',
					));
					$right_id = $db->lastInsertId('engine4_core_content');

					$db->insert('engine4_core_content', array(
								'page_id' => $page_id,
								'type' => 'container',
								'name' => 'middle',
								'parent_content_id' => $container_id,
								'order' => 3,
								'params' => '',
					));
					$middle_id = $db->lastInsertId('engine4_core_content');

					// middle column content
					$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepagealbum.album-content',
							'parent_content_id' => $middle_id,
							'order' => 1,
							'params' => '',
					));
				}
			}
    }
    //END THE WORK FOR MAKE WIDGETIZE PAGE OF ALBUMS LISTING AND ALBUM VIEW PAGE
    $select = new Zend_Db_Select($db);
    $select
          ->from('engine4_core_settings')
          ->where('name = ?', 'sitepage.feed.type');
		$info = $select->query()->fetch();
    $enable = 1;
    if (!empty($info))
      $enable = $info['value'];
    $db->query('INSERT IGNORE INTO  `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES("sitepagealbum_admin_photo_new", "sitepagealbum", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title}:", '.$enable.', 6, 2, 1, 1, 1, 1)');


		$select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_modules')
					->where('name = ?', 'sitemobile')
					->where('enabled = ?', 1);
		$is_sitemobile_object = $select->query()->fetchObject();
		if($is_sitemobile_object)  {
				include APPLICATION_PATH . "/application/modules/Sitepagealbum/controllers/license/mobileLayoutCreation.php";
		}

    $albumTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_albums\'')->fetch();
    if(!empty($albumTable)) {     
        
        $featuredColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_albums LIKE 'featured'")->fetch();        
        if(!empty($featuredColumn)) {
            $featuredIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_albums` WHERE Key_name = 'featured'")->fetch();   
            if(empty($featuredIndex)) {
              $db->query("ALTER TABLE `engine4_sitepage_albums` ADD INDEX ( `featured` )");
            }         
        }
    }
    
    $photoTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_photos\'')->fetch();
    if(!empty($photoTable)) {     
        
        $featuredColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_photos LIKE 'featured'")->fetch();        
        if(!empty($featuredColumn)) {
            $featuredIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_photos` WHERE Key_name = 'featured'")->fetch();   
            if(empty($featuredIndex)) {
              $db->query("ALTER TABLE `engine4_sitepage_photos` ADD INDEX ( `featured` )");
            }         
        }
    }    
    
    parent::onInstall();
  }

  public function onPostInstall() {

    $db = $this->getDb();
		$select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemobile')
            ->where('enabled = ?', 1);
    $is_sitemobile_object = $select->query()->fetchObject();
    if(!empty($is_sitemobile_object)) {
			$db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('sitepagealbum','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', 'sitepagealbum')
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitepagealbum/integrated/0/redirect/install');
				} 
      }
    }
    
    //Work for the word changes in the page plugin .csv file.
    $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
    $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
    if($controllerName == 'manage' && ($actionName == 'install' || $actionName == 'query')) {
      $view = new Zend_View();
      $baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
      $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
      if ($actionName == 'install') {
        $redirector->gotoUrl($baseUrl . 'admin/sitepage/settings/language/redirect/install');
      } else {
        $redirector->gotoUrl($baseUrl . 'admin/sitepage/settings/language/redirect/query');
      }
    }
    
  }

}

?>
