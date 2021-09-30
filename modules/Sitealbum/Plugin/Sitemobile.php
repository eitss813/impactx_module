<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Plugin_Sitemobile {
  
  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    
    $this->_pagesTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;

    //Sitealbum pages
    $this->addSitealbumHomePage();
    $this->addSitealbumBrowsePage();
    $this->addSitealbumBrowsePhotosPage();
    $this->addSitealbumManagePage();
    $this->addSitealbumCreatePage();
    $this->addSitealbumEditPage();
    $this->addSitealbumPhotoViewPage();
    $this->addSitealbumViewPage();
    $this->addSitealbumUserProfileContent();  
 
  }

    //Get page id of pages from "sitemobile_pages" table.
    public function getPageId($page_name) {
      $db = Engine_Db_Table::getDefaultAdapter();

      // profile page
      $page_id = $db->select()
              ->from($this->_pagesTable, 'page_id')
              ->where('name = ?', $page_name)
              ->limit(1)
              ->query()
              ->fetchColumn();
      return $page_id;
    }
    
    public function addSitealbumHomePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitealbum_index_index');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitealbum_index_index',
          'displayname' => 'Advanced Albums - Album Home Page',
          'title' => 'Advanced Albums - Album Home Page',
          'description' => 'This is album home page.',
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

      //Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","location":0,"nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
      ));
      
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $main_middle_id,
          'order' => 5,
          'params' => '{"max":6}',
          'module' => 'sitemobile'
      ));
      $tab_id = $db->lastInsertId($this->_contentTable);

      // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitealbum.sitemobile-popular-albums',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 1,
                'module' => 'sitealbum',
                'params' => '{"title":"Recent","titleCount":true,"columnHeight":"260","category_id":"0","albumInfo":["albumTitle","totalPhotos","ownerName"],"name":"sitealbum.sitemobile-popular-albums","popularity":"recentalbums","itemCount":"5","truncation":"16"}',
            ));
            
             // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitealbum.sitemobile-popular-albums',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 2,
                'module' => 'sitealbum',
                'params' => '{"title":"Most Liked","titleCount":true,"columnHeight":"260","category_id":"0","albumInfo":["albumTitle","totalPhotos","ownerName"],"name":"sitealbum.sitemobile-popular-albums","popularity":"most_likedalbums","itemCount":"5","truncation":"16"}',                
            ));
            
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitealbum.sitemobile-popular-albums',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 3,
                'module' => 'sitealbum',
                'params' => '{"title":"Most Viewed","titleCount":true,"columnHeight":"260","category_id":"0","albumInfo":["albumTitle","totalPhotos","ownerName"],"name":"sitealbum.sitemobile-popular-albums","popularity":"most_viewedalbums","itemCount":"5","truncation":"16"}',                
            ));
            
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitealbum.sitemobile-popular-albums',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 4,
                'module' => 'sitealbum',
                'params' => '{"title":"Featured","titleCount":true,"columnHeight":"260","category_id":"0","albumInfo":["albumTitle","totalPhotos","ownerName"],"name":"sitealbum.sitemobile-popular-albums","popularity":"featuredalbums","itemCount":"5","truncation":"16"}',
                 ));
            
            $siteRatingEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1);
            if ($siteRatingEnabled) {
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitealbum.sitemobile-popular-albums',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 5,
                'module' => 'sitealbum',
                'params' => '{"title":"Most Rated","titleCount":true,"columnHeight":"260","category_id":"0","albumInfo":["albumTitle","totalPhotos","ownerName"],"name":"sitealbum.sitemobile-popular-albums","popularity":"most_ratedalbums",
                "itemCount":"5","truncation":"16"}',
                ));
            }
      
    }
  }
  
  public function addSitealbumBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitealbum_index_browse');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitealbum_index_browse',
          'displayname' => 'Advanced Albums - Album Browse Page',
          'title' => 'Advanced Albums - Album Browse Page',
          'description' => 'This page lists album entries.',
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
          'module' => 'sitemobile'
      ));

      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","location":0,"name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      
      if ($this->_contentTable == 'engine4_sitemobile_content' || $this->_contentTable == 'engine4_sitemobile_tablet_content') {
          $db->insert($this->_contentTable, array(
              'page_id' => $page_id,
              'type' => 'widget',
              'name' => 'sitealbum.browse-breadcrumb-sitealbum',
              'parent_content_id' => $main_middle_id,
              'order' => 3,
              'params' => '{"nomobile":"1"}',
          ));
      }
      
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitealbum.browse-albums-sitealbum',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"title":"","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","albumInfo":["ownerName","albumTitle","totalPhotos"],"customParams":"1","orderby":"creation_date","show_content":"3","truncationLocation":"35","albumTitleTruncation":"50","limit":"10","name":"sitealbum.browse-albums-sitealbum"}',
          'order' => 4,
      ));
    }
  }
  
  public function addSitealbumManagePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('sitealbum_index_manage');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitealbum_index_manage',
          'displayname' => 'Advanced Albums - My Albums Page',
          'title' => 'Advanced Albums - My Albums Page',
          'description' => 'This page lists album a user\'s albums.',
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
          'module' => 'sitemobile'
      ));

      // Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitealbum.my-albums-sitealbum',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }

    return $this;
  }

  public function addSitealbumCreatePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('sitealbum_index_upload');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitealbum_index_upload',
          'displayname' => 'Advanced Albums - Album Create Page',
          'title' => 'Add New Photos',
          'description' => 'This page is the album create page.',
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

      //WE WILL NOT ADD THE NAVIGATION TAB ON APP AND TABLET APP.
      if ($this->_pagesTable == 'engine4_sitemobile_pages' || $this->_pagesTable == 'engine4_sitemobile_tablet_pages'){
      // Insert menu
        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitemobile.sitemobile-navigation',
            'page_id' => $page_id,
            'parent_content_id' => $main_middle_id,
            'order' => 1,
            'module' => 'sitemobile'
        ));
      }
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

    public function addSitealbumEditPage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('sitealbum_album_edit');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitealbum_album_edit',
          'displayname' => 'Advanced Albums - Album Edit Page',
          'title' => 'Edit Album',
          'description' => 'This page is the album edit page.',
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

      //WE WILL NOT ADD THE NAVIGATION TAB ON APP AND TABLET APP.
      if ($this->_pagesTable == 'engine4_sitemobile_pages' || $this->_pagesTable == 'engine4_sitemobile_tablet_pages'){
      // Insert menu
        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitemobile.sitemobile-navigation',
            'page_id' => $page_id,
            'parent_content_id' => $main_middle_id,
            'order' => 1,
            'module' => 'sitemobile'
        ));
      }
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }
  
  public function addSitealbumPhotoViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('sitealbum_photo_view');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitealbum_photo_view',
          'displayname' => 'Advanced Albums - Photo View Page',
          'title' => 'Advanced Albums - Photo View Page',
          'description' => 'This is the main view page of a photo.',
          'provides' => 'subject=album_photo',
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
          'name' => 'sitealbum.photo-view',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile',
          'params' => '{"titleCount":"true","itemCountPerPage":4,"title":""}',
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }

    return $this;
  }

  public function addSitealbumViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('sitealbum_album_view');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitealbum_album_view',
          'displayname' => 'Advanced Albums - Album View Page',
          'title' => 'Advanced Albums - Album View Page',
          'description' => 'This is the main view page of an album.',
          'provides' => 'subject=album',
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
      
      if($this->_pagesTable == 'engine4_sitemobile_pages' || $this->_pagesTable == 'engine4_sitemobile_tablet_pages')  {
        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitealbum.profile-breadcrumb',
            'parent_content_id' => $middle_id,
            'order' => 1,
            'params' => '',
        ));
      }

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitealbum.top-content-of-album',
          'parent_content_id' => $middle_id,
          'order' => 2,
          'params' => '{"title":"","titleCount":true,"showInformationOptions":["title","owner","description","location","updateddate","categoryLink","tags","rating","viewCount","totalPhotos","likeCount","commentCount"],"nomobile":"0","name":"sitealbum.top-content-of-album"}',
          'module' => 'sitealbum'
      ));
            
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitealbum.album-view',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 3,
          'module' => 'sitealbum',
          'params' => '{"titleCount":true,"itemCountPerPage":"24","show_content":"2","photoTitleTruncation":"100","title":"","nomobile":"0","name":"sitealbum.album-view"}',
      ));
  
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }

    return $this;
  }
  
  public function addSitealbumUserProfileContent() {
    // install content areas
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;

    // album.profile-albums
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitealbum.profile-photos')
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
          'name' => 'sitealbum.profile-photos',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 9,
          'params' => '{"title":"Albums","titleCount":true,"itemCountPerPage":"12","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","margin_photo":"2","selectDispalyTabs":["yourphotos","photosofyou","albums"],"albumInfo":["albumTitle","totalPhotos"],"truncationLocation":"35","titleTruncation":"100","nomobile":"0","name":"sitealbum.profile-photos"}',
          'module' => 'sitealbum'
      ));
   
      return $this;
    }
  }
  
  //Album pages
  public function addSitealbumBrowsePhotosPage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('sitealbum_index_photos');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitealbum_index_photos',
          'displayname' => 'Advanced Albums - Browse Photo Page',
          'title' => 'Advanced Albums - Browse Photo Page',
          'description' => 'This page lists photo entries.',
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
          'module' => 'sitemobile'
      ));
      
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $main_middle_id,
          'order' => 5,
          'params' => '{"max":6}',
          'module' => 'sitemobile'
      ));
      $tab_id = $db->lastInsertId($this->_contentTable);

      // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitealbum.sitemobile-popular-photos',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 1,
                'module' => 'sitealbum',
                'params' => '{"title":"Recent","titleCount":true,"columnHeight":"260","category_id":"0","name":"sitealbum.sitemobile-popular-photos","popularity":"recentphotos","itemCount":"5","truncation":"16"}',
            ));
            
             // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitealbum.sitemobile-popular-photos',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 2,
                'module' => 'sitealbum',
                'params' => '{"title":"Most Liked","titleCount":true,"columnHeight":"260","category_id":"0","name":"sitealbum.sitemobile-popular-photos","popularity":"most_likedphotos","itemCount":"5","truncation":"16"}',                
            ));
            
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitealbum.sitemobile-popular-photos',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 3,
                'module' => 'sitealbum',
                'params' => '{"title":"Most Viewed","titleCount":true,"columnHeight":"260","category_id":"0","name":"sitealbum.sitemobile-popular-photos","popularity":"most_viewedphotos","itemCount":"5","truncation":"16"}',                
            ));
            
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitealbum.sitemobile-popular-photos',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 4,
                'module' => 'sitealbum',
                'params' => '{"title":"Featured","titleCount":true,"columnHeight":"260","category_id":"0","name":"sitealbum.sitemobile-popular-photos","popularity":"featuredphotos","itemCount":"5","truncation":"16"}',
                 ));
            
            $siteRatingEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1);
            if ($siteRatingEnabled) {
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitealbum.sitemobile-popular-photos',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 5,
                'module' => 'sitealbum',
                'params' => '{"title":"Most Rated","titleCount":true,"columnHeight":"260","category_id":"0","name":"sitealbum.sitemobile-popular-photos","popularity":"most_ratedphotos",
                "itemCount":"5","truncation":"16"}',
                ));
            }
    }

    return $this;
  }

}