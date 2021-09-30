<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepagevideo
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
  $select = new Zend_Db_Select($db);
  $select_page = $select
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'sitepage_index_view')
          ->limit(1);
  $page = $select_page->query()->fetchAll();
  if ( !empty($page) ) {
    $page_id = $page[0]['page_id'];

    //INSERTING THE VIDEO WIDGET IN SITEPAGE_ADMIN_CONTENT TABLE ALSO.
    Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagevideo.profile-sitepagevideos', $page_id, 'Videos', 'true', '111');

    //INSERTING THE VIDEO WIDGET IN CORE_CONTENT TABLE ALSO.
    Engine_Api::_()->getApi('layoutcore', 'sitepage')->setContentDefaultInfo('sitepagevideo.profile-sitepagevideos', $page_id, 'Videos', 'true', '111');

    //INSERTING THE VIDEO WIDGET IN SITEPAGE_CONTENT TABLE ALSO.
    $select = new Zend_Db_Select($db);
    $contentpage_ids = $select->from('engine4_sitepage_contentpages', 'contentpage_id')->query()->fetchAll();
    foreach ( $contentpage_ids as $contentpage_id ) {
      if ( !empty($contentpage_id) ) {
        Engine_Api::_()->getDbtable('content', 'sitepage')->setDefaultInfo('sitepagevideo.profile-sitepagevideos', $contentpage_id['contentpage_id'], 'Videos', 'true', '111');
      }
    }
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
          ->where('name = ?', 'sitepagevideo_index_browse')
          ->limit(1);
  ;
  $info = $select->query()->fetch();
  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitepagevideo_index_browse',
        'displayname' => 'Browse Page videos',
        'title' => 'Page Videos List',
        'description' => 'This is the page videos.',
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

//INSERT VIDEO WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagevideo.sitepage-video', $middle_id, 2);

    //INSERT SEARCH PAGE VIDEO WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagevideo.search-sitepagevideo', $right_id, 3, "", "true");

    //INSERT FEATURED PAGE VIDEO WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagevideo.homefeaturelist-sitepagevideos', $right_id, 4, "Featured Videos", "true");

    //INSERT SPONSORED PAGE VIDEO WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagevideo.sitepage-sponsoredvideo', $right_id, 5, "Sponsored Videos", "true");

    //INSERT MOST COMMENTED PAGE VIDEO WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagevideo.homecomment-sitepagevideos', $right_id, 6, "Most Commented Videos", "true");

    //INSERT MOST VIEWED PAGE VIDEO WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagevideo.homeview-sitepagevideos', $right_id, 7, "Most Viewed Videos", "true");

    //INSERT MOST LIKED PAGE VIDEO WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagevideo.homelike-sitepagevideos', $right_id, 8, "Most Liked Videos", "true");

    //INSERT TOP RATED PAGE VIDEO WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagevideo.homerate-sitepagevideos', $right_id, 9, "Top Rated Videos", "true");

    //INSERT RECENT PAGE VIDEO WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagevideo.homerecent-sitepagevideos', $right_id, 10, "Recent Videos", "true");

    if ( !empty($infomation)  && !empty($rowinfo) ) {
      Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-ads', $right_id, 11, "", "true");
    }
  }

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
        'params' => '{"title":"Other Videos From Page"}',
    ));

    if ( !empty($infomation)  && !empty($rowinfo) ) {
      Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-ads', $right_id, 4, "", "true");
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
					'params' => '{"title":"From the same Member"}',
			));

		}
	}

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

		//INSERT MOST VIEWED PAGE VIDEO WIDGET
			Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagevideo.homeview-sitepagevideos', $right_id, 20, "Most Viewed Videos", "true");

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

}
?>