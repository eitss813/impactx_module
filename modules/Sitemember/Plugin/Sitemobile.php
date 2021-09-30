<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Sitemobile.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Plugin_Sitemobile {
  
  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    
    $this->_pagesTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;

    $this->addSitememberBrowsePage();
  
  }

  public function addSitememberBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitemember_location_userby-locations');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitemember_location_userby-locations',
          'displayname' => 'Advanced Members - Browse Members’ Locations',
          'title' => 'Advanced Members - Browse Members’ Locations',
          'description' => 'This is browse members page.',
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

      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","location":1,"name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemember.browse-members-sitemember',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"0":"","title":"","titleCount":true,"links":["addfriend","messege"],"has_photo":"0","orderby":"creationDate","itemCount":"10","truncation":"16","detactLocation":"1","defaultLocationDistance":"1000","name":"sitemember.browse-members-sitemember"}',
          'order' => 3,
      ));
    }
  }

}