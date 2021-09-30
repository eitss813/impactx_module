<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WidgetSettings.php 6590 2010-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
//START LANGUAGE WORK
Engine_Api::_()->getApi('language', 'sitepage')->languageChanges();
//END LANGUAGE WORK
//GET DB
$db = Zend_Db_Table_Abstract::getDefaultAdapter();

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagealbum_admin_main_photo_featured", "sitepagealbum", "Featured Photos", "", \'{"route":"admin_default","module":"sitepagealbum","controller":"settings", "action": "featured"}\', "sitepagealbum_admin_main", "", 1, 0, 4)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagealbum_admin_submain_general_tab", "sitepagealbum", "General Settings", "", \'{"route":"admin_default","module":"sitepagealbum","controller":"widgets", "action": "index"}\', "sitepagealbum_admin_submain", "", 1, 0, 1)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagealbum_admin_submain_album_tab", "sitepagealbum", "Tabbed Albums Widget", "", \'{"route":"admin_default","module":"sitepagealbum","controller":"album", "action": "index"}\', "sitepagealbum_admin_submain", "", 1, 0, 2)');


$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagealbum_admin_submain_photo_tab", "sitepagealbum", "Tabbed Photos Widget", "", \'{"route":"admin_default","module":"sitepagealbum","controller":"photo", "action": "index"}\', "sitepagealbum_admin_submain", "", 1, 0, 3)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagealbum_admin_submain_dayitems", "sitepagealbum", "Album of the Day", "", \'{"route":"admin_default","module":"sitepagealbum","controller":"album", "action": "manage-day-items"}\', "sitepagealbum_admin_submain", "", 1, 0, 4)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagealbum_admin_submain_photo_items", "sitepagealbum", "Photo of the Day", "", \'{"route":"admin_default","module":"sitepagealbum","controller":"photo", "action": "photo-of-day"}\', "sitepagealbum_admin_submain", "", 1, 0, 5)');

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
  $select = new Zend_Db_Select($db);
  $select_page = $select
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'sitepage_index_view')
          ->limit(1);
  $page = $select_page->query()->fetchAll();
  if (!empty($page)) {
    $page_id = $page[0]['page_id'];

    //INSERTING THE PHOTO WIDGET IN SITEPAGE_ADMIN_CONTENT TABLE ALSO.
    Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepage.photos-sitepage', $page_id, 'Photos', 'true', '110');

    //INSERTING THE PHOTO WIDGET IN CORE_CONTENT TABLE ALSO.
    Engine_Api::_()->getApi('layoutcore', 'sitepage')->setContentDefaultInfo('sitepage.photos-sitepage', $page_id, 'Photos', 'true', '110');

    //INSERTING THE PHOTO WIDGET IN SITEPAGE_CONTENT TABLE ALSO.
    $select = new Zend_Db_Select($db);
    $select = $select
            ->from('engine4_sitepage_contentpages', 'contentpage_id');
    $contentpage_ids = $select->query()->fetchAll();
    foreach ($contentpage_ids as $contentpage_id) {
      if (!empty($contentpage_id)) {
        $contentpage_id = $contentpage_id['contentpage_id'];
        Engine_Api::_()->getDbtable('content', 'sitepage')->setDefaultInfo('sitepage.photos-sitepage', $contentpage_id, 'Photos', 'true', '110');

        //INSERT THE RANDOM ALBUM WIDGET
        $select = new Zend_Db_Select($db);
        $select_content = $select
                ->from('engine4_sitepage_content')
                ->where('contentpage_id = ?', $contentpage_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'sitepage.albums-sitepage')
                ->limit(1);
        $content = $select_content->query()->fetchAll();
        if (empty($content)) {
          $select = new Zend_Db_Select($db);
          $select_container = $select
                  ->from('engine4_sitepage_content', 'content_id')
                  ->where('contentpage_id = ?', $contentpage_id)
                  ->where('type = ?', 'container')
                  ->limit(1);
          $container = $select_container->query()->fetchAll();
          if (!empty($container)) {
            $container_id = $container[0]['content_id'];
            $select = new Zend_Db_Select($db);
            $select_left = $select
                    ->from('engine4_sitepage_content')
                    ->where('parent_content_id = ?', $container_id)
                    ->where('type = ?', 'container')
                     ->where('contentpage_id = ?', $contentpage_id)
	                   ->where('name in (?)', array('left', 'right'))
                    ->limit(1);
            $left = $select_left->query()->fetchAll();
            if (!empty($left)) {
              $left_id = $left[0]['content_id'];
              $db->insert('engine4_sitepage_content', array(
                  'contentpage_id' => $contentpage_id,
                  'type' => 'widget',
                  'name' => 'sitepage.albums-sitepage',
                  'parent_content_id' => $left_id,
                  'order' => 25,
                  'params' => '{"title":"Albums","titleCount":""}',
              ));
            }
          }
        }

//         //INSERT THE PHOTO STRIP WIDGET IN SITEPAGE CONTENT TABLE FOR USER
//         $select = new Zend_Db_Select($db);
//         $select_content = $select
//                 ->from('engine4_sitepage_content')
//                 ->where('contentpage_id = ?', $contentpage_id)
//                 ->where('type = ?', 'widget')
//                 ->where('name = ?', 'sitepage.photorecent-sitepage')
//                 ->limit(1);
//         $content = $select_content->query()->fetchAll();
//         if (empty($content)) {
//           $select = new Zend_Db_Select($db);
//           $select_container = $select
//                   ->from('engine4_sitepage_content', 'content_id')
//                   ->where('contentpage_id = ?', $contentpage_id)
//                   ->where('type = ?', 'container')
//                   ->limit(1);
//           $container = $select_container->query()->fetchAll();
//           if (!empty($container)) {
//             $container_id = $container[0]['content_id'];
//             $select = new Zend_Db_Select($db);
//             $select_middle = $select
//                     ->from('engine4_sitepage_content')
//                     ->where('parent_content_id = ?', $container_id)
//                     ->where('type = ?', 'container')
//                     ->where('name = ?', 'middle')
//                     ->limit(1);
//             $middle = $select_middle->query()->fetchAll();
//             if (!empty($middle)) {
//               $middle_id = $middle[0]['content_id'];
//               $db->insert('engine4_sitepage_content', array(
//                   'contentpage_id' => $contentpage_id,
//                   'type' => 'widget',
//                   'name' => 'sitepage.photorecent-sitepage',
//                   'parent_content_id' => $middle_id,
//                   'order' => 5,
//                   'params' => '{"title":"","titleCount":""}',
//               ));
//             }
//           }
//         }
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
								->where('page_id = ?', $page_id)
								->where('name in (?)', array('left', 'right'))
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
//     $select = new Zend_Db_Select($db);
//     $select_content = $select
//             ->from('engine4_sitepage_admincontent')
//             ->where('page_id = ?', $page_id)
//             ->where('type = ?', 'widget')
//             ->where('name = ?', 'sitepage.photorecent-sitepage')
//             ->limit(1);
//     $content = $select_content->query()->fetchAll();
//     if (empty($content)) {
//       $select = new Zend_Db_Select($db);
//       $select_container = $select
//               ->from('engine4_sitepage_admincontent', 'admincontent_id')
//               ->where('page_id = ?', $page_id)
//               ->where('type = ?', 'container')
//               ->limit(1);
//       $container = $select_container->query()->fetchAll();
//       if (!empty($container)) {
//         $container_id = $container[0]['admincontent_id'];
//         $select = new Zend_Db_Select($db);
//         $select_middle = $select
//                 ->from('engine4_sitepage_admincontent')
//                 ->where('parent_content_id = ?', $container_id)
//                 ->where('type = ?', 'container')
//                 ->where('name = ?', 'middle')
//                 ->limit(1);
//         $middle = $select_middle->query()->fetchAll();
//         if (!empty($middle)) {
//           $middle_id = $middle[0]['admincontent_id'];
//           $db->insert('engine4_sitepage_admincontent', array(
//               'page_id' => $page_id,
//               'type' => 'widget',
//               'name' => 'sitepage.photorecent-sitepage',
//               'parent_content_id' => $middle_id,
//               'order' => 5,
//               'params' => '{"title":"","titleCount":""}',
//           ));
//         }
//       }
//     }

    //INSERT THE RANDOM ALBUM WIDGET IN CORE CONTENT TABLE
    $select = new Zend_Db_Select($db);
    $select_content = $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitepage.albums-sitepage')
            ->limit(1);
    $content = $select_content->query()->fetchAll();
    if (empty($content)) {
      $select = new Zend_Db_Select($db);
      $select_container = $select
              ->from('engine4_core_content', 'content_id')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'container')
              ->limit(1);
      $container = $select_container->query()->fetchAll();
      if (!empty($container)) {
        $container_id = $container[0]['content_id'];
        $select = new Zend_Db_Select($db);
        $select_left = $select
                ->from('engine4_core_content')
                ->where('parent_content_id = ?', $container_id)
                ->where('type = ?', 'container')
								->where('page_id = ?', $page_id)
								->where('name in (?)', array('left', 'right'))
                ->limit(1);
        $left = $select_left->query()->fetchAll();
        if (!empty($left)) {
          $left_id = $left[0]['content_id'];
          $db->insert('engine4_core_content', array(
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

//     //INSERT THE PHOTO STRIP WIDGET IN CORE CONTENT TABLE
//     $select = new Zend_Db_Select($db);
//     $select_content = $select
//             ->from('engine4_core_content')
//             ->where('page_id = ?', $page_id)
//             ->where('type = ?', 'widget')
//             ->where('name = ?', 'sitepage.photorecent-sitepage')
//             ->limit(1);
//     $content = $select_content->query()->fetchAll();
//     if (empty($content)) {
//       $select = new Zend_Db_Select($db);
//       $select_container = $select
//               ->from('engine4_core_content', 'content_id')
//               ->where('page_id = ?', $page_id)
//               ->where('type = ?', 'container')
//               ->limit(1);
//       $container = $select_container->query()->fetchAll();
//       if (!empty($container)) {
//         $container_id = $container[0]['content_id'];
//         $select = new Zend_Db_Select($db);
//         $select_middle = $select
//                 ->from('engine4_core_content')
//                 ->where('parent_content_id = ?', $container_id)
//                 ->where('type = ?', 'container')
//                 ->where('name = ?', 'middle')
//                 ->limit(1);
//         $middle = $select_middle->query()->fetchAll();
//         if (!empty($middle)) {
//           $middle_id = $middle[0]['content_id'];
//           $db->insert('engine4_core_content', array(
//               'page_id' => $page_id,
//               'type' => 'widget',
//               'name' => 'sitepage.photorecent-sitepage',
//               'parent_content_id' => $middle_id,
//               'order' => 5,
//               'params' => '{"title":"","titleCount":""}',
//           ));
//         }
//       }
//     }
  }
  
  $contentTable = Engine_Api::_()->getDbtable('content', 'core');
  $contentTableName = $contentTable->info('name');
 
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
    ;
    $rowinfo = $select->query()->fetch();

  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitepage_album_browse')
          ->limit(1);
  ;
  $info = $select->query()->fetch();
  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitepage_album_browse',
        'displayname' => 'Browse Page albums',
        'title' => 'Page Albums List',
        'description' => 'This is the page albums.',
        'custom' => 1,
    ));
    $page_id = $db->lastInsertId('engine4_core_pages');
//INSERT MAIN CONTAINER
    $mainContainer = $contentTable->createRow();
    $mainContainer->page_id = $page_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

//INSERT MAIN - MIDDLE CONTAINER
    $mainMiddleContainer = $contentTable->createRow();
    $mainMiddleContainer->page_id = $page_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

//INSERT MAIN - RIGHT CONTAINER
    $mainRightContainer = $contentTable->createRow();
    $mainRightContainer->page_id = $page_id;
    $mainRightContainer->type = 'container';
    $mainRightContainer->name = 'right';
    $mainRightContainer->parent_content_id = $container_id;
    $mainRightContainer->order = 5;
    $mainRightContainer->save();
    $right_id = $mainRightContainer->content_id;

//INSERT TOP CONTAINER
    $topContainer = $contentTable->createRow();
    $topContainer->page_id = $page_id;
    $topContainer->type = 'container';
    $topContainer->name = 'top';
    $topContainer->order = 1;
    $topContainer->save();
    $top_id = $topContainer->content_id;

//INSERT TOP- MIDDLE CONTAINER
    $topMiddleContainer = $contentTable->createRow();
    $topMiddleContainer->page_id = $page_id;
    $topMiddleContainer->type = 'container';
    $topMiddleContainer->name = 'middle';
    $topMiddleContainer->parent_content_id = $top_id;
    $topMiddleContainer->order = 6;
    $topMiddleContainer->save();
    $top_middle_id = $topMiddleContainer->content_id;

    //INSERT NAVIGATION WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.browsenevigation-sitepage', $top_middle_id, 1);

//INSERT ALBUM WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagealbum.sitepage-album', $middle_id, 2);

    //INSERT SEARCH PAGE ALBUM WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagealbum.search-sitepagealbum', $right_id, 3, "", "true");

    //INSERT RECENT PAGE ALBUM WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.mostrecentphotos-sitepage', $right_id, 4, "Recent Photos", "true");

    //INSERT SPONSORED PAGE ALBUM WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagealbum.sitepage-sponsoredalbum', $right_id, 5, "Sponsored Albums", "true");

    //INSERT MOST POUPLAR PAGE ALBUM WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.popularphotos-sitepage', $right_id, 6, "Most Popular Photos", "true");

    if ( $infomation && $rowinfo ) {
      Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-ads', $right_id, 7, "", "true");
    }
  }

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
      Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-ads', $right_id, 1, "", "true");
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

}
?>