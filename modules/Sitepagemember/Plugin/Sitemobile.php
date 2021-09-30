<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagemember_Plugin_Sitemobile {
  
  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    
    $this->_pagesTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    //PAGE MEMBER
    $this->addSitepageMemberProfileContent();
    $this->addSitepageMemberBrowsePage();
    $this->addSitepagememberPages();
    include APPLICATION_PATH . "/application/modules/Sitepagemember/controllers/license/mobileLayoutCreation.php";
  }
  
  public function addSitepagememberPages() {
    $this->setDefaultWidgetForSitepagemember('content', 'pages');
    $this->setDefaultWidgetForSitepagemember('tabletcontent', 'tabletpages');
  }

  public function setDefaultWidgetForSitepagemember($content, $pages) {
    // install content areas

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;


    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitepage.profile-joined-sitepage')
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
          'name' => 'sitepage.profile-joined-sitepage',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 10,
          'params' => '{"title":"Joined / Owned Pages","titleCount":true, "pageAdminJoined":"2","textShow":"Verified","showMemberText":"1","category_id":"0"}',
          'module' => 'sitepagemember'
      ));
    }
  }

  public function addSitepageMemberProfileContent() {
    //install content areas
    $db = Engine_Db_Table::getDefaultAdapter();
      $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepage_index_view');


    //sitemobile.blog-profile-blogs
    //Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitepagemember.sitemobile-profile-sitepagemembers')
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {

      //container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'container')
              ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      //middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      //tab_id (tab container) may not always be there
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

      //tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepagemember.sitemobile-profile-sitepagemembers',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 1000,
          'params' => '{"title":"Members","titleCount":true}',
      ));

      //tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepagemember.profile-sitepagemembers-announcements',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 500,
          'params' => '{"title":"Announcements","titleCount":true}',
      ));
    }
  }

  public function addSitepageMemberBrowsePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitepagemember_index_browse');
    //insert if it doesn't exist yet
    if (!$page_id) {
      //Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepagemember_index_browse',
          'displayname' => 'Directory / Pages - Browse Members',
          'title' => 'Browse Members',
          'description' => 'This is member browse page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      //Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      //Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));

      //Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      //Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitepagemember.sitepage-member',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"itemCount":"10"}',
          'order' => 3,
      ));
    }
  }

}