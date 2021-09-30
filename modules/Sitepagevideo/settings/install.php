<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {
    $db = $this->getDb();

    //CHECK THAT SITEPAGE PLUGIN IS ACTIVATED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.is.active')
            ->limit(1);
    $sitepage_settings = $select->query()->fetchAll();
    if ( !empty($sitepage_settings) ) {
      $sitepage_is_active = $sitepage_settings[0]['value'];
    }
    else {
      $sitepage_is_active = 0;
    }

    //CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('enabled = ?', 1);
    $check_sitepage = $select->query()->fetchObject();
    if ( !empty($check_sitepage) && !empty($sitepage_is_active) ) {
      $PRODUCT_TYPE = 'sitepagevideo';
      $PLUGIN_TITLE = 'Sitepagevideo';
      $PLUGIN_VERSION = '4.10.3';
      $PLUGIN_CATEGORY = 'plugin';
      $PRODUCT_DESCRIPTION = 'Sitepagevideo Plugin';
      $PRODUCT_TITLE = 'Directory / Pages - Videos Extension';
      $_PRODUCT_FINAL_FILE = 0;
      $sitepage_plugin_version = '4.8.6';
      $SocialEngineAddOns_version = '4.10.3p6';
      $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
      $is_file = file_exists($file_path);
      if ( empty($is_file) ) {
        include APPLICATION_PATH . "/application/modules/Sitepage/controllers/license/license4.php";
      }
      else {
        include $file_path;
      }

      $this->_checkFfmpegPath();

      $pageTime = time();
      $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
			('sitepagevideo.basetime', $pageTime ),
			('sitepagevideo.isvar', 0 ),
			('sitepagevideo.filepath', 'Sitepagevideo/controllers/license/license2.php');");

      //PUT SITEPAGE VIDEO WIDGET IN ADMIN CONTENT TABLE
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', 'sitepagevideo')
              ->where('version <= ?', '4.1.5p1');
      $is_enabled = $select->query()->fetchObject();
      if ( !empty($is_enabled) ) {
        $select = new Zend_Db_Select($db);
        $select_page = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitepage_index_view')
                ->limit(1);
        $page = $select_page->query()->fetchAll();
        if ( !empty($page) ) {
          $page_id = $page[0]['page_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitepage_admincontent')
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitepagevideo.profile-sitepagevideos')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if ( empty($content) ) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitepage_admincontent', 'admincontent_id')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if ( !empty($container) ) {
              $container_id = $container[0]['admincontent_id'];
              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_sitepage_admincontent')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('type = ?', 'container')
                      ->where('name = ?', 'middle')
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if ( !empty($middle) ) {
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
                if ( !empty($tab) ) {
                  $tab_id = $tab[0]['admincontent_id'];
                }
                else {
                  $tab_id = $middle_id;
                }
                $db->insert('engine4_sitepage_admincontent', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepagevideo.profile-sitepagevideos',
                    'parent_content_id' => $tab_id,
                    'order' => 111,
                    'params' => '{"title":"Videos","titleCount":"true"}',
                ));
              }
            }
          }
        }
      }
      parent::onPreInstall();
    }
    elseif ( !empty($check_sitepage) && empty($sitepage_is_active) ) {
      $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      if ( strstr($url_string, "manage/install") ) {
        $calling_from = 'install';
      }
      else if ( strstr($url_string, "manage/query") ) {
        $calling_from = 'queary';
      }
      $explode_base_url = explode("/", $baseUrl);
      foreach ( $explode_base_url as $url_key ) {
        if ( $url_key != 'install' ) {
          $core_final_url .= $url_key . '/';
        }
      }

      return $this->_error("<span style='color:red'>Note: You have installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> but not activated it on your site yet. Please activate it first before installing the Directory / Pages - Videos Extension.</span><br/> <a href='" . 'http://' . $core_final_url . "admin/sitepage/settings/readme'>Click here</a> to activate the Directory / Pages Plugin.");
    }
    else {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: You have not installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> on your site yet. Please install it first before installing the <a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-videos' target='_blank'>Directory / Pages - Videos Extension</a>.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
    }
  }

  protected function _checkFfmpegPath() {

    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    //CHECK FFMPEG PATH FOR CORRECTNESS
    if ( function_exists('exec') && function_exists('shell_exec') ) {

      //API IS NOT AVAILABLE
      //$ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
      $ffmpeg_path = $db->select()
              ->from('engine4_core_settings', 'value')
              ->where('name = ?', 'sitepagevideo.ffmpeg.path')
              ->limit(1)
              ->query()
              ->fetchColumn(0);

      $output = null;
      $return = null;
      if ( !empty($ffmpeg_path) ) {
        exec($ffmpeg_path . ' -version', $output, $return);
      }

      //TRY TO AUTO-GUESS FFMPEG PATH IF IT IS NOT SET CORRECTLY
      $ffmpeg_path_original = $ffmpeg_path;
      if ( empty($ffmpeg_path) || $return > 0 || stripos(join('', $output), 'ffmpeg') === false ) {
        $ffmpeg_path = null;

        //WINDOWS
        if ( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ) {
          // @todo
        }
        //NOT WINDOWS
        else {
          $output = null;
          $return = null;
          @exec('which ffmpeg', $output, $return);
          if ( 0 == $return ) {
            $ffmpeg_path = array_shift($output);
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version', $output, $return);
            if ( 0 == $return ) {
              $ffmpeg_path = null;
            }
          }
        }
      }
      if ( $ffmpeg_path != $ffmpeg_path_original ) {
        $count = $db->update('engine4_core_settings', array(
            'value' => $ffmpeg_path,
                ), array(
            'name = ?' => 'sitepagevideo.ffmpeg.path',
                ));
        if ( $count === 0 ) {
          try {
            $db->insert('engine4_core_settings', array(
                'value' => $ffmpeg_path,
                'name' => 'sitepagevideo.ffmpeg.path',
            ));
          }
          catch ( Exception $e ) {
            
          }
        }
      }
    }
  }

  function onInstall() {

    $db = $this->getDb();

    $db->query('UPDATE  `engine4_activity_notificationtypes` SET  `body` =  \'{item:$subject} has created a page video {item:$object}.\' WHERE  `engine4_activity_notificationtypes`.`type` =  "sitepagevideo_create";');
    
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepagevideo_videos'")->fetch();
    if ( !empty($table_exist) ) {
      //DROP THE COLUMN FROM THE "engine4_sitepagevideo_videos" TABLE
      $pageOwnerIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagevideo_videos LIKE 'page_owner_id'")->fetch();
      if ( !empty($pageOwnerIdColumn) ) {
        $db->query("ALTER TABLE `engine4_sitepagevideo_videos` DROP `page_owner_id`");
      }

      //DROP THE COLUMN FROM THE "engine4_sitepagevideo_videos" TABLE
      $ownerTypeColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagevideo_videos LIKE 'owner_type'")->fetch();
      if ( !empty($ownerTypeColumn) ) {
        $db->query("ALTER TABLE `engine4_sitepagevideo_videos` DROP `owner_type`");
      }

      //DROP THE INDEX FROM THE "engine4_sitepagevideo_videos" TABLE
      $creationDateColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagevideo_videos` WHERE Key_name = 'creation_date'")->fetch();

      if ( !empty($creationDateColumnIndex) ) {
        $db->query("ALTER TABLE `engine4_sitepagevideo_videos` DROP INDEX `creation_date`");
      }

      //DROP THE INDEX FROM THE "engine4_sitepagevideo_videos" TABLE
      $viewCountColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagevideo_videos` WHERE Key_name = 'view_count'")->fetch();

      if ( !empty($viewCountColumnIndex) ) {
        $db->query("ALTER TABLE `engine4_sitepagevideo_videos` DROP INDEX `view_count`");
      }

      //ADD THE INDEX FROM THE "engine4_sitepagevideo_videos" TABLE
      $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagevideo_videos` WHERE Key_name = 'page_id'")->fetch();

      if ( empty($pageIdColumnIndex) ) {
        $db->query("ALTER TABLE `engine4_sitepagevideo_videos` ADD INDEX ( `page_id` );");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepagevideo_ratings'")->fetch();
    if ( !empty($table_exist) ) {
      //DROP THE INDEX FROM THE "engine4_sitepagevideo_ratings" TABLE
      $indexColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagevideo_ratings` WHERE Key_name = 'INDEX'")->fetch();

      if ( !empty($indexColumnIndex) ) {
        $db->query("ALTER TABLE `engine4_sitepagevideo_ratings` DROP INDEX `INDEX`");
      }

      //ADD THE INDEX FROM THE "engine4_sitepagevideo_ratings" TABLE
      $videoIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagevideo_ratings` WHERE Key_name = 'video_id'")->fetch();

      if ( empty($videoIdColumnIndex) ) {
        $db->query("ALTER TABLE `engine4_sitepagevideo_ratings` ADD INDEX ( `video_id` );");
      }

      //DROP THE INDEX FROM THE "engine4_sitepagevideo_ratings" TABLE
      $pageIdRatingColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagevideo_ratings` WHERE Key_name = 'page_id'")->fetch();

      if ( !empty($pageIdRatingColumnIndex) ) {
        $db->query("ALTER TABLE `engine4_sitepagevideo_ratings` DROP INDEX `page_id`");
      }
    }

    //REMOVED WIDGET SETTING TAB FROM ADMIN PANEL
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sitepagevideo')
            ->where('version <= ?', '4.1.7');
    $is_enabled = $select->query()->fetchObject();
    if ( !empty($is_enabled) ) {
      $widget_names = array('comment', 'recent', 'like', 'view', 'rate', 'featurelist', 'homerecent');

      foreach ( $widget_names as $widget_name ) {

        $widget_type = $widget_name;

        $widget_name = 'sitepagevideo.' . $widget_name . '-sitepagevideos';

        if ( $widget_name == 'sitepagevideo.featurelist-sitepagerevideos' ) {
          $setting_name = 'sitepagevideo.featured.widgets';
        }
        elseif ( $widget_name == 'sitepagevideo.homerecent-sitepagerevideos' ) {
          $setting_name = 'sitepagevideo.homerecentvideos.widgets';
        }
        else {
          $setting_name = 'sitepagevideo.' . $widget_type . '.widgets';
        }


        $total_items = $db->select()
                ->from('engine4_core_settings', array('value'))
                ->where('name = ?', $setting_name)
                ->limit(1)
                ->query()
                ->fetchColumn();

        if ( empty($total_items) ) {
          $total_items = 3;
        }

        //WORK FOR CORE CONTENT PAGES
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
        $widgets = $select->query()->fetchAll();
        foreach ( $widgets as $widget ) {
          $explode_params = explode('}', $widget['params']);
          if ( !empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"') ) {
            $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';

            $db->update('engine4_core_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
          }
        }

        //WORK FOR ADMIN USER CONTENT PAGE
        $select = new Zend_Db_Select($db);
        $select->from('engine4_sitepage_admincontent', array('name', 'params', 'admincontent_id'))->where('name = ?', $widget_name);
        $widgets = $select->query()->fetchAll();
        foreach ( $widgets as $widget ) {
          $explode_params = explode('}', $widget['params']);
          if ( !empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"') ) {
            $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';

            $db->update('engine4_sitepage_admincontent', array('params' => $params), array('admincontent_id = ?' => $widget['admincontent_id'], 'name = ?' => $widget_name));
          }
        }

        //WORK FOR USER CONTENT PAGES
        $select = new Zend_Db_Select($db);
        $select->from('engine4_sitepage_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
        $widgets = $select->query()->fetchAll();
        foreach ( $widgets as $widget ) {
          $explode_params = explode('}', $widget['params']);
          if ( !empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"') ) {
            $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';

            $db->update('engine4_sitepage_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
          }
        }
      }
    }

    //START THE WORK FOR MAKE WIDGETIZE PAGE OF VIDEO LISTING AND VIDEO VIEW PAGE
    $select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_modules')
						->where('name = ?', 'sitepagevideo')
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
							->where('name = ?', 'sitepagevideo_index_videolist')
							->limit(1);
			;
			$info = $select->query()->fetch();

      $select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitepagevideo_index_browse')
							->limit(1);
			;
			$info_browse = $select->query()->fetch();

			if ( empty($info) && empty($info_browse) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitepagevideo_index_browse',
						'displayname' => 'Browse Page Videos',
						'title' => 'Page Videos',
						'description' => 'This is the page videos.',
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
						'name' => 'sitepagevideo.search-sitepagevideo',
						'parent_content_id' => $right_id,
						'order' => 3,
						'params' => '{"title":"","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.sitepage-video',
						'parent_content_id' => $middle_id,
						'order' => 2,
						'params' => '{"title":"","titleCount":""}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.homefeaturelist-sitepagevideos',
						'parent_content_id' => $right_id,
						'order' => 4,
						'params' => '{"title":"Featured Videos","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.sitepage-sponsoredvideo',
						'parent_content_id' => $right_id,
						'order' => 5,
						'params' => '{"title":"Sponsored Videos","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.homecomment-sitepagevideos',
						'parent_content_id' => $right_id,
						'order' => 6,
						'params' => '{"title":"Most Commented Videos","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.homeview-sitepagevideos',
						'parent_content_id' => $right_id,
						'order' => 7,
						'params' => '{"title":"Most Viewed Videos","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.homelike-sitepagevideos',
						'parent_content_id' => $right_id,
						'order' => 8,
						'params' => '{"title":"Most Liked Videos","titleCount":"true"}',
				));


				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.homerate-sitepagevideos',
						'parent_content_id' => $right_id,
						'order' => 9,
						'params' => '{"title":"Top Rated Videos","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepagevideo.homerecent-sitepagevideos',
							'parent_content_id' => $right_id,
							'order' => 10,
							'params' => '{"title":"Recent Videos","titleCount":"true"}',
					));

				if ( !empty($infomation)  && !empty($rowinfo) ) {
					$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepage.page-ads',
							'parent_content_id' => $right_id,
							'order' => 11,
							'params' => '{"title":"","titleCount":""}',
					));
				}
			}
      else {
        $db->update('engine4_core_pages', array('name' => 'sitepagevideo_index_browse'), array('name = ?' => 'sitepagevideo_index_videolist'));
      }

		 $db = $this->getDb();
   
			$select = new Zend_Db_Select($db);

			// Check if it's already been placed
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitepagevideo_index_view')
							->limit(1);
			;
			$info = $select->query()->fetch();

			if ( empty($info) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitepagevideo_index_view',
						'displayname' => 'Page Video View Page',
						'title' => 'View Page Video',
						'description' => 'This is the view page for a page video.',
						'custom' => 1,
						'provides' => 'subject=sitepagevideo',
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
						'name' => 'sitepagevideo.video-content',
						'parent_content_id' => $middle_id,
						'order' => 1,
						'params' => '',
				));

				// right column
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.show-same-tags',
						'parent_content_id' => $right_id,
						'order' => 1,
						'params' => '{"title":"Similar Videos"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.show-also-liked',
						'parent_content_id' => $right_id,
						'order' => 2,
						'params' => '{"title":"People Also Liked"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.show-same-poster',
						'parent_content_id' => $right_id,
						'order' => 3,
						'params' => '{"title":"From the same Member"}',
				));

				if ( !empty($infomation)  && !empty($rowinfo) ) {
					$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepage.page-ads',
							'parent_content_id' => $right_id,
							'order' => 4,
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
								->where('name = ?', 'sitepagevideo_mobi_view')
								->limit(1);
				;
				$info = $select->query()->fetch();
				if (empty($info)) {
					$db->insert('engine4_core_pages', array(
							'name' => 'sitepagevideo_mobi_view',
							'displayname' => 'Mobile Page Video Profile',
							'title' => 'Mobile Page Video Profile',
							'description' => 'This is the mobile verison of a Page video profile page.',
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
							'name' => 'sitepagevideo.video-content',
							'parent_content_id' => $middle_id,
							'order' => 1,
							'params' => '',
					));

					// right column
					$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepagevideo.show-same-tags',
							'parent_content_id' => $right_id,
							'order' => 1,
							'params' => '{"title":"Similar Videos"}',
					));

					$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepagevideo.show-also-liked',
							'parent_content_id' => $right_id,
							'order' => 2,
							'params' => '{"title":"People Also Liked"}',
					));

					$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepagevideo.show-same-poster',
							'parent_content_id' => $right_id,
							'order' => 3,
							'params' => '{"title":"Other Videos From Page"}',
					));

				}
			}
    }
		$select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_modules')
					->where('name = ?', 'sitepagevideo')
					->where('version < ?', '4.2.1');
		$is_enabled_video = $select->query()->fetchObject();
		
		if($is_enabled_video) {
			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagevideo_admin_submain_general_tab", "sitepagevideo", "General Settings", "", \'{"route":"admin_default","module":"sitepagevideo","controller":"widgets", "action": "index"}\', "sitepagevideo_admin_submain", "", 1, 0, 1)');

			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagevideo_admin_submain_video_tab", "sitepagevideo", "Tabbed Videos Widget", "", \'{"route":"admin_default","module":"sitepagevideo","controller":"settings", "action": "widget"}\', "sitepagevideo_admin_submain", "", 1, 0, 2)');

			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagevideo_admin_submain_dayitems", "sitepagevideo", "Video of the Day", "", \'{"route":"admin_default","module":"sitepagevideo","controller":"settings", "action": "manage-day-items"}\', "sitepagevideo_admin_submain", "", 1, 0, 4)');


			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitepagevideo_index_home')
							->limit(1);
			$info = $select->query()->fetch();
			if (empty($info)) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitepagevideo_index_home',
						'displayname' => 'Page Videos Home',
						'title' => 'Page Videos Home',
						'description' => 'This is page video home page.',
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
			
				//INSERT TOP RATED PAGE VIDEO WIDGET
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.homerate-sitepagevideos',
						'parent_content_id' => $left_id,
						'order' => 13,
						'params' => '{"title":"Top Rated Videos","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.homerecent-sitepagevideos',
						'parent_content_id' => $left_id,
						'order' => 14,
						'params' => '{"title":"Recent Videos","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.homeview-sitepagevideos',
						'parent_content_id' => $right_id,
						'order' => 20,
						'params' => '{"title":"Most Viewed Videos","titleCount":"true"}',
				));

			// Middele
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.featured-videos-carousel',
						'parent_content_id' => $middle_id,
						'order' => 16,
						'params' => '{"title":"Featured Videos","vertical":"0", "noOfRow":"2","inOneRow":"3","interval":"250","name":"sitepagevideo.featured-videos-carousel"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.list-videos-tabs-view',
						'parent_content_id' => $middle_id,
						'order' => 17,
						'params' => '{"title":"Videos","margin_photo":"12"}',
				));
				// Right Side
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.sitepagevideolist-link',
						'parent_content_id' => $right_id,
						'order' => 19,
						'params' => '',
				));

				// Right Side
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.search-sitepagevideo',
						'parent_content_id' => $right_id,
						'order' => 18,
						'params' => '',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.video-of-the-day',
						'parent_content_id' => $left_id,
						'order' => 12,
						'params' => '{"title":"Video of the Day"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.homefeaturelist-sitepagevideos',
						'parent_content_id' => $right_id,
						'order' => 21,
						'params' => '{"title":"Featured Videos","itemCountPerPage":3}',
				));


				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.homecomment-sitepagevideos',
						'parent_content_id' => $right_id,
						'order' => 22,
						'params' => '{"title":"Most Commented Videos","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagevideo.homelike-sitepagevideos',
						'parent_content_id' => $right_id,
						'order' => 23,
						'params' => '{"title":"Most Liked Videos","titleCount":"true"}',
				));
			}

			$select = new Zend_Db_Select($db);
			$select
						->from('engine4_core_content')
						->where('name = ?', 'sitepagevideo.featurelist-sitepagevideos');
						
			$is_exit = $select->query()->fetchObject();
			if(!empty($is_exit)) {
				$db->update('engine4_core_content', array(
					'name' => 'sitepagevideo.highlightlist-sitepagevideos',
					'params' => '{"title":"Highlighted Videos","titleCount":true}',
							), array(
					'name = ?' => 'sitepagevideo.featurelist-sitepagevideos',
				));
			}
			
			$select = new Zend_Db_Select($db);
			$select
						->from('engine4_sitepage_admincontent')
						->where('name = ?', 'sitepagevideo.featurelist-sitepagevideos');
						
			$is_exit_content = $select->query()->fetchObject();
			if(!empty($is_exit_content)) {
				$db->update('engine4_sitepage_admincontent', array(
					'name' => 'sitepagevideo.highlightlist-sitepagevideos',
					'params' => '{"title":"Highlighted Videos","titleCount":true}',
							), array(
					'name = ?' => 'sitepagevideo.featurelist-sitepagevideos',
				));
			}

			$select = new Zend_Db_Select($db);
			$select
						->from('engine4_sitepage_content')
						->where('name = ?', 'sitepagevideo.featurelist-sitepagevideos');
						
			$is_exit_sitepagecontent = $select->query()->fetchObject();
			if(!empty($is_exit_sitepagecontent)) {
				$db->update('engine4_sitepage_content', array(
					'name' => 'sitepagevideo.highlightlist-sitepagevideos',
					'params' => '{"title":"Highlighted Videos","titleCount":true}',
							), array(
					'name = ?' => 'sitepagevideo.featurelist-sitepagevideos',
				));
			}

			$featuredColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagevideo_videos LIKE 'featured'")->fetch();
      $highlightedColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagevideo_videos LIKE 'highlighted'")->fetch();
			if (!empty($featuredColumn) && empty($highlightedColumn)) {
				$db->query("ALTER TABLE `engine4_sitepagevideo_videos` CHANGE `featured` `highlighted` TINYINT( 1 ) NOT NULL");
			}

			$featuredColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagevideo_videos LIKE 'featured'")->fetch();
			if (empty($featuredColumn)) {
				$db->query("ALTER TABLE `engine4_sitepagevideo_videos` ADD `featured` TINYINT( 1 ) NOT NULL AFTER `highlighted`");
			}

      $db->update('engine4_core_pages', array('displayname' => 'Browse Page Videos'), array('displayname = ?' => 'Page Videos')); 
		}
	
    //END THE WORK FOR MAKE WIDGETIZE PAGE OF VIDEO LISTING AND VIDEO VIEW PAGE
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.feed.type');
    $info = $select->query()->fetch();
    $enable = 1;
    if (!empty($info)) 
     $enable = $info['value'];
      $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES("sitepagevideo_admin_new", "sitepagevideo", "{item:$object} posted a new video:",' . $enable . ', 6, 2, 1, 1, 1, 1)');
    
		$select = new Zend_Db_Select($db);
		$select
				->from('engine4_core_modules')
				->where('name = ?', 'sitemobile')
				->where('enabled = ?', 1);
		$is_sitemobile_object = $select->query()->fetchObject();
		if($is_sitemobile_object)  {
			include APPLICATION_PATH . "/application/modules/Sitepagevideo/controllers/license/mobileLayoutCreation.php";
		}
    
    $videoTable = $db->query('SHOW TABLES LIKE \'engine4_sitepagevideo_videos\'')->fetch();
    if(!empty($videoTable)) {     
        
        $featuredColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagevideo_videos LIKE 'featured'")->fetch();        
        if(!empty($featuredColumn)) {
            $featuredIndex = $db->query("SHOW INDEX FROM `engine4_sitepagevideo_videos` WHERE Key_name = 'featured'")->fetch();   
            if(empty($featuredIndex)) {
              $db->query("ALTER TABLE `engine4_sitepagevideo_videos` ADD INDEX ( `featured` )");
            }         
        }
        
        $highlightedColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagevideo_videos LIKE 'highlighted'")->fetch();        
        if(!empty($highlightedColumn)) {
            $highlightedIndex = $db->query("SHOW INDEX FROM `engine4_sitepagevideo_videos` WHERE Key_name = 'highlighted'")->fetch();   
            if(empty($highlightedIndex)) {
              $db->query("ALTER TABLE `engine4_sitepagevideo_videos` ADD INDEX ( `highlighted` )");
            }         
        }        
        
    }    
    $this->setActivityFeeds();
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
('sitepagevideo','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', 'sitepagevideo')
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitepagevideo/integrated/0/redirect/install');
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
  
  public function setActivityFeeds() {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'nestedcomment')
            ->where('enabled = ?', 1);
    $is_nestedcomment_object = $select->query()->fetchObject();
    if ($is_nestedcomment_object) {
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitepagevideo_video", "sitepagevideo", \'{item:$subject} replied to a comment on {item:$owner}\'\'s page video {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
    }
  }  

}