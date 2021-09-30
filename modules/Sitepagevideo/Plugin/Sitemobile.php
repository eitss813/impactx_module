<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Plugin_Sitemobile {
  
  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    
    $this->_pagesTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    //Page video
    $this->addSitepageVideoProfileContent();
    $this->addSitepageVideoBrowsePage();
    $this->addSitepageVideoCreatePage();
    $this->addSitepageVideoViewPage();
    include APPLICATION_PATH . "/application/modules/Sitepagevideo/controllers/license/mobileLayoutCreation.php";
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->query("INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
( 'sitepagevideo_share', 'sitepagevideo', 'Share', 'Sitepagevideo_Plugin_Menus', '', 'sitepagevideo_profile', '', 0, 4, 0, 0)");
    
    // Video Report
    $db->query("INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
( 'sitepagevideo_report', 'sitepagevideo', 'Report', 'Sitepagevideo_Plugin_Menus', '', 'sitepagevideo_profile', '', 0, 5, 0, 0)");
  }
  //page video
  public function addSitepageVideoProfileContent() {

    // install content areas

    $db = Engine_Db_Table::getDefaultAdapter();
    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_index_view');

    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitepagevideo.sitemobile-profile-sitepagevideos')
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'container')
              ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
              ->reset('where')
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.container-tabs-columns')
              ->where('page_id = ?', $page_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepagevideo.sitemobile-profile-sitepagevideos',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 700,
          'params' => '{"title":"Videos","titleCount":true}',
      ));
    }
  }

  public function addSitepageVideoViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepagevideo_index_view');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepagevideo_index_view',
          'displayname' => 'Directory / Pages - Video View Page',
          'title' => 'Video View Page',
          'description' => 'This is video view page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitepagevideo.video-content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 3,
      ));
    }
  }

  public function addSitepageVideoBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepagevideo_index_browse');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepagevideo_index_browse',
          'displayname' => 'Directory / Pages - Browse Videos',
          'title' =>  'Browse Videos',
          'description' => 'This is video browse page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitepagevideo.sitepage-video',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'params' => '{"itemCount":"10"}',
          'order' => 3,
      ));
    }
  }

  public function addSitepageVideoCreatePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepagevideo_index_create');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepagevideo_index_create',
          'displayname' => 'Directory / Pages - Create Video',
          'title' => 'Create new Video',
          'description' => 'This is video create page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
      ));
    }
  }

}