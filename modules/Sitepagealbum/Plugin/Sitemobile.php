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
class Sitepagealbum_Plugin_Sitemobile {
  
  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    
    $this->_pagesTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    //Page Album
    $this->addSitepageAlbumProfileContent();
    $this->addSitepageAlbumCreatePage();
    $this->addSitepageAlbumBrowsePage();
    $this->addSitepageAlbumViewPage();
    $this->addSitepageAlbumPhotoViewPage();
    include APPLICATION_PATH . "/application/modules/Sitepagealbum/controllers/license/mobileLayoutCreation.php";
  }
  
  //Site page Albums 
  public function addSitepageAlbumProfileContent() {
    // install content areas
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_index_view');


    if(empty($page_id)) 
      return false;

    // album.profile-albums
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitepage.sitemobile-photos-sitepage')
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
          'name' => 'sitepage.sitemobile-photos-sitepage',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 600,
          'params' => '{"title":"Photos","titleCount":true}',
      ));

      return $this;
    }
  }

  public function addSitepageAlbumCreatePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_photo_upload-album');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_photo_upload-album',
          'displayname' => 'Directory / Pages - Create Album',
          'title' => 'Create new Album',
          'description' => 'This is album create page.',
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

  public function addSitepageAlbumBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_album_browse');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_album_browse',
          'displayname' => 'Directory / Pages - Browse Albums',
          'title' => 'Browse Albums',
          'description' => 'This is browse albums page.',
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

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitepagealbum.sitepage-album',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"itemCount":"10"}',
          'order' => 3,
      ));
    }
  }

  public function addSitepageAlbumViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_album_view');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_album_view',
          'displayname' => 'Directory / Pages - Album View Page',
          'title' => 'Album View Page',
          'description' => 'This is album view page.',
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

//      $db->insert($this->_contentTable, array(
//          'page_id' => $page_id,
//          'type' => 'widget',
//          'name' => 'sitemobile.sitemobile-headingtitle',
//          'parent_content_id' => $middle_id,
//          'order' => 1,
//          'params' => '{"title":"","nonloggedin":"1","loggedin":"1","nomobile":"0","notablet":"0","name":"sitemobile.sitemobile-headingtitle"}',
//          'module' => 'sitemobile'
//      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitepagealbum.album-content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
      ));
    }
  }

  public function addSitepageAlbumPhotoViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_photo_view');

    // profile page
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_photo_view',
          'displayname' => 'Directory / Pages - Album Photo View Page',
          'title' => 'Album Photo View Page',
          'description' => 'This is album photo view page.',
          'provides' => 'subject=sitepage_photo',
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
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
      ));
    }

    return $this;
  }
  
 }