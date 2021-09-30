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
class Sitepagediscussion_Plugin_Sitemobile {
  
  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    
    $this->_pagesTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
   //page events
   $this->addSitepageDiscussionProfileContent();
   $this->addSitepagediscussionProfilePage();
   include APPLICATION_PATH . "/application/modules/Sitepagediscussion/controllers/license/mobileLayoutCreation.php";
  }
  
  //page Discussion
  public function addSitepageDiscussionProfileContent() {

    // install content areas

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'sitepage_index_view')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;

    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitepage.sitemobile-discussion-sitepage')
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
          'name' => 'sitepage.sitemobile-discussion-sitepage',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 1700,
          'params' => '{"title":"Discussions","titleCount":true}',
      ));
    }
  }

  //Page plugin main pages
  public function addSitepagediscussionProfilePage() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // Check if it's already been placed
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'sitepage_topic_view')
            ->limit(1);

    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert($this->_pagesTable, array(
          'name' => 'sitepage_topic_view',
          'displayname' => 'Directory / Pages - Topic View Page',
          'title' => 'Topic View Page',
          'description' => 'This is topic view page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId($this->_pagesTable);

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 2,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepage.discussion-content',
            'parent_content_id' => $middle_id,
            'order' => 1,
            'params' => '',
        ));
    }
  }
}